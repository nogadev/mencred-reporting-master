@extends('layouts.app')
@section('custom_styles')
    @include('datatables.style')
@endsection
@section('content')
<div class="container">
	<h3>Lista de Vendedores</h3>
    <hr>
	<div class="container">
	<a href="{{ route('sellers.create') }}" class="btn btn-lg btn-info">NUEVO</a>
	</div>
	<hr>
    <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
		<table id="table" class="compact hover nowrap row-border">
			<thead>
				<tr class="text-center">
					<th>NOMBRE</th>
					<th>META</th>
					<th>COMISION</th>
					<th></th>
					<th class="no-order no-search fit"></th>
				</tr>
			</thead>
			<tbody>
			@foreach($sellers as $row)
				<tr>
					<td>{{ $row->name }}</td>
					<td class="text-center">$ {{ number_format($row->goal, 2,',','.') }}    </td>
					<td class="text-center">{{ $row->commission }} %</td>
					<td>@if($row->trashed()) INACTIVO @endif</td>
					<td>
						@if($row->trashed())
							<form action="{{ route('sellers.restore', $row->id) }}" method="post" style="display: inline;">
                                {{ method_field('PATCH') }}
                                {{ csrf_field() }}
                                <button type="submit" class="btn btn-sm btn-success">Restaurar</button>
							</form>
                        @else
						<div style="display: inline;">
							<form method="get" action="{{route('sellers.edit', $row)}}" style="display: inline;">
								<button type="submit" class="btn btn-sm btn-primary">Editar</button>
							</form>
							{{-- <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#confirmModal" onclick="setDeleteAction('{{ route('sellers.destroy', $row) }}','{{$row->name}}');">Eliminar</button> --}}
						</div>
						@endif
					</td>
				</tr>
			@endforeach
			</tbody>
		</table>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Confirmar</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form id="delForm" action="#" method="post" style="display: inline;">
					{{ method_field('DELETE') }}
					{{ csrf_field() }}
					<p>Realmente desea eliminar <span id="delName" style="display: inline;"></span>?</p>
					<button type="submit" class="btn btn-danger">Eliminar</button>
				</form>
			</div>
		</div>
	</div>
</div>

@endsection
@section('custom_scripts')
	@include('datatables.script')
    <script>
        $(document).ready(function() {
			var table = $('#table').DataTable( {
				"createdRow": function( row, data, dataIndex){
					if (data[1] == 'INACTIVO') {
						$(row).addClass('deleted');
					}
				}
			});
        });
		function setDeleteAction(link, name){
			$("#delForm").attr('action', link);
			$("#delName").html(name);
		}
    </script>
@endsection
