<?php

require_once 'db.php';
require_once 'ResultSet.php';
require_once 'tidkollutils.php';
require_once 'redigeraaktivitetfunktioner.php';
require_once 'tidkollrapporter.php';
require_once 'RedigeraView.php';
require_once 'RaderaView.php';
require_once 'RedAntView.php';
require_once 'SkrivInView.php';
require_once 'NyAntView.php';
require_once 'RaderaAntView.php';

session_start();

/*function trace($str){
    echo $str;
}*/

function trace($str){
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
<a href="tidkoll.php?add"> Lägg till aktivitet </a> &nbsp; &nbsp;
<a href="rapportsida.php"> Rapport </a>&nbsp; &nbsp;
<a href="seanteckningar.php"> Anteckningar </a> &nbsp; &nbsp;
<a href="budget.php"> Budget </a>  <br>
</div>








<?php



$dateToday = date("Y-m-d");
   $db = new DB();
$db->query("SET lc_time_names = 'sv_SE';");
if(isset($_SESSION['currentView'])){

    $currentView=$_SESSION['currentView'];
    $currentView->setDB($db);
}
else {
    $currentView=null;
}

if($currentView!=null){
    trace ("Nuvarande vy:".$currentView->getName());

    $currentView=$currentView->process();
}
else {
    trace ("Ingen process view null<br>\n");
}



if(isset($_GET['akt'])){
    $currentView = new RedigeraView($db, $_GET['akt']);
}

if(isset($_GET['rad'])){
    $currentView = new RaderaView($db);
}
if(isset($_GET['add'])){
    $currentView = new SkrivInView($db);
}


$_SESSION['currentView']=$currentView;


$defaultSokPaDatum = "";
$defaultSokPaArbUpg = '';
$sokPaArbUpg = false;
$sokPaDatum = false;
if(isset($_POST['skickadatum'])){
    if($_POST['skickadatum']=="Visa"){


    $defaultDatum = corrDate($_POST['datumvisa'],0);
    if(substr($_POST['datumvisa'],0,1)=="v" && $_POST['tilldatumvisa']=="")
    {
        $defaultTillDatum = corrDate($_POST['datumvisa'],6);
    }
    else
    {
        $defaultTillDatum = corrDate($_POST['tilldatumvisa'],6);
    }

    if(isset($_POST['sokpadatum'])){
        $sokPaDatum = true;

    }
    if(isset($_POST['sokpaarbupg'])){
        $sokPaArbUpg = true;
    }

    }
    else {
        if ($_POST['skickadatum']=="Idag"){
           $defaultDatum = corrDate("idag");
        }
        elseif ($_POST['skickadatum']=="Imorgon"){
           $defaultDatum = corrDate("imorgon");

        }
        elseif ($_POST['skickadatum']=="Igår"){
           $defaultDatum = corrDate("igår");

        }
        elseif ($_POST['skickadatum']=="Måndag"){
           $defaultDatum = corrDate("v",0);
        }
        elseif ($_POST['skickadatum']=="Nästa Måndag"){
           $defaultDatum = corrDate("v",7);
        }
        elseif ($_POST['skickadatum']=="Tisdag"){
           $defaultDatum = corrDate("v",1);
        }
        elseif ($_POST['skickadatum']=="Nästa Tisdag"){
           $defaultDatum = corrDate("v",8);
        }
        elseif ($_POST['skickadatum']=="Onsdag"){
           $defaultDatum = corrDate("v",2);
        }
        elseif ($_POST['skickadatum']=="Nästa Onsdag"){
           $defaultDatum = corrDate("v",9);
        }
        elseif ($_POST['skickadatum']=="Torsdag"){
           $defaultDatum = corrDate("v",3);
        }
        elseif ($_POST['skickadatum']=="Nästa Torsdag"){
           $defaultDatum = corrDate("v",10);
        }
        elseif ($_POST['skickadatum']=="Fredag"){
           $defaultDatum = corrDate("v",4);
        }
        elseif ($_POST['skickadatum']=="Nästa Fredag"){
           $defaultDatum = corrDate("v",11);
        }
        $sokPaDatum = true;
         $defaultTillDatum="";
    }



    $defaultArbUpg = $_POST['arbupgsok'];
    $_SESSION['sokPaArbUpg'] = $sokPaArbUpg;
    $_SESSION['sokPaDatum'] = $sokPaDatum;
    $_SESSION['arbupgsok'] = $defaultArbUpg;
    $_SESSION['datumvisa'] = $defaultDatum;
    $_SESSION['tilldatumvisa'] = $defaultTillDatum;
}
elseif(isset($_SESSION['datumvisa'])){
$defaultDatum = $_SESSION['datumvisa'];
$defaultTillDatum = $_SESSION['tilldatumvisa'];
$defaultArbUpg = $_SESSION['arbupgsok'];
$sokPaDatum = $_SESSION['sokPaDatum'];
$sokPaArbUpg = $_SESSION['sokPaArbUpg'];
}
else {
$defaultDatum = $dateToday;
$defaultTillDatum = "";
$defaultArbUpg = 1;
/*    if(isset($_POST['sokpadatum'])){
$defaultSokPaDatum = "";
}
if(isset($_POST['sokpaarbupg'])){
$defaultSokPaArbUpg = "checked='checked'";
trace '$defaultSokPaArbUpg: '. $defaultSokPaDatum."<br>";
}
*/
}

if($currentView!=null){
    trace("Nuvarande vy:".$currentView->getName());
    $currentView->showUI($defaultDatum);
}
else {
    trace ("Ingen showUI view null<br>\n");
}

if($sokPaDatum){
$defaultSokPaDatum = "checked='checked'";
//trace '$defaultSokPaDatum: '. $defaultSokPaDatum."<br>";
}
if($sokPaArbUpg){
$defaultSokPaArbUpg = "checked='checked'";
//trace '$defaultSokPaArbUpg: '. $defaultSokPaArbUpg."<br>";
}

$sokPaPeriod=false;
if($sokPaDatum){
if($defaultTillDatum!=""){
    $sokPaPeriod=true;
    //trace "Nu söker vi på period";
}
/*    if(isset($_POST['tilldatumvisa'])){
if($_POST['tilldatumvisa']!=""){
    $sokPaPeriod=true;
    //trace "Nu söker vi på period";
}
}
elseif(isset($_SESSION['tilldatumvisa'])){
if($_SESSION['tilldatumvisa']!=""){
    $sokPaPeriod=true;
    //trace "Nu söker vi på period";
}
}*/

}
?>
<br>
<form method="POST" action="tidkoll.php">
    Datum:
    <input type="checkbox" name="sokpadatum" value="1" <?php echo $defaultSokPaDatum ?>>
    <input type="text" name="datumvisa" value="<?php echo $defaultDatum?>"/>
    <input type="text" name="tilldatumvisa" value="<?php echo $defaultTillDatum?>"/>
    Kurs/Arbetsuppgift:
    <input type="checkbox" name="sokpaarbupg" value="1" <?php echo $defaultSokPaArbUpg ?>>
    <?php $db->QueryAndGenereateSelectList("arbupgsok", "arbuppgift", "id", "namn", $defaultArbUpg)  ?>
    <input type="submit" name="skickadatum" value="Visa"/>
        <br/>
        <input type="submit" name="skickadatum" value="Igår"/>
        <input type="submit" name="skickadatum" value="Idag"/>
        <input type="submit" name="skickadatum" value="Imorgon"/>
        <input type="submit" name="skickadatum" value="Måndag"/>
        <input type="submit" name="skickadatum" value="Tisdag"/>
        <input type="submit" name="skickadatum" value="Onsdag"/>
        <input type="submit" name="skickadatum" value="Torsdag"/>
        <input type="submit" name="skickadatum" value="Fredag"/>
        <input type="submit" name="skickadatum" value="Nästa Måndag"/>
        <input type="submit" name="skickadatum" value="Nästa Tisdag"/>
        <input type="submit" name="skickadatum" value="Nästa Onsdag"/>
        <input type="submit" name="skickadatum" value="Nästa Torsdag"/>
        <input type="submit" name="skickadatum" value="Nästa Fredag"/>
 </form>

    <?php
    //echo 'isset($_POST[skickadatum])||isset($_GET[akt])'.isset($_POST['skickadatum'])||isset($_GET['akt']).'<br>';
    //echo 'isset($_POST[skickadatum])'.isset($_POST['skickadatum']).'<br>';
 
    if(isset($_POST['skickadatum'])||isset($_GET['akt'])||isset($_SESSION['datumvisa'])){

        printRapport($db, $sokPaDatum && (!$sokPaPeriod), $sokPaPeriod, $sokPaArbUpg,$defaultDatum, $defaultTillDatum, $defaultArbUpg);

    }



?>
