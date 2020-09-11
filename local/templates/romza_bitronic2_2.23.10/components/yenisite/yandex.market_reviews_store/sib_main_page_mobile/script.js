(typeof(jQuery) != 'undefined')
&& jQuery(function ($) {	
	updateYRMS = function (page, path, count, call_url) {
		var data = [
			{'name': 'PAGE', 'value': page},
            {'name': 'URL', 'value': call_url},
            {'name': 'COUNT', 'value': count}
		];
		return $.ajax({
			url: path + "/ajax.handler.php",
			type: "POST",
			dataType: "html",
			data: data,
			success: function (msg) {
                var $container = $('#main-shop-reviews');
				$container.html(msg);
                setTimeout(function(){
                    if ($('.js-ellip-3').length) {
                        $('.js-ellip-3').shave(17 * 3);
                    }   
                    if ($('.js-ellip-4').length) {
                        $('.js-ellip-4').shave(17 * 6);
                    }                    
                    if ($(".js-slider-5").length) {
                        $('.js-slider-5').slick({
                            dots: true,
                            arrows: true,
                            infinite: true,
                            autoplay: false,
                            speed: 300,
                            slidesToShow: 1,
                            slidesToScroll: 1,
                            adaptiveHeight: true,
                            prevArrow: $('.js-prev-5'),
                            nextArrow: $('.js-next-5'),
                            appendDots: $(".js-dots-2")
                        });
                    }
                    if ($(".js-rating").length) { $('.js-rating').barrating({ showSelectedRating: false,  readonly: true }); }
    
                   /*  $('.js-prev-5').click(function () {$('.js-slider-5').slick('slickPrev');})
                    $('.js-next-5').click(function () {$('.js-slider-5').slick('slickNext');}) */
                }, 0);
			}
		});
	}
});
