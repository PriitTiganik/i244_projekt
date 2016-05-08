<?php

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

    $pictures = pics_from_base();
    echo "<pre>";
    print_r($pictures);
    echo "</pre>";

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
function kuva_img_view(){

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
        //echo "noerrors";
        if(($_POST["login_email"])=='kasutaja@email.ee'&&($_POST["login_password"])=='parool'){
            $_SESSION['user']="kasutaja";
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
    print_r($result);
    //    Pärast päringu käivitamist kasutada tulemuste lugemiseks while tsüklit.
    while($row=mysqli_fetch_assoc($result)){ //Seni kuni on ridu, loe järgmine rida ja paiguta see eelnevalt loodud massiivi lõppu
        $db_pildid[]=$row;
    }

    //funktsiooni lõpus tagastada täidetud massiiv
    return $db_pildid;
}