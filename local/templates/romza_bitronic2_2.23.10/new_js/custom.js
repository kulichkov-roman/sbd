//UTILS
function viewport() {
    var a = window,
        b = "inner";
    return "innerWidth" in window || (b = "client", a = document.documentElement || document.body), {
        width: a[b + "Width"],
        height: a[b + "Height"]
    }
}

function testWebP() {
    return new Promise(res => {
        const webP = new Image();
        webP.src = 'data:image/webp;base64,UklGRjoAAABXRUJQVlA4IC4AAACyAgCdASoCAAIALmk0mk0iIiIiIgBoSygABc6WWgAA/veff/0PP8bA//LwYAAA';
        webP.onload = webP.onerror = function () {
            res(webP.height === 2);
        };        
    })
}
testWebP().then(hasWebP => {
    if(hasWebP){
        $('html').addClass('webp');
    }
});

function checkBrowserFix(){
    if (/MSIE 10/i.test(navigator.userAgent)) { $("html").addClass("ie") }
    if (/MSIE 9/i.test(navigator.userAgent) || /rv:11.0/i.test(navigator.userAgent)) { $("html").addClass("ie") }
    if (/Edge\/\d./i.test(navigator.userAgent)) { $("html").addClass("ie") }

    if (navigator.userAgent.toLowerCase().indexOf("firefox") > -1) { $("html").addClass("mozilla") }
    
    if (/Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent)) { $('html').addClass('ios'); }
    else {$('html').addClass('desktop') }

    if (/^((?!chrome|android).)*safari/i.test(navigator.userAgent)) { $('html').addClass('safari') }
}

var hideDesktopMenuTmt;
function hideDesktopMenu(){hideDesktopMenuTmt = setTimeout(function(){$('.main-nav_inner, .main-nav_index').removeClass("open-desktop");}, 400);}
function stillDesktopMenu(){if(!!hideDesktopMenuTmt){clearTimeout(hideDesktopMenuTmt);}}

function showMask(){!$('main.main').hasClass('masked-light') && $('main.main').addClass('masked-light');}
function hideMask(){$('main.main').hasClass('masked-light') && $('main.main').removeClass('masked-light');}

function toggleMobileHead(type, customSelector){
    customSelector = typeof customSelector !== 'undefined' ? customSelector : false;
    var slct = customSelector || 'header.header',
        slctForCss = '.main_catalog1,.main_cart-open';
    //console.log([type]);
    switch(type){
        case 'hide':
            $(slct).hide();
            $(slctForCss).css({'padding-top':'0'});
        break;
        case 'show':
            $(slct).show();
            $(slctForCss).css({'padding-top':'50px'});
        break;
        default:
    }
}
// viewport size
// footer
function footersize() {
    var footLen = $(".footer").length;
    if (footLen < 1) {
        $(".main-wrap").css("padding-bottom", "0");
    } else {
        var footH = $(".footer").height();
        $(".main-wrap").css("padding-bottom", footH + "px");
    }
}
//height of basket item container
function resizeBasketItems(){
    var winHeight = $(window).height(),
        heightTop = (winHeight - 350) > 160 ? winHeight - 350 : 160,
        heightBasket = (winHeight - 320) > 160 ? winHeight - 320 : 160;

    $('.compared .basket-items, .watched .basket-items').css({
        'max-height': heightTop + 'px'
    });
    $('.header-basket .basket-items').css({
        'max-height': heightBasket + 'px'
    });
}

