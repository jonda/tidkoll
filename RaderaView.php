<?php
require_once "View.php";
class RaderaView extends View{

    private $radPost;
    public function getName(){
        return "Radera";
    }


    public function  process(){
        $this->trace("RaderaView process<br>\n");
        if(isset($_POST['raderaja'])){
            raderaAktivitet($this->db, $this->radPost);
        }
        return null;
    }
    
    public function showUI(){
    $this->radPost=$_GET['rad'];
    echo '$radPost:'.$this->radPost.'<br>';
    echo "<fieldset>\n";
    echo "<legend>�r du s�ker p� att du vill ta bort f�ljande post?</legend>";
    echo "<div style='background: white; border:1px solid'>";
    echo "<h2>�r du s�ker p� att du vill ta bort f�ljande post?</h2>";
    visaRaderingsInfo($this->db, $this->radPost);
    echo "<br></div>";
    ?>
<br>

<form method="POST" action="tidkoll.php">
<input type="submit" name="raderaja" value="Ja"/>
<input type="submit" name="raderanej" value="Nej"/>
</form>
</fieldset>
<br>

<?php
}
//else {
//$_SESSION['radera']=null;
//}
//Slut p� radera

}

/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

?>
