<?php
header('Content-type: text/html; charset=ISO-8859-1'); 
require_once 'db.php';
require_once 'ResultSet.php';
//echo "Hej alla barn";
$akt = $_GET['akt'];
$db = new DB();
$sql= "select defaultakttyp from arbuppgift where id=$akt";
$aktTyp=$db->getSingleValue($sql);
$sql= "select kategori from arbuppgift where id=$akt";
$kategori=$db->getSingleValue($sql);
$where = "kategori=0 or kategori=$kategori";
//echo "setAktSkript there: $where <br>";

$db->QueryAndGenereateSelectList("aktivitetstyp","aktivitetstyp", "id", "namn", $aktTyp,null,  $where)
?>