hoveredOnJsNavLink = false;
//функция для композита
function initOnFrameLoaded(){
    
    resizeBasketItems();

    $(".js-click-button").on("click", function (e) {
        e.preventDefault();

        if(window.isMobile){
            $("html").toggleClass("no-scroll");
        }

        if ($(this).hasClass("active")) {
            $(".js-fade-hide, .js-click-hide").stop().fadeOut(0);
            $(".js-fade-button, .js-click-button").removeClass("active");
            //$('html').addClass('no-scroll');
            if(!$(this).hasClass('search-form__button')){
                $(".mask-inner").hide();
                $(".rbs-mask").hide();
            }        
        } else {
            $(".js-fade-hide, .js-click-hide").fadeOut(0);
            $(".js-fade-button, .js-click-button").removeClass("active");
            //$('html').removeClass('no-scroll');
            $(this).parents(".js-click").find(".js-click-hide").stop().fadeIn(0);
            $(this).addClass("active");
            if(!$(this).hasClass('search-form__button')){
                $(".mask-inner").show();
                $(".rbs-mask").show();
            }           
           RZB2.utils.initLazy($(".js-fade-hide, .js-click-hide"));
        }
    });

    //catalog button
    $(".js-catalog-button").click(function () {$('.main-nav_inner, .main-nav_index').toggleClass("open");});    
    $('.js-catalog-button, .main-nav_inner, .main-nav_index, .js-click-button').click(function (event) {event.stopPropagation();});
    $(".js-catalog-button_mob").click(function () {
        $(".main-nav_inner, .main-nav_index").toggleClass("open");
        $(this).toggleClass("active");
        $("html").toggleClass("menu-open");
        if ($(this).hasClass("active")) {
            $(".mask-inner").show();
            toggleMobileHead('hide');
        } else {
            $(".mask-inner").hide();
            toggleMobileHead('show');
        }
    });
    $(".js-catalog-button_mob").mouseover(function (e) {
        $('.main-nav_inner, .main-nav_index').toggleClass("open-desktop");
        $('.main-nav_inner, .main-nav_index').mouseover(function () {
            $('.main-nav_inner, .main-nav_index').addClass("open-desktop");
        });
        $('.main-nav_inner, .main-nav_index').addClass("open-desktop");
    });
    $(".js-catalog-button_mob").mouseout(function () {$('.main-nav_inner, .main-nav_index').removeClass("open-desktop");});
    $('.js-catalog-button_mob, .main-nav_inner, .main-nav_index, .search-form-cnt').click(function (e) {e.stopPropagation();});

    $(".main-nav_mobile .js-nav-link:not('.main-nav__link_ctg')").on("click", function () {
        if ($(this).hasClass("active")) {$(".main-nav_inner.main-nav_mobile, .main-nav_index.main-nav_mobile").css("overflow", "scroll");} 
        else {$(".main-nav_inner.main-nav_mobile, .main-nav_index.main-nav_mobile").css("overflow", "visible");}
    });

    $(".main-nav_mobile .main-nav__pages .js-nav-link").on("click", function () {
        $(".main-nav_inner.main-nav_mobile, .main-nav_index.main-nav_mobile").css("overflow", "scroll");
    });
    $(".main-nav_mobile .main-nav__link_ctg").on("click", function () {
        $(".main-nav_inner.main-nav_mobile, .main-nav_index.main-nav_mobile").css("overflow", "scroll");
    });

    /*menu hovers*/
    $(".js-catalog-button").mouseover(function (e) {
        var jsCatalogBtnMask = function(){
            showMask();
            $('.main-nav_inner, .main-nav_index').toggleClass("open-desktop");
            $('.main-nav_inner, .main-nav_index').mouseover(function () {
                $('.main-nav_inner, .main-nav_index').addClass("open-desktop");
            });
            $('.main-nav_inner, .main-nav_index').addClass("open-desktop");
        }
        if(bMainPage && !hoveredOnJsNavLink){
            btnCatalogOverTimeout = setTimeout(function(){
                jsCatalogBtnMask();
                hoveredOnJsNavLink = true;
            }, 300);
        } else {
            if('hoveredOnJsNavLinkTimeout' in window){
                clearTimeout(hoveredOnJsNavLinkTimeout);
            }
            btnCatalogOverTimeout = setTimeout(function(){
                jsCatalogBtnMask();
            }, 300);
            stillDesktopMenu(); 
        }
    });

    $(".js-catalog-button").mouseout(function () {
        hideDesktopMenu();
        hideMask();
        if('btnCatalogOverTimeout' in window){clearTimeout(btnCatalogOverTimeout);}
    });

    $('.rbs-main-nav-desktop-ul').on('mouseenter', function(){
        if(bMainPage){
            hoveredOnRbsUlMainNav = setTimeout(function(){
                showMask();
            }, 300);
        } else {
            hoveredOnRbsUlMainNav = setTimeout(function(){}, 300);
            showMask();
            stillDesktopMenu();
        }    
        setTimeout(() => {
            !!window.menuHeight || (window.menuHeight = $(this).height());
        }, 1);
    });

    $('.rbs-main-nav-desktop-ul').on('mouseleave', function(){ 
        if(bMainPage){
            hoveredOnJsNavLinkTimeout = setTimeout(function(){
                hoveredOnJsNavLink = false;
                hideMask();
            }, 100);
            clearTimeout(hoveredOnRbsUlMainNav);
        } else {
            hoveredOnJsNavLinkTimeout = setTimeout(function(){}, 100);
            hideMask();
            hideDesktopMenu();
        }
    });

    $('.rbs-li-lvl-2').on('mouseenter', function(){
        setTimeout(() => {
            $thisBlock = $(this).find('.rbs-ul-lvl-3');
            if(!$thisBlock.hasClass('resized')){
                $maxWidth = 0;
                $thisBlock.find('.inner-nav__text').each(function(){
                    $maxWidth = $(this).width() > $maxWidth ? $(this).width() : $maxWidth;
                });
                $nominalWidth = 0;
                if($maxWidth + 45 > 210){
                    $nominalWidth = ($maxWidth - 210) + 40;
                    $thisBlock.css({
                        width: $thisBlock.width() + $nominalWidth + 'px'
                    });
                }
                $thisBlock.addClass('resized');
            }
            if($thisBlock.height() > $('.rbs-main-nav-desktop-ul').height()){
                $('.rbs-main-nav-desktop-ul').height($thisBlock);
            }
        }, 5);
    }); 

    $('.rbs-li-lvl-2').on('mouseleave', function(){
        window.menuHeight == $('.rbs-main-nav-desktop-ul').height() || $('.rbs-main-nav-desktop-ul').height(window.menuHeight);
    });

    $(".js-nav-link").on("click", function (e) {
        if (!!$(this).closest(".rbs-main-catalog-menu").length && !$(this).hasClass("active")) {
            $('.rbs-main-catalog-menu .inner-nav__head .inner-nav__title').text($(this).text());
        }
        if(!!$(this).siblings('#rbs-compare-list-mobile').length || !!$(this).siblings('#rbs-favorite-list-mobile').length){
            $(".js-del-btn-comp-fav").on("click", function () {
                if($(this).data('type') == 'compare'){
                    RZB2.ajax.Compare.Delete($(this).data('product-id'));
                    $(this).closest(".js-remove").fadeOut(0, function () {
                        $(this).remove();
                    })
                } else if ($(this).data('type') == 'favorite') {
                    RZB2.ajax.Favorite.Delete($(this).data('product-id'));
                }
                
                
                return false;
            });
        }
        if(!!$(this).siblings('.js-nav-hide').find('.city-hide').length){
            $(this).siblings('.js-nav-hide').find('.city-hide').show();
        }
        RZB2.utils.initLazy($(this).closest('.js-nav-item'));
    })

    $('.js-nav-height').on('click', function(){
        setTimeout(() => {$('.main-nav_mobile .main-nav__list').height($(this).siblings('ul.js-nav-hide').height())}, 1);
    });
    /*RBS_CUSTOM_END*/

    // main navigation
    

    $(".js-nav-link").on("mouseenter", function () {
        var _this = this,
            checkJsNavLink = function(){
                if ($(".ios").length < 1) {
                    $(_this).closest(".js-nav-item").siblings(".js-nav-item").removeClass("active").find(".js-nav-hide").stop().fadeOut(0);                
                    $(_this).addClass("active").closest(".js-nav-item").siblings(".js-nav-item").find(".js-nav-link").removeClass("active");        
                    $(_this).siblings(".js-nav-hide").stop().fadeIn(0);
                }
            };

        if(bMainPage && !hoveredOnJsNavLink){
            jsNavLinkTimeout = setTimeout(function(){
                checkJsNavLink();
                hoveredOnJsNavLink = true;
            }, 300);
        } else {
            checkJsNavLink();
            stillDesktopMenu();
            jsNavLinkTimeout = setTimeout(function(){}, 0);
        }
    });

    $(".js-nav-item").on("mouseleave", function () {    
        var _this = this,
            checkJsNavItemLeave = function(){
                if ($(".ios").length < 1) {
                    $(_this).find(".js-nav-hide").stop().fadeOut(0);
                    $(_this).find(".js-nav-link").removeClass("active");
                }
            };

        if(bMainPage){clearTimeout(jsNavLinkTimeout);}
        checkJsNavItemLeave();
    });

    $(".js-nav-item .js-nav-hide").on("mouseover", function(){stillDesktopMenu();});
    $(".js-nav-item .js-nav-hide").on("mouseleave", function(){hideDesktopMenu();});

    $(".js-nav-link").on("click", function (e) {
        if ($(".ios").length > 0) {
            if ($(this).hasClass("active")) {
                $(this).removeClass("active");
                $(this).siblings(".js-nav-hide").stop().fadeOut(0);
            } else {
                $(this).closest(".js-nav-item").siblings(".js-nav-item").removeClass("active").find(".js-nav-hide").stop().fadeOut(0);
                $(this).addClass("active").closest(".js-nav-item").siblings(".js-nav-item").find(".js-nav-link").removeClass("active");
                $(this).siblings(".js-nav-hide").stop().fadeIn(0);
            }
        }    
        //e.preventDefault();
    })

    $(".main-nav_mobile .js-click-close").on("click", function () {
        $('.main-nav_inner, .main-nav_index').removeClass("open");
        $('.js-catalog-button_mob').removeClass("active");
        $('html').removeClass("menu-open");
        toggleMobileHead('show');
    });


    $(".js-nav").on("mouseover", function () {
        $(this).addClass("active");
        $("#mask").stop().fadeIn(0);
    });

    $(".js-nav").on("mouseleave", function () {
        $(this).removeClass("active");
        $("#mask").stop().fadeOut(0);
    });
    // main navigation

    // js fade
    $(".js-fade-button").on("mouseenter", function () {
        if ($(".ios").length < 1) {
            if ($(this).hasClass("active")) {
                return;
            } else {
                $(".js-fade-hide, .js-click-hide").stop().fadeOut(0);
                $(".js-fade-button, .js-click-button").removeClass("active");
                $(this).parents(".js-fade").find(".js-fade-hide").stop().fadeIn(0);
                $(this).removeClass("fixed");
                $(this).addClass("active");
            }
        }
    });

    $(".js-fade-button").on("click", function () {
        if ($(".ios").length > 0) {
            if ($(this).hasClass("active")) {
                $(this).removeClass("active");
                $(this).parents(".js-fade").find(".js-fade-hide").stop().fadeOut(0);
                if($(this).hasClass('login__button')) toggleMobileHead('show', 'header .header-bottom');
            } else {
                $(this).addClass("active");
                $(this).parents(".js-fade").find(".js-fade-hide").stop().fadeIn(0);
                if($(this).hasClass('login__button')) toggleMobileHead('hide', 'header .header-bottom');
            }
        }
    });

    $(".js-fade-out").on("mouseleave", function () {
        $(".js-fade-hide").stop().fadeOut(0);
        $(".js-fade-button").removeClass("active");
    });

    $(".js-fade-close").on("click", function () {
        $(".js-fade-hide").stop().fadeOut(0);
        $(".js-fade-button").removeClass("active");
        if($(this).hasClass('login-hide__close')) toggleMobileHead('show', 'header .header-bottom');
    });
    // js fade  

    //удаление в сравнении
    $('.catalog-page, .catalog').on('click', '.catalog-item .compare-delete-item', function(e){
        e.preventDefault();
        var compareButton =  $(this).closest('li.catalog-item').find('button.action.compare');
        compareButton.data('delete', true);
        compareButton.click();
        $(this).closest('.compare-tooltip').addClass('hide');
    });

    $(".js-click-back").on("click", function () {
        $(this).closest(".js-nav-hide").fadeOut();
        $('nav.main-nav_index>ul.main-nav__list>li>a').removeClass('active');
        if($(this).hasClass('normalize-height'))
            $('.main-nav_mobile .main-nav__list').css('height', 'auto');
    });
    
    $('.js-click-back-catalog').on('click', function(){
        var activeLink = $('.rbs-main-catalog-menu .js-nav-link.active'),
            activeSubMenu = $('.rbs-main-catalog-menu .js-nav-link.active + ul.js-nav-hide');
                
        if(activeLink.length){
            activeSubMenu.last().fadeOut();
            activeLink.last().removeClass('active');
        } else {
            $(this).closest('.js-nav-hide').fadeOut();
            $('nav.main-nav_index>ul.main-nav__list>li>a').removeClass('active');
        }
        
        var headerText = $('.rbs-main-catalog-menu .js-nav-link.active').first().text().trim();
        if(!headerText.length) headerText = $('.main-nav__link_ctg .main-nav__text').text();
        $('.rbs-main-catalog-menu .inner-nav__head .inner-nav__title').text(headerText);
    });

    $(".js-review-more").on("click", function () {

        if ($(this).hasClass("active")) {
            $(".js-review-content").animate({
                'height': '250px'
            });
            $(this).removeClass("active").closest(".js-review").find(".js-review-content").removeClass("opened");
    
        } else {
            var h = $(".js-review-content").height();
            
            $(this).addClass("active").closest(".js-review").find(".js-review-content").addClass("opened");
            $(".js-review-content").animate({
                'height': h + 10 + 'px'
            });
        }
    });

    // reviews sliders
    if ($(".js-slider-4").length) {        
        $('.js-slider-4').on('init', function(e, slick){if ($('.reviews .response-1__content').length) {$('.reviews .response-1__content').shave(17 * 5);}});
        $('.js-slider-4').slick({adaptiveHeight:!0,dots:!0,arrows:!0,infinite:!0,autoplay:!1,speed:300,slidesToShow:1,slidesToScroll:1,adaptiveHeight:!0,prevArrow:$(".js-prev-4"),nextArrow:$(".js-next-4"),appendDots:$(".js-dots-1")});
    }

    setTimeout(function () {
        if ($('.js-ellip-2 span').length){
            if(!$('html').hasClass('iphone')){
                $('.js-ellip-2 span').shave(72);
            } else {
                $('.js-ellip-2 span').shave(84);
            }           
        }
    }, 300);
    // rating
    if ($(".js-rating").length) {$('.js-rating').barrating({showSelectedRating: false,readonly: true});}

    
    $(".js-click-close, .js-city-select").live("click", function (e) {
        e.preventDefault();
        e.stopPropagation();
        
        $(".js-click-hide").stop().fadeOut(0);
        $(".js-click-button").removeClass("active");
        $(".mask-inner").hide();
        $(".rbs-mask").hide();
        $('.city-hide').css({'display':'none'});
        $('html').removeClass('no-scroll');
    });
    // js fade click            

    // tabs
    $(".js-tabs-link").on("click", function (e) {
        e.preventDefault();
        if ($(this).closest(".js-tabs-item.active").length < 1) {
            $(this).closest(".js-tabs-item").addClass("active").siblings(".js-tabs-item").removeClass("active");
            var curHref = $(this).attr("href");
            $(curHref).addClass("active").siblings(".js-tabs-content").removeClass("active");
        }
    })

    $(".js-go-tab-1").on("click", function () {
        $(".js-tab-1").trigger("click");
    })
    $(".js-go-tab-2").on("click", function () {
        $(".js-tab-2").trigger("click");
    })
    // tabs 

    
    /* tabs */
    $('.tabs li a').click(function () {
        $(this).parents('.tab-wrap').find('.tab-cont').addClass('hide');
        $(this).parent().siblings().removeClass('active');
        var id = $(this).attr('href');
        $(id).removeClass('hide');
        $(this).parent().addClass('active');
        //$(".responses-2").slick("resize");
        return false;
    });

    $('.js-tabs-carousel').on('mouseover', function (e) {
        e.preventDefault();
        $('.tabs li a').click(function () {
            $(this).parents('.tab-wrap').find('.tab-cont').addClass('hide');
            $(this).parent().parents().find(".nav-tab-list__item.active").removeClass("active");
            var id = $(this).attr('href');
            $(id).removeClass('hide');
            $(this).parent().addClass('active');
            return false;
        });
    });

     

    $('.js-scroll-id').click(function (e) {
        e.preventDefault();
        e.stopPropagation();
    
        var defFixPos = 110;
        
        var target = $(this).attr('href');
        if($(target).length){
            if($('.nav-tab-list__link').length){
                $('.nav-tab-list__link').parent().removeClass("active");
                $('a[href="' + target + '"].nav-tab-list__link').trigger('click');
            }
            if($(target).hasClass('accordion__item')){
                
                var callback = function(){
                    var item = $(target);
                    $(".accordion__item").removeClass("open").removeClass("active");
                    $(".accordion__item").find('.accordion__text').hide().removeClass("open");
                    item.find(".accordion__arrow").addClass("rotate");
                    item.addClass("active");
                    item.find(".accordion__text").show().addClass("open");
                    var sections = item.find('.js-accessories-nav__link');
                    if(sections.length == 1){
                        sections.click();
                    }
                    $(".accordion__text.open").click(function (e) {
                        e.stopPropagation();
                    });
                };
    
                if(target == '#tab_3_mobile'){
                    $tabAccess.template='sib_detail_list_tab_mobile';
                    $tabAccess.getFullTab(callback);
                } else if(target == '#tab_4_mobile'){
                    $tabServices.template='sib_detail_list_tab_mobile';
                    $tabServices.getFullTab(callback);
                } else {
                    callback();
                }
                
                defFixPos = 60;
            }
            
            $('html, body').animate({
                scrollTop: $(target).offset().top - defFixPos
            }, 900);
        }   
        return false;
    });

    var mScrollBarDefConfigItems = { horizontalScroll: true, scrollButtons: { enable: false }, mouseWheel: { enable: true }, advanced: { updateOnContentResize: true }, advanced: { updateOnBrowserResize: true } };
    if ($(".js-items").length) { $(".js-items").mCustomScrollbar( { horizontalScroll: false, scrollButtons: { enable: false }, mouseWheel: { enable: true }, advanced: { updateOnContentResize: true }, advanced: { updateOnBrowserResize: true } }) }
    if ($(".js-scroll-accessories").length) { $(".js-scroll-accessories").mCustomScrollbar(mScrollBarDefConfigItems) }

     //accordion
     $(".accordion__item").on("click", '.accordion__heading', function () {
        var item = $(this).closest('.accordion__item');
        $(".accordion__item").removeClass("open").removeClass("active");
        item.find(".accordion__arrow").toggleClass("rotate");
        item.addClass("active");
        item.find(".accordion__text").slideToggle().toggleClass("open");
        $(".accordion__text.open").click(function (e) {
            e.stopPropagation();
        });
    });

    $(".accessories-nav__arr").on('click', function (e) {
        e.preventDefault();
        $(this).toggleClass("active");
        $('.accessories-nav__inner').fadeToggle();
    });
    $('.accessories-nav__inner .accessories-nav__link').on('click', function (e) {
        e.preventDefault();
        var title = $(this).find('.accessories-nav__title').text();
        var cnt = $(this).find('.accessories-nav__count').text();

        $('.js-accessories-nav ').find('.accessories-nav__title').text(title);
        $('.js-accessories-nav ').find('.accessories-nav__count').text(cnt);
    });

    
    BX.addCustomEvent('rbsBeforeAddToBasket', function(a, b){
        if(window.isMobile){
            $.fancybox.open($('#modal_add_to_cart'));
        }
    });

    initCompareTable();
}

