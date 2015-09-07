<?php
require_once 'View.php';
require_once 'anteckningfunktioner.php';
class NyAntView extends View {

    private $anteckning = null;

    public function  __construct($db, $akt) {
        parent::__construct($db);
        $this->aktivitet=$akt;
    }

    public function getName() {
        return "Ny anteckning";
    }

    public function process() {
        $this->trace("NyAntView process <br>\n");
        if (isset($_POST['skickanyant'])) {
            $innehall = $_POST['anteckning'];
            $rubrik=$_POST['rubrik'];
            $anntyp=$_POST['anttyp'];
            nyAnteckning($this->db, $this->aktivitet,$anntyp, $rubrik, $innehall);
            
        }
    }

    public function showUI() {
        tinymceinit();

?>
        <br>
        <br>
        <fieldset>
            <legend> Ny anteckning </legend>
            <form method="POST" action="tidkoll.php">
                <br/>
                <label for="rubrik">    Rubrik:   </label> <input type="text" name="rubrik" id="rubrik"/><br/>
               <label for="anttyp">Typ: </label>   <?php $this->db->QueryAndGenereateSelectList("anttyp", "anttyp", "id", "namn", 1) ?><br/><br/>
 
                <label for="antecning">    Anteckning:   </label><br><textarea rows="10" cols="80" name="anteckning" id="anteckning"></textarea>
                <input type="submit" name="skickanyant" value="Spara"/>
            </form>
        </fieldset>
        <br>
 

<?php
    }

}
?>
