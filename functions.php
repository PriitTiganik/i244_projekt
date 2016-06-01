<?php
connect_db();
$pictures = array();//pics_from_base();

function alusta_sessioon(){
    // siin ees võiks muuta ka sessiooni kehtivusaega, aga see pole hetkel tähtis
    session_start();
    if(!isset($_SESSION["userid"])){
        $_SESSION["userid"]="";//as long as user is not logged in userid exists, but is blank
    }

}

function lopeta_sessioon(){
    $_SESSION = array();
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time()-42000, '/');
    }
    session_destroy();
}



function kuva_galerii(){

    global $pictures;
    $pictures = pics_from_base();
    include_once("view/head.html");
    gallery ();
    include_once("view/foot.html");
    if(isset($_SESSION['teade'])){
        unset($_SESSION['teade']);
    }
}
function kuva_galleryprivate(){
    global $pictures;
    $pictures = pics_from_base();
    include_once("view/head.html");
    gallery (false);
    include_once("view/foot.html");
    if(isset($_SESSION['teade'])){
        unset($_SESSION['teade']);
    }
}
function gallery ($public=true){ //generates gallery
    echo "<div class=\"div_gallery\"><table>";
    global $pictures;
    $columns=4;
    $cellcount=0;

    echo '<tr><td colspan="'.$columns.'"></td>'; //first row is empty
    foreach($pictures as $pic) {
        if($pic["user_id"]==$_SESSION["userid"]||($pic["is_public"]=="yes"&&$public)) { //gallery for only public pictures or from the same user
            $jpgimg = "?mode=img_view&public=$public&img=" . $pic["id"]; /// pildi aadressist teeb get p'ringu
            $jpgthumb = "img/thumb/" . $pic["thumb"];
            $alt = $pic["title"];
            if($cellcount%$columns==0){echo '</tr>';}//end table row if 4 columns are full
            if($cellcount%$columns==0){echo '<tr>';} //start table row if previous 4 columns are full
            echo '<td class="imageingallery"><div><a href="' . $jpgimg . '"><img src="' . $jpgthumb . '" alt="' . $alt . '"></a></div></td>';
            $cellcount++;
        }
    }
    echo "</table></div>";

}
function kuva_index(){
    include_once("view/head.html");
    include("view/index.html");
    include_once("view/foot.html");
}

