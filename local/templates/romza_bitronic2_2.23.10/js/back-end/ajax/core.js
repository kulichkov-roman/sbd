var RZB2 = RZB2 || {ajax: {params: {}}};

if (typeof RZB2.ajax == "undefined") {
	RZB2.ajax = {params: {}};
}

$(window).on('b2ready', function(){
	var backEndJsList = ['back-end/handlers/commonsmin'];
	if (b2.s.quickView == "Y"
		&& (
			typeof b2.init.catalogPage == "function"
			|| (
				typeof b2.init.homePage == "function" &&
				b2.s.blockHomeSpecials == "Y"
			)
		)
	) {
		//backEndJsList.unshift('back-end/ajax/catalog_quick_view');
	}
	if (typeof b2.init.productPage == "function") {
		backEndJsList.push('back-end/handlers/catalog_element');
	}
	if (typeof b2.init.catalogPage == "function") {
		backEndJsList.push('back-end/handlers/catalog_section');
	}
	if (typeof b2.init.comparePage == "function" || typeof b2.init.comparePageMobile == "function") {
		backEndJsList.push('back-end/ajax/catalog_compare');
		backEndJsList.push('back-end/handlers/catalog_compare');
	}
	require(backEndJsList, function(){
		$(window).trigger('b2handlers');
	});
});
/* 
$(window).on('reload.GeoIPStore', function (callback) {
	$.ajax({
		url: SITE_DIR + 'ajax/sib/composite.php',
		complete: callback
	});
	// we will execute callback ourselves upon ajax completion
	return true;
});

$(window).on('redirect.GeoIPStore', function (callback, domain) {
	var $iframe = $('<iframe src="//'+domain+SITE_DIR+'ajax/sib/composite.php" style="display:none"></iframe>').on('load', callback);
	$('body').append($iframe);
	// we will execute callback ourselves on iframe loading
	return true;
}); */

RZB2.ajax.quickViewInit = function(target, fancySelector){
	fancySelector = fancySelector || ".js-fancybox-2";
	if (target.find(fancySelector).length){
		target.find(fancySelector).each(function(){
			if($(this).data('customPopup') === 'access_custom'){
				$(this).fancybox({
					beforeShow: function(){
						$('#popup-accessories .popup__main').html('<div class="load_quick_view"><i class="icon-load"></i></div>');
					},
					afterShow: function () {
						$('.fancybox-close-small').addClass('fixed-close-btn');
						var orig = $(this).get()[0].opts.$orig;
						ajaxPath = SITE_DIR + orig.data('ajax-url');
						var doAfterAjax = function(result){
							$("body").addClass("popup-open");
							if(result){
								if(!!$('#popup-accessories .popup__main').find('#hydrogel-window').length){
									$('#popup-accessories .popup__main #hydrogel-window').html($(result).find('#hydrogel-window').html()).css({paddingBottom:'19px'});
								} else {
									$('#popup-accessories .popup__main').html(result).css({paddingBottom:'19px'});
								}
								
								if ($('#popup-accessories .popup__main').find(".js-card-img-slider-2").length) {
									var mainSlider = $('#popup-accessories .popup__main').find('.js-card-img-big-2');
									mainSlider.off().on('afterChange', function(event, slick, currentSlide){
										var baseItem = $(this).find('[data-slick-index="'+currentSlide+'"]').find('li'),
											name = baseItem.data('name'),
											pid = baseItem.data('pid');
											$('#popup-accessories .rbs-buy').removeClass('rbs-in-cart');
											$('#popup-accessories .rbs-buy').data('pid', pid);
											
											if('hydroEls' in window){
												for(k in hydroEls){
													if(hydroEls[k]['ID'] == pid){
														$('#popup-accessories .rbs-white-button').attr('href', hydroEls[k]['DETAIL_PAGE_URL']);
													}
												}
											}
											if('arAddedHydroInBasket' in window){
												for(k in arAddedHydroInBasket){
													if(arAddedHydroInBasket[k] == pid){
														$('#popup-accessories .rbs-buy').addClass('rbs-in-cart');
													}
												}
											}
											if('arHydroElsPrices' in window){
												for(k in arHydroElsPrices){
													if(k == pid){
														$('#popup-accessories .current-price .value').text(arHydroElsPrices[k]);
													}
												}
											}
											
										$('[data-name-tag]').text(name);
									});
									
									mainSlider.not('.slick-initialized').slick({
										slidesToShow: 1,
										slidesToScroll: 1,
										arrows: false,
										fade: true,
										asNavFor: '.js-card-img-nav-2',
										infinite: false
									});
									
									$('#popup-accessories .popup__main').find('.js-card-img-nav-2').not('.slick-initialized').slick({
										slidesToShow: 5,
										slidesToScroll: 1,
										vertical: false,
										focusOnSelect: true,
										asNavFor: '.js-card-img-big-2',
										dots: false,
										infinite: false,
										responsive: [
											{
												breakpoint: 520,
												settings: {
													slidesToShow: 3
												}
											}
										]
									});									
								}
								$('#popup-accessories .rbs-buy').off().on('click', function(){
									if($(this).hasClass('rbs-in-cart') && !!$(this).data('pid')){
										return;
									}
									var _this = $(this);
									if(window.location.pathname !== '/personal/cart/'){
										$.ajax({
											url: SITE_DIR + 'ajax/sib/main_spec_sib.php',
											data: {
												action: 'ADD2BASKET',
												id: _this.data('pid'),
												ajax_basket: 'Y',
												IBLOCK_ID: '6'
											},
											method: 'GET',
											success: function(){
												if(!'arAddedHydroInBasket' in window){
													arAddedHydroInBasket = {};
												}
												arAddedHydroInBasket[_this.data('pid')] = _this.data('pid');
												_this.addClass('rbs-in-cart');

												if(window.isMobile){
													$.fancybox.open($('#modal_add_to_cart'));
												}
											}
										})
									} else {
										RZB2.ajax.BigBasket.AddToBasket($(this).data('pid'));
										arAddedHydroInBasket[_this.data('pid')] = _this.data('pid');
										_this.addClass('rbs-in-cart');
									}
									
								});
								$('#popup-accessories .popup__main').find('.hydrotype-item:not(.active)').off().on('touchstart click', function(){
									$('#popup-accessories .popup__main').find('.hydrotype-item').removeClass('active');
									$(this).addClass('active');
									$.ajax({
										url: $(this).data('url'),
										type: "POST",
										data: {
											'sessid': BX.bitrix_sessid(),
											'webp': $('html').hasClass('webp') ? 'Y' : 'N'
										},
										dataType: "html",
										success: doAfterAjax,
										error: function(jqXHR, textStatus, errorThrown) {
											RZB2.ajax.showMessage('AJAX error: ' + textStatus, 'error');
										}
									});
								});
							}
						};

						$.ajax({
							url: ajaxPath,
							type: "POST",
							data: {
								'sessid': BX.bitrix_sessid(),
								'webp': $('html').hasClass('webp') ? 'Y' : 'N'
							},
							dataType: "html",
							success: doAfterAjax,
							error: function(jqXHR, textStatus, errorThrown) {
								RZB2.ajax.showMessage('AJAX error: ' + textStatus, 'error');
							}
						});
					},
					'beforeClose': function () {
						$("body").removeClass("popup-open");
						$('.fancybox-close-small').removeClass('fixed-close-btn');
						if(window.location.pathname !== '/personal/cart/'){
							RZB2.ajax.BasketSmall.Refresh();
						}
					},
					touch: false
				});
			} else {
				$(this).fancybox({
					beforeShow: function(){
						$('#popup-accessories .popup__main').html('<div class="load_quick_view"><i class="icon-load"></i></div>');
					},
					afterShow: function () {
						var orig = $(this).get()[0].opts.$orig;
						if(orig.data('url') !== undefined){
							var data = {}, ajaxPath = SITE_DIR + 'ajax/sib/catalog.php';
							for(var key in RZB2.ajax.params) data[key] = RZB2.ajax.params[key];
							data['rz_ajax'] = 'y';
							data['rz_quick_view'] = 'y';
							data['REQUEST_URI'] = orig.data('url');
							if(parseInt(orig.data('service-id')) > 0){
								data['QUICK_VIEW_SERVICE_ID'] = orig.data('service-id');
								data['QUICK_VIEW_SERVICE_PRICE'] = orig.data('service-price');
								ajaxPath = SITE_DIR + 'ajax/sib/service_quick_view.php';
							}					
							
							if('access_custom' === orig.data('custom-popup')){
								ajaxPath = SITE_DIR + orig.data('ajax-url');
								data = {};
							}
		
							$.ajax({
								url: ajaxPath,
								type: "POST",
								data: data,
								dataType: "html",
								success: function(result) {
									$("body").addClass("popup-open");
									if(result){
										$('#popup-accessories .popup__main').html(result);
										if ($('#popup-accessories .popup__main').find(".js-rating").length) {
											$('#popup-accessories .popup__main').find('.js-rating').barrating({
												showSelectedRating: false,
												readonly: true
											});
										}
										if ($('#popup-accessories .popup__main').find(".js-card-img-slider-2").length) {
		
											$('#popup-accessories .popup__main').find('.js-card-img-big-2').not('.slick-initialized').slick({
												slidesToShow: 1,
												slidesToScroll: 1,
												arrows: false,
												fade: true,
												asNavFor: '.js-card-img-nav-2'
											});
											
											$('#popup-accessories .popup__main').find('.js-card-img-nav-2').not('.slick-initialized').slick({
												slidesToShow: 4,
												slidesToScroll: 1,
												vertical: true,
												focusOnSelect: true,
												asNavFor: '.js-card-img-big-2',
												dots: false
											});									
										}
		
										if(parseInt(data['QUICK_VIEW_SERVICE_ID']) > 0){
											$('.rbs-buy[data-product-id="'+data['QUICK_VIEW_SERVICE_ID']+'"]').on('click', function(){
												$('.catalog-bottom__button.buy[data-product-id="'+data['QUICK_VIEW_SERVICE_ID']+'"]').first().click();
											});
										}
									}
								},
								error: function(jqXHR, textStatus, errorThrown) {
									RZB2.ajax.showMessage('AJAX error: ' + textStatus, 'error');
								}
							});
						}
					},
					'beforeClose': function () {
						$("body").removeClass("popup-open");
					}
				});
			}
		});
	}
};