if (typeof window.frameCacheVars !== "undefined"){BX.addCustomEvent("onFrameDataReceived", function (json){initOnFrameLoaded();});} else {$(document).ready(function(){initOnFrameLoaded();});}
/*----------begin doc ready----------*/
$(document).ready(function () {
    checkBrowserFix();
    
     // main slider
     if ($(".js-slider-1").length) {
        $(".js-slider-1").slick({dots:!0,infinite:!0,autoplay:!0,autoplaySpeed:5e3,speed:700,cssEase:"linear",slidesToShow:1,slidesToScroll:1,pauseOnHover:!0});
    }
    // main slider
    
    $('.catalog-page, .catalog').on('mouseleave', '.catalog-item .action.compare', function(e){if ($(this).hasClass('clicked')) $(this).removeClass('clicked');});
   
    $( ".search-form-mob__input" ).focus(function() {
        $(".search-form-cnt").addClass("active");
        $('.search-form-mob__input').off().keypress(function (e) {
            var key = e.which;
            if(key == 13 && $('.search-form-mob__input').val() != ''){
                $('.search-form-cnt [type="submit"]').click();
            }
        });  
    });

    $('.filter-top .filter-list__link').on('click', function(e){
        e.preventDefault();
        $('.filter-top .filter-list .filter-list__item:nth-child(n+9)').fadeIn().css("display","inline-block");
        $(this).hide();
    });
    var filterItems = $('.filter-top .filter-list .filter-list__item').length;
    if (filterItems && filterItems < 10)
        $('.filter-top .filter-list .filter-list__item:last-child').hide();

    var mScrollBarDefConfig = { horizontalScroll: true, scrollButtons: { enable: false }, mouseWheel: { enable: false }, advanced: { updateOnContentResize: true }, advanced: { updateOnBrowserResize: true } };
    if ($(".js-catalog__list").length) { $(".js-catalog__list").mCustomScrollbar(mScrollBarDefConfig) }
    
    // placeholder
    $("input, textarea").each(function () {
        var a = $(this).attr("placeholder");
        $(this).focus(function () {
            $(this).attr("placeholder", "")
        }), $(this).focusout(function () {
            $(this).attr("placeholder", a)
        })
    });
    // placeholder

   
    //filter
    $('.presence-list__link').click(function () {
        $(this).parent().prevAll().find(".presence-list__link").removeClass('active');
        $(this).parent().nextAll().find(".presence-list__link").removeClass('active');
        $(this).toggleClass('active');
    });

    $('.catalog-page').on('click', '.js-filters', function (e) {
        e.preventDefault();
        $(this).toggleClass('open');
        if(window.isMobile)
            $('html').toggleClass('no-scroll');
        $('.category, .filter-top').fadeToggle();
        var h = $('.filter-top').height();
        var hc = $('.category').height();
        $('.category').toggleClass('open');
        $('header').toggleClass('hide');
        
    });
    $('.rbs-close-filter-btn, .show-results').on('click', function(){
        $('.js-filters').click();
    })

    $('.catalog-item__overlay .js-close-loc').live('click', function (e) {
        e.preventDefault();
        $('#basket .js-remove-button[data-product-id="' + $(this).data('product-id') + '"]').click();
    });
 
    
    $('.catalog-item_remove').click(function (e) {
        e.preventDefault();
        $(this).closest('.catalog-item').fadeOut(500);
    });

    // placeholder text
    $(".js-focus-fix").on("click", function () {
        $(this).closest(".js-focus").find("textarea, input").focus();
    });

    if($('.js-tabs-carousel').length && !$('.box-tab-cont #tab_1').length){
        $('a[href="#tab_2"]').parents('.tab-wrap').find('.tab-cont').addClass('hide');
        $('a[href="#tab_2"]').parent().parents().find(".nav-tab-list__item.active").removeClass("active");
        var id = $('a[href="#tab_2"]').attr('href');
        $(id).removeClass('hide');
        $('a[href="#tab_2"]').parent().addClass('active');
    }

    
    // more seo
    $(".js-seo-more").on("click", function () {
        //console.log('js-seo-more-main');
        if ($(this).hasClass("active")) {
            $(this).removeClass("active").closest(".js-seo").find(".js-seo-content").removeClass("opened");
        } else {
            $(this).addClass("active").closest(".js-seo").find(".js-seo-content").addClass("opened");
        }
    });

    //RZB2.utils.initLazy($("img.lazy"));
    // more seo
    //$('.more-catalog').css({'pointer-events':'all'});
});

