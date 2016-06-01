<?php
require_once("functions.php");
alusta_sessioon();

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

    case "change":
        if(isset($_SESSION["user"])) {
            kuva_change();
        } else { //pole sisse loginud
            kuva_index();
        }
        break;
    case "galleryprivate":
        if(isset($_SESSION["user"])) {
            kuva_galleryprivate();
        } else { //pole sisse loginud
            kuva_index();
        }
        break;
    case "img_view":
        kuva_img_view();
        break;
    case "login":
        kuva_login();
        break;
    case "logout":
        kuva_logout();
        break;
    case "register":
        kuva_register();
        break;
}

?>