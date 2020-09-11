var RZB2 = RZB2 || {ajax: {params: {}}};

if (typeof RZB2.ajax == "undefined") {
	RZB2.ajax = {params: {}};
}

RZB2.ajax.showMessage = function (text, type, title)
{
	$(document).ready(function(){
		if(!type)
			type = 'success';  // success , fail
		
		title = title || BX.message('BITRONIC2_ERROR_TITLE');
		var modal = $('#modal_' + type);
		
		
		if(modal.length)
		{
			if(title.length)
			{
				modal.find('.alert-title').html(title);
			}
			modal.find('.alert-text').html(text);
			modal.modal('show');	
		}
	});
}
RZB2.ajax.setLocation = function (curLoc)
{
	try {
		history.pushState(null, null, curLoc);
		return;
	} catch(e) {}
		location.hash = '#' + curLoc.substr(1)
}

RZB2.ajax.scrollPage = function($scrollToObj)
{
	if($scrollToObj.length && window.pageYOffset > $scrollToObj.offset().top-60) {
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


RZB2.ajax.spinner = function($obj)
{
	var spinner;

	var Start = function(params)
	{
		if (typeof spinner === 'undefined') {
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
		$obj.append(spinner.el);
	};
	var Stop = function(spinnerObj)
	{
		if (typeof spinner !== 'object') return;

		spinner.stop();
		delete spinner;
		$obj.css('pointer-events', '').removeClass('stop-selection');
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
	   .animate({opacity: 0.4}, 500);

	if(!notLoader)
		obj.addClass('ajax_loader');
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

	obj.removeClass('ajax_loader');
}

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
	
	RefreshButtons: function()
	{
		var compareButtons = $('button.btn-action.compare, button.action.compare, .compare-checkbox');
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
	},
	
	ButtonsViewStatus: function(obButns, active, silent)
	{
		active = !!active || false;
		silent = !!silent || false;
		if(active)
			obButns.addClass('toggled').attr('title', BX.message('BITRONIC2_COMPARE_DELETE')).tooltip('fixTitle');
		else
			obButns.removeClass('toggled').attr('title', BX.message('BITRONIC2_COMPARE_ADD')).tooltip('fixTitle');
			
		if(!silent)
			obButns.tooltip('show')
	},
	
	RefreshResult: function(res)
	{
		$('#popup_compare').empty().html($(res).find('#popup_compare').html());
		$('.btn-compare .items-inside').empty().html($(res).find('.btn-compare .items-inside').html());
		
		this.RefreshButtons();
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
		if(typeof this.ElementsList[id] !== 'undefined' )
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
		$.ajax({
			url: SITE_DIR + 'ajax/sib/favorites.php',
			type: "POST",
			data: data,
			dataType: 'html',
			success: function(res){
				if(typeof data[_this.Params.actionVar] === 'undefined')
					_this.RefreshResult(res);
				else
					_this.Refresh();
			}
		});
	},
	
	Refresh: function()
	{
		var data = {};
		this.SendRequest();
	},
	
	RefreshButtons: function()
	{
	
		var obButtons = $('button.btn-action.favorite, button.action.favorite');
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
	},
	
	ButtonsViewStatus: function(obButns, active, silent)
	{
		active = !!active || false;
		silent = !!silent || false;
		if(active)
			obButns.addClass('toggled').attr('title', BX.message('BITRONIC2_FAVORITE_DELETE')).tooltip('fixTitle');
		else
			obButns.removeClass('toggled').attr('title', BX.message('BITRONIC2_FAVORITE_ADD')).tooltip('fixTitle');
			
		if(!silent)
			obButns.tooltip('show')
	},
	
	RefreshResult: function(res)
	{
		$('#popup_favorites').empty().html($(res).find('#popup_favorites').html());
		$('.btn-favorites .items-inside').empty().html($(res).find('.btn-favorites .items-inside').html());
		
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

		this.ElementsList[params.id] = params.id;
		
		var $modal = $('#modal_basket');
		var $content = $modal.find('.content');
		$content.empty();
		var spinner = RZB2.ajax.spinner($modal.find('.modal-dialog'));
		spinner.Start({color: RZB2.themeColor});
		$modal.modal('show');
	
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
				initRatingStars($content);
				initTooltips($content);
				_this.RefreshButtons();
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
				_this.pricesTotal.old = Number(basket.find('#popup_basket .popup-footer .price').data('total-price'));
				
				var replaceContentSelectors = [
					'.basket-items-number',
					'.basket-items-text',
					'.basket-items-number-sticker',
					'.popup-header .basket-content .text',
					// 'a .basket-total-price',
					// '.popup-header .basket-content .total-price',
					// '#popup_basket .popup-footer .price',
					'.items-table',
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

				basket.find('#popup_basket .popup-footer .price').data('total-price', $data.find('#popup_basket .popup-footer .price').data('total-price'));
				
				switch(_this.addType)
				{
					case 'popup':
						if(typeof itemParams !== "undefined") {
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
				
				
				//if empty basket (1 tr it's header of table)
				if(basket.find('.items-table tr').length <= 1)
				{
					//close open basket
					$('a[data-target="#popup_basket"][data-toggle="um_popup"]').trigger('click');
				}
				else
				{
					initTooltips(basket.find('#popup_basket'));
				}
				RZB2.ajax.loader.Stop( basket.find('.items-table') );
				RZB2.ajax.BasketSmall.RefreshButtons();
			}
		});
	},

	RefreshButtons: function()
	{
		var obButtons = $('button.buy');
		if(obButtons.length)
		{
			var btnSelector = '';
			for (var id in this.ElementsList)
			{
				if(!!btnSelector.length) btnSelector += ',';
				btnSelector += '[data-product-id="' + this.ElementsList[id] + '"],[data-offer-id="' + this.ElementsList[id] + '"]';
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
	},
	
	ButtonsViewStatus: function(obButns, active, silent)
	{
		active = !!active || false;
		silent = !!silent || false;
		if(active)
			obButns.addClass('main-clicked forced').attr('title', BX.message('BITRONIC2_BASKET_REDIRECT')).tooltip({
				//'trigger': 'click',
				placement: 'auto',
				html: true,
				container: 'body'
			}).tooltip('fixTitle');
		else
			obButns.removeClass('main-clicked forced').removeAttr('title').tooltip('destroy');
			
		if(!silent)
			obButns.tooltip('show')
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
				if (typeof window['recalcBasketAjax'] == 'function') recalcBasketAjax({}, true);
			}
		});
	},
	
	ChangeQuantity: function(obj, Params)
	{
		var data = {};
		if(!!obj)
		{
			var $table = $(obj.target).closest('.items-table');
			if ($table.hasClass('ajax_loader')) return;

			RZB2.ajax.loader.Start($table);

			var $itemContainer = $(obj.target).prop('disabled', true).closest('tr');
			var $quanInput = $itemContainer.find('input[name=quantity]');
			var quantity = this.CorrectRatioQuantity($quanInput.val(), $quanInput.data('ratio'));
			data['action'] = 'setQuantity';
			data['id'] = $itemContainer.data('id'); 
			data['productId'] = $itemContainer.data('product-id');
			if(isNaN(quantity))
			{
				quantity = parseInt(obj.target.defaultValue, 10);
			}
			data['quantity'] = quantity;
			if (typeof this.ElementsList[data['productId']] != 'undefined') delete this.ElementsList[data['productId']];
		}
		this.Reload(data);		
	},

	CorrectRatioQuantity: function(quantity, ratio)
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
	},
	
	Delete: function(obj)
	{
		$(obj.target).parents('tr').find('input[name=quantity]').val(0);
		this.ChangeQuantity(obj);
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
				urlParams += key + '=' + this.filterParams[key];
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
					case 'where':
					case 'q':
					case 'PAGEN_1':
					case 'page_count':
					case 'view':
					case 'sort':
					case 'by':
					case 'spec':
					case 'rz_all_elements':
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
		spinner.Start(spinnerParams);
		
		return this.Reload(spinner, Params);		
	},
	
	Reload: function(spinner, params)
	{
		var objLoader = $('#catalog_section');
		
		var data = {
			'rz_ajax' : 'y',
			'site_id': SITE_ID,
		};
		
		for(var key in RZB2.ajax.params) 
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
			RZB2.ajax.scrollPage($('.sort-n-view'));
		};
		
		return $.ajax({
			url: SITE_DIR + 'ajax/sib/catalog.php?' + paramFilterString,
			type: "POST",
			data: data,
			dataType: 'html',
			success: function(data){
				RZB2.ajax.loader.Stop(objLoader);
				
				if (typeof spinner == 'object') {
					spinner.Stop();
					delete spinner;
				}
				
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
		var catalogClass = (RZB2.ajax.params['view'] == 'table') ? 'catalog-table' : RZB2.ajax.params['view'];

		var $catalogSection = $('#catalog_section');
		
		// for close dropdown list on ajax
		$catalogSection.find('.ik_select_link').each(function(){
			if(!$(this).siblings('.ik_select_dropdown').length)
			{
				$(this).click();
			}
		});
			
		$catalogSection.removeClass('blocks list catalog-table').addClass(catalogClass);

		if ('MORE_CLICK' in params) {
			var itemSelector = (RZB2.ajax.params['view'] == 'table') ? 'table' : '.catalog-item-wrap';
			var $lastItem = $catalogSection.children(itemSelector).last();
			var curOffset = $lastItem.offset().top;
			$catalogSection.append($(data).find('#catalog_section').html().trim());
		} else {
			$catalogSection.empty().html($(data).find('#catalog_section').html());
		}

		var paginatorSelector = '.pagination'; // !!!!!!
		var paginatorTextSelector = '.current-state'; // !!!!!!
		if($(data).find(paginatorSelector).length)
		{
			$(paginatorSelector).empty().html($(data).find(paginatorSelector).html());
		}
		else
		{
			$(paginatorSelector).empty();
		}
		if($(data).find(paginatorTextSelector).length)
		{
			$(paginatorTextSelector).empty().html($(data).find(paginatorTextSelector).html());
		}
		else
		{
			$(paginatorTextSelector).empty();
		}

		var $paginatorMore = $(data).find('.more-catalog-wrap'); // !!!!!!
		if ($paginatorMore.length > 0) {
			$($paginatorMore.selector).empty().html($paginatorMore.html());
		} else {
			$paginatorMore.empty();
		}

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
			initPhotoThumbs($catalogSection);
			initSelects($catalogSection);
		}
		initCatalogHover($catalogSection.parent());
		initTooltips($catalogSection);
		initRatingStars($catalogSection);
			
		RZB2.ajax.Compare.RefreshButtons();
		RZB2.ajax.Favorite.RefreshButtons();
		RZB2.ajax.BasketSmall.RefreshButtons();
		
		var exe = $('<div></div>');
		exe.html(data); // for execute JS in data

		if ('MORE_CLICK' in params) {
			$('html,body').animate({scrollTop: window.pageYOffset + $lastItem.offset().top - curOffset}, 0);
		}
	},
	
	AddToBasketSimple: function(id, quantity, spinner)
	{
		var data = {items:{}};
		data.action = 'addList';
		data.rz_ajax = 'y';
		if(Number(id)>0)
		{
			if(isNaN(quantity)) quantity = 1;
			
			data.items[id] = {
				id: id,
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
					for (var item in data.items) {
						RZB2.ajax.BasketSmall.ElementsList[item] = item;
					}
					RZB2.ajax.BasketSmall.Refresh();
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
		},
	}
}

// SERVICES ON DETAIL PAGE
RZB2.ajax.Services =
{
	AddToBasket: function(product_name, iblock_id, quantity)
	{
		var data = {items:{}};
		data.action = 'addList';
		data.rz_ajax = 'y';
		data.iblock_id = iblock_id;
		$('.buy-block-additional input[type="checkbox"]').filter(':checked').each(function(){

			var id = parseInt($(this).data('service-id'), 10);
				
			data.items[id] = {
				id: id,
				quantity: parseInt(quantity, 10),
				props: [
					{NAME: BX.message('MESS_FOR'), VALUE: product_name}
				]
			};
		});

		if (data.items.length < 1) return;
		
		this.SendRequest(data);
	},
		
	SendRequest: function(data)
	{
		$.ajax({
			url: SITE_DIR + 'ajax/sib/basket_sib.php',
			type: "POST",
			data: data,
			dataType: 'json',
			success: function(data){
				RZB2.ajax.BasketSmall.Refresh();
			}
		});
	}
};

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
				$container.closest('.submenu-wrap').prepend($container.find('button.show-hide-hits'));
				b2.el.$menuHits = $('.submenu-wrap .scroll-slider-wrap');
				
				var $menu = b2.el.$menu || b2.elements.$menu;
				initHorizontalCarousels($menu);
				initToggles($menu);
				return;
			}

			//anything else
			if ($container.find('.scroll-slider-wrap').length > 0) {
				$container.find('.one-click-buy').on('click', function(e){
					$($(this).data('target')).modal('show');
					oneClickBuyHandler.call(this, e);
				});
			}
			
			initHorizontalCarousels($container);
			initRatingStars($container);
			initTooltips($container);
			initToggles($container);
			RZB2.ajax.BasketSmall.RefreshButtons();
			RZB2.ajax.Favorite.RefreshButtons();
			RZB2.ajax.Compare.RefreshButtons();
		}
	});
};

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
};
/*
RZB2.ajax.Registration = 
{
	Refresh: function()
	{
		location.reload();
	},
	
	Reload: function(obj)
	{
		var objLoader = $('.basket-open-popup .basket');
		
		var data = {
			'rz_ajax' : 'y'
		};
		
		if(!!obj)
		{
		
			data = $(obj).serializeArray();
			data['register_submit_button'] = "Y";
		}

		this.SendRequest(data);
	},
	
	SendRequest: function(data)
	{
		var _this = this;
		$.ajax({
			url: SITE_DIR + 'ajax/sib/register.php',
			type: "POST",
			data: data,
			dataType: 'html',
			success: function(data){
				_this.Refresh(true);
			}
		});
	},
	
	Start: function(obj, Params)
	{	
		this.Reload(obj);		
	},
}*/

