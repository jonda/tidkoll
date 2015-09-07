


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
   <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
   <title>Tidkoll</title>
</head>

<?php
require_once 'db.php';
require_once 'ResultSet.php';

$db = new DB();
/*$sql="select id, namn as 'Kurs/Arbetsuppgift' from arbuppgift order by id";
$res = $db->query($sql);
$res->drawHTMLTable();
echo "<br>";
$sql="select id, namn as Aktivitetstyp from aktivitetstyp";
$res = $db->query($sql);
$res->drawHTMLTable();
*/
if(isset($_POST['datum'])){
    if(isset($_POST['datum'])){
        $aktDatum = $_POST['datum'];
    }
    else {
        $sql="select curdate()";

        //Fixar datum till formuläret nedan
        $res =$db->query($sql);
        $row = $res->fetchRow();
        $date =  $row[0];
        $aktDatum = $date;
    }
}

?>
<form method="POST" action="rapportdatum.php">
Datum:   <input type="text" name="datum" value="<?php echo $aktDatum?>"/><br><br>
<input type="submit" name="skicka" value="Skicka in"/>
</form>

<?php
if(isset($_POST['skicka'])){
    $sql = "SELECT aktivitet.id, arbuppgift.namn as 'Kurs/Arbetsuppgift', aktivitetstyp.namn as Aktivitetstyp, langd as Längd, datum as Datum, kommentar as Kommentar FROM aktivitet inner join arbuppgift on arbuppgift.id=aktivitet.arbuppgift inner join aktivitetstyp on aktivitetstyp.id=aktivitet.aktivitetstyp where datum='$aktDatum'";
    //echo $sql;
    $res = $db->query($sql);
    $res->drawHTMLTableWithLink("redigeraaktivitet.php?akt=");
    echo "$aktDatum<br>";
    $sql = "SELECT sum(langd)/60 as 'Summa längd $aktDatum i timmar' FROM aktivitet where datum='$aktDatum'";
    //echo $sql;
    $res = $db->query($sql);
    $res->drawHTMLTable();
    echo "<br>";
    $sql = "SELECT sum(langd) as 'Summa längd $aktDatum i minuter' FROM aktivitet where datum='$aktDatum'";
    $res = $db->query($sql);
    $res->drawHTMLTable();
}
?>