// close location
$(".js-close-loc").on("click", function () {
    $("#mask, .location, .rbs-mask").fadeOut(300, function () {
        $("body").removeClass("geotarget");
    });
});

$('.rbs-close-mob-btn').on('click', function(){
    $('html').trigger('click');
    toggleMobileHead('show');
});

$('html').click(function () {
    $('.main-nav_inner, .main-nav_index').removeClass("open");
    $('.js-catalog-button_mob').removeClass("active");
    $('html').removeClass("menu-open");

    $('.main-nav_inner, .main-nav_index').removeClass("open");
    $('.search-form-cnt').removeClass("active");
});

$(window).on('resize orientationchange', function(){
    if ($('.main-nav_mobile').css('display') === 'none'){ $('.mask-inner').hide() }
    resizeBasketItems();
});
// js fade click


var onBasketBut = false;	//Курсор на кнопке
var onBasketPan = false;	//Курсор на панели
var timeOutForOver = 300;
var basketHoverTimeout, compareHoverTimeout, watchedHoverTimeout;
function isBasketOpen(e){	//Скрыть панель
    if ((!onBasketBut) && (!onBasketPan)){
        e.preventDefault();    
        $(".js-fade-hide, .js-click-hide").not('.login-hide, .city-hide').stop().fadeOut(0);
        $(".js-fade-button, .js-click-button").not('.login__button, .city__selected').removeClass("active");
        $(".mask-inner").hide();
    }
}
function hideFade(_this){
    $(".js-fade-hide, .js-click-hide").fadeOut(0);
    $(".js-fade-button, .js-click-button").removeClass("active");
    _this.parents(".js-click").find(".js-click-hide").stop().fadeIn(0);
    _this.addClass("active");
    $(".mask-inner").show();
    RZB2.utils.initLazy($(".js-fade-hide, .js-click-hide"));
}
function rbsHoversBtn(){
    /* RBS-CUSTOM-START */
    /*Ховеры на панели в шапке*/
    
    $(".header-basket__button").on("mouseover", function (e) {
        e.preventDefault();    
        _this = $(this);
        basketHoverTimeout = setTimeout(function(){
            hideFade(_this);
            onBasketBut = true;
        }, timeOutForOver);
        
    })
    $(".header-basket__button").on("mouseout", function (e) {
        onBasketBut = false;
        setTimeout(isBasketOpen, 500, e);
        clearTimeout(basketHoverTimeout);
    })
    $(".basket-hide").on("mouseover", function (e) {
        onBasketPan = true;
    })
    $(".basket-hide").on("mouseout", function (e) {
        onBasketPan = false;
        setTimeout(isBasketOpen, 500, e);
    })
    $(".header-basket__button").on("click", function (e) {
        window.location.href = "/personal/cart/";
    })
    $(".compared__button").on("mouseover", function (e) {
        e.preventDefault();    
        _this = $(this);
        compareHoverTimeout = setTimeout(function(){
            hideFade(_this);
            onBasketBut = true;
        }, timeOutForOver);
        
    })
    $(".compared__button").on("mouseout", function (e) {
        onBasketBut = false;
        setTimeout(isBasketOpen, 500, e);
        clearTimeout(compareHoverTimeout);
    })
    $(".compared__button:not(.favorite-mode)").on("click", function (e) {
        if ($('#rbs-compare-list .basket-item').length > 0){
            window.location.href = "/catalog/compare.php";
        }
    })
    $(".watched__button").on("mouseover", function (e) {
        e.preventDefault();    
        _this = $(this);
        watchedHoverTimeout = setTimeout(function(){
            hideFade(_this);
            onBasketBut = true;
        }, timeOutForOver);
        
    })
    $(".watched__button").on("mouseout", function (e) {
        onBasketBut = false;
        setTimeout(isBasketOpen, 500, e);
        clearTimeout(watchedHoverTimeout);
    })
    $(".watched__button").on("click", function (e) {
        if ($('#rbs-compare-list .basket-item').length > 0){
            window.location.href = "/catalog/";
        }
    })
    $(".rbs-basket-hide").on("mouseout", function (e) {
        onBasketPan = false;
        setTimeout(isBasketOpen, 500, e);
    })

    $(".js-click-button.favorite-mode").off('click');
    /* RBS-CUSTOM-END */
}
//rbsHoversBtn(); call in RZB2.ajax.Viewed.Load /backend/ajax/core.js


