/**
 * @param basketItemId
 * @param {{BASKET_ID : string, BASKET_DATA : { GRID : { ROWS : {} }}, COLUMNS: {}, PARAMS: {}, DELETE_ORIGINAL : string }} res
 */
/*function updateBasketTable(basketItemId, basketBlock, simpleUpdate)
 {
 if (!simpleUpdate)
 RZB2.ajax.BasketSmall.Refresh(true);

 initSliders();
 }*/

function skuPropChangeHandler(e)
{
    if (!e)
    {
        e = window.event;
    }
    var select = $(this),
        basketItemId,
        property,
        property_values = {},
        postData = {},
        action_var,
        all_sku_props,
        i,
        sku_prop_value,
        m;

    if (select.length < 1) return;

    // if already selected element is clicked
    if (select.val() == select.find('[selected]').val()) {
        return;
    }
    select.find('[selected]').removeAttr('selected');
    select.find('[value="'+select.val()+'"]').attr('selected', 'selected');

    RZB2.ajax.loader.Start($('#basket_form_container'));

    basketItemId = select.closest('tr').attr('id');
    property = select.data('property');
    action_var = BX('action_var').value;

    property_values[property] = select.val();

    // get other basket item props to get full unique set of props of the new product
    all_sku_props = $('#'+basketItemId).find('.sku select');
    all_sku_props.each(function(){
        if ($(this).attr('id') === 'prop_' + property + '_' + basketItemId) return;

        property_values[$(this).data('property')] = $(this).val();
    });

    postData = {
        'basketItemId': basketItemId,
        'sessid': BX.bitrix_sessid(),
        'site_id': BX.message('SITE_ID'),
        'props': property_values,
        'action_var': action_var,
        'select_props': BX('column_headers').value,
        'offers_props': BX('offers_props').value,
        'quantity_float': BX('quantity_float').value,
        'count_discount_4_all_quantity': BX('count_discount_4_all_quantity').value,
        'price_vat_show_value': BX('price_vat_show_value').value,
        'hide_coupon': BX('hide_coupon').value,
        'use_prepayment': BX('use_prepayment').value
    };

    postData[action_var] = 'select_item';

    BX.ajax({
        url: '/bitrix/components/bitrix/sale.basket.basket/ajax.php',
        method: 'POST',
        data: postData,
        dataType: 'json',
        onsuccess: function(result)
        {
            if (typeof result == "object" && result.CODE == 'ERROR') {
                var message = result.MESSAGE || BX.message('basket_sku_not_available');
                RZB2.ajax.showMessage(message, 'fail');
                RZB2.ajax.loader.Stop($('#basket_form_container'));
                select.closest('tr').addClass('out-of-stock').removeClass('available-for-order');
                return;
            }
            recalcBasketAjax({'BasketRefresh':'n'});
        }
    });
}

//used in template.php
function checkOut()
{
    if (!!BX('coupon'))
        BX('coupon').disabled = true;
    BX("basket_form").submit();
    return true;
}

//used in basket_items.php
function enterCoupon()
{
    var newCoupon = BX('coupon');
    if (!!newCoupon && !!newCoupon.value)
        recalcBasketAjax({'coupon' : newCoupon.value});
}

// check if quantity is valid
// and update values of both controls (text input field for PC and mobile quantity select) simultaneously
function updateQuantity(controlId, basketId, ratio, bUseFloatQuantity)
{
    var oldVal = BX(controlId).defaultValue,
        newVal = parseFloat(BX(controlId).value) || 0,
        bIsCorrectQuantityForRatio = false;

    if (ratio === 0 || ratio == 1)
    {
        bIsCorrectQuantityForRatio = true;
    }
    else
    {

        var newValInt = newVal * 10000,
            ratioInt = ratio * 10000,
            reminder = newValInt % ratioInt,
            newValRound = parseInt(newVal);

        if (reminder === 0)
        {
            bIsCorrectQuantityForRatio = true;
        }
    }

    var bIsQuantityFloat = false;

    if (parseInt(newVal) != parseFloat(newVal))
    {
        bIsQuantityFloat = true;
    }

    newVal = (bUseFloatQuantity === false && bIsQuantityFloat === false) ? parseInt(newVal) : parseFloat(newVal).toFixed(2);

    if (bIsCorrectQuantityForRatio)
    {
        BX(controlId).defaultValue = newVal;

        BX("QUANTITY_INPUT_" + basketId).value = newVal;

        // set hidden real quantity value (will be used in actual calculation)
        BX("QUANTITY_" + basketId).value = newVal;

        recalcBasketAjax({});
    }
    else
    {
        newVal = getCorrectRatioQuantity(newVal, ratio, bUseFloatQuantity);

        if (newVal != oldVal)
        {
            BX("QUANTITY_INPUT_" + basketId).value = newVal;
            BX("QUANTITY_" + basketId).value = newVal;
            recalcBasketAjax({});
        }else
        {
            BX(controlId).value = oldVal;
        }
    }
}

