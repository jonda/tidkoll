<?php
require_once "View.php";
require_once 'anteckningfunktioner.php';
class SkrivInBudgetView extends View {

    private $radPost;
    private $kopieraAktivitet = null;

    public function __construct($db, $kop=0) {
        parent::__construct($db);
        $this->kopieraAktivitet = $kop;
        trace ("Skrivin konstruktor this->kopieraAktivitet: $this->kopieraAktivitet <br>\n");
    }

    public function getName() {
        return "Lätt till ny budgetpost";
    }

    public function process() {
        if (isset($_POST['Skicka'])) {
            $arbetsuppgift = 0;
            $arbetsuppgift = $_POST['arbuppgift'];
            $aktivitetstyp = $_POST['aktivitetstyp'];
            $timmar = $_POST['timmar'];
            $kommentar = $_POST['kommentar'];
            $sql = "insert into budget (arbuppgift, aktivitetstyp, timmar, kommentar) values ('$arbetsuppgift', '$aktivitetstyp', '$timmar','$kommentar'); ";
            if ($this->db->query($sql)) {
                trace("Det gick bra");
                $aktid=mysql_insert_id();
            } else {
                trace("Det gick dåligt");
            }

        }
        if (isset($_POST['Kasta'])) {
            return null;
        }
        return $this;

    }

    public function showUI() {
        $defaultArbUpg = 1;
        $defaultAktTyp = 1;
        $defaulttimmar = "";
        $defaultKommentar = "";
        $defaultSparaVarden = "";

        if (isset($_POST['sparavarden'])) {
            $defaultArbUpg = $_POST['arbuppgift'];
            $defaultAktTyp = $_POST['aktivitetstyp'];
            $defaulttimmar = $_POST['timmar'];
            $defaultKommentar = $_POST['kommentar'];
            if (isset($_POST['sparavarden'])) {
                $defaultSparaVarden = "checked='checked'";
            }
        }

        if ($this->kopieraAktivitet != 0) {
            $sql = "select * from budget where id=$this->kopieraAktivitet";
            $res = $this->db->query($sql);
            $row = $res->fetchAssoc();
            $defaultArbUpg = $row['arbuppgift'];
            $defaultAktTyp = $row['aktivitetstyp'];
            $defaulttimmar = $row['timmar'];
            $defaultKommentar = $row['kommentar'];
        }
?>
        <br>
        <fieldset>
            <legend> Ny budgetpost </legend>

            <form method="POST" action="budget.php">
                <p><label for="arbuppgift">Kurs/Arbetsuppgift: </label>   <?php $this->db->QueryAndGenereateSelectList("arbuppgift", "arbuppgift", "id", "namn", $defaultArbUpg, "setAktivitet(this.value)") ?></p>
                <p><label for="aktivitetstyp">Uppgiftstyp:</label>    <div id="aktivitetstypdiv"><?php $this->db->QueryAndGenereateSelectList("aktivitetstyp", "aktivitetstyp", "id", "namn", $defaultAktTyp) ?></div></p>
                <p><label for="timmar">Längd:</label>   <input type="text" name="timmar" id="timmar" value="<?php echo $defaulttimmar ?>"/></p>
                <p><label for="kommentar">Kommentar:   </label><br><textarea rows="10" cols="80" name="kommentar" id="kommentar"><?php echo $defaultKommentar ?></textarea></p>
                 Spara värden till nästa gång: <input type="checkbox" name="sparavarden" id="sparavarden" <?php echo $defaultSparaVarden ?> >
                <input type="submit" name="Skicka" value="Skicka in"/>
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