function kuva_change(){
    global $connection;
    $errors=array();

    include_once("view/head.html");
    if(!empty($_POST)){ //on sisse loginud ja vajutas uploadi nuppu
        if(empty($_POST["img_author"])){
            $errors[]="Missing author!";
        }
        if(empty($_POST["img_title"])){
            $errors[]="Missing title!";
        }
        if($_POST["post_type"]=='update'){
            //ei pea faile kontrollima
        } else if ($_POST["post_type"]=='insert'){
            //kontrollida ka failide olemasolu
            if($_FILES["img_file"]["error"] > 0 ){
                $errors[]="Missing large image!";
            }
            if($_FILES["thumb_file"]["error"] > 0 && empty($_POST["genthumb"])){
                $errors[]="Missing thumbnail image!";
            }
        }

    } else{ //on sisse logitud, aga pole uploadi nuppu vajutanud
        include("view/change.html");
        include_once("view/foot.html");
    }

    if(count($errors)==0&&!empty($_POST)) { //erroreid pole, upload voi insert
        $queryresult="";
        $img_author=array('img_author',mysqli_real_escape_string($connection,$_POST["img_author"]))[1];
        $img_title=array('img_title',mysqli_real_escape_string($connection,$_POST["img_title"]))[1];
        $is_public=array('is_public',mysqli_real_escape_string($connection,$_POST["is_public"]))[1];
        $img_img=upload('img_file','/home/ptiganik/public_html/i244_projekt/img/img', true,600);

        if( empty($_POST["genthumb"])){//generate thumbnail from large image.
            $img_thumb=upload('thumb_file','/home/ptiganik/public_html/i244_projekt/img/thumb', true,150);
        } else {
            $img_thumb="";
        }
        if(($img_img=="error"|| $img_img=="exists") && $_POST["post_type"]=='insert'){
            $errors[]="Large file with the same name already exists and the request was not processed!";
        }

        if(($img_thumb=="error"|| $img_thumb=="exists") && $_POST["post_type"]=='insert' && empty($_POST["genthumb"])){
            $errors[]="Thumbnail file with the same name already exists and the request was not processed!";
        }

        $user=$_SESSION['userid'];
        if($_POST["post_type"]=='update'&&count($errors)==0){
            $img_id=array('img_id',mysqli_real_escape_string($connection,$_POST["img_id"]))[1];
            if($img_img=="error"|| $img_thumb=="error"){
                $pic=pic_from_base($img_id);
            }
            if($img_img=="error"){
                $img_img=$pic["pic"];
            }
            if($img_thumb=="error"){
                $img_thumb=$pic["thumb"];
            }
            //update query
            $query="update ptiganik_pildid set pic='$img_img', thumb='$img_thumb', title='$img_title', author='$img_author',user_id='$user', is_public= '$is_public' where id='$img_id'";

            $queryresult=mysqli_query($connection, $query);
            if($queryresult){
                $queryresult='Picture updated!';
            }else {
                $queryresult='For some reason picture was not updated..';
            }
        } else if ($_POST["post_type"]=='insert'&&count($errors)==0){
            if( !empty($_POST["genthumb"])){//generate thumbnail from large image.
                include_once("img_resize.php");
                $file='/home/ptiganik/public_html/i244_projekt/img/img/'.$img_img;
                $resizedfile='/home/ptiganik/public_html/i244_projekt/img/thumb/'.$img_img;
                smart_resize_image($file,null,150,150,true,$resizedfile,false,false, 100);
                $img_thumb=$img_img;
            }
            //insert query
            $query="INSERT INTO ptiganik_pildid ( pic,thumb, title, author, user_id, is_public) VALUES('$img_img', '$img_thumb','$img_title','$img_author','$user','$is_public')";
            $queryresult=mysqli_query($connection, $query);
            if($queryresult){
                $queryresult='Picture uploaded!';
            }else {
                $queryresult='For some reason picture was not uploaded..';
            }
        }
        echo  '<span style="color:red">'.$queryresult.'</span></br>'; //annab teada, mida tehti
        print_errors($errors);
        include("view/change.html");
        include_once("view/foot.html");
    } else{ //mingid valjad on tyhjad voi muud errorid
        print_errors($errors);
        include_once("view/change.html");
        include_once("view/foot.html");
    }

}
function kuva_img_view(){
    include_once("view/head.html");
    if($_GET["public"]){
        img(1);
    }else{
        img(0);
    }

    include_once("view/img_view.html");
    include_once("view/foot.html");
}
function img($public=1){
    $img_id="";
    if(isset($_GET["img"])){
        $img_id=$_GET["img"];
    } else{kuva_galerii(); } //empty parameter returns gallery
    $pic=pic_from_base($img_id); //returns the row of the picture
    //if picture does not exist or is not from the same user and not made public then return to gallery
    if($pic==""||($pic["user_id"]!=$_SESSION["userid"]&&$pic["is_public"]!="yes")){ header("Location: controller.php?mode=gallery");}

//navigate calculations
    $nextpic=pic_from_base($img_id, 'next');
    $prevpic=pic_from_base($img_id,'prev');
    if($public){
        if($nextpic!=""){$nextpic=$nextpic["id"];}else {$nextpic=$pic["id"];}
        if($prevpic!=""){$prevpic=$prevpic["id"];}else {$prevpic=$pic["id"];}
    } else if(!$public) {
        if($nextpic!="" &&$nextpic["user_id"]==$_SESSION["userid"]){$nextpic=$nextpic["id"];}else {$nextpic=$pic["id"];}
        if($prevpic!=""&&$prevpic["user_id"]==$_SESSION["userid"]){$prevpic=$prevpic["id"];}else {$prevpic=$pic["id"];}
    }

//   contents
    echo '<table border="0"><tr>
        <td><a  id="previmage" href="?mode=img_view&public='.$public.'&img='.$prevpic.'"><span><img  src="prev.png" alt="prev img"></span></a></td>
        <td colspan="2"><div class="img_view"><a  href="?mode=img_view&public='.$public.'&img='.$nextpic.'"><img src="img/img/'.$pic["pic"].'" alt="pic" ></a> </div></td>
        <td><a id="nextimage" href="?mode=img_view&public='.$public.'&img='.$nextpic.'"><span><img  src="next.png" alt="next img"></span></a></td>
        </tr>';

    echo '<tr><td></td><td rowspan="2">';

    //change pic button
    if(isset($_SESSION["user"])&&$pic["user_id"]==$_SESSION["userid"]){ //if user is logged in he/she can change the picture
        $getimg="&img=".$_GET["img"];
        echo '<div ><a class="button_green" href="?mode=change'.$getimg.'">Change picture</a></div>';
    }

    echo '</td><td align="right"><span class="img_name">'.$pic["title"].'</span> / by: <span class="img_author">'.$pic["author"].'</span></td><td></td></tr>';
    echo '<tr><td></td><td align="right"><span class="img_is_public">Public: '.$pic["is_public"].'</span></td><td></td></tr>';
    echo '</table>';

}