RZB2.ajax.subscribeForm = function(form){
    if (form.find(".sub-form__button").length){
        form.find(".sub-form__button").fancybox({
            beforeShow: function () {
				var email = form.find('input[type=email]').val();
                $('#popup-subscribe-form .popup__content').empty();

                if (isEmail(email))
                {
                    var data = form.serialize();

                    $.ajax({
                        url: SITE_DIR + 'ajax/sib/subscribe.php',
                        type: "POST",
                        data: data,
                        success: function(result) {
                        	var response = $.parseJSON(result);

                        	if (response.STATUS === 'SUCCESS')
                            	$('#popup-subscribe-form .popup__content').html(BX.message('BITRONIC2_SUBSCRIBE_SUCCESS'));
                        	else
                                $('#popup-subscribe-form .popup__content').html(BX.message('BITRONIC2_SUBSCRIBE_FAIL'));
                        }
                    });
                }
                else
                {
                    $('#popup-subscribe-form .popup__content').html(BX.message('BITRONIC2_VALIDATION_NOT_EMAIL'));
                }
            }
        });
    }
};

RZB2.ajax.showMessage = function (text, type, title)
{
	var showInternal = function(){
		if(!type)
			type = 'success';  // success , fail
		
		title = title || BX.message('BITRONIC2_ERROR_TITLE');
		if(type == 'success'){
			title = '';
		}
		var modal = '#modal_msg_uni';

		$(modal).find('.popup__title').text(title);
		$(modal).find('.form').html(text);
		
		$('<a href="' + modal + '"></a>').fancybox().click();
	};
	if (!!domReady) {
		showInternal();
	} else {
		$(document).ready(showInternal);
	}
}
RZB2.ajax.setLocation = function (curLoc)
{
	try {
		history.pushState(null, null, curLoc);
		return;
	} catch(e) {}
		location.hash = '#' + curLoc.substr(1)
}

RZB2.ajax.scrollPage = function($scrollToObj, forced)
{
	forced = !!forced;
	if($scrollToObj.length && (forced || window.pageYOffset > $scrollToObj.offset().top-60)) {
		//$('html,body').animate({scrollTop: $scrollToObj.offset().top-60},800);
		$scrollToObj.velocity('scroll', {
			duration: 800,
			offset: ( b2.s.topLinePosition === 'fixed-top' ) ? -1 * $body.find('.top-line').outerHeight() : 0
		});
	}
}

RZB2.ajax.loader = function()
{
	return $('<div class="ajax_loader"></div>');
}

var smallSpinnerParams = {radius: 3, width: 2};

RZB2.ajax.spinner = function($obj)
{
	var spinner;

	var Start = function(params)
	{
		return;

		/* if (typeof spinner === 'undefined') {
			var defParams = {
				lines: 13, // The number of lines to draw
				length: 5, // The length of each line
				width: 2, // The line thickness
				radius: 5, // The radius of the inner circle
				corners: 1, // Corner roundness (0..1)
				rotate: 0, // The rotation offset
				direction: 1, // 1: clockwise, -1: counterclockwise
				color: '#000', // #rgb or #rrggbb or array of colors
				speed: 1, // Rounds per second
				trail: 60, // Afterglow percentage
				shadow: false, // Whether to render a shadow
				hwaccel: false, // Whether to use hardware acceleration
				className: 'spinner', // The CSS class to assign to the spinner
				zIndex: 2e9, // The z-index (defaults to 2000000000)
				top: '50%', // Top position relative to parent
				left: '50%' // Left position relative to parent
			};
			if(typeof params === 'object') {
				$.extend(defParams, params);
			}
			spinner = new Spinner(defParams);
		}
		if ($obj.css('position') == 'static') {
			$obj.css('position', 'relative');
		}
		$obj.css('pointer-events', 'none');
		$obj.addClass('stop-selection');
		spinner.spin($obj);
		$obj.append(spinner.el); */
	};
	var Stop = function(spinnerObj)
	{
		return;

		/* if (typeof spinner !== 'object') return;

		spinner.stop();
		delete spinner;
		$obj.css({
			'pointer-events': '',
			'position': ''
		}).removeClass('stop-selection'); */
	};

	return {
		Start: Start,
		Stop: Stop
	};
}

RZB2.ajax.loader.Start = function(obj, notLoader)
{
	if (typeof(obj) == "undefined") 
	{ 
		return;
	}
	obj.css('pointer-events', 'none')
	   .animate({opacity: 1}, 500);

	if(!notLoader)
		obj.addClass('placeholder');
}

RZB2.ajax.loader.Stop = function(obj)
{
	if (typeof(obj) == "undefined") 
	{ 
		return;
	}
	obj.animate({opacity: 1}, 300, function(){
		$(this).css('pointer-events', '');
	});

	obj.removeClass('placeholder');
}

RZB2.ajax.RefreshTogglers = function(list, togglerSelector, popupSelector) {
	var elementCount = Object.keys(list).length;
	$(togglerSelector).toggleClass('rz-no-pointer', elementCount <= 0);
	if (elementCount <= 0 && typeof popClose == "function") {
		popClose($(popupSelector));
	}
};

