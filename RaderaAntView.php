<?php
require_once 'View.php';

class RaderaAntView extends View {

    private $anteckning = null;

    public function __construct($db, $ant) {
        parent::__construct($db);
        $this->anteckning = $ant;
    }

    public function getName() {
        return "Radera anteckning";
    }

    public function process() {
        $this->trace("RaderaAntView process <br>\n");
        if (isset($_POST['raderaantbekr'])) {
            raderaAnteckning($this->db, $this->anteckning);
        }
        return null;
    }

    public function showUI() {




        $sql = "select rubrik, innehall from anteckning where id=$this->anteckning";
        $res = $this->db->query($sql);
        $row = $res->fetchRow();
        $rubrik = $row[0];
        $innehall = $row[1];
?>
        <br>
        <br>
        <fieldset>
            <legend> Radera anteckning: </legend>
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF'] ?>">
                <label for="rubrik">    Rubrik:   </label> <?php echo $rubrik ?><br/><br/>

                <label for="antecning">    Anteckning:   </label><br><?php echo $innehall; ?>
                <br/>

                <input type="submit" name="raderaantbekr" value="Radera anteckning"/>
            </form>
        </fieldset>
        <br>


<?php
    }

}
?>