// remove item
$(".js-remove-button").live("click", function () {
    console.log('test');
    /*RBS_CUSTOM_START*/
    if(!!$(this).closest("#basket").length){
        RZB2.ajax.BasketSmall.Delete($(this).data('basket-id'), $(this).data('product-id'));
    }
    if(!!$(this).closest('#rbs-compare-list').length){
        RZB2.ajax.Compare.Delete($(this).data('product-id')); 
    }
    if(!!$(this).closest('#rbs-favorite-list').length || !!$(this).closest('#rbs-favorite-list-mobile').length){
        RZB2.ajax.Favorite.Delete($(this).data('product-id'));
    }
    /*RBS_CUSTOM_END*/
    $(this).closest(".js-remove").fadeOut(0, function () {
        $(this).remove();
    })
    return false;
});
// remove item  


// service slider
/* $(".services__list .js-open-service").on("click", function () {
    var curData = parseInt($(this).attr("data-item")) - 1;
    $(".js-services").stop().fadeIn(500);
    $('.js-slider-6').slick({
        dots: false,
        infinite: true,
        autoplay: false,
        speed: 500,
        slidesToShow: 1,
        slidesToScroll: 1,
        touchThreshold: 200,
        initialSlide: curData,
        adaptiveHeight: true,
        prevArrow: $('.js-prev-6'),
        nextArrow: $('.js-next-6')
    });
})

$(".js-close-service").on("click", function () {
    $(".js-services").stop().fadeOut(0, function () {
        $('.js-slider-6').slick('unslick');
    });
}) */
// service slider