RZB2.ajax.Viewed = {
	isLoaded: false,
	Load: function(){
		var viewedBlock = !isMobile ? $('#rbs-viewed-block') : $('#rbs-viewed-block-mobile'),
			_this  = this;
		if(viewedBlock.length && !this.isLoaded){
			var sendData = {rz_ajax: 'y'};
			if(isMobile){
				sendData.mobile = 'y';
			}

			$.ajax({
				url: SITE_DIR + 'ajax/sib/viewed.php',
				type: "POST",
				data: sendData,
				dataType: 'html',
				success: function(res){

					var result = $('<div>'+res+'</div>');

					if(!isMobile){
						if(result.find('#rbs-viewed-block').length){
							viewedBlock.html(result.find('#rbs-viewed-block').html());
							viewedBlock.show();
						}
						rbsHoversBtn();
					} else {
						if(result.find('.main-nav__text span').length){
							viewedBlock.find('.main-nav__text span').text(result.find('.main-nav__text span').text());
						}
						if(result.find('.inner-nav.js-nav-hide').length){
							viewedBlock.append(result.find('.inner-nav.js-nav-hide'));
							viewedBlock.show();
						}

						viewedBlock.find(".js-click-close").on("click", function () {
							$('.main-nav_inner, .main-nav_index').removeClass("open");
							$('.js-catalog-button_mob').removeClass("active");
							$('html').removeClass("menu-open");
						});
						viewedBlock.find(".js-click-back").on("click", function () {
							$(this).closest(".js-nav-hide").fadeOut();
							$('nav.main-nav_index>ul.main-nav__list>li>a').removeClass('active');
							if($(this).hasClass('normalize-height'))
								$('.main-nav_mobile .main-nav__list').css('height', 'auto');
						});
					}
						
					_this.isLoaded = true;			
				},
				error: function(){
					if(!isMobile){
						rbsHoversBtn();
					}
				}
			});
		}		
	}
};

RZB2.ajax.Compare = 
{
	ElementsList: {},
	
	Params: 
	{
		actionVar: 'action',
		productVar: 'id',
	},
	
	Add: function(id)
	{
		var data = {};
		data[this.Params.actionVar] = 'ADD_TO_COMPARE_LIST';
		data[this.Params.productVar] = id;
		
		this.ElementsList[id] = id;
		
		this.SendRequest(data);
	},
	
	Delete: function(id)
	{
		var data = {};
		data[this.Params.actionVar] = 'DELETE_FROM_COMPARE_LIST';
		data[this.Params.productVar] = id;
		
		//deleted
		if(typeof this.ElementsList[id] !== 'undefined' )
			delete this.ElementsList[id];
			
		this.SendRequest(data);
	},
	
	DeleteAll: function()
	{
		var data = {};
		data[this.Params.actionVar] = 'DELETE_ALL_COMPARE_LIST';
		
		this.ElementsList = {};
		
		this.SendRequest(data);
	},
	
	SendRequest: function(data)
	{
		var _this = this;
		$.ajax({
			url: SITE_DIR + 'ajax/sib/compare_sib.php',
			type: "POST",
			data: data,
			dataType: 'html',
			success: function(res){
				_this.RefreshResult(res);
			}
		});
	},
	
	Refresh: function()
	{
		var data = {};
		this.SendRequest();
	},
	
	RefreshButtons: function ($node)
	{
		if (typeof $node !== "object") {
			$node = $(document);
		}
		var compareButtons = $node.find('button.btn-action.compare, button.action.compare, .compare-detail, .compare-list-btn');
		if(compareButtons.length)
		{
			var btnSelector = '';
			for (var id in this.ElementsList)
			{
				if(!!btnSelector.length) btnSelector += ',';
				btnSelector += '[data-compare-id="' + this.ElementsList[id] + '"]';					
			}
			if(!!btnSelector.length)
			{
				var itemsInCompare = compareButtons.filter(btnSelector);
				var itemsNotCompare = compareButtons.not(itemsInCompare);
				
				if(itemsInCompare.length)
					this.ButtonsViewStatus(itemsInCompare, true, true);
				
				if(itemsNotCompare.length)
					this.ButtonsViewStatus(itemsNotCompare, false, true);
			}
			else
			{
				this.ButtonsViewStatus(compareButtons, false, true);
			}
		}
		RZB2.ajax.RefreshTogglers(this.ElementsList, '#compare-toggler', '#popup_compare');
	},
	
	ButtonsViewStatus: function(obButns, active, silent)
	{
		active = !!active || false;
		silent = !!silent || false;
		if(active) {
			obButns.addClass('toggled').attr('title', BX.message('BITRONIC2_COMPARE_DELETE'));
		}
		else {
			obButns.removeClass('toggled').attr('title', BX.message('BITRONIC2_COMPARE_ADD'));
		}
		/* if (typeof $.fn.tooltip != "undefined" && isHover) {
			obButns.tooltip('fixTitle');
			if(!silent) {
				obButns.tooltip('show')
			}
		} */
	},
	
	RefreshResult: function(res)
	{
		var $res = $(res),
			count = $res.find('.rbs-compare-count').html();

		$('#rbs-compare-list .rbs-basket-hide').html($res.find('.rbs-basket-hide').html());
		$('#rbs-compare-list .rbs-compare-count').html(count);
		
		if(!!$('#rbs-compare-list-mobile').length){
			$('#rbs-compare-list-mobile .rbs-basket-hide-mobile').html($res.find('.rbs-basket-hide-mobile').html());
			$('.rbs-compare-count-mobile').html(count);
			$("#rbs-compare-list-mobile .js-click-back").on("click", function () {
				$(this).closest(".js-nav-hide").fadeOut();
				$('nav.main-nav_index>ul.main-nav__list>li>a').removeClass('active');
				if($(this).hasClass('normalize-height'))
					$('.main-nav_mobile .main-nav__list').css('height', 'auto');
			});
			$(".main-nav_mobile #rbs-compare-list-mobile .js-click-close").on("click", function () {
				$('.main-nav_inner, .main-nav_index').removeClass("open");
				$('.js-catalog-button_mob').removeClass("active");
				$('html').removeClass("menu-open");
			});
			$(".js-del-btn-comp-fav[data-type='compare']").on("click", function () {
				RZB2.ajax.Compare.Delete($(this).data('product-id'));
				$(this).closest(".js-remove").fadeOut(0, function () {
					$(this).remove();
				})
				return false;
			});
		}
		
		this.RefreshButtons();
		RZB2.utils.initLazy($('#rbs-compare-list .rbs-basket-hide, #rbs-compare-list-mobile'));
		//this.RefreshButtons();
	},
	
}

