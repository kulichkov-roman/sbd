<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
use Bitrix\Main\ModuleManager;

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

//no whitespace in this file!!!!!!
$this->setFrameMode(true);
/* ВСТАВИТЬ ПОСЛЕ ОБНОВЛЕНИЯ*/
$configuration = \Bitrix\Main\Config\Configuration::getInstance();
/* ВСТАВИТЬ ПОСЛЕ ОБНОВЛЕНИЯ*/

// @var $moduleId
// @var $moduleCode
include $_SERVER["DOCUMENT_ROOT"] . SITE_TEMPLATE_PATH . '/include/debug_info_dynamic.php';

$this->SetViewTarget('catalog_paginator');
echo $arResult["NAV_STRING"];
$this->EndViewTarget();

if (empty($arResult['ITEMS']))
{
    if ($arParams['SEARCH_PAGE'] == 'Y') {
        $this->__component->__parent->arResult['EMPTY_CATALOG'] = true;
        ShowNote(GetMessage("BITRONIC2_SEARCH_NOTHING_TO_FOUND"));
    }
    return;
}
if ($arParams['SEARCH_PAGE'] == 'Y' && $arParams['SEARCH_PAGE_CLOSE_TAG'] != 'Y')
{
    echo '<div class="' . $arParams['SEARCH_PAGE_CLASS'] . '" id="catalog_section" data-hover-effect="' . $arParams['HOVER-MODE'] . '"  data-quick-view-enabled="false">';
}

$arJsCache = CRZBitronic2CatalogUtils::getJSCache($component);

$strElementEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT");
$strElementDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE");
$arElementDeleteParams = array("CONFIRM" => GetMessage('CT_BCS_TPL_ELEMENT_DELETE_CONFIRM'));