// select city
$(".js-city-select").on("click", function () {
    var selectedCity = $(this).text();
    $(".js-city-selected").text(selectedCity);
})
// select city

/*----------begin win load----------*/
$(window).load(function () {

    if(!window.isIOS){
        $('.rbs-hor-catalog').not('.rbs-ajax-cmp').removeClass('rbs-hor-catalog');
        $('.rbs-hor-catalog__list').not('.rbs-ajax-cmp').removeClass('rbs-hor-catalog__list');
    } else {
        $('.rbs-hor-catalog__list').css({'overflow-x':'scroll'});
    }

    // instagram slider
    if ($(".js-slider-3").length) {
        if(!window.isIOS){
            $('.js-slider-3').slick({dots:!0,arrows:!0,infinite:!0,autoplay:!1,swipeToSlide:!0,slidesToShow:6,slidesToScroll:1,touchThreshold:200,speed:300,adaptiveHeight:!0,responsive:[{breakpoint:1400,settings:{slidesToShow:5}},{breakpoint:1150,settings:{slidesToShow:4}},{breakpoint:1023,settings:{slidesToShow:4,dots:!1,arrows:!1}},{breakpoint:640,settings:{slidesToShow:2,dots:!1,arrows:!1}}]});
        } else {
            $('.rbs-hor-catalog__list .instagram-item__link').lazyload({data_attribute: 'lazy-jpg',data_attribute_webp: 'lazy',container: '.rbs-hor-catalog__list.js-slider-3'});
        }
    }
    // instagram slider 
    $('.js-prev-4').click(function () {$('.js-slider-4').slick('slickPrev');})
    $('.js-next-4').click(function () {$('.js-slider-4').slick('slickNext');})


    if ($(".js-slider-7").length) {
        $(".js-slider-7").each(function(){
            if(!(window.screen.width <= 640 && $(this).find('.catalog-item').length <= 1)){
                $(this).slick({dots:!0,arrows:!0,infinite:!0,autoplay:!1,speed:300,slidesToShow:6,slidesToScroll:1,adaptiveHeight:!0,responsive:[{breakpoint:1420,settings:{slidesToShow:5}},{breakpoint:1355,settings:{slidesToShow:4}},{breakpoint:1145,settings:{slidesToShow:3}},{breakpoint:1023,settings:{slidesToShow:4}},{breakpoint:640,settings:{slidesToShow:1}}]});
            } else {
                $(this).find('.placeholder').lazyload({
                    data_attribute  : "lazy-jpg",
                    data_attribute_webp  : "lazy"
                });
            }
        });
        
    }
    
    if ($(".js-slider-8").length) {
        var shaveVideoTitle = function($container, limit){
            $container.find('.video-item__title.js-ellip-2').shave(limit);
            $container.find('.js-ellip-2').css({
                'max-height':'auto',
                'min-height':'auto',
                'overflow':'auto'
            });
        }
        if(!window.isIOS){
            $(".js-slider-8").on('init', function(){shaveVideoTitle($(this), 60)});
            $(".js-slider-8").slick({dots:!0,arrows:!0,infinite:!0,autoplay:!1,swipeToSlide:!0,slidesToShow:5,slidesToScroll:1,touchThreshold:200,speed:300,adaptiveHeight:!0,responsive:[{breakpoint:1400,settings:{slidesToShow:4,dots:!0,arrows:!0}},{breakpoint:1150,settings:{slidesToShow:3,dots:!0,arrows:!0}},{breakpoint:1023,settings:{slidesToShow:3,dots:!1,arrows:!1}},{breakpoint:640,settings:{slidesToShow:1,dots:!1,arrows:!1}}]});
        } else {
            $('.rbs-hor-catalog__list .video-item__image').lazyload({
                data_attribute: 'lazy-jpg',
                data_attribute_webp: 'lazy',
                container: '.rbs-hor-catalog__list.js-slider-8'
            });
            shaveVideoTitle($('.rbs-hor-catalog__list'), 50);
        }
    }
    
    if ($('.js-copy-from').length && !window.isIOS) {
            $(".js-copy-from").slick({dots:!1,arrows:!0,infinite:!1,autoplay:!1,speed:400,slidesToShow:3,slidesToScroll:1,responsive:[{breakpoint:6e3,settings:"unslick"},{breakpoint:1023,settings:"slick"},{breakpoint:640,settings:{slidesToShow:1}}]});
    }
    
    if ($(".js-slider-2").length) {
        if(!window.isIOS){
            $(".js-slider-2").slick({dots:!0,arrows:!0,infinite:!1,autoplay:!1,swipeToSlide:!0,slidesToShow:5,slidesToScroll:1,touchThreshold:200,speed:300,adaptiveHeight:!0,/* lazyLoad:'ondemand', */responsive:[{breakpoint:1400,settings:{slidesToShow:4}},{breakpoint:1150,settings:{slidesToShow:3}},{breakpoint:1023,settings:{slidesToShow:3}},{breakpoint:700,settings:{slidesToShow:2}},{breakpoint:480,settings:{slidesToShow:1}}]});
        } else {
            $(".js-slider-2").each(function(){
                $(this).find('img').lazyload({
                    data_attribute: 'lazy-jpg',
                    data_attribute_webp: 'lazy',
                    $container: $(this)
                });
            })            
        }        
    }

    if ($(".js-styled").length) {$(".js-styled").styler({});}
    if ($(".js-fancybox").length) {$(".js-fancybox").fancybox({'beforeClose': function () {$("body").removeClass("popup-open");}});}
    if ($(".js-fancybox-video").length) {
        $(".js-fancybox-video").fancybox({
            'beforeClose': function () {
                $("body").removeClass("popup-open");
            },
            'beforeShow': function(e){
                e.$refs.container.addClass('rbs-video-main');
            },
            'afterShow': function(e){
                e.$refs.container.find('.fancybox-content').prepend(e.$refs.container.find('.fancybox-button--close'));
                e.$refs.container.find('.fancybox-button--close').css({'opacity': 1});
            }
        });
    }
    
    if ($(".js-fancybox-2").length) {
        $(".js-fancybox-2").fancybox({afterShow:function(){$("body").addClass("popup-open"),$(".js-card-img-slider-2").length&&($(".js-card-img-big-2").not(".slick-initialized").slick({slidesToShow:1,slidesToScroll:1,arrows:!1,fade:!0,asNavFor:".js-card-img-nav-2"}),$(".js-card-img-nav-2").not(".slick-initialized").slick({slidesToShow:4,slidesToScroll:1,vertical:!0,focusOnSelect:!0,asNavFor:".js-card-img-big-2",dots:!1}))},beforeClose:function(){$("body").removeClass("popup-open")}});
    }


    
    /*show-card-question*/
    $(".js-card-question").live('click', function () {
        $(".js-card-question").removeClass('show-question');
        $(this).toggleClass('show-question');
        return false;
    });

    /*show-answer*/
    $(".js-close-answer").click(function () {
        $(this).parents('.faq-list__row_answer').slideUp();
        return false;
    });
    $(".js-show-answer").click(function () {
        $(this).parents('.faq-list__item').find('.faq-list__row_answer').slideDown();
        return false;
    });
    /*show-answer*/

    /* scroll to top */
    $(".js-top").click(function () {
        $("html, body").animate({
            scrollTop: 0
        }, "slow");
        return false;
    });

    $('body').removeClass('loaded');   
});
/*----------win load eof----------*/

