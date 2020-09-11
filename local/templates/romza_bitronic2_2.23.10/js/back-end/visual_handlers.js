$(document).ready(function(){

	// SMALL BASKET
	$('#popup_basket').on('shown.bs.modal', function(e){
		initTooltips(this);
	});

	// CATALOG BRANDS
	$('.brands-catalog').on('togglebrand', function(e, data){
		var id = data.item.data('checkbox');
		$('#'+id).click();
	});

	// REGISTRATION
	$('.form_registration').submit(function(){
		var _ = $(this);
		_.find('input[name="USER_LOGIN"]').val(_.find('input[name="USER_EMAIL"]').val());
		_.find('input[name="NEW_LOGIN"]').val(_.find('input[name="NEW_EMAIL"]').val());
	});

	if (typeof window.frameCacheVars !== "undefined" && window.isFrameDataReceived == false) {
		BX.addCustomEvent("onFrameDataReceived", function (json){
			initFormValidation('.form_footer-subscribe, .subscribe-edit', true);
		});
	} else {
		initFormValidation('.form_footer-subscribe, .subscribe-edit', true);
	}
});