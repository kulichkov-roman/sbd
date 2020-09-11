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
window.isIOS = false;
if (/Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent)) {window.isIOS = true;}

var isLikesDisable = false;
var initLikes = function(target){
    target.find('.likes__rate').off().on('click', function(){

        if(!isAuthUser){
            $('#auth_modal').modal();
            return;
        }

        var parent = $(this).parent(),
            entity = parent.data('entity') || false,
            id = parent.data('id') || 0,
            currentRate = parent.data('current') || 0,
            countNode = parent.find('.likes__count'),
            isLike = $(this).hasClass('likes__up');
        
        if(isLikesDisable){
            $('#alert_modal').find('.entity').text('оценок');
            $('#alert_modal').modal();
        }
        
        if(entity === 'comment' || entity === 'item'){
            if(id > 0){
                $.ajax({
                    url: '/local/components/sibdroid/blog.rating/ajax.php',
                    data: {
                        entity: entity,
                        id: id,
                        sessid: BX.bitrix_sessid,
                        rate: isLike ? 1 : 0,
                        currentRate: currentRate
                    },
                    type: 'POST',

                    beforeSend: function()
                    {						
                        parent.addClass('loading');
                    },

                    success: function(data)
                    {				
                        var result = JSON.parse(data);
                        if(result.TYPE === 'OK'){
                            countNode.text(result.CNT);
                            parent.data('current', result.CNT);
                            parent.removeClass('neitral positive negative').addClass(result.RATING_CLASS);
                        }	
                        if(result.TYPE === 'ERROR_ACTIVE_SESSION'){
                            isLikesDisable = true;
                            $('#alert_modal').find('.entity').text('оценок');
                            $('#alert_modal').modal();
                        }
                        
                        parent.removeClass('loading');
                    }
                })
            }
        }
    });
};

var checkRatingClass = function(target){

    //if(!window.isAuthUser) return false;
    if(target.find('.js-like-check').length > 0){
        var likes = {
            item: [],
            comment: []
        };
        target.find('.js-like-check').each(function(){
            likes[$(this).data('entity')].push($(this).data('id'));
        });
        
        $.ajax({
            url: '/local/components/sibdroid/blog.rating/ajax.php',
            data: {
                check: likes,
                sessid: BX.bitrix_sessid
            },
            type: 'POST',

            beforeSend: function()
            {						
                target.find('.js-like-check').addClass('loading');
            },

            success: function(data)
            {				
                var result = JSON.parse(data);
                if(!!result && result.TYPE === 'OK'){
                    for(entity in result.ITEMS){
                        itemList = result.ITEMS[entity];
                        if(itemList.length > 0){
                            itemList.forEach(function(item){
                                var classRate = parseInt(item.RATE) === 1 ? 'positive' : 'negative';
                                $('.js-like-check[data-entity="' + entity + '"][data-id="' + item.ID + '"]').removeClass('neitral').addClass(classRate);
                            });
                        }
                    }

                    for(itemId in result.ITEM_COUNTS){
                        countInfo = result.ITEM_COUNTS[itemId];
                        $('.js-like-check[data-entity="item"][data-id="' + itemId + '"]').find('.likes__count').text(countInfo.LIKES);
                        $('.js-check-comment[data-entity="item"][data-id="' + itemId + '"]').find('a span').text(countInfo.COMMENTS);
                    }

                    if(!!$('.blog__item_headline__views').length){
                        for(itemId in result.SHOWS){
                            $('.blog__item_headline__views').text(result.SHOWS[itemId].SHOW_COUNTER);
                            console.log(result.SHOWS[itemId].SHOW_COUNTER);
                        }
                    }
                }                
                target.find('.js-like-check').removeClass('loading');
            }
        });
    }
};

