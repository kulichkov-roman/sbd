function getBlockToDetail(templateName, filter, params, block, replace, ajaxDone){
	var data = {},
		currentBlock = $(block);
	
/* 	ajaxDone = typeof ajaxDone === 'object' ? ajaxDone : {is: false};

	data.rz_ajax = 'y';
	data.filter = filter;
	data.params = params;
	data.template = templateName;

	$.ajax({
		url: SITE_DIR + 'ajax/sib/service_ajax.php',
		async: false,
		type: "POST",
		data: data,
		dataType: 'html',
		success: function(data){
			currentBlock.find(replace).html($(data).find(replace).html()); */
			
			currentBlock.find('ul').on('init', function(event, slick){
				RZB2.ajax.BasketSmall.RefreshButtons(currentBlock);
				RZB2.ajax.quickViewInit($(this), '.js-fancybox-accessory');

				shaveHeight = 72;
				if(Modernizr.mq('(max-width: 1023px)')) shaveHeight = 58;
				$(this).find('.catalog-title__link span').shave(shaveHeight); 
			});
			
			if(!window.isIOS){
				$('.rbs-hor-catalog.rbs-ajax-cmp').removeClass('rbs-hor-catalog');
				$('.rbs-hor-catalog__list.rbs-ajax-cmp').removeClass('rbs-hor-catalog__list');
				$(".js-slider-access-line").slick({dots:!0,arrows:!0,infinite:!1,autoplay:!1,swipeToSlide:!0,slidesToShow:5,slidesToScroll:1,touchThreshold:200,speed:300,adaptiveHeight:!0,/* lazyLoad:'ondemand', */responsive:[{breakpoint:1400,settings:{slidesToShow:4}},{breakpoint:1150,settings:{slidesToShow:3}},{breakpoint:1023,settings:{slidesToShow:3}},{breakpoint:700,settings:{slidesToShow:2}},{breakpoint:480,settings:{slidesToShow:1}}]});
			} else {
				currentBlock.find('ul').trigger('init', [currentBlock.find('ul')]);
				$('.rbs-hor-catalog img').lazyload({
					data_attribute: 'lazy-jpg',
					data_attribute_webp: 'lazy',
					container: '.rbs-hor-catalog__list.js-slider-access-line'
				});
			}

			/* ajaxDone.is = true;
		}
	}); */
}

