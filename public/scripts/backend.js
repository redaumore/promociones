var _holidays = null;



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
        colNames: ["", "", "Periodo", "Desde", "Hasta", "Total"],
        colModel: [
            {name:'',index:'', width:15, align:"center", hidden: (reportType=="pendientes")?false:true,edittype:'checkbox',formatter: "checkbox",editoptions: { value:"True:False"},editable:true,formatoptions: {disabled : false}},
            {name: "charge_id", width:0, hidden:true, key: true},
            {name: "periodo", width: 65, align:"center"},
            {name: "desde", width: 150, align:"center", formatter: 'date', formatoptions: {srcformat:"Y-m-d H:i A", newformat: 'd/m/Y' }, editable: false, datefmt: 'd-m-Y', },
            {name: "hasta", width: 150, align:"center", formatter: 'date', formatoptions: {srcformat:"Y-m-d H:i A", newformat: 'd/m/Y' }, editable: false, datefmt: 'd-m-Y',},
            {name: "total", width: 150, align:"right", formatter: "currency", formatoptions:{decimalSeparator:",", thousandsSeparator: ".", decimalPlaces: 2, prefix: "$ "}}
        ],
        rowNum: 10,
        rowList: [5, 10, 20],
        pager: "#pager",
        gridview: true,
        ignoreCase: true,
        rownumbers: true,
        sortname: "sequence",
        viewrecords: true,
        height: "100%",
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