$bStores = $arParams["USE_STORE"] == "Y" && Bitrix\Main\ModuleManager::isModuleInstalled("catalog");
$bHoverMode = $arParams['HOVER-MODE'] == 'detailed-expand';
$showBannerOn = ceil(count($arResult['ITEMS']) / 2);
$count = 0;
$countItems = count($arResult['ITEMS']); ?><div class="catalog">
<ul class="catalog__list catalog__list_1 catalog__list_grid">
<?
foreach ($arResult['ITEMS'] as $key => $arItem):
    $this->AddEditAction($templateName . '-' . $arItem['ID'], $arItem['EDIT_LINK'], $strElementEdit);
    $this->AddDeleteAction($templateName . '-' . $arItem['ID'], $arItem['DELETE_LINK'], $strElementDelete, $arElementDeleteParams);
    $strMainID = $this->GetEditAreaId($templateName . '-' . $arItem['ID']);
    $arItemIDs = array(
        'ID' => $strMainID,
        'PICT' => $strMainID . '_pict',
        'SECOND_PICT' => $strMainID . '_secondpict',
        'STICKER_ID' => $strMainID . '_sticker',
        'SECOND_STICKER_ID' => $strMainID . '_secondsticker',
        'QUANTITY_CONTAINER' => $strMainID . '_quantity_container',
        'QUANTITY' => $strMainID . '_quantity',
        'QUANTITY_DOWN' => $strMainID . '_quant_down',
        'QUANTITY_UP' => $strMainID . '_quant_up',
        'QUANTITY_MEASURE' => $strMainID . '_quant_measure',
        'BUY_LINK' => $strMainID . '_buy_link',
        'BUY_ONECLICK' => $strMainID . '_buy_oneclick',
        'BASKET_ACTIONS' => $strMainID . '_basket_actions',
        'NOT_AVAILABLE_MESS' => $strMainID . '_not_avail',
        'SUBSCRIBE_LINK' => $strMainID . '_subscribe',
        'COMPARE_LINK' => $strMainID . '_compare_link',
        'FAVORITE_LINK' => $strMainID . '_favorite_link',
        'REQUEST_LINK' => $strMainID . '_request_link',

        'PRICE_CONTAINER' => $strMainID . '_price_container',
        'OLD_PRICE' => $strMainID . '_old_price',
        'PRICE' => $strMainID . '_price',
        'PRICE_ADDITIONAL' => $strMainID . '_price_additional',
        'DSC_PERC' => $strMainID . '_dsc_perc',
        'SECOND_DSC_PERC' => $strMainID . '_second_dsc_perc',
        'PROP_DIV' => $strMainID . '_sku_tree',
        'PROP' => $strMainID . '_prop_',
        'DISPLAY_PROP_DIV' => $strMainID . '_sku_prop',
        'BASKET_PROP_DIV' => $strMainID . '_basket_prop',
        'AVAILABILITY' => $strMainID . '_availability',
        'AVAILABILITY_MOBILE' => $strMainID . '_availability_mobile',
        'AVAILABLE_INFO_FULL' => $strMainID . '_avail_info_full',
        'ARTICUL' => $strMainID . '_articul',
    );
    $strObName = 'ob' . preg_replace("/[^a-zA-Z0-9_]/", "x", $strMainID);

    $productTitle = (
    !empty($arItem['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'])
        ? $arItem['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']
        : $arItem['NAME']
    );
    $imgTitle = (
    !empty($arItem['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_TITLE'])
        ? $arItem['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_TITLE']
        : $arItem['NAME']
    );
    $bShowStore = $bStores && !$arItem['bSkuSimple'];
    $bExpandedStore = $arParams['PRODUCT_AVAILABILITY_VIEW'];
    $bShowOneClick = $arParams['DISPLAY_ONECLICK'] && (!$arItem['bOffers'] || $arItem['bSkuExt']);;
    $bSkuExt = $arItem['bSkuExt'];

    $arItem['ARTICUL'] = (
    $arItem['bOffers'] && $bSkuExt && !empty($arItem['JS_OFFERS'][$arItem['OFFERS_SELECTED']]['ARTICUL'])
        ? $arItem['JS_OFFERS'][$arItem['OFFERS_SELECTED']]['ARTICUL']
        : (
    is_array($arItem['PROPERTIES'][$arParams['ARTICUL_PROP']]['VALUE'])
        ? implode(' / ', $arItem['PROPERTIES'][$arParams['ARTICUL_PROP']]['VALUE'])
        : $arItem['PROPERTIES'][$arParams['ARTICUL_PROP']]['VALUE']
    )
    );
    $availableOnRequest = $arItem['ON_REQUEST'];
   
    $arItem['CAN_BUY'] = (
    $arItem['bOffers'] && $bSkuExt
        ? $arItem['JS_OFFERS'][$arItem['OFFERS_SELECTED']]['CAN_BUY']
        : $arItem['CAN_BUY'] && !$availableOnRequest
    );

    
    $availableClass = (
    !$arItem['CAN_BUY'] && !$availableOnRequest
        ? 'out-of-stock'
        : (
    $arItem['FOR_ORDER'] || $availableOnRequest
        ? 'available-for-order'
        : 'in-stock'
    )
    );
    
    if ($availableOnRequest) $arItem['CAN_BUY'] = false;

    $bEmptyProductProperties = empty($arItem['PRODUCT_PROPERTIES']);
    $bBuyProps = ('Y' == $arParams['ADD_PROPERTIES_TO_BASKET'] && !$bEmptyProductProperties);

    $bCatchbuy = ($arParams['SHOW_CATCHBUY'] && $arItem['CATCHBUY']);
    if ($arItem['SHOW_SLIDER']) {
        $arItem['SHOW_SLIDER'] = $arParams['SHOW_GALLERY_THUMB'] == 'Y';
    }
    ?>
    <li class="catalog-item" id="<?= $arItemIDs['ID'] ?>">
        <div class="catalog-item__main custom-catalog-item">
            <a class="catalog-image" title="<?=$imgTitle?>" href="<?=$arItem['DETAIL_PAGE_URL']?>">
                <div class="catalog-image__fix">
                    <img alt="<?=$arItem['PICTURE_PRINT']['ALT']?>" class="lazy placeholder" src="<?=SITE_TEMPLATE_PATH.'/img/placetransparent.png'?>" data-original="<?=$arItem['PICTURE_PRINT']['SRC']?>" data-original-jpg="<?=$arItem['PICTURE_PRINT']['SRC_JPG']?>">
                </div>
            </a>
            <button class="catalog-compare button-compare action compare" href="#"
               data-compare-id="<?= $arItem['ID'] ?>"
               id="<?= $arItemIDs['COMPARE_LINK'] ?>"
            >
            </button>
            <button class="catalog-favorite button-favorite action favorite" href="#"
               data-favorite-id="<?=$arItem['ID'] ?>"
               id="<?= $arItemIDs['FAVORITE_LINK'] ?>" 
            >
            </button>
            <div class="compare-tooltip">
                <a href="/catalog/compare.php"><?=GetMessage('BITRONIC2_BLOCKS_LINK_TO_COMPARE_TOOLTIP')?></a>
                <a href="#" class="compare-delete-item"><?=GetMessage('BITRONIC2_BLOCKS_DEL_TO_COMPARE_TOOLTIP')?></a>
            </div>
            <?=$arItem['yenisite:stickers']?>
            <?
                $availableID = &$arItemIDs['AVAILABILITY'];
                $availableFrame = true;
                $availableForOrderText = &$arItem['PROPERTIES']['RZ_FOR_ORDER_TEXT']['VALUE'];
                $availableItemID = &$arItem['ID'];
                $availableMeasure = &$arItem['CATALOG_MEASURE_NAME'];
                $availableQuantity = &$arItem['CATALOG_QUANTITY'];
                $availableStoresPostfix = 'blocks';
                $availableSubscribe = $arItem['bOffers'] ? 'N' : $arItem['CATALOG_SUBSCRIBE'];
                $bShowEveryStatus = ($arItem['bOffers'] && $bSkuExt);
                include $_SERVER['DOCUMENT_ROOT'] . SITE_TEMPLATE_PATH . '/include/availability_info.php';
            ?>
            <div class="catalog-item__content">
                <div class="catalog-title">
                    <a class="catalog-title__link js-ellip-2" href="<?=$arItem['DETAIL_PAGE_URL']?>">
                        <span><?=$productTitle?></span>
                    </a>
                </div>
                <?
                if ($arParams['SHOW_STARS'] == 'N')
                {
                    $APPLICATION->IncludeComponent("bitrix:iblock.vote", "sib_stars", array(
                        "IBLOCK_TYPE" => $arItem['IBLOCK_TYPE_ID'],
                        "IBLOCK_ID" => $arItem['IBLOCK_ID'],
                        "ELEMENT_ID" => $arItem['ID'],
                        "CACHE_TYPE" => $arParams["CACHE_TYPE"],
                        "CACHE_TIME" => $arParams["CACHE_TIME"],
                        "MAX_VOTE" => "5",
                        "VOTE_NAMES" => array("1", "2", "3", "4", "5"),
                        "SET_STATUS_404" => "N",
                    ),
                        $component, array("HIDE_ICONS" => "Y")
                    );
                }
                ?>
                <div class="catalog-bottom">
                   
                        <div class="catalog-bottom__left" id="<?=$arItemIDs['PRICE_CONTAINER']?>">
                            <? $frame = $this->createFrame($arItemIDs['PRICE_CONTAINER'], false)->begin() ?>
                                <?if ($arItem['MIN_PRICE']['DISCOUNT_DIFF'] > 0):?>
                                    <p class="old-price rbs-old-price-list"><?=CRZBitronic2CatalogUtils::getElementPriceFormat($arItem['MIN_PRICE']['CURRENCY'], $arItem['MIN_PRICE']['VALUE'], $arItem['MIN_PRICE']['PRINT_VALUE']);?></p>
                                    <div class="economy rbs-economy-list">
                                        Экономия: <?=CRZBitronic2CatalogUtils::getElementPriceFormat($arItem['MIN_PRICE']['CURRENCY'], $arItem['MIN_PRICE']['DISCOUNT_DIFF'], $arItem['MIN_PRICE']['PRINT_VALUE']);?>
                                    </div>
                                    <div class="clearfix"></div>
                                <?endif?>
                                <p class="current-price" id="<?= $arItemIDs['PRICE'] ?>">
                                    <?= CRZBitronic2CatalogUtils::getElementPriceFormat($arItem['MIN_PRICE']['CURRENCY'], $arItem['MIN_PRICE']['DISCOUNT_VALUE'], $arItem['MIN_PRICE']['PRINT_DISCOUNT_VALUE']); ?>
                                    <span class="hidden"
                                        itemprop="lowPrice"><?= $arItem['MIN_PRICE']['DISCOUNT_VALUE'] ?: 0 ?></span>
                                    <span class="hidden" itemprop="priceCurrency"><?= $arItem['MIN_PRICE']['CURRENCY'] ?></span>
                                </p>
                            <? $frame->end() ?>
                        </div>
                        <div class="catalog-bottom__right" id="<?= $arItemIDs['BASKET_ACTIONS'] ?>">
                            <? $frame = $this->createFrame($arItemIDs['BASKET_ACTIONS'], false)->begin() ?>
                                <?if ($arItem['CAN_BUY'] || $availableOnRequest):?>
                                    <button class="catalog-bottom__button button btn-action buy when-in-stock"
                                            id="<?= $arItemIDs['BUY_LINK'] ?>" data-product-id="<?= $arItem['ID'] ?>">
                                        <?=COption::GetOptionString($moduleId, 'button_text_buy')?>
                                    </button>
                                <?else:?>
                                    <button class="catalog-bottom__button button button_white"
                                            data-product-id="<?= $availableItemID ?>"
                                            data-fancybox
                                            data-src="#popup-notify"
                                    >
                                        <?=GetMessage('NOTIFY')?>
                                    </button>
                                <?endif?>
                            <?$frame->end()?>
                        </div>
                    
                </div>
            </div>
        </div>
    </li>
    <hr class="m-sort">
    <? include 'js_params.php'; ?>
<?endforeach?>
</ul>
</div>
<script type="application/javascript">
    $("#catalog_section").toggleClass("availability-comments-enabled", <?=($arResult['AVAILABILITY_COMMENTS_ENABLED'] ? 'true' : 'false')?>);
</script>

<?
$frame = $this->createFrame()->begin('');
if ($arJsCache['file'])
{
    $bytes = fwrite($arJsCache['file'], $jsString);
    if ($bytes === false || $bytes != mb_strlen($jsString, 'windows-1251'))
    {
        fclose($arJsCache['file']);
        $arJsCache['file'] = false;
    }
}
?>

<?if (!$arJsCache['file']):?>
    <script type="text/javascript">
        <?=$jsString?>
    </script>
<?endif?>

<?
$frame->end();

if ($arParams['SEARCH_PAGE'] == 'Y' && ($arParams['SEARCH_PAGE_CLOSE_TAG'] == 'Y' || $arParams['SEARCH_PAGE_ONLY_CATALOG'] == 'Y'))
{
    echo '</div>';
}

if ($arJsCache['file'])
{
    $templateData['jsFile'] = $arJsCache['path'] . '/' . $arJsCache['idJS'];
    fclose($arJsCache['file']);
}