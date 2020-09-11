/* http://keith-wood.name/countdown.html
   Icelandic initialisation for the jQuery countdown extension
   Written by RГіbert K. L. */
(function($) {
	$.countdown.regionalOptions['is'] = {
		labels: ['ГЃr', 'MГЎnuГ°ir', 'Vikur', 'Dagar', 'Klukkustundir', 'MГ­nГєtur', 'SekГєndur'],
		labels1: ['ГЃr', 'MГЎnuГ°ur', 'Vika', 'Dagur', 'Klukkustund', 'MГ­nГєta', 'SekГєnda'],
		compactLabels: ['ГЎr.', 'mГЎn.', 'vik.', 'dag.', 'klst.', 'mГ­n.', 'sek.'],
		whichLabels: null,
		digits: ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'],
		timeSeparator: ':', isRTL: false};
	$.countdown.setDefaults($.countdown.regionalOptions['is']);
})(jQuery);