// used when quantity is changed by clicking on arrows
function setQuantity(basketId, ratio, sign, bUseFloatQuantity)
{
    var curVal = parseFloat(BX("QUANTITY_INPUT_" + basketId).value),
        newVal;

    newVal = (sign == 'up') ? curVal + ratio : curVal - ratio;

    if (newVal < 0)
        newVal = 0;

    if (bUseFloatQuantity)
    {
        newVal = newVal.toFixed(2);
    }

    if (ratio > 0 && newVal <= ratio) {
        newVal = ratio;
        $('#QUANTITY_DOWN_' + basketId).addClass('disabled');
    } else {
        $('#QUANTITY_DOWN_' + basketId).removeClass('disabled');
    }

    if (!bUseFloatQuantity && newVal != newVal.toFixed(2))
    {
        newVal = newVal.toFixed(2);
    }

    newVal = getCorrectRatioQuantity(newVal, ratio, bUseFloatQuantity);

    BX("QUANTITY_INPUT_" + basketId).value = newVal;
    BX("QUANTITY_INPUT_" + basketId).defaultValue = newVal;

    //updateQuantity('QUANTITY_INPUT_' + basketId, basketId, ratio, bUseFloatQuantity);
}

function getCorrectRatioQuantity(quantity, ratio, bUseFloatQuantity)
{
    var newValInt = quantity * 10000,
        ratioInt = ratio * 10000,
        reminder = newValInt % ratioInt,
        result = quantity,
        bIsQuantityFloat = false,
        i;
    ratio = parseFloat(ratio);

    if (reminder === 0)
    {
        return result;
    }

    if (ratio !== 0 && ratio != 1)
    {
        for (i = ratio, max = parseFloat(quantity) + parseFloat(ratio); i <= max; i = parseFloat(parseFloat(i) + parseFloat(ratio)).toFixed(2))
        {
            result = i;
        }

    }else if (ratio === 1)
    {
        result = quantity | 0;
    }

    if (parseInt(result, 10) != parseFloat(result))
    {
        bIsQuantityFloat = true;
    }

    result = (bUseFloatQuantity === false && bIsQuantityFloat === false) ? parseInt(result, 10) : parseFloat(result).toFixed(2);

    return result;
}
/**
 *
 * @param {} params
 */
