<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<div class="is-your-city popup-arrow" style="display:none">
    <button class="button-close close-city-choose-popup" onclick="$('.is-your-city').hide()"></button>
    <div class="city-text">
        <div class="location__title">Ваш город</div>
        <div class="location__city">Новосибирск?</div>
    </div>
    <div class="location__cols">
        <div class="location__col">
            <a class="location__button button active button_white js-close-loc" onclick="$('.is-your-city').hide()">Да</a>
        </div>    
        <div class="location__col">
            <button class="location__button button button_white button_border" onclick="chooseCity()">Выбрать другой</button>
        </div>           
    </div>
</div>
<script>

        var sitySelectInit = function(){

            var showCityPopup = function(cityHideBlock){
                $('.card-main-delivery .city-hide').css({'display':'block'});
                $(".js-fade-hide, .js-click-hide").fadeOut(0);
                cityHideBlock.stop().fadeIn(0);
                $(".rbs-mask").show();
            }

            $(".rbs-detail-city").on("click", function (e) {
                e.preventDefault();
                var _this = $(this);

                if(!_this.hasClass('loaded')){
                    $.ajax({
                        url: '/include_areas/sib/header/vregions_hide.php',
                        method: 'post',
                        data: {'ajax-city-hide': 'Y', 'request-uri': window.location.pathname,'is-mobile': window.isMobile},
                        success: function(data){
                            var cityHideBlock = _this.parents(".js-click").find(".js-click-hide");
                            cityHideBlock.html($(data).html());
                            showCityPopup(cityHideBlock);
                            $('.is-your-city').hide();
                            _this.addClass('loaded');

                            $('.footer-fix .js-click-close').on('click', function(){
                                $('.footer-fix').remove();
                            });
                        }
                    });
                } else {
                    showCityPopup(_this.parents(".js-click").find(".js-click-hide"));
                }
                
            });
        };

        if (typeof window.frameCacheVars !== "undefined"){BX.addCustomEvent("onFrameDataReceived", function (json){sitySelectInit();});} else {$(document).ready(function(){sitySelectInit();});}

   
    function chooseCity(){
        if(isMobile){
            if(!$('.rbs-detail-city').length){
                var cardMaindelivery = `
                    <div class="card-main-delivery footer-fix">
                        <div class="city city_delivery js-click">
                            <a class="rbs-detail-city" href="#"></a>
                            <div class="city-hide js-click-hide"></div>
                        </div>
                    </div>
                `;
                $('body').append($(cardMaindelivery));
                sitySelectInit();
            } 
            $('.rbs-detail-city').click();
        } else {
            $('.is-your-city').hide();
            $('#city-header-choose').click();
        }
    }
</script>