RZB2.ajax.Vote =
{
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
		
		this.parentObj = $(div).closest('.rating-stars');

		if (typeof this.spinner == 'undefined') {
			this.spinner = RZB2.ajax.spinner(this.parentObj);
			this.spinner.Start({color: RZB2.themeColor});
		}
		
		var vote_id = $(div).data('id');
		var vote_value = $(div).data('value');

		arParams['vote'] = 'Y';
		arParams['vote_id'] = vote_id;
		arParams['rating'] = vote_value;
		
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
		this.parentObj.attr('data-rating', Number(result.value)).attr('data-disabled', 'true');
		this.parentObj.find('i.flaticon-black13').removeAttr('onclick');
		
		this.parentObj.siblings().find('span.review-number').empty().html(result.votes);
		
		RZB2.ajax.showMessage(BX.message('BITRONIC2_IBLOCK_VOTE_SUCCESS'), 'success');

		if (typeof this.spinner == 'object') {
			this.spinner.Stop();
			this.spinner = undefined;
		}
	}
}

RZB2.ajax.Review =
{
	Blog:
	{
		ChangePage: function ($target, page) {
			var $blog_comments = $('#blog_comments');
			
			$target.addClass('active').siblings('a').removeClass('active');
			$blog_comments.find('[id ^= "blog-comment-page-"]').hide().filter('[id ^= "blog-comment-page-' + page + '"]').show();
			
			RZB2.ajax.scrollPage($blog_comments);
		},
		
		Refresh: function () {
			var $formBlogComment = $('#form_comment_blog');
			
			if(!$formBlogComment.length) return;
			
			var arBlogParams = $formBlogComment.serializeArray()
			var data = [];
			for(var key in arBlogParams)
			{
				if(arBlogParams[key].name == 'ID' || arBlogParams[key].name == 'IBLOCK_ID' || arBlogParams[key].name == 'ELEMENT_ID' || arBlogParams[key].name == 'sessid')
					data.push({name: arBlogParams[key].name, value: arBlogParams[key].value});
			}
			
			this.SendRequest(false, data, true);
		},
		
		SendRequest: function ($form, data, fullRefresh) {
			if (typeof(data) == 'undefined') {
				data = [];
			}
			data.push({name: "REQUEST_URI", value: RZB2.ajax.params['REQUEST_URI']});
			data.push({name: "SCRIPT_NAME", value: RZB2.ajax.params['SCRIPT_NAME']});
			data.push({name: "rz_ajax", value: 'Y'});
			data.push({name: "comment_mode", value: 'blog'});
			var $blog_comments = $('#blog_comments');
			RZB2.ajax.loader.Start($blog_comments);
			$.ajax({
				type: "POST",
				url: SITE_DIR + "ajax/sib/catalog_comments.php",
				data: data,
				success: function (res) {
					if(fullRefresh)
						$blog_comments.empty().html(res);
					else
					{
						$form.parent('.form-wrap').remove();
						$blog_comments.prepend(res);
					}
					RZB2.ajax.loader.Stop($blog_comments);
				}
			})
		},
	},
	
	Forum:
	{
		params: [],
		ChangePage: function (page, pagen_key) {
			var $forum_comments = $('#forum_comments');
			var data = [];
			
			$.extend(data,this.params);
			data.push({name: pagen_key, value: page});
			this.SendRequest(data);
			RZB2.ajax.scrollPage($forum_comments);
		},
		
		Refresh: function () {
			var $formForumComment = $('#form_comment_forum');
			
			if(!$formForumComment.length) return;
			
			var arForumParams = $formForumComment.serializeArray()
			var data = [];
			for(var key in arForumParams)
			{
				if(arForumParams[key].name == 'IBLOCK_ID' || arForumParams[key].name == 'ELEMENT_ID' || arForumParams[key].name == 'sessid')
				{
					data.push({name: arForumParams[key].name, value: arForumParams[key].value});
					this.params.push({name: arForumParams[key].name, value: arForumParams[key].value});
				}
			}
			
			this.SendRequest(data);
		},
		
		SendRequest: function (data) {
			if (typeof(data) == 'undefined') {
				data = [];
			}
			data.push({name: "REQUEST_URI", value: RZB2.ajax.params['REQUEST_URI']});
			data.push({name: "SCRIPT_NAME", value: RZB2.ajax.params['SCRIPT_NAME']});
			data.push({name: "rz_ajax", value: 'Y'});
			data.push({name: "comment_mode", value: 'forum'});
			var $forum_comments = $('#forum_comments');
			RZB2.ajax.loader.Start($forum_comments);
			$.ajax({
				type: "POST",
				url: SITE_DIR + "ajax/sib/catalog_comments.php",
				data: data,
				success: function (res) {
					$forum_comments.replaceWith(res);
					RZB2.ajax.loader.Stop($forum_comments);
				}
			})
		},
	},
}

