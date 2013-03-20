
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