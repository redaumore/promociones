<?php

class PAP_Model_PriceRule
{
    /***
    * Devuelve el código del período al cual pertenece una fecha con formato dd-mm-yyyy
    */
    public static function getPeriodCode($date){
        try{
            $code = '';
            $date = DateTime::createFromFormat('d-m-Y', $date);
            $day = date('j', $date);
            $month = date('m', $date);
            $year = date('Y', $date);
            if($day < 15)
                $code = '1-'.$month.'-'.$year;    
            else
                $code = '2-'.$month.'-'.$year;
            return $code;
        }
        catch(Exception $ex){
            return '';
        }
    }
    
    

}

