<script type="text/javascript">

    const data = @json($buys);

    let formatter = new Intl.NumberFormat(['ban', 'id'] ,
    { style: 'currency',
        currency: 'ARS',
        currencyDisplay: 'narrowSymbol',
        currencySign: 'accounting',
    });

    $.each(data, function(i, item){

        let button_download = "";
        let classButtons = "";
        if(data[i].file_route!= null){
            button_download = '<a class="btn btn-info btn-circle" data-toggle="tooltip" data-placement="top" title="Descargar" onclick="viewFile(' + data[i].id + ')"><i class="fa fa-file-download" aria-hidden="true"></i></a>';
            classButtons = "btn-group";
        }else{
            button_download = '<a id="file_download_' + data[i].id + '" class="btn btn-info btn-circle" data-toggle="tooltip" data-placement="top" title="Descargar" onclick="viewFile(' + data[i].id + ')" style="display:none"><i class="fa fa-file-download" aria-hidden="true"></i></a>';
        }

        let buy_date = item.date;
        let formatedDate = buy_date.split('-');
        let dateCell = formatedDate[2].substr(0,2) + '/' + formatedDate[1] + '/' + formatedDate[0];      
        data[i].date = dateCell;
        data[i].total = formatter.format(parseFloat(item.total).toFixed(2));
        data[i].buttons =  
            '<div id="classButtons_' + data[i].id + '" class="' + classButtons + '">' +
                '<a class="btn btn-primary btn-circle" data-toggle="tooltip" data-placement="top" title="Adjuntar" onclick="exploreFile(' + data[i].id + ')"><i class="fa fa-paperclip" aria-hidden="true"></i></a>' +
                button_download +     
            '</div>' +
            '<form enctype="multipart/form-data" id="form_' + data[i].id + '" style="display:none">' +
                '<input  type="file" id="file_' + data[i].id + '" name="file" accept=".pdf">' +
            '</form>'
    });

    const table = $('#table').DataTable( {
        iDisplayLength: 50,
        order:[[0,"dec"]],
        columnDefs: [{ className: "dt-right", "targets": [2,3,5] },{"targets": 6, "orderable": false}],
        "columns": [
            { "data": "date" },
            { "data": "description" },
            { "data": "subsidiary_number" },
            { "data": "voucher_number" },
            { "data": "name" },
            { "data": "total" },
            { "data": "buttons" }
        ],
        data:           data,
        deferRender:    true,
        orderCellsTop: true,
        fixedHeader: true
    });

    $('#table thead tr').clone(true).appendTo( '#table thead' );
    $('#table thead tr:eq(1) th').each( function (i) {
        if (i !== 6){
            var title = $(this).text();
            $(this).html( '<input type="text" id="search_'+title+'" class="form-control form-control-sm" placeholder="Buscar '+title+'" />' );

            $( 'input', this ).on( 'keyup change', function () {
                if ( table.column(i).search() !== this.value ) {
                    table
                        .column(i)
                        .search( this.value )
                        .draw();
                }
            } );
        }			
    } );

    function exploreFile(id){
        $('#file_' + id).click();
        $('#file_' + id).on('change', function(){
            formFile(id);
        });
    }

    function viewFile(id){
        $(".preloader").css("display", "block");
        let token = $("input[name='_token']").val();
        $.ajax({
            url: "{{route('buys.view-file')}}",
            method: 'POST',
            data: {id:id, _token:token},
            dataType:"json",
            success: function(response) {
                if(response){
                    $(".preloader").css("display", "none");
                    let url_file = "{{route('buys.view-file-get.file')}}" + "?id=" + id;
                    window.open(url_file , '_blank');
                }else{
                    fileNotFound();
                }
                
            },
            error: function(response) {
                fileNotFound();
            }
        }); 
    }

    function fileNotFound(){
        $(".preloader").css("display", "none");
        $.notify({
                icon: 'fas fa-exclamation-circle',
                message: "No se encontro el comprobante"
            },{
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

    function formFile(id){
        $(".preloader").css("display", "block");
        let formData = new FormData(document.getElementById('form_' + id));
        formData.append('_token', $('input[name=_token]').val());
        formData.append('id', id);
        $.ajax({
            url: "{{route('buys.store-file')}}",
            type: "POST",
            dataType: "html",
            data: formData,
            cache: false,
            contentType: false,
	        processData: false,
            success: function (response) {
                $(".preloader").css("display", "none");
                $("#classButtons_" + id).addClass('btn-group');
                $("#file_download_" + id).css("display", "block");
                $.notify({
                    icon: 'fas fa-success-circle',
                    message: "Comprobante guardado con éxito"
                }, {
                    type: "success",
                    showProgressbar: false,
                    animate: {
                        enter: 'animated bounceIn',
                        exit: 'animated bounceOut'
                    }
                });
            },
            error: function(response) {
                $(".preloader").css("display", "none");
                errors = $.parseJSON(response.responseText);
                $.each(errors, function(key, value){
                    $.notify({
                            icon: 'fas fa-exclamation-circle',
                            message: value
                        },{
                            type: "warning",
                            showProgressbar: false,
                            mouse_over: 'pause',
                            animate: {
                                enter: 'animated bounceIn',
                                exit: 'animated bounceOut'
                            }
                        }
                    );
                });
            }
        });
    }

</script>
