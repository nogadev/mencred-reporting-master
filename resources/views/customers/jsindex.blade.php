<script type="text/javascript">

    $(document).ready(function() {
        $('#table thead tr').clone(true).appendTo( '#table thead' );
		$('#table thead tr:eq(1) th').each( function (i) {
            if(i < 7){//donde 7 es la ultima columna la cualtiene botones
                var title = $(this).text();
                $(this).html( '<input type="text" id="search_'+title+'" class="form-control form-control-sm" placeholder="Buscar '+title+'"/>' );
                $( 'input', this ).on( 'keyup change', function () {
                    if ( table.column(i).search() !== this.value ) {
                        table.column(i).search( this.value ).draw();
                    }
                } );
            }		
		} )

        const data = @json($customers);
        const table = $('#table').DataTable( {
            iDisplayLength: 50,
            columnDefs: [{
                "targets": 7,
                "orderable": true,
                "render": function (data, type, row) {
                    var button = '<a href="/customers/'+data.customer_id+'/edit" class="btn btn-sm btn-primary">VER</a>';
                    return button;
                }
            }],
            "columns": [
                { "data": "customer_name" },
                { "data": "route_name" },
                { "data": "seller_name" },
                { "data": "commercial_town" },
                { "data": "commercial_neighborhood" },
                { "data": "commercial_address" },
                { "data": "doc_number" },
                { "data": null}
            ],
            data:           data,
            deferRender:    true,
            orderCellsTop: true,
        	fixedHeader: true
        } );
    });

    $("#print").click(function(){
        const search = $('input[type="search"]').val();
        const orderBy = $('#orderBy').val();
        let url = "{{ route('print.customers.all') }}";
        url = url + "?search=" + search+"&orderBy=" + orderBy;
		window.open(url, '_blank');
    });

</script>
