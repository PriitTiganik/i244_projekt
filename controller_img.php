<?php
/**
 * Created by PhpStorm.
 * User: Priit
 * Date: 10/04/2016
 * Time: 12:01
 */
define("IMGMAX", 4);
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

echo '<div class="img_view"><img src="img/img/'.$img.'.jpg" alt="pic" > </div>';
echo '<div class="img_name">'.$img.'.jpg</div>';
?>
