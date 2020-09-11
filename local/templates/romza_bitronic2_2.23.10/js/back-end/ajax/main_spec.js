var block = $('.main-block_index').find('.js-slider-2');

block.on('init', function(){
    $(this).on('afterChange', function(){
        block.find("img.lazy").lazyload();
    });
});
//Main Spec
/*
RZB2.ajax.MainSpecTab = function(params, tabID, filter){
    this.params = params;
    this.container = '#tab_' + tabID;
    this.filter = filter;
};
RZB2.ajax.MainSpecTab.prototype.GetTab = function(){
	var _ = this;

    $.ajax({
        url: SITE_DIR + 'ajax/sib/main_spec_sib.php',
        type: "POST",
        data: {params: _.params, filter: _.filter},
        success: function(res){
            $(_.container).html(res);
            var block = $(_.container).find('.js-slider-2');
            block.on('init', function(event, slick){
                $(this).on('afterChange', function(){
                    block.find("img.lazy").lazyload();
                });
            });
        }
    });
};
*/