RZB2.ajax.Favorite = 
{
	ElementsList: {},
	
	Params: 
	{
		actionVar: 'ACTION',
		productVar: 'ID',
	},
	
	Add: function(id)
	{
		var data = {};
		data[this.Params.actionVar] = 'ADD';
		data[this.Params.productVar] = id;
		
		this.ElementsList[id] = id;
		
		this.SendRequest(data);
	},
	
	Delete: function(id)
	{
		var data = {};
		data[this.Params.actionVar] = 'DELETE';
		data[this.Params.productVar] = id;
		
		//deleted
		if(typeof this.ElementsList[id] !== 'undefined')
			delete this.ElementsList[id];
			
		this.SendRequest(data);
	},
	
	DeleteAll: function()
	{
		var data = {};
		data[this.Params.actionVar] = 'FLUSH';
		
		this.ElementsList = {};
		
		this.SendRequest(data);
	},
	
	SendRequest: function(data)
	{
		data = data || {};
		var _this = this;

		data['sessid'] = BX.bitrix_sessid();
		data['IS_MOBILE'] = isMobile ? 1 : 0;
		$.ajax({
			url: SITE_DIR + 'ajax/sib/favorites.php',
			type: "POST",
			data: data,
			dataType: 'html',
			success: function(res){
				_this.RefreshResult(res);
			}
		});
	},
	
	Refresh: function()
	{
		var data = {};
		this.SendRequest();
	},
	
	RefreshButtons: function ($node)
	{
		if (typeof $node !== "object") {
			$node = $(document);
		}
		var obButtons = $node.find('.favorite-detail, button.action.favorite, .favorite-list-btn');
		if(obButtons.length)
		{
			var btnSelector = '';
			for (var id in this.ElementsList)
			{
				if(!!btnSelector.length) btnSelector += ',';
				btnSelector += '[data-favorite-id="' + this.ElementsList[id] + '"]';
			}
			if(!!btnSelector.length)
			{
				var itemsIn = obButtons.filter(btnSelector);
				var itemsNot = obButtons.not(itemsIn);
				
				if(itemsIn.length)
					this.ButtonsViewStatus(itemsIn, true, true);
				
				if(itemsNot.length)
					this.ButtonsViewStatus(itemsNot, false, true);
			}
			else
			{
				this.ButtonsViewStatus(obButtons, false, true);
			}
		}

		//RZB2.ajax.RefreshTogglers(this.ElementsList, '#favorites-toggler', '#popup_favorites');
	},
	
	ButtonsViewStatus: function(obButns, active, silent)
	{
        var elementCount = Object.keys(this.ElementsList).length;
		active = !!active || false;
		silent = !!silent || false;
		if(active) {
			obButns.addClass('toggled').attr('title', BX.message('BITRONIC2_FAVORITE_DELETE'));
		}
		else {
			obButns.removeClass('toggled').attr('title', BX.message('BITRONIC2_FAVORITE_ADD'));
		}
		
		if (obButns.closest('.actions-with-count').length){
            obButns.closest('.actions-with-count').find(obButns).find('i').html(elementCount);
		}
	},
	
	RefreshResult: function(res)
	{
		if(!isMobile){
			$('.favorite-desktop-result').html($(res).find('.favorite-desktop-result').html());
			$('.rbs-favorite-count').html($(res).find('.rbs-favorite-count').html());
		} else {
			$('.favorite-desktop-result').html($(res).find('.favorite-desktop-result').html());
			$('.rbs-favorite-count-mobile').html($(res).find('.rbs-favorite-count-mobile').html());
			$(".js-del-btn-comp-fav[data-type='favorite']").off().on("click", function () {
				RZB2.ajax.Favorite.Delete($(this).data('product-id'));
			});
		}		
		RZB2.utils.initLazy($('.favorite-desktop-result'));		
		this.RefreshButtons();
	},
	
	ToBasket:
	{
		AddAll: function()
		{
			var data = {items:{}};
			data.action = 'addList';
			data.rz_ajax = 'y';
			$('#popup_favorites table [name="quantity"]').each(function(){
				if(Number($(this).val())>0)
				{
					var id = $(this).data('id');
					var quantity = Number($(this).val());
						
					data.items[id] = {
						id: id,
						quantity: quantity
					};					
				}
			});
			
			this.SendRequest(data);
		},
		
		SendRequest: function(data)
		{
			var _ = this;
			$.ajax({
				url: SITE_DIR + 'ajax/sib/basket_sib.php',
				type: "POST",
				data: data,
				dataType: 'json',
				success: function(data){
					RZB2.ajax.BasketSmall.Refresh();
				}
			});
		},
	}
}

