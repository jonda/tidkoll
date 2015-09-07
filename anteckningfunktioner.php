<?php

function nyAnteckning($db, $aktid,$anttyp, $rubrik, $anteckning) {
    if($rubrik==''){
        $rubrik=strip_tags(substr($anteckning, 0, 20));
    }
    $sql = "insert into anteckning (anttyp, rubrik, innehall) values ('$anttyp','$rubrik','$anteckning'); ";
    if ($db->query($sql)) {
        $antid = mysql_insert_id();
        echo("Det gick bra att lägga till anteckning");
    } else {
        echo("Det gick dåligt att lägga till anteckning<br>");
    }

    $sql = "insert into anttillakt (anteckning, aktivitet) values ('$antid', '$aktid'); ";
    if ($db->query($sql)) {
        echo("Det gick bra");
    }
    else {
        echo("Det gick dåligt");
    }
}

function kopplaAnt($db, $antid, $aktid){
    $sql = "insert into anttillakt (anteckning, aktivitet) values ('$antid', '$aktid'); ";
    if ($db->query($sql)) {
        echo("Det gick bra");
    }
    else {
        echo("Det gick dåligt");
    }
    
}

function raderaAnteckning($db, $anteckning){
    $sql="delete from anteckning where id=$anteckning";
    if ($db->query($sql)) {
        echo("Antecnkningen togs bort");
    }
    else {
        echo("Anteckningen togs inte bort");
    }
    $sql="delete from anttillakt where anteckning=$anteckning";
    if ($db->query($sql)) {
        echo("Kopplingarna togs bort");
    }
    else {
        echo("Kopplingarna togs inte bort");
    }
    
}
?>
