<?php

abstract class View{

    protected $db;
    public function __construct($db) {
        $this->db=$db;
    }

    public function setDB($db){
        $this->db=$db;
    }
protected function trace($str){
    //echo $str;
}

//protected function trace($str){
//}


    abstract public function process();

    abstract public function showUI();


}

?>
