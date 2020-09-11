/* http://keith-wood.name/countdown.html
   Greek initialisation for the jQuery countdown extension
   Written by Philip. */
(function($) {
	$.countdown.regionalOptions['el'] = {
		labels: ['О§ПЃПЊОЅО№О±', 'ОњО®ОЅОµП‚', 'О•ОІОґОїОјО¬ОґОµП‚', 'ОњО­ПЃОµП‚', 'ОЏПЃОµП‚', 'О›ОµПЂП„О¬', 'О”ОµП…П„ОµПЃПЊО»ОµПЂП„О±'],
		labels1: ['О§ПЃПЊОЅОїП‚', 'ОњО®ОЅО±П‚', 'О•ОІОґОїОјО¬ОґО±', 'О—ОјО­ПЃО±', 'ОЏПЃО±', 'О›ОµПЂП„ПЊ', 'О”ОµП…П„ОµПЃПЊО»ОµПЂП„Ої'],
		compactLabels: ['О§ПЃ.', 'ОњО·ОЅ.', 'О•ОІОґ.', 'О—Ој.'],
		whichLabels: null,
		digits: ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'],
		timeSeparator: ':', isRTL: false};
	$.countdown.setDefaults($.countdown.regionalOptions['el']);
})(jQuery);