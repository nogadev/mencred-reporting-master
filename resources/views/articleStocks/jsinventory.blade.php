<script type="text/javascript">
    $(document).ready(function() {
        $('#table thead tr').clone(true).appendTo( '#table thead' );
		$('#table thead tr:eq(1) th').each( function (i) {

            var title = $(this).text();
            $(this).html( '<input type="text" class="form-control form-control-sm" id="search_'+title+'" placeholder="Buscar '+title+'"/>' );

            $( 'input', this ).on( 'keyup change', function () {
                if ( table.column(i).search() !== this.value ) {
                    table
                        .column(i)
                        .search( this.value )
                        .draw();
                }
            } );

		} );

        $('select[name="store_id"]').selectpicker(selectBootDefaOpt);
        $('select[name="company_id"]').selectpicker(selectBootDefaOpt);

        $('select[name="store_id"]').on('change', function(){
            buscar();
        });

        $('select[name="company_id"]').on('change', function(){
            buscar();
        });

        function buscar(){
            var store_id =$('select[name="store_id"]').val();
            var company_id = $('select[name="company_id"]').val();
            var url = "{{url( '/inventory') }}";
            url = url + "?store_id=" + store_id + "&company_id=" + company_id;
            $(location).attr('href',url);
        }

        var response=@json($articles);
        var data = [];
        $.each(response, function(i, item){
            let update_date;

            if(item.update_inventory_date == null){
                update_date = item.created_at;
            }else{
                update_date = item.update_inventory_date;
            }

            let formatedDate = update_date.split('-');
            let dateCell = formatedDate[2].substr(0,2) + '/' + formatedDate[1] + '/' + formatedDate[0];

            data.push({
                article: item.name,
                company: item.company,
                trademark: item.trademark,
                stock: '<input id="'+item.id+'" name="stock" type="number" value="'+Number(item.stock)+'" style="width:50%" class="form-control form-control-sm position-absolute text-center">'+Number(item.stock),
                updated_at: dateCell

            });
        });

        table = $('#table').DataTable( {
            iDisplayLength: 50,
            columnDefs: [{
                className: "dt-center", "targets": "_all",
                className: "pb-3 position-relative date-stock", "targets": [3]
            }],
            "columns": [
                { "data": "article" },
                { "data": "company" },
                { "data": "trademark" },
                { "data": "stock" },
                { "data": "updated_at" }
            ],
			data:           data,
            deferRender:    true,
            orderCellsTop: true,
        	fixedHeader: true
        } );

        $('.date-stock input').each( function (i) {
            if (parseInt($('.date-stock input')[i].value)>0){
                $($(this)[0]).css('font-weight','bold');
            }
        } );

        $(document).click(function(){
            $('.date-stock input').each( function (i) {
                if (parseInt($('.date-stock input')[i].value)>0){
                    $($(this)[0]).css('font-weight','bold');
                }
            } );
        });

        $(document).keyup(function () {
            $('.date-stock input').each( function (i) {
                if (parseInt($('.date-stock input')[i].value)>0){
                    $($(this)[0]).css('font-weight','bold');
                }else{
                    $($(this)[0]).css('font-weight','normal');
                }
            } );
        });
    });

    $(document).on('keypress', 'input[name="stock"]', function(e) {
        //Si el usuario presiona Enter
        if(e.which == 13) {
            e.preventDefault();
            //llamada ajax para guardar
            var id = this.id;
            var stock = this.value;
            setStock(id, stock);
        }
    });

    function setStock(id, stock){
		$.ajax({
            type: "GET",
            url: "{{route('articlestocks.setstock')}}",
            data: {id:id , stock:stock},
            dataType: "json",
            success: function (response) {
				$("#stock_old_"+id).val(this.value);
                var d = new Date();
                var strDate = (d.getDate()<10?'0'+d.getDate():d.getDate()) + "/" + ((d.getMonth()+1)<10?'0'+(d.getMonth()+1):(d.getMonth()+1)) + "/" + d.getFullYear();
                $($('#'+id).parent()).next()[0].innerText=strDate;

				$.notify(
                    {
                        icon: 'fas fa-success-circle',
                        message: "Stock actualizado con éxito"
                    },
                    {
                        type: "success",
                        showProgressbar: false,
                        animate: {
                            enter: 'animated bounceIn',
                            exit: 'animated bounceOut'
                        }
                    }
                );

            },
            error: function(jqXHR, textStatus, errorThrown) {
                $.notify(
                    {
                        icon: 'fas fa-exclamation-circle',
                        message: "Se ha producido un error"
                    },
                    {
                        type: "warning",
                        showProgressbar: false,
                        mouse_over: 'pause',
                        animate: {
                            enter: 'animated bounceIn',
                            exit: 'animated bounceOut'
                        }
                    }
                );
            }
        });

    }

</script>
