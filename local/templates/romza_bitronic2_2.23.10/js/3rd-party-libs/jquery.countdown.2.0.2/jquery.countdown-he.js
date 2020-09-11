/* http://keith-wood.name/countdown.html
 * Hebrew initialisation for the jQuery countdown extension
 * Translated by Nir Livne, Dec 2008 */
(function($) {
	$.countdown.regionalOptions['he'] = {
		labels: ['Ч©Ч Ч™Чќ', 'Ч—Ч•Ч“Ч©Ч™Чќ', 'Ч©Ч‘Ч•ЧўЧ•ЧЄ', 'Ч™ЧћЧ™Чќ', 'Ч©ЧўЧ•ЧЄ', 'Ч“Ч§Ч•ЧЄ', 'Ч©Ч Ч™Ч•ЧЄ'],
		labels1: ['Ч©Ч Ч”', 'Ч—Ч•Ч“Ч©', 'Ч©Ч‘Ч•Чў', 'Ч™Ч•Чќ', 'Ч©ЧўЧ”', 'Ч“Ч§Ч”', 'Ч©Ч Ч™Ч™Ч”'],
		compactLabels: ['Ч©Ч ', 'Ч—', 'Ч©Ч‘', 'Ч™'],
		whichLabels: null,
		digits: ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'],
		timeSeparator: ':', isRTL: true};
	$.countdown.setDefaults($.countdown.regionalOptions['he']);
})(jQuery);
