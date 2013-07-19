var _holidays = null;
var _baseServUri = _baseUri + "services/";
var _urlMPiFrame = "";

function showMessage(messageType, message){
    if(jQuery("#div_message")){
        jQuery("#p_message").text(message);
        switch(messageType){
            case 'error':
                jQuery("#div_message").removeClass("alert-info alert-success").addClass("alert-error");
                break;
            case 'info':
                jQuery("#div_message").removeClass("alert-error alert-success").addClass("alert-info");
                break;
            case 'success':
                jQuery("#div_message").removeClass("alert-error alert-info").addClass("alert-success");
                break;
        }
        jQuery("#p_message").val(message);
        jQuery("#div_message").show();
        jQuery('html, body').animate({
            scrollTop: jQuery("#pageWrapper").offset().top
        }, 500);
    }
    else{
        alert(message);
    }
}

function showPaymentInfo(){
        var reportType = jQuery("input[name='reportType']:checked").val();
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
                jQuery('#txt_monto').val((total).toFixed(2));
                jQuery("#txt_monto").prop("readonly",true);
                jQuery('#txt_desc_pago').val(periodos.substring(0, periodos.length -2));
                jQuery('#charges_ids').val(charges_ids.substring(0, charges_ids.length -1));
                //jQuery('#paymentFormMessage').hide();
                jQuery("#payHidden").click();
                //jQuery("#paymentInfo").attr("class", "fullscreen alpha60");
            }
        }
        else
            showMessage("error", "Para informar pagos debes seleccionar Cargos Pendientes.");    
}

function requestCashPayment(){
    var reportType = jQuery("input[name='reportType']:checked").val();
        if(reportType == "pendientes"){
            var ids;
            ids = jQuery("#list2").jqGrid('getGridParam','selarrrow');
            if(ids == "")            
                showMessage("error", "No hay cargos seleccionados.");
            else{
                bootbox.confirm("Miembros del staff pasará por tu comercio para efectuar el cobro. Te anticiparemos la fecha de la visita por email.", 
                    function(result) {
                        if(result){
                            var ret, total = 0;
                            var periodos = "";
                            var charges_ids = "";
                            for (var i = 0; i < ids.length; i++) {
                                ret = jQuery("#list2").jqGrid('getRowData',ids[i]);
                                total += parseFloat(ret.total);
                                periodos += ret.periodo + ",";
                                charges_ids += ret.charge_id + ",";;
                            }
                
                            var json_data = {"data":[{
                            'charges_ids':charges_ids.substring(0, charges_ids.length-1), 
                            'periodos':periodos.substring(0, periodos.length-1),
                            'total':total
                            }]};
                        
                        $.ajax({
                            url: _baseServUri + 'requestcash',
                            dataType: 'jsonp',
                            data: {"data":json_data},
                            jsonp: 'jsoncallback',
                            async: false,
                            contentType: "application/json; charset=utf-8",
                            timeout: 5000,
                            success: function(data, status){
                                if(data.result_code != 0){
                                    showMessage('error', data.result_message);    
                                }
                                else{
                                    getPayments();    
                                }
                            },
                            error: function(jqXHR, textStatus, errorThrown){
                                showAjaxError(textStatus, jqXHR.responseText);   
                            }
                        });    
                        }
                });
            }
        }     
}

function showAjaxError(_err, _mess){
    var message = '';
    if(_mess != ''){
        //result = JSON.parse(_mess);
        //message = '('+result.result_code+') '+result.result_message;
    }
    switch(_err){
        case 'parsererror':
            if(message=='')message='No se pudo leer la respuesta del sistema. Intentalo nuevamente más tarde.';
            break;
        case 'error':
            if(message=='')message='Hubo un error en la comunicación con el sistema. Intentalo nuevamente más tarde.';
            break;
        case 'timeout':
            if(message=='')message='El sistema no contestó a tiempo. Intentalo nuevamente más tarde.';
            break;
    }
    showMessage('error', message);
}

