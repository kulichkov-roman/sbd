<?php
require $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php";
include_once "include_stop_statistic.php";

use Bitrix\Main\Loader;
use Bitrix\Main\Application;

$request = Application::getInstance()->getContext()->getRequest();

if (!in_array($request->get('type'), ['back', 'front']) || !check_bitrix_sessid()) {
    die();
}

\Bitrix\Main\Loader::includeModule('iblock');
\Bitrix\Main\Loader::includeModule('sib.core');
\Bitrix\Main\Loader::includeModule('yenisite.resizer2');

$hydroEls = \Sib\Core\Helper::getHydrogelElements($request->get('type'));

if(count($hydroEls) <= 0) die();

$arAddedInBasket = [];
$basket = \Bitrix\Sale\Basket::loadItemsForFUser(\Bitrix\Sale\Fuser::getId(), \Bitrix\Main\Context::getCurrent()->getSite());
$basketItems = $basket->getBasketItems();
if (count($basketItems) > 0) {
    foreach ($basketItems as $basketItem) {
        $arAddedInBasket[] = $basketItem->getProductId();
    }
}

$arAddedHydroInBasket= [];
foreach($hydroEls as $el){
    if(in_array($el['ID'], $arAddedInBasket)){
        $arAddedHydroInBasket[$el['ID']] = $el['ID'];
    }
}