$(document).ready(function(){
    
    $('.js-top-btn').on('click', function(e){
        $(this).find('.js-toggle').slideToggle(200);
    });
    
    initLikes($(document));
    if(!!$('.blog-list').length){
        checkRatingClass($(document));
    }
    
    var hashName = window.location.hash;
    if(hashName !== ''){
        window.location.hash = '';
        $('html, body').animate({scrollTop: $(hashName).offset().top}, 1000);
    }

    $('a.js-to-elem').on('click', function(e){
        e.preventDefault();
        $('html, body').animate({scrollTop: $($(this).attr('href')).offset().top}, 300);
    });

    if(!!$('.blog__item_share').length){
        $('.blog__item_share').on('click', 'a', function(e){
            e.preventDefault();
            
            var url = window.location.href.split('?').shift(),
                title = document.title,
                /* img = window.location.origin + $('.blog__item_prevdescr img').attr('src'), */
                href = $(this).attr('href').split('#URL#').join(url);
                
            window.open(href, this.target,'width= 300,height=300,scrollbars=1')
        });
    }

    if(!!$('.js-blog-list').length){

        $.fn.isInViewport = function() {
            var elementTop = $(this).offset().top;
            var elementBottom = elementTop + $(this).outerHeight();
            var viewportTop = $(window).scrollTop();
            var viewportBottom = viewportTop + $(window).height();
            return elementBottom > viewportTop && elementTop < viewportBottom;
        };

        $(window).on('scroll resize', function(){
            if($(".blog__item_last").length > 0 && $(".blog__item_last").isInViewport()){
                var nextPage = parseInt($(".blog__item_last").data('next-page')) || 0;
                
                if(nextPage > 0){
                    var nextPageParams = '?ajax=Y&PAGEN_1=' + nextPage;
                    if(window.location.search !== ""){
                        nextPageParams = '&ajax=Y&PAGEN_1=' + nextPage;
                    }
                    var url = window.location.href + nextPageParams;

                    $.ajax({
                        url: url,
                        type: 'GET',    
                        beforeSend: function()
                        {						
                            $(".blog__item_last").removeClass('blog__item_last');
                            $('.load-list-block').show();
                        },    
                        success: function(data)
                        {				
                            var result = $(data);
                            if(result.find('.js-blog-list').length){
                                $('.js-blog-list').append(result.find('.js-blog-list').html());
                                initLikes($('.js-blog-list'));
                                checkRatingClass($(document));
                                if($('.rbs-find-img-detail-descr').length){
                                    $('.rbs-find-img-detail-descr').lazyload({}).removeClass('placeholder');
                                }
                            }
                            $('.load-list-block').hide();
                        }
                    });
                }
            }
        });
    }

    if ($(".js-rating").length) {$('.js-rating').barrating({showSelectedRating: false,readonly: true});}

    setTimeout(function () {
        if ($('.js-ellip-2 span').length){
            if(!$('html').hasClass('iphone')){
                $('.js-ellip-2 span').shave(72);
            } else {
                $('.js-ellip-2 span').shave(84);
            }           
        }

        if($('.rbs-find-img-detail-descr').length){
            $('.rbs-find-img-detail-descr').lazyload({}).removeClass('placeholder');
        }
            
    }, 300);

    if(!!$('.js-slider-2').length){
        if ($(".js-slider-2").length) {
           // if(!window.isIOS){
                $(".js-slider-2").slick({dots:!1,arrows:!0,infinite:!1,autoplay:!1,swipeToSlide:!0,slidesToShow:2,slidesToScroll:1,touchThreshold:200,speed:300,adaptiveHeight:!0,/* lazyLoad:'ondemand', */responsive:[{breakpoint:1400,settings:{slidesToShow:2}},{breakpoint:1150,settings:{slidesToShow:2}},{breakpoint:1023,settings:{slidesToShow:2}},{breakpoint:992,settings:{slidesToShow:1}},{breakpoint:480,settings:{slidesToShow:1}}]});
           // } else {
               /*  $(".js-slider-2").each(function(){
                    $(this).find('img').lazyload({
                        data_attribute: 'lazy-jpg',
                        data_attribute_webp: 'lazy',
                        $container: $(this)
                    });
                })     */        
           // }        
        }
    }
});

if (typeof window.sib_blog == "undefined") {
	sib_blog = {utils: {}};
}

if (typeof sib_blog.utils == "undefined") {
	sib_blog.utils = {};
}

sib_blog.utils.cookiePrefix = 'RZ_';

sib_blog.utils.setCookie = function(name, value, prefix)
{
	var date = new Date();
	date.setFullYear(date.getFullYear() + 1);

	prefix = prefix || this.cookiePrefix;
	document.cookie = prefix + name + '=' + value + '; path=/; domain=sibdroid.ru; expires=' + date.toUTCString();
}

sib_blog.utils.getCookie = function(name, prefix)
{
	prefix = prefix || this.cookiePrefix;
	name = prefix + name;
	var matches = document.cookie.match(new RegExp(
		"(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
	))
	return matches ? decodeURIComponent(matches[1]) : undefined
}