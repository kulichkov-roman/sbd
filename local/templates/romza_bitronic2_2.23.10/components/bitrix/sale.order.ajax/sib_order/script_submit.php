<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
global $USER;
?>
<script type="text/javascript">

    <?if(CSaleLocation::isLocationProEnabled()):?>

        <?
        // spike: for children of cities we place this prompt
        $city = \Bitrix\Sale\Location\TypeTable::getList(array('filter' => array('=CODE' => 'CITY'), 'select' => array('ID')))->fetch();
        ?>

        BX.saleOrderAjax.init(<?=CUtil::PhpToJSObject(array(
            'source' => $this->__component->getPath().'/get.php',
            'cityTypeId' => intval($city['ID']),
            'messages' => array(
                'otherLocation' => '--- '.GetMessage('BITRONIC2_SOA_OTHER_LOCATION'),
                'moreInfoLocation' => '--- '.GetMessage('BITRONIC2_SOA_NOT_SELECTED_ALT'), // spike: for children of cities we place this prompt
                'notFoundPrompt' => '<div class="-bx-popup-special-prompt">'.GetMessage('BITRONIC2_SOA_LOCATION_NOT_FOUND').'.<br />'.GetMessage('BITRONIC2_SOA_LOCATION_NOT_FOUND_PROMPT', array(
                    '#ANCHOR#' => '<a href="javascript:void(0)" class="-bx-popup-set-mode-add-loc">',
                    '#ANCHOR_END#' => '</a>'
                )).'</div>'
            )
        ))?>);

    <?endif?>

    var BXFormPosting = false;
    
    function submitForm(val)
    {
        if (BXFormPosting === true)
            return true;
        
        BXFormPosting = true;
        
        if(val != 'Y'){
            BX('confirmorder').value = 'N';
        } else {
            if(!$('.rbs-delivery-type__active').length){
                var text = $('#ORDER_DESCRIPTION').val();
                text += "\n[Клиент пропустил выбор доставки и оплаты]";
                $('#ORDER_DESCRIPTION').val(text);
            }
            if($('#edost_street').val().trim() == ''){
                $('#edost_street').val('Уточнить у клиента');
            }
        }
        var orderForm = BX('ORDER_FORM');
        window.ajaxLoader.style.display = window.ajaxLoaderMask.style.display = "block";

        //BX.showWait();

        <?if(CSaleLocation::isLocationProEnabled()):?>
            BX.saleOrderAjax.cleanUp();
        <?endif?>

        BX.ajax.submit(orderForm, ajaxResult);

        return true;
    }

    function ajaxResult(res)
    {
        var orderForm = BX('ORDER_FORM');
        try {
            // if json came, it obviously a successfull order submit
            var json = JSON.parse(res);
            //BX.closeWait();
            window.ajaxLoader.style.display = window.ajaxLoaderMask.style.display = "none";
            if (json.error){
                BXFormPosting = false;
                return;
            } else if (json.redirect) {
                window.top.location.href = json.redirect;
            }
        }
        catch (e)
        {
            // json parse failed, so it is a simple chunk of html

            BXFormPosting = false;
            BX('order_form_content').innerHTML = res;
            if($('.errortext').length)
            {
                var text = '';
                $('.errortext').each(function(){
                    text += $(this).text() + '<br>';
                });
                RZB2.ajax.showMessage(text, 'fail');
            }
            <?if(CSaleLocation::isLocationProEnabled()):?>
                BX.saleOrderAjax.initDeferredControl();
            <?endif?>
        }
        //b2.init.selects('#ORDER_FORM');
        //BX.closeWait();
        window.ajaxLoader.style.display = window.ajaxLoaderMask.style.display = "none";
        BX.onCustomEvent(orderForm, 'onAjaxSuccess');
        customEventsInit();
    }

    function SetContact(profileId)
    {
        BX("profile_change").value = "Y";
        submitForm();
    }

    function customEventsInit(){

        var initDeliveryItems = function(){
            if ($(".js-delivery-options").length) {
                $('.js-delivery-options').on('init', function(event, slick){
                    //$('.js-delivery-options').show();
                    var maxHeight = 0;
                    slick.$slides.each(function(){
                        maxHeight = maxHeight < $(this).find('.delivery-options__item>div').height() ? $(this).find('.delivery-options__item>div').height() : maxHeight;
                    });
                    if(maxHeight > 0){
                        slick.$slides.find('.delivery-options__item>div').css({'min-height': maxHeight + 'px'});
                    }
                });
                $('.js-delivery-options').slick(slickOrderOptions);
            }
        };
        initDeliveryItems();

        if ($(".js-payment-options").length){
            $('.js-payment-options').on('init', function(event, slick){
                //$(".js-payment-options").show();
                var maxHeight = 0;
                slick.$slides.each(function(){
                    maxHeight = maxHeight < $(this).find('.payment-list__item>span').height() ? $(this).find('.payment-list__item>span').height() : maxHeight;
                });
                if(maxHeight > 0){
                    slick.$slides.find('.payment-list__item>span').css({'min-height': maxHeight + 'px'});
                }
            });
            $('.js-payment-options').slick(slickOrderOptions);            
        }

        var doNextStep = function(_this){
            $(".js-delivery-options").slick("resize");
            $(".js-payment-options").slick("resize");
            $(_this).parents('.order-step__item').removeClass('active').addClass('order-ok');
            $(_this).parents('.order-step__item').next().addClass('active');
            //$('.total_order').find('.button').prop("disabled", false).removeClass('button_disabled');
            $('.total_order').find('.comment-order').slideDown();

            $(document).scrollTop($('.order-step__item').first().offset().top);
        };

        var showError = function(field, text){
            var boxField = $(field).closest('.box-field__input');
            boxField.removeClass('input-ok');
            if(!boxField.find('.input-error__req').length){
                boxField.prepend($('<div class="input-error__req"></div>'));
            }
            text = text || 'Обязательное поле!';
            boxField.find('.input-error__req').text(text);
            boxField.addClass('input-error');
        };

        var showDone = function(field){
            var boxField = $(field).closest('.box-field__input');
            boxField.removeClass('input-error');
            if(boxField.find('.input-error__req').length){
                boxField.find('.input-error__req').remove();
            }
            boxField.addClass('input-ok');
        };

        var activeOrderButton = function(){
            $('.total_order').find('.button').prop("disabled", false).removeClass('button_disabled');
            $('.rbs-mobile-order-button').show();
        };
        var disableOrderButton = function(){
            $('.total_order').find('.button').prop("disabled", true).addClass('button_disabled');
            $('.rbs-mobile-order-button').hide();
        };

        var phoneInit = function(){
            if($('#ORDER_PROP_3').length) {
                $('#ORDER_PROP_3').mask('+7(999)999-9999');
                $('#ORDER_PROP_3').on('keydown', function(e){
                    if(e.originalEvent.key == 8 && $(this).val() == '+7(___)___-____'){                
                        return false;
                    }
                });
                $("#ORDER_PROP_3").on('input', function(e){
                    var isPhone8 = $(this).val().length == 11 && $(this).val().charAt(0) == '8';
                    var isPhone7 = $(this).val().length == 12 && $(this).val().substring(0, 2) == '+7';
                    if(isPhone8){
                        $(this).val('+7' + $(this).val().substring(1, 11));
                    }
                });
            };
        };
        phoneInit();

        /* var checkDelivery = function()
        {
            if($('.rbs-delivery-type__active').length == 1){
                if($('.rbs-delivery-type__active').data('delivery-type-btn') == 'self'){
                    if($('.delivery-address').length)
                        $('.delivery-address').hide();
                    if($('#edost_location_address_div .address-form__col input#edost_street').length)
                        $('#edost_location_address_div .address-form__col input#edost_street').val('Самовывоз');
                } else {
                    if($('#edost_location_address_div .address-form__col input#edost_street').length){
                        if($('#edost_location_address_div .address-form__col input#edost_street').val() == 'Самовывоз')
                            $('#edost_location_address_div .address-form__col input#edost_street').val('');
                    }
                        
                }
                
            } else {
                if($('#edost_location_address_div .address-form__col input#edost_street').length)
                    $('#edost_location_address_div .address-form__col input#edost_street').val('Уточнить у клиента');
                if($('.delivery-address').length)
                    $('.delivery-address').hide();
            }
        }
        checkDelivery(); */

        var sitySelectInit = function(){

            var showCityPopup = function(cityHideBlock){
                $('.city-hide').css({'display':'block'});
                $(".js-fade-hide, .js-click-hide").fadeOut(0);
                cityHideBlock.stop().fadeIn(0);
                $(".mask-inner").show();
            }

            $(".js-click-button-city").on("click", function (e) {
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
                            
                            _this.addClass('loaded');
                        }
                    });
                } else {
                    showCityPopup(_this.parents(".js-click").find(".js-click-hide"));
                }
                
            });
        };
        sitySelectInit();

        var checkPayFirst = function(){
            if(!$('.payment-list__item input').first().is(':checked') && !$('.payment-list__item input').hasClass('rbs-fisrt-checekd')){
                $('.payment-list__item input').first().prop('checked', 'checked');
                $('.payment-list__item input').addClass('rbs-fisrt-checekd');
                submitForm();
            }
        };

        $('input[type=checkbox].js-formstyler').styler({});
        $('input[type=checkbox].js-formstyler').on('change', function(){
            if($(this).is(':checked')){
                $(this).closest('.checkbox').addClass('active');
            } else {
                $(this).closest('.checkbox').removeClass('active');
            }
        });
        setTimeout(() => {
            $('input[type=checkbox].js-formstyler').trigger('change');
        }, 300);

        var patternEmail = /^([a-z0-9_\.-])+@[a-z0-9-]+\.([a-z]{2,4}\.)?[a-z]{2,4}$/i;

        $(".js-next-step-order").on("click", function(e){
            e.preventDefault();
            e.stopPropagation();
            
            if($(this).data('block') == 'props'){
                $err = false;
                if($('#ORDER_PROP_1').val().trim() == ''){
                    showError('#ORDER_PROP_1');
                    $err = true;
                } else if($('#ORDER_PROP_1').val().trim().split(' ').length < 1){
                    showError('#ORDER_PROP_1', 'Введите ФИО!');
                    $err = true;
                } else if($('#ORDER_PROP_1').val().trim() != ''){
                    showDone('#ORDER_PROP_1');
                }

                if($('#ORDER_PROP_2').val().trim() == ''){
                    showError('#ORDER_PROP_2');
                    $err = true;
                } else if(!patternEmail.test($('#ORDER_PROP_2').val().trim())){
                    showError('#ORDER_PROP_2', 'Введите email!');
                    $err = true;
                } else if(patternEmail.test($('#ORDER_PROP_2').val().trim())){
                    showDone('#ORDER_PROP_2');
                }

                if($('#ORDER_PROP_3').val().trim() == ''){
                    showError('#ORDER_PROP_3');
                    $err = true;
                } else if($('#ORDER_PROP_3').val().trim() != ''){
                    showDone('#ORDER_PROP_3');
                }

                if(!$('#personal_check').is(':checked')){
                    var boxField = $('#personal_check').closest('.checkbox');
                    boxField.removeClass('input-ok');
                    boxField.addClass('input-error');
                    $err = true;
                } else if($('#personal_check').is(':checked')){
                    var boxField = $('#personal_check').closest('.checkbox');
                    boxField.removeClass('input-error');
                    boxField.addClass('input-ok');
                }

                if(!$err){
                    rbsSetOption("activeBlock", "delivery");
                    $('.total__btn_mobile').removeClass('hide');
                    doNextStep(this);
                    activeOrderButton();
                }else if($err){
                    rbsSetOption("activeBlock", "props");
                    disableOrderButton();
                }

            }

            if($(this).data('block') == 'delivery'){
                $err = false;
                if($('.rbs-delivery-type__active').length == 1 && $('.rbs-delivery-type__active').data('delivery-type-btn') == 'courier'){
                    $('#edost_location_address_div .address-form__col input#edost_street').each(function(){
                        if($(this).val().trim() == ''){
                            $err = true;
                        }
                    });
                }

                if(!$err){
                    $('.rbs-need-address').hide();
                    rbsSetOption("activeBlock", "pay");
                    checkPayFirst();
                    doNextStep(this);
                    activeOrderButton();
                } else {
                    $('.rbs-need-address').show();
                    rbsSetOption("activeBlock", "delivery");
                    disableOrderButton();
                }
            }

            return false;
        });


        $(".js-delivery-choice").on("click", function () {
            $(this).parents('.slick-slide').siblings().find('.delivery-options__item').removeClass('active');
            $(this).parent().addClass('active');
            if ($(this).hasClass('js-delivery-form')) {
                $('.delivery-address').slideDown();
            } else {
                $('.delivery-address').slideUp();
            }
            return false;
        });
        
        $(".js-order-change").on("click", function () {
            rbsSetOption("activeBlock", $(this).parents('.order-step__item').data('block-name'));
            $(this).parents('.order-step__item').addClass('active').removeClass('order-ok');

            $(this).parents('.order-step__item').prev().removeClass('active');
            $(this).parents('.order-step__item').nextAll().removeClass('active').removeClass('order-ok');
            //$('.total_order').find('.button').prop("disabled", true).addClass('button_disabled');
            //$('.total_order').find('.comment-order').slideUp();
            return false;
        });

        $(".js-payment-item").on("click", function () {
            $(".js-payment-item").removeClass("active");
            $(this).addClass('active');
            return false;
        });

        $('.comment-order-wrap a.total__link').on('click', function(e){
            e.preventDefault();
            e.stopPropagation();
            $(this).siblings('.comment-order').toggle();
        });

        $('#ORDER_PROP_1, #ORDER_PROP_2, #ORDER_PROP_3').on('change', function(){
            if(isAuthUser) return;
            rbsSetOption($(this).attr('name'), $(this).val());
        });

        var clearDeliveryTypes = function(){
            var disabledDelivery = $('.rbs-delivery-type__type').not('.rbs-delivery-type__active');

            if(disabledDelivery.length == 2){
                $(".js-delivery-options").css({display: 'none'});
            } else if(disabledDelivery.length == 1) {
                disabledDelivery = disabledDelivery.data('delivery-type-btn');
                if(disabledDelivery){
                    $('[data-delivery-type="' + disabledDelivery + '"]').closest('.slick-slide').css({
                        display: 'none',
                        width: '0px' 
                    });
                    $('[data-delivery-type="' + disabledDelivery + '"]').closest('.slick-slide').each(function(){
                        //$(".js-delivery-options").slick('slickRemove', $(this).data('slick-index'));
                    })
                }
            }
            
        };
        clearDeliveryTypes();

        $('.rbs-delivery-type__type').on('click', function(e){
            e.preventDefault();
            e.stopPropagation();

            if($(this).hasClass('rbs-delivery-type__active')){
                return false;
            }
            
            var deliveryTypeBtn = $(this).data('delivery-type-btn');
            if(!!deliveryTypeBtn){
                $('[data-delivery-type="' + deliveryTypeBtn + '"]').first().find('input').prop('checked', true);
                $('#delivery_type_' + deliveryTypeBtn).prop('checked', true);
                deliveryType = deliveryTypeBtn;
                submitForm();
            }
        });

        if(!!$('[data-delivery-type-btn="self"].rbs-delivery-type__active').length || !$('.rbs-delivery-type__active').length){
            if(!$('[data-delivery-type="self"] input:checked').length){
                disableOrderButton();
                setTimeout(function(){$('[data-delivery-type="self"]').first().find('.js-delivery-choice').click();}, 1000);
            }
        }
       
        updateMobileResultButton();
    };

    $(document).ready(function(){customEventsInit();});
    $(window).scroll(updateMobileResultButton);
    
    function updateMobileResultButton(){
        var fixedBlock = $('.rbs-mobile-order-button');
        if(!!fixedBlock.length){
            if (fixedBlock.offset().top >= $('.total__btm').offset().top ||  $('.total_order').find('.button').is(':disabled')){
                fixedBlock.addClass('_hide');
            } else {
                fixedBlock.removeClass('_hide');
            }
        }
    }
    function rbsSetOption($name, $val){
        var serverHost = '.<?= $_SERVER['HTTP_HOST'] == 'sib.rosbusiness.site' ? $_SERVER['HTTP_HOST'] : 'sibdroid.ru'?>';
        BX.setCookie($name, $val, {expires: 8640000, path: '/', domain: serverHost});
    }

    var isAuthUser = <?=$USER->IsAuthorized() ? 'true' : 'false';?>;    
    var slickOrderOptions={dots:!1,arrows:!0,infinite:!1,autoplay:!1,speed:400,slidesToShow:5,slidesToScroll:1,responsive:[{breakpoint:1400,settings:{slidesToShow:4}},{breakpoint:1150,settings:{slidesToShow:3}},{breakpoint:640,settings:{slidesToShow:2}},{breakpoint:480,settings:{slidesToShow:1}}]};
</script>