RZB2.ajax.FormUnified = function (arParams) {
	this.modalId = '';
	this.ajaxPath = '';
	
	if ('object' === typeof arParams)
	{
		this.modalId = arParams.ID;
		this.ajaxPath = arParams.AJAX_FILE;
	}
};

RZB2.ajax.FormUnified.prototype.Load = function (params) {
	var $form = $('#' + this.modalId).find('.content');
	$form.empty();
	params = params || [];
	var data = params;
	this.SendRequest(data, $form);
};
	
RZB2.ajax.FormUnified.prototype.Post = function ($form) {
	var data = $form.serializeArray();
	
	this.SendRequest(data, $form, true)
};
	
RZB2.ajax.FormUnified.prototype.SendRequest = function (data, objLoad, close) {
	if (typeof(data) == 'undefined') {
		data = [];
	}
	data.push({name: "rz_ajax", value: 'Y'});
	var _this = this;
	var spinner = RZB2.ajax.spinner(objLoad);
	spinner.Start({color: RZB2.themeColor});
	//RZB2.ajax.loader.Start(objLoad);
	$.ajax({
		type: "POST",
		url: this.ajaxPath,
		data: data,
		success: function (res) {
			_this.Refresh(res, objLoad, close, spinner);
		}
	})
};
	
RZB2.ajax.FormUnified.prototype.Refresh = function (res, objLoad, close, spinner) {
	if(close)
	{
		$('#' + this.modalId).modal('hide');
		if (typeof recalcBasketAjax == "function") {
			recalcBasketAjax();
		}
		RZB2.ajax.BasketSmall.Refresh(true);
	}
	if (typeof spinner == 'object') {
		spinner.Stop();
		delete spinner;
	}
	objLoad.html(res);
	//RZB2.ajax.loader.Stop(objLoad);
};


