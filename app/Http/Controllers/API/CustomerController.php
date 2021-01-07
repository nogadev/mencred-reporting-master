<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

use DB;
use App\Traits\RestClientTrait;

class CustomerController extends Controller
{
    use RestClientTrait;

    public function findAll(Request $request)
    {
        $search    = $request->search;
        $orderBy = $request->orderBy;
        $customers = Customer::select([
            'customers.name AS customer_name',
            'routes.name AS route_name',
            'sellers.name AS seller_name',
            'towns.name AS commercial_town',
            'neighborhoods.name AS commercial_neighborhood',
            'customers.commercial_address as commercial_address',

            'customers.doc_number AS doc_number'
        ])
            ->join('routes', 'routes.id', '=', 'customers.route_id')
            ->join('sellers', 'sellers.id', '=', 'customers.seller_id')
            // ->join('commerces', 'commerces.id', '=', 'customers.commercial_neighborhood_id')
            ->join('towns', 'towns.id', '=', 'customers.commercial_town_id')
            ->join('neighborhoods', 'neighborhoods.id', '=', 'customers.commercial_neighborhood_id')
            ->when($search, function($q) use ($search) {
                return $q->where('customers.name','like','%'. $search . '%')
                    ->orWhere('customers.doc_number','like','%'. $search . '%')
                    ->orWhere('routes.name','like','%'. $search . '%')
                    ->orWhere('sellers.name','like','%'. $search . '%')
                    ->orWhere('towns.name','like','%'. $search . '%')
                    ->orWhere('neighborhoods.name','like','%'. $search . '%')
                    ->orWhere('customers.commercial_address','like','%'. $search . '%');
            })
            ->when(!$orderBy, function($q) use($orderBy){
                return $q->orderBy('customers.name');
            })
            ->when($orderBy, function($q) use($orderBy){
                return $q->orderBy($orderBy);
            })
            ->get();

        $this->post('customers', $customers, "Clientes.pdf");
    }

    public function sequence($routeId = null)
    {
        $customers = Customer::where('route_id', '=', $routeId)
            ->has('activeCredits')
            ->with(['route','seller','commercial_neighborhood','commercial_town','commerce', 'visitday'])
            ->orderBy('sequence_order')
            ->get();

        $this->post('customers/sequences', $customers, "Secuencia.pdf");
    }

}
