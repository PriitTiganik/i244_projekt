/**
 * Created by Priit on 19/03/2016.
 */

/*var aa=document.getElementById('nr');
alert(aa);*/

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



