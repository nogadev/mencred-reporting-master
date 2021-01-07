<script type="text/javascript">

    var path_claims = "{{ route('claimsJson') }}";
    var path_fees = "{{ route('feesJson') }}";
    var path_credit = "{{ route('creditJson') }}";

    var table = $('#table').DataTable();
    table.order( [ 0, 'desc' ] ).draw();

    //Formato de moneda argentina
    var formatter = new Intl.NumberFormat(['ban', 'id'] ,
    { style: 'currency',
        currency: 'ARS',
        currencyDisplay: 'narrowSymbol',
        currencySign: 'accounting',
    });

    $('#table tbody').on('dblclick', 'tr', function () {
        var data = table.row( this ).data();
        showClaims(data[0]);
    } );

    function showClaims(id){
        if(id > 0){
            $("#credit_id").val(id);
            $.get("{{ route('claimsJson') }}", { credit_id: id } , function (data) {
                claimTable.clear().draw();
                $.each(data, function(i, item) {

                    var arr = item.init_date.split(' ')[0].split('-');
                    var init_date = arr[2] + '/' + arr[1] + '/' + arr[0];

                    var end_date = '';
                    if(item.end_date != null){
                        var arr = item.end_date.split(' ')[0].split('-');
                        end_date = arr[2]+'/'+arr[1]+'/'+arr[0];
                    }

                    var action = '';
                    if(item.status_id == 7){
                        var action = '<a onclick="resolvClaim('+item.id+','+i+')" class="btn btn-success btn-circle" data-toggle="tooltip" data-placement="top" title="Confirmar"><i class="fa fa-check" aria-hidden="true"></i></a>';
                    }
                    var claimId = item.id;
                    var printClaim = "{{url( '/print/claim') }}/" + claimId;
                    var print = '<a id="printClaim" target="_blank" href='+printClaim+' class="btn btn-warning btn-circle" data-toggle="tooltip" data-placement="top" title="IMPRIMIR"><i class="fa fa-print" aria-hidden="true"></i></a>';

                    claimTable.row.add({
                        id:         item.id,
                        detail:     '',
                        type:       item.type,
                        init_date:  init_date,
                        end_date:   end_date,
                        status:     item.status.status,
                        trakings:   item.claimtrakings,
                        actions:    action,
                        print:      print
                    }).draw( false );
                });
            });
            $("#claimModal").modal('show');
        }
    }


    function showFees(idcredit){

        colorRow(idcredit);

        if(idcredit > 0){
            $.get(path_fees, { credit_id: idcredit } , function (data) {
                $("#tableFees tbody").empty();

                var paid=0;
                let payment_fee=0;

                $.each(data.fees, function(i, item) {
                    let payment_amount = parseFloat(item.payment_amount);
                    let paid_amount = parseFloat(item.paid_amount);

                    if (payment_amount == 0){
                        payment_amount = parseFloat(data.fee_amount);    
                    }

                    reason = '';
                    if(item.reason != null) {
                        reason = item.reason.reason;
                    }
                    var arr = item.paid_date.split(' ')[0].split('-');
                    var paid_date = arr[2] + '/' + arr[1] + '/' + arr[0];
                    paid = paid + paid_amount;
                    payment_fee += payment_amount;

                    let paid_style ="";
                    if(paid_amount>0) {
                        paid_style='font-weight:bold';
                    }

                    var line = "<tr><td>"+item.number+"</td>"+
                        "<td>"+paid_date+"</td>"+
                        "<td style='"+paid_style+"'>"+formatter.format(paid_amount)+"</td>"+
                        "<td>"+formatter.format(payment_amount)+"</td>"+
                        "<td>"+reason+"</td></tr>";
                    $("#tableFees tbody").append(line);
                });

                $("#total_modal").val(formatter.format(data.total));
                $("#paid_modal").val(formatter.format(paid));
                $("#balance_modal").val(formatter.format((data.total-paid)));

                $("#payment_modal").val(formatter.format((payment_fee)));

                $("#linkPrint").attr('href', "{{url( '/print/credit/detail') }}/" + idcredit);


                total_fee = parseFloat((data.fees[0])?data.fees[0].payment_amount:0) * data.fees.length;

                if(paid > 0 && data.total > 0 && total_fee > 0 ) {
                    $("#efectivity_modal").val(parseFloat(paid*100/payment_fee).toFixed(2)+"%");
                } else {
                    $("#efectivity_modal").val("0%");
                }

            });
            $("#feesModal").modal('show');
        }
    }


    function showDetail(idcredit){

        colorRow(idcredit);

        if(idcredit > 0){
            $.get(path_credit, { credit_id: idcredit } , function (data) {
                $("#tableArticles tbody").empty();

                $.each(data.articles, function(i, item) {
                    var line = "<tr id='"+idcredit+"'><td>"+item.id+"</td>"+
                        "<td>"+item.description+"</td>"+
                        "<td>"+item.pivot.serial_number+"</td>"+
                        "<td>"+formatter.format(item.pivot.price)+"</td>"+
                        "<td>"+item.pivot.quantity+"</td>"+
                        "<td>"+formatter.format(parseFloat(item.pivot.quantity)*parseFloat(item.pivot.price))+"</td></tr>";
                    $("#tableArticles tbody").append(line);
                });

                $("#observation_modal").val(data.observation);
            });
            $("#detailModal").modal('show');
        }
    }

    function setObservation(){
        var data = {
            'id': $('#tableArticles tbody tr').attr("id"),
            observation: $('#observation_modal').val(),
            "_token": $("meta[name='csrf-token']").attr("content")
        };
        $.ajax({
            type: "POST",
            url: "{{route('updateObservation')}}",
            data: data,
            dataType: "json",
            success: function () {
                $.notify(
                    {
                        // options
                        icon: 'fas fa-success-circle',
                        message: "Observación de credito actualizada"
                    },{
                        // settings
                        type: "success",
                        showProgressbar: false,
                        animate: {
                            enter: 'animated bounceIn',
                            exit: 'animated bounceOut'
                        }
                    }
                );
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $.notify({
                    icon: 'fas fa-exclamation-circle',
                    message: "Se ha producido un error"
                }, {
                    type: "warning",
                    showProgressbar: false,
                    mouse_over: 'pause',
                    animate: {
                        enter: 'animated bounceIn',
                        exit: 'animated bounceOut'
                    }
                });
            }
        });
        $('#detailModal').modal('hide');
    }

    function colorRow(id){
        $('#'+id).addClass('bg-secondary text-light');
        $($('#'+id).children()[1]).addClass('bg-secondary');

        $('#detailModal').on('hidden.bs.modal', function (e) {
            colorRowDel();
        })

        $('#feesModal').on('hidden.bs.modal', function (e) {
            colorRowDel();
        })

        function colorRowDel(){
            $('#'+id).removeClass('bg-secondary text-light');
            $($('#'+id).children()[1]).removeClass('bg-secondary');
        }
    }

</script>
