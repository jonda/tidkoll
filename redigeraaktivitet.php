<?php session_start();
?>

<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <title>Tidkoll</title>
    <link rel="stylesheet" type="text/css" href="tidkollstyle.css" />
</head>
<div class="navigering">
<a href="skrivinaktivitet.php"> L�gg till aktivitet </a> &nbsp; &nbsp;<a href="rapportsida.php"> Rapport </a>  <br>
</div>
<?php
require_once 'db.php';
require_once 'ResultSet.php';
require_once 'tidkollutils.php';
require_once 'redigeraaktivitetfunktioner.php';
require_once 'tidkollrapporter.php';

//echo "veckodag: ".date("Y-m-d l w", strtotime("2010-09-26"));

$db = new DB();

$sql="select curdate()";

//Fixar datum till formul�ret nedan
$res =$db->query($sql);
$row = $res->fetchRow();
$dateToday =  $row[0];
$updateDatum = null;
$aktPostDatum = null;

$db->query("SET lc_time_names = 'sv_SE';");


if(isset($_SESSION['radera'])){
    echo "Radera �r satt<br>";
        if($_SESSION['radera']!=null){
            if(isset($_POST['raderaja'])){
                raderaAktivitet($db, $_SESSION['radera']);
            }
        }
    }

    //Om man tryckt radera
if(isset($_GET['rad'])){
    $radPost=$_GET['rad'];
    echo '$radPost:'.$radPost.'<br>';
    echo "<fieldset>\n";
    echo "<legend>�r du s�ker p� att du vill ta bort f�ljande post?</legend>";
    echo "<div style='background: white; border:1px solid'>";
    echo "<h2>�r du s�ker p� att du vill ta bort f�ljande post?</h2>";
    visaRaderingsInfo($db, $radPost);
    echo "<br></div>";
    $_SESSION['radera']=$radPost;
    ?>
<br>

<form method="POST" action="redigeraaktivitet.php">
<input type="submit" name="raderaja" value="Ja"/>
<input type="submit" name="raderanej" value="Nej"/>
</form>
</fieldset>
<br>

<?php
}
else {
$_SESSION['radera']=null;
}
//Slut p� radera




//F�rsta delen som visas om mankommit hit fr�n en l�nk med en post
if(isset($_GET['akt'])){
echo '<a href="redigeraaktivitet.php"> Kasta bort �ndringar och visa enbart rapport </a>';
//echo "akt �r satt!!!!!<br>\n";
$currAktivitet = $_GET['akt'];
// Om man har skickat in en uppdatering
if(isset($_POST['skickauppdatering']))
{
    uppdateraAktivitet($db, $currAktivitet, $_POST['arbuppgift'], $_POST['aktivitetstyp'], $_POST['langd'],
        $_POST['datum'], $_POST['kommentar']);
}


$sql="select * from aktivitet where id=$currAktivitet";
$res=$db->query($sql);
$row = $res->fetchAssoc();
$aktPostDatum = $row['datum'];
?>
<br>
<br>
<fieldset>
<legend> Redigera aktivitet </legend>
<form method="POST" action="redigeraaktivitet.php?akt=<?php echo $currAktivitet; ?>">
<label for="arbuppgift">    Kurs/Arbetsuppgift:</label>    <?php $db->QueryAndGenereateSelectList("arbuppgift","arbuppgift", "id", "namn", $row['arbuppgift'], "setAktivitet(this.value)")?><br><br>
<label for="aktivitetstyp">    Uppgiftstyp:   </label> <div id="aktivitetstypdiv"><?php $db->QueryAndGenereateSelectList("aktivitetstyp","aktivitetstyp", "id", "namn", $row['aktivitetstyp']) ?></div><br>
<label for="langd">    L�ngd:   </label> <input type="text" name="langd" id="langd" value="<?php echo $row['langd']?>"/><br><br>
<label for="datum">    Datum:   </label><input type="text" name="datum" id="datum" value="<?php echo $aktPostDatum?>"/><br><br>
<label for="kommentar">    Kommentar:   </label><br><textarea rows="10" cols="80" name="kommentar" id="kommentar"><?php echo$row['kommentar'];?></textarea>
    <input type="submit" name="skickauppdatering" value="Spara"/>
</form>
    </fieldset>
    <br>
    <fieldset style="width: 14em">
<form method="POST" action="redigeraaktivitet.php?rad=<?php echo $currAktivitet; ?>">
    Radera posten ovan?  <input type="submit" name="radera" value="Radera"/>
    </fieldset>
</form>
<div id="javascripttest"> Denna text ska �ndra </div>hej ehj

<script type="text/javascript">
document.write("This is my first JavaScript!");

function aktivitetCallBack(){
     var serverResponse = xhReq.responseText;
    alert(serverResponse);
    document.getElementById("javascripttest").innerHTML="aktivitetCallBack:Akt har v�rdet skripter har k�rts: " + akt + "<br>";
    
}

function setAktivitet(akt)
{
    //document.write("Hej nu k�rdes setAktivitet");
    //objXMLHttp=new XMLHttpRequest();
    
    //document.write("Akt har v�rdet: " + akt);
    //document.getElementById("javascripttest").innerHTML="Akt har v�rdet: " + akt + "<br>";
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = aktivitetCallBack;
/*   xmlhttp.onreadystatechange=function()
  {
        if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
    document.getElementById("javascripttest").innerHTML="Akt har v�rdet skripter har k�rts: " + akt + "<br>";
    document.getElementById("aktivitetstypdiv").innerHTML=xmlhttp.responseText;
    //document.write(xmlhttp.responseText);
    }
  }*/
xmlhttp.open("GET","setAktScript.php?akt="+akt,false);
//xmlhttp.open("GET","setAktScript.php",false);
xmlhttp.send();
var serverResponse = xmlhttp.responseText;
//alert("setAktScript.php?akt="+akt);
//    alert(serverResponse);
var res =xmlhttp.responseText;

document.getElementById("aktivitetstypdiv").innerHTML=res;


}</script>


<?php




}
// slut p� del som visas om variabeln akt �r satt