function recalcBasketAjax(params, simpleUpdate, actionType)
{
    var property_values = {},
        action_var = BX('action_var').value,
        items = BX('basket_items'),
        delayedItems = BX('delayed_items'),
        postData,
        i;

    postData = {
        'sessid': BX.bitrix_sessid(),
        'site_id': BX.message('SITE_ID'),
        'props': property_values,
        'action_var': action_var,
        'select_props': BX('column_headers').value,
        'offers_props': BX('offers_props').value,
        'quantity_float': BX('quantity_float').value,
        'count_discount_4_all_quantity': BX('count_discount_4_all_quantity').value,
        'price_vat_show_value': BX('price_vat_show_value').value,
        'hide_coupon': BX('hide_coupon').value,
        'use_prepayment': BX('use_prepayment').value,
        'rz_ajax': 'y',
        'rz_ajax_no_header': 'y',
        'BasketRefresh': 'y',
        'self_url': basketJSParams.SELF_URL,
        'tab': $('#basket_form .um_tab.active').data('tab')
    };

    //postData[action_var] = 'recalculate';

    if (!simpleUpdate)
    {
        if (!!params && typeof params === 'object')
        {
            for (i in params)
            {
                if (params.hasOwnProperty(i))
                    postData[i] = params[i];
            }
        }

        if (!!items && items.childNodes.length > 0)
        {
            for (i = 1; items.childNodes.length > i; i++)
            {
                if(typeof items.childNodes[i].id !== 'undefined' && Number(items.childNodes[i].id) > 0)
                {
                    postData['QUANTITY_' + items.childNodes[i].id] = BX('QUANTITY_' + items.childNodes[i].id).value;
                }
            }
        }

        if (!!delayedItems && delayedItems.rows.length > 0)
        {
            for (i = 1; delayedItems.rows.length > i; i++)
            {
                if(typeof delayedItems.childNodes[i].id !== 'undefined' && Number(delayedItems.childNodes[i].id) > 0)
                {
                    postData['DELAY_' + delayedItems.childNodes[i].id] = 'Y';
                }
            }
        }
    }
    RZB2.ajax.loader.Start($('#basket_form_container'));

    BX.ajax({
        //url: '/bitrix/components/bitrix/sale.basket.basket/ajax.php',
        url: SITE_DIR + 'ajax/sib/big_basket_sib.php',
        method: 'POST',
        data: postData,
        dataType: 'html',
        onsuccess: function(result)
        {
            RZB2.ajax.loader.Stop($('#basket_form_container'));

            if (actionType === 'deleteItem' && !$(result).find('#basket_items').length)
            {
                $('#basket_form').html(result);

                refreshMainSpecSlider();
                $('main.main.main_cart-empty').removeClass('main_cart-full');
            }
            else
            {
                var total = $(result).find('.total').contents(),
                    mobileTotal = $(result).find('.is-mobile-only').contents(),
                    items = $(result).find('#basket_items .cart-item');

                $('#basket_form').find('.total').html(total);
                $('#basket_form').find('.is-mobile-only').html(mobileTotal);

                items.each(function(){
                    var priceBlock = $(this).find('.price.price_sale').contents(),
                        itemID = '#' + $(this).attr('id'),
                        priceTotalMobile = $(this).find('.price__total.mobile').contents();

                    $('#basket_items').find(itemID + ' .price.price_sale').html(priceBlock);
                    $('#basket_items').find(itemID + ' .price__total.mobile').html(priceTotalMobile);
                });

                if (actionType === 'addItem')
                {
                    newItem = items.last();
                    $('#basket_items').append(newItem);
                }
            }

            $('#basket_form').find("img.lazy").lazyload();
        }
    });
}

// BITRONIC 2 CUSTOM PART

var bigBasketTimerQuantity,
    bigBasketTimerTimeout,
    bigBasketUpdateTimeout;