RZB2.ajax.BasketSmall = 
{
	pricesTotal: {old:0, current:0},
	basketCurrency: '',
	addType: 'buzz', // buzz | popup
	ElementsList: {},
	
	// it's function addToBasket from \js\custom-libs\um_basket.js
	addAnimation: function(silentMode){
		silentMode = !!silentMode;
		var basket = $('#basket');
		if(!silentMode)
			basket.addClass('buzz'); // class for animation (set in CSS)
		
		var currency = this.basketCurrency;		
		var target = basket.find('a .basket-total-price .value, .popup-header .basket-content .total-price .value, #popup_basket .popup-footer .price .value');
		var to = Number(this.pricesTotal.current);
		var from = Number(this.pricesTotal.old);
		to = (to % 1 <= 0) ? parseInt(to) : to;
		from = (from % 1 <= 0) ? parseInt(from) : from;
		var priceInterval;
		var stepInterval = 35;
		var steps = 15;
		var difference = to - from;

		if (steps > Math.abs(difference)) {
			steps = parseInt(Math.abs(difference));
		}
		if (steps == 0) {
			steps = 1;
		}
		
		var step = difference / steps;
		if(to % 1 <= 0 && from % 1 <= 0)
		{
			step = parseInt(step);
		}
		// we don't need real difference anymore, so use existing variable for setting
		// direction - increase (1) or decrease (-1)
		difference = ( difference > 0 ) ? 1 : -1;
		var interval = setInterval(function(){
			from += step;
			if ( (from - to)*difference >= 0 ){
				// ^ this tricky expression works for both directions
				// we check not against number of steps, but against price
				// shown on current step. If we've reached target, then
				// clearInterval.
				from = to;
				basket.removeClass('buzz');
				clearInterval(interval);
			}
			
			var showPrice;
			if(typeof BX.Currency == "object" && currency.length) {
				showPrice = BX.Currency.currencyFormat(from, currency, false);
			} else {
				showPrice = from.toFixed(2);
				if (showPrice == Math.round(showPrice)) showPrice = Math.round(showPrice);
			}
			
			target.html( showPrice );
		}, stepInterval);
	},
	
	addPopup: function(params){
		var _this = this;
		var data = {
			'rz_ajax' : 'y',
			'action' : 'addPopup',
			'id' : params.id,
		};
		if(typeof params.iblockId != 'undefined') data['iblock_id'] = params.iblockId;
		if(typeof params.iblockIdSku != 'undefined') data['iblock_id_sku'] = params.iblockIdSku;
		if(typeof params.parentId != 'undefined') data['parentId'] = params.parentId;

		this.ElementsList[params.id] = params.id;
		
		var $modal = $('#modal_basket');
		var $content = $modal.find('.content');
		$content.empty();
		var spinner = RZB2.ajax.spinner($modal.find('.modal-dialog'));
		spinner.Start({color: RZB2.themeColor});
		//$modal.modal('show');

		$.ajax({
			url: SITE_DIR + 'ajax/sib/basket_sib.php',
			type: "POST",
			data: data,
			dataType: 'html',
			success: function(data){
				if (typeof spinner == 'object') {
					spinner.Stop();
					delete spinner;
				}
				$content.html(data);
				// trigger event for picturefill
				var event = document.createEvent("HTMLEvents");
				event.initEvent("DOMContentLoaded",true,false);
				window.dispatchEvent(event);
				// optional sliders
				/* if ($content.find('.scroll-slider-wrap').length > 0) {
					$content.find('[data-toggle="modal"]').off('click').on('click', function(e){
						var $this = $(this);
						$($this.data('target')).modal('show', this);
						if ($this.hasClass('one-click-buy')) {
							oneClickBuyHandler.call(this, e);
						}
					});
					initHorizontalCarousels($content);
					if (typeof initToggles == "function") initToggles($content);
					RZB2.ajax.Favorite.RefreshButtons();
					RZB2.ajax.Compare.RefreshButtons();
                    RZB2.utils.initLazy($content);
				} */
				//b2.init.ratingStars($content);
				//b2.init.tooltips($content);
				_this.RefreshButtons();
				RZB2.utils.initLazy($content);
			}
		});
	},
	
	Refresh: function(silentMode, itemParams)
	{
		var data = {
			'rz_ajax' : 'y',
			'action' : 'updateBasket'
		};
		var _this = this;
		$.ajax({
			url: SITE_DIR + 'ajax/sib/basket_sib.php',
			type: "POST",
			data: data,
			dataType: 'html',
			success: function(data){
				var basket = $('#basket');
				var $data = $(data);
				_this.pricesTotal.old = Number(basket.find('.basket-total__price').text());
				
				var replaceContentSelectors = [
					'.basket-items',
					'.basket-total__price',
					'.header-basket__count>span',
					'.header-basket__total'
				];
				
				if(_this.addType == 'popup')
				{
					replaceContentSelectors.push('a .basket-total-price .value');
					replaceContentSelectors.push('.popup-header .basket-content .total-price .value');
					replaceContentSelectors.push('#popup_basket .popup-footer .price .value');
				}
				
				for(var i=0; i<replaceContentSelectors.length; i++)
				{
					basket.find(replaceContentSelectors[i]).empty().html($data.find(replaceContentSelectors[i]).html());
				}

				
				$('.header-basket__count>span').html($data.find('.header-basket__count>span').html());

				//basket.find('#popup_basket .popup-footer .price').data('total-price', $data.find('#popup_basket .popup-footer .price').data('total-price'));
				//bonus
				//basket.find('#popup_basket .popup-footer .bonus').html($data.find('#popup_basket .popup-footer .bonus').html());
				
				switch(_this.addType)
				{
					case 'popup':
						if(typeof itemParams !== "undefined" && typeof window['recalcBasketAjax'] === "undefined") {
							if(typeof itemParams['id'] !== 'undefined' && parseInt(itemParams['id']) > 0) _this.addPopup(itemParams);
							break;
						}
						//continue to case buzz
					
					case 'buzz':
					default: 
						_this.pricesTotal.current = Number(basket.find('#popup_basket .popup-footer .price').data('total-price'));
						if(_this.pricesTotal.current != _this.pricesTotal.old)
							_this.addAnimation(silentMode);
					break;
				}
				if (!silentMode && typeof window['recalcBasketAjax'] === "function") {
					recalcBasketAjax({}, true, 'addItem');
				}
				
				$('<div></div>').html(data).empty(); //execute JS
				
				//if empty basket (1 tr it's header of table)
				/*if(basket.find('.items-table tr').length <= 1)
				{
					//close open basket
					$('a[data-target="#popup_basket"][data-toggle="um_popup"]').trigger('click');
				}
				else if(typeof b2.init.tooltips != "undefined")
				{
					b2.init.tooltips(basket.find('#popup_basket'));
				}*/
				//RZB2.ajax.loader.Stop( basket.find('.items-table') );
				RZB2.ajax.BasketSmall.RefreshButtons();
                RZB2.utils.initLazy($(basket));
			}
		});
	},

	RefreshButtons: function ($node)
	{
		//return;
		if (typeof $node !== "object") {
			$node = $(document);
		}
		var obButtons = $node.find('button.buy');
		
		if(obButtons.length)
		{
			var btnSelector = '';
			for (var id in this.ElementsList)
			{
				if(!!btnSelector.length) btnSelector += ',';
				btnSelector += '[data-product-id="' + this.ElementsList[id] + '"]:not(.on-request),[data-offer-id="' + this.ElementsList[id] + '"]:not(.on-request)';
			}
			if(!!btnSelector.length)
			{
				var itemsIn = obButtons.filter(btnSelector);
				var itemsNot = obButtons.not(itemsIn);
				
				if(itemsIn.length)
					this.ButtonsViewStatus(itemsIn, true, true);
				
				if(itemsNot.length)
					this.ButtonsViewStatus(itemsNot, false, true);
			}
			else
			{
				this.ButtonsViewStatus(obButtons, false, true);
			}
		}
		RZB2.ajax.RefreshTogglers(this.ElementsList, '#bxdinamic_bitronic2_basket_string', '#popup_basket');
	},
	
	ButtonsViewStatus: function(obButns, active, silent)
	{
		active = !!active || false;
		silent = !!silent || false;
		if (active) {
			$(obButns).closest('.catalog-item').addClass('active');
			obButns.each(function(){
				if (!$(this).hasClass('button_white')) {
					$(this).html('<div class="catalog__cart-ico"></div>');
				}else if($(this).hasClass('rbs-buy')) {
					$(this).addClass('rbs-in-cart');
				}
			});
			//obButns.addClass('main-clicked forced').attr('title', BX.message('BITRONIC2_BASKET_REDIRECT'));
			/* if (typeof $.fn.tooltip == "function" && isHover) {
				obButns.tooltip({
					//'trigger': 'click',
					placement: 'auto',
					html: true,
					container: 'body'
				}).tooltip('fixTitle');
			} */
		}
		else {
			$(obButns).closest('.catalog-item').removeClass('active');
			obButns.each(function(){
				if (!$(this).hasClass('button_white')) {
					$(this).html('В корзину');
				}else if($(this).hasClass('rbs-buy')) {
					$(this).removeClass('rbs-in-cart');
				}
			});
			
			/* if (typeof $.fn.tooltip == "function" && isHover) {
				obButns.tooltip('destroy');
			} */
		}
			
		/* if(!silent && isHover)
			obButns.tooltip('show') */
	},
	
	Reload: function(data)
	{
		data['rz_ajax'] = 'y';

		this.SendRequest(data);
	},
	
	SendRequest: function(data)
	{
		var _this = this;
		$.ajax({
			url: SITE_DIR + 'ajax/sib/basket_sib.php',
			type: "POST",
			data: data,
			dataType: 'html',
			success: function(data){
				_this.Refresh(true);
				//if (typeof window['recalcBasketAjax'] == 'function') recalcBasketAjax({}, true);
			}
		});
	},
	
	/* ChangeQuantity: function(obj, Params)
	{
		var data = {};
		if(!!obj)
		{
			var $table = $(obj.target).closest('.items-table');
			if ($table.hasClass('ajax_loader')) return;

			RZB2.ajax.loader.Start($table);

			var $itemContainer = $(obj.target).prop('disabled', true).closest('tr');
			var $quanInput = $itemContainer.find('input[name=quantity]');
			var quantity = this.CorrectRatioQuantity(parseFloat($quanInput.val()), $quanInput.data('ratio'));
			data['action'] = 'setQuantity';
			data['id'] = $itemContainer.data('id'); 
			data['productId'] = $itemContainer.data('product-id');
			data['key'] = $itemContainer.data('key');
			if(isNaN(quantity))
			{
				quantity = parseInt(obj.target.defaultValue, 10);
			}
			if(quantity <= 0 && $quanInput.val() != '0') {
				this.Refresh(true);
				return;
			}
			data['quantity'] = quantity;
			if (typeof this.ElementsList[data['productId']] != 'undefined') delete this.ElementsList[data['productId']];
		}
		this.Reload(data);		
	}, */

	/* CorrectRatioQuantity: function(quantity, ratio)
	{
		var newValInt = quantity * 10000,
			ratioInt = ratio * 10000,
			reminder = newValInt % ratioInt,
			result = quantity,
			bIsQuantityFloat = false,
			i;
		ratio = parseFloat(ratio);

		if (reminder === 0)
		{
			return result;
		}

		if (ratio !== 0 && ratio != 1)
		{
			for (i = ratio, max = parseFloat(quantity) + parseFloat(ratio); i <= max; i = parseFloat(parseFloat(i) + parseFloat(ratio)).toFixed(2))
			{
				result = i;
			}

		}else if (ratio === 1)
		{
			result = quantity | 0;
		}

		if (parseInt(result, 10) != parseFloat(result))
		{
			bIsQuantityFloat = true;
		}

		result = (bIsQuantityFloat === false) ? parseInt(result, 10) : parseFloat(result).toFixed(2);

		return result;
	}, */
	
	Delete: function(basketItemId, productId)
	{
		//$(obj.target).parents('tr').find('input[name=quantity]').val(0);
		//this.ChangeQuantity(obj);
		var data = {};
		data['id'] = basketItemId;
		data['quantity'] = 0;
		data['rz_ajax'] = 'y';
		data['action'] = 'setQuantity';

		if (typeof this.ElementsList[productId] != 'undefined') delete this.ElementsList[productId];
		this.SendRequest(data);
	},
	
	DeleteAll: function(obj)
	{
		$(obj.target).parents('#popup_basket').find('input[name=quantity]').val(0);
		var data = {'action': 'deleteAll'};
		this.ElementsList = {};
		this.Reload(data);
	},
}