$arHydroElsPrices = [];
foreach($hydroEls as $el){
    $arHydroElsPrices[$el['ID']] = $el['PRICES'][0];
}
?>
<div>
    <style>
        h1.title-full{
            max-width: calc(100% - 330px) !important;
            font-size: 22px !important;
        }
        .under-title{
            max-width: 100% !important;
            text-align: center;
            padding: 10px;
        }
        .popup-custom-new{
            display: flex;
        }
        .popup-custom-new__content{
            width: calc(100% - 330px);
            padding: 0 15px 0 0;
            position: relative;
            min-height: 490px;
        }
        .catalog-labels{z-index:9999;}
        .popup-custom-new__aside{
            width: 330px;
            padding-top: 10px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding-bottom: 10px;
        }
        .popup-custom-new__aside .card-main-price .current-price{
            padding-top:15px;
        }

        .aside_sticker{
            border: 1px solid #ffce24;
            border-radius: 5px;
            text-align: center;
            font-size: 15px;
            padding: 10px 15px;
            color: #222;
            font-family: 'OpenSans', sans-serif;
        }
        .js-card-img-big-2 li img{
            max-height: 380px;
            margin: auto;
        }
        .new-sticker{
            background: #ffce24;
            color: #222!important;
            padding-left: 10px;
            font: 700 14px/21px 'OpenSans', sans-serif;
            height: 20px;
        }
        .arrows-area{
            width: calc(100% - 100px);
            margin: auto;
        }

        .arrows-area .arrows-2 .slick-prev, .arrows-area .arrows-2 .slick-next{
            margin: -25px 0 0;
        }

        .js-card-img-nav-2 {
            padding: 0;
        }

        .js-card-img-nav-2 li{
            padding: 10px;
        }

        .js-card-img-nav-2 li div{
            border: 1px solid #dadada;
            padding: 5px;
        }
        .js-card-img-nav-2 .slick-current li div{
            border-color: #ffce24;
            padding: 3px;
        }

            .js-card-img-nav-2 li img{
                max-height: 90px;
                margin: auto;
            }

            .v-container {
                position: relative;
                width: 100%;
                height: 0;
                padding-bottom: 56.25%;
            }
            .video {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
            }

        .hydrotype{
            display: flex;
            justify-content: space-between;
            margin: auto;
            width: 380px;
            padding: 7px;
            background: #f1f2f5;
            border-radius: 20px;
            cursor: pointer;
        }
        .hydrotype-item{
            padding: 7px 15px;
            color: #222;
            border-radius: 15px;
            font-size: 15px;
        }
            .hydrotype-item.active{
                background: #ffce24;
                font-weight: bold;
                
            }

        @media(max-width:992px){
            h1.title-full, .under-title{
                max-width: 100%!important;
            }
            .under-title{
                padding: 0;
            }
            .popup-custom-new{
                flex-direction: column;
            }
            .popup-custom-new__aside, .popup-custom-new__content{
                width: 100%;
                padding: 0;
            }
            .popup-custom-new__content{
                min-height: 455px;
            }
            .v-container{
                margin: 0 auto 0;
            }
            .aside_sticker{
                margin-bottom: 20px;
            }
            .arrows-area{
                width: calc(100% - 50px);
            }
            .hydrotype{
                width: 265px;
            }
            .hydrotype-item {
                padding: 7px 10px;
                font-size: 10px;
            }
        }
        @media(max-width:640px){
            .arrows-area .arrows-2 .slick-next{
                right: -44px;
            }
        }
        @media(max-width: 320px){
            .popup-custom-new__content{
                min-height: 380px;
            }
        }
    </style>

    <div id="hydrogel-window"> 
        <h1 class="main-title detail-title title-full" data-name-tag><?=$hydroEls[0]['NAME']?></h1>
        
        <div class="popup-custom-new">
            <div class="popup-custom-new__content">
                <div class='under-title'>
                    <div class="hydrotype">
                        <div class="hydrotype-item <?=$request->get('type') === 'back' ? 'active' : ''?>" data-url="/ajax/sib/hydrogel.php?type=back">
                            На заднюю панель
                        </div>
                        <div class="hydrotype-item <?=$request->get('type') === 'front' ? 'active' : ''?>" data-url="/ajax/sib/hydrogel.php?type=front">
                            На переднюю панель
                        </div>
                    </div>
                </div>
                <!-- <div class="catalog-labels">
                    <div class="catalog-label new-sticker">Новинка 2020</div>
                </div> -->
                <div class="js-card-img-slider-2">
                    <div class="popup-gallery ">
                        <ul class="js-card-img-big-2">
                            <?foreach ($hydroEls as $el):?>
                                <li class="" data-name="<?=$el['NAME']?>" data-pid="<?=$el['ID']?>">
                                    <img src="<?=$request->get('webp') == 'Y' ? $el['PIC']['WEBP'] : $el['PIC']['JPG']?>">
                                </li>
                            <?endforeach?>
                        </ul>
                    </div>
                    <div class="arrows-area">
                        <ul class="js-card-img-nav-2 arrows-2">
                            <?foreach ($hydroEls as $el):?>
                                <li>
                                    <div>
                                        <img src="<?=$request->get('webp') == 'Y' ? $el['PIC']['WEBP'] : $el['PIC']['JPG']?>">
                                    </div>
                                </li>
                            <?endforeach?>
                        </ul>
                    </div>
                </div>
            
            </div>
            <div class="popup-custom-new__aside">
                <div class="v-container">
                    <iframe class="video" src="https://www.youtube.com/embed/llZRpI6T26k" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                </div>

                <div class="aside_sticker" style="margin:20px 0 10px 0;">
                    Инновационная технология<br>Новинка 2020
                </div>
                <div class="aside_sticker">
                    Самовосстанавливающийся<br>материал
                </div>
                <div class="card-main-price" style="margin-top:15px;">
                    <!--  
                    <div class="card-main-price__top">
                        <p class="old-price" id="bx_117848907_15608_old_price">
                            <span id="price_detail_min_price15608" class="value">13 750</span> <span class="b-rub">Р</span> </p>
                        <div class="economy">
                            Экономия: <span id="price_detail_discount15608" class="value">2 360</span> <span class="b-rub">Р</span> </div>
                    </div>
                    -->
                    <p class="current-price in-ajax-popup" id="">
                        <span id="" class="value"><?=$hydroEls[0]['PRICES'][0]?></span> <span class="b-rub">Р</span> 
                    </p>
                    <div class="detail-bactions" id="bx_117848907_15608_basket_actions">
                        <button type="button" class="buy card-main-price__btn rbs-buy button_white <?=in_array($hydroEls[0]['ID'],$arAddedHydroInBasket)?'rbs-in-cart':''?>" id="" data-pid="<?=$hydroEls[0]['ID']?>">
                            <i class="icon-basket-btn"></i>
                            <span class="text">Добавить в корзину</span>
                            <span class="text in-cart">В корзине</span>
                        </button>
                        <a target="_blank" href="<?=$hydroEls[0]['DETAIL_PAGE_URL']?>" class="rbs-white-button card-main-price__btn">
                            <i class="rbs-full-descr-ico"></i>
                            <span>Полное описание</span>
                        </a>
                    </div>
                </div>

            </div>
        </div>
        <script>
            <?if(count($arAddedHydroInBasket) > 0):?>
                arAddedHydroInBasket = <?=CUtil::PhpToJsObject($arAddedHydroInBasket);?>;
            <?else:?>
                arAddedHydroInBasket = {};
            <?endif?>
                arHydroElsPrices = <?=CUtil::PhpToJsObject($arHydroElsPrices);?>;
                aside_sticker = <?=CUtil::PhpToJsObject($hydroEls);?>;
        </script>
    </div>

</div>