@extends('layouts.app')
@section('custom_styles')
	@include('bootstrap-select.style')
	@include('datatables.style')
    @include('loader.style')
@endsection
@section('content')
    <div class="container">
        <h3>Modificar datos de Proveedores</h3>
        <hr>
        <div class="clearfix"></div>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <form method="post" action="{{route('suppliers.update', $supplier)}}">
                {{ method_field('PUT') }}
                @include('suppliers.form')
                <hr>
                <button type="submit" class="btn btn-success">Modificar</button>
                <a href="{{ route('suppliers.index') }}" class="btn btn-danger">Cancelar</a>
            </form>
        </div>
        <div class="clearfix"></div>
        <br>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            @include('suppliers.buy.grids')
        </div>
    </div>

@endsection
@section('custom_scripts')
    @include('bootstrap-select.script')
    @include('commons.autosize')
    @include('datatables.script')
    @include('suppliers.js')
    @include('suppliers.buy.jsgrid')
@endsection
