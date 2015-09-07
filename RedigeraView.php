<?php
require_once 'View.php';

class RedigeraView extends View {

    private $aktivitet = null;

    public function RedigeraView($db, $akt){
        parent::__construct($db);
        $this->aktivitet=$akt;
        //echo "konstruktorn RedigerView<br/>";
    }

    public function getName() {
        return "Redigera";
    }

    public function process() {
        $this->trace("RedigerView process <br>\n");
        $i = 0;
        while (isset($_POST['antid' . $i])) {
            if (isset($_POST['redant' . $i])) {
                $antid = $_POST['antid' . $i];
                return new RedAntView($this->db, $antid);
            }
            $i++;
        }
        if (isset($_POST['nyant'])) {
            return new NyAntView($this->db, $this->aktivitet);
        } elseif (isset($_POST['kopplaant'])) {
            kopplaAnt($this->db, $_POST['antkoppl'], $this->aktivitet);
        }

        if (isset($_POST['skickauppdatering'])) {
            uppdateraAktivitet($this->db, $this->aktivitet, $_POST['arbuppgift'], $_POST['aktivitetstyp'], $_POST['langd'], $_POST['stalltid'],
                    $_POST['datum'], $_POST['rubrik'], $_POST['kommentar']);
            if ($_POST['skickauppdatering'] == "Spara och kopiera till ny") {
                return new SkrivInView($this->db, $this->aktivitet);
            } else if ($_POST['skickauppdatering'] == "Spara") {
                return null;
            }
        }

        if (isset($_POST['kasta'])) {
            return null;
        }
        return $this;
    }

    public function showUI() {


       // echo '<a href="tidkoll.php"> Kasta bort ändringar och visa enbart rapport </a>';

        /*if ($this->aktivitet == null) {
            $this->aktivitet = $_GET['akt'];
        }*/
// Om man har skickat in en uppdatering


        $sql = "select * from aktivitet where id=$this->aktivitet";
        $res = $this->db->query($sql);
        $row = $res->fetchAssoc();
        $aktPostDatum = $row['datum'];
        $aktivitetstyp = $row['aktivitetstyp'];
        $arbuppgift = $row['arbuppgift'];

        $sql = "select anteckning.id,anteckning.rubrik, innehall from anteckning inner join anttillakt on anttillakt.anteckning=anteckning.id where anttillakt.aktivitet=$this->aktivitet";
        $antres = $this->db->query($sql);
?>
        <br>
        <br>
        <fieldset>
            <legend> Redigera aktivitet </legend>
            <form id="myform" method="POST" action="tidkoll.php">
                <label for="arbuppgift">    Kurs/Arbetsuppgift:</label>    <?php $this->db->QueryAndGenereateSelectList("arbuppgift", "arbuppgift", "id", "namn", $arbuppgift, "setAktivitet(this.value)") ?><br><br>
                <label for="aktivitetstyp">    Uppgiftstyp:   </label> <div id="aktivitetstypdiv"><?php $this->db->QueryAndGenereateSelectList("aktivitetstyp", "aktivitetstyp", "id", "namn", $aktivitetstyp) ?></div><br>
                <label for="langd">    Längd:   </label> <input type="text" name="langd" id="langd" value="<?php echo $row['langd'] ?>"/><br><br>
                <label for="stalltid">    Ställtid:   </label> <input type="text" name="stalltid" id="stalltid" value="<?php echo $row['stalltid'] ?>"/><br><br>
                <label for="datum">    Datum:   </label><input type="text" name="datum" id="datum" value="<?php echo $aktPostDatum ?>"/><br><br>
                <label for="rubrik">    Rubrik:   </label><br><textarea rows="2" cols="80" name="rubrik" id="rubrik"><?php echo$row['rubrik']; ?></textarea>
                <label for="kommentar">    Kommentar:   </label><br><textarea rows="10" cols="80" name="kommentar" id="kommentar"><?php echo$row['kommentar']; ?></textarea>
<?php
        $i = 0;
        while (($antrow = $antres->fetchRow()) != null) {
            echo "<h3>Anteckning:</h3>\n";
            $anteckning = $antrow[2];
            echo $anteckning;

            echo "<input type='submit' name='redant$i' value='Redigera anteckning'/><br/>\n";
            echo "<input type='hidden' name='antid$i' value='$antrow[0]'>\n";
            $i++;
        }
        //else {
        {
            $where = "anteckning.id=anttillakt.anteckning and anttillakt.aktivitet=aktivitet.id and aktivitet.arbuppgift=$arbuppgift";
            $this->db->QueryAndGenereateSelectList("antkoppl", "anteckning, anttillakt, aktivitet", "anteckning.id", "anteckning.rubrik", 1, null, $where);
            echo '<input type="submit" name="kopplaant" value="Koppla anteckning"/>';
            echo '<input type="submit" name="nyant" value="Ny anteckning"/>';
        }
?>
        <br/>
        <input type="submit" name="skickauppdatering" value="Spara"/>
        <input type="submit" name="skickauppdatering" value="Spara och fortsätt redigera"/>
        <input type="submit" name="skickauppdatering" value="Spara och kopiera till ny"/>
        <input type="submit" name="kasta" value="Kasta"/>
    </form>
</fieldset>
<br>
<fieldset style="width: 14em">
    <form method="POST" action="tidkoll.php?rad=<?php echo $this->aktivitet; ?>">
        Radera posten ovan?  <input type="submit" name="radera" value="Radera"/>
</fieldset>
</form>
<div id="javascripttest"> Denna text ska ändra </div>hej ehj

<script type="text/javascript">
    document.write("This is my first JavaScript!");

    function aktivitetCallBack(){
        var serverResponse = xhReq.responseText;
        alert(serverResponse);
        document.getElementById("javascripttest").innerHTML="aktivitetCallBack:Akt har värdet skripter har körts: " + akt + "<br>";

    }

    function setAktivitet(akt)
    {
        //document.write("Hej nu kördes setAktivitet");
        //objXMLHttp=new XMLHttpRequest();

        //document.write("Akt har värdet: " + akt);
        //document.getElementById("javascripttest").innerHTML="Akt har värdet: " + akt + "<br>";
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = aktivitetCallBack;
        /*   xmlhttp.onreadystatechange=function()
  {
        if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
    document.getElementById("javascripttest").innerHTML="Akt har värdet skripter har körts: " + akt + "<br>";
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


    }

    function mysave()
    {
        document.forms["myform"].submit();
    }

</script>
<?php
    }

}
?>
