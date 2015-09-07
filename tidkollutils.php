<?php
function corrDate($dateString, $weekDay=0){
    //echo "substr($dateString, 0,1): ".substr($dateString, 0,1);
    if(substr($dateString, 0,1)=="v" || substr($dateString, 0,1)=="V"){
        if(strlen($dateString)==1){
            echo "dateString: $dateString</br>\n";
            echo "strtotime( $dateString ): ".strtotime( $dateString )."   ".date("Y-m-d", strtotime( $dateString ))."</br>\n";
            echo "veckodag(strtotime( $dateString ): ".veckodag(strtotime( $dateString ))."</br>\n";
            
            $dateString = date("Y-m-d", strtotime( $dateString )-60*60*24*(veckodag(strtotime( $dateString ))-$weekDay) );
            echo "dateString: $dateString</br>\n";
        }
        else {
            $currWeek=date("W");
            $reqWeek=substr($dateString,1,2);
            $addweek = $reqWeek - $currWeek;
            $addDays = $addweek*7 - veckodag(time())+$weekDay;
           /* echo '$currWeek '.$currWeek.'<br>';
            echo '$reqWeek '.$reqWeek.'<br>';
            echo '$diffweek '.$addweek.'<br>';
            echo '$weekDay '.$weekDay.'<br>';
            echo 'veckodag(time() '.veckodag(time()).'<br>';
            echo '$addDays '.$addDays.'<br>';*/
            $dateString = date("Y-m-d", time()+60*60*24*$addDays );

        }

        
    }
    elseif($dateString=="-"){
        $dateString = "2099-12-31";
    }
    elseif($dateString=="d" || $dateString=="idag"){
         $dateString = date("Y-m-d");
     }
    elseif($dateString=="m" || $dateString=="imorgon"){
         $dateString = date("Y-m-d" , time()+60*60*24);
     }
    elseif($dateString=="g" || $dateString=="igår"){
         $dateString = date("Y-m-d" , time()-60*60*24);
     }
    else{
        switch(strlen($dateString)){
            case 1: $dateString = "0". $dateString;
                case 2:
                    $dateString =  date("Y-m-") .$dateString;
                    break;
                case 4:
                    $dateString = date("Y-").substr($dateString, 0, 2) . "-" . substr($dateString,2);
                    break;
                case 5:
                    $dateString = date("Y-").$dateString;



         }
     }
            return $dateString;
 }
 function veckodag($tid){
     $phpDag = date("w", $tid);
     if($phpDag==0){
         return 7;
     }
     else {
         return $phpDag - 1;
     }
 }
        ?>
