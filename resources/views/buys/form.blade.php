{{ csrf_field() }}
@include('errors')
<input type="hidden" name="id" @if(isset($buy)) value="{{ $buy->id }}" @else value="0" @endif>
<div class="row">
    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
        @include('commons.asterix-sm')<label>Proveedor | Nombre Fantasia</label>
        <select name="supplier_id" onchange="setSupplierData();" class="form-control form-control-sm">
            @foreach($suppliers as $supplier)
                <option value="{{$supplier->id}}" @if((isset($buy) && $buy->supplier_id == $supplier->id) || (old('supplier_id') == $supplier->id)) selected @endif>{{$supplier->business_name}}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
        <label>Proveedor | Razón Social</label>
        <input type="text" name="business_name" class="form-control form-control-sm" placeholder="Razón social"
            @if(isset($buy)) value="{{ $buy->supplier->name }}" @else value="{{ old('name') }}" @endif readonly>
    </div>
    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
        <label>CUIT</label>
        <input type="text" name="code" class="form-control form-control-sm" placeholder="CUIT"
            @if(isset($buy)) value="{{ $buy->supplier->code }}" @else value="{{ old('code') }}" @endif readonly>
    </div>
    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
        <label>Dirección</label>
        <input type="text" name="address" class="form-control form-control-sm" placeholder="Dirección"
            @if(isset($buy)) value="{{ $buy->supplier->address }}" @else value="{{ old('address') }}" @endif readonly>
    </div>
</div>
<div class="row">
    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
        @include('commons.asterix-sm')<label>Fecha</label>
        <input type="text" id="date" name="date" class="form-control form-control-sm" placeholder="dd/mm/aaaa">
    </div>
    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
        @include('commons.asterix-sm')<label>Tipo de comprobante</label>
        <select name="voucher_type_id" class="form-control form-control-sm">
            @foreach($voucherTypes as $voucherType)
                <option value="{{$voucherType->id}}" @if((isset($buy) && $buy->voucher_type_id == $voucherType->id) || (old('voucher_type_id') == $voucherType->id)) selected @endif>{{$voucherType->description}}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
        <div class="row">
            <div class="form-group col-xs-6 col-sm-6 col-md-5 col-lg-5">
                @include('commons.asterix-sm')<label>Pto. vta.</label>
                <input type="text" name="subsidiary_number" class="form-control form-control-sm" placeholder="Pto. Vta."
                    @if(isset($buy)) value="{{ $buy->subsidiary_number }}" @else value="{{ old('subsidiary_number') }}" @endif>
            </div>
            <div class="form-group col-xs-6 col-sm-6 col-md-7 col-lg-7">
                @include('commons.asterix-sm')<label>N°</label>
                <input type="text" name="voucher_number" class="form-control form-control-sm" placeholder="N°"
                    @if(isset($buy)) value="{{ $buy->voucher_number }}" @else value="{{ old('voucher_number') }}" @endif>
            </div>
        </div>
    </div>
    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
        @include('commons.asterix-sm')<label>Empresa</label>
        <select name="company_id" class="form-control form-control-sm">
            @foreach($companies as $company)
                <option value="{{$company->id}}" @if((isset($buy) && $buy->company_id == $company->id) || (old('company_id') == $company->id)) selected @endif>{{$company->name}}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="row">
    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
        <label>Percepción IIBB</label>
        <input type="text" name="perception_iibb" class="form-control form-control-sm" placeholder="Percepción IIBB" @if(isset($buy)) value="{{ $buy->perception_iibb }}" @else value="{{ old('perception_iibb') }}" @endif readonly>
    </div>
    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
        @include('commons.asterix-sm')<label>Condiciones de Pago</label>
        <select name="payment_term_id" class="selectpicker show-tick form-control form-control-sm" data-live-search="true" data-width="100%" data-style="btn-info">
            @foreach($paymentTerms as $paymentTerm)
                <option value="{{$paymentTerm->id}}" @if((isset($paymentTerm) && $paymentTerm->name == "CONTADO") || (old('payment_term_id') == $paymentTerm->id)) selected @endif>{{$paymentTerm->name}}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="row justify-content-center">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="card border-primary">
            <div class="card-header">
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                        <label></label><br/>
                        <button type="button" id="addArticleModal" class="btn btn-success btn-block" disabled onclick="openArticleList();">Agregar Artículo</button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <table id="tableArticlesEnd" class="compact hover nowrap row-border table-responsive-lg">
                    <thead>
                        <tr>
                            <th class="no-order fit">#Item</th>
                            <th>Artículo</th>
                            <th>Cantidad</th>
                            <th>$ Neto</th>
                            <th>$ Desc.</th>
                            <th>$ IVA</th>
                            <th>$ Subtotal</th>
                            <th class="no-order no-search fit"></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="form-group col-xs-6 col-sm-6 col-md-3 col-lg-3">
                        <label>$ Imp. Adicional</label><br/>
                        <input id="additional_tax" type="text" name="additional_tax" class="form-control form-control-sm" placeholder="$ 0,00">
                    </div>
                    <div class="form-group col-xs-6 col-sm-6 col-md-3 col-lg-3">
                        <label>% Imp. Adicional</label><br/>
                        <input id="additional_tax_p" type="text" name="additional_tax_p" class="form-control form-control-sm" placeholder="% Adic.">
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-1">
                        <label>Neto</label><br/>
                        <input id="net_total" type="text" name="net_1" class="form-control form-control-sm" placeholder="Neto Total" readonly>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-1">
                        <label>Iva</label><br/>
                        <input id="tax_total" type="text" name="tax_1" class="form-control form-control-sm" placeholder="Iva Total" readonly>
                    </div>

                    <div class="form-group col-xs-12 col-sm-12 col-md-2 col-lg-2">
                        <label>IMP. ADICIONAL</label><br/>
                        <input id="additional_tax_total" type="text" name="additional_tax_total" class="form-control form-control-sm" placeholder="Imp. adicional" readonly>
                    </div>

                    <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2 ml-auto">
                        <label>Total</label><br/>
                        <input id="total" type="text" name="total" class="form-control form-control-sm" readonly>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<textarea name="art_data" id="art_data" style="display: none;"></textarea>
