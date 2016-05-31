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

    $('.imageingallery img').on({
        mouseenter: function(){//midateha
            $(this).css("border","2px solid #3bc78b");
        },
        mouseleave: function(){
            $(this).css("border","2px solid #363E45");
        }
    });

    /*http://stackoverflow.com/questions/1402698/binding-arrow-keys-in-js-jquery*/
    $(document).keydown(function(e) {
        switch(e.which) {
            case 37: // left
                window.location=document.getElementById('previmage').href;
                break;

            case 38: // up
                window.location=document.getElementById('previmage').href;
                break;

            case 39: // right
                window.location=document.getElementById('nextimage').href;
                break;

            case 40: // down
                window.location=document.getElementById('nextimage').href;
                break;

            default: return; // exit this handler for other keys
        }
        e.preventDefault(); // prevent the default action (scroll / move caret)
    });


});