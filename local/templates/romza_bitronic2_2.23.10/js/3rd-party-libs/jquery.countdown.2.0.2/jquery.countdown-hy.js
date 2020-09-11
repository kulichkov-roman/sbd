/* http://keith-wood.name/countdown.html
 * Armenian initialisation for the jQuery countdown extension
 * Written by Artur Martirosyan. (artur{at}zoom.am) October 2011. */
(function($) {
	$.countdown.regionalOptions['hy'] = {
		labels: ['ХЏХЎЦЂХ«', 'Ф±ХґХ«ХЅ', 'Х‡ХЎХўХЎХ©', 'Х•ЦЂ', 'ФєХЎХґ', 'ХђХёХєХҐ', 'ХЋХЎЦЂХЇХµХЎХ¶'],
		labels1: ['ХЏХЎЦЂХ«', 'Ф±ХґХ«ХЅ', 'Х‡ХЎХўХЎХ©', 'Х•ЦЂ', 'ФєХЎХґ', 'ХђХёХєХҐ', 'ХЋХЎЦЂХЇХµХЎХ¶'],
		compactLabels: ['Хї', 'ХЎ', 'Х·', 'Ц…'], 
		whichLabels: null,
		digits: ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'],
		timeSeparator: ':', isRTL: false};
	$.countdown.setDefaults($.countdown.regionalOptions['hy']);
})(jQuery);
