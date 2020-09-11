/* http://keith-wood.name/countdown.html
   Persian (ЩЃШ§Ш±ШіЫЊ) initialisation for the jQuery countdown extension
   Written by Alireza Ziaie (ziai@magfa.com) Oct 2008.
   Digits corrected by Hamed Ramezanian Feb 2013. */
(function($) {
	$.countdown.regionalOptions['fa'] = {
		labels: ['вЂЊШіШ§Щ„', 'Щ…Ш§Щ‡', 'Щ‡ЩЃШЄЩ‡', 'Ш±Щ€ШІ', 'ШіШ§Ш№ШЄ', 'ШЇЩ‚ЫЊЩ‚Щ‡', 'Ш«Ш§Щ†ЫЊЩ‡'],
		labels1: ['ШіШ§Щ„', 'Щ…Ш§Щ‡', 'Щ‡ЩЃШЄЩ‡', 'Ш±Щ€ШІ', 'ШіШ§Ш№ШЄ', 'ШЇЩ‚ЫЊЩ‚Щ‡', 'Ш«Ш§Щ†ЫЊЩ‡'],
		compactLabels: ['Ші', 'Щ…', 'Щ‡', 'Ш±'],
		whichLabels: null,
		digits: ['Ы°', 'Ы±', 'ЫІ', 'Ыі', 'Ыґ', 'Ыµ', 'Ы¶', 'Ы·', 'Ыё', 'Ы№'],
		timeSeparator: ':', isRTL: true};
	$.countdown.setDefaults($.countdown.regionalOptions['fa']);
})(jQuery);
