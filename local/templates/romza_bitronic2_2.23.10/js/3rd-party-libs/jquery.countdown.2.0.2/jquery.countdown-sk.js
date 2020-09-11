/* http://keith-wood.name/countdown.html
 * Slovak initialisation for the jQuery countdown extension
 * Written by Roman Chlebec (creamd@c64.sk) (2008) */
(function($) {
	$.countdown.regionalOptions['sk'] = {
		labels: ['Rokov', 'Mesiacov', 'TГЅЕѕdЕ€ov', 'DnГ­', 'HodГ­n', 'MinГєt', 'SekГєnd'],
		labels1: ['Rok', 'Mesiac', 'TГЅЕѕdeЕ€', 'DeЕ€', 'Hodina', 'MinГєta', 'Sekunda'],
		labels2: ['Roky', 'Mesiace', 'TГЅЕѕdne', 'Dni', 'Hodiny', 'MinГєty', 'Sekundy'],
		compactLabels: ['r', 'm', 't', 'd'],
		whichLabels: function(amount) {
			return (amount == 1 ? 1 : (amount >= 2 && amount <= 4 ? 2 : 0));
		},
		digits: ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'],
		timeSeparator: ':', isRTL: false};
	$.countdown.setDefaults($.countdown.regionalOptions['sk']);
})(jQuery);
