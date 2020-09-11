<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;

$this->setFrameMode(true);

/* ВСТАВИТЬ ПОСЛЕ ОБНОВЛЕНИЯ*/
$configuration = \Bitrix\Main\Config\Configuration::getInstance();
/* /ВСТАВИТЬ ПОСЛЕ ОБНОВЛЕНИЯ*/

// @var $moduleId
// @var $moduleCode
include $_SERVER["DOCUMENT_ROOT"] . SITE_TEMPLATE_PATH . '/include/debug_info_dynamic.php';

$this->SetViewTarget('catalog_paginator');
echo $arResult["NAV_STRING"];
$this->EndViewTarget();

if (empty($arResult['ITEMS']))
    return;

$arJsCache = CRZBitronic2CatalogUtils::getJSCache($component);
$jsString = '';

$showBannerOn = ceil(count($arResult['ITEMS']) / 2);
$count = 0;

$strElementEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT");
$strElementDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE");
$arElementDeleteParams = array("CONFIRM" => GetMessage('CT_BCS_TPL_ELEMENT_DELETE_CONFIRM'));

$bStores = $arParams["USE_STORE"] == "Y" && Bitrix\Main\ModuleManager::isModuleInstalled("catalog");
?>
    <div class="catalog catalog_list">
        <ul class="catalog__list catalog__list_1">
            <? foreach ($arResult['ITEMS'] as $arItem):
                $this->AddEditAction($templateName . '-' . $arItem['ID'], $arItem['EDIT_LINK'], $strElementEdit);
                $this->AddDeleteAction($templateName . '-' . $arItem['ID'], $arItem['DELETE_LINK'], $strElementDelete, $arElementDeleteParams);
                $strMainID = $this->GetEditAreaId($templateName . '-' . $arItem['ID']);
                $arItemIDs = array(
                    'CUSTOM_ID' => $strMainID . '_block_price_but_custom',
                    'ID' => $strMainID,
                    'PICT' => $strMainID . '_pict',
                    'SLIDER_CONT_OF_ID' => $strMainID . '_slider_cont_',
                    'SECOND_PICT' => $strMainID . '_secondpict',
                    'STICKER_ID' => $strMainID . '_sticker',
                    'SECOND_STICKER_ID' => $strMainID . '_secondsticker',
                    'QUANTITY' => $strMainID . '_quantity',
                    'QUANTITY_DOWN' => $strMainID . '_quant_down',
                    'QUANTITY_UP' => $strMainID . '_quant_up',
                    'QUANTITY_MEASURE' => $strMainID . '_quant_measure',
                    'BUY_LINK' => $strMainID . '_buy_link',
                    'BUY_ONECLICK' => $strMainID . '_buy_oneclick',
                    'BUY_ONECLICK_CREDIT' => $strMainID . '_buy_oneclick_credit',
                    'BASKET_ACTIONS' => $strMainID . '_basket_actions',
                    'AVAILABLE_INFO' => $strMainID . '_avail_info',
                    'AVAILABLE_INFO_FULL' => $strMainID . '_avail_info_full',
                    'SUBSCRIBE_LINK' => $strMainID . '_subscribe',
                    'COMPARE_LINK' => $strMainID . '_compare_link',
                    'FAVORITE_LINK' => $strMainID . '_favorite_link',
                    'REQUEST_LINK' => $strMainID . '_request_link',

                    'PRICE' => $strMainID . '_price',
                    'PRICE_CONTAINER' => $strMainID . '_price_container',
                    'PRICE_OLD' => $strMainID . '_price_old',
                    'DSC_PERC' => $strMainID . '_dsc_perc',
                    'SECOND_DSC_PERC' => $strMainID . '_second_dsc_perc',
                    'PROP_DIV' => $strMainID . '_sku_tree',
                    'PROP' => $strMainID . '_prop_',
                    'ARTICUL' => $strMainID . '_articul',
                    'DISPLAY_PROP_DIV' => $strMainID . '_sku_prop',
                    'BASKET_PROP_DIV' => $strMainID . '_basket_prop',
                    'BASKET_BUTTON' => $strMainID . '_basket_button',
                    'STORES' => $strMainID . '_stores',
                    'PRICE_ADDITIONAL' => $strMainID . '_price_additional',
                );
                $arItemCLASSes = array(
                    'LINK' => $strMainID . '_link',
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

                $bSkuExt = $arItem['bSkuExt'];
                $bShowStore = $bStores && !$arItem['bSkuSimple'];
                $bExpandedStore = $arParams['PRODUCT_AVAILABILITY_VIEW'];
                $bShowOneClick = $arParams['DISPLAY_ONECLICK'] && (!$arItem['bOffers'] || $arItem['bSkuExt']);
                $bShowOneClickCredit = false; //$arItem['MIN_PRICE']['DISCOUNT_VALUE'] >= 3000;

                $arItem['ARTICUL'] = (
                $arItem['bOffers'] && $bSkuExt && !empty($arItem['JS_OFFERS'][$arItem['OFFERS_SELECTED']]['ARTICUL'])
                    ? $arItem['JS_OFFERS'][$arItem['OFFERS_SELECTED']]['ARTICUL']
                    : (
                is_array($arItem['PROPERTIES'][$arParams['ARTICUL_PROP']]['VALUE'])
                    ? implode(' / ', $arItem['PROPERTIES'][$arParams['ARTICUL_PROP']]['VALUE'])
                    : $arItem['PROPERTIES'][$arParams['ARTICUL_PROP']]['VALUE']
                )
                );

                $availableOnRequest = (
                $arItem['bOffers'] && $bSkuExt
                    ? empty($arItem['JS_OFFERS'][$arItem['OFFERS_SELECTED']]['MIN_PRICE']) || $arItem['JS_OFFERS'][$arItem['OFFERS_SELECTED']]['MIN_PRICE']['VALUE'] <= 0
                    : empty($arItem['MIN_PRICE']) || $arItem['MIN_PRICE']['VALUE'] <= 0
                );
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

                $bEmptyProductProperties = empty($arItem['PRODUCT_PROPERTIES']);
                $bBuyProps = ('Y' == $arParams['ADD_PROPERTIES_TO_BASKET'] && !$bEmptyProductProperties);

                $bCatchbuy = ($arParams['SHOW_CATCHBUY'] && $arItem['CATCHBUY']);

                if ($arItem['SHOW_SLIDER']) {
                    $arItem['SHOW_SLIDER'] = $arParams['SHOW_GALLERY_THUMB'] == 'Y';
                }
                ?>
                <li class="catalog-item" id="<?= $arItemIDs['ID'] ?>">
                    <div class="catalog-item__main">
                        <?=$arItem['yenisite:stickers']?>
                        <a class="catalog-image" title="<?=$imgTitle?>" href="<?=$arItem['DETAIL_PAGE_URL']?>">
                            <div class="catalog-image__fix">
                                
                                <img alt="<?=$arItem['PICTURE_PRINT']['ALT']?>" class="lazy placeholder" src="<?=SITE_TEMPLATE_PATH.'/img/placetransparent.png'?>" data-original="<?=$arItem['PICTURE_PRINT']['SRC']?>" data-original-jpg="<?=$arItem['PICTURE_PRINT']['SRC_JPG']?>">
                            </div>
                        </a>
                        <div class="catalog-item__content">
                            <div class="catalog-item__col" style="position: relative">
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
                                
                                <div class="reviews-count">
                                    <div class="review-count-ico">
                                        <?=$arItem['COUNT_ASK']?>
                                    </div>
                                </div>

                                <div class="catalog-title">
                                    <a class="catalog-title__link js-ellip-2" href="<?=$arItem['DETAIL_PAGE_URL']?>">
                                        <span><?=$arItem['PREVIEW_TEXT']?></span>
                                    </a>
                                </div>
                                <div class="catalog-item__info">
                                    <?if ($arParams['SHOW_ARTICLE'] == 'Y'):?>
                                        <div class="vendor-code<? if (empty($arItem['ARTICUL'])): ?> hidden<? endif ?>"
                                             id="<?= $arItemIDs['ARTICUL'] ?>"
                                        >
                                            <?=$arItem['PROPERTIES'][$arParams['ARTICUL_PROP']]['NAME']?>: <span><?=$arItem['ARTICUL']?></span>
                                        </div>
                                    <? endif ?>

                                    

                                    <?/*?>
                                    <ul class="device-list">
                                        <? foreach ($arItem['DISPLAY_PROPERTIES'] as $arProp):
                                            $arProp["DISPLAY_VALUE"] = (substr_count($arProp["DISPLAY_VALUE"], "a href") > 0)
                                                ? strip_tags($arProp["DISPLAY_VALUE"])
                                                : $arProp["DISPLAY_VALUE"];
                                            ?>
                                            <li class="device-list__item">
                                                <span><?=$arProp['NAME']?>:</span>
                                                <span><?=(is_array($arProp['DISPLAY_VALUE']) ? implode(' / ', $arProp['DISPLAY_VALUE']) : $arProp['DISPLAY_VALUE'])?></span>
                                            </li>
                                        <?endforeach ?>
                                    </ul>
                                    <?*/?>
                                    <?/* ?>
                                    <div class="reviews-num">
                                        <?
                                            $rev = 0;
                                            $revCnt = (int)$arItem['PROPERTIES']['RESP_COUNT']['VALUE'] <= 10 ? (int)$arItem['PROPERTIES']['RESP_COUNT']['VALUE'] : (int)$arItem['PROPERTIES']['RESP_COUNT']['VALUE'] % 10;
                                            if($revCnt == 1){
                                                $rev = 1;
                                            } else if($revCnt >= 2 && $revCnt <= 4){
                                                $rev = 2;
                                            }

                                            if((int)$arItem['PROPERTIES']['RESP_COUNT']['VALUE'] >= 10 && (int)$arItem['PROPERTIES']['RESP_COUNT']['VALUE'] <=20){
                                                $rev = 0;
                                            }
                                        ?>
                                        <a href="#" class="reviews-num__link"><?=(int)$arItem['PROPERTIES']['RESP_COUNT']['VALUE']?> <?=GetMessage('REVIEWS_' . $rev)?></a>
                                    </div>
                                    <? */?>
                                    <?/*?>
                                    <div class="compare-block">
                                        <button class="compare__link action compare toggleable" href="#"
                                                data-compare-id="<?= $arItem['ID'] ?>"
                                                id="<?= $arItemIDs['COMPARE_LINK'] ?>"
                                        >
                                            <span class="text when-not-toggled"><?= GetMessage('BITRONIC2_TO_COMPARE') ?></span>
                                            <span class="text when-toggled"><?= GetMessage('BITRONIC2_IN_COMPARE') ?></span>
                                        </button>
                                        <div class="compare-tooltip">
                                            <a href="/catalog/compare.php"><?=GetMessage('BITRONIC2_BLOCKS_LINK_TO_COMPARE_TOOLTIP')?></a>
                                            <a href="#" class="compare-delete-item"><?=GetMessage('BITRONIC2_BLOCKS_DEL_TO_COMPARE_TOOLTIP')?></a>
                                        </div>
                                    </div>
                                    <?*/?>
                                </div>
                                <div class="compfav-block">
                                    <div class="favorite-list-btn" id="<?= $arItemIDs['FAVORITE_LINK'] ?>" data-favorite-id="<?= $arItem['ID'] ?>">
                                        В избранное                            
                                    </div>
                                    <div class="compare-list-btn" id="<?= $arItemIDs['COMPARE_LINK'] ?>" data-compare-id="<?= $arItem['ID'] ?>">
                                        Сравнить                           
                                    </div>
                                </div>
                            </div>
                            <div class="catalog-item__col" id="<?=$arItemIDs['CUSTOM_ID']?>">
                                <?$frame = $this->createFrame($arItemIDs['CUSTOM_ID'])->begin();?>
                                    <?if ($arItem['MIN_PRICE']['DISCOUNT_DIFF'] > 0):?>
                                        <div class="economy rbs-economy-list"> 
                                            <?=GetMessage('BITRONIC2_ECONOMY')?> <?=CRZBitronic2CatalogUtils::getElementPriceFormat($arItem['MIN_PRICE']['CURRENCY'], $arItem['MIN_PRICE']['DISCOUNT_DIFF']);?>
                                        </div>
                                    <?endif?>
                                    <div class="catalog-bottom">
                                        <div class="catalog-bottom__left">
                                            <?if ($arItem['MIN_PRICE']['DISCOUNT_DIFF'] > 0):?>
                                                <p class="old-price"><?=CRZBitronic2CatalogUtils::getElementPriceFormat($arItem['MIN_PRICE']['CURRENCY'], $arItem['MIN_PRICE']['VALUE'], $arItem['MIN_PRICE']['PRINT_VALUE']);?></p>
                                            <?endif?>
                                            <p class="current-price" id="<?= $arItemIDs['PRICE'] ?>">
                                        <span class="hidden"
                                            itemprop="lowPrice"><?= $arItem['MIN_PRICE']['DISCOUNT_VALUE'] ?: 0 ?></span>
                                                <span class="hidden" itemprop="priceCurrency"><?= $arItem['MIN_PRICE']['CURRENCY'] ?></span>
                                                <?=CRZBitronic2CatalogUtils::getElementPriceFormat($arItem['MIN_PRICE']['CURRENCY'], $arItem['MIN_PRICE']['DISCOUNT_VALUE'], $arItem['MIN_PRICE']['PRINT_DISCOUNT_VALUE']);?>
                                            </p>
                                        </div>
                                        <div class="catalog-bottom__right" id="<?= $arItemIDs['BASKET_ACTIONS'] ?>">
                                            <?if ($arItem['CAN_BUY'] || $availableOnRequest):?>
                                                <button class="catalog-bottom__button button btn-action buy when-in-stock"
                                                        id="<?= $arItemIDs['BUY_LINK'] ?>" data-product-id="<?= $arItem['ID'] ?>">
                                                    <?=COption::GetOptionString($moduleId, 'button_text_buy')?>
                                                </button>
                                            <?else:?>
                                                <button class="catalog-bottom__button button button_white js-kvk-button"
                                                        data-product="<?= $availableItemID ?>"
                                                        data-fancybox
                                                        data-src="#popup-notify"
                                                >
                                                    <?=GetMessage('BITRONIC2_NOTIFY')?>
                                                </button>
                                            <?endif?>
                                        </div>
                                        <? if ($bShowOneClickCredit): ?>
                                            <div class="credit">
                                                <div class="credit__text">от <?= $arItem['PRICE_CREDIT'] ?><span></span>/мес</div>
                                                <a id="<?= $arItemIDs['BUY_ONECLICK_CREDIT'] ?>" type="button"
                                                class="credit__link one-click-buy-credit js-fancybox"
                                                href="#modal_credit"
                                                data-id="<?= $arItem['ID'] ?>"
                                                data-props="<?= \Yenisite\Core\Tools::GetEncodedArParams($arParams['OFFER_TREE_PROPS']) ?>">
                                                    <?=GetMessage('RBS_BUY_CREDIT')?>
                                                </a>
                                            </div>
                                        <? endif ?>
                                    </div>
                                <?$frame->end();?>
                            </div>
                        </div>
                    </div>
                </li>
                <? include 'js_params.php'; ?>
            <?endforeach?>
        </ul>
    </div>
<?
$frame = $this->createFrame()->begin('');
if ($arJsCache['file']):
    $bytes = fwrite($arJsCache['file'], $jsString);
    if ($bytes === false || $bytes != mb_strlen($jsString, 'windows-1251')) {
        fclose($arJsCache['file']);
        $arJsCache['file'] = false;
    }
endif;
if (!$arJsCache['file']):?>
    <script type="text/javascript">
        <?=$jsString?>
    </script>
<?endif;
$frame->end();

if ($arJsCache['file']) {
    $templateData['jsFile'] = $arJsCache['path'] . '/' . $arJsCache['idJS'];
    fclose($arJsCache['file']);
}