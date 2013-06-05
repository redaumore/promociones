jQuery(document).ready(function(){
    if(jQuery.browser.mobile){
        $.mobile.defaultPageTransition = 'none';
        _last_update = window.localStorage.getItem("last_update");    
        if(_last_update == null)
            setLastUpdate(new Date(0));
        console.log("Ultima actualización: "+_last_update);    
        console.log("Actualizando ciudades...");
        getRegionsUpdate();
        navigator.geolocation.getCurrentPosition(onSuccess, 
                onError_highAccuracy, 
                {maximumAge:600000, timeout:5000, enableHighAccuracy: true});
        setLastUpdate(new Date());
    }
});

jQuery(document).bind("mobileinit", function(){
    $.mobile.defaultPageTransition = 'none';
});

jQuery.fn.center = function () {
    this.css("position", "absolute");
    this.css("top", (jQuery(window).height() - this.height()) / 2 + jQuery(window).scrollTop() + "px");
    this.css("left", (jQuery(window).width() - this.width()) / 2 + jQuery(window).scrollLeft() + "px");
    return this;
}

jQuery('#state_select').live("change blur", function() {
    var selectedState = jQuery(this).val();
    addCites(selectedState);
    if (jQuery('#city_select option').size() == 0) {
        jQuery('#city_select').append('<option value="nocity">No se encontraron ciudades</option>');
    }
    event.preventDefault();
});

jQuery('#a_search_button').live("click", function() {
    event.preventDefault();
    doSearch();
});

jQuery(document).delegate( "#page-map", "pagebeforeshow", function(event){
    initialize();
    var _width = jQuery(window).width();
    var _height = jQuery(window).height();
    jQuery("#map_canvas").css({height:_height});
    jQuery("#map_canvas").css({width:_width});
    calcRoute();
});

(function(a){jQuery.browser.mobile=/android.+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|e\-|e\/|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|xda(\-|2|g)|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))})(navigator.userAgent||navigator.vendor||window.opera);

function showMessage(message, title, button){
    $.mobile.showPageLoadingMsg('a', message, true);
    setTimeout( function() { $.mobile.hidePageLoadingMsg(); }, 3000 );
}

function saveFavorite(){
    var located = false;
    var favoritos = window.localStorage.getItem("favoritos");
    var activePromo = window.localStorage.getItem("activePromotion");
    if (favoritos != null){
        var arrFav = favoritos.split(",");
        for(var i = 0; i < arrFav.length; i++){
            if(arrFav[i] == activePromo)
                located = true;
        }
        if(!located)
            favoritos = favoritos + activePromo + ",";
    }
    else{
        favoritos = activePromo + ",";
    }
    window.localStorage.setItem("favoritos", favoritos);
    showMessage("La promo se ha agregado a tus favoritos.", "Info", "Ok");    
}

function deleteFavorite(id){
    var fav = window.localStorage.getItem("favoritos");
    if(fav == null)
        return;
    arrFav = fav.split(",");
    for(var i=0; i<arrFav.length; i++){
        if(arrFav[i] = id)
            arrFav.splice(i, 1);
    }
    if(arrFav.toString()=="")
        window.localStorage.removeItem("favoritos");
    else
        window.localStorage.setItem("favoritos", arrFav.toString());
    showMessage("La promo se ha eliminado de tus favoritos.", "Info", "Ok");
}

function isFavorite(id){
    var fav = window.localStorage.getItem("favoritos");
    if(fav == null)
        return false;
    arrFav = fav.split(",");
    for(var i=0; i<arrFav.length; i++){
        if(arrFav[i] == id)
            return true;
    }
    return false;
}

function gotoFavoritos(){
    var favoritos = window.localStorage.getItem("favoritos");
    if(favoritos != null)
        if(favoritos != ""){
            _inFavorites = true;
            loadPromoListByIds(favoritos.substring(0, favoritos.lastIndexOf(",")));
            return;
        }
    showMessage('No tienes favoritos.', 'Info', 'Ok');        
}

/*CONFIG*/

