<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DB
 *
 * @author jnda
 */
 error_reporting(E_ERROR | E_PARSE);
class DB {
    const host="localhost";
    const user="root";
    const password="";
    const database="tidkoll2015";
    private $conn;



    public function __construct() {
        $this->conn = mysql_connect( self::host, self::user, self::password );
        if (! $this->conn) {
            print ("Anslutningen misslyckades");
            exit;
        }
        		mysql_select_db(self::database, $this->conn) or die (self::database . " Database not found." . self::user);

    }
/**
 *
 * @param String $sql En sql fråga
 * @return ResultSet Lämnar till baka en resultsetklass av den aktuella frågan
 */
    public function query($sql) {
        $res = mysql_query($sql, $this->conn);
        if($res==null){
            echo "Det gick fel sql!!:".$sql.'<br/>';
            echo mysql_error($this->conn);
            return null;
        }
        else {
            return new ResultSet($res);
        }
    }

    public function getSingleValue($sql){
        $res = mysql_query($sql, $this->conn);
        if($res==null){
            echo "Det gick fel sql:".$sql;
            return null;
        }
        else {
            $row = mysql_fetch_row($res);
            if($row!=null){
                return $row[0];

            }
        }

        
    }

    function QueryAndGenereateSelectList($varindex, $table, $id, $field, $default, $onchange=null, $where=""){
        $sql = "select distinct $id, $field from $table";
        //echo "where: $where <br>";
        if($where!=""){
            $sql=$sql." where " . $where;
        }
        //echo $sql;
        $res = $this->query($sql);
        if($res==null){
            echo "QueryAndGenereateSelectList gick fel:".$sql;
        }
        if($onchange != null) {
            $onchangeString = "onchange='$onchange'";
        }
        else {
            $onchangeString="";
        }
        echo "<select name='$varindex' id='$varindex' value='$default' $onchangeString>\n";

        while(($row=$res->fetchRow())!= null){
            echo "<option value='$row[0]'";
            if($row[0]==$default){
                echo " SELECTED='selected'";
            }
            echo "> $row[1] </option>\n";
        }
        echo "</select>\n";

    }
    function QueryAndGenereateRadioList($varindex, $table, $id, $field, $default, $onchange=null, $where=""){
        $sql = "select $id, $field from $table";
        //echo "where: $where <br>";
        if($where!=""){
            $sql=$sql." where " . $where;
        }
        //echo $sql;
        $res = $this->query($sql);
        if($res==null){
            echo "QueryAndGenereateSelectList gick fel:".$sql;
        }
        if($onchange != null) {
            $onchangeString = "onchange='$onchange'";
        }
        else {
            $onchangeString="";
        }

        while(($row=$res->fetchRow())!= null){
        echo "<input type='radio' name='$varindex' id='$row[0]' $onchangeString ";
            echo "value='$row[0]' ";
            if($row[0]==$default){
                echo " checked='checked'";
            }
            echo "><label for='$row[0]' class='radiolabel'> $row[1] </label><br/>\n";
        }
        //echo "</select>\n";

    }

    function close(){
        mysql_close($this->conn);
    }

}
?>
