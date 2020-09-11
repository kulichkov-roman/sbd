/* http://keith-wood.name/countdown.html
   Burmese initialisation for the jQuery countdown extension
   Written by Win Lwin Moe (winnlwinmoe@gmail.com) Dec 2009. */
(function($) {
	$.countdown.regionalOptions['my'] = {
		labels: ['бЂ”бЂЅбЂ…бЂ№', 'бЂњ', 'бЂ›бЂЂбЂ№бЂћбЂђбЂїбЂђбЂ•бЂђбЂ№', 'бЂ›бЂЂбЂ№', 'бЂ”бЂ¬бЂ›бЂ®', 'бЂ™бЂ­бЂ”бЂ…бЂ№', 'бЂ…бЂЂбЂїбЂЂбЂ”бЂ№бЂ·'],
		labels1: ['бЂ”бЂЅбЂ…бЂ№', 'бЂњ', 'бЂ›бЂЂбЂ№бЂћбЂђбЂїбЂђбЂ•бЂђбЂ№', 'бЂ›бЂЂбЂ№', 'бЂ”бЂ¬бЂ›бЂ®', 'бЂ™бЂ­бЂ”бЂ…бЂ№', 'бЂ…бЂЂбЂїбЂЂбЂ”бЂ№бЂ·'],
		compactLabels: ['бЂ”бЂЅбЂ…бЂ№', 'бЂњ', 'бЂ›бЂЂбЂ№бЂћбЂђбЂїбЂђбЂ•бЂђбЂ№', 'бЂ›бЂЂбЂ№'],
		whichLabels: null,
		digits: ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'],
		timeSeparator: ':', isRTL: false};
	$.countdown.setDefaults($.countdown.regionalOptions['my']);
})(jQuery);