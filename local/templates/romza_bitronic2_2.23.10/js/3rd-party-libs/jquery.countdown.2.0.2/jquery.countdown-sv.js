/* http://keith-wood.name/countdown.html
   Swedish initialisation for the jQuery countdown extension
   Written by Carl (carl@nordenfelt.com). */
(function($) {
	$.countdown.regionalOptions['sv'] = {
		labels: ['Г…r', 'MГҐnader', 'Veckor', 'Dagar', 'Timmar', 'Minuter', 'Sekunder'],
		labels1: ['Г…r', 'MГҐnad', 'Vecka', 'Dag', 'Timme', 'Minut', 'Sekund'],
		compactLabels: ['Г…', 'M', 'V', 'D'],
		whichLabels: null,
		digits: ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'],
		timeSeparator: ':', isRTL: false};
	$.countdown.setDefaults($.countdown.regionalOptions['sv']);
})(jQuery);
