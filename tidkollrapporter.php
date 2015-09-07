<?php

//Observera att $sokPaDatum samt $sokPaPeriod inte står för samma sak här som i redigeraAktivitet.....
    function printRapport($db,$sokPaDatum, $sokPaPeriod, $sokPaArbUpg, $datum, $tillDatum, $arbUpg){
        /*echo '$sokPaDatum: '.$sokPaDatum."<br>";
        echo '$sokPaPeriod: '.$sokPaPeriod."<br>";
        echo '$sokPaArbUpg: '.$sokPaArbUpg."<br>";
*/
         $sql = "SELECT aktivitet.id, stil, ";
        if(!$sokPaDatum){
            $sql.="date_format(datum, '%u') as Vecka, ";
            //$sql.="date_format(datum, '%a') as dag, datum as Datum,  ";
            //$sql.="date_format(datum, '%a V%u') as dag, datum as Datum,  ";
            //$sql.="DAYNAME(datum) as dag, datum as Datum,  ";
            $sql.="concat(DAYNAME(datum),' ', datum)  as '',  ";
        }
        if(!$sokPaArbUpg){
            $sql.= "arbuppgift.namn as '', ";
        }
        $sql .= "aktivitetstyp.namn as Aktivitetstyp, concat(langd,' + ', stalltid) as Längd, ";
//        $sql.="concat(kommentar,concat('<br>', group_concat(concat('<br>',innehall))))   as Kommentar ".
//          $sql.="concat(kommentar,group_concat(innehall))   as Kommentar ".
          $sql.="concat('<b>',aktivitet.rubrik,'</b><br/>',kommentar) as Kommentar,concat('<b><br/>',  anteckning.rubrik , '</b>', innehall,'') as Anteckning ".

        "FROM aktivitet inner join arbuppgift on arbuppgift.id=aktivitet.arbuppgift ".
        "inner join aktivitetstyp on aktivitetstyp.id=aktivitet.aktivitetstyp ".
        "left outer join anttillakt on aktivitet.id=anttillakt.aktivitet ".
        "left outer join anteckning on anteckning.id=anttillakt.anteckning ";
        "left outer join anttyp on anttyp.id=anteckning.typ ";
        if($sokPaDatum ){
            $sql.=" where datum='$datum'";

        }
        if($sokPaPeriod){
                $sql.=" where datum>='$datum' and datum<='$tillDatum'";
        }
        if(($sokPaDatum || $sokPaPeriod) && $sokPaArbUpg){
            $sql.=" and ";
        }
        else {
            if($sokPaArbUpg){
                $sql.=" where ";
            }
        }
        if($sokPaArbUpg){
            $sql.="arbuppgift='$arbUpg'";
        }
        $sql.=" order by datum, arbuppgift, aktivitet.id";
        //echo $sql."<br>";
        $res = $db->query($sql);

        if( ((!$sokPaDatum) && (!$sokPaArbUpg))|| ($sokPaPeriod && (!$sokPaArbUpg)) ) {
            $kategorier = 3;
            //echo "tre grupper";

        }
        elseif($sokPaPeriod || $sokPaArbUpg ){
            $kategorier = 2;
            //echo "två grupper";
        }
        else {
            $kategorier = 1;
            //echo "en grupper";
        }
        $groupconcat = $kategorier + 5;
        $slaIhop = $groupconcat -1;
//        echo '$kategorier: '. $kategorier . ' $groupconcat: '. $groupconcat.' $slaIhop: '.$slaIhop.'<br/>';
//        $res->drawHTMLTableWithLink("redigeraaktivitet.php?akt=", $kategorier, true);
        $res->drawHTMLTableWithLink("tidkoll.php?akt=", $kategorier, $groupconcat,$slaIhop, true);

        if($sokPaDatum){
            echo "<br>";
            $sql = "SELECT sum(langd+stalltid)/60 as 'Summa längd+ställtid $datum i timmar' FROM aktivitet where datum='$datum'";
            //echo $sql;
            $res = $db->query($sql);
            $res->drawHTMLTable();
            echo "<br>";
            $sql = "SELECT sum(langd) as 'Summa längd $datum i minuter' FROM aktivitet where datum='$datum'";
            $res = $db->query($sql);
            $res->drawHTMLTable();
        }
        if($sokPaPeriod){
            echo "<br>";
            $sql = "SELECT sum(langd+stalltid)/60 as 'Summa längd+ställtid från $datum till $tillDatum i timmar' FROM aktivitet where datum>='$datum' and datum<='$tillDatum'";
            //echo $sql;
            $res = $db->query($sql);
            $res->drawHTMLTable();            
        }
 
        if($sokPaArbUpg){
           echo "<br>";

            $arbUpgNamn = $db->getSingleValue("select namn from arbuppgift where id=$arbUpg");

            $sql = "SELECT sum(langd)/60 as 'Summa längd $arbUpgNamn i timmar' FROM aktivitet where arbuppgift='$arbUpg'";
            //echo $sql;
            $res = $db->query($sql);
            $res->drawHTMLTable();
            echo "<br>";
            $sql = "SELECT sum(langd) as 'Summa längd $arbUpgNamn i minuter' FROM aktivitet where arbuppgift='$arbUpg'";
            $res = $db->query($sql);
            $res->drawHTMLTable();
            echo "<br>";

            $sql = "SELECT sum(langd)/60 as 'Summa längd $arbUpgNamn i timmar undervisning', count(*) as 'Antal tillfällen' FROM aktivitet where arbuppgift='$arbUpg' and aktivitetstyp=1";
            //echo $sql;
            $res = $db->query($sql);
            $res->drawHTMLTable();


        }
    }

?>
