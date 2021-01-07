<script type="text/javascript">
    var table,tableArticlesEnd,tableArticlesSelect,a_tax_p,proffit,net_price,additional_tax_p,additional_tax;
    var net_total,tax_total,additional_tax_total,total;
    var taxesJSON = [];
    var articlesJSON = [];
    var itemNo = 0;
    var taxesID =  [];
    let a_quantity = [];
    let a_net = [];
    let a_bonus_p = [];
    let a_bonus = [];
    let a_tax = [];
    let a_subtotal = [];

    $(document).ready(function() {

        $.each(@json($taxes) , function ($key, tax){
            taxesID[tax.value] = tax.id;
        });

        var supplierOptions = $.extend( {}, selectBootDefaOpt);
        supplierOptions["noneResultsText"] += "<br><button type='button' class='btn btn-success' onclick='newSupplier()'>Crear nuevo</button>";
        $('select[name="supplier_id"]').selectpicker(supplierOptions);

        $('select[name="voucher_type_id"]').selectpicker(selectBootDefaOpt);

        var companyOptions = $.extend( {}, selectBootDefaOpt);
        companyOptions["noneResultsText"] += "<br><button type='button' class='btn btn-success' onclick='newCompany()'>Crear nueva</button>";
        $('select[name="company_id"]').selectpicker(companyOptions);

        var articleOptions = $.extend( {}, selectBootDefaOpt);
        articleOptions["noneResultsText"] += "<br><button type='button' class='btn btn-success' onclick='newArticle()'>Crear nuevo</button>";
        $('select[name="a_id"]').selectpicker(articleOptions);

        var storeOptions = $.extend( {}, selectBootDefaOpt);
        storeOptions["noneResultsText"] += "<br><button type='button' class='btn btn-success' onclick='newStore()'>Crear nuevo</button>";
        $('select[name="store_id"]').selectpicker(storeOptions);

        $('#date').datepicker({
            format: "dd/mm/yyyy",
            maxViewMode: "decades",
            endDate: "0d",
            language: "es",
            autoclose: true
        });

        $('#date').datepicker('setDate', '0d');

        additional_tax = currency_format("#additional_tax");
        additional_tax_p = percentage_format("#additional_tax_p");

        tableArticlesEnd = $('#tableArticlesEnd').DataTable({
            columnDefs: [{ className: "dt-right", "targets": [2,3,4,5,6] }]
        });

        net_total = currency_format("#net_total");
        tax_total = currency_format("#tax_total");
        additional_tax_total = currency_format("#additional_tax_total");
        total = currency_format("#total");

        //Carga los datos de la tabla final en el formulario para ser enviados

        $('#buyForm').submit(function(eventObj) {
            $("#art_data").html(JSON.stringify(articlesJSON));
            return true;
        });

        $('#tableArticlesList thead tr').clone(true).appendTo( '#tableArticlesList thead' );
		$('#tableArticlesList thead tr:eq(1) th').each( function (i) {

            var title = $(this).text();
            $(this).html( '<input type="text" class="form-control form-control-sm" id="search_'+title+'" placeholder="Buscar '+title+'"/>' );

            $( 'input', this ).on( 'keyup change', function () {
                if ( table.column(i).search() !== this.value ) {
                    table.column(i)
                    .search( this.value )
                    .draw();
                }
            } );
		} );

        let response = @json($articles);
        let data = [];

        $.each(response, function(i, item){
            data.push({
                article: item.description,
                code: item.id,
                barcode: item.barcode,
            });
        });

        table = $('#tableArticlesList').DataTable( {
            iDisplayLength: 10,
            "columns": [
                { "data": "article" },
                { "data": "code" },
                { "data": "barcode" }
            ],
            data:           data,
            deferRender:    true,
            orderCellsTop: true,
            fixedHeader: true,
            "fnRowCallback": function( nRow, data) {
                $(nRow).attr("id", 'row' + data['code']);
                return nRow;
            }
        } );

        //Validar si el articulo ya esta cargado en la tabla de seleccion o en la tabla final

        $( "#tableArticlesList tbody" ).on( "click", "tr", function() {
            let articleSelected = $($( this ).children()[0]).text();
            let codeSelected = $($( this ).children()[1]).text();

            if(!a_quantity[codeSelected]){
                addRow(articleSelected,codeSelected);
                $( this ).addClass('bg-warning');
            }else{
                $.notify(
                    {
                        // options
                        icon: 'fas fa-exclamation-circle',
                        message: "El artículo ya esta cargado."
                    },{
                        // settings
                        element:  $('#addArticleList'),
                        position: 'fixed',
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

        tableSelected = $('#tableArticlesSelect').DataTable({});

        $( "#additional_tax" ).on( 'keyup change', function () {
            setTotals("add");
        });

        $( "#additional_tax_p" ).on( 'keyup change', function () {
            setTotals("add");
        });
    });

    function newSupplier(){
        $('#newSupplierModal').modal('show');
    }

    function storeNewSupplier(){
        if ($("#new_supplier_code").val() != "" ||
            $("#new_supplier_name").val() != "") {
                var _token = $('input[name="_token"]').val();
                var data = {
                    'code' : $("#new_supplier_code").val(),
                    'name' : $("#new_supplier_name").val(),
                    'business_name' : $("#new_supplier_name").val(),
                    _token : _token
                };

                $.ajax({
                    type: "POST",
                    url: "{{route('suppliers.fastStore')}}",
                    data: data,
                    dataType: "json",
                    success: function (response) {
                        $('select[name="supplier_id"]').append(
                            $("<option></option>")
                                .attr("value", response["id"])
                                .text(response["name"])
                        ).selectpicker('refresh');
                        $('select[name="supplier_id"]').selectpicker('val', response["id"]);
                        $('input[name="business_name"]').val(response["business_name"]);
                        $('input[name="code"]').val(response["code"]);
                        $('input[name="address"]').val(response["address"]);
                        $('input[name="perception_iibb"]').val(response["perception_iibb"]);

                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        $.notify(
                            {
                                // options
                                icon: 'fas fa-exclamation-circle',
                                message: "Se ha producido un error"
                            },{
                                // settings
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
                $("#new_supplier_code").val("");
                $("#new_supplier_name").val("");
                $("#newSupplierModal").modal('hide');
        } else {
            //Show error
        }
    }

    function setSupplierData() {
        var _token = $('input[name="_token"]').val();
        var data = {
            'id' : $('select[name="supplier_id"]').val(),
            _token : _token
        };
        $.ajax({
            type: "POST",
            url: "{{route('suppliers.getById')}}",
            data: data,
            dataType: "json",
            success: function (response) {
                $('input[name="business_name"]').val(response["name"]);
                $('input[name="code"]').val(response["code"]);
                $('input[name="address"]').val(response["address"]);
                $('input[name="perception_iibb"]').val(response["perception_iibb"]);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                $.notify(
                    {
                        // options
                        icon: 'fas fa-exclamation-circle',
                        message: "Se ha producido un error"
                    },{
                        // settings
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

    function newCompany(){
        $('#newCompanyModal').modal('show');
    }

    function storeNewCompany(){
        if ($("#new_company_name").val() != "") {
                var _token = $('input[name="_token"]').val();
                var data = {
                    'name' : $("#new_company_name").val(),
                    _token : _token
                };

                $.ajax({
                    type: "POST",
                    url: "{{route('companies.fastStore')}}",
                    data: data,
                    dataType: "json",
                    success: function (response) {
                        $('select[name="company_id"]').append(
                            $("<option></option>")
                                .attr("value", response["id"])
                                .text(response["name"])
                        ).selectpicker('refresh');
                        $('select[name="company_id"]').selectpicker('val', response["id"]);
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        $.notify(
                            {
                                // options
                                icon: 'fas fa-exclamation-circle',
                                message: "Se ha producido un error"
                            },{
                                // settings
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
                $("#new_company_name").val("");
                $("#newCompanyModal").modal('hide');
        } else {
            //Show error
        }
    }

    function newArticle(){
        $('#newArticleModal').modal('show');
    }

    function storeNewArticle(){
        if ($("#new_article_description").val() != "" &&
            $("#new_article_price_list").val() != "") {
                var _token = $('input[name="_token"]').val();
                var data = {
                    'description' : $("#new_article_description").val(),
                    'print_name' : $("#new_article_price_list").val(),
                    _token : _token
                };

                $.ajax({
                    type: "POST",
                    url: "{{route('articles.fastStore')}}",
                    data: data,
                    dataType: "json",
                    success: function (rNewArticle) {

                        table.row.add({
                            article: rNewArticle["description"],
                            code: rNewArticle["id"],
                            barcode: ""
                        }).draw();

                        cancelNewArticle();

                        $.notify(
                            {
                                // options
                                icon: 'far fa-check-circle',
                                message: "Producto creado"
                            },{
                                // settings
                                element:  $('#addArticleList'),
                                position: 'fixed',
                                type: "success",
                                showProgressbar: false,
                                mouse_over: 'pause',
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
                                // options
                                icon: 'fas fa-exclamation-circle',
                                message: "Se ha producido un error"
                            },{
                                // settings
                                element:  $('#addArticleList'),
                                position: 'fixed',
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
                $("#new_article_description").val("");
                $("#new_article_price_list").val("");
                $("#newArticleModal").modal('hide');
        }
    }

    //Calcular por articulo

    function calculate(source,typeBonus) {
        //typeBonus = 0 significa que calcula el descuento por los pesos;
        //typeBonus = 1 significa que calcula el descuento en porcentajes;

        let q = parseFloat(a_quantity[source].rawValue);
        let n = (a_net[source].rawValue) ? parseFloat(a_net[source].rawValue) : 0;
        let b_p = (a_bonus_p[source].rawValue) ? parseFloat(a_bonus_p[source].rawValue) : 0;
        let b = (a_bonus[source].rawValue) ? parseFloat(a_bonus[source].rawValue) : 0;
        let t_p = parseFloat($("#a_tax_p_"+source).val());
        let t = (a_tax[source].rawValue) ? parseFloat(a_tax[source].rawValue) : 0;
        let s = (a_subtotal[source].rawValue) ? parseFloat(a_subtotal[source].rawValue) : 0;

        if (typeBonus == 1){
            b = ((q * n) * b_p) / 100;
        }else{
            b_p = (b == 0) ? 0 : b * 100 / (q * n);
        }

        t = ((q * n) - (b)) * t_p / 100;
        s = ((q * n) - (b)) + t;

        a_tax[source].set(t);
        a_subtotal[source].set(s);
        a_bonus[source].set(b);
        a_bonus_p[source].set(b_p);
    }

    //Calcular los tolales en la tabla end al agregar, borrar y modificar items

    function setTotals(type,index){

        let net_total_val = net_total.getNumber();
        let tax_total_val = tax_total.getNumber();
        const addTaxP = additional_tax_p.getNumber();
        const addTax = additional_tax.getNumber();
        const bonus_total_val = (index) ? a_bonus[index].getNumber() : 0;
        const a_quantity_val = (index) ? parseInt(a_quantity[index].rawValue)  : 0;
        const a_tax_val =  (index) ? parseFloat(a_tax[index].rawValue) : 0;
        let a_net_val = (index) ? parseFloat(a_net[index].rawValue) : 0;
        let total_val = 0;
        let additional_tax_calc = 0;

        if(type == "add"){

            net_total.set(net_total_val + a_net_val * a_quantity_val - bonus_total_val);
            tax_total.set(tax_total_val + a_tax_val);

            net_total_val = net_total.getNumber();
            tax_total_val = tax_total.getNumber();

            total_val = net_total_val + tax_total_val;
            additional_tax_calc = (total_val * addTaxP) / 100 + addTax;

            additional_tax_total.set(additional_tax_calc);
            total.set(total_val + additional_tax_calc);

        }else if(type == "del"){
            a_net_val = a_net_val * a_quantity_val;

            net_total_val = net_total_val + bonus_total_val - a_net_val;
            tax_total_val = tax_total_val - a_tax_val;

            net_total.set(net_total_val);
            tax_total.set(tax_total_val);

            total_val = net_total_val + tax_total_val;
            additional_tax_calc = (total_val * addTaxP) / 100 + addTax;

            delete a_quantity[index];
            delete a_net[index];
            delete a_bonus[index];
            delete a_bonus_p[index];
            delete a_tax[index];
            delete a_subtotal[index];

            additional_tax_total.set(additional_tax_calc);
            total.set(total_val + additional_tax_calc);
        }
    }

    //Agregar productos en la tabla de seleccion del modal

    function addRow(articleSelected,codeSelected){
        itemNo++;
        var row = {
            article_id      : codeSelected,
            item_no         : itemNo,
            article         : articleSelected
        }

        tableSelected.row.add([
            row["item_no"],
            '<td>' + row["article"] + ' Cod. ' + row["article_id"] + '</td>',
            '<td><input id="a_quantity_' + row["article_id"] + '" type="text" class="form-control form-control-sm" placeholder="Cant." onblur="calculate(' + row["article_id"] + ',0);"></td>',
            '<td><input id="a_net_' + row["article_id"] + '" type="text" class="form-control form-control-sm" placeholder="$ 0,00" onblur="calculate(' + row["article_id"] + ',0);"></td>',
            '<td><input id="a_bonus_p_' + row["article_id"] + '" type="text" class="form-control form-control-sm" placeholder="% Desc." onblur="calculate(' + row["article_id"] + ',1);"></td>',
            '<td><input id="a_bonus_' + row["article_id"] + '" type="text" class="form-control form-control-sm" placeholder="$ 0,00" onblur="calculate(' + row["article_id"] + ',0);"></td>',
            '<td><select class="selectpicker show-tick form-control form-control-sm" id="a_tax_p_' + row["article_id"] + '" data-live-search="true"  title="Sel" data-width="100%" data-style="btn-info" onchange="calculate(' + row["article_id"] + ',0);">@foreach($taxes as $tax)<option value="{{$tax->value}}" @if(isset($tax) && $tax->name == "21%") selected @endif>{{$tax->name}}</option>@endforeach</select></td>',
            '<td><input id="a_tax_' + row["article_id"] + '" type="text" class="form-control form-control-sm" placeholder="$ IVA" readonly></td>',
            '<td><input id="a_subtotal_' + row["article_id"] + '" type="text" class="form-control form-control-sm" placeholder="$ Subt." readonly></td>',
            '<td><div style="display:inline;"><button type="button"class="btn btn-sm btn-danger" onclick="deleteRow('+row["item_no"]+')"><i class="fas fa-trash"></i></button></div></td>'
        ]).draw( false );

        $('#a_tax_p_' + row["article_id"]).selectpicker();
        a_quantity[row["article_id"]] = integer_format('#a_quantity_' + row["article_id"]);
        a_net[row["article_id"]] = currency_format('#a_net_' + row["article_id"]);
        a_bonus_p[row["article_id"]] = percentage_format('#a_bonus_p_' + row["article_id"]);
        a_bonus[row["article_id"]] = currency_format('#a_bonus_' + row["article_id"]);
        a_tax[row["article_id"]] = currency_format('#a_tax_' + row["article_id"]);
        a_subtotal[row["article_id"]] = currency_format('#a_subtotal_' + row["article_id"]);
        enterTab();
    }

    //Eliminar productos de la tabla de seleccion del modal

    function deleteRow(number) {
        let rows = $('#tableArticlesSelect tbody tr');
        let itemN = 0;
        let rowN = 0;
        let rowId = "";
        let rowIdArr = [];

        rows.each(function (r, k) {

            rowN = $(k).find('td:eq(0)').html();
            rowId = $(k).find('td:eq(2) input').attr("id");

            if(rowN == number && rowId !== ""){
                tableSelected.row($(k)).remove().draw();
                itemNo--;
                rowIdArr = rowId.split('_')
                if(rowIdArr && rowIdArr.length == 3){
                    $('#row' + rowIdArr[2]).removeClass('bg-warning');
                    delete a_quantity[rowIdArr[2]];
                }
            }
            else{
                itemN = reCreateListSelect(k,itemN);
            }
        });
    }

    //Eliminar productos de la tabla final

    function deleteRowEnd(number) {
        let rows = $('#tableArticlesEnd tbody tr');
        let itemN = 0;
        let rowN = 0;
        let rowId = "";
        let rowIdArr = [];

        rows.each(function (r, k) {

            rowN = $(k).find('td:eq(0)').html();
            rowId = $(k).find('td:eq(7) div').attr("id");

            if(rowN == number){
                tableArticlesEnd.row($(k)).remove().draw();

                if(rowId !== ""){
                    rowIdArr = rowId.split('_')
                    if(rowIdArr && rowIdArr.length == 4){
                        setTotals("del",rowIdArr[3]);
                        $('#row' + rowIdArr[3]).removeClass('bg-warning');
                    }
                }
            }
            else{
                itemN ++;
                $(k).find('td:eq(0)').html(itemN);
                let linkButton = $(k).find('td:eq(7) button');
                linkButton.remove();
                let button = $('<button>');
                let icon  = $('<i>');
                button.addClass('btn btn-sm btn-danger');
                icon.addClass('fas fa-trash');
                button.attr({'onClick': 'deleteRowEnd('+itemN+');', 'type': 'button'});
                button.append(icon);
                $(k).find('td:eq(7)').append(button);
            }
        });
    }

    // Agregar articulos a la tabla final para enviar lista

    function addListEnd(){
        let rows = $('#tableArticlesSelect tbody tr');
        let rowLength = rows.length;
        let itemN = 0;
        let rowEndN = 0;
        let rowN = "";
        let rowId = "";
        let rowIdArr = [];

        //Formato de moneda argentina
        var formatter = new Intl.NumberFormat(['ban', 'id'] , { style: 'currency',
        currency: 'ARS',
        currencyDisplay: 'narrowSymbol',
        currencySign: 'accounting',});

        if ($('#tableArticlesEnd tbody tr')[0].innerText == "Ningún dato disponible"){
            rowEndN = $('#tableArticlesEnd tbody tr').length-1;
        }else{
            rowEndN = $('#tableArticlesEnd tbody tr').length;
        }

        rows.each(function (r, k) {
            rowN = $(k).find('td:eq(0)').html(); // Para eliminar la fila copiada.
            rowId = $(k).find('td:eq(2) input').attr("id");

            if(rowId && rowN){
                if(rowN == '1'){
                    rowIdArr = rowId.split('_');
                    if(rowIdArr && rowIdArr.length == 3){

                        if(canAddArticle(rowIdArr[2])){
                            rowEndN++;

                            let row = {
                                article_id      : rowIdArr[2],
                                item_no         : rowEndN,
                                quantity        : parseInt(a_quantity[rowIdArr[2]].rawValue),
                                article         : $(k).find('td:eq(1)').text().split(' Cod. ')[0],
                                net             : parseFloat(a_net[rowIdArr[2]].rawValue),
                                bonus_percentage: (a_bonus_p[rowIdArr[2]].rawValue != "") ? parseFloat(a_bonus_p[rowIdArr[2]].rawValue) : 0,
                                bonus           : (a_bonus[rowIdArr[2]].rawValue != "") ? parseFloat(a_bonus[rowIdArr[2]].rawValue) : 0,
                                tax_percentage  : $("#a_tax_p_" + [rowIdArr[2]]).val(),
                                tax             : parseFloat(a_tax[rowIdArr[2]].rawValue),
                                subtotal        : parseFloat(a_subtotal[rowIdArr[2]].rawValue),
                                tax_id          : taxesID[$("#a_tax_p_" + [rowIdArr[2]]).val()]
                            }

                            articlesJSON.push(row);

                            setTotals("add",rowIdArr[2]);

                            tableArticlesEnd.row.add([
                                row["item_no"],
                                row["article"],
                                row["quantity"] + ',00',
                                row["net"].toFixed(2),
                                '<td>' + a_bonus[rowIdArr[2]].getNumber() + '</td>',
                                row["tax"].toFixed(2) + ' $',
                                row["subtotal"].toFixed(2) + ' $',
                                '<td><div  id="delete_id_end_' + rowIdArr[2] + '" style="display:inline;"><button type="button"class="btn btn-sm btn-danger" onclick="deleteRowEnd('+row["item_no"]+')"><i class="fas fa-trash"></i></button></div></td>'
                            ]).draw( false );

                        }else{
                            $.notify(
                                {
                                    // options
                                    icon: 'fas fa-exclamation-circle',
                                    message: "El artículo, cantidad precio y alícuota son obligatorios."
                                },{
                                    // settings
                                    element:  $('#addArticleList'),
                                    position: 'fixed',
                                    type: "warning",
                                    showProgressbar: false,
                                    mouse_over: 'pause',
                                    animate: {
                                        enter: 'animated bounceIn',
                                        exit: 'animated bounceOut'
                                    }
                                }
                            );
                            return false;
                        }
                    }
                    tableSelected.row($(k)).remove().draw();
                    rowLength--;
                    itemNo--;
                }else{
                    itemN = reCreateListSelect(k,itemN);
                }
            }
        });

        if(itemN == rowLength && rowLength > 0){
            addListEnd();
        }

        if(tableSelected.row().length == 0){
            $('#addArticleList').modal('hide');
        }
    }

    //Funcion para verificar que los datos del articulo esten completos y se pueda agregar a la lista final

    function canAddArticle(idArticle){
        if(a_quantity[idArticle] && a_quantity[idArticle].rawValue > 0 && a_subtotal[idArticle] && a_subtotal[idArticle].rawValue > 0 && $('#a_tax_p_' + [idArticle]) && $('#a_tax_p_' + [idArticle]).val() !== ""){
            return true;
        }else{
            return false;
        }
    }

    //Funcion para rearmar la lista de tabla seleccion al eliminar un articulo

    function reCreateListSelect(k,itemN){
        itemN ++;
        $(k).find('td:eq(0)').html(itemN);
        const linkButton = $(k).find('td:eq(9) button');
        linkButton.remove();
        const button = $('<button>');
        const icon  = $('<i>');
        button.addClass('btn btn-sm btn-danger');
        icon.addClass('fas fa-trash');
        button.attr('onClick', 'deleteRow('+itemN+');');
        button.append(icon);
        $(k).find('td:eq(9)').append(button);
        return itemN;
    }

    function newStore(){
        $('#newStoreModal').modal('show');
    }

    function storeNewStore(){
        if ($("#new_store_name").val() != "") {
                var _token = $('input[name="_token"]').val();
                var data = {
                    'name' : $("#new_store_name").val(),
                    _token : _token
                };

                $.ajax({
                    type: "POST",
                    url: "{{route('stores.fastStore')}}",
                    data: data,
                    dataType: "json",
                    success: function (response) {
                        $('select[name="store_id"]').append(
                            $("<option></option>")
                                .attr("value", response["id"])
                                .text(response["name"])
                        ).selectpicker('refresh');
                        $('select[name="store_id"]').selectpicker('val', response["id"]);
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        $.notify(
                            {
                                // options
                                icon: 'fas fa-exclamation-circle',
                                message: "Se ha producido un error"
                            },{
                                // settings
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
                $("#new_store_name").val("");
                $("#newStoreModal").modal('hide');
        } else {
            //Show error
        }
    }

    function confirm() {
        $('#confirmModal').modal('show');
    }

    //Apertura de modal para agregar articulos
    function openArticleList() {
        $('#addArticleList').modal('show');
    }

    $('#addArticleModal').attr('disabled', false);

    //Desplazarse por imputs de lista de articulos seleccionados

    function enterTab(){
        $('#tableArticlesSelect tbody tr input.form-control-sm[onblur]').bind('keypress', function(event) {
            if (event.keyCode == '13') {
                event.preventDefault();
                let list = $("input.form-control-sm[onblur]");
                list.eq(list.index(this)+1).focus().select();
            }
        });
    }

    //Mostrar tabla para agregar nuevo articulo

    function addNewArticle(){
        $('#tableArticlesList_wrapper').hide();
        $('#btnAddNewArticle').hide();
        $('#tableArticlesSelect_wrapper').parent().hide();
        $('#footerAddArticle').hide();
        $('#addArticlesNew').show();
    }

    //Ocultar tabla sin agregar articulo

    function cancelNewArticle(){
        $('#tableArticlesList_wrapper').show();
        $('#btnAddNewArticle').show();
        $('#tableArticlesSelect_wrapper').parent().show();
        $('#footerAddArticle').show();
        $('#addArticlesNew tbody tr td input')[0].value = ""
        $('#addArticlesNew tbody tr td input')[1].value = ""
        $('#addArticlesNew').hide();
    }

    function currency_format($id_currency){
        return new AutoNumeric($id_currency, {
            currencySymbol: "$",
            decimalCharacter: ",",
            decimalCharacterAlternative: ".",
            digitGroupSeparator: "."
        });
    }

    function percentage_format($id_percentage){
        return new AutoNumeric($id_percentage, {
            suffixText: "%",
            symbolWhenUnfocused: ""
        });
    }

    function integer_format($id_integer){
        return new AutoNumeric($id_integer, {
            allowDecimalPadding: false,
        })
    }
</script>
