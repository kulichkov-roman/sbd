/* http://keith-wood.name/countdown.html
 * Lithuanian localisation for the jQuery countdown extension
 * Written by Moacir P. de SГЎ Pereira (moacir{at}gmail.com) (2009) */
(function($) {
	$.countdown.regionalOptions['lt'] = {
		labels: ['MetЕі', 'MД—nesiЕі', 'SavaiДЌiЕі', 'DienЕі', 'ValandЕі', 'MinuДЌiЕі', 'SekundЕѕiЕі'],
		labels1: ['Metai', 'MД—nuo', 'SavaitД—', 'Diena', 'Valanda', 'MinutД—', 'SekundД—'],
		compactLabels: ['m', 'm', 's', 'd'],
		whichLabels: null,
		digits: ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'],
		timeSeparator: ':', isRTL: false};
	$.countdown.setDefaults($.countdown.regionalOptions['lt']);
})(jQuery);
