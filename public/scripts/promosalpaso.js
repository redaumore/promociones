/*
  Variables guardadas en Session:
    -activePromotion
    -lastSearch
*/

/*RAMOS MEJIA
var _lat = "-34.6463";
var _lng = "-58.5648";
*/


/*SAN JUSTO*/
var _lat = "-34.681774410598"; //"-34.6463";
var _lng = "-58.561710095183" ; //"-58.5648";


/* CASANOVA */
  //var _lat = "-34,707320691758";
  //var _lng = "-58,583339428671";
 
var _promo_lat;
var _promo_lng;
//var _baseServUri = "http://192.168.1.33/services/";
var _baseServUri = _baseUri + "services/";
var _baseAjaxUri = _baseUri + "Backendajax/";
var _activePromo;

$(document).delegate( "#detail", "pagebeforeshow", function(event){
    id_promotion = window.localStorage.getItem("activePromotion");
    callPromoDetail(id_promotion);
});

$(document).delegate( "#page-map", "pageshow", function(event){
    initialize();
    var _width = $(window).width();
    var _height = $(window).height();
    $("#map_canvas").css({height:_height});
    $("#map_canvas").css({width:_width});
    calcRoute();
    //var yourStartLatLng = new google.maps.LatLng(_lat, _lng);
    //$('#map_canvas').gmap({'center': yourStartLatLng});
});

$(document).ready(function(){
    
    $(document).ajaxStart(function () { showProgress() }).ajaxStop(function () { hideProgress() });
    setFullScreen();
    loadPromoList();    
});

function loadPromoList(){
    $.ajax({
        url: _baseServUri + 'getpromolist',
        dataType: 'jsonp',
        data: {"lat": _lat,
               "lng": _lng},
        jsonp: 'jsoncallback',
        contentType: "application/json; charset=utf-8",
        timeout: 5000,
        beforeSend: function (jqXHR, settings) {
            url = settings.url + "?" + settings.data;
        },
        success: function(data, status){
                window.localStorage.setItem("lastSearch", JSON.stringify(data));
                if(data.length == 0)
                    $.mobile.changePage($("#nopromos"));    
                $.each(data, function(i,item){
                    document.getElementById("promolist").innerHTML += getPromoRecord(item);
                });
        },
        error: function(jqXHR, textStatus, errorThrown){
            navigator.notification.alert(
            'Something went wrong...',
            null,
            'Error',
            'Done'
            );
        }
    });
}

function getPromoRecord(promo){
    var liString = getLiString();
    liString = liString.replace("#ID#", promo.promotion_id);
    liString = liString.replace("#IMAGE#", promo.path);
    liString = liString.replace("#COMERCIO#", promo.name);
    liString = liString.replace("#DESCRIPCION#", promo.short_description);        
    liString = liString.replace("#PROMO#", promo.displayed_text);
    liString = liString.replace("#PRECIO#", formatPrice(promo.promo_value));
    liString = liString.replace("#DISTANCIA#", promo.distance);
    return liString;
}

function gotoPromo(id_promotion){
    window.localStorage.setItem("activePromotion", id_promotion);
    $.mobile.changePage($("#detail"));
}

function getPamarByName(url, paramName){ 
    var strGET = url.substr(url.indexOf('?')+1,url.length - url.indexOf('?')); 
    var arrGET = strGET.split("&"); 
    var paramValue = '';
    for(i=0;i<arrGET.length;i++){ 
          var aux = arrGET[i].split("="); 
          if (aux[0] == paramName){
                paramValue = aux[1];
          }
    } 
    return paramValue;
}

function callPromoDetail(promotion_id){
    var promotion_detail;
    $.ajax({
        url: _baseServUri + 'getpromodetail',
        dataType: 'jsonp',
        data: {"lat": _lat,
               "lng": _lng, 
               "promoid": promotion_id},
        jsonp: 'jsoncallback',
        contentType: "application/json; charset=utf-8",
        timeout: 5000,
        beforeSend: function (jqXHR, settings) {
            url = settings.url + "?" + settings.data;
        },
        success: function(data, status){
                loadPromoDetail(data);
        },
        error: function(jqXHR, textStatus, errorThrown){
            debugger;
            navigator.notification.alert(
            'Something went wrong...',
            null,
            'Error',
            'Done'
            );
        }
    });
    return promotion_detail;
}

