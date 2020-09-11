<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;

// @var $moduleId
// @var $moduleCode
include $_SERVER['DOCUMENT_ROOT'] . SITE_TEMPLATE_PATH . '/include/module_code.php';

/* ВСТАВИТЬ ПОСЛЕ ОБНОВЛЕНИЯ */
$configuration = \Bitrix\Main\Config\Configuration::getInstance();
/* /ВСТАВИТЬ ПОСЛЕ ОБНОВЛЕНИЯ */

$this->setFrameMode(true);
$compositeLoader = CRZBitronic2Composite::insertCompositLoader();
$templateLibrary = array();
$currencyList = '';


if (!empty($arResult['CURRENCIES'])) {
    $templateLibrary[] = 'currency';
    $currencyList = CUtil::PhpToJSObject($arResult['CURRENCIES'], false, true, true);
}
$templateData = array(
    'TEMPLATE_LIBRARY' => $templateLibrary,
    'CURRENCIES' => $currencyList,
    /* 'PROPERTIES' => $arResult['PROPERTIES'], */
);

if ($arResult['CATALOG'] && isset($arResult['OFFERS']) && !empty($arResult['OFFERS'])) {
    $templateData['OFFERS_KEYS'] = array();
    foreach ($arResult['OFFERS'] as $keyOffer => $arOffer) {
        $templateData['OFFERS_KEYS'][$arOffer['ID']] = $keyOffer;
    }
}

$arJsCache = CRZBitronic2CatalogUtils::getJSCache($component);
$_SESSION['RZ_DETAIL_JS_FILE'] = $arJsCache['file'];
$templateData['jsFile'] = $arJsCache['path'] . '/' . $arJsCache['idJS'];
$templateData['jsFullPath'] = $arJsCache['path-full'];

$strMainID = $this->GetEditAreaId($arResult['ID']);
$arItemIDs = array(
    'ID' => $strMainID,
    'PICT' => $strMainID . '_pict',
    'PICT_MODAL' => $strMainID . '_pict_modal',
    'PICT_FLY' => $strMainID . '_pict_fly',
    'DISCOUNT_PICT_ID' => $strMainID . '_dsc_pict',
    'STICKER_ID' => $strMainID . '_sticker',
    'BIG_SLIDER_ID' => $strMainID . '_big_slider',
    'BIG_IMG_CONT_ID' => $strMainID . '_bigimg_cont',
    'SLIDER_CONT_ID' => $strMainID . '_slider_cont',
    'SLIDER_LIST' => $strMainID . '_slider_list',
    'SLIDER_LEFT' => $strMainID . '_slider_left',
    'SLIDER_RIGHT' => $strMainID . '_slider_right',
    'OLD_PRICE' => $strMainID . '_old_price',
    'PRICE' => $strMainID . '_price',
    'DSC_PERC' => $strMainID . '_dsc_perc',
    'DISCOUNT_PRICE' => $strMainID . '_price_discount',
    'SLIDER_CONT_OF_ID' => $strMainID . '_slider_cont_',
    'SLIDER_MODAL_CONT_OF_ID' => $strMainID . '_slider_modal_cont_',
    'SLIDER_LIST_OF_ID' => $strMainID . '_slider_list_',
    'SLIDER_LEFT_OF_ID' => $strMainID . '_slider_left_',
    'SLIDER_RIGHT_OF_ID' => $strMainID . '_slider_right_',
    'QUANTITY' => $strMainID . '_quantity',
    'QUANTITY_DOWN' => $strMainID . '_quant_down',
    'QUANTITY_UP' => $strMainID . '_quant_up',
    'QUANTITY_MEASURE' => $strMainID . '_quant_measure',
    'QUANTITY_LIMIT' => $strMainID . '_quant_limit',
    'BASKET_ACTIONS' => $strMainID . '_basket_actions',
    'AVAILABLE_INFO' => $strMainID . '_avail_info',
    'BUY_LINK' => $strMainID . '_buy_link',
    'BUY_ONECLICK' => $strMainID . '_buy_oneclick',
    'ADD_BASKET_LINK' => $strMainID . '_add_basket_link',
    'COMPARE_LINK' => $strMainID . '_compare_link',
    'FAVORITE_LINK' => $strMainID . '_favorite_link',
    'REQUEST_LINK' => $strMainID . '_request_link',
    'PROP' => $strMainID . '_prop_',
    'PROP_DIV' => $strMainID . '_skudiv',
    'DISPLAY_PROP_DIV' => $strMainID . '_sku_prop',
    'OFFER_GROUP' => $strMainID . '_set_group_',
    'BASKET_PROP_DIV' => $strMainID . '_basket_prop',
    'ARTICUL' => $strMainID . '_articul',
    'PRICE_ADDITIONAL' => $strMainID . '_price_additional',
    'PRICE_ACTIONS' => $strMainID . '_price_actions',
    'PRICE_BONUS' => $strMainID . '_price_bonus',

    //SKU
    'SKU_TABLE' => $strMainID . '_sku_table',
    'AVAIBILITY_EXPANDED' => 'catalog_store_amount_div_detail_' . $arResult['ID'],
);
$arItemCLASSes = array(
    'LINK' => $strMainID . '_link',
);
$strObName = 'ob' . preg_replace("/[^a-zA-Z0-9_]/", "x", $strMainID);
$templateData['strObName'] = $strObName;

