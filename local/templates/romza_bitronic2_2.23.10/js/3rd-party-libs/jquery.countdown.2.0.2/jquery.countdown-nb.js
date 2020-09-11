/* http://keith-wood.name/countdown.html
   Norwegian BokmГҐl translation
   Written by Kristian Ravnevand */
(function($) {
	$.countdown.regionalOptions['nb'] = {
		labels: ['Г…r', 'MГҐneder', 'Uker', 'Dager', 'Timer', 'Minutter', 'Sekunder'],
		labels1: ['Г…r', 'MГҐned', 'Uke', 'Dag', 'Time', 'Minutt', 'Sekund'],
		compactLabels: ['Г…', 'M', 'U', 'D'],
		whichLabels: null,
		digits: ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'],
		timeSeparator: ':', isRTL: false};
	$.countdown.setDefaults($.countdown.regionalOptions['nb']);
})(jQuery);
