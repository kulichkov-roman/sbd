if (typeof window.RZB2 == "undefined") {
	RZB2 = {utils: {}};
}

if (typeof RZB2.utils == "undefined") {
	RZB2.utils = {};
}

RZB2.utils.cookiePrefix = 'RZ_';
RZB2.utils.setCookie = function(name, value, prefix)
{
	var date = new Date();
	date.setFullYear(date.getFullYear() + 1);

	prefix = prefix || this.cookiePrefix;
	document.cookie = prefix + name + '=' + value + '; path=/; domain=sibdroid.ru; expires=' + date.toUTCString();
}

RZB2.utils.getCookie = function(name, prefix)
{
	prefix = prefix || this.cookiePrefix;
	name = prefix + name;
	var matches = document.cookie.match(new RegExp(
		"(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
	))
	return matches ? decodeURIComponent(matches[1]) : undefined
}

RZB2.utils.deleteCookie = function(name)
{
	name = this.cookiePrefix + name;
	this.setCookie(name, null, { expires: -1 })
}

RZB2.utils.getQueryVariable = function (variable, query, remove) {
	if (!query) {
		query = window.location.search.substring(1);
	} else {
		query = query.split('?')[1];
		if (!query) {
			return [];
		}
	}
	var result = {};
	if (query.length > 0) {
		var vars = query.split("&");

		for (var i = 0; i < vars.length; i++) {
			var pair = vars[i].split("=");
			if (variable && pair[0] == variable) {
				return pair[1];
			}
			if (typeof(remove) != 'undefined'
				&& pair[0] in remove) {
				continue;
			}
			result[pair[0]] = pair[1];
		}
	}
	return (result);
};

RZB2.utils.initLazy = function($container){
    $container.find("img.lazy").lazyload({
        effect: "fadeIn",
        threshold: 1000,
        failurelimit: 10000
    }).removeClass('lazy');
};
RZB2.utils.checkPrivityPolicy = function(container){
    var $inputPrivicy = $(container).closest('form').find('[name="privacy_policy"]');
    if ($inputPrivicy.length && !$inputPrivicy.filter(':checked').length) {
    	return false;
    } else {
    	return true;
	}
};
RZB2.utils.addFuncToReady = function(funcName, element,params){
    if (Array.isArray(funcName)){
        for (var i=0; i < funcName.length; i++){
            RZB2.utils.readyDocument(funcName[i],element,params);
        }
    } else{
        RZB2.utils.readyDocument(funcName,element,params);
    }
};
RZB2.utils.readyDocument = function(func,el,params){
    if (typeof window.frameCacheVars !== "undefined")
    {
        BX.addCustomEvent("onFrameDataReceived", function (json){
            jQuery( document ).ready( function() {
                RZB2.utils.callFunc(func,el,params);
            });
        });
    } else {
        jQuery( document ).ready( function() {
            RZB2.utils.callFunc(func,el,params);
        });
    }
};

RZB2.utils.callFunc = function(func,el,params){
    if (typeof func == 'function'){
        params ? func(params) : func();
    }else if(typeof func == 'string') {
        if ($(el).length) {
            params ? $(el)[func](params) : $(el)[func](params) ;
        }else{
            params ? window[func](params) : window[func]();
        }
    }
};

maskPhoneInit = function(phoneField){
	phoneField = phoneField || $('[name="FIELDS[PHONE]"]');

	phoneField.mask("+7(999)999-9999");
	phoneField.on('keydown', function(e){
		if(e.originalEvent.key == 8 && $(this).val() == '+7(___)___-____'){                
			return false;
		}
	});
	phoneField.on('input', function(e){
		var isPhone8 = $(this).val().length == 11 && $(this).val().charAt(0) == '8';
		var isPhone7 = $(this).val().length == 12 && $(this).val().substring(0, 2) == '+7';
		if(isPhone8){
			$(this).val('+7' + $(this).val().substring(1, 11));
		}
	});
};

// just simple check for email!
// have @ - not first!
// have . - after @, but not right after
// have some symbols after .
function isEmail(str){
	// checking for presence of @
	/*var atPos = str.indexOf('@');
	if ( atPos < 1 ){
		return false;
	}
	
	// @ is here and is not a first symbol! Good.
	// now check if string after it has . somewhere inside
	// - not first, not last
	var afterAt = str.substring(atPos+1);
	var dotPos = afterAt.indexOf('.');
	if ( dotPos < 1 || dotPos >= afterAt.length-1){
		return false;
	}

	return true;*/
	var re = /^[\w-\.]+@[\w-]+\.[a-z]{2,10}$/i;
	var valid = re.test(str);
	return valid;
}

alertToAvailableHandler = function(e){
	e.preventDefault();
	var form = $(this);
	
	if(!form.closest('.wait').find('[name="agree_policy_detail_phone"]').is(':checked')){
		form.closest('.wait').find('.agree-tooltip').show();
		form.find('[name="privacy_policy"]').val('N');
		return;
	} else {
		form.closest('.wait').find('.agree-tooltip').hide();
		form.find('[name="privacy_policy"]').val('Y');
	}

	if(form.find('[name="FIELDS[PHONE]"]').val() != ''){
		num = form.find('[name="FIELDS[PHONE]"]').val().match(/[\d\.]+/g),
		number = num != null ? num.join('') : false;

		form.find('[name="FIELDS[FIO]"]').val('Подписка ' + number);
		form.find('[name="FIELDS[EMAIL]"]').val(number + '@yandex.ru');
	}

	data = form.serialize();	
	$.ajax({
		url: '/ajax/sib/alert_to_available.php',
		type: "POST",
		data: data,
		async: false,
		dataType: 'html',
		success: function(data){
			form.find('[name="FIELDS[PHONE]"]').val('');
			if(form.closest('.popup').length){
				form.closest('.popup').find('[data-fancybox-close]').click();
			}
			$('body').append($(data));
		}
	});
};
window.isIOS = false;
if (/Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent)) {window.isIOS = true;}