<?php
require_once 'db.php';
require_once 'ResultSet.php';
require_once 'tidkollutils.php';
require_once 'redigeraaktivitetfunktioner.php';
require_once 'tidkollrapporter.php';
require_once 'RedigeraView.php';
require_once 'RaderaView.php';
require_once 'RedAntView.php';
require_once 'SkrivInView.php';
require_once 'NyAntView.php';
require_once 'RaderaAntView.php';
session_start();

/* function trace($str){
  echo $str;
  }
 */

function trace($str) {

}
?>

<!DOCTYPE HTML>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <title>Tidkoll</title>
        <link rel="stylesheet" type="text/css" href="tidkollstyle.css" />
    </head>
    <div class="navigering">
        <a href="tidkoll.php"> Redigera/Planera </a> &nbsp; &nbsp;
        <a href="tidkoll.php?add"> Lägg till aktivitet </a> &nbsp; &nbsp;
        <a href="rapportsida.php"> Rapport </a>  <br>
    </div>


    <script type="text/javascript" src="tinymce/jscripts/tiny_mce/tiny_mce.js"></script>

    <script type="text/javascript">
       
        tinyMCE.init({
            // General options
            mode : "textareas",
            theme : "advanced",
            plugins : "pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

            // Theme options
            //theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
            theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
            theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
            theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
            // theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,insertfile,insertimage",
            theme_advanced_toolbar_location : "top",
            theme_advanced_toolbar_align : "left",
            theme_advanced_statusbar_location : "bottom",
            theme_advanced_resizing : true,

            // Skin options
            skin : "o2k7",
            skin_variant : "silver",

            // Example content CSS (should be your site CSS)
            content_css : "css/example.css",

            // Drop lists for link/image/media/template dialogs
            template_external_list_url : "js/template_list.js",
            external_link_list_url : "js/link_list.js",
            external_image_list_url : "js/image_list.js",
            media_external_list_url : "js/media_list.js",

        });
       
    </script>






<?php
$db = new DB();
$db->query("SET lc_time_names = 'sv_SE';");
if (isset($_SESSION['currentView'])) {

    $currentView = $_SESSION['currentView'];
    $currentView->setDB($db);
} else {
    $currentView = null;
}

if ($currentView != null) {
    trace("Nuvarande vy:" . $currentView->getName());

    $currentView = $currentView->process();
} else {
    trace("Ingen process view null<br>\n");
}



if (isset($_GET['ant'])) {
    $currentView = new RedAntView($db, $_GET['ant']);
}


if ($currentView != null) {
    trace("Nuvarande vy:" . $currentView->getName());
    $currentView->showUI();
} else {
    trace("Ingen showUI view null<br>\n");
}

$_SESSION['currentView'] = $currentView;



if(isset($_POST['visaant'])){
$arbuppgift=$_POST['arbuppgift'];
}
elseif(isset($_SESSION['arbupgsok'])){
    $arbuppgift=$_SESSION['arbupgsok'];
}
else {
    $arbuppgift=null;
}
echo "<form action='seanteckningar.php' method='post'>\n";
echo '<label for="arbuppgift">    Kurs/Arbetsuppgift:</label>';
$db->QueryAndGenereateSelectList("arbuppgift", "arbuppgift", "id", "namn", $arbuppgift , "this.form.visaant.click()" );
echo '<input type="submit" name="visaant" value="Visa anteckning"/>';
echo "\n</form>";


if($arbuppgift!=null){

$sql = "select distinct anteckning.id, anteckning.rubrik as '',anttyp.namn as 'Typ', innehall as Anteckning from anteckning, anttillakt, aktivitet,anttyp where anteckning.id=anttillakt.anteckning and anttillakt.aktivitet=aktivitet.id and aktivitet.arbuppgift=$arbuppgift and anttyp.id=anteckning.anttyp";
$res=$db->query($sql);
$res->drawHTMLTableWithLink("seanteckningar.php?ant=", 1);
}
else {
    echo "Visa ant inte satt";
}

$_SESSION['arbupgsok']=$arbuppgift;

?>