function getAccessToken(preference){
    $.ajax({
        url: _baseServUri + 'getmpinitpoint',
        dataType: 'jsonp',
        data: {data:preference},
        jsonp: 'jsoncallback',
        contentType: "application/json; charset=utf-8",
        timeout: 5000,
        success: function(data, status){
                if (typeof(data) == "string")
                    ajaxResponse = $.parseJSON(data);
                else
                    ajaxResponse = data;
                if(ajaxResponse.status == "OK"){
                    _iframe_MP_url = ajaxResponse.body;
                    jQuery("#iframe_MP").attr("src", _iframe_MP_url);
                    jQuery("#payHiddenMP").click();
                    //jQuery("#div_MP").show();
                }   
                else{
                    showMessage("error", "No nos podemos conectar con Mercado Pago, intentalo nuevamente en algunos minutos");
                    return "";
                }
        },
        error: function(jqXHR, textStatus, errorThrown){
            alert(textStatus);
            showMessage("error", "No nos podemos conectar con Mercado Pago, intentalo nuevamente en algunos minutos");    
        }
    });
}

function showMercadoPago(){
        
        var reportType = jQuery("input[name='reportType']:checked").val();
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
                json_MP.items[0].id = "Código",
                json_MP.items[0].unit_price = (total).toFixed(2);
                json_MP.items[0].title = "Promos al Paso. Periodo/s a pagar: " + periodos.substring(0, periodos.length -2);
                json_MP.items[0].description = "Periodo/s a pagar: " + periodos.substring(0, periodos.length -2);
                json_MP.items[0].quantity = 1;
                json_MP.items[0].currency = "ARS"; 
                json_MP.items[0].picture_url = 'http://promosalpaso.com/logo-promosalpaso.png';
                json_MP.external_reference = charges_ids.substring(0, charges_ids.length -1);
                json_MP.payer.name = "Rolando";
                json_MP.payer.surname = "Daumas";
                json_MP.payer.email = "redaumore@gmail.com";
                json_MP.back_urls.success = _baseUri + "payment/success";
                json_MP.back_urls.failure = _baseUri + "payment/failure";
                json_MP.back_urls.pending = _baseUri + "payment/pending";
                getAccessToken(json_MP);
            }
        }
        else
            showMessage("error", "Para informar pagos debes seleccionar Cargos Pendientes.");    
}


