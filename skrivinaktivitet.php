<?php session_start() ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
   <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
   <title>Tidkoll</title>
   <link rel="stylesheet" type="text/css" href="tidkollstyle.css" />

</head>
<div class="navigering">
<a href="tidkoll.php"> Redigera/Rapporter </a> &nbsp; &nbsp;<a href="rapportsida.php">Rapport</a>
</div>
<?php
require_once 'db.php';
require_once 'ResultSet.php';
require_once 'tidkollrapporter.php';


$db = new DB();
/*$sql="select id, namn as 'Kurs/Arbetsuppgift' from arbuppgift order by id";
$res = $db->query($sql);
$res->drawHTMLTable();
echo "<br>";
$sql="select id, namn as Aktivitetstyp from aktivitetstyp";
$res = $db->query($sql);
$res->drawHTMLTable();
*/
$sql="select curdate()";

//Fixar datum till formuläret nedan
$res =$db->query($sql);
$row = $res->fetchRow();
$date =  $row[0];

$db->query("SET lc_time_names = 'sv_SE';");

   $defaultArbUpg = 1;
    $defaultAktTyp = 1;
    $defaultLangd = "";
    $defaultKommentar = "";
    $defaultSparaVarden = "";
    $defaultDatum = $date;
    $defaultOkaDatum = "";
    $defaultRapEfterArbUpg = "";

if(isset($_POST['sparavarden'])){
    $defaultArbUpg = $_POST['arbuppgift'];
    $defaultAktTyp = $_POST['aktivitetstyp'];
    $defaultLangd = $_POST['langd'];
    $defaultDatum = $_POST['datum'];
    $defaultKommentar = $_POST['kommentar'];
    if(isset($_POST['okadatum'])){
        $defaultDatum = date("Y-m-d", strtotime( $defaultDatum )+60*60*24*7 );
    }

    if(isset($_POST['okadatum'])){
        $defaultOkaDatum = "checked='checked'";
    }
    if(isset($_POST['sparavarden'])){
        $defaultSparaVarden = "checked='checked'";
    }
   if(isset($_POST['rapefterarbupg'])){
        $defaultRapEfterArbUpg = "checked='checked'";
    }

/*    else {
    $defaultArbUpg = 1;
    $defaultAktTyp = 1;
    $defaultLangd = "";
    $defaultKommentar = "";
    $defaultSparaVarden = "";

    }*/
}
?>
<br>
<fieldset>
<legend> Ny aktivitet </legend>

<form method="POST" action="skrivinaktivitet.php">
<p><label for="arbuppgift">Kurs/Arbetsuppgift: </label>   <?php $db->QueryAndGenereateSelectList("arbuppgift","arbuppgift", "id", "namn", $defaultArbUpg, "setAktivitet(this.value)")?></p>
<p><label for="aktivitetstyp">Uppgiftstyp:</label>    <div id="aktivitetstypdiv"><?php $db->QueryAndGenereateSelectList("aktivitetstyp","aktivitetstyp", "id", "namn", $defaultAktTyp) ?></div></p>
<p><label for="langd">Längd:</label>   <input type="text" name="langd" id="langd" value="<?php echo $defaultLangd ?>"/></p>
<p><label for="datum">Datum:</label> <input type="text" name="datum" id="datum" value="<?php echo $defaultDatum?>"/></p>
<p><label for="kommentar">Kommentar:   </label><br><textarea rows="10" cols="80" name="kommentar" id="kommentar"><?php echo $defaultKommentar ?></textarea></p>
Spara värden till nästa gång: <input type="checkbox" name="sparavarden" id="sparavarden" <?php echo $defaultSparaVarden ?> >
Öka datum med sju nästa gång: <input type="checkbox" name="okadatum" <?php echo $defaultOkaDatum ?> ><br>
Rapport efter Arbetsuppgift: <input type="checkbox" name="rapefterarbupg" <?php echo $defaultRapEfterArbUpg ?> ><br>
<input type="submit" name="Skicka" value="Skicka in"/>
</form>
</fieldset>
<script type="text/javascript">
function setAktivitet(akt)
{
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.open("GET","setAktScript.php?akt="+akt,false);
    xmlhttp.send();
    var serverResponse = xmlhttp.responseText;
    var res =xmlhttp.responseText;

    document.getElementById("aktivitetstypdiv").innerHTML=res;

}</script>


<?php
if(isset($_POST['Skicka']))
{
    $arbetsuppgift = 0;
    $arbetsuppgift = $_POST['arbuppgift'];
    $aktivitetstyp = $_POST['aktivitetstyp'];
    $langd=$_POST['langd'];
    $datum=$_POST['datum'];
    $kommentar=$_POST['kommentar'];
    $sql="insert into aktivitet (arbuppgift, aktivitetstyp, langd, datum, kommentar) values ('$arbetsuppgift', '$aktivitetstyp', '$langd', '$datum', '$kommentar'); ";
    if($db->query($sql)){
        echo "Det gick bra";

    }
    else {
        echo "Det gick dåligt";
    }
    
    }
    if(isset($_POST['datum'])){
        $aktDatum = $_POST['datum'];
    }
    else {
        $aktDatum = $date;
    }
    if(!isset($arbetsuppgift)){
    $arbetsuppgift = 0;
    }
    if($defaultRapEfterArbUpg==""){
        echo "<h2>$aktDatum:</h2>";
    }
    
   printRapport($db,$defaultRapEfterArbUpg=="", false, $defaultRapEfterArbUpg!="", $aktDatum, null, $arbetsuppgift);

?>

