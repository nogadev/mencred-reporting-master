<script type="text/javascript">

    let net_total,tax_total,additional_tax_total,total;

    let buttonSend = '<button type="button" class="btn btn-success" id="openModal">Guardar</button>';
    let buttonCancel = '<a href="{{ route("buys.list-buy") }}" class="btn btn-danger ml-1">Cancelar</a>';

    $(document).ready(function() {

        let response = @json($data);

        net_total = currency_format("#net_total");
        tax_total = currency_format("#tax_total");
        additional_tax_total = currency_format("#additional_tax_total");
        total = currency_format("#total");
        arr = response.date.split(' ')[0].split('-');
        date = arr[2]+'/'+arr[1]+'/'+arr[0];
        
        net_total.set(parseFloat(response.net_total_by_voucher));
        tax_total.set(parseFloat(response.iva_tax));
        additional_tax_total.set(parseFloat(response.additional_tax_total));
        total.set(parseFloat(response.total));

        $("#id").val(response.id);
        $("#business_name").val(response.supplier_name);
        $("#code").val(response.supplier_code);
        $("#address").val(response.address);
        $("#date").val(date);
        $("#subsidiary_number").val(response.subsidiary_number);
        $("#voucher_number").val(response.voucher_number);
        $("#perception_iibb").val(response.perception_iibb);

        //acciones si la proforma esta abierta

        if(response.voucher_type == "PROFORMA" && response.buy_status == 11){
            
            editInputs(false);

            $('#date').datepicker({
                format: "dd/mm/yyyy",
                maxViewMode: "decades",
                startDate: date,
                endDate: "0d",
                language: "es",
                autoclose: true
            });

            let voucherTypes = @json($voucherTypes);

            $("#voucher_type_id option").remove();

            $.each(voucherTypes, function(i, item){
                if(item.id == 14){
                    $('#voucher_type_id').append($('<option />', {
                        text: item.description,
                        value: item.id,
                        selected: true,
                    }));
                }else{
                    $('#voucher_type_id').append($('<option />', {
                        text: item.description,
                        value: item.id,
                    })); 
                }
            })

            $('#buyForm').append(buttonSend);
            $('#buyForm').append(buttonCancel);
           
            $('#sendForm').on('click', function(){
                let message, type;
                $('#sendForm').attr("disabled", true);
                $('#confirmEditModal').modal('hide');
                if($("#voucher_type_id").val() !== "14"){
                    $(".preloader").css("display", "block");

                    $("#status_id").val(12);

                    let articlesJSON = []; 

                    $.each(response.articles, function(i, item){
                        articlesJSON.push({
                            article_id      : item.id,
                            item_no         : item.pivot.item_no,
                            quantity        : item.pivot.quantity,
                            article         : item.description,
                            net             : parseFloat(item.pivot.net),
                            bonus_percentage: parseFloat(item.pivot.bonus_percentage),
                            bonus           : parseFloat(item.pivot.bonus),
                            tax_percentage  : parseFloat(item.pivot.tax_percentage),
                            tax             : parseFloat(item.pivot.tax),
                            subtotal        : parseFloat(item.pivot.subtotal),
                            tax_id          : item.tax_id
                        });
                    }); 

                    $("#art_data").html(JSON.stringify(articlesJSON));

                    let dataForm = $('#buyForm').serialize();  
                
                    $.ajax({
                        url: "{{ route('buys.store') }}",
                        method: 'POST',
                        data: dataForm,
                        dataType:"json"
                    }).always(function( data ) {
                        if(data.status == 200){
                            $(".preloader").css("display", "none");
                            message = 'Compra registrada';
                            type = "success";
                            notify(type, message);
                            $('#openModal').remove();
                            editInputs(true);
                            $("#voucher_type_id").on('mousedown', function(e) {
                                e.preventDefault();
                                this.blur();
                                window.focus();
                            });
                            $("#voucher_type_id").on('keydown', function(e) {
                                e.preventDefault();
                                this.blur();
                                window.focus();
                            });
                            $('#date').datepicker("destroy");
                        }else{
                            $(".preloader").css("display", "none");
                            message = "Se ha producido un error";
                            type = "warning";
                            notify(type, message);
                            setTimeout("location.reload(true);",2000);
                        }
                    });
                }else{
                    $('#sendForm').attr("disabled", false); 
                    message = "El tipo de comprobante no puede ser proforma";
                    type = "warning";
                    notify(type, message);
                }           
            });
        }else{
            $('#buyForm').append(buttonCancel);
        }

        let data = [];
        $.each(response.articles, function(i, item){
            data.push({
                description: item.description,
                quantity: item.pivot.quantity,
                net: item.pivot.net + ' $',
                bonus: item.pivot.bonus + ' $',
                tax: item.pivot.tax + ' $',
                subtotal: item.pivot.subtotal + ' $',
                item: item.pivot.item_no,
            });
        });  

        table = $('#tableArticlesEnd').DataTable( {
            iDisplayLength: 50,
            columnDefs: [{ className: "dt-right", "targets": [2,3,4,5,6] }],
            "columns": [
                { "data": "item" },
                { "data": "description" },
                { "data": "quantity" },
                { "data": "net" },
                { "data": "bonus" },
                { "data": "tax" },
                { "data": "subtotal" }		                
            ],
            data:           data,
            deferRender:    true
        } );

        $("#openModal").on('click', function(){
            $('#confirmEditModal').modal('show');
        })

        let storeOptions = $.extend( {}, selectBootDefaOpt);
        storeOptions["noneResultsText"] += "<br><button type='button' class='btn btn-success' id='openModal'>Crear nuevo</button>";
        $('select[name="store_id"]').selectpicker(storeOptions);
    })

    function editInputs(status){
        $("#subsidiary_number").attr("readonly", status); 
        $("#voucher_number").attr("readonly", status); 
        $("#date").attr("readonly", status); 
        $("#voucher_type_id").attr("readonly", status);
    }

    function notify(type, message){
        $.notify(
            {
                // options
                icon: 'fas fa-exclamation-circle',
                message: message
            },{
                // settings
                type: type,
                showProgressbar: false,
                mouse_over: 'pause',
                animate: {
                    enter: 'animated bounceIn',
                    exit: 'animated bounceOut'
                }
            }
        );  
    }

    function currency_format($id_currency){
        return new AutoNumeric($id_currency, {currencySymbol : ' $', decimalCharacter : ',', digitGroupSeparator : '.',currencySymbolPlacement:'s'});
    }
    
</script>
