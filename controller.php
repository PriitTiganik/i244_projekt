<?php

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



include_once("view/head.html");

switch($mode){
    case "gallery":
        include("view/gallery.html");
        break;
    case "index":
        include("view/index.html");
        break;
    case "upload":
        include("view/upload.html");
        break;
    case "img_view":
        include_once("controller_img.php");
        include_once("view/img_view.html");
        break;
}

include_once("view/foot.html");
?>