$(window).scroll(function () {
    if ($(window).scrollTop() > 250) {
        $('.js-top').addClass("up-fixed");
    } else {
        $('.js-top').removeClass("up-fixed")
    }
});

$(document).on('touchstart click', function (e) {
    if ($(e.target).parents().filter('.card-tooltipe:visible').length != 1) {
        $('.js-card-question').removeClass('show-question');
    }
});

/*----------begin bind load & resize & orientation eof----------*/
var handler1 = function () {setTimeout(function () {footersize();}, 1); setTimeout(function () {footersize();}, 500);}
$(window).bind('orientationchange', handler1);
$(window).bind('resize', handler1);
$(window).bind('load', handler1);
/*----------bind load & resize & orientation eof----------*/
/*----------begin bind load ----------*/
var handler2 = function ($container) {

    $container = $container || $(document);
    
    var formLen1 = $container.find("input[type=checkbox].js-formstyler").length;
    var formLen2 = $container.find("select.js-formstyler").length;
    var formLen3 = $container.find("input[type=radio].js-formstyler").length;
    if (formLen1 > 0 || formLen2 > 0 || formLen3 > 0) {
        $container.find('input[type=checkbox].js-formstyler').styler({});
        $container.find('input[type=checkbox].js-formstyler').on('change', function(){
            if($(this).is(':checked')){
                $(this).closest('.checkbox').addClass('active');
            } else {
                $(this).closest('.checkbox').removeClass('active');
            }
        });
        $container.find('select.js-formstyler').styler({});
        $container.find('input[type=radio].js-formstyler').styler({
            wrapper: '.radio'
        });
    }
}
$(window).bind('load', function(){handler2($(document))});
/*----------bind load----------*/
/*----------begin bind load & click----------*/
var handler3 = function () {
    // formstyler fix
    $(".radio-item").each(function () {
        var a = $(this).find(".jq-radio.checked").length;
        a >= 1 ? $(this).addClass("active") : $(this).removeClass("active")
    }), $(".radio-item").each(function () {
        var a = $(this).find(".jq-radio.disabled").length;
        a >= 1 ? $(this).addClass("disabled") : $(this).removeClass("disabled")
    }), $(".checkbox").each(function () {
        var a = $(this).find(".jq-checkbox.checked").length;
        a >= 1 ? $(this).addClass("active") : $(this).removeClass("active")
    }), $(".checkbox").each(function () {
        var a = $(this).find(".jq-checkbox.disabled").length;
        a >= 1 ? $(this).addClass("disabled") : $(this).removeClass("disabled")
    });
    // formstyler fix
}
//$(window).bind('click', handler3);
$(window).bind('load', handler3);
var slickedDetailTab = false;
/*----------bind load & click eof----------*/
var handler4 = function () {
    var viewport_wid = viewport().width;
    var viewport_height = viewport().height;
    if(viewport_wid <= 1023) {
		if($('.js-copy-from').length && !$(".ios").length) {
			$('.js-copy-from').slick("getSlick").refresh();
		};
    };
    if(viewport_wid < 992){
        if($('.js-tabs-carousel').length) {
            if(!slickedDetailTab){
                $(".js-tabs-carousel").slick({dots:!1,arrows:!0,infinite:!1,autoplay:!1,speed:300,slidesToShow:4,slidesToScroll:1,swipe:!0,swipeToSlide:!0,touchMove:!0,draggable:!0,variableWidth:!1,responsive:[{breakpoint:992,settings:{slidesToShow:4}}]});
                slickedDetailTab = true;
            } else {
                $('.js-tabs-carousel').slick("getSlick").refresh();
            }
			
		};
    } else if(slickedDetailTab) {
        $('.js-tabs-carousel').slick('unslick');
        slickedDetailTab = false;
    }
}
$(window).bind('load', handler4);
$(window).bind('resize', handler4);
var handler5 = function () {
    var viewport_wid = viewport().width;
    var viewport_height = viewport().height;
    if(viewport_wid <= 640) {
        $('.js-next-step').on('click', function(){
            $("html, body").animate({ scrollTop: 0 }, "slow");
        });  
         
    };
}
$(window).bind('load', handler5);
$(window).bind('resize', handler5);

/*----------begin touch----------*/
$(document).on('touchstart', function () {
    documentClick = true;
});
$(document).on('touchmove', function () {
    documentClick = false;
});
$(document).on('click touchend', function (event) {
    if (event.type == "click") documentClick = true;
    if (documentClick) {
        var target = $(event.target);
        
        if (target.is('.js-fade-out') || target.is('.js-fade-out *')) {
            return
        }
        if (target.is('.js-click') || target.is('.js-click *')) {
            return
        }
        if (target.is('.js-nav') || target.is('.js-nav *')) {
            return
        }
        if (target.is('.services') || target.is('.services *')) {
            return
        }
        /* if(target.closest('.fancybox-is-open').length){
            return
        } */
        if (target.is('.js-basket-hide') || target.is('.js-basket-hide *') || target.is('.js-basket-open') || target.is('.js-basket-open *')) {
            return
        } else {
            $(".js-fade-hide, .js-click-hide, .js-basket-hide").stop().fadeOut(0);
            $(".js-fade-button, .js-click-button, .js-basket-open").removeClass("active");
           
            $(".js-nav-hide").stop().fadeOut(0);
            $(".js-nav-link").removeClass("active");
            $(".js-nav").removeClass("active");
            $("#mask").stop().fadeOut(0);
            $('.rbs-mask').hide();
        }

        if($('header .header-bottom').css('display') == 'none')
            toggleMobileHead('show', 'header .header-bottom');
        
        $('html').removeClass('no-scroll');
    }
});
/*----------touch eof----------*/