RZB2.ajax.CatalogSection = 
{
	filterParams: {},
	GetFilterParamsString: function()
	{
		var notFirst = false;
		var urlParams = '';
		var delParams = ['clear_cache','ajax'];
		
		for(var key in this.filterParams)
		{
			if(!BX.util.in_array(key,delParams) 
				&& !(BX.util.in_array(key,BX.util.array_keys(RZB2.ajax.params)) || BX.util.in_array(key,BX.util._array_values_ob(RZB2.ajax.params)))
			)
			{
				if(notFirst)
				{
					urlParams +='&';
				}
				notFirst = true;
				urlParams += key + '=' + encodeURIComponent(this.filterParams[key]);
			}
		}
		return urlParams;
	},
	GetCommonParamsString: function()
	{
		var notFirst = false;
		var urlParams = '';
		for(var key in RZB2.ajax.params)
		{
			if(RZB2.ajax.params[key].length)
			{
				switch(key)
				{
					/* case 'PAGEN_1': */
					case 'page_count':
					case 'view':
					case 'sort':
					case 'by':
					case 'spec':
					case 'rz_all_elements':
					case 'q':
					case 'where':
					case 'section':
						if(notFirst)
						{
							urlParams +='&';
						}
						urlParams += key + '=' + RZB2.ajax.params[key];
						
						notFirst = true;
					break;
				
				}
			}
		}
		return urlParams;
	},
	
	Start: function(obj, Params, spinnerParams)
	{	
		for(var key in Params) 
		{
			if(key.length)
			{
				RZB2.ajax.params[key] = Params[key];
			}
		}
		
		var spinner = RZB2.ajax.spinner($(obj));
		/*spinner.Start(spinnerParams);*/
		
		return this.Reload(spinner, Params);		
	},
	
	Reload: function(spinner, params)
	{
		var objLoader = $('#catalog_section');
		
		var data = {
			'rz_ajax' : 'y',
			'site_id': SITE_ID,
		};
		
		for (var key in RZB2.ajax.params)
		{
			data[key] = RZB2.ajax.params[key];
		}
				
		return this.SendRequest(data, objLoader, params, spinner);
	},
	
	SendRequest: function(data, objLoader, params, spinner)
	{
		RZB2.ajax.loader.Start(objLoader, true);
		
		var paramFilterString = this.GetFilterParamsString();
		this.SetNewLocation();
		params = params || {};
		if (!('MORE_CLICK' in params)) {
			RZB2.ajax.scrollPage($('.box-bread-crumbs'));
		};
		
		return $.ajax({
			url: SITE_DIR + 'ajax/sib/catalog.php?' + paramFilterString,
			type: "POST",
			data: data,
			dataType: 'html',
			success: function(data){
				RZB2.ajax.loader.Stop(objLoader);

				/*if (typeof spinner == 'object') {
					spinner.Stop();
					delete spinner;
				}*/

				RZB2.ajax.CatalogSection.Refresh(data, params);
			}
		});
	},
	
	SetNewLocation: function()
	{
		var paramCommonString = this.GetCommonParamsString();
		var paramFilterString = this.GetFilterParamsString();
		var newLocation = window.location.pathname;

		if ('SefSetUrl' in this && this.SefSetUrl.length > 0) {
			newLocation = this.SefSetUrl + '?' + paramCommonString;
		} else {
			if (paramCommonString.length) {
				newLocation += "?" + paramCommonString;
				if (paramFilterString.length)
					newLocation += '&' + paramFilterString;

			}
			else {
				if (paramFilterString.length)
					newLocation += '?' + paramFilterString;

			}
		}
		RZB2.ajax.setLocation(newLocation);
	},
	
	Refresh: function(data, params)
	{
		var $data = $(data);
		var catalogClass = (RZB2.ajax.params['view'] == 'table') ? 'catalog-table' : RZB2.ajax.params['view'];

		var $catalogSection = $('#catalog_section');

		$catalogSection.find('.box-paging').remove();
		$catalogSection.find('.rbs-section-description-block').remove();
		
		// stop timers
		$catalogSection.find('.timer').each(function(){
			$(this).off().countdown('pause');
		});

		$catalogSection.removeClass('blocks list catalog-table').addClass(catalogClass);

		if ('MORE_CLICK' in params) {
			var $lastItem = $catalogSection.find('.catalog-item').last();
			var curOffset = $lastItem.offset().top;
			if (RZB2.ajax.params['view'] == 'table') {
				var $headerArt = $catalogSection.find('.table-header .art-wrap');
				if ($headerArt.hasClass('no-art')) {
					$data.find('#catalog_section td.art-wrap').each(function(){
						if ($(this).hasClass('no-art')) return true;
						$headerArt.removeClass('no-art');
						return false;
					});
				}
				$catalogSection.find('table').append($data.find('#catalog_section table').html().trim());
			} else {
				if (catalogClass == 'blocks') {
					$data.find('#catalog_section.catalog-page-wrap > div').not('.catalog-item-wrap').css('display', 'inline');
					$catalogSection.children('div').not('.catalog-item-wrap').css('display', 'inline');
				}
				$catalogSection.append($data.find('#catalog_section').html().trim());
			}
		} else {
			$catalogSection.empty().html($data.find('#catalog_section').html());
		}

		var paginatorSelector = '.pagination'; // !!!!!!
		var paginatorTextSelector = '.current-state'; // !!!!!!
		if($data.find(paginatorSelector).length)
		{
			$(paginatorSelector).empty().html($data.find(paginatorSelector).html());
		}
		else
		{
			$(paginatorSelector).empty();
		}
		if($data.find(paginatorTextSelector).length)
		{
			$(paginatorTextSelector).empty().html($data.find(paginatorTextSelector).html());
		}
		else
		{
			$(paginatorTextSelector).empty();
		}

		var $paginatorMore = $data.find('.more-catalog-wrap'); // !!!!!!
		if ($paginatorMore.length > 0) {
			$($paginatorMore.selector).empty().html($paginatorMore.html());
		} else {
			$paginatorMore.empty();
		}

		//update view buttons
		$data.find('span.view-type a').each(function(){
			$('div.catalog-main-content a[data-view="'+$(this).data('view')+'"]').attr('href', $(this).attr('href'));
		});

		if ( RZB2.ajax.params['view'] == 'table' ){
			var tableHeader = $('.table-header');
			if ( tableHeader.length ){
				var el = $('.table-header');
				var wrap = el.closest('.catalog');
				var offBot = 50;
				var thScrollFix = new UmScrollFix(el, wrap, 0, offBot);
				thScrollFix.getDims();
				thScrollFix.update();
			}
		} else {
			//initTimers($catalogSection);
			//initPhotoThumbs($catalogSection);
			//b2.init.selects($catalogSection);
			//if (typeof b2.init.scrollbarsTargeted == "function") b2.init.scrollbarsTargeted($catalogSection[0]);
		}
		/*initToggles($catalogSection.parent());
		b2.init.tooltips($catalogSection);
		b2.init.ratingStars($catalogSection);*/
			
		RZB2.ajax.Compare.RefreshButtons();
		RZB2.ajax.Favorite.RefreshButtons();
		RZB2.ajax.BasketSmall.RefreshButtons();
		RZB2.ajax.CatalogSection.RefreshButtons();
		RZB2.utils.initLazy($catalogSection);

		setTimeout(function(){
			if($('.rbs-find-img-detail-descr').length)
				$('.rbs-find-img-detail-descr').lazyload({}).removeClass('placeholder');

			if(typeof checkBannerSection === 'function')
				checkBannerSection();
			
		}, 300);
		

        /*if ($catalogSection.hasClass('blocks')) {
		  $catalogSection.flexGreedSort('update', 'catalog-item-wrap', 'big-item', 'last-item', 'banner-catalog', $('[data-catalog-banner-pos]').data('catalog-banner-pos'));
        }*/

      //  b2.init.selects && b2.init.selects($('select:visible').parent());
		
		var exe = $('<div></div>');
		exe.html(data); // for execute JS in data

		if ('MORE_CLICK' in params) {
			$('html,body').animate({scrollTop: window.pageYOffset + $lastItem.offset().top - curOffset}, 0);
		}
	},

	RefreshButtons: function()
	{
		var itemCount = $('.catalog-page .catalog-item').length;
		var totalCount = parseInt($('div.catalog-main-content .current-state-total').eq(0).text());
		if (isNaN(totalCount)) {
			totalCount = 0;
		}
		$('.catalog-page').find('.view-type, .sort-list, .sort-n-view.for-catalog .chosen-container').toggleClass('disabled', !itemCount);

		var $showBy = $('.catalog-page .show-by select.show-by');
		var $options = $showBy.find('option');
		var arDisabled = [], arEnabled = [0];
		$showBy.eq(0).find('option').each(function(index){
			var $_ = $(this);
			if(index >= arEnabled.length) {
				if ($_.is(':selected')) {
					arEnabled.push(index);
				} else {
					arDisabled.push(index);
				}
			}
			if (totalCount > parseInt($_.val()) && (index + 1 < $options.length) ) {
				arEnabled.push(index + 1);
			}
		});
		$showBy.find('option').filter(function(index,el) {
			return  $.inArray(index,arDisabled) > -1;
		}).prop('disabled',true);
		$showBy.find('option').filter(function(index,el) {
            return  $.inArray(index,arEnabled) > -1;
        }).prop('disabled',false);

        //b2.init.selects('.catalog-page .show-by');
        //if (typeof b2.init.scrollbarsTargeted == "function") b2.init.scrollbarsTargeted($('.catalog-page .show-by'));
    },
	
	AddToBasketSimple: function(id, iblock_id, quantity, spinner)
	{
		var data = {items:{}};
		data.action = 'addList';
		data.rz_ajax = 'y';
		if(Number(id)>0)
		{
			if(isNaN(quantity)) quantity = 1;
			
			data.items[id] = {
				id: id,
				iblockId: iblock_id,
				quantity: quantity
			};
			
			this.Table.SendRequest(data, spinner);
		}		
	},
	
	
	// view TABLE
	Table:
	{
		AddToBasket: function(obj, spinnerParams)
		{
			var data = {items:{}};
			data.action = 'addList';
			data.rz_ajax = 'y';
			data.iblock_id = RZB2.ajax.params['IBLOCK_ID'];
			$('#catalog_section table [name="quantity"]').each(function(){
				if(Number($(this).val())>0)
				{
					var id = $(this).data('item-id');
					var quantity = Number($(this).val());
						
					data.items[id] = {
						id: id,
						quantity: quantity
					};					
				}
			});

			var spinner = RZB2.ajax.spinner($(obj));
			spinner.Start(spinnerParams);
			
			this.SendRequest(data, spinner);
		},
		
		SendRequest: function(data, spinner)
		{
			var _ = this;
			$.ajax({
				url: SITE_DIR + 'ajax/sib/basket_sib.php',
				type: "POST",
				data: data,
				dataType: 'json',
				success: function(_data){
					var counter = 0;
					for (var item in data.items) {
						RZB2.ajax.BasketSmall.ElementsList[item] = item;
						counter++;
					}
					if (counter == 1) {
						RZB2.ajax.BasketSmall.Refresh(false, data.items[item]);
					} else {
						RZB2.ajax.BasketSmall.Refresh();
					}
					RZB2.ajax.BasketSmall.RefreshButtons();
					_.ClearInputs();
					if (typeof spinner == 'object') {
						spinner.Stop();
						delete spinner;
					}
				}
			});
		},
		
		ClearInputs: function()
		{
			$('#catalog_section table [name="quantity"]').val(0);
			$('#add_basket_table .number').text(0);
			$('#add_basket_table').addClass('disabled');
			$('.quantity-counter .btn-silver.quantity-change.decrease').addClass('disabled');
		},
	}
}
RZB2.ajax.BigBasket =
{
    AddToBasket: function(id)
    {
    	var data = {items: {}};
		data.action = 'addList';
		data.rz_ajax = 'y';

        if (Number(id) > 0)
        {
            data.items[id] = {
                id: id,
                quantity: 1
            };

            this.SendRequest(data);
        }
    },

    SendRequest: function(data)
    {
        var _ = this;
        $.ajax({
            url: SITE_DIR + 'ajax/sib/basket_sib.php',
            type: "POST",
            data: data,
            dataType: 'json',
            success: function(res){
                recalcBasketAjax({}, true, 'addItem');
            }
        });
    }
}/* 
// BIG DATA
RZB2.ajax.BigData =  function () {
	this.containerId = '';
	this.parameters = '';
	this.template = '';
	
};
RZB2.ajax.BigData.prototype.SendRequest = function(response)
{
	var _ = this;
	var $container = $('#' + _.containerId);

	if (!$container.parent().is(':visible')) {
		setTimeout(function(){
			_.SendRequest(response);
		}, 1000);
		return;
	}

	var spinner = RZB2.ajax.spinner($container);
	spinner.Start({color: RZB2.themeColor});
	
	if (!response.items)
	{
		response.items = [];
	}
	var data = {'parameters': _.parameters, 'template': _.template, 'rcm': 'yes'};
	if(!!RZB2.ajax.params['REQUEST_URI']) data["REQUEST_URI"] = RZB2.ajax.params['REQUEST_URI'];
	if(!!RZB2.ajax.params['SCRIPT_NAME']) data["SCRIPT_NAME"] = RZB2.ajax.params['SCRIPT_NAME'];
	
	BX.ajax({
		url: SITE_DIR + 'ajax/sib/bigdata.php?'+BX.ajax.prepareData({'AJAX_ITEMS': response.items, 'RID': response.id}),
		method: 'POST',
		data: data,
		dataType: 'html',
		processData: false,
		start: true,
		onsuccess: function (res) {
			if (typeof spinner == 'object') {
				spinner.Stop();
				delete spinner;
			}
			// inject
			$container.html(res);
			
			// trigger event for picturefill
			var event = document.createEvent("HTMLEvents");
			event.initEvent("DOMContentLoaded",true,false);
			window.dispatchEvent(event);

			// menu hits
			if ($container.hasClass('scroll-slider-wrap')) {
				if ($container.find('.slider-item').length < 1) {
					$container.hide();
					return;
				}
				$container.closest('.submenu-wrap').prepend($container.find('button.show-hide-hits'));
				b2.el.$menuHits = $('.submenu-wrap .scroll-slider-wrap');
				
				var $menu = b2.el.$menu || b2.elements.$menu;
				initHorizontalCarousels($menu);
				initToggles($container.parent());
				return;
			}

			//anything else
			if ($container.find('.scroll-slider-wrap').length > 0) {
				$container.find('[data-toggle="modal"]').off('click').on('click', function(e){
					var $this = $(this);
					$($this.data('target')).modal('show', this);
					if ($this.hasClass('one-click-buy')) {
						oneClickBuyHandler.call(this, e);
					}
				});
			}
			
			if (typeof initHorizontalCarousels == "function") initHorizontalCarousels($container);
			//if (typeof b2.init.ratingStars     == "function") b2.init.ratingStars($container);
			//if (typeof b2.init.tooltips        == "function") b2.init.tooltips($container);
			if (typeof initToggles             == "function") initToggles($container);
			RZB2.ajax.BasketSmall.RefreshButtons($container);
			RZB2.ajax.Favorite.RefreshButtons($container);
			RZB2.ajax.Compare.RefreshButtons($container);
            RZB2.utils.initLazy($container);
		}
	});
}; */
/* 
// Menu Hits
RZB2.ajax.MenuHits = function(){
	this.url = SITE_DIR + 'ajax/sib/menu_hits.php';
	this.spinners = [];
};
RZB2.ajax.MenuHits.prototype.Init = function(){
	var _ = this;

	if (typeof b2.el.$menuHits == "undefined") {
		setTimeout(BX.delegate(_.Init, _), 500);
		return;
	}
	b2.el.$menuHits.each(function(){
		var spinner = RZB2.ajax.spinner($(this));
		spinner.Start({color: RZB2.themeColor});
		_.spinners.push(spinner);
	});

	BX.ajax({
		url: _.url,
		method: 'POST',
		data: {RZ_B2_AJAX_MENU_HITS: 'Y'},
		dataType: 'html',
		processData: false,
		start: true,
		onsuccess: BX.delegate(_.OnSuccess, _),
		onfailure: BX.delegate(_.OnFailure, _)
	});
};
RZB2.ajax.MenuHits.prototype.OnFailure = function(){
	for (var i = 0; i < this.spinners.length; i++) {
		this.spinners[i].Stop();
		delete this.spinners[i];
	}
	this.spinners = [];
};
RZB2.ajax.MenuHits.prototype.OnSuccess = function(res){
	this.OnFailure();
	var $res = $(res);
	$res.filter('.scroll-slider-wrap').each(function(){
		var $this = $(this);
		var $container = $('#'+$(this).data('id'));
		$container.html($this.html())
			.closest('.submenu-wrap').prepend($this.prev());
	});
	var $menu = b2.el.$menu || b2.elements.$menu;
	initHorizontalCarousels($menu);
	initToggles($menu);
	// trigger event for picturefill
	var event = document.createEvent("HTMLEvents");
	event.initEvent("DOMContentLoaded",true,false);
	window.dispatchEvent(event);
    RZB2.utils.initLazy($menu);
}; */
/* 
RZB2.ajax.Vote =
{
	arParams: {},
	trace_vote: function(my_div, flag)
	{
		if(flag)
			while( $(my_div).length > 0 ) {
				$(my_div).addClass('star-over');
				my_div = $(my_div).prev();
			}
		else
			while( $(my_div).length > 0 ) {
				$(my_div).removeClass('star-over');
				my_div = $(my_div).prev();
			}
	},
	
	do_vote: function(div, arParams, e)
	{
		e = e || window.event;
		e.preventDefault();
		e.stopPropagation();
		
		this.parentObj = $(div).closest('.rating');

		if (typeof this.spinner == 'undefined') {
			this.spinner = RZB2.ajax.spinner(this.parentObj);
			this.spinner.Start({color: RZB2.themeColor});
		}
		
		var vote_id = this.parentObj.data('itemid');
		var vote_value = $(div).data('value');

		arParams['vote'] = 'Y';
		arParams['vote_id'] = vote_id;
		arParams['rating'] = vote_value;
		arParams['PAGE_PARAMS'] = {'ELEMENT_ID':vote_id};
		
		BX.ajax({
			timeout:   30,
			method:   'POST',
			dataType: 'json',
			url:       '/bitrix/components/bitrix/iblock.vote/component.php',
			data:      arParams,
			onsuccess: BX.delegate(this.SetResult, this)
		});	
	},
	
	SetResult: function(result)
	{
		this.parentObj.attr('data-rating', Number(result.value)).data('disabled', true);
		this.parentObj.find('i.flaticon-black13').removeAttr('onclick');
		
		this.parentObj.siblings().find('span.review-number').empty().html(result.votes);
		
		RZB2.ajax.showMessage(BX.message('BITRONIC2_IBLOCK_VOTE_SUCCESS'), 'success');

		if (typeof this.spinner == 'object') {
			this.spinner.Stop();
			this.spinner = undefined;
		}
	}
}
 */