function getRegionsUpdate(){
    console.log("getRegionsUpdate-last_update: "+_last_update);
    $.ajax({
        url: _baseServUri + 'getregions',
        dataType: 'jsonp',
        data: {"lastupdate": _last_update},
        jsonp: 'jsoncallback',
        contentType: "application/json; charset=utf-8",
        timeout: 5000,
        beforeSend: function (jqXHR, settings) {
            console.log(settings.url);
        },
        success: function(data, status){
                console.log("getRegionUpdate: llamada a servicio exitosa");
                if(data == null){
                    console.log("No se actualizaron regiones");
                    return;
                }
                addRegions(data.province, data.city);    
        },
        error: function(jqXHR, textStatus, errorThrown){
            console.log("Error getRegionUpdate: " + textStatus);
            showMessage('Hubo un error actualizando las ciudades', 'Error', 'Ok');
        }
    });
}    
function addRegions(provinces, cities){
    var db = window.openDatabase("promosalpaso", "1.0", "Promos al Paso", 300000);
    db.transaction(function(tx){populateRegionsDB(tx, provinces, cities)}, errorCB, successCB);
}
function populateRegionsDB(tx, provinces, cities) {
    if(provinces != null ){
         tx.executeSql('DROP TABLE IF EXISTS province');
         tx.executeSql('CREATE TABLE IF NOT EXISTS province (province_id INTEGER PRIMARY KEY, name, updated DATETIME)');
         $.each(provinces, function(i,item){
            console.log("populateRegionsDB: actualizando provincia "+item.name);
            tx.executeSql('INSERT INTO province (province_id, name, updated) VALUES ('+item.province_id+',"'+item.name+'","'+item.updated+'")');
         });
     }
     if(cities != null){
         tx.executeSql('DROP TABLE IF EXISTS city');
         tx.executeSql('CREATE TABLE IF NOT EXISTS city (city_id INTEGER PRIMARY KEY, name, latitude, longitude, province_id INTEGER, updated DATETIME)');
         $.each(cities, function(i,item){
            console.log("populateRegionsDB: actualizando ciudad "+item.name);
            console.log('INSERT INTO city (city_id, name, latitude, longitude, province_id, updated) VALUES ('+item.city_id+',"'+item.name+'","'+item.latitude+'","'+item.longitude+'",'+item.province_id+',"'+item.updated+'")');
            tx.executeSql('INSERT INTO city (city_id, name, latitude, longitude, province_id, updated) VALUES ('+item.city_id+',"'+item.name+'","'+item.latitude+'","'+item.longitude+'",'+item.province_id+',"'+item.updated+'")');
         });
     }
}
function errorCB(err) {
    console.log("errorCB: "+err.message+". Code: "+err.code);
    alert("Error actualizando ciudades: "+err.code);
}
function successCB(){
    window.localStorage.setItem("last_update", _last_update);
}
function gotoSearch(){
    var db = window.openDatabase("promosalpaso", "1.0", "Promos al Paso", 200000);
    db.transaction(populateProvinceDDL, errorProvinceDDL, successProvinceDDL);
    jQuery('#city_button').hide();
    $.mobile.changePage(jQuery("#search"));        
}
function populateProvinceDDL(tx){
    tx.executeSql('SELECT province_id, name FROM province ORDER BY name', [], queryProvinceSuccess, errorCB);
}
function successProvinceDDL(){
    
}
function errorProvinceDDL(err) {
        console.log("errorProvinceDDL: "+err.message+". Code: "+err.code);
    }
function queryProvinceSuccess(tx, results){
    jQuery('#state_select').empty();
    for(i=0;i<results.rows.length;i++){
        jQuery('#state_select').append('<option value="'+results.rows.item(i).province_id+'">' + results.rows.item(i).name + '</option>');
    }
    jQuery("#state_select option:first").attr('selected','selected');
    jQuery('#state_select').selectmenu("refresh");
    addCites(jQuery('#state_select').val());
}
function addCites(province_id) {
    var db = window.openDatabase("promosalpaso", "1.0", "Promos al Paso", 200000);
    db.transaction(function(tx){populateCityDDL(tx, province_id)}, errorCityDDL, successCityDDL);
}
function populateCityDDL(tx, province_id){
    tx.executeSql('SELECT city_id, name FROM city WHERE province_id = '+province_id+' ORDER BY name', [], queryCitySuccess, errorCB);
}
function successCityDDL(){
    
}
function errorCityDDL(err) {
        console.log("Error City SQL: "+err.code);
    }
function queryCitySuccess(tx, results){
    jQuery('#city_select').empty();
    for(i=0;i<results.rows.length;i++){
        jQuery('#city_select').append('<option value="'+results.rows.item(i).city_id+'">' + results.rows.item(i).name + '</option>');
    }
    jQuery("#city_select option:first").attr('selected','selected');     
    jQuery('#city_select').selectmenu("refresh");
    jQuery('#city_button').show();
}

//SEARCH
function doSearch(){
    var city_id = jQuery("#city_select option:selected").val();
    if(city_id != null){
        jQuery("#promolist").html("");
        var db = window.openDatabase("promosalpaso", "1.0", "Promos al Paso", 200000);
        db.transaction(function(tx){querySearchDB(tx, city_id)}, errorSearchDB);    
    }
    else{
        showMessage("No hay ciudad seleccionada.", "Info", "Ok");
    }
}
function querySearchDB(tx, city_id) {
        tx.executeSql('SELECT * FROM city WHERE city_id = ' + city_id, [], querySearchSuccess, errorSearchDB);
}
function querySearchSuccess(tx, results) {
    len = results.rows.length;
    if(len = 1){
        _lat = results.rows.item(0).latitude;
        _lng = results.rows.item(0).longitude;
        loadPromoList();
        $.mobile.changePage(jQuery("#one"));
    }
}
function errorSearchDB(err){
    console.log("error en la búsqueda de promociones por ciudad: " + err.code);
}