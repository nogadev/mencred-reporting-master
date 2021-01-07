@extends('layouts.app')
@section('custom_styles')
    @include('bootstrap-select.style')
    @include('bootstrap-datepicker.style')
    @include('datatables.style')
    @include('loader.style')
@endsection
@section('content')
    <div class="container-fluid">
        <h3>Ver factura de compra</h3>
        <hr>
        <div class="clearfix"></div>
        <div class="row justify-content-center">
            <div class="col-xs-12 col-sm-12 col-md-11 col-lg-11">
                <form id="buyForm" method="post" action="/buys" autocomplete="off">
                    @include('buys.edit.form')
                    @include('buys.edit.modal.confirm')
                    <div class="preloader" style="display:none"></div>
                    <hr>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('custom_scripts')
    @include('bootstrap-select.script')
    @include('bootstrap-datepicker.script')
    @include('commons.autonumeric')
    @include('datatables.script')
    @include('buys.edit.js')
@endsection