/* 
Универсальный контроллер аякса для табов с элементами каталога
Вход:
0) Путь до аякс файла
1) Параметры компонента
2) Фильтр
3) Шаблон компонента
4) Главный идентификатор (ид) в теле аякса
5) Заменяемый блок (опционально)
*/
TabAjaxCatalogController = function(p){
	return {
		//Исходные данные
		ajaxDir: p.ajaxDir || '',
		params: p.params || {},
		filter: p.filter || {},
		template: p.template || '',
		hideAvailableBlock: p.hideAvailableBlock || false,
		//Селекторы
		tabId: p.tabId || '',
		replaceId: p.replaceId || '',
		//флаг загрузки таба
		tabLoaded: false,
		callback: null,

		getFullTab: function(callback){
			if(!this.tabLoaded){
				this.params.HIDE_AVAILABE = p.hideAvailableBlock ? 'Y' : 'N';
				this.callback = callback;
				this.sendRequest(this.tabReplaceHandler);
			}
				
		},

			tabReplaceHandler: function(data, context){
				$(context.tabId)
					.html(data.find(context.tabId).html());
				context.tabLoaded = true;

				context.refreshTab($(context.tabId));
			},


		changeSection: function(sectionId){
			
			if(+sectionId == 0 && !this.filter.PROPERTY_TIP_AKSESSUARA_1){
				return;
			}

			$('.accessories-nav__item').removeClass('active');
			$('[data-section-id="' + sectionId + '"]').addClass('active');

			if(this.filter.PROPERTY_TIP_AKSESSUARA_1 != sectionId){
				if(sectionId == 0)
					delete this.filter.PROPERTY_TIP_AKSESSUARA_1;
				else
					this.filter.PROPERTY_TIP_AKSESSUARA_1 = sectionId;

				this.sendRequest(this.catalogReplaceHandler);
			}
		},

		changeSort: function(obj){
			var _this = $(obj);
			_this.data('by', _this.data('by') == 'ASC' ? 'DESC' : 'ASC');
		
			this.params.ELEMENT_SORT_FIELD2 = _this.data('sort');
			this.params.ELEMENT_SORT_ORDER2 = _this.data('by');
		
			_this.closest('ul').find('li').removeClass('active');
			_this.closest('li').addClass('active');
			_this.find('i.icon-arrow-filter').toggleClass('rbs-rotate-90');

			this.sendRequest(this.catalogReplaceHandler);
		},
			catalogReplaceHandler: function(data, context){
				$(context.tabId)
					.find(context.replaceId)
					.html(data.find(context.replaceId).html());

				context.refreshTab($(context.tabId));
			},

		sendRequest: function(callBack){
			var _this = this,
				data = {
					rz_ajax: 'y',
					filter: this.filter,
					params: this.params,
					template: this.template
				};

			this.startLoader();
			$.ajax({
				url: this.ajaxDir,
				type: "POST",
				async: false,
				data: data,
				dataType: 'html',
				success: function(data){
					callBack($('<div>'+data+'</div>'), _this);
					_this.stopLoader();
				}
			});
		},

		startLoader: function(){RZB2.ajax.loader.Start($(this.tabId))},
		stopLoader: function(){RZB2.ajax.loader.Stop($(this.tabId))},
		refreshTab: function(target){
			
			RZB2.ajax.BasketSmall.RefreshButtons(target);
			if (target.find(".js-rating").length) {
				target.find('.js-rating').barrating({
					showSelectedRating: false,
					readonly: true
				});
			}
			if(target.find(".js-accessories-nav__link").length){
				target.find(".js-accessories-nav__link").on("click", function() {
					$(this).parent().siblings().find(".accessories-cont").slideUp();
					$(this).parent().find(".accessories-cont").slideToggle();
					//target.find("img.lazy").lazyload();					
					return false;
				});
				/* $('select.js-formstyler').styler({}); */
				$('.catalog-item__overlay .js-close-loc').on('click', function (e) {
					e.preventDefault();
					$('#basket .js-remove-button[data-product-id="' + $(this).data('product-id') + '"]').click();
				});
			}
			if(target.find('.accessories-nav__item').length && target.find('.accessories-nav__item.active').length == 0 && this.template != 'sib_detail_list_tab_mobile'){
				target.find('.accessories-nav__item').first().addClass('active');
			}
			RZB2.ajax.quickViewInit(target);
			setTimeout(function(){
				target.find('.js-ellip-2 span').shave(80); 
				$('img.lazy').lazyload({}).removeClass('placeholder').removeClass('lazy');
			}, 300);
			if(this.template == 'sib_detail_list_tab_mobile'){
				target.find('.js-accessories-nav__link').one('click', function(){
					sliderCatalog = $(this).siblings('.accessories-cont').find('.js-mobile-tab-catalog');
					setTimeout(function(){
						sliderCatalog.on('init', function(){
							//$(this).find("img.lazy").lazyload();
						});
						sliderCatalog.slick({
							dots: true,
							arrows: true,
							infinite: true,
							autoplay: false,
							swipeToSlide: true,
							slidesToShow: 1,
							slidesToScroll: 1,
							touchThreshold: 200,
							speed: 300,
							adaptiveHeight: true
						});
					}, 0);
				});
			}
			
			if(typeof this.callback == 'function'){
				this.callback();
			}

		}
	}
};

