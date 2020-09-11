/* http://keith-wood.name/countdown.html
   Urdu (Ш§Ш±ШЇЩ€) initialisation for the jQuery countdown extension
   Translated by Azhar Rasheed (azhar.rasheed19@gmail.com), November 2013. */
(function($) {
	$.countdown.regionalOptions['ur'] = {
		labels: ['ШіШ§Щ„','Щ…ЫЃЩЉЩ†Ы’','ЫЃЩЃШЄЫ’','ШЇЩ†','ЪЇЪѕЩ†Щ№Ы’','Щ…Щ†Щ№Ші','ШіЩЉЪ©Щ†Ъ‘ШІ'],
		labels1: ['ШіШ§Щ„','Щ…Ш§ЫЃ','ЫЃЩЃШЄЫЃ','ШЇЩ†','ЪЇЪѕЩ†Щ№ЫЃ','Щ…Щ†Щ№','ШіЫЊЪ©Щ†Ъ€ШІ'],
		compactLabels: ['(Щ‚)', 'ШіЫЊЩ†Щ№', 'Ш§ЫЊЪ©', 'J'],
		whichLabels: null,
		digits: ['Щ ', 'ЩЎ', 'Щў', 'ЩЈ', 'Ыґ', 'Ыµ', 'Ы¶', 'Ы·', 'ЩЁ', 'Щ©'],
		timeSeparator: ':', isRTL: true};
	$.countdown.setDefaults($.countdown.regionalOptions['ur']);
})(jQuery);
