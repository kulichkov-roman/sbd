<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;

//no whitespace in this file!!!!!!
$this->setFrameMode(true);

// @var $moduleId
// @var $moduleCode
include $_SERVER["DOCUMENT_ROOT"].SITE_TEMPLATE_PATH.'/include/debug_info.php';

if (empty($arResult['ITEMS']) && empty($arResult['CUSTOM_ITEMS']))
	return;

$strElementEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT");
$strElementDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE");
$arElementDeleteParams = array("CONFIRM" => GetMessage('CT_BCS_TPL_ELEMENT_DELETE_CONFIRM'));

$bStores = $arParams["SHOW_AMOUNT_STORE"] == "Y" && Bitrix\Main\ModuleManager::isModuleInstalled("catalog");
$bOneClick = $arParams['DISPLAY_ONECLICK'];
$bHoverMode = $arParams['HOVER-MODE'] == 'detailed-expand';
$curPage = $arParams['BASKET_URL'].'?'.$arParams["ACTION_VARIABLE"].'=';
$arUrls = array("delete" => $curPage."delete&id=#ID#");
?>
<ul class="catalog__list catalog__list_1 js-slider-7 dots-1 arrows-2">
    <?foreach($arResult['CUSTOM_ITEMS'] as $customItem):?>
        <li class="catalog-item">
            <div class="catalog-item__main custom-item">
                <a class="catalog-image js-fancybox-accessory" title="<?=$customItem['NAME']?>" data-price="<?=$customItem['PRICE']?>"  data-custom-popup="access_custom" data-ajax-url="<?=$customItem['AJAX_HREF']?>" href="#popup-accessories">
                    <div class="catalog-image__fix">
                        <img alt="<?=$customItem['NAME']?>" class="placeholder" data-lazy="<?=$customItem['PIC']['WEBP']?>" data-lazy-jpg="<?=$customItem['PIC']['JPG']?>" src="<?=SITE_TEMPLATE_PATH . '/img/placetransparent.png'?>">
                    </div>
                </a>
                <div class="rbs-hydrogel-sticker"></div>
                <div class="catalog-item__content">
                    <div class="catalog-title">
                        <a href="#popup-accessories" data-custom-popup="access_custom" data-price="<?=$customItem['PRICE']?>" data-ajax-url="<?=$customItem['AJAX_HREF']?>" href="#popup-accessories"
                           class="catalog-title__link js-ellip-2 js-fancybox-accessory" title="<?=$customItem["NAME"]?>"
                        >
                            <span><?=$customItem['NAME']?></span>
                        </a>
                    </div>
                    <div class="catalog-bottom">
                        <div class="catalog-bottom__left">
                            <?if ($customItem['OLD_PRICE'] > 0):?>
                                <p class="old-price"><?=CRZBitronic2CatalogUtils::getElementPriceFormat('RUB', $customItem['OLD_PRICE'], $customItem['OLD_PRICE']);?></p>
                            <?endif?>
                            <p class="current-price in-ajax-popup">
                                <?=CRZBitronic2CatalogUtils::getElementPriceFormat('RUB', $customItem['PRICE'], $customItem['PRICE']);?>
                            </p>
                        </div>
                        <div class="catalog-bottom__right">
                            <button class="catalog-bottom__button button btn-action when-in-stock js-fancybox-accessory" data-price="<?=$customItem['PRICE']?>" data-custom-popup="access_custom" data-ajax-url="<?=$customItem['AJAX_HREF']?>" href="#popup-accessories">
                            </button>
                        </div>
                    </div>
                </div>
            </div><!-- /.catalog-item__main -->
        </li>
    <?endforeach?>

	<?foreach ($arResult["ITEMS"] as $k => $arItem):?>   
	<?
		$strMainID = $this->GetEditAreaId($arItem['ID']);
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

            'PRICE' => $strMainID.'_price',
            'DSC_PERC' => $strMainID.'_dsc_perc',
            'SECOND_DSC_PERC' => $strMainID.'_second_dsc_perc',
            'PROP_DIV' => $strMainID.'_sku_tree',
            'PROP' => $strMainID.'_prop_',
            'DISPLAY_PROP_DIV' => $strMainID.'_sku_prop',
            'BASKET_PROP_DIV' => $strMainID.'_basket_prop',
            'PRICE_ADDITIONAL' => $strMainID.'_price_additional',
        );
	    ?>
		<li class="catalog-item <?if ($arItem['IN_BASKET']):?>active<?endif?>" data-id="<?=$arItem['ID']?>">
			<div class="catalog-item__main">
				<div class="catalog-item__overlay">
					<a class="location__close button-close" data-action="delete"
                       data-id="<?=$arItem['IN_BASKET']?>" data-product="<?=$arItem['ID']?>"
                       href="<?=htmlspecialcharsbx(str_replace("#ID#", $arItem['IN_BASKET'], $arUrls["delete"]))?>"
                    >
                    </a>
					<?=GetMessage('BITRONIC2_ADDED_ITEM');?>
				</div>
                <a href="#popup-accessories" data-url="<?=$arItem['DETAIL_PAGE_URL']?>"
                   class="catalog-image js-fancybox-2 js-fancybox-accessory" title="<?=$arItem["NAME"]?>"
                >
                    <div class="catalog-image__fix">
                        <img alt="<?=$arItem['PICTURE_PRINT']['ALT']?>" class="placeholder" data-lazy="<?=$arItem['PICTURE_PRINT']['SRC']?>" data-lazy-jpg="<?=$arItem['PICTURE_PRINT']['SRC_JPG']?>" src="<?=SITE_TEMPLATE_PATH . '/img/placetransparent.png'?>"> 
                    </div>
                </a>
				<div class="catalog-item__content">
                    <div class="catalog-title">
                        <a href="#popup-accessories" data-url="<?=$arItem['DETAIL_PAGE_URL']?>"
                           class="catalog-title__link js-ellip-2 js-fancybox-2 js-fancybox-accessory" title="<?=$arItem["NAME"]?>"
                        >
                            <span><?=$arItem['NAME']?></span>
                        </a>
                    </div>
                    <div class="catalog-bottom">
                        <div class="catalog-bottom__left">
                            <?if ($arItem['MIN_PRICE']['DISCOUNT_DIFF'] > 0):?>
                                <p class="old-price"><?=CRZBitronic2CatalogUtils::getElementPriceFormat($arItem['MIN_PRICE']['CURRENCY'], $arItem['MIN_PRICE']['VALUE'], $arItem['MIN_PRICE']['PRINT_VALUE']);?></p>
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
                                <button 
                                    class="catalog-bottom__button button btn-action when-in-stock"
                                    id="<?= $arItemIDs['BUY_LINK'] ?>"
                                    data-product-id="<?= $arItem['ID'] ?>"
                                    data-group-id="<?= $arItem['IBLOCK_SECTION_ID'] ?>"
                                    data-price="<?=(int)$arItem['MIN_PRICE']['DISCOUNT_VALUE']?>"
                                    data-price-old="<?=(int)$arItem['MIN_PRICE']['VALUE']?>"
                                >
                                </button>
                            <?else:?>
                                <button class="catalog-bottom__button button button_white js-kvk-button"
                                        data-product="<?= $availableItemID ?>"
                                        data-placement="bottom"
                                        data-toggle="modal"
                                        data-target="#modal_subscribe_product"
                                >
                                    <?=GetMessage('NOTIFY')?>
                                </button>
                            <?endif?>
                        </div>
                    </div>
				</div>
			</div>
		</li>
	<?endforeach?>
	</ul>

