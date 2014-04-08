<?php
class PAP_Helper_Tools extends Zend_Controller_Action_Helper_Abstract
{
    public static function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }
    
    /*Devuelve millas o metros por defecto.*/
    public static function getDistance($lat1, $lng1, $lat2, $lng2, $miles = false)
    {
        $pi80 = M_PI / 180;
        $lat1 *= $pi80;
        $lng1 *= $pi80;
        $lat2 *= $pi80;
        $lng2 *= $pi80;
     
        $r = 6372.797; // mean radius of Earth in km
        $dlat = $lat2 - $lat1;
        $dlng = $lng2 - $lng1;
        $a = sin($dlat / 2) * sin($dlat / 2) + cos($lat1) * cos($lat2) * sin($dlng / 2) * sin($dlng / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $km = $r * $c;
     
        return ($miles ? ($km * 0.621371192) : round($km * 1000));
    }
    
    public static function getCoordinates($street, $city, $province){
        $address = $street.','.$city.','.$province.','.'Argentina';
        $address = str_replace(" ", "+", $address); // replace all the white space with "+" sign to match with google search pattern
         
        $url = "http://maps.google.com/maps/api/geocode/json?sensor=false&address=$address";
         
        $response = file_get_contents($url);
        
        $json = json_decode($response,TRUE); //generate array object from the response from the web
        
        $arr['lat'] = $json['results'][0]['geometry']['location']['lat'];  
        $arr['lng'] = $json['results'][0]['geometry']['location']['lng'];   
        
        return $arr; 
        //return ($json['results'][0]['geometry']['location']['lat'].",".$json['results'][0]['geometry']['location']['lng']);
         
        }
        
 }