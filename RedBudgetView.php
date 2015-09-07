<?php
require_once 'View.php';

class RedBudgetView extends View {

    private $bp = null;

    public function getName() {
        return "Redigera budgetpost";
    }

    public function process() {
        $this->trace("RedigeraBudgetView process <br>\n");
        if (isset($_POST['skickauppdatering'])) {
            $arbuppgift = $_POST['arbuppgift'];
            $aktivitetstyp =  $_POST['aktivitetstyp'];
            $timmar = $_POST['timmar'];
            $kommentar = $_POST['kommentar'];
            $sql = "update budget set  arbuppgift=$arbuppgift,  aktivitetstyp=$aktivitetstyp,  timmar=$timmar,  kommentar='$kommentar' where id= $this->bp";
            $this->db->query($sql);
        }
    }

    public function showUI() {


        echo '<a href="tidkoll.php"> Kasta bort ändringar och visa enbart rapport </a>';

        if ($this->bp == null) {
            $this->bp = $_GET['bp'];
        }
// Om man har skickat in en uppdatering


        $sql = "select * from budget where id=$this->bp";
        $res = $this->db->query($sql);
        $row = $res->fetchAssoc();
        $aktivitetstyp = $row['aktivitetstyp'];
        $arbuppgift = $row['arbuppgift'];
        $timmar= $row['timmar'];

?>
        <br>
        <br>
        <fieldset>
            <legend> Redigera aktivitet </legend>
            <form id="myform" method="POST" action="budget.php">
                <label for="arbuppgift">    Kurs/Arbetsuppgift:</label>    <?php $this->db->QueryAndGenereateSelectList("arbuppgift", "arbuppgift", "id", "namn", $arbuppgift, "setAktivitet(this.value)") ?><br><br>
                <label for="aktivitetstyp">    Uppgiftstyp:   </label> <div id="aktivitetstypdiv"><?php $this->db->QueryAndGenereateSelectList("aktivitetstyp", "aktivitetstyp", "id", "namn", $aktivitetstyp) ?></div><br>
                <label for="timmar">    Längd:   </label> <input type="text" name="timmar" id="timmar" value="<?php echo $timmar ?>"/><br><br>
                <label for="kommentar">    Kommentar:   </label><br><textarea rows="10" cols="80" name="kommentar" id="kommentar"><?php echo$row['kommentar']; ?></textarea>
        <input type="submit" name="skickauppdatering" value="Spara"/>
        <input type="submit" name="skickauppdatering" value="Spara och kopiera till ny"/>
    </form>
</fieldset>
<br>
<?php
/* <fieldset style="width: 14em">
    <form method="POST" action="budget.php?rad=<?php echo $this->aktivitet; ?>">
        Radera posten ovan?  <input type="submit" name="radera" value="Radera"/>
</fieldset>
</form>*/
?>
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
