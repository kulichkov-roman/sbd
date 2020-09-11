/* http://keith-wood.name/countdown.html
 * Ukrainian initialisation for the jQuery countdown extension
 * Written by Goloborodko M misha.gm@gmail.com (2009), corrections by IРіРѕСЂ KРѕРЅРѕРІР°Р» */
(function($) {
	$.countdown.regionalOptions['uk'] = {
		labels: ['Р РѕРєС–РІ', 'РњС–СЃСЏС†С–РІ', 'РўРёР¶РЅС–РІ', 'Р”РЅС–РІ', 'Р“РѕРґРёРЅ', 'РҐРІРёР»РёРЅ', 'РЎРµРєСѓРЅРґ'],
		labels1: ['Р С–Рє', 'РњС–СЃСЏС†СЊ', 'РўРёР¶РґРµРЅСЊ', 'Р”РµРЅСЊ', 'Р“РѕРґРёРЅР°', 'РҐРІРёР»РёРЅР°', 'РЎРµРєСѓРЅРґР°'],
		labels2: ['Р РѕРєРё', 'РњС–СЃСЏС†С–', 'РўРёР¶РЅС–', 'Р”РЅС–', 'Р“РѕРґРёРЅРё', 'РҐРІРёР»РёРЅРё', 'РЎРµРєСѓРЅРґРё'],
		compactLabels: ['r', 'm', 't', 'd'],
		whichLabels: function(amount) {
			return (amount == 1 ? 1 : (amount >=2 && amount <= 4 ? 2 : 0));
		},
		digits: ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'],
		timeSeparator: ':', isRTL: false};
	$.countdown.setDefaults($.countdown.regionalOptions['uk']);
})(jQuery);
