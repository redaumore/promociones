<?php
    $lat  = $_GET['lat'];
    $lng = $_GET['lng']; 
    //$promotion = new PAP_Model_Promotion();
    //$records = $promotion->getPromotionsByCoords($lat, $lng);
    echo $_GET['jsoncallback'] .'([{"lat":"'.$lat.'","lng":"'.$lng.'"}])'
    //echo $_GET['jsoncallback']."(".json_encode($records).")"; 
?>
