/* http://keith-wood.name/countdown.html
 * Serbian Cyrillic initialisation for the jQuery countdown extension
 * Written by Predrag Leka lp@lemurcake.com (2010) */
(function($) {
	$.countdown.regionalOptions['sr'] = {
		labels: ['Р“РѕРґРёРЅР°', 'РњРµСЃРµС†Рё', 'РќРµРґРµС™Р°', 'Р”Р°РЅР°', 'Р§Р°СЃРѕРІР°', 'РњРёРЅСѓС‚Р°', 'РЎРµРєСѓРЅРґРё'],
		labels1: ['Р“РѕРґРёРЅР°', 'РјРµСЃРµС†', 'РќРµРґРµС™Р°', 'Р”Р°РЅ', 'Р§Р°СЃ', 'РњРёРЅСѓС‚', 'РЎРµРєСѓРЅРґР°'],
		labels2: ['Р“РѕРґРёРЅРµ', 'РњРµСЃРµС†Р°', 'РќРµРґРµС™Рµ', 'Р”Р°РЅР°', 'Р§Р°СЃР°', 'РњРёРЅСѓС‚Р°', 'РЎРµРєСѓРЅРґРµ'],
		compactLabels: ['Рі', 'Рј', 'РЅ', 'Рґ'],
		whichLabels: function(amount) {
			return (amount == 1 ? 1 : (amount >= 2 && amount <= 4 ? 2 : 0));
		},
		digits: ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'],
		timeSeparator: ':', isRTL: false};
	$.countdown.setDefaults($.countdown.regionalOptions['sr']);
})(jQuery);
