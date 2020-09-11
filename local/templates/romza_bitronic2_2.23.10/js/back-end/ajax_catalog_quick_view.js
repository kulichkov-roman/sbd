// QUICK view
RZB2.ajax.quickView =
{
	URL: undefined,
	HTML: undefined,
	jqXHR: undefined,
	iblockID: undefined,
	contentClass: '.modal_quick-view_content',
	catalogParamsRequested: false,
	$content: undefined,

	load: function (callback)
	{
		var data = {};
		var qv = RZB2.ajax.quickView;
		
		for(var key in RZB2.ajax.params) 
		{
			data[key] = RZB2.ajax.params[key];
		}
		data['rz_ajax'] = 'y';
		data['rz_quick_view'] = 'y';
		data['REQUEST_URI'] = this.URL;

		if (typeof this.iblockID != "undefined") data['IBLOCK_ID'] = this.iblockID;

		if (typeof qv.spinner == 'undefined') {
			qv.spinner = RZB2.ajax.spinner(qv.$content);
			qv.spinner.Start({color: RZB2.themeColor});
		}

		return $.ajax(
		{
			url: SITE_DIR + 'ajax/sib/catalog.php',
			type: "POST",
			data: data,
			dataType: "html",
			success: function(data) {
				if (typeof callback == "function") callback(data);
			},
			error: function(jqXHR, textStatus, errorThrown) {
				RZB2.ajax.showMessage('AJAX error: ' + textStatus, 'error');
			}
		});
	},

	//external handlers
	onModalShown: function(e){
		var qv = RZB2.ajax.quickView;
		if (typeof qv.$content == "undefined") {
			qv.$content = $(this).find(qv.contentClass);
		}
		var $target = $(e.relatedTarget);
		qv.URL = $target.closest('.catalog-item').find('a.link').attr('href');

		if ($target.closest('.special-blocks').length > 0) {
			//get iblock id on main page
			var obId = $target.closest('.catalog-item-wrap').attr('id');
			obId = obId.replace('-', 'x');
			qv.iblockID = window['ob'+obId].product.IBLOCK_ID;
		}

		qv.jqXHR = qv.load(qv.onLoadModalCallback);
	},

	onLoadModalCallback: function (data)
	{
		var qv = RZB2.ajax.quickView;

		if (typeof qv.spinner == 'object') {
			qv.spinner.Stop();
			qv.spinner = undefined;
		}

		if (!data) return;
		if (data == "[ajax died] loading params" && !qv.catalogParamsRequested) {
			qv.catalogParamsRequested = true;
			qv.jqXHR = RZB2.ajax.updateCatalogParametersCache(function(){
				qv.jqXHR = qv.load(qv.onLoadModalCallback);
			});
			return;
		}
		
		qv.HTML = qv.$content.html();
		qv.$content.html(data);
		RZB2.ajax.BasketSmall.RefreshButtons();
		RZB2.ajax.Compare.RefreshButtons();
		RZB2.ajax.Favorite.RefreshButtons();
		initThumbs(qv.$content[0]);
		initTooltips(qv.$content[0]);
		initSelects(qv.$content[0]);
		initRatingStars(qv.$content[0]);

		if (serverSettings.quickViewChars == "Y") {
			initGenInfoToggle();
			initToggles(qv.$content[0]);
		}
	},

	onModalHidden: function(e){
		var qv = RZB2.ajax.quickView;
		qv.URL = undefined;
		qv.iblockID = undefined;

		if (qv.jqXHR && qv.jqXHR.readyState != 4) {
			qv.jqXHR.abort();
			return;
		}
		if (typeof qv.HTML != "undefined") {
			$(this).find(qv.contentClass).html(qv.HTML);
		}
	}
};
