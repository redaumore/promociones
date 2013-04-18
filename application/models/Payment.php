<?php

class PAP_Model_Payment
{
    public static function  getWorkingDays($startDate, $endDate, $holidays = null){ 

        // Calculate weekday number. Monday is 1, Sunday is 7 
        $firstWeekdayNumber = date("N", strtotime($startDate)); 
        $lastWeekdayNumber  = date("N", strtotime($endDate)); 

        // Normalize the dates if they're weekends or holidays as they count for full days (24 hours) 
        if ($firstWeekdayNumber == 6 || $firstWeekdayNumber == 7 || in_array( date("Y-m-d", strtotime($startDate)), $holidays )) 
            $startDate = date("Y-m-d 00:00:00", strtotime($startDate)); 
        if ($lastWeekdayNumber == 6  || $lastWeekdayNumber == 7  || in_array( date("Y-m-d", strtotime($endDate)), $holidays )) 
            $endDate   = date("Y-m-d 00:00:00", strtotime("+1 days", strtotime( $endDate ))); 

        // Compute the floating-point differences in the dates 
        $daysDifference          = (strtotime($endDate) - strtotime($startDate)) / 86400; 
        $fullWeeksDifference     = floor($daysDifference / 7); 
        $remainingDaysDifference = fmod($daysDifference, 7); 

        // Subtract the weekends; In the first case the whole interval is within a week, in the second case the interval falls in two weeks. 
        if ($firstWeekdayNumber <= $lastWeekdayNumber){ 
            if ($firstWeekdayNumber <= 6 && 6 <= $lastWeekdayNumber && $remainingDaysDifference >= 1) $remainingDaysDifference--; 
            if ($firstWeekdayNumber <= 7 && 7 <= $lastWeekdayNumber && $remainingDaysDifference >= 1) $remainingDaysDifference--; 
        } 
        else{ 
            if ($firstWeekdayNumber <= 6  && $remainingDaysDifference >= 1) $remainingDaysDifference--; 
            // In the case when the interval falls in two weeks, there will be a Sunday for sure 
            $remainingDaysDifference--; 
        } 

        // Compute the working days based on full weeks + 
        $workingDays = $fullWeeksDifference * 5; 
        if ($remainingDaysDifference > 0 ) 
            $workingDays += $remainingDaysDifference; 

        // Subtract the holidays 
        foreach($holidays as $holiday) 
        { 
            $holidayTimeStamp=strtotime($holiday); 
            // If the holiday doesn't fall in weekend 
            if (strtotime($startDate) <= $holidayTimeStamp && $holidayTimeStamp <= strtotime($endDate) && date("N",$holidayTimeStamp) != 6 && date("N",$holidayTimeStamp) != 7 && $workingDays >= 1) 
                $workingDays--; 
        } 
        
        // End of calculation, return the result now 
        return $workingDays; 
    }
    
    public static function getPayments($user, $periods){
        $payments = array();
        
        foreach($periods as $period){
            $payment = array();
            $payment['periodo'] = $period->getCode();
            $payment['desde'] = $period->getFrom();
            $payment['hasta'] = $period->getTo();
            $promoObject = new PAP_Model_Promotion();
            $promoObject->
        }    
    }
    
    public static function getGrandTotal($promo){
        return PAP_Model_Payment::getTotal($promo, $promo->getStarts(), $promo->getEnds());    
    }
    
    public static function getTotal(PAP_Model_Promotion $promo, $from, $to){
        $days = PAP_Model_Payment::getWorkingDays($from, $to);
        $cost = (float)explode('-',$promo->getPromoCost());
        if($days > 0){
          $total = $days * $cost[1];
          return $total;      
        }
        else
            return 0;
    } 

}