$defaultSokPaDatum = "";
$defaultSokPaArbUpg = '';
$sokPaArbUpg = false;
$sokPaDatum = false;
if(isset($_POST['skickadatum'])){


$defaultDatum = corrDate($_POST['datumvisa'],0);
if(substr($_POST['datumvisa'],0,1)=="v" && $_POST['tilldatumvisa']=="")
{
    $defaultTillDatum = corrDate($_POST['datumvisa'],6);
}
else
{
    $defaultTillDatum = corrDate($_POST['tilldatumvisa'],6);
}
$defaultArbUpg = $_POST['arbupgsok'];
$_SESSION['datumvisa'] = $defaultDatum;
$_SESSION['tilldatumvisa'] = $defaultTillDatum;
$_SESSION['arbupgsok'] = $_POST['arbupgsok'];

if(isset($_POST['sokpadatum'])){
    $sokPaDatum = true;

}
if(isset($_POST['sokpaarbupg'])){
    $sokPaArbUpg = true;
}
$_SESSION['sokPaDatum'] = $sokPaDatum;
$_SESSION['sokPaArbUpg'] = $sokPaArbUpg;

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
echo '$defaultSokPaArbUpg: '. $defaultSokPaDatum."<br>";
}
*/
}
if($sokPaDatum){
$defaultSokPaDatum = "checked='checked'";
//echo '$defaultSokPaDatum: '. $defaultSokPaDatum."<br>";
}
if($sokPaArbUpg){
$defaultSokPaArbUpg = "checked='checked'";
//echo '$defaultSokPaArbUpg: '. $defaultSokPaArbUpg."<br>";
}

$sokPaPeriod=false;
if($sokPaDatum){
if($defaultTillDatum!=""){
    $sokPaPeriod=true;
    //echo "Nu s�ker vi p� period";
}
/*    if(isset($_POST['tilldatumvisa'])){
if($_POST['tilldatumvisa']!=""){
    $sokPaPeriod=true;
    //echo "Nu s�ker vi p� period";
}
}
elseif(isset($_SESSION['tilldatumvisa'])){
if($_SESSION['tilldatumvisa']!=""){
    $sokPaPeriod=true;
    //echo "Nu s�ker vi p� period";
}
}*/

}
?>
<br>
<form method="POST" action="redigeraaktivitet.php">
    Datum:
    <input type="checkbox" name="sokpadatum" value="1" <?php echo $defaultSokPaDatum ?>>
    <input type="text" name="datumvisa" value="<?php echo $defaultDatum?>"/>
    <input type="text" name="tilldatumvisa" value="<?php echo $defaultTillDatum?>"/>
    Kurs/Arbetsuppgift:
    <input type="checkbox" name="sokpaarbupg" value="1" <?php echo $defaultSokPaArbUpg ?>>
    <?php $db->QueryAndGenereateSelectList("arbupgsok", "arbuppgift", "id", "namn", $defaultArbUpg)  ?>
    <input type="submit" name="skickadatum" value="Visa"/>
</form>

    <?php
    //echo 'isset($_POST[skickadatum])||isset($_GET[akt])'.isset($_POST['skickadatum'])||isset($_GET['akt']).'<br>';
    //echo 'isset($_POST[skickadatum])'.isset($_POST['skickadatum']).'<br>';
    if(isset($_POST['skickadatum'])||isset($_GET['akt'])||isset($_SESSION['datumvisa'])){

        printRapport($db, $sokPaDatum && (!$sokPaPeriod), $sokPaPeriod, $sokPaArbUpg,$defaultDatum, $defaultTillDatum, $defaultArbUpg);

    }



    ?>

