/* http://keith-wood.name/countdown.html
   Faroese initialisation for the jQuery countdown extension
   Written by Kasper Friis Christensen (kasper@friischristensen.com). */
(function($) {
	$.countdown.regionalOptions['fo'] = {
		labels: ['ГЃr', 'MГЎnaГ°ir', 'Vikur', 'Dagar', 'TГ­mar', 'Minuttir', 'Sekund'],
		labels1: ['ГЃr', 'MГЎnaГ°ur', 'Vika', 'Dagur', 'TГ­mi', 'Minuttur', 'Sekund'],
		compactLabels: ['ГЃ', 'M', 'V', 'D'],
		whichLabels: null,
		digits: ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'],
		timeSeparator: ':', isRTL: false};
	$.countdown.setDefaults($.countdown.regionalOptions['fo']);
})(jQuery);
