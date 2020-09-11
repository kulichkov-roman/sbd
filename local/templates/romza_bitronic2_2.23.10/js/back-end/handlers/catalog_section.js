function RZB2_initCatalogHandlers($){
	// CATALOG - change quantity
	$('.catalog-page .show-by select.show-by').on('change', function(e){
		e.stopPropagation();
		
		var $select = $('.catalog-page .show-by select.show-by');
		$select.find('option[value="'+$(this).val()+'"]').prop('selected', true);
		
		RZB2.ajax.CatalogSection.Start(this, {'page_count':$(this).val()});
	})
	.closest('.show-by.disabled').removeClass('disabled');
	
	// CATALOG - change view
	$('.catalog-page .tab-top .nav-tab-list').on('click', 'a', function(e){
        e.stopPropagation();
        e.preventDefault();
        $(this).parent().siblings().removeClass('active');
		var $_ = $(this);
		var parent = $_.parent();

		if (parent.hasClass('active'))
		{
			return;
		}
		else
		{
			parent.addClass('active');
			delete RZB2.ajax.params.PAGEN_1;
			RZB2.ajax.CatalogSection.Start(this, {'view':$_.data('view')}, smallSpinnerParams);
		}
	});

	// CATALOG - change page
	$('.catalog-page').on('click', '.pagination a',function(e){
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
    $('.js-rbs-sort').on('change', function(e){
    	/* var $this = $(this)
        	selectBox = $this.closest('.jq-selectbox.jqselect');

        if ( $this.hasClass('active') ){
            $this.toggleClass('sort-down');
            if ($(this).data('sortby') == 'asc')
                $(this).data('sortby', 'desc');
            else
                $(this).data('sortby', 'asc');
        } else {
            $this.addClass('active').siblings('.active').removeClass('active');
            selectBox.find('.jq-selectbox__select-text').removeClass('sort-down');
        }

        if ( $this.hasClass('sort-down') )
            selectBox.find('.jq-selectbox__select-text').addClass('sort-down');
        else
            selectBox.find('.jq-selectbox__select-text').removeClass('sort-down'); */
		var _this = $(this).find('option:checked');
        RZB2.ajax.CatalogSection.Start($(this), {'sort': _this.data('sort') , 'by' : _this.data('sort-by')}, smallSpinnerParams);
    });

	// CATALOG - change sort
	$('.catalog-page ul.sort-list li').on('click', function(e){
		if ($(this).hasClass('active'))
		{
			if ($(this).data('sort-by') == 'asc')
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

    $('.sorting').on('click', '.sorting__item', function(){
        var _ = $(this);
        if ( _.hasClass('active') ){
            _.toggleClass('sort-down');
        } else {
            _.addClass('active').siblings('.active').removeClass('active');
        }
    });

    $('.catalog-page ul.sort-list a').on('click', function(e){
        e.preventDefault();
    });

	// CATALOG - TABLE - add to basket list
	$('.catalog-page').on('click', '#add_basket_table', function(e){
		$(this).css('position', 'relative').addClass('disabled');
		RZB2.ajax.CatalogSection.Table.AddToBasket(this, {radius: 5, color: RZB2.themeColor, top: '45%'});
	});

	// CATALOG BRANDS
	$('.brands-catalog').on('togglebrand', function(e, data){
		if (typeof smartFilter !== "undefined") {
			smartFilter.position = $(this).next().offset();
		}
		var id = data.item.data('checkbox');
		$('#'+id).click();
	});

	$('body').keydown(function(e){
        e = e || window.event;
        var keyCode = e.keyCode || e.which;
        if (e.ctrlKey){
        	switch (keyCode){
				case 37:
					$('.pagination-item.arrow.prev:not(.disabled)').click();
					break;
				case 39:
                    $('.pagination-item.arrow.next:not(.disabled)').click();
					break;
			}
		}
	});

	RZB2.ajax.CatalogSection.RefreshButtons();

	if($('.rbs-find-img-detail-descr').length)
		$('.rbs-find-img-detail-descr').lazyload({}).removeClass('placeholder');

	$(window).on('scroll', function(){
		$('img').lazyload({}).removeClass('placeholder');
	});
}

if (typeof domReady != "undefined" && domReady == true) {
	RZB2_initCatalogHandlers(jQuery);
} else {
	jQuery(document).ready( RZB2_initCatalogHandlers );
}

//# sourceURL=js/back-end/handlers/catalog_section.js
