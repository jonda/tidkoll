<?php session_start();
$timmarHittils=20;
$timmarTotalt=1808*.8;
$andel=$timmarHittils/$timmarTotalt;




$defaultFranDatum ="";
if(isset($_POST['frandatum'])){
    $defaultFranDatum=$_POST['frandatum'];
}
$defaultTillDatum = date("Y-m-d");
if(isset($_POST['tilldatum'])){
    $defaultTillDatum=$_POST['tilldatum'];
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
   <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
   <title>Tidkoll</title>
   <link rel="stylesheet" type="text/css" href="tidkollstyle.css" />

</head>
<div class="navigering">
<a href="tidkoll.php"> Redigera/Planera </a>  &nbsp; <a href="skrivinaktivitet.php"> Lägg till aktivitet </a>  <br>

</div>
<form action="rapportsida.php" method="post">
Från datum <INPUT type="TEXT" name="frandatum" value="<?php echo $defaultFranDatum ?>"/>
Till datum<INPUT type="TEXT" name="tilldatum" value="<?php echo $defaultTillDatum ?>"/>
<br>
<input type="submit"/>
</form>

<?php


require_once 'db.php';
require_once 'ResultSet.php';
$db = new db();
    $sql="select sum(langd+stalltid)/60 from aktivitet where 1 ";
if($defaultFranDatum!=""){
    $sql.="and datum>='$defaultFranDatum' ";
}
if($defaultTillDatum!=""){
    $sql.="and datum<='$defaultTillDatum' ";
}
    $timtot=round($db->getSingleValue($sql),2);
echo "<h2>Totalt antal timmar :".$timtot."</h2>";

// Rapport efter kurs/arbetsuppgift
echo "<h2>Rapport efter arbetsuppgift <h2>\n";
echo "<h3>Översikt: </h3>\n";

$sql ="select arbuppgift.id,arbuppgift.namn as Arbetsuppgift, sum(langd) as Minuter, sum(langd)/60 as Timmar, sum(stalltid)/60 as Ställtid, (sum(langd) + sum(stalltid))/60 as 'Timmar totalt'  ";
$sql.="FROM aktivitet inner join arbuppgift on arbuppgift.id=aktivitet.arbuppgift inner join aktivitetstyp on aktivitetstyp.id=aktivitet.aktivitetstyp where 1 ";

if($defaultFranDatum!=""){
    $sql.="and datum>=$defaultFranDatum ";
}
if($defaultTillDatum!=""){
    $sql.="and datum<='$defaultTillDatum' ";

}
$sql.="group by arbuppgift order by Timmar desc";
echo $sql;
$res = $db->query($sql);
$res->drawHTMLTable();
$res = $db->query($sql);
while($row1=$res->fetchRow()){
    echo "<h3>$row1[1]  $row1[3] </h3>";
    $id = $row1[0];
    $sql ="select aktivitetstyp.namn as Arbetsuppgift, sum(langd)/60 as Timmar, sum(stalltid)/60 as Ställtid,(sum(langd) + sum(stalltid))/60 as 'Timmar totalt' ";
    $sql.="FROM aktivitet inner join arbuppgift on arbuppgift.id=aktivitet.arbuppgift inner join aktivitetstyp on aktivitetstyp.id=aktivitet.aktivitetstyp ";
    $where="where arbuppgift=$id ";
    if($defaultFranDatum!=""){
        $where.="and datum>='$defaultFranDatum' ";
    }
    if($defaultTillDatum!=""){
        $where.="and datum<='$defaultTillDatum' ";

    }
    
    $where.="group by aktivitetstyp order by Timmar desc";
    $sql.=$where;
    //echo $sql;
    $resarb = $db->query($sql);
    $resarb->drawHTMLTable();
    echo "<br/>\n";

    $sql ="select aktivitetstyp.namn as Aktivitetstyp, Timmar, timmar*$andel from budget inner join aktivitetstyp on aktivitetstyp.id=aktivitetstyp where arbuppgift=$id group by aktivitetstyp ";
   // echo $sql.'<br/>';
    $resbud = $db->query($sql);
    $resbud->drawHTMLTable();


}

//Rapport efter aktivitetstyp
echo "<h2> Rapport efter aktivitetstyp </h2>";

$sql ="select aktivitetstyp.id,aktivitetstyp.namn as Aktivitetstyp, sum(langd) as Minuter, sum(langd)/60 as Timmar ";
$sql.="FROM aktivitet inner join arbuppgift on arbuppgift.id=aktivitet.arbuppgift inner join aktivitetstyp on aktivitetstyp.id=aktivitet.aktivitetstyp where 1 ";

if($defaultFranDatum!=""){
    $sql.="and datum>=$defaultFranDatum ";
}
if($defaultTillDatum!=""){
    $sql.="and datum<='$defaultTillDatum' ";

}
$sql.="group by aktivitetstyp order by Timmar desc";
echo $sql;
$res = $db->query($sql);
echo "<h2>Översikt: <h2>\n";
$res->drawHTMLTable();
$res = $db->query($sql);
while($row1=$res->fetchRow()){
    echo "<h3>$row1[1]  $row1[3] </h3>";
    $id = $row1[0];
    $sql ="select arbuppgift.namn as Arbetsuppgift, sum(langd) as Minuter, sum(langd)/60 as Timmar ";
    $sql.="FROM aktivitet inner join arbuppgift on arbuppgift.id=aktivitet.arbuppgift inner join aktivitetstyp on aktivitetstyp.id=aktivitet.aktivitetstyp ";
    $sql.="where aktivitetstyp=$id ";
    if($defaultFranDatum!=""){
        $sql.="and datum>='$defaultFranDatum' ";
    }
    if($defaultTillDatum!=""){
        $sql.="and datum<='$defaultTillDatum' ";

    }

    $sql.="group by arbuppgift order by Timmar desc";
    echo $sql;
    $resarb = $db->query($sql);
    $resarb->drawHTMLTable();
}

?>