function kuva_logout(){
    lopeta_sessioon();
    kuva_login();
}

function kuva_login(){
    include_once("view/head.html");
    $errors=array();

    if(!empty($_POST)){
        if(empty($_POST["login_email"])){
            $errors[]="missing email";
        }
        if(empty($_POST["login_password"])){
            $errors[]="missing password";
        }
    }

    if(count($errors)==0&&!empty($_POST)){
        $userid=too_kasutaja_id($_POST["login_email"],$_POST["login_password"], 'validate_user');

        if(!empty($userid)){
            $_SESSION['user']=$_POST["login_email"];
            $_SESSION['userid']=($userid[0]['id']);
            $_SESSION['teade']="Tere, kasutaja ".$_POST["login_email"];
            header("Location: controller.php?mode=gallery");

        } else{
            echo '<span style="color:red">Vale kasutajanimi voi parool!</span>';
            include_once("view/login.html");
            include_once("view/foot.html");
        }

    } else{
        include_once("view/login.html");
        include_once("view/foot.html");
    }

}

function kuva_register(){
    include_once("view/head.html");
    $errors=array();

    if(!empty($_POST)){
        if(empty($_POST["register_email"])){
            $errors[]="Missing email!";
        }
        if(empty($_POST["register_password"])){
            $errors[]="Missing password!";
        }
        if(empty($_POST["register_password_repeat"])){
            $errors[]="Missing second password!";
        }
        if($_POST["register_password"]!=$_POST["register_password_repeat"]){
            $errors[]="Passwords do not match!";
        }
        if(!empty(too_kasutaja_id($_POST["register_email"],'', 'check_email'))){
            $errors[]="Email already in use!";
        }
    }

    if(count($errors)==0&&!empty($_POST)){

        if(loo_kasutaja($_POST["register_email"], $_POST["register_password"])){
            include_once("view/login.html");
            echo '<span style="color:red">Kasutaja loodud, logi sisse!</span>';
            include_once("view/foot.html");
        }
        else{
            //ei saanud kasutajat luua
            echo '<span style="color:red">Kasutajat ei saanud mingil pohjusel luua, vota adminniga yhendust, ilmselt on tal PHPs mingi kala!</span>';
            include_once("view/login.html");
            include_once("view/foot.html");
        }

    } else{
        include_once("view/login.html");
        print_errors($errors);
        include_once("view/foot.html");
    }

}
function print_errors($errors){
    echo '<span style="color:red">';
    foreach($errors as $error){
        echo $error.'</br>';
    }
    echo '</span>';
}
function loo_kasutaja($user, $pass){
    global $connection;
    $user=mysqli_real_escape_string($connection, $user);
    $pass=mysqli_real_escape_string($connection, $pass);
    $query="insert into ptiganik_kasutajad (user, pass)  values('$user',SHA1('$pass'))";
    $result =mysqli_query($connection, $query);
    //echo $query;
    return $result;
}
function too_kasutaja_id($user, $pass, $type){
    global $connection;
    //luua massiiv info hoidmiseks
    $db_user = array();
    //turvalisust juurde
    $user=mysqli_real_escape_string($connection, $user);
    $pass=mysqli_real_escape_string($connection, $pass);
    //Käivitada päring, toob oige kasutaja id
    if($type=='check_email'){
        $query="select id from ptiganik_kasutajad where user='$user' ";
    } else if ($type=='validate_user') {
        $query="select id from ptiganik_kasutajad where user='$user' and pass=sha1('$pass') ";
    }

    $result =mysqli_query($connection, $query);
    //    Pärast päringu käivitamist kasutada tulemuste lugemiseks while tsüklit.
    while($row=mysqli_fetch_assoc($result)){ //Seni kuni on ridu, loe järgmine rida ja paiguta see eelnevalt loodud massiivi lõppu
        $db_user[]=$row;
    }
    //funktsiooni lõpus tagastada täidetud massiiv
    return $db_user;
}

