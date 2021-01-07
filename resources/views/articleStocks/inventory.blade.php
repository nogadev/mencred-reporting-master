@extends('layouts.app')

@section('custom_styles')
	@include('bootstrap-select.style')
    @include('datatables.style')
	@include('articleStocks.tableStyles.style')
@endsection
<!-- <link rel="stylesheet" href="{{ asset('app/public/css/inventario.css') }}"> -->
@section('content')
	<div class="container">
		<h3>INVENTARIO</h3>
	    <hr>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 ">
		{!! Form::open(['method' => 'GET', 'id' => 'frm_inventory_search' ,'url' => Request::fullUrl() , 'role' => 'search'])  !!}
			<div class="container">
				<div class="row">
					<div class="col-md-3">
						<label>DEPOSITO</label>
						<select name="store_id">

							@foreach($stores as $store)
								<option value="{{$store->id}}" @if(request('store_id')==$store->id) selected @endif >{{$store->name}}</option>
							@endforeach
						</select>
					</div>
					<div class="col-md-3">
						<label>EMPRESA</label>
						<select name="company_id">
							@foreach($companies as $company)
								<option value="{{$company->id}}" @if(request('company_id')==$company->id) selected @endif >{{$company->name}}</option>
							@endforeach
						</select>
					</div>
				</div>
			</div>
			<hr>
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="card border-primary">
					<div class="card-header">
						<table class="display" id="table">
							<thead class="thead-dark">
								<tr class="bg-info text-white">
									<th>ARTICULO</th>
									<th>EMPRESA</th>
									<th>MARCA</th>
									<th>STOCK</th>
									<th>FECHA ACTUALIZACIÓN</th>
								</tr>
							</thead>
						</table>
					</div>
				</div>
			</div>
		{!! Form::close() !!}
		</div>

@endsection
@section('custom_scripts')
    @include('articleStocks.jsinventory')
    @include('commons.autonumeric')
    @include('bootstrap-select.script')
	@include('datatables.script')
@endsection
