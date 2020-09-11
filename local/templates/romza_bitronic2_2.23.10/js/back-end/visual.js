if (typeof window.RZB2 == "undefined") {
	RZB2 = {visual: {}};
}

if (typeof RZB2.visual == "undefined") {
	RZB2.visual = {};
}

RZB2.visual.Hits = 
{
	InitShow: function()
	{
		var bHide = RZB2.utils.getCookie('show_hits') === 'false';
		if (bHide) {
			$('#mainmenu').addClass('hits-hidden');
		} else {
			$('#mainmenu').removeClass('hits-hidden');
		}
	},

	ToggleShow: function(e, res)
	{
		var cookieName = 'show_hits';
		var bShow = (typeof res !== 'object') ? (res === 'show') : (res.type === 'show');
		if ($(e.target).hasClass('catalog-hits')) {
			cookieName = 'show_hits_catalog';
		}
		if(typeof bShow != 'undefined')
		{
			RZB2.utils.setCookie(cookieName, bShow);
		}
	},
}