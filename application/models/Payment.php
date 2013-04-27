<?php

class PAP_Model_Payment
{
    public static function  getWorkingDays($startDate, $endDate, $holidays = array()){ 

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
        $grandtotal = 0;
        foreach($periods as $period){
            $promoObject = new PAP_Model_Promotion();
            $promos = $promoObject->getPromotionsByDates($period->getFrom(), $period->getTo(), $user);
            if(isset($promos)){
                $payment = array();
                $payment['periodo'] = $period->getCode();
                $payment['desde'] = $period->getFrom();
                $payment['hasta'] = $period->getTo();    
                $total = PAP_Model_Payment::getPeriodsRecords($period, $promos, $promoscost);
                $payment['costos'] = $promoscost;
                $payment['total'] = $total;
                $payments[] = $payment;
                $grandtotal += $total;
            }
        }
        return $payments;    
    }
    
    public static function getAllPayments($periods){
        $payments = array();
        $grandtotal = 0;
        $i = 0;
        $userpromos = array();
        $current_user = '';
        foreach($periods as $period){
            $promoObject = new PAP_Model_Promotion();
            $allpromos = $promoObject->getPromotionsByDates($period->getFrom(), $period->getTo(), null);
            $countpromos = count($allpromos);
            foreach($allpromos as $promo){
                $i += 1;
                if(!($current_user == $promo['user_id'] || $current_user == '')){
                    if(isset($userpromos)){
                        $payment = array();
                        $payment['user_id'] = $current_user;
                        $payment['periodo'] = $period->getCode();
                        $payment['desde'] = $period->getFrom();
                        $payment['hasta'] = $period->getTo();    
                        $total = PAP_Model_Payment::getPeriodsRecords($period, $userpromos, $promoscost);
                        $payment['costos'] = $promoscost;
                        $payment['total'] = $total;
                        $payments[] = $payment;
                        $grandtotal += $total;
                    }
                    $userpromos = array();
                    $total = 0;
                }
                $userpromos[] = $promo;
                $current_user = $promo['user_id'];
                if($countpromos == $i){
                    //proceso ultima promoción.
                    $payment = array();
                    $payment['user_id'] = $current_user;
                    $payment['periodo'] = $period->getCode();
                    $payment['desde'] = $period->getFrom();
                    $payment['hasta'] = $period->getTo();    
                    $total = PAP_Model_Payment::getPeriodsRecords($period, $userpromos, $promoscost);
                    $payment['costos'] = $promoscost;
                    $payment['total'] = $total;
                    $payments[] = $payment;
                    $grandtotal += $total;    
                }
            }
        }
        return $payments;    
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
    
    public static function getPeriodsRecords($period, $promos, &$promoscost){
        $promocost = '';
        $cost = 0;
        $total = 0;
        $promoscounter = 0;
        $cost_row = array();
        $promos_rows = array();
        $first = true;
        $cantdias = 0;
        $current_user = '';
        foreach ($promos as $promo){
            if($promocost <> $promo['promo_cost']){
                $promocost = $promo['promo_cost'];
                if($first)
                    $first = false;    
                else{
                    $cost_row['cost'] = $cost;
                    $cost_row['promo_count'] = $promoscounter;
                    $cost_row['cant_dias'] = $cantdias;
                    $cost_row['subtotal'] = $cost * $cantdias;
                    $promos_rows[] = $cost_row;
                    $total =+ $cost * $cantdias;   
                    $promoscounter = 0;
                    $cantdias = 0;
                }    
            }
            $a_cost = explode('-', $promo['promo_cost']);
            $cost = (float)$a_cost[1];
            $promoscounter += 1;
            //Dejamos que el limite derecho lo calcule el controlador modificando 'ends' $today = date("Y-m-d H:i:s");
            $datefrom = ($period->getFrom()>$promo['starts'])?$period->getFrom():$promo['starts'];
            $dateto = ($period->getTo()>$promo['ends'])?$promo['ends']:$period->getTo();
            
            $cantdias += round(PAP_Model_Payment::getWorkingDays($datefrom, $dateto));
        }
        $cost_row['cost'] = $cost;
        $cost_row['promo_count'] = $promoscounter;
        $cost_row['cant_dias'] = $cantdias;
        $cost_row['subtotal'] = $cost * $cantdias;
        $promos_rows[] = $cost_row;
        $total += $cost * $cantdias;
        $promoscost = $promos_rows;
        return $total;
    }

}

