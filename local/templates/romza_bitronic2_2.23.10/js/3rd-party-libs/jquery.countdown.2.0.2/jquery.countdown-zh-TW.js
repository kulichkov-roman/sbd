/* http://keith-wood.name/countdown.html
   Traditional Chinese initialisation for the jQuery countdown extension
   Written by Cloudream (cloudream@gmail.com). */
(function($) {
	$.countdown.regionalOptions['zh-TW'] = {
		labels: ['е№ґ', 'жњ€', 'е‘Ё', 'е¤©', 'ж™‚', 'е€†', 'з§’'],
		labels1: ['е№ґ', 'жњ€', 'е‘Ё', 'е¤©', 'ж™‚', 'е€†', 'з§’'],
		compactLabels: ['е№ґ', 'жњ€', 'е‘Ё', 'е¤©'], compactLabels1: ['е№ґ', 'жњ€', 'е‘Ё', 'е¤©'],
		whichLabels: null,
		digits: ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'],
		timeSeparator: ':', isRTL: false};
	$.countdown.setDefaults($.countdown.regionalOptions['zh-TW']);
})(jQuery);