$(document).ready(function(){
    if ($('#basket_form').length)
        $('main.main.main_cart-empty').addClass('main_cart-full');

    $('#basket_form')
        .on('mousedown', 'button.qty__btn', function(e){
            e = e || window.event;
            var _ = $(this);
            if ( _.hasClass('disabled') ) return;

            var id = parseInt(_.closest('.cart-item').attr('id'), 10);
            var ratio = parseFloat(_.data('ratio'));
            var sign = _.hasClass('decrease') ? 'down' : 'up';
            var bUseFloatQuantity = $('#quantity_float').val() == "Y" ? true : false;

            setQuantity(id, ratio, sign, bUseFloatQuantity);
            clearTimeout(bigBasketUpdateTimeout);

            bigBasketTimerTimeout = setTimeout(function(){
                bigBasketTimerQuantity = setInterval(function(){
                    setQuantity(id, ratio, sign, bUseFloatQuantity);
                }, 100);
            }, 300);
             _.data('changed', true);
        })
        .on('mouseup mouseleave', 'button.qty__btn', function(e){
            clearTimeout(bigBasketTimerTimeout);
            clearInterval(bigBasketTimerQuantity);
            var _ = $(this);
             if (!_.data('changed')) return;

             _.data('changed', false);
            var basketId = parseInt(_.closest('.cart-item').attr('id'), 10);
            var ratio = parseFloat(_.data('ratio'));
            var bUseFloatQuantity = $('#quantity_float').val() == "Y" ? true : false;
            bigBasketUpdateTimeout = setTimeout(function () {
                updateQuantity('QUANTITY_INPUT_' + basketId, basketId, ratio, bUseFloatQuantity);
            }, 300);
        })
        .on('click', 'button.qty__btn', function(e) {
            return false;
        })
        .on('click', '.cart-item__del', function(e){
            e.preventDefault();
            var _ = $(this),
                cartItem = _.closest('.cart-item');

            recalcBasketAjax({
                action: _.data('action'),
                id:     _.data('id')
            }, false, 'deleteItem');

            cartItem.remove();
            RZB2.ajax.BasketSmall.ElementsList = {};
            setTimeout(function() { $('#basket_items .cart-filter-list__link.active').click(); }, 200);
        })
        .on('click', '.location__close.button-close', function(e){
            e.preventDefault();

            var _ = $(this),
                item = _.closest('.catalog-item'),
                cartItem = _.closest('.cart-item'),
                deleteCartItemID = $('#basket_items .cart-item[data-product="'+_.data('product')+'"]')[0].id;

            recalcBasketAjax({
                action: _.data('action'),
                id:     deleteCartItemID
            }, false, 'deleteItem');

            $('#basket_items .cart-item#'+deleteCartItemID).remove();

            item.removeClass('active');
            RZB2.ajax.BasketSmall.ElementsList = {};
            setTimeout(function() { cartItem.siblings().find('.cart-filter-list__link.active').click(); }, 200);
        })
        .on('click', 'button[data-coupon]', function(e){
            var value = $(this).attr('data-coupon');
            if (!!value && value.length > 0)
            {
                recalcBasketAjax({'delete_coupon' : value});
            }
        })
        .on('mouseover', '.total__input.inputed', function (params) {
            $(this).next('#btn_delete').addClass('active').removeClass('disable');
        })
        .on('mouseleave', '.total__input.inputed', function (params) {
            $(this).next('#btn_delete').removeClass('active').addClass('disable');
        })
        .on('mouseover', '#btn_delete', function (params) {
            $(this).addClass('active').removeClass('disable');
        })
        .on('mouseleave', '#btn_delete', function (params) {
            $(this).removeClass('active').addClass('disable');
        })
        .on('click', '.coupon-link', function(e){
            $(this).remove();
            e.preventDefault();
        })
        .on('mouseenter', 'button.apply-coupon.valid', function(){
            $(this).find('i').removeClass('flaticon-check33').addClass('flaticon-x5');
        })
        .on('mouseleave', 'button.apply-coupon.valid', function(){
            $(this).find('i').removeClass('flaticon-x5').addClass('flaticon-check33');
        })
        .on('change', '.sku select', skuPropChangeHandler)
        .on('click', '.button.btn-action', function(e){
            e.preventDefault();
            var item = $(this).closest('.catalog-item'),
                btn = item.find('.button'),
                id = item.data('id'),
                cartItem = $(this).closest('.cart-item');

                if(typeof VK !== 'undefined'){
                    var retargetProduct = {
                        id: btn.data('product-id'),
                        group_id: btn.data('group-id'),
                        price: parseInt(btn.data('price')),
                        price_old: parseInt(btn.data('price-old'))
                    };
                    if(retargetProduct.price_old <= 0 || retargetProduct.price >= retargetProduct.price_old){
                        delete retargetProduct.price_old;
                    }
                    VK.Retargeting.ProductEvent(PRICE_LIST_ID, "add_to_cart", {
                        products: [retargetProduct],
                        currency_code: 'RUR',
                        total_price: retargetProduct.price
                    });
                }

            RZB2.ajax.BigBasket.AddToBasket(id);
            item.addClass('active');

            setTimeout(function() { cartItem.siblings().find('.cart-filter-list__link.active').click(); }, 200);
        })
        .on('click', '.cart-filter-list__link', function(e){
            e.preventDefault();
            if ($(this).hasClass('last_link'))
                return;

            $(this).parent().prevAll().find(".cart-filter-list__link").removeClass('active');
            $(this).parent().nextAll().find(".cart-filter-list__link").removeClass('active');
            $(this).addClass('active');

            var item = $(this).closest('.cart-item');
            itemID = item.data('product'),
                sectionID = $(this).data('section'),
                data = {};

            data.rz_ajax = 'y';
            data.item = itemID;
            data.section = sectionID;

            $.ajax({
                type: 'POST',
                url: SITE_DIR + 'ajax/sib/cart_accessories.php',
                data: data,
                success: function (res) {
                    item.find('.cart-slider').html(res);
                    initSliders();
                    RZB2.ajax.quickViewInit($('#basket_form'), '.js-fancybox-accessory');
                }
            })
        })
        .find('.scroll-slider-wrap').each(function(){
        initHorizontalCarousels($('#basket_form'));
        return false;
    });

    $('#popup-accessories').on('click', 'button.buy', function(e){
        var productID = $(this).data('product-id');
        $('#basket_items .catalog-item[data-id="'+productID+'"]').addClass('active');
    });
    $("#basket_items").find('.cart-filter-list__link').first().addClass('active');

    $(window).scroll(function () {
        if (!$('.cart').length)
            return;

        var headerHeight = $('header.header')[0].scrollHeight,
            cartHeight = $('.cart')[0].scrollHeight,
            commonHeight = cartHeight + headerHeight - 500,
            fixedBlock = $('.is-mobile-only');

        if ($(this).scrollTop() >= commonHeight) {
            fixedBlock.addClass('_hide');
        }
        else {
            fixedBlock.removeClass('_hide');
        }
    });
});

