// JavaScript Document
jQuery(function($){


    $(window).resize(function(){
        var width=$(window).width();
        var height=$(window).height();
        $('.main_visual').css('height', (width)*0.4487534626038781);
        $('.main_image').css('height', (width)*0.4487534626038781);
        $('.main_image ul').css('height', (width)*0.4487534626038781);
        $('.main_image ul li').css('height', (width)*0.4487534626038781);
        $('.main_image ul span').css('height', (width)*0.4487534626038781);
        $('.main_image ul span a').css('height', (width)*0.4487534626038781);
        $('.main_image ul span a img').css('height', (width)*0.4487534626038781);
        $('.xzlanbox dl').css('width',((width)/2)-17);
        $('header p').css('width',(width)-75);
        $('.xsearchbox').css('height',(height)-220);
        $('.xsearchbox dl dt').css('height',(height)-223);
        $('.xsearchbox dl dd').css('height',(height)-223);
        $('.fatxtarea').css('height',(height)-230);
        $('.fatxtarea #editorContainer').css('height',(height)-235);
        $('.facon').css('min-height',(height)-235);

        $('#wrapper dl').css('width', (width)/4);
        $('.scroll').css('width',  $('.scroll dl').size() *  $('.scroll dl').width());
    }).resize();


    $(".nav ul li a").click(function(){
        $(this).addClass("active");
        $(this).parents().siblings().children('a').removeClass("active");
    });


    $("header ul li").click(function(){
        $(this).children('a').addClass("active");
        $(this).siblings().children('a').removeClass("active");
    });

    $(".xsearchbox dl dt ul li").click(function(){
        $(this).addClass("active");
        $(this).siblings().removeClass("active");
    });

    $(".xsearchbox dl dd a").click(function(){
        $(this).addClass("active");
        $(this).siblings().removeClass("active");
    });

    $('.xzlanbox dl:nth-child(2n)').css('float', 'right');
    $('.xsearchbox dl dt ul li:last-child a').css('border', 'none');



    $('.interaction span:last-child').css('border', 'none');
    $('.like_peo dl:last-child').css('border', 'none');

    $(".tab ul li").click(function(){
        $(this).addClass("cur").siblings().removeClass("cur");
    });

    $("footer ul li a").click(function(){
        $(this).addClass("cur").parents("li").siblings().find("a").removeClass("cur");
    });

    $(".qmeun span").click(function(){
        $(this).siblings("a").toggle();
    });

});



 
 
