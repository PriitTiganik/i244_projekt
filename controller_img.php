<?php

define("IMGMAX", 5);
define("IMGMIN", 1);

$img="";
if(isset($_GET["img"])){
    $img=$_GET["img"];
}
$imgno=substr($img,3)*1;

if($imgno<IMGMIN){
   $img="img".($imgno+1);
} else if($imgno>IMGMAX){
    $img="img".($imgno-1);
}
if(!empty($_GET["newimg"])&&$_GET["newimg"]=="t"){
    echo '<img src="img/img/'.$img.'.JPG" alt="pic" > </div>';
} else{
    echo '<div class="img_view"><img src="img/img/'.$img.'.JPG" alt="pic" > </div>';
/*    echo '<pre>';
    print_r($pictures);
    echo '</pre>';*/
    echo '<div class="img_name">'.$img.'.JPG</div>';
    if(isset($_SESSION["user"])){
        $getimg="&img=".$_GET["img"];
        echo '<div class="button_green"><a  href="?mode=change'.$getimg.'">Change pic</a></div></br>';
    }
}

?>