$strTitle = (
!empty($arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_TITLE"])
    ? $arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_TITLE"]
    : $arResult['NAME']
);
$strAlt = (
!empty($arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_ALT"])
    ? $arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_ALT"]
    : $arResult['NAME']
);

$bUseBrands = ('Y' == $arParams['BRAND_USE']);

if (isset($arResult['OFFERS']) && !empty($arResult['OFFERS'])) {
    $arOffer = $arResult['OFFERS'][$arResult['OFFERS_SELECTED']];
    $canBuy = $arOffer['CAN_BUY'];
    unset($arOffer);
} else {
    $availableOnRequest = (empty($arResult['MIN_PRICE']) || $arResult['MIN_PRICE']['VALUE'] <= 0);
    $canBuy = (!$availableOnRequest && $arResult['CAN_BUY']);
}

$productTitle = (
isset($arResult["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"]) && $arResult["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"] != ''
    ? $arResult["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"]
    : $arResult["NAME"]
);

$articul = (
$arResult['bOffers'] && $arResult['bSkuExt'] && !empty($arResult['JS_OFFERS'][$arResult['OFFERS_SELECTED']]['ARTICUL'])
    ? $arResult['JS_OFFERS'][$arResult['OFFERS_SELECTED']]['ARTICUL']
    : (
is_array($arResult['PROPERTIES'][$arParams['ARTICUL_PROP']]['VALUE'])
    ? implode(' / ', $arResult['PROPERTIES'][$arParams['ARTICUL_PROP']]['VALUE'])
    : $arResult['PROPERTIES'][$arParams['ARTICUL_PROP']]['VALUE']
)
);
if ('N' == $arParams['SHOW_ARTICLE']) {
    $articul = '';
}

$availableClass = (
!$canBuy && !$availableOnRequest
    ? 'out-of-stock'
    : (
$arResult['FOR_ORDER'] || $availableOnRequest
    ? 'available-for-order'
    : ''
)
);

$strAvailable = (
isset($availableOnRequest) && !$availableOnRequest
    ? (
$canBuy
    ? ($arResult['FOR_ORDER'] ? 'PreOrder' : 'InStock')
    : 'OutOfStock'
)
    : ''
);
$bSibCore = \Bitrix\Main\Loader::includeModule('sib.core');
?>
<?if(!$arParams['QUICK_VIEW']):?>
<? /* ==================== edost НАЧАЛО (инициализация модуля) */ ?>
<?
if(!$_SESSION['is_google_pagespeed']){
    $delivery_img = $APPLICATION->IncludeComponent('edost:catalogdelivery', '', array(
        'PARAM' => array(
                'sort' => 'ASC', // 'ASC' - сортировка по возрастанию, 'cpcr:simple|edost:3|1' - сортировка по тарифам (работает только если нет модуля edost.delivery)
    //            'show_error' => 'Y', // 'Y' - показывать ошибки
    //            'location_id_default' => '1234', // код местоположения по умолчанию
    //            'ico_default' => '/bitrix/images/delivery_edost_img/0.gif', // дефолтная иконка тарифа
            'price_value' => 'min', // если у товара несколько цен, тогда брать минимальную 'min' (по умолчанию), максимальную 'max', самую первую 'first'
            'minimize' => '|normal', // минимизация во встроенном блоке: '|normal' - маленькие иконки, '|full' - то же, что и 'normal' + показывать только самые дешевые тарифы каждой группы/службы доставки
    //            'economize' => 'Y', // экономный расчет: 'Y' - округление веса и стоимости заказа (габариты не учитываются), '500|1000' - расчет по фиксированным параметрам (вес в граммах|стоимость в руб.)
    //            'attract_weight' => '1200|3000|10000', // при экономном расчете притягивать округленный вес к указанным значениям
    //            'attract_price' => '1500|3200', // при экономном расчете притягивать округленную цену к указанным значениям (здесь необходимо перечислить суммы от которых действует скидка с доставки)
            'max' => '6', // ограничение количества тарифов для встроенного блока
            'format_ico' => 'Y', // 'Y' - вместо первых попывшихся тарифов, выводить иконки групп (только с модулем edost.delivery)
    //            'show_ico' => 'N', // 'N' - не показывать иконки
    //            'show_day' => 'N', // 'N' - не показывать срок доставки
        ),
    
    //        'NO_DELIVERY_MESSAGE' => '<span style="color: #F00;">Расчет недоступен</span>', // сообщение, которое выводится, когда нет доступных способов доставки
    //        "INFO" => "Здесь представлена ориентировочная стоимость доставки - окончательный расчет будет производиться на станице оформления заказа.", // выводится в шапке калькулятора
    //        'SHOW_BUTTON' => 'Y', // 'Y' - кнопки 'Пересчитать' и 'Закрыть'
        'FRAME_X' => '650', // ширина окна
    //        'FRAME_Y' => '200', // высота окна
    //        'FRAME_AUTO' => 'Y', // 'Y' - окно растягивается под данные (если данные больше 'FRAME_Y')
    
        'SHOW_QTY' => 'Y', // 'Y' - ячейка для ввода количества
        'SHOW_ADD_CART' => 'Y', // 'Y' - галочка 'Учитывать товары в корзине'
    //        'IMAGE' => 'delivery_blue.png', // картинка калькулятора: delivery_blue.png, delivery_orange.png, delivery_red.png
        'COLOR' => 'clear_white', // цвет окна: blue, blue_light, green, orange, red, gray, black, white, clear_white, F00, FF00FF
    //        'RADIUS' => '8', // скругление углов окна
    
    //        'LOADING' => 'loading_f2.gif', // нестандартная иконка загрузки (в папке bitrix/components/edost/catalogdelivery/images)
    //        'LOADING_SMALL' => 'loading_small_f2.gif', // нестандартная иконка загрузки маленькая (в папке bitrix/components/edost/catalogdelivery/images)
    //        'SCRIPT' => 'Y', // 'Y' - подключать скрипты (с кэшированием НЕ работает!!!), 'N' - НЕ подключать скрипты, 'A' - подключать через JS при загрузке стрaницы (по умолчанию)
    
        'CACHE_TYPE' => 'A',
        'CACHE_GROUPS' => 'Y',
    //        'CACHE_TIME' => '180',
    ), null, array('HIDE_ICONS' => 'Y'));
}
?>

<?endif; /* ==================== edost КОНЕЦ */ ?>

<?
$bCatchbuy = ($arParams['SHOW_CATCHBUY'] && $arResult['CATCHBUY']);
$bDiscountShow = (0 < $arResult['MIN_PRICE']['DISCOUNT_DIFF'] && $arParams['SHOW_OLD_PRICE'] == 'Y');
$bEmptyProductProperties = empty($arResult['PRODUCT_PROPERTIES']);
$bBuyProps = ('Y' == $arParams['ADD_PROPERTIES_TO_BASKET'] && !$bEmptyProductProperties);
$bStores = $arParams["USE_STORE"] == "Y" && Bitrix\Main\ModuleManager::isModuleInstalled("catalog");
$bShowStore = $bStores; // for show stores popup on available status
$bExpandedStore = $arParams['PRODUCT_AVAILABILITY_VIEW'] == 'expanded';
$bTabsStore = $arParams['PRODUCT_AVAILABILITY_VIEW'] == 'tabs' && !$arParams['QUICK_VIEW'];
$arParams['MANUAL_PROP'] = empty($arParams['MANUAL_PROP']) ? 'MANUAL' : $arParams['MANUAL_PROP'];
$bShowDocs = is_array($arResult["PROPERTIES"][$arParams['MANUAL_PROP']]['VALUE']);

//$bShowVideo = is_array($arResult["PROPERTIES"]['VIDEO']['VALUE']);
$bShowVideo = $arResult['RBS_REVIEWS']['COUNT'] > 0;

$bShowOneClick = $arParams['DISPLAY_ONECLICK'] && $arResult['CATALOG'] && (!$arResult['bOffers'] || $arResult['bSkuExt']);
$bShowOneClickCredit = $arResult['MIN_PRICE']['DISCOUNT_VALUE'] >= 3000 && $arResult['CATALOG'];
$bShowOneClickCredit = false;
if(!$_SESSION['is_google_pagespeed']){
    $bShowEdost = CModule::IncludeModule('edost.catalogdelivery') && ($canBuy || $arResult['bOffers']);
}

$bReviewsItem = !empty($arResult['PROPERTIES'][$arParams['PROP_FOR_REVIEWS_ITEM']]['VALUE']) && !empty($arParams['IBLOCK_REVIEWS_ID']) && $arParams['SHOW_REVIEW_ITEM'];
$arResult['bTabs'] = $arResult['bTechTab']
    || $arParams['USE_REVIEW'] == 'Y'
    || $bShowVideo
    || $bShowDocs;

//$placeHolderClass = 'placeholder';
$placeHolderClass = '';
$placeHoldeImg = SITE_TEMPLATE_PATH . '/img/placetransparent.png';
$placeHoldeJpg = SITE_TEMPLATE_PATH . '/img/placejpg.png';
if($arParams['QUICK_VIEW']){
    $placeHoldeJpg = false;
}


?>
<!-- BEGIN CART OPEN  -->

<div itemscope itemtype="http://schema.org/Product" data-page="product-page">
<h1 class="main-title detail-title" itemprop="name"><?= $productTitle ?></h1>
<?if(!$arParams['QUICK_VIEW']):?>
<section class="main-block card-main detail-main-block" id="<? echo $arItemIDs['ID']; ?>">
    <div class="card-main__img">
        <div class="card-img-slider js-card-img-slider">
            <?=$arResult['yenisite:stickers'] ?>
            <div class="card-img-nav js-card-img-nav arrows-2">
                <? foreach ($arResult['MORE_PHOTO'] as $key => $arPhoto): ?>
                    <? if (strval($key) == 'VIDEO') continue; ?>
                        <div class="card-img-nav__item">
                            <span itemscope itemtype="http://schema.org/ImageObject" class="card-img-nav__img">
                                <img class="lazy-detail-nav <?=$placeHolderClass?>"
                                    id="<?= $arItemIDs['PICT'].$key.'_inner' ?>"
                                    src="<?=$placeHoldeJpg?:$arPhoto['SRC_SMALL_JPG']?>"
                                    data-lazy="<?= $arPhoto['SRC_SMALL'] ?>"
                                    data-lazy-jpg="<?= $arPhoto['SRC_SMALL_JPG'] ?>"
                                    alt="<?= $arPhoto['ALT'] ?: $strAlt ?>"
                                    title="<?= $arPhoto['TITLE'] ?: $strTitle ?>"
                                    itemprop="image contentUrl"
                                >
                            </span>
                        </div>
                <? endforeach ?>
            </div>
            <div class="card-img-big js-card-img-big">
                <? foreach ($arResult['MORE_PHOTO'] as $key => $arPhoto): ?>
                    <? if (strval($key) == 'VIDEO') continue; ?>
                    <div class="card-img-big__item">
                        <a class="js-detail-gallery" href="#popup-detail-gallery">
                            <span class="card-img-big__img" itemscope itemtype="http://schema.org/ImageObject">
                                <img class="lazy-detail-main <?=$placeHolderClass?> <?echo !$first?'rbs-first-big-img':''; $first=true;?>"
                                    data-zoom="<?= $arPhoto['SRC_BIG'] ?>"
                                    id="<?= $arItemIDs['PICT'].$key.'_inner' ?>"
                                    src="<?=$placeHoldeJpg?:$arPhoto['SRC_SMALL_JPG']?>"
                                    data-lazy-jpg="<?= $arPhoto['SRC_SMALL_JPG'] ?>"
                                    data-lazy="<?= $arPhoto['SRC_BIG'] ?>"
                                    alt="<?= $arPhoto['ALT'] ?: $strAlt ?>"
                                    title="<?= $arPhoto['TITLE'] ?: $strTitle ?>"
                                    itemprop="image contentUrl"
                                >
                            </span>
                        </a>
                    </div>
                <? endforeach ?>
            </div>
            <?/*?>
            <div class="rbs-video-icon">
                <svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="45px" height="45px" viewBox="0 0 314.068 314.068" style="enable-background:new 0 0 314.068 314.068;" xml:space="preserve"><g><g id="_x33_56._Play"><g><path d="M293.002,78.53C249.646,3.435,153.618-22.296,78.529,21.068C3.434,64.418-22.298,160.442,21.066,235.534c43.35,75.095,139.375,100.83,214.465,57.47C310.627,249.639,336.371,153.62,293.002,78.53z M219.834,265.801c-60.067,34.692-136.894,14.106-171.576-45.973C13.568,159.761,34.161,82.935,94.23,48.26c60.071-34.69,136.894-14.106,171.578,45.971C300.493,154.307,279.906,231.117,219.834,265.801z M213.555,150.652l-82.214-47.949c-7.492-4.374-13.535-0.877-13.493,7.789l0.421,95.174c0.038,8.664,6.155,12.191,13.669,7.851l81.585-47.103C221.029,162.082,221.045,155.026,213.555,150.652z"/></g></g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg>
            </div>
            <?*/?>
        </div>
       
    </div>
<?endif;?>
    <?
        $availableID = &$arItemIDs['AVAILABLE_INFO'];
        $availableFrame = false;
        $availableForOrderText = &$arResult['PROPERTIES']['RZ_FOR_ORDER_TEXT']['VALUE'];
        $availableItemID = &$arResult['ID'];
        $availableMeasure = &$arResult['CATALOG_MEASURE_NAME'];
        $availableQuantity = &$arResult['CATALOG_QUANTITY'];
        $availableStoresPostfix = 'detail';
        $availableSubscribe = $arResult['CATALOG_SUBSCRIBE'];
        $bShowEveryStatus = ($arResult['bOffers'] && $arResult['bSkuExt']);

        $arStatus = [
            'IN_STOCK' => !empty($bShowEveryStatus) || $availableClass == 'in-stock' || empty($availableClass),
            'OUT_OF_STOCK' => !empty($bShowEveryStatus) || $availableClass == 'out-of-stock',
        ];    

        if($bSibCore && $arStatus['OUT_OF_STOCK']) {
            if (isset($arResult['PROPERTIES']['CML2_TRAITS']['VALUE'])) {
                $arStatus['DATE'] = \Sib\Core\Catalog::checkRemainStatus($arResult['PROPERTIES']['CML2_TRAITS']);
            }
        }  

        if($arParams['IS_SERVICE_VIEW']){
            $arStatus['IN_STOCK'] = true;            
        }

        $statusClass = '';
        if(!$arStatus['IN_STOCK']) {
            $statusClass .= 'card-main__cont_wait';
        }

        if($arParams['QUICK_VIEW'] && !$arParams['IS_SERVICE_VIEW']){
            $statusClass .= ' rbs_quick_view';
        }

    ?>
    <?//$frame = $this->createFrame()->begin()?>
    <div class="card-main__cont <?=$statusClass?>" <?if($arParams['QUICK_VIEW']):?>id="<? echo $arItemIDs['ID']; ?>"<?endif;?>>
        <div class="card-main__inf <?if($arParams['IS_SERVICE_VIEW']):?>rbs-quick-view-service<?endif?>">
            <div class="card-main-desc">
                <div class="card-main__title">
                    <div class="card-main__row">
                        <?
                            if(!$arParams['IS_SERVICE_VIEW'] && !$arParams['QUICK_VIEW']){     
                                include $_SERVER['DOCUMENT_ROOT'] . SITE_TEMPLATE_PATH . '/include/availability_info.php';    
                            }
                               
                            $countStore = 0;
                            $textStore = 'Нет';
                            if($arResult['CATALOG_QUANTITY'] >= 20){ $countStore = 3;$textStore = 'Много';}
                            else if($arResult['CATALOG_QUANTITY'] >= 5 && $arResult['CATALOG_QUANTITY'] <= 19){ $countStore = 2;$textStore = 'Средне';}
                            else if($arResult['CATALOG_QUANTITY'] < 5 && $arResult['CATALOG_QUANTITY'] > 0){ $countStore = 1;$textStore = 'Мало';}
                            else { $countStore = 0;}
                        ?>    
                        <?if(!$arParams['QUICK_VIEW']):?>           
                        <?$frame = $this->createFrame()->begin()?>         
                            <div class="store-status card-tooltipe-wrap">
                                <span>Доступно: </span>
                                <span class="store-count" data-count="<?=$countStore?>">
                                    <span>.</span>
                                    <span>.</span>
                                    <span>.</span>
                                </span>
                                <span class="card-tooltipe"><?=$textStore?></span>
                            </div>
                        <?$frame->end();?>
                        <?endif?>
                        <?/*if(!empty($arResult['PROPERTIES'][$arParams['ARTICUL_PROP']]['VALUE'])):?>
                            <div class="card-articul <?=$datePostup?'rbs-articul-with-date':''?>">
                                <?=GetMessage('RZ_ARTICLE')?> <span><?= $arResult['PROPERTIES'][$arParams['ARTICUL_PROP']]['VALUE'] ?></span>
                            </div>
                        <?endif;*/?>
                        <?
                        /*
                            $id = 'bxdinamic_BITRONIC2_detail_rating_' . $arResult['ID'];
                            if(!$arParams['IS_SERVICE_VIEW'] && !$arParams['QUICK_VIEW']):
                        ?>
                        <div id="<?= $id ?>" class="rbs-rating" itemprop="aggregateRating" itemscope
                            itemtype="http://schema.org/AggregateRating">
                            <? //$frame = $this->createFrame($id, false)->begin($compositeLoader);
                        // $frame->setAssetMode(\Bitrix\Main\Page\AssetMode::STANDARD) ?>
                            <? //if ($arParams['SHOW_STARS'] == 'Y'): ?>
                                <? $APPLICATION->IncludeComponent("bitrix:iblock.vote", "sib_stars", array(
                                    "IBLOCK_TYPE" => $arResult['IBLOCK_TYPE_ID'],
                                    "IBLOCK_ID" => $arResult['IBLOCK_ID'],
                                    "ELEMENT_ID" => $arResult['ID'],
                                    "CACHE_TYPE" => $arParams["CACHE_TYPE"],
                                    "CACHE_TIME" => $arParams["CACHE_TIME"],
                                    "MAX_VOTE" => "5",
                                    "VOTE_NAMES" => array("1", "2", "3", "4", "5"),
                                    "SET_STATUS_404" => "N",
                                    "GAMIFICATION" => $arParams["SHOW_GAMIFICATION"]
                                ),
                                    $component,
                                    array("HIDE_ICONS" => "Y")
                                ); ?>
                            <? //endif ?>
                            <?// $frame->end(); ?>
                        </div>
                        <?endif*/?>
                        <?/*if(!$arParams['QUICK_VIEW']):?>
                            <div class="reviews-num">
                                <?
                                    $rev = 0;
                                    $revCnt = (int)$arResult['PROPERTIES']['RESP_COUNT']['VALUE'] <= 10 ? (int)$arResult['PROPERTIES']['RESP_COUNT']['VALUE'] : (int)$arResult['PROPERTIES']['RESP_COUNT']['VALUE'] % 10;
                                    if($revCnt == 1){
                                        $rev = 1;
                                    } else if($revCnt >= 2 && $revCnt <= 4){
                                        $rev = 2;
                                    }

                                    if((int)$arResult['PROPERTIES']['RESP_COUNT']['VALUE'] >= 10 && (int)$arResult['PROPERTIES']['RESP_COUNT']['VALUE'] <=20){
                                        $rev = 0;
                                    }
                                ?>
                                <a href="#tab_5" class="reviews-num__link js-scroll-id">
                                    <?=(int)$arResult['PROPERTIES']['RESP_COUNT']['VALUE']?> <?=GetMessage('REVIEWS_' . $rev)?>
                                </a>
                            </div>
                        <?endif;*/?>
                    </div>
                </div>
                <?if($arParams['QUICK_VIEW']):?>
                    <div class="card-main__img count-<?=count($arResult['MORE_PHOTO'])?>">
                        <div class="card-img-slider js-card-img-slider">
                            <?=$arResult['yenisite:stickers'] ?>
                            <div class="card-img-nav js-card-img-nav arrows-2">
                                <? foreach ($arResult['MORE_PHOTO'] as $key => $arPhoto): ?>
                                    <? if (strval($key) == 'VIDEO') continue; ?>
                                        <div class="card-img-nav__item">
                                            <span itemscope itemtype="http://schema.org/ImageObject" class="card-img-nav__img">
                                                <img class="lazy-detail-nav <?=$placeHolderClass?>"
                                                    id="<?= $arItemIDs['PICT'].$key.'_inner' ?>"
                                                    src="<?=$placeHoldeJpg?:$arPhoto['SRC_SMALL_JPG']?>"
                                                    data-lazy="<?= $arPhoto['SRC_SMALL'] ?>"
                                                    data-lazy-jpg="<?= $arPhoto['SRC_SMALL_JPG'] ?>"
                                                    alt="<?= $arPhoto['ALT'] ?: $strAlt ?>"
                                                    title="<?= $arPhoto['TITLE'] ?: $strTitle ?>"
                                                    itemprop="image contentUrl"
                                                >
                                            </span>
                                        </div>
                                <? endforeach ?>
                            </div>
                            <div class="card-img-big js-card-img-big">
                                <? foreach ($arResult['MORE_PHOTO'] as $key => $arPhoto): ?>
                                    <? if (strval($key) == 'VIDEO') continue; ?>
                                    <div class="card-img-big__item">
                                        <a class="js-detail-gallery" href="#popup-detail-gallery">
                                            <span class="card-img-big__img" itemscope itemtype="http://schema.org/ImageObject">
                                                <img class="lazy-detail-main <?=$placeHolderClass?> <?echo !$first?'rbs-first-big-img':''; $first=true;?>"
                                                    data-zoom="<?= $arPhoto['SRC_BIG'] ?>"
                                                    id="<?= $arItemIDs['PICT'].$key.'_inner' ?>"
                                                    src="<?=$placeHoldeJpg?:$arPhoto['SRC_SMALL_JPG']?>"
                                                    data-lazy-jpg="<?= $arPhoto['SRC_SMALL_JPG'] ?>"
                                                    data-lazy="<?= $arPhoto['SRC_BIG'] ?>"
                                                    alt="<?= $arPhoto['ALT'] ?: $strAlt ?>"
                                                    title="<?= $arPhoto['TITLE'] ?: $strTitle ?>"
                                                    itemprop="image contentUrl"
                                                >
                                            </span>
                                        </a>
                                    </div>
                                <? endforeach ?>
                            </div>
                        </div>
                    </div>
                <?endif?>

                <?if($arParams['IS_SERVICE_VIEW']):?>
                    <?echo $arResult['PREVIEW_TEXT'];?>
                <?endif?>

                <?if(
                    $arResult['PSEUDO_SKU'] && 
                    $arResult['CURRENT_RAM_ROM'] &&
                    $arStatus['IN_STOCK']
                ):?>

                    <?if($arResult['IS_SMARTPHONE_ITEM']):
                        //global $USER; if($USER->IsAdmin()){echo '<pre>'; print_r($arResult['COUNT_PSEUDO_VER']); echo '</pre>';}
                        ?>
                        <?if(!$arResult['IS_APPLE_SECTION'] && $arResult['COUNT_PSEUDO_VER'] > 1):?>
                        <div class="rbs-choose-props-sku">
                            <div class="card-main__memory"><?=GetMessage('RBS_PROP_VER')?></div>
                            <ul class="memory-choice">
                                <?foreach ($arResult['PSEUDO_SKU']['PROPS']['VER'] as $ver):?>
                                    <?
                                        $href = false;
                                        $currVer = $arResult['PSEUDO_SKU']['TREE']['SKU'][$ver];
                                        if(isset($currVer[$arResult['CURRENT_RAM_ROM']][$arResult['CURRENT_COLOR']])){
                                            if($currVer[$arResult['CURRENT_RAM_ROM']][$arResult['CURRENT_COLOR']]['AVAILABLE'])
                                                $href = $currVer[$arResult['CURRENT_RAM_ROM']][$arResult['CURRENT_COLOR']]['LINK'];
                                            else {
                                                foreach($arResult['PSEUDO_SKU']['TREE']['SKU'][$ver] as $mem){
                                                    foreach($mem as $clr)
                                                        if($clr['AVAILABLE'])
                                                            $href = $clr['LINK'];
                                                }
                                            }
                                        } else {
                                            foreach($arResult['PSEUDO_SKU']['TREE']['SKU'][$ver] as $mem){
                                                foreach($mem as $clr)
                                                    if($clr['AVAILABLE'])
                                                        $href = $clr['LINK'];
                                            }
                                        }

                                        $verClass = '';
                                        $isAvialble = true;
                                        if($ver == $arResult['CURRENT_VER']){
                                            $verClass .= ' active';
                                            $href = 'javascript:void(0)';
                                        } else if(!$href){
                                            $verClass .= ' rbs-disable-link';
                                            $href = 'javascript:void(0)';
                                            $isAvialble = false;
                                        }
                                            
                                    ?>  
                                    <li class="memory-choice__item <?=$verClass?>">
                                        <a href="<?=$href?>" class="memory-choice__link js-change-active card-tooltipe-wrap">
                                        <?=$ver?>
                                        <span class="card-tooltipe">
                                            <?=$arResult['PSEUDO_SKU']['TOOLTIPE']['VER'][$ver]?:$ver;?> <?=!$isAvialble?'(нет в наличии)':'';?>
                                        </span>
                                        </a>
                                    </li>
                                <?endforeach;?>
                            </ul>
                        </div>
                        <?endif?>
                        <div class="rbs-choose-props-sku">
                            <div class="card-main__memory"><?=GetMessage('RBS_PROP_MEMORY')?></div>
                            <ul class="memory-choice">
                                <?foreach ($arResult['PSEUDO_SKU']['PROPS']['RAM_ROM'] as $mem => $arMem):?>
                                    <?
                                        $href = false;
                                        $currMem = $arResult['PSEUDO_SKU']['TREE']['SKU'][$arResult['CURRENT_VER']][$mem];
                                        if(isset($currMem[$arResult['CURRENT_COLOR']])){
                                            if($currMem[$arResult['CURRENT_COLOR']]['AVAILABLE'])
                                                $href = $currMem[$arResult['CURRENT_COLOR']]['LINK'];
                                            else {
                                                foreach($arResult['PSEUDO_SKU']['TREE']['SKU'][$arResult['CURRENT_VER']][$mem] as $colorCheck){
                                                    if($colorCheck['AVAILABLE'])
                                                        $href = $colorCheck['LINK'];
                                                }
                                            }
                                        } else {
                                            $href = array_shift($arResult['PSEUDO_SKU']['TREE']['SKU'][$arResult['CURRENT_VER']][$mem])['LINK'];
                                        }

                                        $memClass = '';
                                        $isAvialble = true;
                                        if($mem == $arResult['CURRENT_RAM_ROM']){
                                            $memClass .= ' active';
                                            $href = 'javascript:void(0)';
                                        } else if(!$href){
                                            $memClass .= ' rbs-disable-link';
                                            $href = 'javascript:void(0)';
                                            $isAvialble = false;
                                        }
                                            
                                    ?>  
                                    <li class="memory-choice__item <?=$memClass?>">
                                        <a href="<?=$href?>" class="memory-choice__link js-change-active card-tooltipe-wrap">
                                        <?=$mem?>
                                        <span class="card-tooltipe">
                                            <?=GetMessage('RBS_RAM_ROM', array('#RAM#' => $arMem['RAM'], '#ROM#' => $arMem['ROM']));?> <?=!$isAvialble?'(нет в наличии)':'';?>
                                        </span>
                                        </a>
                                    </li>
                                <?endforeach;?>
                            </ul>
                        </div>
                    <?endif?>
                    <div class="color-choice">
                        <?//if($arParams['IS_MOBILE']):?>
                            <div class="color-choice__title"><?=GetMessage('RBS_PROP_COLOR')?> <span><?//=$arResult['PSEUDO_SKU']['COLORS'][$arResult['CURRENT_COLOR']]['UF_NAME']?></span></div>
                        <?//endif?>
                        <ul class="color-list">
                            <?foreach($arResult['PSEUDO_SKU']['PROPS']['CLR'] as $color):?>
                                <?
                                    $colorClass = '';
                                    $isAvialble = true;
                                    $href = $arResult['PSEUDO_SKU']['TREE']['CURRENT'][$arResult['CURRENT_VER']][$arResult['CURRENT_RAM_ROM']][$color]['LINK'];
                                    
                                    if($arResult['PSEUDO_SKU']['COLORS'][$color]['UF_XML_ID'] == $arResult['CURRENT_COLOR']){
                                        $colorClass .= ' active';
                                        $href = 'javascript:void(0)';
                                    }
                                    if(!$arResult['PSEUDO_SKU']['TREE']['CURRENT'][$arResult['CURRENT_VER']][$arResult['CURRENT_RAM_ROM']][$color]['AVAILABLE']){
                                        $colorClass .= ' rbs-disable-link';
                                        $href = 'javascript:void(0)';
                                        $isAvialble = false;
                                    }
                                        
                                ?>
                                <li class="color-list__item <?=$colorClass?>">
                                    <a href="<?=$href?>" class="color-list__link js-change-active card-tooltipe-wrap">
                                        <?
                                            $borderStyle = '';
                                            if($arResult['PSEUDO_SKU']['COLORS'][$color]['UF_DESCRIPTION'] == '#FFFFFF'){
                                                $borderStyle = 'border:1px solid;';
                                            }
                                        ?>
                                        <span class="color-phone" style="background-color:<?=$arResult['PSEUDO_SKU']['COLORS'][$color]['UF_DESCRIPTION']?>; <?=$borderStyle?>"></span>
                                        <span class="card-tooltipe"><?=$arResult['PSEUDO_SKU']['COLORS'][$color]['UF_NAME']?> <?=!$isAvialble?'(нет в наличии)':'';?></span>
                                    </a>
                                </li>
                            <?endforeach;?>
                        </ul>
                        <?/*if(!$arParams['IS_MOBILE']):?>
                            <div class="color-choice__title"><?=GetMessage('RBS_PROP_COLOR')?> <span><?=$arResult['PSEUDO_SKU']['COLORS'][$arResult['CURRENT_COLOR']]['UF_NAME']?></span></div>
                        <?endif*/?>
                    </div>
                <?              
                    elseif(!empty($arStatus['DATE'])):
                        include $_SERVER['DOCUMENT_ROOT'] . SITE_TEMPLATE_PATH . '/include/detail_wait_avail.php';   
                    elseif(!$arStatus['IN_STOCK']):
                        include $_SERVER['DOCUMENT_ROOT'] . SITE_TEMPLATE_PATH . '/include/detail_not_avail.php';
                    endif;
                ?>

                <?/*if($arParams['QUICK_VIEW'] && !$arParams['IS_SERVICE_VIEW']):?>
                    <div class="compare">
                        <button
                            type="button"
                            class="compare__link action toggleable"
                            id="<?= $arItemIDs['COMPARE_LINK'] ?>"
                            data-compare-id="<?= $arResult['ID'] ?>"
                        >
                            <span class="text when-not-toggled"><?= GetMessage('BITRONIC2_ADD_COMPARE') ?></span>
                            <span class="text when-toggled"><?= GetMessage('BITRONIC2_ADDED_COMPARE') ?></span>
                        </button>
                    </div>
                <?endif;*/?>

                <?if($arResult['GIFT_SMARTPHONE']['IS_SMARTPHONE']):?>
                    <div class="rbs-garanty-block">
                        <div class="rbs-garanty-img">
                            <svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve"><g><g><path d="M480,143.686H378.752c7.264-4.96,13.504-9.888,17.856-14.304c25.792-25.952,25.792-68.192,0-94.144c-25.056-25.216-68.768-25.248-93.856,0c-13.856,13.92-50.688,70.592-45.6,108.448h-2.304c5.056-37.856-31.744-94.528-45.6-108.448c-25.088-25.248-68.8-25.216-93.856,0C89.6,61.19,89.6,103.43,115.36,129.382c4.384,4.416,10.624,9.344,17.888,14.304H32c-17.632,0-32,14.368-32,32v80c0,8.832,7.168,16,16,16h16v192c0,17.632,14.368,32,32,32h384c17.632,0,32-14.368,32-32v-192h16c8.832,0,16-7.168,16-16v-80C512,158.054,497.632,143.686,480,143.686z M138.08,57.798c6.496-6.528,15.104-10.112,24.256-10.112c9.12,0,17.728,3.584,24.224,10.112c21.568,21.696,43.008,77.12,35.552,84.832c0,0-1.344,1.056-5.92,1.056c-22.112,0-64.32-22.976-78.112-36.864C124.672,93.318,124.672,71.302,138.08,57.798z M240,463.686H64v-192h176V463.686z M240,239.686H32v-64h184.192H240V239.686z M325.44,57.798c12.992-13.024,35.52-12.992,48.48,0c13.408,13.504,13.408,35.52,0,49.024c-13.792,13.888-56,36.864-78.112,36.864c-4.576,0-5.92-1.024-5.952-1.056C282.432,134.918,303.872,79.494,325.44,57.798z M448,463.686H272v-192h176V463.686z M480,239.686H272v-64h23.808H480V239.686z"/></g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg>
                        </div>
                        <div>
                            Гарантия 2 года:
                        </div>
                        <div>
                            Дарим второй год сервисного обслуживания
                        </div>
                    </div>
                <?endif?>
                <?if(!$arParams['QUICK_VIEW']):?>
                <div class="rbs-garanty-block rbs-ym-block" onclick="window.open('https://market.yandex.ru/shop--sibdroid-ru/307694/reviews?cpc=srxrGCIO1EEklClLRuasO1uVNrZIignkzyaRrTT8LznXcaNRLFTLa0egn8-TubYFjFRT40IMH_oy6cysa4H6_I15nJJtLtfdaJnnbLV2x6nT3YdeJrDG-ny0o9LCle0ePknkpwJFWlEHtzq5Or3P4Q%2C%2C&cmid=M8NLG09T1LTe4RhKr4axvQ&track=default_offer_reviews_link', '_blank');">
                    <div class="rbs-ym-img">
 
                        <img src="<?=SITE_TEMPLATE_PATH?>/new_img/ym.png" alt="">
                        <div class="rbs-ym-stars">
                            <div class="rating rating_big" data-rating="5">
                                <div class="br-wrapper">
                                    <div class="br-widget br-readonly">
                                        <a href="#"></a>
                                        <a href="#"></a>
                                        <a href="#"></a>
                                        <a href="#"></a>
                                        <a href="#"></a>
                                    </div>
                                </div>
                            </div>
                            <div class="rbs-ym-stars-count">> <?=$arResult['REVIEWS_COUNT']?>к</div>
                        </div>
                    </div>
                    <div>
                        Нам доверяют
                    </div>
                    <div>
                        95% покупают у нас снова                        
                    </div>
                </div>
                <?endif?>
                <?/* if(!$arParams['QUICK_VIEW']):?>
                    <ul class="device-list">
                        <?foreach($arParams['SHORT_PROPERTY_DETAIL_LIST'] as $propCode):?>
                            <?if(isset($arResult['PROPERTIES'][$propCode]) && !empty($arResult['PROPERTIES'][$propCode]['VALUE'])):?>
                                <li class="device-list__item">
                                    <span><span><?=$arResult['PROPERTIES'][$propCode]['NAME']?></span></span>
                                    <span><?=$arResult['PROPERTIES'][$propCode]['VALUE']?></span>
                                </li>
                            <?endif;?>
                        <?endforeach;?>
                    </ul>
                    <? if (strlen(trim($arResult['DETAIL_TEXT'])) > 0) : ?>        
                        <a href="#tab_1" class="blue-link blue-link-with-arr js-scroll-id"><?=GetMessage('RBS_GET_DESCR')?></a>
                    <? endif ?>
                <?endif */?>
            </div>
            <div class="card-main-aside">
                <?//if($arStatus['IN_STOCK']):?>
                    <div style="<?=$arStatus['IN_STOCK'] ? '' : 'display:none;'?>">
                    <div class="card-main-price" id="<?= $arItemIDs['PRICE_ACTIONS'] ?>">
                        <?if($arResult['MIN_PRICE']['DISCOUNT_DIFF'] > 0):?>
                            <div class="card-main-price__top">
                                <p class="old-price" id="<?= $arItemIDs['OLD_PRICE'] ?>">
                                    <?=CRZBitronic2CatalogUtils::getElementPriceFormat($arResult['MIN_PRICE']["CURRENCY"], $arResult['MIN_PRICE']['VALUE'], $arResult['MIN_PRICE']["PRINT_VALUE"], array('ID'=> 'price_detail_min_price'.$arResult['ID']))?>
                                </p>
                                <div class="economy">
                                    <?=GetMessage('RBS_ECONOMY')?> <?=CRZBitronic2CatalogUtils::getElementPriceFormat($arResult['MIN_PRICE']["CURRENCY"], $arResult['MIN_PRICE']['DISCOUNT_DIFF'], $arResult['MIN_PRICE']["PRINT_DISCOUNT_DIFF"], array('ID'=> 'price_detail_discount'.$arResult['ID']))?>
                                </div>
                            </div>
                        <?endif;?>
                        <p class="current-price" id="<?= $arItemIDs['PRICE'] ?>">
                            <?=CRZBitronic2CatalogUtils::getElementPriceFormat($arResult['MIN_PRICE']["CURRENCY"], $arResult['MIN_PRICE']['DISCOUNT_VALUE'], $arResult['MIN_PRICE']["PRINT_DISCOUNT_VALUE"], array('ID'=> 'price_detail'.$arResult['ID']))?>
                        </p>
                        
                        <div class="detail-bactions" id="<? echo $arItemIDs['BASKET_ACTIONS']; ?>">
                            <?// $frame = $this->createFrame($arItemIDs['BASKET_ACTIONS'], false)->begin(CRZBitronic2Composite::insertCompositLoader());?>           
                                <button type="button"
                                        class="buy card-main-price__btn rbs-buy button_white"
                                        id="<?= $arItemIDs['BUY_LINK']; ?>"
                                        <? if (!$bBuyProps || $emptyProductProperties): ?>data-product-id="<?= $arResult['ID'] ?>"<? endif ?>
                                    <?= ($arResult['bOffers'] && $arResult['bSkuExt'] ? ' data-offer-id="' . $arResult['OFFERS'][$arResult['OFFERS_SELECTED']]['ID'] . '"' : '') ?>>
                                        <i class="icon-basket-btn"></i>
                                        <span class="text">Добавить в корзину</span>
                                        <span class="text in-cart">В корзине</span>
                                </button>
                                <?if($arParams['QUICK_VIEW']):?>
                                    <a target="_blank" href="<?=$arResult['DETAIL_PAGE_URL']?>" class="rbs-white-button card-main-price__btn">
                                        <i class="rbs-full-descr-ico"></i>
                                        <span>Полное описание</span>
                                    </a>
                                <?endif?>
                                <? if ($bShowOneClick && !$arParams['QUICK_VIEW']): ?>
                                    <a  id="<?= $arItemIDs['BUY_ONECLICK'] ?>"
                                        href="#modal_quick-buy"
                                        data-id="<?= $arResult['ID'] ?>"
                                        data-props="<?= \Yenisite\Core\Tools::GetEncodedArParams($arParams['OFFER_TREE_PROPS']) ?>"
                                        class="btn-one-click js-fancybox one-click-buy">
                                            <span>Заказ в один клик</span>
                                    </a>
                                <? endif ?>
                            <? //$frame->end() ?>
                        </div>                       
                    </div>
                        <?if(!$arParams['QUICK_VIEW']):?>
                            <?/*  if ($bShowOneClick): ?>
                                <a  id="<?= $arItemIDs['BUY_ONECLICK'] ?>"
                                    href="#modal_quick-buy"
                                    data-id="<?= $arResult['ID'] ?>"
                                    data-props="<?= \Yenisite\Core\Tools::GetEncodedArParams($arParams['OFFER_TREE_PROPS']) ?>"
                                    class="btn-one-click js-fancybox one-click-buy">
                                        <i class="icon-flesh"></i>
                                        <span><?= GetMessage('BITRONIC2_ONECLICK') ?></span>
                                </a>
                            <? endif */ ?>
                            <? /* if ($bShowOneClickCredit): ?>
                                <div class="buy-credit">
                                    <div class="buy-credit__price">от <?= $arResult['PRICE_CREDIT'] ?> <i class="icon-rub"></i>/мес </div>
                                    <a id="<?= $arItemIDs['BUY_ONECLICK_CREDIT'] ?>" type="button"
                                            class="buy-credit__link one-click-buy-credit js-fancybox"
                                            href="#modal_credit"
                                            data-id="<?= $arResult['ID'] ?>"
                                            data-props="<?= \Yenisite\Core\Tools::GetEncodedArParams($arParams['OFFER_TREE_PROPS']) ?>">
                                            <?=GetMessage('RBS_BUY_CREDIT')?>
                                    </a>
                                </div>
                            <? endif */ ?>
                            <? 
                                if(!$_SESSION['is_google_pagespeed']){
                                    include 'delivery.php';
                                }
                            ?>    
                        <?elseif(!$arParams['IS_SERVICE_VIEW'] && !$arParams['QUICK_VIEW']):?>
                            <a 
                                href="<?=$arResult['DETAIL_PAGE_URL']?>"
                                class="btn-one-click one-click-buy"
                                target="_blank"
                            >
                                    <span><?= GetMessage('RBS_MORE') ?></span>
                            </a>
                        <?endif;?>


                        <?if(!$arParams['QUICK_VIEW']):?>
                            <div class="other-options">
                                <div class="compare-checkbox compare-detail" id="<?= $arItemIDs['COMPARE_LINK'] ?>" data-compare-id="<?= $arResult['ID'] ?>">
                                    <span>
                                        Сравнить
                                    </span>                            
                                </div>
                                <div class="compare-checkbox favorite-detail" id="<?= $arItemIDs['FAVORITE_LINK'] ?>" data-favorite-id="<?= $arResult['ID'] ?>">
                                    <span>
                                        В избранное
                                    </span>                            
                                </div>
                            </div>
                        <?endif;?> 
                    </div>      

                <?if(!$arStatus['IN_STOCK']):?>
                    <?if(isset($arResult['FOR_RECOMMEND']) && count($arResult['FOR_RECOMMEND']) > 0):?>
                        <? include 'recommended_sku.php' ?>
                    <?elseif (!empty($arResult['PROPERTIES']['RECOMMEND']['VALUE'])): ?>
                        <? include 'recommended.php' ?>
                    <? endif; ?>
                <?endif;?>
            </div>
            
        </div>
    </div>
    <?//$frame->end()?>
<?
if(!$arParams['QUICK_VIEW']):?>
</section>
<?endif;?>
</div>


<div class="popups">
    <div class="popup-detail-gallery fancybox-content" id="popup-detail-gallery" style="display: none;">
        <div class="popup__main">
                <div class="rbs-modal-img-head">
                    <?=$arResult['NAME'];?>
                </div>
                <div class="rbs-modal-gallery-container">
                    <div class="js-slider-detail-modal arrows-2">
                            <? foreach ($arResult['MORE_PHOTO'] as $key => $arPhoto): ?>
                                <? if (strval($key) == 'VIDEO') continue; ?>
                                <div class="">
                                    <span class="" itemscope itemtype="http://schema.org/ImageObject">
                                        <img
                                            id="<?= $arItemIDs['PICT'].$key.'_inner_modal' ?>"
                                            src="<?=$placeHoldeJpg?>"
                                            data-lazy="<?= $arPhoto['SRC_SMALL'] ?>"
                                            data-lazy-jpg="<?= $arPhoto['SRC_SMALL_JPG'] ?>"
                                            alt="<?= $arPhoto['ALT'] ?: $strAlt ?>"
                                            title="<?= $arPhoto['TITLE'] ?: $strTitle ?>"
                                            itemprop="image contentUrl"
                                        >
                                    </span>
                                </div>
                            <? endforeach ?>
                    </div>
                    
                </div>
                
                <div class="card-main-price"></div>
                <div class="card-main-delivery"></div>
        </div>
    </div>
</div>
<div class="js-slider-detail-modal-nav arrows-2 rbs-invisible">
    <? foreach ($arResult['MORE_PHOTO'] as $key => $arPhoto): ?>
        <? if (strval($key) == 'VIDEO') continue; ?>
            <div class="">
                <span itemscope itemtype="http://schema.org/ImageObject" class="card-img-nav__img">
                    <img 
                        id="<?= $arItemIDs['PICT'].$key.'_inner_min_modal' ?>"
                        src="<?=$placeHoldeJpg?>"
                        data-lazy="<?= $arPhoto['SRC_SMALL'] ?>"
                        data-lazy-jpg="<?= $arPhoto['SRC_SMALL_JPG'] ?>"
                        alt="<?= $arPhoto['ALT'] ?: $strAlt ?>"
                        title="<?= $arPhoto['TITLE'] ?: $strTitle ?>"
                        itemprop="image contentUrl"
                    >
                </span>
            </div>
    <? endforeach ?>
</div>
<!-- CART OPEN EOF -->
<?if(!$arParams['QUICK_VIEW']):?>
    <!-- BEGIN CATALOG -->
    <?$frame = $this->createFrame()->begin('')?>
        <?$bTabServices = false;?>
        <? include 'services.php' ?>
    <?$frame->end()?>
    <?$frame = $this->createFrame()->begin('')?>
        <?$bTabAccess = false;?>
        <? include 'access.php' ?>
    <?$frame->end()?>
    <!-- CARD TABS  -->
    <section class="main-block main-block_tabs" id="description">
    <?$frame = $this->createFrame()->begin('')?>
        <?if($arParams['IS_MOBILE']):?>
            <? include 'tabs_mobile.php' ?>
        <?else:?>
            <? include 'tabs.php' ?>
        <?endif?>
    <?$frame->end()?>
    </section>
    <!-- CARD TABS EOF --> 
    <!-- CATALOG EOF -->   
<?endif;?>
<?if(!$arParams['IS_SERVICE_VIEW']):?>
    <?include 'js_params.php';?>
<?endif?>
<script data-skip-moving="true">
     window.vkAsyncInitCallbacks.push(function(){
        var productObj = {
            id: '<?=$arResult['ID']?>',
            group_id: '<?=$arResult['IBLOCK_SECTION_ID']?>',
            price: parseInt('<?=(int)$arResult['MIN_PRICE']['DISCOUNT_VALUE']?>')
        };

        <?if($arResult['MIN_PRICE']['DISCOUNT_DIFF'] > 0):?>
            productObj.price_old = parseInt('<?=(int)$arResult['MIN_PRICE']['VALUE']?>');
        <?endif?>

        VK.Retargeting.ProductEvent(PRICE_LIST_ID, "view_product", {
            products: [productObj],
            currency_code: 'RUR',
            total_price: productObj.price
        });
    });

    window.fbqAsyncInitCallbacks.push(function(){
        fbq('track', 'ViewContent', { 
            content_type: 'product',
            content_ids: ['<?=$arResult['ID']?>'],
            content_name: '<?=$arResult['NAME']?>',
            content_category: '<?=$arResult['SIB_SECTION_INF']['NAME']?>',
            value: parseInt('<?=(int)$arResult['MIN_PRICE']['DISCOUNT_VALUE']?>'),
            currency: 'RUB'
        });
    });
</script>