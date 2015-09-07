<?php
require_once 'db.php';
require_once 'ResultSet.php';
require_once 'tidkollutils.php';
require_once 'redigeraaktivitetfunktioner.php';
require_once 'tidkollrapporter.php';
require_once 'RedigeraView.php';
require_once 'RaderaView.php';
require_once 'RedAntView.php';
require_once 'SkrivInBudgetView.php';
require_once 'RedBudgetView.php';
require_once 'NyAntView.php';
require_once 'RaderaAntView.php';
session_start();

/* function trace($str){
  echo $str;
  }
 */

function trace($str) {

}
?>

<!DOCTYPE HTML>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <title>Tidkoll</title>
        <link rel="stylesheet" type="text/css" href="tidkollstyle.css" />
    </head>
    <div class="navigering">
        <a href="tidkoll.php"> Redigera/Planera </a> &nbsp; &nbsp;
        <a href="budget.php?add"> Lägg till budgetpost </a> &nbsp; &nbsp;
        <a href="rapportsida.php"> Rapport </a>  <br>
    </div>


<?php
$db = new DB();
$db->query("SET lc_time_names = 'sv_SE';");
if (isset($_SESSION['currentView'])) {

    $currentView = $_SESSION['currentView'];
    $currentView->setDB($db);
} else {
    $currentView = null;
}

if ($currentView != null) {
    trace("Nuvarande vy:" . $currentView->getName());

    $currentView = $currentView->process();
} else {
    trace("Ingen process view null<br>\n");
}



if (isset($_GET['bp'])) {
    $currentView = new RedBudgetView($db, $_GET['bp']);
}
if(isset($_GET['add'])){
    $currentView = new SkrivInBudgetView($db);
}


if ($currentView != null) {
    trace("Nuvarande vy:" . $currentView->getName());
    $currentView->showUI();
} else {
    trace("Ingen showUI view null<br>\n");
}

$_SESSION['currentView'] = $currentView;


/*
if(isset($_POST['visaant'])){
$arbuppgift=$_POST['arbuppgift'];
}
elseif(isset($_SESSION['arbupgsok'])){
    $arbuppgift=$_SESSION['arbupgsok'];
}
else {
    $arbuppgift=null;
}
echo "<form action='seanteckningar.php' method='post'>\n";
echo '<label for="arbuppgift">    Kurs/Arbetsuppgift:</label>';
$db->QueryAndGenereateSelectList("arbuppgift", "arbuppgift", "id", "namn", $arbuppgift , "this.form.visaant.click()" );
echo '<input type="submit" name="visaant" value="Visa anteckning"/>';
echo "\n</form>";


if($arbuppgift!=null){
*/
$sql = "select distinct budget.id, arbuppgift.namn as '', aktivitetstyp.namn as 'Akrivitetstyp',timmar as 'Timmar', kommentar as Kommentar ".
"from budget, aktivitetstyp, arbuppgift where aktivitetstyp.id=budget.aktivitetstyp and arbuppgift.id=budget.arbuppgift ".
"order by arbuppgift.id";
$res=$db->query($sql);
$res->drawHTMLTableWithLink("budget.php?bp=", 1);
/*}
else {
    echo "Visa ant inte satt";
}

$_SESSION['arbupgsok']=$arbuppgift;
*/


$sql = "select sum(timmar) from budget";
$budtot = $db->getSingleValue($sql);
echo "<h2>Totalt antal timmar :".$budtot."</h2>";



$sql = "select distinct arbuppgift.namn as 'Arbetuppgift',sum(timmar) as 'Timmar' ".
"from budget, arbuppgift where arbuppgift.id=budget.arbuppgift ".
"group by arbuppgift order by Timmar desc";
echo $sql."<br/>";
$res=$db->query($sql);
$res->drawHTMLTable();


//echo "<br/><br/>\n";

$defaultFranDatum ="";
if(isset($_POST['frandatum'])){
    $defaultFranDatum=$_POST['frandatum'];
}
$defaultTillDatum = date("Y-m-d");
if(isset($_POST['tilldatum'])){
    $defaultTillDatum=$_POST['tilldatum'];
}




// Rapport efter kurs/arbetsuppgift
echo "<h5>Rapport efter arbetsuppgift <h5>\n";

$sql ="select arbuppgift.id,arbuppgift.namn as Arbetsuppgift, sum(langd) as Minuter, sum(langd)/60 as Timmar, sum(stalltid)/60 as Ställtid, (sum(langd) + sum(stalltid))/60 as 'Timmar totalt'  ";
$sql.="FROM aktivitet inner join arbuppgift on arbuppgift.id=aktivitet.arbuppgift inner join aktivitetstyp on aktivitetstyp.id=aktivitet.aktivitetstyp where 1 ";
$anddatum="";
if($defaultFranDatum!=""){
    $anddatum.="and datum>=$defaultFranDatum ";
}
if($defaultTillDatum!=""){
    $anddatum.="and datum<='$defaultTillDatum' ";

}
$sql .=$anddatum;
$sql.="group by arbuppgift order by Timmar desc";
echo $sql;
$res = $db->query($sql);
$res->drawHTMLTable();

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<form action="rapportsida.php" method="post">
Från datum <INPUT type="TEXT" name="frandatum" value="<?php echo $defaultFranDatum ?>"/>
Till datum<INPUT type="TEXT" name="tilldatum" value="<?php echo $defaultTillDatum ?>"/>
<br>
<input type="submit"/>
</form>

<?php

$res=$db->query($sql);
$res->drawHTMLTable();
$sql = "select distinct arbuppgift.namn as 'Arbetuppgift',sum(timmar) as 'Timmar', arbuppgift.id ".
"from budget, arbuppgift where arbuppgift.id=budget.arbuppgift ".
"group by arbuppgift order by Timmar desc";
echo $sql."<br/>";
$res=$db->query($sql);
echo "<table border = 1>";
echo "<tr><th>Arbetsuppgift</th><th>budget</th><th>förbrukat</th><th>förbrukat i procent av budget</th></tr>\n";
while ($row=$res->fetchRow()){
    echo "<tr><td>\n";
    echo $row[0]. "</td><td>".$row[1]."</td><td> ";
    $sql2="select sum(langd+stalltid)/60 from aktivitet where arbuppgift=$row[2] $anddatum";
    $timupg=round($db->getSingleValue($sql2),2);
    echo $timupg."</td><td>";
    echo round($timupg/$row[1]*100,2);
    echo "</td></tr>\n";
}
echo "</table>\n";
$sql = "select sum(timmar) from budget";
$budtot = $db->getSingleValue($sql);
echo "Totalt antal timmar :".$budtot."<br/>";
    $sql2="select sum(langd+stalltid)/60 from aktivitet where 1 $anddatum";
    $timtot=round($db->getSingleValue($sql2),2);
    echo "Summar totalt införda i systemet: ".$timtot."<br/>\n";
echo "Procent: ".round($timtot/$budtot*100, 2)."<br/>";


$sql = "select distinct arbuppgift.namn as 'Arbetuppgift',sum(timmar) as 'Timmar', sum(aktivitet.langd)/60 ".
"from budget inner join arbuppgift on arbuppgift.id=budget.arbuppgift left outer join aktivitet on aktivitet.arbuppgift=arbuppgift.id ".
"where datum<'111026' group by budget.arbuppgift order by arbuppgift.id ";
$res=$db->query($sql);
$res->drawHTMLTable();


?>
