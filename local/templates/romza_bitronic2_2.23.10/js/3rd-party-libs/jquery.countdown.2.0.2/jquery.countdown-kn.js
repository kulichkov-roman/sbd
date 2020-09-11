/* http://keith-wood.name/countdown.html
 * Kannada initialization for the jQuery countdown extension
 * Written by Guru Chaturvedi guru@gangarasa.com (2011) */
(function($) {
	$.countdown.regionalOptions['kn'] = {
		labels: ['аІµаІ°аіЌаІ·аІ—аІіаіЃ', 'аІ¤аІїаІ‚аІ—аІіаіЃ', 'аІµаІѕаІ°аІ—аІіаіЃ', 'аІ¦аІїаІЁаІ—аІіаіЃ', 'аІ?аІ‚аІџаі†аІ—аІіаіЃ', 'аІЁаІїаІ®аІїаІ·аІ—аІіаіЃ', 'аІ•аіЌаІ·аІЈаІ—аІіаіЃ'],
		labels1: ['аІµаІ°аіЌаІ·', 'аІ¤аІїаІ‚аІ—аІіаіЃ', 'аІµаІѕаІ°', 'аІ¦аІїаІЁ', 'аІ?аІ‚аІџаі†', 'аІЁаІїаІ®аІїаІ·', 'аІ•аіЌаІ·аІЈ'],
		compactLabels: ['аІµ', 'аІ¤аІї', 'аІµаІѕ', 'аІ¦аІї'],
		whichLabels: null,
		digits: ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'],
		timeSeparator: ':', isRTL: false};
	$.countdown.setDefaults($.countdown.regionalOptions['kn']);
})(jQuery);