function connect_db(){
    global $connection;
    $host="localhost";//"http://enos.itcollege.ee/phpmyadmin/";
    $user="test";
    $pass="t3st3r123";
    $db="test";
    $connection = mysqli_connect($host, $user, $pass, $db) or die("ei saa mootoriga ühendust");
    mysqli_query($connection, "SET CHARACTER SET UTF8") or die("Ei saanud baasi utf-8-sse - ".mysqli_error($connection));
}

function pics_from_base(){
    global $connection;
      //luua massiiv info hoidmiseks
    $db_pildid = array();
    //Käivitada päring, mis hangib eelmisel korral loodud piltide tabelist kõik read.
    $query="select * from ptiganik_pildid";
    $result =mysqli_query($connection, $query);

    //    Pärast päringu käivitamist kasutada tulemuste lugemiseks while tsüklit.
    while($row=mysqli_fetch_assoc($result)){ //Seni kuni on ridu, loe järgmine rida ja paiguta see eelnevalt loodud massiivi lõppu
        $db_pildid[]=$row;
    }
    //funktsiooni lõpus tagastada täidetud massiiv
    return $db_pildid;
}
function pic_from_base($picid, $type=""){
    global $connection;
    //luua massiiv info hoidmiseks
    $db_pilt = array();
    //turvalisust
    $picid=mysqli_real_escape_string($connection, $picid);
    //toob yhe pildi rea
    $user=$_SESSION["userid"];
    switch($type){
        case 'next':
            $query="select * from ptiganik_pildid where id = (select min(id) from ptiganik_pildid where id > '$picid' and (user_id='$user' or is_public='yes'))";
            break;
        case 'prev':
            $query="select * from ptiganik_pildid where id = (select max(id) from ptiganik_pildid where id < '$picid' and (user_id='$user' or is_public='yes'))";
            break;
        default:
            $query="select * from ptiganik_pildid where id = '$picid'";
    }

    $result =mysqli_query($connection, $query);

    //    Pärast päringu käivitamist kasutada tulemuste lugemiseks while tsüklit.
    while($row=mysqli_fetch_assoc($result)){ //Seni kuni on ridu, loe järgmine rida ja paiguta see eelnevalt loodud massiivi lõppu
        $db_pilt[]=$row;
    }
    //funktsiooni lõpus tagastada täidetud massiiv

    if(!empty($db_pilt)){
        return $db_pilt[0];
    } else {
        return "";
    }
}
function upload($name, $loc, $resize, $size){
    $allowedExts = array("jpg", "jpeg", "gif", "png", "JPG", "JPEG", "GIF", "PNG");
    $allowedTypes = array("image/gif", "image/jpeg", "image/png","image/pjpeg");
    error_reporting(E_ALL ^ E_STRICT);
    $extension = end(explode(".", $_FILES[$name]["name"]));
    error_reporting(-1);
    if ( in_array($_FILES[$name]["type"], $allowedTypes)
        && ($_FILES[$name]["size"] < 10000000) // see on 10000kb
        && in_array($extension, $allowedExts)) {
        // fail õiget tüüpi ja suurusega
        if ($_FILES[$name]["error"] > 0) {
            return "error";
        } else {
            // vigu ei ole
            if (file_exists($loc."/" . $_FILES[$name]["name"])) {
                // fail olemas ära uuesti lae, tagasta failinimi
                //return $_FILES[$name]["name"];
                return "exists";
            } else {
                // kõik ok, aseta pilt
                move_uploaded_file($_FILES[$name]["tmp_name"], $loc."/" . $_FILES[$name]["name"]);
                if($resize){
                    include_once("img_resize.php");
                    smart_resize_image($loc."/".$_FILES[$name]["name"], null, $size,$size, true,$loc."/".$_FILES[$name]["name"] , false, false,100);
                }
                return $_FILES[$name]["name"];
            }
        }
    } else {
        return "error";
    }
}