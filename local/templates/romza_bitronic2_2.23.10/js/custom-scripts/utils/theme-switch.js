// for switching color themes
var theme;// = localStorage.getItem('bitronic2.0-theme');
// getting color theme from localStorage if available
if (!theme) theme = 'mint-flat'; // setting default if no theme was found in storage
//$('link.current-theme').attr('href','css/themes/theme_'+theme+'.css');

$(document).ready(function(){
	
	//$('.theme-demo.'+theme).addClass('active').siblings().removeClass('active');

	// color-theme switch
	$('.theme-demo').on('click', function(){
		var newTheme = $(this).attr('data-theme');
		if (theme != newTheme){
			theme = newTheme;
			var curThemeLink = $('link.current-theme');
			curThemeLink.attr('href', curThemeLink.data('path') + 'theme_' + theme + '.css');
			$('.theme-demo.active').removeClass('active');
			$(this).addClass('active');
		} else return;
	});
})
