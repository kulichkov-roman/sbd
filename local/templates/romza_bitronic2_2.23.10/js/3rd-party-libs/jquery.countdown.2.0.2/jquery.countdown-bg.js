/* http://keith-wood.name/countdown.html
 * Bulgarian initialisation for the jQuery countdown extension
 * Written by Manol Trendafilov manol@rastermania.com (2010) */
(function($) {
	$.countdown.regionalOptions['bg'] = {
		labels: ['Р“РѕРґРёРЅРё', 'РњРµСЃРµС†Р°', 'РЎРµРґРјРёС†Р°', 'Р”РЅРё', 'Р§Р°СЃР°', 'РњРёРЅСѓС‚Рё', 'РЎРµРєСѓРЅРґРё'],
		labels1: ['Р“РѕРґРёРЅР°', 'РњРµСЃРµС†', 'РЎРµРґРјРёС†Р°', 'Р”РµРЅ', 'Р§Р°СЃ', 'РњРёРЅСѓС‚Р°', 'РЎРµРєСѓРЅРґР°'],
		compactLabels: ['l', 'm', 'n', 'd'], compactLabels1: ['g', 'm', 'n', 'd'],
		whichLabels: null,
		digits: ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'],
		timeSeparator: ':', isRTL: false};
	$.countdown.setDefaults($.countdown.regionalOptions['bg']);
})(jQuery);
