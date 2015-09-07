<?php
require_once 'View.php';
require_once 'tinymceconf.php';

class RedAntView extends View {

    private $anteckning = null;

    public function  __construct($db, $ant) {
        parent::__construct($db);
        $this->anteckning=$ant;
    }

    public function getName() {
        return "Redigera anteckning";
    }

    public function process() {
        $this->trace("RedAntView process <br>\n");
        if (isset($_POST['skickaantred'])||isset($_POST['anteckning'])) {
            $innehall = $_POST['anteckning'];
            $innehall = addslashes($innehall);
            $anttyp= $_POST['anttyp'];
            $rubrik= $_POST['rubrik'];
            $sql = "update anteckning set anttyp='$anttyp',rubrik='$rubrik', innehall='$innehall' where id=$this->anteckning";
            $this->db->query($sql);
            return $this;
            
        }
        if(isset($_POST['raderaant'])){
            return new RaderaAntView($this->db, $this->anteckning);
        }
    }

    public function showUI() {




        $sql = "select anttyp,rubrik, innehall from anteckning where id=$this->anteckning";
        $res = $this->db->query($sql);
        $row=$res->fetchRow();
        $anttyp=$row[0];
        $rubrik=$row[1];
        $innehall=$row[2];

        tinymceinit();
?>
        <br>
        <br>
        <fieldset>
            <legend> Redigera anteckning </legend>
            <form id="mysave" method="POST" action="<?php echo $_SERVER['PHP_SELF'] ?>">
                <label for="rubrik">    Rubrik:   </label> <input type="text" name="rubrik" id="rubrik" size=50 value="<?php echo $rubrik ?>"/><br/><br/>
                <label for="anttyp">    Typ:</label>    <?php $this->db->QueryAndGenereateSelectList("anttyp", "anttyp", "id", "namn", $anttyp) ?><br><br>

                <label for="anteckning">    Anteckning:   </label><br><textarea rows="40" cols="80" name="anteckning" id="anteckning"><?php echo $innehall; ?></textarea>
                <input type="submit" name="skickaantred" value="Spara"/>
                <br/>
                <br/>
                <input type="submit" name="raderaant" value="Radera anteckning"/>
            </form>
        </fieldset>
        <br>
 <script type="text/javascript">
    function mysave()
    {
        document.forms["myform"].submit();
    }

        </script>


<?php
    }

}
?>
