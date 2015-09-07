<?php
require_once "View.php";
require_once 'anteckningfunktioner.php';
class SkrivInView extends View {

    private $radPost;
    private $kopieraAktivitet = null;

    public function __construct($db, $kop=0) {
        parent::__construct($db);
        $this->kopieraAktivitet = $kop;
        trace ("Skrivin konstruktor this->kopieraAktivitet: $this->kopieraAktivitet <br>\n");
    }

    public function getName() {
        return "Lätt till ny aktivitet";
    }

    public function process() {
        if (isset($_POST['Skicka'])) {
            $arbetsuppgift = 0;
            $arbetsuppgift = $_POST['arbuppgift'];
            $aktivitetstyp = $_POST['aktivitetstyp'];
            $langd = $_POST['langd'];
            $stalltid = $_POST['stalltid'];
            $datum = $_POST['datum'];
            $rubrik = $_POST['rubrik'];
            $kommentar = $_POST['kommentar'];
            $sql = "insert into aktivitet (arbuppgift, aktivitetstyp, langd, stalltid, datum,rubrik, kommentar) values ('$arbetsuppgift', '$aktivitetstyp', '$langd','$stalltid', '$datum', '$rubrik','$kommentar'); ";
            if ($this->db->query($sql)) {
                trace("Det gick bra");
                $aktid=mysql_insert_id();
            } else {
                trace("Det gick dåligt");
            }
            $aktivitet = mysql_insert_id();
            //Fixar allt som har med anteckning att göra
//            if(isset($_POST['nyanteckning']))
 /*           if(isset($_POST['anteckning'])){
                if($_POST['anteckning']!=''){
                    echo "Ny ska nyAnteckning anropas!!";
                    nyAnteckning($this->db, $aktid,'' ,$_POST['anteckning']);

                }
            }else {
                trace ("Ingen ny anteckning");
            }
*/
            if(isset($_POST['nyanteckning'])){
                return new NyAntView($this->db, $aktivitet);
            }

        }
        if (isset($_POST['Kasta'])) {
            return null;
        }
        if (isset($_POST['Skicka'])) {
            if ($_POST['Skicka']=="Skicka in") {
                return null;
            }
            if ($_POST['Skicka']=="Skicka in och fortsätt redigera") {
                return new RedigeraView($this->db, $aktivitet);
            }

        }
        return $this;

    }

    public function showUI($datum="") {
        echo "SkrivViewShowUI datum".$datum;
        $defaultArbUpg = 1;
        $defaultAktTyp = 1;
        $defaultLangd = "";
        $defaultStallTid = "";
        $defaultRubrik = "";
        $defaultKommentar = "";
        $defaultSparaVarden = "";
        $defaultDatum = date('Y-m-d');
        $defaultOkaDatum = "";
        $defaultRapEfterArbUpg = "";
        if($datum!=""){
            $defaultDatum = $datum;
        }

        if ($this->kopieraAktivitet != 0) {
            $sql = "select * from aktivitet where id=$this->kopieraAktivitet";
            $res = $this->db->query($sql);
            $row = $res->fetchAssoc();
            $defaultArbUpg = $row['arbuppgift'];
            $defaultAktTyp = $row['aktivitetstyp'];
            $defaultLangd = $row['langd'];
            $defaultStallTid = $row['stalltid'];
            $defaultDatum = $row['datum'];
            $defaultRubrik = $row['rubrik'];
            $defaultKommentar = $row['kommentar'];
        }
       if (isset($_POST['sparavarden'])) {
            $defaultArbUpg = $_POST['arbuppgift'];
            $defaultAktTyp = $_POST['aktivitetstyp'];
            $defaultLangd = $_POST['langd'];
            $defaultStallTid = $_POST['stalltid'];
            $defaultDatum = $_POST['datum'];
            $defaultRubrik = $_POST['rubrik'];
            $defaultKommentar = $_POST['kommentar'];
            if (isset($_POST['okadatum'])) {
                $defaultDatum = date("Y-m-d", strtotime($defaultDatum) + 60 * 60 * 24 * 7);
            }

            if (isset($_POST['okadatum'])) {
                $defaultOkaDatum = "checked='checked'";
            }
            if (isset($_POST['sparavarden'])) {
                $defaultSparaVarden = "checked='checked'";
            }
            if (isset($_POST['rapefterarbupg'])) {
                $defaultRapEfterArbUpg = "checked='checked'";
            }
        }

 ?>
        <br>
        <fieldset>
            <legend> Ny aktivitet </legend>

            <form method="POST" action="tidkoll.php">
                <p><label for="arbuppgift">Kurs/Arbetsuppgift: </label>   <?php $this->db->QueryAndGenereateSelectList("arbuppgift", "arbuppgift", "id", "namn", $defaultArbUpg, "setAktivitet(this.value)") ?></p>
                <p><label for="aktivitetstyp">Uppgiftstyp:</label>    <div id="aktivitetstypdiv"><?php $this->db->QueryAndGenereateSelectList("aktivitetstyp", "aktivitetstyp", "id", "namn", $defaultAktTyp) ?></div></p>
                <p><label for="langd">Längd:</label>   <input type="text" name="langd" id="langd" value="<?php echo $defaultLangd ?>"/> 
				 <input type="button" value="30min" onclick="document.getElementById('langd').value='30'" >
				 <input type="button" value="60min" onclick="document.getElementById('langd').value='60'" >
				 <input type="button" value="75min" onclick="document.getElementById('langd').value='75'" >
				 <input type="button" value="90min" onclick="document.getElementById('langd').value='90'" >
				 <input type="button" value="120min" onclick="document.getElementById('langd').value='120'" >
				 <input type="button" value="160min" onclick="document.getElementById('langd').value='160'" >
				</p>
                <p><label for="stalltid">Ställtid:</label>   <input type="text" name="stalltid" id="stalltid" value="<?php echo $defaultStallTid ?>"/>
				<input type="button" value="15min" onclick="document.getElementById('stalltid').value='15'" >

                </p>
                <p><label for="datum">Datum:</label> <input type="text" name="datum" id="datum" value="<?php echo $defaultDatum ?>"/></p>
                <p><label for="rubrik">Rubrik:   </label><br><textarea rows="2" cols="80" name="rubrik" id="rubrik"><?php echo $defaultRubrik ?></textarea></p>
                <p><label for="kommentar">Kommentar:   </label><br><textarea rows="10" cols="80" name="kommentar" id="kommentar"><?php echo $defaultKommentar ?></textarea></p>
                Skapa ny anteckning: <input type="checkbox" name="nyanteckning" id="nyanteckning"  ><br/>
                Spara värden till nästa gång: <input type="checkbox" name="sparavarden" id="sparavarden" <?php echo $defaultSparaVarden ?> >
                Öka datum med sju nästa gång: <input type="checkbox" name="okadatum" <?php echo $defaultOkaDatum ?> >
                Rapport efter Arbetsuppgift: <input type="checkbox" name="rapefterarbupg" <?php echo $defaultRapEfterArbUpg ?> ><br>
                <input type="submit" name="Skicka" value="Skicka in"/>
                <input type="submit" name="Skicka" value="Skicka in och lägg till fler"/>
                <input type="submit" name="Skicka" value="Skicka in och fortsätt redigera"/>
                <input type="submit" name="Kasta" value="Kasta"/>
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
    }

}
?>