/* RZB2.ajax.Stores = {
	Load: function($obj, params, callback){
		params = params || [];
		$.ajax({
			type: "POST",
			url: SITE_DIR + "ajax/sib/catalog_stores.php",
			data: params,
			success: function (res) {
				$obj.html(res);
				if (typeof callback == "function") {
					callback(res);
				}
			}
		});
	}
}; */


RZB2.ajax.FormUnified = function (arParams) {
	this.modalId = '';
	this.ajaxPath = '';
	
	if ('object' === typeof arParams)
	{
		this.modalId = arParams.ID;
		this.ajaxPath = arParams.AJAX_FILE;
	}
};

RZB2.ajax.FormUnified.prototype.Load = function (params, callback) {
	var $form = $('#' + this.modalId).find('.content');
	$form.empty();
	params = params || [];
	var data = params;
	this.SendRequest(data, $form, false, callback);
};
	
RZB2.ajax.FormUnified.prototype.Post = function ($form, callback, close, refresh) {
	var data = $form.serializeArray();
	close   = (typeof close   == "undefined") ? true  : !!close;
	refresh = (typeof refresh == "undefined") ? false : !!refresh;
	
	this.SendRequest(data, $form, close, callback, refresh);
};
	
RZB2.ajax.FormUnified.prototype.SendRequest = function (data, objLoad, close, callback, refresh) {
	refresh = (typeof refresh == 'undefined') ? true : !!refresh ;
	if (typeof(data) == 'undefined') {
		data = [];
	}
	data.push({name: "rz_ajax", value: 'Y'});
	var _this = this;
	if(!!objLoad.find('.button').length){
		objLoad.find('.button').addClass('disabled');
	}
	var spinner = RZB2.ajax.spinner(objLoad);
	if (!RZB2.utils.checkPrivityPolicy(objLoad)) return false;
	spinner.Start({color: RZB2.themeColor});
	//RZB2.ajax.loader.Start(objLoad);
	$.ajax({
		type: "POST",
		url: this.ajaxPath,
		data: data,
		success: function (res) {
			_this.Refresh(res, objLoad, close, spinner, refresh);
			if(!!objLoad.find('.button').length){
				objLoad.find('.button').removeClass('disabled');
			}
			if (typeof callback == "function") {
				callback(res);
			}
		}
	})
};
	
