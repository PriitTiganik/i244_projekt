<?php
require_once("functions.php");
alusta_sessioon();
ini_set("display_errors", 1);

$pictures=array(
    array("big"=>"?mode=img_view&img=img1", "small"=>"img/thumb/img1.jpg", "alt"=>"img1"),
    array("big"=>"?mode=img_view&img=img2", "small"=>"img/thumb/img2.jpg", "alt"=>"img2"),
    array("big"=>"?mode=img_view&img=img3", "small"=>"img/thumb/img3.jpg", "alt"=>"img3"),
    array("big"=>"?mode=img_view&img=img4", "small"=>"img/thumb/img4.jpg", "alt"=>"img4")
);

$mode="";

if(isset($_GET["mode"])){
    $mode=$_GET["mode"];
} else {
    $mode="index";
}

switch($mode){
    case "gallery":
        kuva_galerii();
        break;
    case "index":
        kuva_index();
        break;
    case "upload":
        kuva_upload();
        break;
    case "img_view":
        kuva_img_view();
        break;
    case "login":
        //var_dump($_POST);
        kuva_login();
        break;
    case "logout":
        //var_dump($_POST);
        kuva_logout();
        break;
}

?>