<?php
require_once("functions.php");
alusta_sessioon();
connect_db();
//print_r(pics_from_base());

ini_set("display_errors", 1);




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