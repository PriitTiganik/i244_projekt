<?php
connect_db();
$pictures = pics_from_base();

function alusta_sessioon(){
    // siin ees võiks muuta ka sessiooni kehtivusaega, aga see pole hetkel tähtis
    session_start();
}

function lopeta_sessioon(){
    $_SESSION = array();
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time()-42000, '/');
    }
    session_destroy();
}



function kuva_galerii(){
//    $pictures=array(
//        array("big"=>"?mode=img_view&img=img1", "small"=>"img/thumb/img1.jpg", "alt"=>"img1"),
//        array("big"=>"?mode=img_view&img=img2", "small"=>"img/thumb/img2.jpg", "alt"=>"img2"),
//        array("big"=>"?mode=img_view&img=img3", "small"=>"img/thumb/img3.jpg", "alt"=>"img3"),
//        array("big"=>"?mode=img_view&img=img4", "small"=>"img/thumb/img4.jpg", "alt"=>"img4")
//    );
global $pictures;
   // $pictures = pics_from_base();
/*    echo "<pre>";
    print_r($pictures);
    echo "</pre>";*/

    //global $pictures;//funktzsiooni sees eraldi skoop
    include_once("view/head.html");
    include("view/gallery.html");
    include_once("view/foot.html");
    if(isset($_SESSION['teade'])){
        unset($_SESSION['teade']);
    }
}
function kuva_index(){
    include_once("view/head.html");
    include("view/index.html");
    include_once("view/foot.html");
}
function kuva_upload(){

    if(isset($_SESSION["user"])){
        include_once("view/head.html");
        include("view/upload.html");
        include_once("view/foot.html");
    } else{
        kuva_index();
    }
}
function kuva_change(){
    global $connection;
    global $pictures;
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
       /* if(empty($_POST["img_img"])){
            $errors[]="Missing large image!";
        }
        if(empty($_POST["img_thumb"])){
            $errors[]="Missing thumbnail image!";
        }*/

    } else{ //on sisse logitud, aga pole uploadi nuppu vajutanud
        include("view/change.html");
        include_once("view/foot.html");
    }
    //echo empty($_POST["genthumb"]);
    //print_r($_POST);
    if(count($errors)==0&&!empty($_POST)) { //erroreid pole, upload voi insert
        $queryresult="aa";
        $picmod=array();
        $img_author=array('img_author',mysqli_real_escape_string($connection,$_POST["img_author"]))[1];
        $img_title=array('img_title',mysqli_real_escape_string($connection,$_POST["img_title"]))[1];
        //$img_img=array('img_img',mysqli_real_escape_string($connection,'img\img\\'.$_POST["img_img"]))[1];
        $img_img=upload('img_file','/home/ptiganik/public_html/i244_projekt/img/img');
        //$img_thumb=array('img_thumb',mysqli_real_escape_string($connection,'img\thumb\\'.$_POST["img_thumb"]))[1];
        $img_thumb=upload('thumb_file','/home/ptiganik/public_html/i244_projekt/img/thumb');

        if($img_img!="error"&&$img_img!="exists"){
            $img_img= 'img/img/'.$img_img; //add address
        } else if(($img_img=="error"|| $img_img=="exists") && $_POST["post_type"]=='insert'){
            $errors[]="Large file with the same name already exists and the request was not processed!";
        }
        if($img_thumb!="error"&&$img_thumb!="exists"){
            $img_thumb= 'img/thumb/'.$img_thumb; //add address
        }else if(($img_thumb=="error"|| $img_thumb=="exists") && $_POST["post_type"]=='insert' && empty($_POST["genthumb"])){
            $errors[]="Thumbnail file with the same name already exists and the request was not processed!";
        }

       // echo ( $img_img);
        //echo ( $img_thumb);
        if($_POST["post_type"]=='update'&&count($errors)==0){
            echo 'update';
            //update query
            $query="update ptiganik_pildid set pic='$img_img', thumb='$img_thumb', title='$img_title', author='$img_author' where title='$img_title'";
            //echo $query;
            $queryresult=mysqli_query($connection, $query);
            if($queryresult){
                $queryresult='Picture updated!';
            }
        } else if ($_POST["post_type"]=='insert'&&count($errors)==0){
            //insert query
            $queryresult='inserted';

            //$query="INSERT INTO ptiganik_pildid ( pic,thumb, title, author) VALUES(".$img_img.", ".$img_thumb.",".$img_title.",".$img_author.")";
            //$queryresult=mysqli_query($connection, $query);
        /*    if($queryresult){
                $queryresult='Picture uploaded!';
            }*/
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
    $pictures = pics_from_base();
    include_once("view/head.html");
    include_once("controller_img.php");
    include_once("view/img_view.html");
    include_once("view/foot.html");
}
function kuva_logout(){
    lopeta_sessioon();
    kuva_login();
}

function kuva_login(){
    include_once("view/head.html");
    $errors=array();
    //var_dump($_POST);
    //echo($_POST["login_password"]);

    if(!empty($_POST)){
        if(empty($_POST["login_email"])){
            $errors[]="missing email";
        }
        if(empty($_POST["login_password"])){
            $errors[]="missing password";
        }
    }
    //var_dump($errors);
    if(count($errors)==0&&!empty($_POST)){
        $userid=too_kasutaja_id($_POST["login_email"],$_POST["login_password"], 'validate_user');
        //insert into ptiganik_kasutajad (user, pass)  values('kasutaja@email.ee',SHA1('parool'));
        //print_r($userid);
        //if(($_POST["login_email"])=='kasutaja@email.ee'&&($_POST["login_password"])=='parool'){//dev sisselogimine
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
    //var_dump($_POST);
    //echo($_POST["login_password"]);

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
        //insert into ptiganik_kasutajad (user, pass)  values('kasutaja@email.ee',SHA1('parool'));
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
    //echo $query;
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
    //print_r($result);
    //    Pärast päringu käivitamist kasutada tulemuste lugemiseks while tsüklit.
    while($row=mysqli_fetch_assoc($result)){ //Seni kuni on ridu, loe järgmine rida ja paiguta see eelnevalt loodud massiivi lõppu
        $db_pildid[]=$row;
    }
    //funktsiooni lõpus tagastada täidetud massiiv
    return $db_pildid;
}
function pic_from_base($picid){
    global $connection;
    //luua massiiv info hoidmiseks
    $db_pilt = array();
    //turvalisust
    $picid=mysqli_real_escape_string($connection, $picid);
    //toob yhe pildi rea
    //echo $picid;
    $query="select * from ptiganik_pildid where title = '$picid'"; //needs to be id
    $result =mysqli_query($connection, $query);
    //print_r($result);
    //    Pärast päringu käivitamist kasutada tulemuste lugemiseks while tsüklit.
    while($row=mysqli_fetch_assoc($result)){ //Seni kuni on ridu, loe järgmine rida ja paiguta see eelnevalt loodud massiivi lõppu
        $db_pilt[]=$row;
    }
    //funktsiooni lõpus tagastada täidetud massiiv
    return $db_pilt;
}

function upload($name, $loc){
    $allowedExts = array("jpg", "jpeg", "gif", "png");
    $allowedTypes = array("image/gif", "image/jpeg", "image/png","image/pjpeg");
    error_reporting(E_ALL ^ E_STRICT);
    $extension = end(explode(".", $_FILES[$name]["name"]));
    error_reporting(-1);

    if ( in_array($_FILES[$name]["type"], $allowedTypes)
        && ($_FILES[$name]["size"] < 100000) // see on 100kb
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
                return $_FILES[$name]["name"];
            }
        }
    } else {
        return "error";
    }
}