TabAjaxQuickAskController = function(p){
	
	return {

		params: p.params || {},
		filter: p.filter || {},
		template: p.template || '',
		tabId: p.tabId || '',

		activePage: 1,
		activeSort: 'date',

		init: function(){
			var _this = this;

			_this.sortField = $(_this.tabId).find('#rbs-tab-ask-sort');
			_this.pagerButton = $(_this.tabId).find('.paging-list li');
			_this.formAsk = $(_this.tabId).find('#popup-faq form');
			_this.formAns = $(_this.tabId).find('#popup-answer form');
			_this.buttonAnswer = $(_this.tabId).find('a[href="#popup-answer"]');
			_this.like = $(_this.tabId).find('li.answer-social__item>a');

			_this.sortField.off().on('change', function(){
				_this.activeSort = $(this).val();
				_this.sendRequest(_this.refreshTab, {type: 'upd'});
			});

			_this.pagerButton.off().on('click', function(e){
				e.preventDefault();
				_this.pagerButton.removeClass('active');
				$(this).addClass('active');
				_this.changePage();
			});

			_this.buttonAnswer.off().on('click', function(){
				_this.formAns.find('[name="askId"]').val($(this).data('ask-id'));
			});
 
			_this.formAsk.off().on('submit', function(e){
				e.preventDefault();
				_this.sendRequest(_this.addAsk, {
					itemId: $(this).find('[name="itemId"]').val() || 0,
					email: $(this).find('[name="email"]').val() || 0,
					name: $(this).find('[name="name"]').val() || 0,
					ask: $(this).find('[name="ask"]').val() || 0,
					type: 'ask'
				}, $(this));
			});

			_this.formAns.off().on('submit', function(e){
				e.preventDefault();
				_this.sendRequest(_this.addAns, {
					itemId: $(this).find('[name="itemId"]').val() || 0,
					email: $(this).find('[name="email"]').val() || 0,
					name: $(this).find('[name="name"]').val() || 0,
					ans: $(this).find('[name="ans"]').val() || 0,
					askId: $(this).find('[name="askId"]').val() || 0,
					type: 'ans'
				}, $(this));
			});

			_this.like.off().on('click', function(e){
				e.preventDefault();
				_this.sendRequest(_this.refreshLike, {
					ansId: $(this).data('ans-id'),
					askId: $(this).data('ask-id'),
					rate: $(this).data('type'),
					type: 'rate' 
				}, $(this).closest('.faq-list__row_answer'));
			});
		},
			changePage: function(){
				if(this.activePage != +this.pagerButton.siblings('.active').text()){
					this.activePage = +this.pagerButton.siblings('.active').text();
					$('html, body').animate({ scrollTop: $(this.tabId).find('.rbs-ask-header').offset().top - $('header').height() }, 500);
					this.sendRequest(this.refreshTab, {
						PAGEN_1: this.activePage,
						type: 'upd'
					});
				}				
			},

			addAsk: function(data, _this){
				data = jQuery.parseJSON(data);
				classMsg = data.TYPE == 'OK' ? 'rbs-success-msg' : 'rbs-error-msg';
				_this.formAsk.closest('.popup__main').prepend($('<div class="' + classMsg + '">' + data.MSG + '</div>'));
				_this.formAsk.closest('.rbs-form-content').hide();
				_this.formAsk.closest('.popup-answer').find('.fancybox-close-small').on('click', function(){
					setTimeout(function(){
						_this.formAsk.closest('.popup__main').find('.' + classMsg).remove();
						_this.formAsk.closest('.rbs-form-content').show();
						_this.formAsk.trigger('reset');
					}, 500);
				});				
			},

			addAns: function(data, _this){
				data = jQuery.parseJSON(data);
				classMsg = data.TYPE == 'OK' ? 'rbs-success-msg' : 'rbs-error-msg';
				_this.formAns.closest('.popup__main').prepend($('<div class="' + classMsg + '">' + data.MSG + '</div>'));
				_this.formAns.closest('.rbs-form-content').hide();
				_this.formAns.closest('.popup-answer').find('.fancybox-close-small').on('click', function(){
					setTimeout(function(){
						_this.formAns.closest('.popup__main').find('.' + classMsg).remove();
						_this.formAns.closest('.rbs-form-content').show();
						_this.formAns.trigger('reset');
					}, 500);
				});		
			},
		
		sendRequest: function(callBack, data, loader){
			var _this = this;

			loader = loader || $(this.tabId);

			data.rz_ajax = 'y';
			data.filter = this.filter;
			data.params = this.params;
			data.template = this.template;

			data.sort = this.activeSort;
			data.params.AJAX = 'Y';

			this.startLoader(loader);
			$.ajax({
				url: this.params.AJAX_DIR,
				type: "POST",
				data: data,
				dataType: 'html',
				success: function(data){
					callBack(data, _this);
					_this.stopLoader(loader);
				}
			});
		},
	
		startLoader: function(loader){RZB2.ajax.loader.Start(loader)},
		stopLoader: function(loader){RZB2.ajax.loader.Stop(loader)},

		refreshTab: function(data, _this){			
			$(_this.tabId).find('.rbs-ask-content').html($(data).siblings('.rbs-ask-content').html());
			_this.init();

			if ($(_this.tabId).find('.js-fancybox').length) {
				$(_this.tabId).find('.js-fancybox').fancybox({
					'beforeClose': function () {
						$("body").removeClass("popup-open");
					}
				});
			}

			$(_this.tabId).find(".js-close-answer").click(function () {
				$(this).parents('.faq-list__row_answer').slideUp();
				return false;
			});
			$(_this.tabId).find(".js-show-answer").click(function () {
				$(this).parents('.faq-list__item').find('.faq-list__row_answer').slideDown();
				return false;
			});

		},

		refreshLike: function(data, _this){
			data = jQuery.parseJSON(data);
			if(data.TYPE == 'OK'){
				$('[data-type="' + data.rate + '"][data-ans-id="' + data.ansId + '"]').siblings('span').html(data.COUNT);
			}
		}

	};
};