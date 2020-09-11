<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;

//no whitespace in this file!!!!!!
$this->setFrameMode(true);

// @var $moduleId
// @var $moduleCode
include $_SERVER["DOCUMENT_ROOT"].SITE_TEMPLATE_PATH.'/include/debug_info.php';
/* if(isset($_POST["rz_ajax"]) && $_POST["rz_ajax"] === "y"){
    global ${$arParams['FILTER_NAME']};
    print_r(${$arParams['FILTER_NAME']});
} */

if (empty($arResult['ITEMS']) && empty($arResult['CUSTOM_ITEMS']))
	return;

$strElementEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT");
$strElementDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE");
$arElementDeleteParams = array("CONFIRM" => GetMessage('CT_BCS_TPL_ELEMENT_DELETE_CONFIRM'));

$bStores = $arParams["SHOW_AMOUNT_STORE"] == "Y" && Bitrix\Main\ModuleManager::isModuleInstalled("catalog");
$bOneClick = $arParams['DISPLAY_ONECLICK'];
$bHoverMode = $arParams['HOVER-MODE'] == 'detailed-expand';
?>

<section id="<?=$arParams['MAIN_BLOCK_ID'];?>" class="main-block main-block_accessory">
    <div class="main-block__top">
        <div class="main-block__left">
            <p class="main-title"><?=$arParams['BLOCK_TITLE']?></p>
        </div>
        <?if(!empty($arResult['ITEMS'])):?>
        <div class="main-block__right">
            <a class="arrow-link js-scroll-id" href="<?=$arParams['LINK_TO_ALL']?>" onclick="<?=$arParams['ONCLICK_ACTION']?>"><span><?=$arParams['LINK_TO_ALL_TEXT']?></span></a>
        </div>
        <?endif?>
    </div>
    <div id="<?=$arParams['REPLACE_AJAX_ID'];?>" class="rbs-hor-catalog rbs-ajax-cmp catalog main-block_index">
        <?//if(isset($_POST["rz_ajax"]) && $_POST["rz_ajax"] === "y"):?>    
        <ul class="rbs-hor-catalog__list rbs-correct-old-price rbs-ajax-cmp catalog__list catalog__list_1 js-slider-access-line dots-1 arrows-2">
            <?foreach($arResult['CUSTOM_ITEMS'] as $customItem):?>
                <li class="catalog-item">
                    <div class="catalog-item__main access_fix custom-item">
                        <div class="catalog-item__overlay">
                            <button class="location__close button-close js-close-loc" data-product-id="<?= $arItem['ID'] ?>"></button>
                            <?=GetMessage('ADDED_TO_CART');?>
                        </div>
                        <a class="catalog-image js-fancybox-accessory" title="<?=$customItem['NAME']?>" data-price="<?=$customItem['PRICE']?>"  data-custom-popup="access_custom" data-ajax-url="<?=$customItem['AJAX_HREF']?>" href="#popup-accessories">
                            <div class="catalog-image__fix">
                                <img alt="<?=$customItem['NAME']?>" class="placeholder" data-lazy="<?=$customItem['PIC']['WEBP']?>" data-lazy-jpg="<?=$customItem['PIC']['JPG']?>" src="<?=SITE_TEMPLATE_PATH . '/img/placetransparent.png'?>">
                            </div>
                        </a>
                        <?
                            //stickers
                            //avail
                        ?>
                       <?
                           /*  $APPLICATION->IncludeComponent("yenisite:stickers", "sib_section", array(
                                "ELEMENT" => $customItem,
                                "STICKER_NEW" => $arParams['STICKER_NEW'],
                                "STICKER_HIT" => $arParams['STICKER_HIT'],
                                "TAB_PROPERTY_NEW" => $arParams['TAB_PROPERTY_NEW'],
                                "TAB_PROPERTY_HIT" => $arParams['TAB_PROPERTY_HIT'],
                                "TAB_PROPERTY_SALE" => $arParams['TAB_PROPERTY_SALE'],
                                "TAB_PROPERTY_BESTSELLER" => $arParams['TAB_PROPERTY_BESTSELLER'],
                                "MAIN_SP_ON_AUTO_NEW" => $arParams['MAIN_SP_ON_AUTO_NEW'],
                                "SHOW_DISCOUNT_PERCENT" => $arParams['SHOW_DISCOUNT_PERCENT'],
                                "CUSTOM_STICKERS" => $customItem['STICKERS'],
                            ),
                                $component, array("HIDE_ICONS"=>"Y")
                            ); */
                        ?>
                        <div class="rbs-hydrogel-sticker"></div>
                        
                        <div class="catalog-item__content">
                            <div class="catalog-title">
                                <a class="catalog-title__link js-fancybox-accessory" data-custom-popup="access_custom" data-price="<?=$customItem['PRICE']?>" data-ajax-url="<?=$customItem['AJAX_HREF']?>" href="#popup-accessories">
                                    <span><?=$customItem['NAME']?></span>
                                </a>
                            </div>
                            <div class="catalog-bottom">
                                <div class="catalog-bottom__left">
                                    <?if ($customItem['OLD_PRICE'] > 0):?>
                                        <p class="old-price"><?=CRZBitronic2CatalogUtils::getElementPriceFormat('RUB', $customItem['OLD_PRICE'], $customItem['OLD_PRICE']);?></p>
                                    <?endif?>
                                    <p class="current-price">
                                        <?=CRZBitronic2CatalogUtils::getElementPriceFormat('RUB', $customItem['PRICE'], $customItem['PRICE']);?>
                                    </p>
                                </div>
                                <div class="catalog-bottom__right">
                                    <button class="catalog-bottom__button button btn-action when-in-stock js-fancybox-accessory" data-price="<?=$customItem['PRICE']?>" data-custom-popup="access_custom" data-ajax-url="<?=$customItem['AJAX_HREF']?>" href="#popup-accessories">
                                        Выбрать
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div><!-- /.catalog-item__main -->
                </li>
            <?endforeach?>
            <?foreach($arResult['ITEMS'] as $arItem):
                //if($arItem['CATALOG_QUANTITY'] <= 0) continue;
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
                    'COMPARE_LINK' => $strMainID.'_compare_link_access',
                    'FAVORITE_LINK' => $strMainID.'_favorite_link',

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
                <div class="catalog-item__main access_fix">
                    <div class="catalog-item__overlay">
                        <button class="location__close button-close js-close-loc" data-product-id="<?= $arItem['ID'] ?>"></button>
                        <?=GetMessage('ADDED_TO_CART');?>
                    </div>
                    <a class="catalog-image js-fancybox-accessory" title="<?=$imgTitle?>" data-url="<?=$arItem['DETAIL_PAGE_URL']?>" href="#popup-accessories">
                        <div class="catalog-image__fix">
                            <img alt="<?=$arItem['PICTURE_PRINT']['ALT']?>" class="placeholder" data-lazy="<?=$arItem['PICTURE_PRINT']['SRC']?>" data-lazy-jpg="<?=$arItem['PICTURE_PRINT']['SRC_JPG']?>" src="<?=SITE_TEMPLATE_PATH . '/img/placetransparent.png'?>">
                        </div>
                    </a>
                    <?/* ?>
                    <button class="catalog-compare button-compare"
                            data-compare-id="<?= $arItem['ID'] ?>"
                            id="<?= $arItemIDs['COMPARE_LINK'] ?>"
                    >
                    </button>
                    <? */?>
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
                    //include $_SERVER['DOCUMENT_ROOT'] . SITE_TEMPLATE_PATH . '/include/availability_info.php';
                    ?>
                    <div class="catalog-item__content">
                        <div class="catalog-title">
                            <a class="catalog-title__link js-fancybox-accessory" data-url="<?=$arItem['DETAIL_PAGE_URL']?>" href="#popup-accessories">
                                <span><?=$productTitle?></span>
                            </a> 
                        </div>
                        <? /* if ($arParams['SHOW_STARS'] == 'N'): ?>
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
                        <? else: ?>
                            <div class="rating rating_big"></div>
                        <? endif */ ?>
                        <div class="catalog-bottom">
                            <div class="catalog-bottom__left">
                                <?if ($arItem['MIN_PRICE']['DISCOUNT_DIFF'] > 0):?>
                                    <p class="old-price"><?=CRZBitronic2CatalogUtils::getElementPriceFormat($arItem['MIN_PRICE']['CURRENCY'], $arItem['MIN_PRICE']['VALUE'], $arItem['MIN_PRICE']['PRINT_VALUE']);?></p>
                                <?endif?>
                                <p class="current-price" id="<?= $arItemIDs['PRICE'] ?>">
                                    <?=CRZBitronic2CatalogUtils::getElementPriceFormat($arItem['MIN_PRICE']['CURRENCY'], $arItem['MIN_PRICE']['DISCOUNT_VALUE'], $arItem['MIN_PRICE']['PRINT_DISCOUNT_VALUE']);?>
                                    <?//=$arItem['MIN_PRICE']['DISCOUNT_VALUE'];?>
                                    <span class="hidden"
                                          itemprop="lowPrice"><?= $arItem['MIN_PRICE']['DISCOUNT_VALUE'] ?: 0 ?></span>
                                    <span class="hidden" itemprop="priceCurrency"><?= $arItem['MIN_PRICE']['CURRENCY'] ?></span>
                                </p>
                            </div>
                            <div class="catalog-bottom__right" id="<?= $arItemIDs['BASKET_ACTIONS'] ?>">
                                <?$frame = $this->createFrame()->begin(CRZBitronic2Composite::insertCompositLoader())?>
                                <?if ($arItem['CAN_BUY'] || $availableOnRequest):?>
                                    <button class="catalog-bottom__button button btn-action buy when-in-stock"
                                            id="<?= $arItemIDs['BUY_LINK'] ?>" data-product-id="<?= $arItem['ID'] ?>">
                                        <?=COption::GetOptionString($moduleId, 'button_text_buy')?>
                                    </button>
                                <?else:?>
                                    <button class="catalog-bottom__button button button_white"
                                            data-product="<?= $availableItemID ?>"
                                            data-fancybox=""
                                            data-src="#popup-notify"
                                    >
                                        <?=GetMessage('NOTIFY')?>
                                    </button>
                                <?endif?>
                                <?$frame->end()?>
                            </div>
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
                    'RETARGET_PRODUCT' => [
                        'id' => $arItem['ID'],
                        'group_id' => $arItem['IBLOCK_SECTION_ID'],
                        'price' => (int)$arItem['MIN_PRICE']['DISCOUNT_VALUE'],
                        'price_old' => (int)$arItem['MIN_PRICE']['VALUE']
                    ],
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
                ?>
                <script type="text/javascript">
                    var <? echo $strObName; ?> = new JCCatalogItem(<? echo CUtil::PhpToJSObject($arJSParams, false, true); ?>);
                </script>
            </li><!-- /.catalog-item -->
            <?endforeach?>
        </ul>
        <?//endif;?>
    </div>
</section>