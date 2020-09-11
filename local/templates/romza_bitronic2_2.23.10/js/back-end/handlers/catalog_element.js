function RZB2_initDetailHandlers($){

	
	$('[href="#tab_7"], .rbs-accordion__item-video').on('click', function(e){
		var tab = $($(this).attr('href')).length ? $($(this).attr('href')) : $(this);
		if(tab.find('.rbs-video-to-show').length){
			tab.find('.rbs-video-to-show').each(function(){
				if($(this).data('src') != ''){
					$('.rbs-video-tab-content').first().append($('<iframe class="rbs-video-iframe" src="'+$(this).data('src')+'" width="560" height="315" allowfullscreen></iframe>'));
				}
			});
			tab.find('.rbs-video-to-show').remove();
		}
		if(tab.find('.rbs-blog-detail-item div').length)
			tab.find('.rbs-blog-detail-item div').lazyload({}).removeClass('placeholder');
	}); 
	

	// CATALOG ELEMENT - EDOST
	$('.calc-delivery').on('click', function(e){
		var _ = $(this);
		e.preventDefault();
		edost_catalogdelivery_show(_.data('id'), _.data('name'));
	});
	var $edost = $('#edost_catalogdelivery_inside');
	if (typeof edost_RunScript == "function") {
		if (typeof YS == "object" && typeof YS.GeoIP == "object" && typeof YS.GeoIP.Cookie == "object") {
			var locID = parseInt(YS.GeoIP.Cookie.getLocationID('YS_GEO_IP_LOC_ID'));
			if (locID > 0) {
				edost_catalogdelivery.set_cookie('edost_location='+locID+'||');
			}
		}
		if ($edost.length > 0) {
			edost_RunScript('preview', $edost.data('id'), $edost.data('name'));
		}
	}

	var slickParamsModal = {
		big: {
			slidesToShow: 1,
			slidesToScroll: 1,
			arrows: true,
			fade: true,
			initialSlide: 0,
			asNavFor: '.js-slider-detail-modal-nav',
			infinite: true,
			adaptiveHeight: false,
			autoFocusArrows: true
		},
		thumbs: {
			slidesToShow: 5,
			slidesToScroll: 1,
			focusOnSelect: true,
			initialSlide: 0,
			asNavFor: '.js-slider-detail-modal',
			dots: false,
			arrows: false,
			positionFixed: true
		}
	};

	var slickParamsSlider = {
		big: {
            slidesToShow: 1,
            slidesToScroll: 1,
            arrows: false,
			fade: false,
			dots: false,
			autoFocusArrows: true
		},
		thumbs: {
            slidesToShow: 4,
            slidesToScroll: 1,
            vertical: true,
            focusOnSelect: true,
            asNavFor: '.js-card-img-big',
            dots: false
        }
	};

	

	if ($(".js-card-img-slider").length) {
        $('.js-card-img-big').on('init', function(event, slick){$('.js-card-img-big img').css({'opacity': '1'});});		
		$('.js-card-img-nav').on('init', function(event, slick){$('.js-card-img-nav').css({'opacity': '1'});});

		if(!Modernizr.mq('(max-width: 731px)')){
			if(!Modernizr.mq('(max-width: 1146px)')){
				$('.js-card-img-nav').slick(slickParamsSlider.thumbs);
				slickParamsSlider.big.asNavFor = '.js-card-img-nav';
			} else {
				$('.js-card-img-nav').remove();
				$('.js-card-img-big').addClass('dots-1').css({'overflow-x': 'hidden'});
				slickParamsSlider.big.dots = true;
			}
		} else {
			$('.js-card-img-nav').remove();
			$('.js-card-img-big').addClass('dots-1').css({'overflow-x': 'hidden'});
			slickParamsSlider.big.dots = true;

			var topNewPoint = $('.card-main__title').offset().top + $('.card-main__title').height() + 20;
			var additionalBottom = 130;
			if(!!$('.card-main-aside .economy').length){
				additionalBottom += 40;
			}
			var maxHeightImg = $(window).height() - topNewPoint - additionalBottom;
			$('.card-main__img img').css({'max-height': maxHeightImg});

			/* var paddingTopImg = $('.card-main__title').height() + 4;
			paddingTopImg = paddingTopImg >= 50 ? paddingTopImg : 50;
			$('.card-main__img').css({'padding-top': paddingTopImg + 'px'}); */
			/*var winHeight = $(window).height(),
				bottomPoint = winHeight - 50,
				topPoint = $('.card-img-big').offset().top + $('.card-main').offset().top,
				imgHeight = bottomPoint - topPoint;
			$('.card-main__img img').css({'max-height': imgHeight});*/
		}
        $('.js-card-img-big').slick(slickParamsSlider.big);
	};

	
	var fancyBindEvent = {
		baseClass: 'rbs-fancy-white',
		beforeShow: function(){
			//$('#popup-detail-gallery .popup__main').html('<div class="load_quick_view"><i class="icon-load"></i></div>');
		},
		afterShow: function () {
			$("body").addClass("popup-open");
			var currSlide = $('.js-card-img-big').slick('slickCurrentSlide') || 0;

			if(!$('#popup-detail-gallery .popup__main').data('inited')){
				slickParamsModal.big.initialSlide = currSlide;
				$('.js-slider-detail-modal img').css({'max-height': (viewport().height - 300) + 'px'});
				$('.js-slider-detail-modal').slick(slickParamsModal.big);
				$('.js-slider-detail-modal-nav').slick(slickParamsModal.thumbs);

				$('#popup-detail-gallery .card-main-price').html($('.card-main-aside .card-main-price').html());
				$('#popup-detail-gallery .card-main-price').find('.buy').on('click', function(){
					$('.card-main-aside .card-main-price .buy').click();
				})
				$('#popup-detail-gallery .card-main-delivery').html($('.card-main-aside .card-main-delivery').html());

				$('#popup-detail-gallery .popup__main').data('inited', true);
				//$(this).get()[0].opts.$orig
			} else {
				$('.js-slider-detail-modal').slick('refresh', slickParamsModal.big);
				$('.js-slider-detail-modal-nav').slick('refresh', slickParamsModal.thumbs);
				$('.js-slider-detail-modal-nav').slick('slickGoTo', currSlide);
			}
			$('.js-slider-detail-modal-nav').removeClass('rbs-invisible');
		},
		'beforeClose': function () {
			$("body").removeClass("popup-open");
			$('.js-slider-detail-modal-nav').addClass('rbs-invisible');
		}
	}

	$('.js-detail-gallery').fancybox(fancyBindEvent);

	
	if($('#detail_available').length){
		maskPhoneInit($('#detail_available [name="FIELDS[PHONE]"]'));
		$('#detail_available').on('submit', alertToAvailableHandler);
	}



	$(window).bind('resize', function(){
		if($('.js-card-img-big').length)
			$('.js-card-img-big').slick('refresh', slickParamsSlider.big);
		if($('.js-card-img-nav').length)
			$('.js-card-img-nav').slick('refresh', slickParamsSlider.thumbs);

		$('.js-detail-gallery').fancybox(fancyBindEvent);

		if($('.js-slider-detail-modal.slick-initialized').length){
			$('.js-slider-detail-modal img').css({'max-height': (viewport().height - 300) + 'px'});
			$('.js-slider-detail-modal').slick('refresh', slickParamsModal.big);
			$('.js-slider-detail-modal-nav').slick('refresh', slickParamsModal.thumbs);
		}

		RZB2.ajax.quickViewInit($('.js-fancybox-accessory'));
	});

	setTimeout(function(){
		if($('.rbs-find-img-detail-descr').length)
			$('.rbs-find-img-detail-descr').lazyload({}).removeClass('placeholder');
	}, 300);
	
}

if (typeof domReady != "undefined" && domReady == true) {
	RZB2_initDetailHandlers(jQuery);
} else {
	jQuery(document).ready( RZB2_initDetailHandlers );
}
