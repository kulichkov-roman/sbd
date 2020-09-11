/* http://keith-wood.name/countdown.html
 * Malayalam/(Indian>>Kerala) initialisation for the jQuery countdown extension
 * Written by Harilal.B (harilal1234@gmail.com) Feb 2013. */
(function($) {
    $.countdown.regionalOptions['ml'] = {
        labels: ['аґµаґ°аµЌвЂЌаґ·аґ™аµЌаґ™аґіаµЌвЂЌ', 'аґ®аґѕаґёаґ™аµЌаґ™аґіаµЌвЂЌ', 'аґ†аґґаµЌаґљаґ•аґіаµЌвЂЌ', 'аґ¦аґїаґµаґёаґ™аµЌаґ™аґіаµЌвЂЌ', 'аґ®аґЈаґїаґ•аµЌаґ•аµ‚аґ±аµЃаґ•аґіаµЌвЂЌ', 'аґ®аґїаґЁаґїаґ±аµЌаґ±аµЃаґ•аґіаµЌвЂЌ', 'аґёаµ†аґ•аµЌаґ•аґЁаµЌаґ±аµЃаґ•аґіаµЌвЂЌ'],
        labels1: ['аґµаґ°аµЌвЂЌаґ·аґ‚', 'аґ®аґѕаґёаґ‚', 'аґ†аґґаµЌаґљ', 'аґ¦аґїаґµаґёаґ‚', 'аґ®аґЈаґїаґ•аµЌаґ•аµ‚аґ°аµЌвЂЌ', 'аґ®аґїаґЁаґїаґ±аµЌаґ±аµЌ', 'аґёаµ†аґ•аµЌаґ•аґЁаµЌаґ±аµЌ'],
        compactLabels: ['аґµ', 'аґ®', 'аґ†', 'аґ¦аґї'],
        whichLabels: null,
		digits: ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'],
//		digits: ['аµ¦', 'аµ§', 'аµЁ', 'аµ©', 'аµЄ', 'аµ«', 'аµ¬', 'аµ­', 'аµ®', 'аµЇ'],
        timeSeparator: ':', isRTL: false};
    $.countdown.setDefaults($.countdown.regionalOptions['ml']);
})(jQuery);