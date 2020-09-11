/* http://keith-wood.name/countdown.html
 * Hungarian initialisation for the jQuery countdown extension
 * Written by Edmond L. (webmond@gmail.com). */
(function($) {
	$.countdown.regionalOptions['hu'] = {
		labels: ['Г‰v', 'HГіnap', 'HГ©t', 'Nap', 'Г“ra', 'Perc', 'MГЎsodperc'],
		labels1: ['Г‰v', 'HГіnap', 'HГ©t', 'Nap', 'Г“ra', 'Perc', 'MГЎsodperc'],
		compactLabels: ['Г‰', 'H', 'HГ©', 'N'],
		whichLabels: null,
		digits: ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'],
		timeSeparator: ':', isRTL: false};
	$.countdown.setDefaults($.countdown.regionalOptions['hu']);
})(jQuery);
