<?php

function uppdateraAktivitet($db, $currAktivitet, $arbetsuppgift, $aktivitetstyp, $langd,$stalltid, $datum, $rubrik, $kommentar){
    $sql="update aktivitet set arbuppgift='$arbetsuppgift', aktivitetstyp='$aktivitetstyp', langd='$langd',stalltid='$stalltid', datum='$datum', rubrik='$rubrik',kommentar='$kommentar' where aktivitet.id=$currAktivitet";
    echo $sql;
    if($db->query($sql)){
        echo "Posten är uppdaterad";

    }
    else {
        echo "Det gick dåligt";
    }
}
function visaRaderingsInfo($db, $radPost){
    $sql = "SELECT aktivitet.id, arbuppgift.namn as 'Kurs/Arbetsuppgift', aktivitetstyp.namn as Aktivitetstyp, langd as Längd,date_format(datum, '%a V%u') as dag, datum as Datum, rubrik, kommentar FROM aktivitet inner join arbuppgift on arbuppgift.id=aktivitet.arbuppgift inner join aktivitetstyp on aktivitetstyp.id=aktivitet.aktivitetstyp where aktivitet.id=$radPost";
    $res = $db->query($sql);
    $res->drawHTMLTable();

}

function raderaAktivitet($db, $radPost){
    $sql = "delete from aktivitet where id=$radPost";
    echo $sql;
    if($db->query($sql)){
        echo "<br>Posten är borttagen <br>";

    }
    else {
        echo "Det gick dåligt";
    }
}

?>
