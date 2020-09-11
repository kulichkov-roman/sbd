$(window).on('b2ready', function(){
	//COMPARE
	$('#popup_compare').on('click', '.table-wrap button.btn-delete', function(e){
		e.preventDefault();
		RZB2.ajax.Compare.Delete($(this).data('id'));
	});
	$('#popup_compare').on('click', '.popup-footer button.btn-delete', function(e){
		e.preventDefault();
		RZB2.ajax.Compare.DeleteAll();
	});
	RZB2.ajax.Compare.RefreshButtons();
	
	//FAVORITES
	$('#popup_favorites').on('click', '.table-wrap button.btn-delete', function(e){
		e.preventDefault();
		RZB2.ajax.Favorite.Delete($(this).data('id'));
		// RZB2.ajax.Favorite.Refresh();
	});
	$('#popup_favorites').on('click', '.popup-footer button.btn-delete', function(e){
		e.preventDefault();
		RZB2.ajax.Favorite.DeleteAll();
	});
	$('#popup_favorites').on('click', '.popup-footer button.btn-main', function(e){
		e.preventDefault();
		RZB2.ajax.Favorite.ToBasket.AddAll();
	});
	RZB2.ajax.Favorite.RefreshButtons();

	
	// SMALL BASKET
	var timerQuantity, timerTimeout;
	$('#popup_basket .basket-content').on({
			'mousedown': function(e){
				e = e || window.event;
				var _ = $(this);
				var quanInput = _.siblings('.quantity-input');
				if ( quanInput.hasClass('disabled') || quanInput.is(':disabled') || _.hasClass('disabled') ) return;
				var curValue = Number(quanInput.val());
				var ratio = parseFloat(quanInput.data('ratio'));
				
				var changeStep = ratio;
				if ( _.hasClass('decrease') ) changeStep = -ratio;
				if (isNaN(curValue) || curValue<1) curValue = ratio;
				

				curValue += changeStep;
				if ( curValue < ratio ) curValue = ratio;
				quanInput.val(curValue);
				timerTimeout = setTimeout(function(){
					timerQuantity = setInterval(function(){
						curValue += changeStep;
						if ( curValue < ratio ) curValue = ratio;
						quanInput.val(curValue);
					}, 100);
				}, 300);
				_.data('changed', true);
			},
			'mouseup mouseleave': function(e){
				clearTimeout(timerTimeout);
				clearInterval(timerQuantity);
				if (!$(this).data('changed')) return;
				
				RZB2.ajax.BasketSmall.ChangeQuantity(e);
			}
		}, 'button.quantity-change'
	).on('change','input[name=quantity]', function (e) {
			RZB2.ajax.BasketSmall.ChangeQuantity(e);
	}).on('click','button.btn-delete', function (e) {
			e = e || window.event;
			e.preventDefault();
			
			RZB2.ajax.BasketSmall.Delete(e);
	});
	
	$('#popup_basket .popup-footer').on('click', 'button.btn-delete', function(e) {
		e = e || window.event;
		e.preventDefault();
		
		RZB2.ajax.BasketSmall.DeleteAll(e);
	});
	RZB2.ajax.BasketSmall.RefreshButtons();
	
	var smallSpinnerParams = {radius: 3, width: 2};
	
	// CATALOG - change quantity
	$('.catalog-page .show-by select.show-by').on('change', function(e){
		e.stopPropagation();
		
		var $select = $('.catalog-page .show-by select.show-by');
		$select.find('option[value="'+$(this).val()+'"]').prop('selected', true);
		$select.ikSelect('reset');
		
		RZB2.ajax.CatalogSection.Start(this, {'page_count':$(this).val()});
	});
	
	// CATALOG - change view
	$('.catalog-page .sort-n-view .view-type a').on('click', function(e){
		e.stopPropagation();
		e.preventDefault();

		var $_ = $(this);
		
		if($_.hasClass('active'))
		{
			return;
		}
		else
		{
			$_.addClass('active').siblings('a').removeClass('active');
			RZB2.ajax.CatalogSection.Start(this, {'view':$_.data('view')}, smallSpinnerParams);
		}
	});
	
	// CATALOG - change page
	$('.catalog-page .pagination').on('click', 'a',function(e){
		e.preventDefault();
		e.stopPropagation();
		if (!$(this).hasClass('active'))
		{
			var pagenKey = $(this).attr('data-pagen-key');
			var params = {};
			params[$(this).attr('data-pagen-key')] = $(this).attr('data-page');
			RZB2.ajax.CatalogSection.Start(this, params, smallSpinnerParams);
		}
	});
	// CATALOG - change page by infinity loader
	$('.catalog-page').on('click', '.more-catalog', function (e) {
		e.preventDefault();
		e.stopPropagation();
		if (!$(this).hasClass('disabled')) {
			var pagenKey = $(this).attr('data-pagen-key');
			var params = {};
			params[$(this).attr('data-pagen-key')] = $(this).attr('data-page');
			params['MORE_CLICK'] = 1;
			RZB2.ajax.CatalogSection.Start($(this).find('.btn-plus'), params);
		}
	});
	
	// CATALOG - change sort MOBILE
	$('.catalog-page #sort-by').on('change', function(e){
		var sortItem = $(this).find('option:selected');
		RZB2.ajax.CatalogSection.Start(this, {'sort': sortItem.data('sort') , 'by' : sortItem.data('sort-by')}, smallSpinnerParams);
	});
	
	// CATALOG - change sort
	$('.catalog-page ul.sort-list li').on('click', function(e){
		if($(this).hasClass('active'))
		{
			if($(this).data('sort-by') == 'asc')
			{
				$(this).data('sort-by', 'desc');
			}
			else
			{
				$(this).data('sort-by', 'asc');
			}
		}
		RZB2.ajax.CatalogSection.Start(this, {'sort': $(this).data('sort') , 'by' : $(this).data('sort-by')}, smallSpinnerParams);		
	});
	
	// CATALOG - TABLE - add to basket list
	$('.catalog-page').on('click', '#add_basket_table', function(e){
		$(this).css('position', 'relative').addClass('disabled');
		RZB2.ajax.CatalogSection.Table.AddToBasket(this, {radius: 5, color: RZB2.themeColor, top: '45%'});
	});

	// CATALOG ELEMENT - EDOST
	$('.calc-delivery').on('click', function(e){
		var _ = $(this);
		e.preventDefault();
		edost_catalogdelivery_show(_.data('id'), _.data('name'));
	});

	// REVIEWS
	//Blog
	RZB2.ajax.Review.Blog.Refresh();
	$('#comments').on('submit', '#form_comment_blog', function (e) {
		e.preventDefault();
		var $this = $(this),
			data = $this.serializeArray();
		RZB2.ajax.Review.Blog.SendRequest($this, data, false);
	});
	
	$('#comments').on('click', '#blog_comments .pagination a', function (e) {
		e.preventDefault();
		var $this = $(this);
		if(!$this.hasClass('active')) 
		{
			RZB2.ajax.Review.Blog.ChangePage($this, $this.data('page'));
		}
	});
	
	//Forum
	RZB2.ajax.Review.Forum.Refresh();
	$('#comments').on('submit', '#form_comment_forum', function (e) {
		e.preventDefault();
		var $this = $(this),
			data = $this.serializeArray();
		RZB2.ajax.Review.Forum.SendRequest(data);
	});
	
	$('#comments').on('click', '#forum_comments .pagination a', function (e) {
		e.preventDefault();
		var $this = $(this);
		if(!$this.hasClass('active') || !$this.hasClass('disabled')) 
		{
			RZB2.ajax.Review.Forum.ChangePage($this.data('page'), $this.data('pagen-key'));
		}
	});
	
	// CALL ME
	$('.contacts-content').on('click', '.phone-link', function(e){
		if ( !Modernizr.mq('(max-width: 767px)') ){
			e.preventDefault();
			$('#modal_callme').modal('show');
			if(typeof RZB2.ajax.callMe === 'undefined')
			{
				RZB2.ajax.callMe = new RZB2.ajax.FormUnified({
					ID: 'modal_callme',
					AJAX_FILE: SITE_DIR + "ajax/sib/callme.php",
				});
			}
			RZB2.ajax.callMe.Load();
		}
	});
	
	$('#modal_callme').on('submit', 'form', function(e){
		e.preventDefault();
		RZB2.ajax.callMe.Post($(this));
	});

	// FEEDBACK
	$(document).on('click', '.feedback-link', function (e) {
		e.preventDefault();
		$('#modal_feedback').modal('show');
		if (typeof RZB2.ajax.feedBack === 'undefined') {
			RZB2.ajax.feedBack = new RZB2.ajax.FormUnified({
				ID: 'modal_feedback',
				AJAX_FILE: SITE_DIR + "ajax/sib/feedback.php",
				URL: location.pathname
			});
		}
		RZB2.ajax.feedBack.Load();
	});

	$('#modal_feedback').on('submit', 'form', function (e) {
		e.preventDefault();
		RZB2.ajax.feedBack.Post($(this));
	});

	// SEARCH AJAX
	$('#search').on('click', 'button.btn-buy', function(e){
		e.preventDefault();
		if ($(this).hasClass('main-clicked') && $(this).hasClass('forced')) {
			location.href = $("#bxdinamic_bitronic2_basket_string").attr("href");
			return;
		}
		var spinner = RZB2.ajax.spinner($(this));
		spinner.Start(smallSpinnerParams);
		RZB2.ajax.CatalogSection.AddToBasketSimple($(this).data('product-id'), 1, spinner);
	})
	.on('change', '#search-area', function(e){
		var _ = $(this);
		var $form = _.closest('form');
		_.find('option').each(function(){
			var category = 'category_' + $(this).data('category');
			if ($(this).val() == _.val()) {
				$form.addClass(category);
			} else {
				$form.removeClass(category);
			}
		});
		setTimeout(function(){
			$('#popup_ajax-search').velocity("finish");
			$('#search-field').focus();
		}, 50);
	});
	
	// BUY IN ONE CLICK
	$('.card-main-aside, .product-page, .catalog-page, .special-blocks, .search-results-page, .basket-big-page, .small-basket-buy-wrap').on('click', '.one-click-buy', oneClickBuyHandler);
	$('.viewed-products').find('.one-click-buy').on('click', function(e){
		$($(this).data('target')).modal('show');
		oneClickBuyHandler.call(this, e);
	});
	
	$('#modal_quick-buy').on('submit', 'form', function(e){
		e.preventDefault();
		RZB2.ajax.oneClick.Post($(this));
	});
	
	// CATALOG SET CONSTRUCTOR
	$('.product-page').on('click', '.collection-wrap .btn-main', function(e){
		e.preventDefault();
		RZB2.ajax.CatalogSetConstructor.AddToBasket(this);
	});
	
	$('.product-page').on('click', '.custom-collection', function(e){
		e.preventDefault();
		if(typeof RZB2.ajax.SetConstructor !== 'undefined')
		{
			RZB2.ajax.SetConstructor.Load();
		}
	});

	// QUICK VIEW
	if (typeof RZB2.ajax.quickView == "object") {
		$('#modal_quick-view')
			.on('shown.bs.modal',  RZB2.ajax.quickView.onModalShown)
			.on('hidden.bs.modal', RZB2.ajax.quickView.onModalHidden);
	}
	// DETAIL MODALS - CRY FOR PRICE, PRICE_DROPS, EXIST_PRODUCT
	var $doc = $(document);
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
		var spinner = {};
		if (typeof $form.data('spinner') == 'undefined') {
			spinner = RZB2.ajax.spinner($form);
			$form.data('spinner', spinner);
		} else {
			spinner = $form.data('spinner');
		}
		spinner.Start({color: RZB2.themeColor});
		data.push({'name': 'ajax', 'value': $form.data('ajax')});
		return $.ajax({
			type: 'POST',
			url: SITE_DIR + 'ajax/sib/detail_modals.php',
			data: data,
			success: function (msg) {
				$form.html(msg);
				spinner.Stop();
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
		var spinner = {};
		if (typeof $form.data('spinner') == 'undefined') {
			spinner = RZB2.ajax.spinner($form);
			$form.data('spinner', spinner);
		} else {
			spinner = $form.data('spinner');
		}
		spinner.Start({color: RZB2.themeColor});
		data.push({'name': 'ajax', 'value': $form.data('ajax')});
		return $.ajax({
			type: 'POST',
			url: SITE_DIR + 'ajax/sib/detail_modals.php',
			data: data,
			success: function (msg) {
				$form.html(msg);
				spinner.Stop();
			}
		})
	};
	var $mcfp = $('#modal_cry-for-price');
	if ($mcfp.length > 0) {
		$mcfp.on('shown.bs.modal', function (e) {
			var $btn = $(e.relatedTarget);
			var data = [
				{'name': 'PRICE', 'value': $btn.data('price')},
				{'name': 'CURRENCY', 'value': $btn.data('currency')},
				{'name': 'PRODUCT', 'value': $btn.data('product')},
				{'name': 'PRICE_TYPE', 'value': $btn.data('price_type')}
			];
			$(this).find('form').rise_modal(data);
		});
	}

	$doc.on('submit', '.form_cry-for-price', function (e) {
		var $this = $(this);
		if (!formCheck($this)) {
			return false;
		} else {
			e.preventDefault();
			$this.send_modal();
			return false;
		}
	});
	var $miwpd = $('#modal_inform-when-price-drops');
	if ($miwpd.length > 0) {
		$miwpd.on('shown.bs.modal', function () {
			var $form = $(this).find('form');
			var $btn = $('#button_price_drops');
			var data = [
				{'name': 'PRICE', 'value': $btn.data('price')},
				{'name': 'CURRENCY', 'value': $btn.data('currency')},
				{'name': 'PRODUCT', 'value': $btn.data('product')},
				{'name': 'PRICE_TYPE', 'value': $btn.data('price_type')}
			];
			$.when($form.rise_modal(data)).then(function () {
				var moneyFormat = wNumb({
					mark: '.',
					thousand: ' ',
					decimals: 2
				});
				var thisModal = $('#modal_inform-when-price-drops');
				var currentPriceField = $('#price-current').children('.value');
				var currentPrice = moneyFormat.from(currentPriceField.html());
				thisModal.on('show.bs.modal', function () {
					currentPrice = moneyFormat.from(currentPriceField.html());
				});

				var npSlider = $('.desired-price-slider').noUiSlider({
					start: currentPrice * 0.9,
					connect: "lower",
					step: 1,
					range: {
						'min': 1,
						'max': currentPrice
					},
					format: moneyFormat
				});

				npSlider.Link('lower').to($('#desired-price>.value'));
				npSlider.Link('lower').to($('#modal_inform-when-price-drops_price'));

				var desiredPriceField = $('#desired-price').children('.value');
				var priceDifferenceField = $('#price-difference').children('.value');
				var priceDifferencePercentField = $('#price-difference').children('.percent-value');

				var desiredPrice = moneyFormat.from(desiredPriceField.html());
				var priceDifference = currentPrice - desiredPrice;
				var priceDifferencePercent = Number((priceDifference / currentPrice) * 100).toFixed(2);

				function setDifference() {
					priceDifferenceField.html(moneyFormat.to(priceDifference));
					priceDifferencePercentField.html('(' + priceDifferencePercent + '%)');
				}

				setDifference();

				npSlider.on('slide set', function () {
					desiredPrice = moneyFormat.from($(this).val());
					priceDifference = currentPrice - desiredPrice;
					priceDifferencePercent = Number((priceDifference / currentPrice) * 100).toFixed(2);
					setDifference();
				});
			});
		});
	}

	$doc.on('keypress', '#modal_inform-when-price-drops_price', function (e) {
		if (e.which !== 13) return true;
		$(this).change();
		return false;
	});
	$doc.on('keypress', '#modal_inform-when-price-drops_email', function (e) {
		if (e.which !== 13) return true;
		$('#modal_inform-when-price-drops_price').focus();
		return false;
	});
	$doc.on('submit', '.form_inform-when-price-drops', function (e) {
		var $this = $(this);
		if (!formCheck($this)) {
			return false;
		} else {
			e.preventDefault();
			$this.send_modal();
			return false;
		}
	});
	var $msp = $('#modal_subscribe_product');
	if ($msp.length > 0) {
		$msp.on('shown.bs.modal', function (e) {
			var $btn = $(e.relatedTarget);
			var data = [
				{'name': 'PRODUCT', 'value': $btn.data('product')}
			];
			$(this).find('form').rise_modal(data);
		});
	}
	$doc.on('submit', '#modal_subscribe_product_form', function (e) {
		var $this = $(this);
		if (!formCheck($this)) {
			return false;
		} else {
			e.preventDefault();
			$this.send_modal();
			return false;
		}
	});

	// SHOW/HIDE ELEMENT-HITS
	$(document).on('hitstoggle', RZB2.visual.Hits.ToggleShow);
	RZB2.visual.Hits.InitShow();

	// COMPARE PAGE
	$('main.compare-page')
	.on('click', '.remove-property', function(e){
		e.preventDefault();
		var _ = $(this);
		var spinner = RZB2.ajax.spinner(_.closest('th, td'));
		spinner.Start({width:2, radius:5, color:RZB2.themeColor});

		RZB2.ajax.ComparePage.SendRequest(_, function(res){
			//stop spinner
			spinner.Stop();
			delete spinner;
			//remove deleted table row
			var tr = _.closest('tr');
			var tbody = tr.parent('tbody');
			var trClass = tr.attr('class');
			var scroller = $('.compare-outer-wrapper .scroller');
			scroller.height(scroller.height() - tr.height());
			$('.compare-table tr.'+trClass).remove();
			if (tbody.length > 0 && tbody.children().not('.section-header').length < 1) {
				scroller.height(scroller.height() - tbody.height());
				$('.compare-table tbody.'+tbody.attr('class')).remove();
			}
			//update list of deleted properties
			var $res = $(res);
			var $deletedRes = $res.find('.deleted-properties');
			var $deletedDiv = $('.deleted-properties');
			if ($deletedDiv.length > 0) {
				$deletedDiv.html($deletedRes.html());
			} else {
				$('main.compare-page').append($deletedRes);
			}
		});
	})
	.on('click', '.compare-switch, .compare-item .btn-close, .deleted-property a', function(e){
		e.preventDefault();
		var spinner = RZB2.ajax.spinner($(this));
		spinner.Start({width:2, radius:5});

		var $container = $('main.compare-page');
		RZB2.ajax.loader.Start($container);

		RZB2.ajax.ComparePage.SendRequest($(this), function(res){
			$body.off('.b2comparepage');
			$(window).off('.b2comparepage');

			var $res = $('<div>'+res+'</div>');
			var $main = $res.find('main.compare-page');//.andSelf().filter('main.compare-page');
			$container.html($main.html());
			initComparePage();
			$(window)
				.trigger('scroll.b2comparepage')
				.trigger('b2ready');
			RZB2.ajax.BasketSmall.RefreshButtons();
			RZB2.ajax.loader.Stop($container);
		});
	});
});


var oneClickBuyHandler = function (e) {
	e.preventDefault();
	var $this = $(this);
	if (typeof RZB2.ajax.oneClick === 'undefined') {
		RZB2.ajax.oneClick = new RZB2.ajax.FormUnified({
			ID: 'modal_quick-buy',
			AJAX_FILE: SITE_DIR + "ajax/sib/one_click.php",
		});
	}
	RZB2.ajax.oneClick.Load([
		{name: "id", value: $this.data('id')},
		{name: "RZ_BASKET", value: $this.data('basket')},
		{name: 'PROPS', value: $this.data('props')}
	]);
};
