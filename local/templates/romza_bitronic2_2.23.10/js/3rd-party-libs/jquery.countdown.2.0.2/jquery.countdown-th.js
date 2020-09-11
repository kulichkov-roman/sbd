/* http://keith-wood.name/countdown.html
   Thai initialisation for the jQuery countdown extension
   Written by Pornchai Sakulsrimontri (li_sin_th@yahoo.com). */
(function($) {
	$.countdown.regionalOptions['th'] = {
		labels: ['аё›аёµ', 'а№Ђаё”аё·аё­аё™', 'аёЄаё±аё›аё”аёІаё«а№Њ', 'аё§аё±аё™', 'аёЉаё±а№€аё§а№‚аёЎаё‡', 'аё™аёІаё—аёµ', 'аё§аёґаё™аёІаё—аёµ'],
		labels1: ['аё›аёµ', 'а№Ђаё”аё·аё­аё™', 'аёЄаё±аё›аё”аёІаё«а№Њ', 'аё§аё±аё™', 'аёЉаё±а№€аё§а№‚аёЎаё‡', 'аё™аёІаё—аёµ', 'аё§аёґаё™аёІаё—аёµ'],
		compactLabels: ['аё›аёµ', 'а№Ђаё”аё·аё­аё™', 'аёЄаё±аё›аё”аёІаё«а№Њ', 'аё§аё±аё™'],
		whichLabels: null,
		digits: ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'],
		timeSeparator: ':', isRTL: false};
	$.countdown.setDefaults($.countdown.regionalOptions['th']);
})(jQuery);