function loadPromoDetail(item){
    $("#det-name").html(item.name);
    $("#det-long_description").html(item.long_description);
    $("#det-displayed_text").html(item.displayed_text);
    $("#det-short_description").html(item.short_description);
    $("#det-promo_value").html(formatPrice(item.promo_value));
    $("#det-distance").html(item.distance);
    $("#det-direccion").html(item.street + ' ' + item.number + ' - ' + item.city);
    $("#det-img-comercio").attr("src",item.logo);
    $("#det-img-promo").attr("src",item.path);
    if(item.alert_type == "N"){
        $("#det-alarma").hide();
    }
    else{
        if(item.alert_type == "Q"){
            $("#det-alarm_num").html(item.quantity);
            $("#det-alarm_type").html("unids");
        }
        else{
            today=new Date();
            ends = new Date(item.ends);
            var one_day = 1000*60*60*24;
            days = Math.ceil((ends.getTime()-today.getTime())/(one_day));
            $("#det-alarm_num").html(days);
            $("#det-alarm_type").html("días");
        } 
            
    }
    _promo_lat = item.latitude;
    _promo_lng = item.longitude;
}

function saveFavorite(){
    alert("Favorito!!");
}

// Function called when phonegap is ready
function setFullScreen() {
    //All pages at least 100% of viewport height
    var viewPortHeight = $(window).height();
    var headerHeight = $('div[data-role="header"]').height();
    var footerHeight = $('div[data-role="footer"]').height();
    var contentHeight = viewPortHeight - headerHeight - footerHeight;

    // Set all pages with class="page-content" to be at least contentHeight
    $('div[class="ui-content"]').css({'min-height': contentHeight + 'px'});
 }
 
function showProgress() {
    $('body').append('<div id="progress"><img src="/css/images/ajax-loader.gif" alt="" width="16" height="11" /> Loading...</div>');
    $('#progress').center();
}
function hideProgress() {
    $('#progress').remove();
}
jQuery.fn.center = function () {
    this.css("position", "absolute");
    this.css("top", ($(window).height() - this.height()) / 2 + $(window).scrollTop() + "px");
    this.css("left", ($(window).width() - this.width()) / 2 + $(window).scrollLeft() + "px");
    return this;
}

function getLiString(){
var liString = new String();

liString = '<li data-corners="false" data-shadow="false" data-iconshadow="true" data-wrapperels="div" data-icon="arrow-r" data-iconpos="right" data-theme="b" class="ui-btn ui-li-has-arrow ui-li ui-li-has-thumb ui-btn-up-c ui-li-static"  style="padding: 0px;">';
liString += '   <div class="ui-btn-inner ui-li ui-li-static ui-btn-up-b" style="padding: 0px;">';
liString += '       <div class="ui-btn-text registro">';
liString += '           <a href="#" data-transition="flip" onclick="gotoPromo(#ID#);">'; //<a href="#ID#">';
liString += '               <table class="aviso">';
liString += '                  <tr>';
liString += '                     <td class="image" style="width: 50px;">';
liString += '                        <img src="#IMAGE#" class="shadow image">';
liString += '                     </td>';
liString += '                     <td style="border-right: solid 1px #9CAAC6;">';
liString += '                        <p class="comercio ui-li-desc">#COMERCIO#</p>';
liString += '                        <p class="descripcion ui-li-desc">#DESCRIPCION#</p>';
liString += '                        <p class="promo ui-li-desc">#PROMO#</p>';
liString += '                     </td>';
liString += '                     <td style="width: 30px;">';
liString += '                       <table>';
liString += '                          <tr><td style="border-bottom: solid 1px #9CAAC6; text-align: center"><span class="precio">#PRECIO#</span></td></tr>';
liString += '                          <tr><td style="vertical-align: middle; text-align: center"><span class="distancia">#DISTANCIA#</span></td></tr>';
liString += '                       </table>';
liString += '                     </td>';
liString += '                     <td class="arrow-r ui-icon-shadow">&nbsp;</td>';
liString += '                  </tr>';
liString += '               </table>';
liString += '            </a></div></div></li>';

    return liString;
}

function formatPrice(price){
    var formatedPrice = "";
    point = price.indexOf(".00");
    if(point == -1)
        formatedPrice = price.substring(0, price.indexOf(".")) + price.substring(price.indexOf(".")+1, price.length).sup();
    else
        formatedPrice = price.substring(0, price.indexOf("."));
    
    return formatedPrice;
}