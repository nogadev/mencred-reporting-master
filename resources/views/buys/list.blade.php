@extends('layouts.app')
@section('custom_styles')
    @include('bootstrap-select.style')
    @include('bootstrap-datepicker.style')
    @include('datatables.style')
@endsection
@section('content')
    <div class="container-fluid">
        <h3>Listar Compras</h3>
        <hr>
        <div class="clearfix"></div>
        <div class="row justify-content-center">
            <div class="col-xs-12 col-sm-12 col-md-11 col-lg-11">
                <form id="creditForm" method="post" action="" autocomplete="off">
                    <div class="row">
                        {{ csrf_field() }}
                        <div class="form-group col-xs-12 col-sm-12 col-md-2 col-lg-2">
                            @include('commons.asterix-sm')<label>DESDE</label>
                            <input type="text" id="date_init" name="date_init" class="datepicker form-control form-control-sm" placeholder="dd/mm/aaaa">
                        </div>
                        <div class="form-group col-xs-12 col-sm-12 col-md-2 col-lg-2">
                            @include('commons.asterix-sm')<label>HASTA</label>
                            <input type="text" id="date_end" name="date_end" class="datepicker form-control form-control-sm" placeholder="dd/mm/aaaa">
                        </div>
                        <div class="form-group col-xs-12 col-sm-12 col-md-2 col-lg-2">
                            <label>Proveedor</label>
                            <select class="selectpicker show-tick form-control form-control-sm" name="select_supplier" id="supplier_id" data-live-search="true"  title="Seleccionar" data-width="100%" data-style="btn-info">
                                <option value="">TODOS LOS PROVEEDORES</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{$supplier->id}}" @if((isset($article) && $article->supplier_id == $supplier->id) || (old('supplier_id') == $supplier->id)) selected @endif>{{$supplier->business_name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-xs-12 col-sm-12 col-md-2 col-lg-2">
                            <br />
                            <a href="#" id="find" class="btn btn-md btn-success">BUSCAR</a>
                        </div>
                    </div>
                </form>
            </div>

            <div class="col-xs-12 col-sm-12 col-md-11 col-lg-11">
                <div class="card border-primary">
                    <div class="card-header">
                        <table class="display " id="table">
                            <thead>
                            <tr>
                                <th>PROVEEDOR</th>
                                <th>TIPO DE FACTURA</th>
                                <th>PUNTO DE VENTA</th>
                                <th>NÚMERO DE FACTURA</th>
                                <th>FECHA</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('custom_scripts')
    @include('bootstrap-select.script')
    @include('bootstrap-datepicker.script')
    @include('datatables.script')
    @include('buys.jslist')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.1/bootstrap3-typeahead.min.js"></script>
@endsection