RZB2.ajax.FormUnified.prototype.Refresh = function (res, objLoad, close, spinner, refresh) {
	if(close)
	{
		$('#' + this.modalId).find('[data-fancybox-close]').click();
		if (typeof recalcBasketAjax == "function") {
			recalcBasketAjax();
		}
		RZB2.ajax.BasketSmall.Refresh(true);
	}
	if (typeof spinner == 'object') {
		spinner.Stop();
		delete spinner;
	}
	if (refresh) {
		objLoad.html(res);
        RZB2.utils.initLazy($(objLoad));
	} else {
		$("<div></div>").html(res).empty().remove();
	}
	//RZB2.ajax.loader.Stop(objLoad);
};


// FEEDBACK MODALS

$.fn.rise_modal = function (add_data) {
	if (typeof add_data == 'undefined') {
		add_data = [];
	}
	var $form = this;
	/*
	 if ($form.data('has_opened') == true) {
	 return true;
	 }
	 */
	var data = [];
	if (add_data.length > 0) {
		data = $.merge(data, add_data);
	}
	/*var spinner = {};
	if (typeof $form.data('spinner') == 'undefined') {
		spinner = RZB2.ajax.spinner($form);
		$form.data('spinner', spinner);
	} else {
		spinner = $form.data('spinner');
	}
	spinner.Start({color: RZB2.themeColor});*/
	data.push({'name': 'ajax', 'value': $form.data('ajax')});
	data.push({'name': 'rz_option_name', 'value': $form.data('rzoption')});
	return $.ajax({
		type: 'POST',
		url: SITE_DIR + 'ajax/sib/detail_modals.php',
		data: data,
		success: function (msg) {
			$form.html(msg);
			//spinner.Stop();
			//$form.data('has_opened', true);
		}
	})
};
$.fn.send_modal = function (add_data) {
	if (typeof add_data == 'undefined') {
		add_data = [];
	}
	var $form = this;
	var data = $form.serializeArray();
	if (add_data.length > 0) {
		data = $.merge(data, add_data);
	}
	/*var spinner = {};
	if (typeof $form.data('spinner') == 'undefined') {
		spinner = RZB2.ajax.spinner($form);
		$form.data('spinner', spinner);
	} else {
		spinner = $form.data('spinner');
	}
	spinner.Start({color: RZB2.themeColor});*/
	data.push({'name': 'ajax', 'value': $form.data('ajax')});
    //data.push({'name': 'rz_option_name', 'value': $form.data('rzoption')});
	return $.ajax({
		type: 'POST',
		url: SITE_DIR + 'ajax/sib/detail_modals.php',
		data: data,
		success: function (msg) {
			$form.html(msg);
			//spinner.Stop();
		}
	})
};
// END FEEDBACK MODALS


RZB2.ajax.updateCatalogParametersCache = function (callback)
{
	return $.get(SITE_DIR + 'catalog/', {rz_update_catalog_parameters_cache: 'Y'}, function() {
		if (typeof callback == "function") callback();
	});
}