{{ csrf_field() }}
@include('errors')
<input type="hidden" name="id" id="id">
<div class="row">
    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
        @include('commons.asterix-sm')<label>Proveedor | Nombre Fantasia</label>
        <select name="supplier_id" class="form-control form-control-sm" readonly>
            <option value="{{$data->supplier_id}}" >{{$data->supplier_business_name}}</option>
        </select>
    </div>
    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
        <label>Proveedor | Razón Social</label>
        <input type="text" name="business_name" id="business_name" class="form-control form-control-sm" placeholder="Razón social" readonly>
    </div>
    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
        <label>CUIT</label>
        <input type="text" name="code" id="code" class="form-control form-control-sm" placeholder="CUIT" readonly>
    </div>
    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
        <label>Dirección</label>
        <input type="text" name="address" id="address" class="form-control form-control-sm" placeholder="Dirección" readonly>
    </div>
</div>
<div class="row">
    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
        @include('commons.asterix-sm')<label>Fecha</label>
        <input type="text" id="date" name="date" class="form-control form-control-sm" placeholder="dd/mm/aaaa" readonly>
    </div>
    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
        @include('commons.asterix-sm')<label>Tipo de comprobante</label>
        <select name="voucher_type_id" id="voucher_type_id" class="form-control form-control-sm" readonly>
            <option value="{{$data->voucher_type_id}}" selected>{{$data->voucher_type}}</option>
        </select>
    </div>
    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
        <div class="row">
            <div class="form-group col-xs-6 col-sm-6 col-md-5 col-lg-5">
                @include('commons.asterix-sm')<label>Pto. vta.</label>
                <input type="text" name="subsidiary_number" id="subsidiary_number" class="form-control form-control-sm" placeholder="Pto. Vta." readonly>
            </div>
            <div class="form-group col-xs-6 col-sm-6 col-md-7 col-lg-7">
                @include('commons.asterix-sm')<label>N°</label>
                <input type="text" name="voucher_number" id="voucher_number" class="form-control form-control-sm" placeholder="N°" readonly>
            </div>
        </div>
    </div>
    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
        @include('commons.asterix-sm')<label>Empresa</label>
        <select name="company_id" class="form-control form-control-sm" readonly>
                <option value="{{$data->company_id}}" selected >{{$data->company_name}}</option>
        </select>
    </div>
</div>
<div class="row">
    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
        <label>Percepción IIBB</label>
        <input type="text" name="perception_iibb" id="perception_iibb" class="form-control form-control-sm" placeholder="Percepción IIBB" readonly>
    </div>
    <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
        @include('commons.asterix-sm')<label>Condiciones de Pago</label>
        <select name="payment_term_id" class="form-control form-control-sm" data-live-search="true" data-width="100%" data-style="btn-info" readonly>
            <option value="{{$data->payment_term_id}}" selected>{{$data->payment_term}}</option>
        </select>
    </div>
</div>
<div class="row justify-content-center">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="card border-primary">
            <div class="card-header">
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                        <h3>Productos</h3>
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
<input type="hidden" name="status_id" id="status_id">
