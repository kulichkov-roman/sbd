/* http://keith-wood.name/countdown.html
   Japanese initialisation for the jQuery countdown extension
   Written by Ken Ishimoto (ken@ksroom.com) Aug 2009. */
(function($) {
	$.countdown.regionalOptions['ja'] = {
		labels: ['е№ґ', 'жњ€', 'йЂ±', 'ж—Ґ', 'ж™‚', 'е€†', 'з§’'],
		labels1: ['е№ґ', 'жњ€', 'йЂ±', 'ж—Ґ', 'ж™‚', 'е€†', 'з§’'],
		compactLabels: ['е№ґ', 'жњ€', 'йЂ±', 'ж—Ґ'],
		whichLabels: null,
		digits: ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'],
		timeSeparator: ':', isRTL: false};
	$.countdown.setDefaults($.countdown.regionalOptions['ja']);
})(jQuery);
