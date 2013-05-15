var _holidays = null;
var _baseServUri = _baseUri + "services/";

function showMessage(messageType, message){
    if($("#div_alert")){
        $("#p_message").text(message);
        $("#div_alert").show();
    }
    else{
        alert(message);
    }
}

function closeMessage(){
   $("#div_alert").hide(); 
}

function showPaymentInfo(){
        var reportType = $("input[name='reportType']:checked").val();
        if(reportType == "pendientes"){
            var ids;
            ids = jQuery("#list2").jqGrid('getGridParam','selarrrow');
            if(ids == "")            
                showMessage("error", "No hay cargos seleccionados.");
            else{
                var ret, total = 0;
                var periodos = "";
                var charges_ids = "";
                for (var i = 0; i < ids.length; i++) {
                    ret = jQuery("#list2").jqGrid('getRowData',ids[i]);
                    total += parseFloat(ret.total);
                    periodos += ret.periodo + ", ";
                    charges_ids += ret.charge_id + ",";;
                }
                $('#txt_monto').val((total).toFixed(2));
                $("#txt_monto").prop("readonly",true);
                $('#txt_desc_pago').val(periodos.substring(0, periodos.length -2));
                $('#charges_ids').val(charges_ids.substring(0, charges_ids.length -1));
                $('#paymentFormMessage').hide();
                document.getElementById("paymentInfo").className = "fullscreen alpha60";
            }
        }
        else
            showMessage("error", "Para informar pagos debes seleccionar Cargos Pendientes.");    
}


function showPromoTotalCost(){
    if($("#starts").val() == "" ||$("#ends").val() == ""){
        $("#totalPromoCost").text("0");
        return;    
    }
        
    from = $("#starts").val().split("-");
    f = new Date(from[2], from[1] - 1, from[0]);
    to = $("#ends").val().split("-");
    t = new Date(to[2], to[1] - 1, to[0]);
    var days = getWorkingDays(f,t);
    
    var cost = parseFloat($("#promoCost :selected").text());
    
    if(days > 0){
          total = days * cost;
          $("#totalPromoCost").text(total);
    }
    else
        $("#totalPromoCost").text("0");    
}

function getWorkingDays(startDate, endDate){
     var result = 0;

    var currentDate = startDate;
    while (currentDate <= endDate)  {  
        var weekDay = currentDate.getDay();
        if(weekDay != 0 && weekDay != 6)
            result++;
        currentDate.setDate(currentDate.getDate()+1); 
    }
    return result;
}

function getPayments(){
    var reportType = $("input[name='reportType']:checked").val();
    jQuery("#list2").jqGrid({
        datatype: 'local',
        data: jQuery.parseJSON($('input#data').val()),
        colNames: ["", "Periodo", "Desde", "Hasta", "Total"],
        colModel: [
            //{name:'',index:'', width:15, align:"center", hidden: (reportType=="pendientes")?false:true,edittype:'checkbox',formatter: "checkbox",editoptions: { value:"True:False"},editable:true,formatoptions: {disabled : false}},
            {name: "charge_id", width:0, hidden:true, key: true},
            {name: "periodo", width: 65, align:"center"},
            {name: "desde", width: 150, align:"center", formatter: 'date', formatoptions: {srcformat:"Y-m-d H:i A", newformat: 'd/m/Y' }, editable: false, datefmt: 'd-m-Y'},
            {name: "hasta", width: 150, align:"center", formatter: 'date', formatoptions: {srcformat:"Y-m-d H:i A", newformat: 'd/m/Y' }, editable: false, datefmt: 'd-m-Y'},
            {name: "total", width: 150, align:"right", formatter: "currency", formatoptions:{decimalSeparator:",", thousandsSeparator: ".", decimalPlaces: 2, prefix: "$ "}}
        ],
        rowNum: 10,
        rowList: [5, 10, 20],
        pager: "#pager",
        gridview: true,
        ignoreCase: true,
        rownumbers: false,
        sortname: "sequence",
        viewrecords: true,
        multiselect: (reportType=="pendientes")?true:false,
        height: "100%",
        gridComplete: function(){LoadComplete();},
        emptyDataText: "No hay registros",
        subGrid: true,
        subGridRowExpanded: function (subgridId, rowid) {
            var subgridTableId = subgridId + "_t";
            $("#" + subgridId).html("<table id='" + subgridTableId + "'></table>");
            $("#" + subgridTableId).jqGrid({
                datatype: "local",
                data: $(this).jqGrid("getLocalRow", rowid).costos,
                colNames: ["Costo Promo", "Cantidad", "Días Anunciados", "Subtotal"],
                colModel: [
                  {name: "cost", width: 125, align:"center", key: true, formatter: "currency", formatoptions:{decimalSeparator:",", thousandsSeparator: ".", decimalPlaces: 2, prefix: "$ "}},
                  {name: "promo_count", align:"center", width: 125},
                  {name: "cant_dias", align:"center", width: 125},
                  {name: "subtotal", align:"right", width: 125, formatter: "currency", formatoptions:{decimalSeparator:",", thousandsSeparator: ".", decimalPlaces: 2, prefix: "$ "}}
                ],
                height: "100%",
                rowNum: 10,
                sortname: "name",
                idPrefix: "s_" + rowid + "_"
            });
        } 
    });
//jQuery("#list2").jqGrid("navGrid", "#pager", {add: false, edit: false, del: false});
}

function sendPayment(){
        if(validatePaymentForm()){
            var json_data = {"data":[{
                'operacion':$('#ddl_operacion').val(),
                'banco_origen':$('#ddl_banco_orig').val(),
                'otro_banco':$('#txt_otro_banco').val(),
                'banco_destino':$('#ddl_banco_dest').val(),
                'nro_tx':$('#txt_nro_tx').val(),
                'monto':$('#txt_monto').val(),
                'fecha':$('#txt_fecha').val(),    
                'charges_ids':$('#charges_ids').val(),
            }]};
            
            $.ajax({
                url: _baseServUri + 'sendpayment',
                dataType: 'jsonp',
                data: {"data":json_data},
                jsonp: 'jsoncallback',
                contentType: "application/json; charset=utf-8",
                timeout: 5000,
                beforeSend: function (jqXHR, settings) {
                    url = settings.url + "?" + settings.data;
                },
                success: function(data, status){
                        if(data.length == 0)
                            alert(data);
                },
                error: function(jqXHR, textStatus, errorThrown){
                    alert(textStatus);    
                }
            });
        }
        else{
            $('#paymentFormMessage').show()
        }
    }
    
function LoadComplete()
{
    if ($('#list2').getGridParam('reccount') == 0) // are there any records?
        DisplayEmptyText(true);
    else
        DisplayEmptyText(false);
}

function DisplayEmptyText( display)
{
    var grid = $('#list2');
    var emptyText = grid.getGridParam('emptyDataText'); // get the empty text
    var container = grid.parents('.ui-jqgrid-view'); // find the grid's container
    if (display) {
        container.find('.ui-jqgrid-hdiv, .ui-jqgrid-bdiv').hide(); // hide the column headers and the cells below
        container.find('.ui-jqgrid-titlebar').after('' + emptyText + ''); // insert the empty data text
    }
    else {
        container.find('.ui-jqgrid-hdiv, .ui-jqgrid-bdiv').show(); // show the column headers
        container.find('#EmptyData' + dataObject).remove(); // remove the empty data text
    }
}



