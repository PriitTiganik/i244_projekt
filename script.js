$(document).ready(function(){
    $('.header-inner>a').mouseenter(function(){
        $(this).css("color","#ffffff");
        $(this).animate({"height": "40px"},
            200);
    });

    $('.header-inner>a').mouseleave(function(){
        $('.header-inner>a:not(#header_active)').css("color","#959B9D");
        $(this).animate({"height": "20px"},
            200);
    });

    $('.table_gallery img').on({
        mouseenter: function(){//midateha
            $(this).css("border","4px solid #363E45");
        },
        mouseleave: function(){
            $(this).css("border","2px solid #363E45");
        }
       /* ,
        click: function(){
            $(this).css("color","Gold");
        }*/
    });

    var img_num;
    function find_img_num(){
        var str=$('.img_view>img').attr("src");
        //var firstindex=st11;
        var firstindex="img/img/img".length;
        var lastindex=str.indexOf(".JPG");
 /*       alert(str);
        alert(firstindex);
        alert(lastindex);
        alert(str.slice(firstindex,lastindex)*1);*/
        return str.slice(firstindex,lastindex)*1;
    }

    $('#previmage').click(function(){
        img_num=find_img_num();
        $newimg=(img_num-1);
        $prevhref="controller_img.php?newimg=t&img=img"+$newimg;
        $.get($prevhref, "html", function(data){//ajax gets new image from controller_img
            $('.img_view').html(data);
        });
    });

    $('#nextimage').click(function(){
        img_num=find_img_num();
        $newimg=(img_num+1);
        $nexthref="controller_img.php?newimg=t&img=img"+$newimg;
        $.get($nexthref, "html", function(data){
            $('.img_view').html(data);
        });
    });



    if(document.getElementById('sheetgallery') != null){
        //alert("tere galerii");
        var imgs = document.querySelectorAll('body #jsgallery img');
        imgs[0].onclick=function(){
            showDetails(imgs[0]);
            return false;
        };
        imgs[1].onclick=function(){
            showDetails(imgs[1]);
            return false;
        };

    }
    function showDetails(el){
        if(document.getElementById('hoidja')==null){
            return false;
        }
        var suurpilt= document.getElementById('suurpilt');
        suurpilt.src= el.parentNode.href;
        //alert(suurpilt.src);
        suurpilt.onload =function(){suurus(this)};
        suurpilt.alt=el.alt;

        document.getElementById('inf').innerHTML=el.alt;
        document.getElementById('hoidja').style.display='initial';

        return false;
    }
    function suurus(el){
        el.removeAttribute("height"); // eemaldab suuruse
        el.removeAttribute("width");
        if (el.width>window.innerWidth || el.height>window.innerHeight){  // ainult liiga suure pildi korral
            if (window.innerWidth >= window.innerHeight){ // lai aken
                el.height=window.innerHeight*0.9; // 90% kõrgune
                if (el.width>window.innerWidth){ // kas element läheb ikka üle piiri?
                    el.removeAttribute("height");
                    el.width=window.innerWidth*0.9;
                }
            } else { // kitsas aken
                el.width=window.innerWidth*0.9;   // 90% laiune
                if (el.height>window.innerHeight){ // kas element läheb ikka üle piiri?
                    el.removeAttribute("width");
                    el.height=window.innerHeight*0.9;
                }
            }
        }
    }
    function  hideDetails() {
        document.getElementById('hoidja').style.display='none';
    }
    window.onresize=function() {
        suurpilt=document.getElementById("suurpilt");
        suurus(suurpilt);
    }


});