$(window).load(function () {
    RZB2.ajax.quickViewInit($('#basket_form'), '.js-fancybox-accessory');
});

function initSliders()
{
    $(".js-slider-7").on('init', function(){
        if ($('.js-ellip-2 span').length) {
            $('.js-ellip-2 span').shave(72);
        }
    });
    
    $(".js-slider-7").each(function(){
        if ( !$(this).hasClass('slick-initialized') )
        {
            if(!(window.screen.width <= 640 && $(this).find('.catalog-item').length <= 1)){
                $(this).slick({
                    dots: true,
                    arrows: true,
                    infinite: true,
                    autoplay: false,
                    speed: 300,
                    slidesToShow: 6,
                    slidesToScroll: 1,
                    adaptiveHeight: true,
                    responsive: [{
                        breakpoint: 1420,
                        settings: {
                            slidesToShow: 5,
                        }
                    },
                        {
                            breakpoint: 1355,
                            settings: {
                                slidesToShow: 4,
                            }
                        },
                        {
                            breakpoint: 1145,
                            settings: {
                                slidesToShow: 3,
                            }
                        },
                        {
                            breakpoint: 1023,
                            settings: {
                                slidesToShow: 4,
                            }
                        },
                        {
                            breakpoint: 640,
                            settings: {
                                slidesToShow: 1,
                            }
                        }
                    ]
                });
            } else {
                $(this).find('.placeholder').lazyload({
                    data_attribute  : "lazy-jpg",
                    data_attribute_webp  : "lazy"
                });
            }
        }
    });
}

function refreshMainSpecSlider(){
    var mainBlock = $('.main-block_index');

    mainBlock.find('.js-slider-2').on('init', function(){
        $(this).on('afterChange', function(){
            mainBlock.find("img.lazy").lazyload();
        });
    });

    if ($(".js-slider-2").length) {
        $('.js-slider-2').slick({
            dots: true,
            arrows: true,
            infinite: true,
            autoplay: false,
            swipeToSlide: true,
            slidesToShow: 5,
            slidesToScroll: 1,
            touchThreshold: 200,
            speed: 300,
            adaptiveHeight: true,
            lazyLoad: 'progressive',
            responsive: [{
                breakpoint: 1400,
                settings: {
                    slidesToShow: 4
                }
            },
                {
                    breakpoint: 1150,
                    settings: {
                        slidesToShow: 3
                    }
                },
                {
                    breakpoint: 1023,
                    settings: {
                        slidesToShow: 3,
                    }
                },
                {
                    breakpoint: 700,
                    settings: {
                        slidesToShow: 2,
                    }
                },
                {
                    breakpoint: 480,
                    settings: {
                        slidesToShow: 1,
                    }
                }
            ]
        });
    };
    if ($(".js-rating").length) { $('.js-rating').barrating({ showSelectedRating: false,  readonly: true }); }
}