// esc click
$(document).keyup(function (e) {
    if (e.keyCode == 27) {
        $("#mask, .location, .rbs-mask").fadeOut(300, function () {
            $("body").remove("geotarget");
        });
    }
});

function initCompareTable(){
    var compLen = ("compare-table").length;
    if (compLen > 0) {
        $(".compare-table .compare-table__item").css("height", "auto");
        for (var i = 1; i < 20; i++) {
            var height2 = 0;
            $('.js-compare-table_1 .compare-table__item:nth-child(' + i + ')').each(function () {
                height2 = height2 > $(this).height() ? height2 : $(this).height();
            });
            $('.js-compare-table_1 .compare-table__item:nth-child(' + i + ')').each(function () {
                $(this).css("height", height2 + "px")
            });
        }
        for (var i = 1; i < 20; i++) {
            var height2 = 0;
            $('.js-compare-table_2 .compare-table__item:nth-child(' + i + ')').each(function () {
                height2 = height2 > $(this).height() ? height2 : $(this).height();
            });
            $('.js-compare-table_2 .compare-table__item:nth-child(' + i + ')').each(function () {
                $(this).css("height", height2 + "px")
            });
        }
        for (var i = 1; i < 20; i++) {
            var height2 = 0;
            $('.js-compare-table_3 .compare-table__item:nth-child(' + i + ')').each(function () {
                height2 = height2 > $(this).height() ? height2 : $(this).height();
            });
            $('.js-compare-table_3 .compare-table__item:nth-child(' + i + ')').each(function () {
                $(this).css("height", height2 + "px")
            });
        }
        for (var i = 1; i < 20; i++) {
            var height2 = 0;
            $('.js-compare-table_4 .compare-table__item:nth-child(' + i + ')').each(function () {
                height2 = height2 > $(this).height() ? height2 : $(this).height();
            });
            $('.js-compare-table_4 .compare-table__item:nth-child(' + i + ')').each(function () {
                $(this).css("height", height2 + "px")
            });
        }
        for (var i = 1; i < 20; i++) {
            var height2 = 0;
            $('.js-compare-table_5 .compare-table__item:nth-child(' + i + ')').each(function () {
                height2 = height2 > $(this).height() ? height2 : $(this).height();
            });
            $('.js-compare-table_5 .compare-table__item:nth-child(' + i + ')').each(function () {
                $(this).css("height", height2 + "px")
            });
        }
        for (var i = 1; i < 20; i++) {
            var height2 = 0;
            $('.js-compare-table_6 .compare-table__item:nth-child(' + i + ')').each(function () {
                height2 = height2 > $(this).height() ? height2 : $(this).height();
            });
            $('.js-compare-table_6 .compare-table__item:nth-child(' + i + ')').each(function () {
                $(this).css("height", height2 + "px")
            });
        }
        for (var i = 1; i < 20; i++) {
            var height2 = 0;
            $('.js-compare-table_7 .compare-table__item:nth-child(' + i + ')').each(function () {
                height2 = height2 > $(this).height() ? height2 : $(this).height();
            });
            $('.js-compare-table_7 .compare-table__item:nth-child(' + i + ')').each(function () {
                $(this).css("height", height2 + "px")
            });
        }
        for (var i = 1; i < 20; i++) {
            var height2 = 0;
            $('.js-compare-table_8 .compare-table__item:nth-child(' + i + ')').each(function () {
                height2 = height2 > $(this).height() ? height2 : $(this).height();
            });
            $('.js-compare-table_8 .compare-table__item:nth-child(' + i + ')').each(function () {
                $(this).css("height", height2 + "px")
            });
        }
    }
}

// calc height in characteristic__table
//accordion compare-page
var accordionItem = document.body.querySelectorAll('.accordion__item_compare');

for (var i = 1; i <= accordionItem.length + 1; i++) {
    $('.main-block_compare').on("click", '.accordion__item_compare-' + i, function () {
        $(".accordion__item_compare").removeClass("open").removeClass("active");
        $(this).find(".accordion__arrow").toggleClass("rotate");

        //content slide
        var id = $(this).attr("data-id");
        $(id).toggleClass('catalog-item__compare_slide');
        //  

        $(this).addClass("active");
        $(this).find(".accordion__text").slideToggle().toggleClass("open");
        $(".accordion__text.open").click(function (e) {
            e.stopPropagation();
        });

    });
};
//accordion compare-page

$('.contacts-tabs-list li a').click(function () {
    $(this).parents('.contacts-info').find('.contacts-tabs-cont .contacts-tabs-cont__item').addClass('hide');
    $(this).parent().siblings().removeClass('active');
    var id = $(this).attr('href');
    $(id).removeClass('hide');
    $(this).parent().addClass('active');
    return false
});

//Personal Settings Page - checkbox

$('.js-dispatch').on('change', function () {
    $('.checkbox-dispatch').slideToggle();
});

//Personal Settings Page Menu
$('.personal-account-list__item').click(function () {
    $('.personal-account-list__item').removeClass('active');
    $(this).toggleClass('active');
});

//Datepicker
if ($(".js-datepicker").length) {
    $('.js-datepicker').datepicker({
        dateFormat: "dd.mm.yy",
        firstDay: 1,
        minDate: new Date($('#hiddendelivdate').val()),
        monthNames: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
        dayNamesMin: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
        dayNames: ['Воскресенье', 'Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота'],
        duration: "normal",
        showOtherMonths: true
    });
};
//Personal-data page
$('.box-field__icon').click(function () {
    $(this).parent().find("input").css('display', 'inline-block')
        .removeAttr('disabled')
        .val('')
        .focus();
    $(this).parent().find(".input-buffer").css("display", "none");

    $(this).css("display", "none");

    $(this).parent().find("input").focusout(function () {
        $(".box-field__icon").css("display", "inline-block");
        $(this).parent().find("input").attr('disabled', 'disabled');
        var text = $(this).parent().find(".input").val();
        $(this).parent().find(".input-buffer").text(text);
        $(this).parent().find(".input").css('display', 'none');
        $(this).parent().find(".input-buffer").css("display", "inline-block");
    });
});

$('.box-field__icon_date').click(function () {
    $(this).parent().find("input").removeAttr('disabled');
    $(this).parent().find("input").focus();
    $(this).parent().find("input").focusout(function () {
        $(this).parent().find("input").css("display", "block");
    });
});

window.addEventListener("load", function (event) {
    event.preventDefault();
    setTimeout(function () {
        if ($("img.lazy").length) {
            //$("img.lazy").lazyload();
            //RZB2.utils.initLazy($("img.lazy"));
            RZB2.utils.initLazy($(document));
        };
    }, 20);
});

if ($(window).width() <= '520') {
    $(".js-accessories-nav__link").on("click", function() {
        $(this).parent().siblings().find(".accessories-cont").slideUp();
        $(this).parent().find(".accessories-cont").slideToggle();
        return false;
    });    
}