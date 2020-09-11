<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
\Bitrix\Main\Loader::includeModule('yenisite.bitronic2');
use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;

//no whitespace in this file!!!!!!
$this->setFrameMode(true);

// @var $moduleId
// @var $moduleCode
include $_SERVER["DOCUMENT_ROOT"].SITE_TEMPLATE_PATH.'/include/debug_info.php';
if (empty($arResult['ITEMS']) || ($arParams['MIN_COUNT'] > 0 && count($arResult['ITEMS']) < $arParams['MIN_COUNT'])){
    return;
}
$strElementEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT");
$strElementDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE");
$arElementDeleteParams = array("CONFIRM" => GetMessage('CT_BCS_TPL_ELEMENT_DELETE_CONFIRM'));

$bStores = $arParams["SHOW_AMOUNT_STORE"] == "Y" && Bitrix\Main\ModuleManager::isModuleInstalled("catalog");
$bOneClick = $arParams['DISPLAY_ONECLICK'];
$bHoverMode = $arParams['HOVER-MODE'] == 'detailed-expand';
?><div class="blog__item catalog">
    <div class="blog__item_head fs-20">
        Вас может заинтересовать
    </div>
    <div class="blog__item_interest rbs-hor-catalog">
        <ul class="rbs-hor-catalog__list rbs-correct-old-price catalog__list catalog__list_1 js-slider-2 dots-1 arrows-2">     
            <?foreach($arResult['ITEMS'] as $arItem):
                $this->AddEditAction($arParams['TAB_BLOCK'].'-'.$arItem['ID'], $arItem['EDIT_LINK'], $strElementEdit);
                $this->AddDeleteAction($arParams['TAB_BLOCK'].'-'.$arItem['ID'], $arItem['DELETE_LINK'], $strElementDelete, $arElementDeleteParams);
                $strMainID = $this->GetEditAreaId($arParams['TAB_BLOCK'].'-'.$arItem['ID']);
                $arItemIDs = array(
                    'ID' => $strMainID,
                    'PICT' => $strMainID.'_pict',
                    'SECOND_PICT' => $strMainID.'_secondpict',
                    'STICKER_ID' => $strMainID.'_sticker',
                    'SECOND_STICKER_ID' => $strMainID.'_secondsticker',
                    'QUANTITY' => $strMainID.'_quantity',
                    'QUANTITY_DOWN' => $strMainID.'_quant_down',
                    'QUANTITY_UP' => $strMainID.'_quant_up',
                    'QUANTITY_MEASURE' => $strMainID.'_quant_measure',
                    'BUY_LINK' => $strMainID.'_buy_link',
                    'BUY_ONECLICK' => $strMainID.'_buy_oneclick',
                    'BASKET_ACTIONS' => $strMainID.'_basket_actions',
                    'NOT_AVAILABLE_MESS' => $strMainID.'_not_avail',
                    'SUBSCRIBE_LINK' => $strMainID.'_subscribe',
                    'COMPARE_LINK' => $strMainID.'_compare_link',
                    'FAVORITE_LINK' => $strMainID.'_favorite_link',
                    'CATALOG_BOTTOM_COMPOSITE' => $strMainID.'_ctlg_btm_cmpst',

                    'PRICE' => $strMainID.'_price',
                    'DSC_PERC' => $strMainID.'_dsc_perc',
                    'SECOND_DSC_PERC' => $strMainID.'_second_dsc_perc',
                    'PROP_DIV' => $strMainID.'_sku_tree',
                    'PROP' => $strMainID.'_prop_',
                    'DISPLAY_PROP_DIV' => $strMainID.'_sku_prop',
                    'BASKET_PROP_DIV' => $strMainID.'_basket_prop',
                    'PRICE_ADDITIONAL' => $strMainID.'_price_additional',
                );
                $strObName = 'ob'.preg_replace("/[^a-zA-Z0-9_]/", "x", $strMainID);

                $productTitle = (
                    isset($arItem['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'])&& $arItem['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'] != ''
                    ? $arItem['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']
                    : $arItem['NAME']
                );
                $imgTitle = (
                    !empty($arItem['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_TITLE'])
                    ? $arItem['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_TITLE']
                    : $arItem['NAME']
                );
                $bShowStore = $bStores && !$arItem['bOffers'];
                $bShowOneClick = $bOneClick && (!$arItem['bOffers'] || $arItem['bSkuExt']);

                $availableOnRequest = $arItem['ON_REQUEST'];
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
                if ($arItem['SHOW_SLIDER']) {
                    $arItem['SHOW_SLIDER'] = $arParams['SHOW_GALLERY_THUMB'] == 'Y';
                }
                ?>
                <li class="catalog-item" id="<?=$arItemIDs['ID']?>">
                    <div class="catalog-item__main">
                        <a class="catalog-image" title="<?=$imgTitle?>" href="<?=$arItem['DETAIL_PAGE_URL']?>">
                            <div class="catalog-image__fix">
                                <img alt="<?=$arItem['PICTURE_PRINT']['ALT']?>" class="placeholder" src="<?=SITE_TEMPLATE_PATH . '/img/placetransparent.png'?>" data-lazy="<?=$arItem['PICTURE_PRINT']['SRC']?>"  data-lazy-jpg="<?=$arItem['PICTURE_PRINT']['SRC_JPG']?>"><? /* src="<?=ConsVar::showLoaderWithTemplatePath()?>" */ ?>
                            </div>
                        </a>
                        <? /* ?>
                        <button class="catalog-compare button-compare action compare" href="#"
                                data-compare-id="<?= $arItem['ID'] ?>"
                                id="<?= $arItemIDs['COMPARE_LINK'] ?>"
                        >
                        </button>
                        <div class="compare-tooltip">
                            <a href="/catalog/compare.php"><?=GetMessage('BITRONIC2_BLOCKS_LINK_TO_COMPARE_TOOLTIP')?></a>
                            <a href="#" class="compare-delete-item"><?=GetMessage('BITRONIC2_BLOCKS_DEL_TO_COMPARE_TOOLTIP')?></a>
                        </div>
                        <? */ ?>
                        <?
                            $APPLICATION->IncludeComponent("yenisite:stickers", "sib_section", array(
                                "ELEMENT" => $arItem,
                                "STICKER_NEW" => $arParams['STICKER_NEW'],
                                "STICKER_HIT" => $arParams['STICKER_HIT'],
                                "TAB_PROPERTY_NEW" => $arParams['TAB_PROPERTY_NEW'],
                                "TAB_PROPERTY_HIT" => $arParams['TAB_PROPERTY_HIT'],
                                "TAB_PROPERTY_SALE" => $arParams['TAB_PROPERTY_SALE'],
                                "TAB_PROPERTY_BESTSELLER" => $arParams['TAB_PROPERTY_BESTSELLER'],
                                "MAIN_SP_ON_AUTO_NEW" => $arParams['MAIN_SP_ON_AUTO_NEW'],
                                "SHOW_DISCOUNT_PERCENT" => $arParams['SHOW_DISCOUNT_PERCENT'],
                                "CUSTOM_STICKERS" => $arItem['PROPERTIES'][iRZProp::STICKERS],
                            ),
                                $component, array("HIDE_ICONS"=>"Y")
                            );

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
                                <a class="catalog-title__link js-ellip-2" href="<?=$arItem['DETAIL_PAGE_URL']?>" title="<?=$productTitle?>">
                                    <span><?=$productTitle?></span>
                                </a>
                            </div>
                            <? //if ($arParams['SHOW_STARS'] == 'N'): ?>
                                <?$APPLICATION->IncludeComponent("bitrix:iblock.vote", "sib_stars", array(
                                    "IBLOCK_TYPE" => $arItem['IBLOCK_TYPE_ID'],
                                    "IBLOCK_ID" => $arItem['IBLOCK_ID'],
                                    "ELEMENT_ID" => $arItem['ID'],
                                    "CACHE_TYPE" => $arParams["CACHE_TYPE"],
                                    "CACHE_TIME" => $arParams["CACHE_TIME"],
                                    "MAX_VOTE" => "5",
                                    "VOTE_NAMES" => array("1", "2", "3", "4", "5"),
                                    "SET_STATUS_404" => "N",
                                ),
                                    $component, array("HIDE_ICONS"=>"Y")
                                );?>
                            <?// endif ?>
                            <div class="catalog-bottom" id="<?=$arItemIDs['CATALOG_BOTTOM_COMPOSITE']?>">
                                <?$frame = $this->createFrame()->begin()?>
                                    <div class="catalog-bottom__left">
                                        <?if ($arItem['MIN_PRICE']['DISCOUNT_DIFF'] > 0):?>
                                            <p class="old-price rbs-old-price-list"><?=CRZBitronic2CatalogUtils::getElementPriceFormat($arItem['MIN_PRICE']['CURRENCY'], $arItem['MIN_PRICE']['VALUE'], $arItem['MIN_PRICE']['PRINT_VALUE']);?></p>
                                            <?/*?>
                                            <div class="economy rbs-economy-list">
                                                Экономия: <?=CRZBitronic2CatalogUtils::getElementPriceFormat($arItem['MIN_PRICE']['CURRENCY'], $arItem['MIN_PRICE']['DISCOUNT_DIFF'], $arItem['MIN_PRICE']['PRINT_VALUE']);?>
                                            </div>
                                            <?*/?>
                                            <div class="clearfix"></div>
                                        <?endif?>
                                        <p class="current-price" id="<?= $arItemIDs['PRICE'] ?>">
                                            <?=CRZBitronic2CatalogUtils::getElementPriceFormat($arItem['MIN_PRICE']['CURRENCY'], $arItem['MIN_PRICE']['DISCOUNT_VALUE'], $arItem['MIN_PRICE']['PRINT_DISCOUNT_VALUE']);?>
                                            <span class="hidden"
                                                itemprop="lowPrice"><?= $arItem['MIN_PRICE']['DISCOUNT_VALUE'] ?: 0 ?></span>
                                            <span class="hidden" itemprop="priceCurrency"><?= $arItem['MIN_PRICE']['CURRENCY'] ?></span>
                                        </p>
                                    </div>
                                    <div class="catalog-bottom__right" id="<?= $arItemIDs['BASKET_ACTIONS'] ?>">
                                        
                                            <?if ($arItem['CAN_BUY'] || $availableOnRequest):?>
                                                <a href="<?=$arItem['DETAIL_PAGE_URL']?>" class="catalog-bottom__button button btn-action buy when-in-stock"
                                                        id="<?= $arItemIDs['BUY_LINK'] ?>" data-product-id="<?= $arItem['ID'] ?>">
                                                    Купить
                                                </a>
                                            <?else:?>
                                                <button class="catalog-bottom__button btn button_white"
                                                        data-fancybox
                                                        data-product="<?= $availableItemID ?>"
                                                        data-src="#popup-notify"
                                                >
                                                    <?=GetMessage('NOTIFY')?>
                                                </button>
                                            <?endif?>
                                        
                                    </div>
                                <?$frame->end()?>
                            </div>
                        </div>
                    </div><!-- /.catalog-item__main -->
                    <?
                    $arJSParams = array(
                        'PRODUCT_TYPE' => $arItem['CATALOG_TYPE'],
                        'SHOW_QUANTITY' => ($arParams['USE_PRODUCT_QUANTITY'] == 'Y'),
                        'SHOW_ADD_BASKET_BTN' => false,
                        'SHOW_BUY_BTN' => false,
                        'SHOW_ABSENT' => false,
                        'SHOW_SKU_PROPS' => false,
                        'SECOND_PICT' => $arItem['SECOND_PICT'],
                        'SHOW_OLD_PRICE' => ('Y' == $arParams['SHOW_OLD_PRICE']),
                        'SHOW_DISCOUNT_PERCENT' => ('Y' == $arParams['SHOW_DISCOUNT_PERCENT']),
                        'ADD_TO_BASKET_ACTION' => $arParams['ADD_TO_BASKET_ACTION'],
                        'SHOW_CLOSE_POPUP' => ($arParams['SHOW_CLOSE_POPUP'] == 'Y'),
                        'DISPLAY_COMPARE' => $arParams['DISPLAY_COMPARE_SOLUTION'],
                        'DISPLAY_FAVORITE' => $arParams['DISPLAY_FAVORITE'],
                        'DEFAULT_PICTURE' => array(
                            'PICTURE' => $arItem['PRODUCT_PREVIEW'],
                            'PICTURE_SECOND' => $arItem['PRODUCT_PREVIEW_SECOND']
                        ),
                        'VISUAL' => array(
                            'ID' => $arItemIDs['ID'],
                            'PICT_ID' => $arItemIDs['PICT'],
                            'SECOND_PICT_ID' => $arItemIDs['SECOND_PICT'],
                            'QUANTITY_ID' => $arItemIDs['QUANTITY'],
                            'QUANTITY_UP_ID' => $arItemIDs['QUANTITY_UP'],
                            'QUANTITY_DOWN_ID' => $arItemIDs['QUANTITY_DOWN'],
                            'QUANTITY_MEASURE' => $arItemIDs['QUANTITY_MEASURE'],
                            'PRICE_ID' => $arItemIDs['PRICE'],
                            'TREE_ID' => $arItemIDs['PROP_DIV'],
                            'TREE_ITEM_ID' => $arItemIDs['PROP'],
                            'BUY_ID' => $arItemIDs['BUY_LINK'],
                            'BUY_ONECLICK' => $arItemIDs['BUY_ONECLICK'],
                            'ADD_BASKET_ID' => $arItemIDs['ADD_BASKET_ID'],
                            'DSC_PERC' => $arItemIDs['DSC_PERC'],
                            'SECOND_DSC_PERC' => $arItemIDs['SECOND_DSC_PERC'],
                            'DISPLAY_PROP_DIV' => $arItemIDs['DISPLAY_PROP_DIV'],
                            'BASKET_ACTIONS_ID' => $arItemIDs['BASKET_ACTIONS'],
                            'BASKET_PROP_DIV' => $arItemIDs['BASKET_PROP_DIV'],
                            'NOT_AVAILABLE_MESS' => $arItemIDs['NOT_AVAILABLE_MESS'],
                            'COMPARE_LINK_ID' => $arItemIDs['COMPARE_LINK'],
                            'FAVORITE_ID' => $arItemIDs['FAVORITE_LINK']
                        ),
                        'BASKET' => array(
                            'QUANTITY' => $arParams['PRODUCT_QUANTITY_VARIABLE'],
                            'ADD_PROPS' => ('Y' == $arParams['ADD_PROPERTIES_TO_BASKET']),
                            'PROPS' => $arParams['PRODUCT_PROPS_VARIABLE'],
                            'EMPTY_PROPS' => $bEmptyProductProperties,
                            'SKU_PROPS' => $arItem['OFFERS_PROP_CODES'],
                            'BASKET_URL' => $arParams['BASKET_URL'],
                            'ADD_URL_TEMPLATE' => $arResult['ADD_URL_TEMPLATE'],
                            'BUY_URL_TEMPLATE' => $arResult['BUY_URL_TEMPLATE']
                        ),
                        'PRODUCT' => array(
                            'ID' => $arItem['ID'],
                            'IBLOCK_ID' => $arItem['IBLOCK_ID'],
                            'NAME' => $productTitle,
                            'PICT' => ('Y' == $arItem['SECOND_PICT'] ? $arItem['PREVIEW_PICTURE_SECOND'] : $arItem['PREVIEW_PICTURE']),
                            'CAN_BUY' => $arItem["CAN_BUY"],
                            'SUBSCRIPTION' => ('Y' == $arItem['CATALOG_SUBSCRIPTION']),
                            'CHECK_QUANTITY' => $arItem['CHECK_QUANTITY'],
                            'MAX_QUANTITY' => $arItem['CATALOG_QUANTITY'],
                            'STEP_QUANTITY' => $arItem['CATALOG_MEASURE_RATIO'],
                            'QUANTITY_FLOAT' => is_double($arItem['CATALOG_MEASURE_RATIO']),
                            'SUBSCRIBE_URL' => $arItem['~SUBSCRIBE_URL'],
                            'BASIS_PRICE' => $arItem['MIN_BASIS_PRICE'],
                            'PRICE_MATRIX' => $arItem['PRICE_MATRIX']
                        ),
                        'OFFERS' => array(),
                        'OFFER_SELECTED' => 0,
                        'TREE_PROPS' => array(),
                        'LAST_ELEMENT' => $arItem['LAST_ELEMENT']
                    );
                    if ($arParams['DISPLAY_COMPARE_SOLUTION'])
                    {
                        $arJSParams['COMPARE'] = array(
                            'COMPARE_URL_TEMPLATE' => $arResult['COMPARE_URL_TEMPLATE'],
                            'COMPARE_URL_TEMPLATE_DEL' => $arResult['COMPARE_URL_TEMPLATE_DEL'],
                            'COMPARE_PATH' => $arParams['COMPARE_PATH']
                        );
                    }
                    /*
                    ?>
                    <script type="text/javascript">
                        var <? echo $strObName; ?> = new JCCatalogItem(<? echo CUtil::PhpToJSObject($arJSParams, false, true); ?>);
                    </script>
                    <?*/?>
                </li><!-- /.catalog-item -->
            <?endforeach?>        
        </ul>
            
    </div>
</div>