//Compare Page
RZB2.ajax.ComparePage = {
	SendRequest: function(sender, params, callback){
		if (typeof params == "function" && typeof callback == "undefined") {
			callback = params;
			params = undefined;
		}
		var data = params || {};
		for (var key in RZB2.ajax.params) {
			data[key] = RZB2.ajax.params[key];
		}
		if (!!sender) {
			var href = $(sender).attr('href');
			if (!!href) {
				data['REQUEST_URI'] = href;
				var uriParams = RZB2.utils.getQueryVariable(null, href);
				for (var key in uriParams) {
					data[key] = uriParams[key];
				}
			}
		}
		data['rz_ajax'] = 'y';
		if (typeof callback != "function") {
			callback = null;
		}
		$.ajax({
			type: "POST",
			url: SITE_DIR + 'ajax/sib/catalog.php',
			data: data,
			dataType: "html",
			error: function(){
				window.location.assign(data['REQUEST_URI']);
			},
			success: callback,
		});
	}
};

//Main Spec
RZB2.ajax.MainSpecTab = function(params){
	this.tab = '';
	this.tabId = '';
	this.contentId = '';
	this.spinners = [];
	this.isLoaded = false;
	this.$tab = false;
	this.$content = false;
	if (typeof params == 'object') {
		this.tab = params.tab || this.tab;
		this.tabId = params.tabId || this.tabId;
		this.contentId = params.contentId || this.contentId;
	}
};
RZB2.ajax.MainSpecTab.prototype.Init = function() {
	var _ = this;
	if (!!_.tabId) {
		_.$tab = $('#'+_.tabId);
		_.$tab.on('b2SblockTabOpen', BX.delegate(_.Load, _));
		if (_.$tab.hasClass('shown')) {
			setTimeout(BX.delegate(_.Load, _), 10);
		}
		//_.spinners[_.spinners.length] = RZB2.ajax.spinner(_.$tab.find('.combo-header'));
		_.spinners[_.spinners.length] = RZB2.ajax.spinner($('.combo-links [href=#'+_.tabId+']'));
	}
	if (!!_.contentId) {
		_.$content = $('#'+_.contentId);
		_.spinners[_.spinners.length] = RZB2.ajax.spinner(_.$content);
	}
};
RZB2.ajax.MainSpecTab.prototype.Load = function() {
	var _ = this;
	if (_.isLoaded) return;

	_.isLoaded = true;
	_.SendRequest();
};
RZB2.ajax.MainSpecTab.prototype.SendRequest = function() {
	var _ = this;
	_.spinners.forEach(function(spinner){
		spinner.Start();
	});
	$.ajax({
		url: SITE_DIR + 'ajax/sib/main_spec_sib.php',
		type: "POST",
		data: {rz_ajax: 'y', tab: _.tab},
		error: function() {
			_.isLoaded = false;
			_.StopSpinners();
		},
		success: function(res){
			_.$content.html(res);
			_.StopSpinners();
			initSpecialBlocks(_.$tab);
			initPhotoThumbs(_.$content);
			initRatingStars(_.$content);
			initTooltips(_.$content);
			RZB2.ajax.BasketSmall.RefreshButtons();
			RZB2.ajax.Compare.RefreshButtons();
			RZB2.ajax.Favorite.RefreshButtons();
		}
	});
};
RZB2.ajax.MainSpecTab.prototype.StopSpinners = function() {
	this.spinners.forEach(function(spinner){
		spinner.Stop();
	});
}


RZB2.ajax.updateCatalogParametersCache = function (callback)
{
	return $.get(SITE_DIR + 'catalog/', {rz_update_catalog_parameters_cache: 'Y'}, function() {
		if (typeof callback == "function") callback();
	});
}