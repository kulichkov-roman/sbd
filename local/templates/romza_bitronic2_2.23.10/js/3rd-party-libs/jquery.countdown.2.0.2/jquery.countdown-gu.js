/* http://keith-wood.name/countdown.html
 * Gujarati initialization for the jQuery countdown extension
 * Written by Sahil Jariwala jariwala.sahil@gmail.com (2012) */
(function($) {
	$.countdown.regionalOptions['gu'] = {
		labels: ['аЄµаЄ°а«ЌаЄ·', 'аЄ®аЄ№аЄїаЄЁа«‹', 'аЄ…аЄ аЄµаЄѕаЄЎаЄїаЄЇаЄѕ', 'аЄ¦аЄїаЄµаЄё', 'аЄ•аЄІаЄѕаЄ•', 'аЄ®аЄїаЄЁаЄїаЄџ','аЄёа«‡аЄ•аЄЁа«ЌаЄЎ'],
		labels1: ['аЄµаЄ°а«ЌаЄ·','аЄ®аЄ№аЄїаЄЁа«‹','аЄ…аЄ аЄµаЄѕаЄЎаЄїаЄЇаЄѕ','аЄ¦аЄїаЄµаЄё','аЄ•аЄІаЄѕаЄ•','аЄ®аЄїаЄЁаЄїаЄџ', 'аЄёа«‡аЄ•аЄЁа«ЌаЄЎ'],
		compactLabels: ['аЄµ', 'аЄ®', 'аЄ…', 'аЄ¦аЄї'],
		whichLabels: null,
		digits: ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'],
		timeSeparator: ':', isRTL: false};
	$.countdown.setDefaults($.countdown.regionalOptions['gu']);
})(jQuery);