function showPromoTotalCost(){
    if(jQuery("#starts").val() == "" ||jQuery("#ends").val() == ""){
        jQuery("#totalPromoCost").text("0");
        return;    
    }
        
    from = jQuery("#starts").val().split("-");
    f = new Date(from[2], from[1] - 1, from[0]);
    to = jQuery("#ends").val().split("-");
    t = new Date(to[2], to[1] - 1, to[0]);
    var days = getWorkingDays(f,t);
    
    var cost = parseFloat(jQuery("#promoCost :selected").text());
    
    if(days > 0){
          total = days * cost;
          jQuery("#totalPromoCost").text(total);
    }
    else
        jQuery("#totalPromoCost").text("0");    
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
    var reportType = jQuery("input[name='reportType']:checked").val();
    var jsondata = jQuery.parseJSON(jQuery('input#data').val());
    jQuery("#list2").jqGrid({
        datatype: 'local',
        //data: jQuery.parseJSON(jQuery('input#data').val()),
        data: jsondata.payments,
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
            jQuery("#" + subgridId).html("<table id='" + subgridTableId + "'></table>");
            jQuery("#" + subgridTableId).jqGrid({
                datatype: "local",
                data: jQuery(this).jqGrid("getLocalRow", rowid).costos,
                colNames: ["Cantidad", "Costo Promo", "Días Anunciados", "Subtotal"],
                colModel: [
                  {name: "promo_count", align:"center", width: 125},
                  {name: "cost", width: 125, align:"center", key: true, formatter: "currency", formatoptions:{decimalSeparator:",", thousandsSeparator: ".", decimalPlaces: 2, prefix: "$ "}},
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
                'operacion':jQuery('#ddl_operacion').val(),
                'banco_origen':jQuery('#ddl_banco_orig').val(),
                'otro_banco':jQuery('#txt_otro_banco').val(),
                'banco_destino':jQuery('#ddl_banco_dest').val(),
                'nro_tx':jQuery('#txt_nro_tx').val(),
                'monto':jQuery('#txt_monto').val(),
                'fecha':jQuery('#txt_fecha').val(),    
                'charges_ids':jQuery('#charges_ids').val()
            }]};
            
            $.ajax({
                url: _baseServUri + 'sendpayment',
                dataType: 'jsonp',
                data: {"data":json_data},
                jsonp: 'jsoncallback',
                contentType: "application/json; charset=utf-8",
                timeout: 5000,
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
            alert("No has completado correctamente todos los campos requeridos. Por favor inténtalo nuevamente.");
        }
    }
    
function LoadComplete(){
    if (jQuery('#list2').getGridParam('reccount') == 0) // are there any records?
        DisplayEmptyText(true);
    else
        DisplayEmptyText(false);
}

function DisplayEmptyText( display){
    var grid = jQuery('#list2');
    jQuery("#pay").hide();
    var emptyText = grid.getGridParam('emptyDataText'); // get the empty text
    var container = grid.parents('.ui-jqgrid-view'); // find the grid's container
    if (display) {
        jQuery("#gbox_list2").hide();
        container.find('.ui-jqgrid-hdiv, .ui-jqgrid-bdiv').hide(); // hide the column headers and the cells below
        //container.find('.ui-jqgrid-titlebar').after('' + emptyText + ''); // insert the empty data text
        showMessage("info", "No hay registros para mostrar.");
        
    }
    else {
        container.find('loading').hide();
        container.find('.ui-jqgrid-hdiv, .ui-jqgrid-bdiv').show(); // show the column headers
        //container.find('#EmptyData' + dataObject).remove(); // remove the empty data text
        var reportType = jQuery("input[name='reportType']:checked").val();
        if(reportType == "pendientes"){
            var jsondata = jQuery.parseJSON(jQuery('input#data').val());
            var paymentMethods = jsondata.payment_methods;
            for (i = 0; paymentMethods.length > i; i += 1) {
                if (paymentMethods[i].payment_method_id == 'T' || paymentMethods[i].payment_method_id == 'D' )
                    jQuery("#pay").show();
                if (paymentMethods[i].payment_method_id == 'MP')
                    jQuery("#payMP").show();
                if (paymentMethods[i].payment_method_id == 'E')
                    jQuery("#payCash").show();
            }
        }
    }
}

function getDaysTo(promo_ends_date){
    var _MS_PER_DAY = 1000 * 60 * 60 * 24;
    today = new Date();
    utc1 = new Date(today.getFullYear(), today.getMonth(), today.getDate());
    promo_ends_date = promo_ends_date.split(" ");
    from = promo_ends_date[0].split("-"); 
    utc2 = new Date(from[0], from[1] - 1, from[2]);
    _return = Math.floor((utc2 - utc1) / _MS_PER_DAY);
    return _return;
}

function callFillPromotion(data){
    var promotion =jQuery.parseJSON(data.responseText);
    FillPromotion(promotion);    
}

function FillPromotion(promotion){
    
    if(promotion == null || promotion.length == 0 ){
        showMessage("error", "No se ha podido cargar la promo. Intenta en otro momento.");
        return;
    }
    var time = new Date();
    if(promotion.length == 1)
        promotion = promotion[0];  
    jQuery('#det-name').text(promotion.name);
    jQuery('#det-direccion').text(promotion.street+' '+promotion.number+' - '+promotion.city_name);
    jQuery('#det-img-comercio').attr("src", promotion.logo.replace(/\\/g, '/')+"?"+time.getTime());
    jQuery('#det-long_description').text(promotion.long_description);
    jQuery('#det-displayed_text').text(promotion.displayed_text);
    if(promotion.path != null)
        jQuery('#det-img-promo').attr("src", promotion.path);
        //jQuery('#det-img-promo').attr("src", '/images'+promotion.path.replace(/\\/g, '/')+"?"+time.getTime());
    else
        jQuery('#det-img-promo').attr("src", promotion.logo);
    //    jQuery('#det-img-promo').attr("src", '/images'+promotion.logo.replace(/\\/g, '/')+"?"+time.getTime());
    jQuery('#det-short_description').text(promotion.short_description);
    if(promotion.value_since)
        jQuery('#det-short_description').show
    else
        jQuery('#det-short_description').hide;
    jQuery('#det-promo_value').html(formatPrice(promotion.promo_value));
    
    if(promotion.value_since == "1")
        jQuery('#precio_desde').html("desde");
    else
        jQuery('#precio_desde').html("&nbsp;");
    
    if(promotion.is_percentage == "1"){
        jQuery("#det-promo_value").removeClass("precio");
        jQuery("#det-promo_value").addClass("porcentaje");
    }
    else{
        jQuery("#det-promo_value").removeClass("porcentaje");
        jQuery("#det-promo_value").addClass("precio");
    }
    
    if(promotion.alert_type == 'N')
        jQuery('#det-alarma').hide();
    if(promotion.alert_type == 'D'){
        jQuery('#det-alarm_num').text(getDaysTo(promotion.ends));
        jQuery('#det-alarm_type').text('días');
        jQuery('#det-alarma').show();
    }
    if(promotion.alert_type == 'Q'){
        jQuery('#det-alarm_num').text(promotion.quantity);
        jQuery('#det-alarm_type').text('unids.');
        jQuery('#det-alarma').show();
    }
    jQuery("#hidden_latitude").val(promotion.latitude);
    jQuery("#hidden_longitude").val(promotion.longitude);
    jQuery("#btnpromobody").click();
    //alert(data.responseText);    
}

function collectPromotionFormData(){
    var userInfo =jQuery.parseJSON(jQuery("#userInfo").val());
    var data = {"promotion":[
    {
        "name": userInfo.user_name,    
        "street": userInfo.branch_street,
        "number": userInfo.branch_number,
        "city_name": userInfo.branch_city_name,
        "logo": userInfo.branch_logo,
        "long_description": jQuery("#longDescription").val(),
        "displayed_text": jQuery("#displayedText").val(),
        "path": jQuery("#imagePromo").attr("src"),  //userInfo.promo_image,
        "short_description": jQuery("#shortDescription").val(),
        "promo_value": jQuery("#promoValue").val(),
        "value_since": jQuery("#valueSince").is(':checked'),
        "is_percentage": jQuery("#valueType").is(':checked'),
        "alert_type": jQuery("#alertType").val(),
        "latitude": jQuery("#hidden_latitude").val(),    
        "longitude": jQuery("#hidden_longitude").val(),
        "quantity": jQuery("#quantity").val(),                                            
    }
    ]};
    return data;
}

function preregister(){
    var name = jQuery("#name").val();
    var email = jQuery("#email").val();
    if(_baseServUri == "")
        _baseServUri = decodeBase64("aHR0cDovL2Rldi5wcm9tb3NhbHBhc28uY29tL3NlcnZpY2VzLw==");
    if(name == "" || email == "")
        alert("El nombre y el email son requeridos");
    else{
        $.ajax({
                url: _baseServUri + 'preregister',
                dataType: 'jsonp',
                data: {"name":name, 
                       "email":email,
                },
                jsonp: 'jsoncallback',
                contentType: "application/json; charset=utf-8",
                timeout: 5000,
                success: function(data, status){
                    alert("Gracias por contactarte. Te avisaremos cuando salgamos al aire!!");
                },
                error: function(jqXHR, textStatus, errorThrown){
                    alert("Hubo un error guardando la información. Intenta dentro de algunos minutos.");    
                }
            });
    }
        
}

function getToday(){
    var now = new Date();
    var today = new Date(now.getFullYear(), now.getMonth(), now.getDay());
    return today;
}

function resetPassword(){
    bootbox.prompt("Ingresa tu email para enviarte la contraseña.", 
        function(result) {
            if(result === null || result == ''){
                showMessage('info', 'Es necesario ingresar tu email para generar una nueva contraseña.');
                return;    
            }
            var email = result;
           
            $.ajax({
                url: _baseServUri + 'resetpassword',
                dataType: 'jsonp',
                data: {"email":email},
                jsonp: 'jsoncallback',
                async: false,
                contentType: "application/json; charset=utf-8",
                timeout: 5000,
                success: function(data, status){
                    if(data.code != 0){
                        showMessage('error', data.message);    
                    }
                    else{
                        showMessage('info', 'Tu nueva contraseña se ha enviado a tu casilla de email.');    
                    }
                },
                error: function(jqXHR, textStatus, errorThrown){
                    showAjaxError(textStatus, jqXHR.responseText);   
                }
            });    
        }
    );    
}








