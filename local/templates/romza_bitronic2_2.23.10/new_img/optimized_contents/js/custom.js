function viewport(){var a=window,b="inner";return"innerWidth"in window||(b="client",a=document.documentElement||document.body),{width:a[b+"Width"],height:a[b+"Height"]}}function footersize(){var footLen=$(".footer").length;if(footLen<1){$(".main-wrap").css("padding-bottom","0");}else{var footH=$(".footer").height();$(".main-wrap").css("padding-bottom",footH+"px");}}$(document).ready(function(){if(/MSIE 10/i.test(navigator.userAgent)){$("html").addClass("ie");}if(/MSIE 9/i.test(navigator.userAgent)||/rv:11.0/i.test(navigator.userAgent)){$("html").addClass("ie");}if(/Edge\/\d./i.test(navigator.userAgent)){$("html").addClass("ie");}if(navigator.userAgent.toLowerCase().indexOf("firefox")>-1){$("html").addClass("mozilla");}if(/Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent)){$('html').addClass('ios');}else{$('html').addClass('desktop');};$("input, textarea").each(function(){var a=$(this).attr("placeholder");$(this).focus(function(){$(this).attr("placeholder","")}),$(this).focusout(function(){$(this).attr("placeholder",a)})});$(".js-focus-fix").on("click",function(){$(this).closest(".js-focus").find("textarea, input").focus();})
$(".js-focus textarea, .js-focus input").focus(function(){$(this).closest(".js-focus").find(".js-focus-fix").fadeOut(0);}).blur(function(){if($(this).val().trim().length<1){$(this).closest(".js-focus").find(".js-focus-fix").fadeIn(0);}})
$(".js-nav-link").on("mouseenter",function(){if($(".ios").length<1){$(this).closest(".js-nav-item").siblings(".js-nav-item").removeClass("active").find(".js-nav-hide").stop().fadeOut(0);$(this).addClass("active").closest(".js-nav-item").siblings(".js-nav-item").find(".js-nav-link").removeClass("active");$(this).siblings(".js-nav-hide").stop().fadeIn(0);}})
$(".js-nav-link").on("click",function(e){if($(".ios").length>0){if($(this).hasClass("active")){$(this).removeClass("active");$(this).siblings(".js-nav-hide").stop().fadeOut(0);}else{$(this).closest(".js-nav-item").siblings(".js-nav-item").removeClass("active").find(".js-nav-hide").stop().fadeOut(0);$(this).addClass("active").closest(".js-nav-item").siblings(".js-nav-item").find(".js-nav-link").removeClass("active");$(this).siblings(".js-nav-hide").stop().fadeIn(0);}}e.preventDefault();})
$(".js-nav-item").on("mouseleave",function(){if($(".ios").length<1){$(this).find(".js-nav-hide").stop().fadeOut(0);$(this).find(".js-nav-link").removeClass("active");}})
$(".js-nav").on("mouseover",function(){$(this).addClass("active");$("#mask").stop().fadeIn(0);})
$(".js-nav").on("mouseleave",function(){$(this).removeClass("active");$("#mask").stop().fadeOut(0);})
$(".js-fade-button").on("mouseenter",function(){if($(".ios").length<1){if($(this).hasClass("active")){return}else{$(".js-fade-hide, .js-click-hide").stop().fadeOut(0);$(".js-fade-button, .js-click-button").removeClass("active");$(this).parents(".js-fade").find(".js-fade-hide").stop().fadeIn(0);$(this).removeClass("fixed");$(this).addClass("active");}}})
$(".js-fade-button").on("click",function(){if($(".ios").length>0){if($(this).hasClass("active")){$(this).removeClass("active");$(this).parents(".js-fade").find(".js-fade-hide").stop().fadeOut(0);}else{$(this).addClass("active");$(this).parents(".js-fade").find(".js-fade-hide").stop().fadeIn(0);}}})
$(".js-fade-out").on("mouseleave",function(){$(".js-fade-hide").stop().fadeOut(0);$(".js-fade-button").removeClass("active");})
$(".js-fade-close").on("click",function(){$(".js-fade-hide").stop().fadeOut(0);$(".js-fade-button").removeClass("active");})
$(".js-click-button").on("click",function(e){e.preventDefault();if($(this).hasClass("active")){$(".js-fade-hide, .js-click-hide").stop().fadeOut(0);$(".js-fade-button, .js-click-button").removeClass("active");}else{$(".js-fade-hide, .js-click-hide").fadeOut(0);$(".js-fade-button, .js-click-button").removeClass("active");$(this).parents(".js-click").find(".js-click-hide").stop().fadeIn(0);$(this).addClass("active");}})
$(".js-click-close, .js-city-select").on("click",function(){$(".js-click-hide").stop().fadeOut(0);$(".js-click-button").removeClass("active");})
$(".js-tabs-link").on("click",function(e){e.preventDefault();if($(this).closest(".js-tabs-item.active").length<1){$(this).closest(".js-tabs-item").addClass("active").siblings(".js-tabs-item").removeClass("active");var curHref=$(this).attr("href");$(curHref).addClass("active").siblings(".js-tabs-content").removeClass("active");}})
$(".js-go-tab-1").on("click",function(){$(".js-tab-1").trigger("click");})
$(".js-go-tab-2").on("click",function(){$(".js-tab-2").trigger("click");})
$(".js-remove-button").live("click",function(){$(this).closest(".js-remove").fadeOut(0,function(){$(this).remove();})
return false;})
$(".js-seo-more").on("click",function(){if($(this).hasClass("active")){$(this).removeClass("active").closest(".js-seo").find(".js-seo-content").removeClass("opened");}else{$(this).addClass("active").closest(".js-seo").find(".js-seo-content").addClass("opened");}})
$(".services__list .js-open-service").on("click",function(){var curData=parseInt($(this).attr("data-item"))-1;$(".js-services").stop().fadeIn(500);$('.js-slider-6').slick({dots:false,infinite:true,autoplay:false,speed:500,slidesToShow:1,slidesToScroll:1,touchThreshold:200,initialSlide:curData,adaptiveHeight:true,prevArrow:$('.js-prev-6'),nextArrow:$('.js-next-6')});})
$(".js-close-service").on("click",function(){$(".js-services").stop().fadeOut(0,function(){$('.js-slider-6').slick('unslick');});})
$(".js-close-loc").on("click",function(){$("#mask, .location").fadeOut(300,function(){$("body").remove("geotarget");});})
$(".js-city-select").on("click",function(){var selectedCity=$(this).text();$(".js-city-selected").text(selectedCity);})})
$(window).load(function(){if($(".js-rating").length){$('.js-rating').barrating({showSelectedRating:false,readonly:true});}if($(".js-slider-1").length){$('.js-slider-1').slick({dots:true,infinite:true,autoplay:true,autoplaySpeed:5000,speed:700,cssEase:'linear',slidesToShow:1,slidesToScroll:1,pauseOnHover:true});}if($(".js-slider-2").length){$('.js-slider-2').slick({dots:true,arrows:true,infinite:true,autoplay:false,swipeToSlide:true,slidesToShow:5,slidesToScroll:1,touchThreshold:200,speed:300,adaptiveHeight:true,responsive:[{breakpoint:1400,settings:{slidesToShow:4}},{breakpoint:1150,settings:{slidesToShow:3}},]});}if($(".js-slider-3").length){$('.js-slider-3').slick({dots:true,arrows:true,infinite:true,autoplay:false,swipeToSlide:true,slidesToShow:6,slidesToScroll:1,touchThreshold:200,speed:300,adaptiveHeight:true,responsive:[{breakpoint:1400,settings:{slidesToShow:5}},{breakpoint:1150,settings:{slidesToShow:4}},]});}if($(".js-slider-4").length){$('.js-slider-4').slick({dots:true,arrows:true,infinite:true,autoplay:false,speed:300,slidesToShow:1,slidesToScroll:1,adaptiveHeight:true,prevArrow:$('.js-prev-4'),nextArrow:$('.js-next-4'),appendDots:$(".js-dots-1")});}if($(".js-slider-5").length){$('.js-slider-5').slick({dots:true,arrows:true,infinite:true,autoplay:false,speed:300,slidesToShow:1,slidesToScroll:1,adaptiveHeight:true,prevArrow:$('.js-prev-5'),nextArrow:$('.js-next-5'),appendDots:$(".js-dots-2")});}$('body').removeClass('loaded');setTimeout(function(){if($('.js-ellip-2').length){Ellipsis({class:'.js-ellip-2',lines:2})}},300);var copyList=$(".js-copy-from").html();$(".js-copy-to").html(copyList);})
var handler1=function(){setTimeout(function(){footersize();},1);setTimeout(function(){footersize();},500);if($(".js-slider-1 .slick-track").length>0){$(".js-slider-1").slick('setPosition');}if($(".js-slider-2 .slick-track").length>0){$(".js-slider-2").slick('setPosition');}if($(".js-slider-3 .slick-track").length>0){$(".js-slider-3").slick('setPosition');}if($(".js-slider-4 .slick-track").length>0){$(".js-slider-4").slick('setPosition');}if($(".js-slider-5 .slick-track").length>0){$(".js-slider-5").slick('setPosition');}if($(".js-slider-6 .slick-track").length>0){$(".js-slider-6").slick('setPosition');}}
$(window).bind('orientationchange',handler1);$(window).bind('resize',handler1);$(window).bind('load',handler1);var handler2=function(){var formLen1=$("input[type=checkbox].js-formstyler").length;var formLen2=$("select.js-formstyler").length;var formLen3=$("input[type=radio].js-formstyler").length;if(formLen1>0||formLen2>0||formLen3>0){$('input[type=checkbox].js-formstyler').styler({});$('select.js-formstyler').styler({});$('input[type=radio].js-formstyler').styler({wrapper:'.radio'});}}
$(window).bind('load',handler2);var handler3=function(){$(".radio-item").each(function(){var a=$(this).find(".jq-radio.checked").length;a>=1?$(this).addClass("active"):$(this).removeClass("active")}),$(".radio-item").each(function(){var a=$(this).find(".jq-radio.disabled").length;a>=1?$(this).addClass("disabled"):$(this).removeClass("disabled")}),$(".checkbox").each(function(){var a=$(this).find(".jq-checkbox.checked").length;a>=1?$(this).addClass("active"):$(this).removeClass("active")}),$(".checkbox").each(function(){var a=$(this).find(".jq-checkbox.disabled").length;a>=1?$(this).addClass("disabled"):$(this).removeClass("disabled")});}
$(window).bind('click',handler3);$(window).bind('load',handler3);$(document).on('touchstart',function(){documentClick=true;});$(document).on('touchmove',function(){documentClick=false;});$(document).on('click touchend',function(event){if(event.type=="click")documentClick=true;if(documentClick){var target=$(event.target);if(target.is('.js-fade-out')||target.is('.js-fade-out *')){return}if(target.is('.js-click')||target.is('.js-click *')){return}if(target.is('.js-nav')||target.is('.js-nav *')){return}if(target.is('.services')||target.is('.services *')){return}if(target.is('.js-basket-hide')||target.is('.js-basket-hide *')||target.is('.js-basket-open')||target.is('.js-basket-open *')){return}else{$(".js-fade-hide, .js-click-hide, .js-basket-hide").stop().fadeOut(0);$(".js-fade-button, .js-click-button, .js-basket-open").removeClass("active");if($(".js-services .slick-track").length>0){$(".js-services").stop().fadeOut(0,function(){$('.js-slider-6').slick('unslick');});}$(".js-nav-hide").stop().fadeOut(0);$(".js-nav-link").removeClass("active");$(".js-nav").removeClass("active");$("#mask").stop().fadeOut(0);}}});$(document).keyup(function(e){if(e.keyCode==27){$("#mask, .location").fadeOut(300,function(){$("body").remove("geotarget");});}});