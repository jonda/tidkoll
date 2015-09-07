<?php
require_once 'db.php';
require_once 'ResultSet.php';

$db = new DB();
$sql="select id, namn as 'Kurs/Arbetsuppgift' from arbuppgift order by id";
$res = $db->query($sql);
$res->drawHTMLTable();
echo "<br>";
$sql="select id, namn as Aktivitetstyp from aktivitetstyp";
$res = $db->query($sql);
$res->drawHTMLTable();

$sql="select curdate()";

//Fixar datum till formuläret nedan
$res =$db->query($sql);
$row = $res->fetchRow();
$date =  $row[0];

?>
<form method="POST" action="skrivinaktivitet.php">
<br>Kurs/Arbetsuppgift:    <input type="text" name="arbetsuppgift"/><br><br>
<br>Kurs/Arbetsuppgift:    <?php $db->QueryAndGenereateSelectList("arbuppgift", "id", "namn")?><br><br>
Uppgiftstyp:    <input type="text" name="aktivitetstyp"/><br><br>
Längd:    <input type="text" name="langd"/><br><br>
Datum:   <input type="text" name="datum" value="<?php echo $date?>"/><br><br>
<input type="submit" name="Skicka" value="Skicka in"/>
</form>

<?php
if(isset($_POST['arbetsuppgift'])){
    $arbetsuppgift = $_POST['arbuppgift'];
    $aktivitetstyp = $_POST['aktivitetstyp'];
    $langd=$_POST['langd'];
    $datum=$_POST['datum'];
    $sql="insert into aktivitet (arbuppgift, aktivitetstyp, langd, datum) values ('$arbetsuppgift', '$aktivitetstyp', '$langd', '$datum'); ";
    if($db->query($sql)){
        echo "Det gick bra";

    }
    else {
        echo "Det gick dåligt";
    }
}
    $sql = 'SELECT arbuppgift.namn as "Kurs/Arbetsuppgift", aktivitetstyp.namn as Aktivitetstyp, langd as Längd, datum as Datum FROM `aktivitet` inner join arbuppgift on arbuppgift.id=aktivitet.arbuppgift inner join aktivitetstyp on aktivitetstyp.id=aktivitet.aktivitetstyp';
    $res = $db->query($sql);
    $res->drawHTMLTable();
?>

