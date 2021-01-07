<?php

namespace App\Http\Controllers;

use App\Models\Buy;
use App\Models\Company;
use App\Models\Supplier;
use App\Models\VoucherType;
use App\Models\Article;
use App\Models\Tax;
use App\Models\Store;
use App\Models\Stock;
use App\Models\ArticleStock;
use App\Traits\DataTrait;
use Illuminate\Http\Request;
use App\Http\Requests\BuyValidator;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\PaymentTerm;
use Illuminate\Support\Facades\Storage;

class BuyController extends Controller
{
    use DataTrait;

    private $path = 'buys';
    private $companies;
    private $suppliers;
    private $voucherTypes;
    private $articles;
    private $taxes;
    private $stores;
    private $stockController;

    public function __construct(){
        $this->companies    = Company::orderBy('name')->get();
        $this->suppliers    = Supplier::orderBy('name')->get();
        $this->voucherTypes = VoucherType::orderBy('description')->get();
        $this->articles     = Article::orderBy('description')->get();
        $this->taxes        = Tax::orderBy('name')->get();
        $this->stores       = Store::orderBy('name')->get();
        $this->paymentTerm  = PaymentTerm::OrderBy('name')->select('id','name')->get();

        $this->stockController = new StockController();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view($this->path.'.create')
            ->with('suppliers', $this->suppliers)
            ->with('voucherTypes', $this->voucherTypes)
            ->with('companies', $this->companies)
            ->with('articles', $this->articles)
            ->with('taxes', $this->taxes)
            ->with('stores', $this->stores)
            ->with('paymentTerms', $this->paymentTerm);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BuyValidator $request)
    {
        try {
            DB::beginTransaction();

            $request->merge([
                'total' => $this->format_number($request->total),
                'additional_tax_total' => $this->format_number($request->additional_tax_total),
                'net_1' => $this->format_number($request->net_1),
                'tax_1' => $this->format_number($request->tax_1)
            ]);

            $buy = new Buy($request->all());
            $buy->date = date_create_from_format('d/m/Y', $request->date)->format('Y-m-d');

            if($buy->voucher_type_id == 14){
                $buy->status_id = 11;   //el numero indica el estado ABIERTA en la tabla de status.
            }

            if($buy->status_id == 12){
                $buy->status_id = null;
                $buy->related_buy =  $request->id;
                $buy_pf = Buy::find($request->id); //se edita compra proforma para cerrarla y relacionarla con el comprobante final.
                $buy_pf->status_id = 12;
                $buy_pf->related_buy = Buy::latest('id')->first()->id + 1;
                $buy_pf->save();
            }

            $buy->save();

            if (isset($request->art_data)){
                $detalle = json_decode($request->art_data);

                foreach ($detalle as $ba) {

                    $article = Article::find($ba->article_id);

                    //actualizo el tipo de iva
                    $article->tax_id        = $ba->tax_id;
                    $article->supplier_id   = $request->supplier_id;
                    $article->available     = 1;
                    $article->save();

                    $buy->articles()->save($article, [
                        'item_no'           => $ba->item_no,
                        'quantity'          => $ba->quantity,
                        'net'               => $ba->net,
                        'bonus_percentage'  => $ba->bonus_percentage,
                        'bonus'             => $ba->bonus,
                        'tax_percentage'    => $ba->tax_percentage,
                        'tax'               => $ba->tax,
                        'subtotal'          => $ba->subtotal,
                        'code'              => $article->code,
                        'description'       => $article->descripcion,
                        'barode'            => $article->barcode
                    ]);

                    $stock = new Stock();
                    $stock->store_id    = $request->store_id;
                    $stock->company_id  = $request->company_id;
                    $stock->date        = $buy->date;
                    $stock->detail      = $buy->voucherType->acronym." ".$buy->subsidiary_number."-".$buy->voucher_number;
                    $stock->quantity    = $ba->quantity;
                    $stock->in_out      = 'I';

                    $article->stocks()->save($stock);

                    if($buy->status_id != 11){
                        $stock_article = $article->articleStocks
                                    ->where('company_id', '=',$request->company_id)
                                    ->where('store_id','=',$request->store_id)
                                    ->first();
                        if(is_null($stock_article))
                        {
                            $stock_article = new ArticleStock();
                            $stock_article->article_id  = $ba->article_id;
                            $stock_article->store_id    = $request->store_id;
                            $stock_article->company_id  = $request->company_id;

                        }
                        $stock_article->stock = $stock_article->stock + $ba->quantity;

                        $stock_article->save();
                    }  
                }

            }
            DB::commit();
            session()->put('success', 'Compra registrada');
        } catch (\Throwable $th) {
            DB::rollback();
            session()->put('warning', 'Se ha producido un error');
        } finally {
            return redirect()->route($this->path.'.create');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Buy  $buy
     * @return \Illuminate\Http\Response
     */
    public function show(Buy $buy)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Buy  $buy
     * @return \Illuminate\Http\Response
     */
    public function edit(Buy $buy)
    {
        $data = $this->getBoughtListData($buy->id);

        return view($this->path.'.edit.view')
            ->with('data', $data[0])
            ->with('voucherTypes', $this->voucherTypes)
            ->with('stores', $this->stores);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Buy  $buy
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Buy $buy)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Buy  $buy
     * @return \Illuminate\Http\Response
     */
    public function destroy(Buy $buy)
    {
        //
    }

    public function showReportForm() {
        $suppliers = $this->suppliers;
        return view('reports.buys.buys')
            ->with('suppliers', $suppliers);
    }

    public function getReportFormData(Request $request)
    {
        $dateInit =  $this->formatDate($request->date_init);
        $dateEnd = $this->formatDate($request->date_end);
        $supplierId = $request->supplier_id;
        $data = $this->getBoughtReportData($dateInit, $dateEnd, $supplierId);
        return response()->json($data);
    }

    public function showListBuy() {
        $suppliers = $this->suppliers;
        return view('buys.list')
            ->with('suppliers', $suppliers);
    }

    public function getListBuyData(Request $request)
    {
        $dateInit =  $this->formatDate($request->date_init);
        $dateEnd = $this->formatDate($request->date_end);
        $supplierId = $request->supplier_id;
        $data = $this->getBoughtList($dateInit, $dateEnd, $supplierId);
        return response()->json($data);
    }

    public static function getBoughtReportData($dateInit, $dateEnd, $supplierId) {
        return Buy::when($dateInit, function($q) use ($dateInit) {
            $q->where('buys.date', '>=', $dateInit);
        })
            ->when($dateEnd, function($q) use ($dateEnd) {
                $q->where('buys.date', '<=', $dateEnd);
            })
            ->select([
                'suppliers.name as supplier_name',
                'voucher_types.description as voucher_type',
                'buys.subsidiary_number as sell_point',
                'buys.voucher_number',
                'articles.description as article_description',
                'net_1 as net_total_by_voucher',
                'article_buy.net as cost_by_article',
                'buys.tax_1 as iva_tax',
                'buys.date',
                'buys.id as id'])
            ->join('suppliers', 'suppliers.id', '=', 'buys.supplier_id')
            ->join('article_buy', 'article_buy.buy_id', '=', 'buys.id')
            ->join('articles', 'articles.id', '=', 'article_buy.article_id')
            ->join('voucher_types', 'voucher_types.id', '=', 'buys.voucher_type_id')
            ->when($supplierId, function($q) use ($supplierId) {
                return $q->where('suppliers.id', '=', $supplierId);
            })
            ->whereBetween('date', [$dateInit, $dateEnd])
            ->orderBy('buys.date', 'desc')->get();
    }

    public static function getBoughtList($dateInit, $dateEnd, $supplierId) {
        return Buy::when($dateInit, function($q) use ($dateInit) {
            $q->where('buys.date', '>=', $dateInit);
        })
            ->when($dateEnd, function($q) use ($dateEnd) {
                $q->where('buys.date', '<=', $dateEnd);
            })
            ->select([
                'suppliers.name as supplier_name',
                'voucher_types.description as voucher_type',
                'buys.subsidiary_number as sell_point',
                'buys.voucher_number',
                'buys.date',
                'buys.id as id'])
            ->join('suppliers', 'suppliers.id', '=', 'buys.supplier_id')
            ->join('voucher_types', 'voucher_types.id', '=', 'buys.voucher_type_id')
            ->when($supplierId, function($q) use ($supplierId) {
                return $q->where('suppliers.id', '=', $supplierId);
            })
            ->whereBetween('date', [$dateInit, $dateEnd])
            ->orderBy('buys.date', 'desc')->get();
    }

    public static function getBoughtListData($id) {
        return Buy::where('buys.id', '=', $id)
            ->select([
                'suppliers.name as supplier_name',
                'suppliers.id as supplier_id',
                'suppliers.business_name as supplier_business_name',
                'suppliers.code as supplier_code',
                'suppliers.address as address',
                'buys.subsidiary_number',
                'voucher_types.description as voucher_type',
                'buys.subsidiary_number as sell_point',
                'buys.voucher_number',
                'buys.voucher_type_id',
                'buys.company_id as company_id',
                'companies.name as company_name',
                'net_1 as net_total_by_voucher',
                'buys.tax_1 as iva_tax',
                'buys.date',
                'buys.id as id',
                'payment_terms.name as payment_term',
                'payment_term_id',
                'additional_tax_total',
                'buys.perception_iibb',
                'total',
                'buys.status_id as buy_status'])
            ->join('suppliers', 'suppliers.id', '=', 'buys.supplier_id')
            ->join('companies', 'companies.id', '=', 'buys.company_id')
            ->join('voucher_types', 'voucher_types.id', '=', 'buys.voucher_type_id')
            ->join('payment_terms', 'payment_terms.id', '=', 'buys.payment_term_id')
            ->with(['articles'])
            ->orderBy('buys.date', 'desc')->get();
    }

    /**
     * Guardar archivo de factura.
     */

    public function storeFile(Request $request)
    {

        $validator = Validator::make(
            $request->all(),
            [
                'file' => 'file|mimes:pdf|max:3072',
            ],
            $messages = [
                'mimes' => 'El archivo debe ser un archivo de tipo: pdf.',
                'max' => 'El archivo no puede tener mas de 3072 kilobytes.',
            ]
        );

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return \Response::json($errors, 400);    
        }

        $buy = Buy::find($request->id);

        if($file = $request->file('file')){

            $filename = $file->getClientOriginalName();
            $extension = \File::extension($filename); 
            $path = $request->file('file')->storeAs('vouchers','voucher_' . $request->id . '.' . $extension, 'public');
            $buy->file_route = $path;
            $buy->save();
        }

        return response()->json(true);
    }

    /**
     * Verificar archivo de factura.
     */

    public function viewFile(Request $request)
    {
        $pathToFile = $this->findFile($request);
        $status_file = file_exists($pathToFile);
        return response()->json($status_file);
    }

    /**
     * Descargar Factura.
     */

    public function viewFileGet(Request $request)
    {
        $pathToFile = $this->findFile($request);
        return response()->download($pathToFile);
    }

    /**
     * Buscar archivo de factura.
     */

    public function findFile(Request $request)
    {
        $buy = Buy::find($request->id);
        return $pathToFile ='storage/' . $buy->file_route;
    }
}
