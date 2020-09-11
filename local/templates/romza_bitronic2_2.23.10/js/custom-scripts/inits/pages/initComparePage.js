b2.init.comparePage = function(){
    if ($(".js-rating").length) { $('.js-rating').barrating({ showSelectedRating: false, readonly: true}); }
    if ($(".js-catalog__list").length) { $(".js-catalog__list").mCustomScrollbar({ horizontalScroll: true,  scrollButtons: { enable: false }, mouseWheel: { enable: false},  advanced: { updateOnContentResize: true },  advanced: { updateOnBrowserResize: true } }); };
}