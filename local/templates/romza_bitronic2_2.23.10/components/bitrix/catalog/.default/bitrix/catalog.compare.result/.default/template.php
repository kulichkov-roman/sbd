<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
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

\Bitrix\Main\Localization\Loc::loadMessages(SITE_TEMPLATE_PATH . '/header.php');

// @var $moduleId
// @var $moduleCode
include $_SERVER["DOCUMENT_ROOT"].SITE_TEMPLATE_PATH.'/include/debug_info.php';
/*if ($arParams['INCLUDE_MOBILE_PAGE']){
    include 'mobile_page.php';
    return;
}*/
?>
<div class="search-box">
    <div class="category">
        <div class="accordion">
            <div class="show">
                <a class="button compare-switch<? echo (!$arResult["DIFFERENT"] ? ' active' : ''); ?>"
                        href="<? echo str_replace($arParams['ACTION_VARIABLE'], '', $arResult['COMPARE_URL_TEMPLATE']).'DIFFERENT=N'; ?>"
                >
                    <?=GetMessage("BITRONIC2_CATALOG_ALL_CHARACTERISTICS")?>
                </a>
                <a class="button button__diff compare-switch<? echo ($arResult["DIFFERENT"] ? ' active' : ''); ?>"
                        href="<? echo str_replace($arParams['ACTION_VARIABLE'], '', $arResult['COMPARE_URL_TEMPLATE']).'DIFFERENT=Y'; ?>"
                >
                    <?=GetMessage("BITRONIC2_CATALOG_ONLY_DIFFERENT")?>
                </a>
                <a href="<?= $arResult['COMPARE_URL_TEMPLATE'] . $arParams['ACTION_VARIABLE'] . '=' . 'DELETE_ALL_COMPARE_LIST'; ?>"
                   class="show__link remove_all_items"><?=GetMessage("CLEAR_LIST")?></a>
                <div class="show__desc"><?=GetMessage('BITRONIC2_CATALOG_COMPARE_NOTE')?></div>
            </div>
            <h4 class="show__title"><?=GetMessage('BITRONIC2_CATALOG_COMPARE_PARAMS')?></h4>
            <?if (!empty($arResult['PROPERTY_GROUPS'])):?>
                <?foreach ($arResult['PROPERTY_GROUPS'] as $groupIndex => $group):
                    if(count($group['PROPERTIES']) <= 0) continue;
                    ?>
                    <div class="accordion__item_compare accordion__item_compare-<?=$groupIndex?>" data-id=".item<?=$groupIndex?>">
                        <div class="accordion__heading">
                            <div class="accordion__head"><?=$group['GROUP_NAME'] ? : GetMessage('BITRONIC2_DEFAULT_GROUP')?></div>
                            <div class="accordion__arrow rotate"></div>
                        </div>
                        <div class="accordion__text" style="display: block;">
                            <ul class="compare-table compare-table_left-side js-compare-table_<?=$groupIndex?>">
                                <?if (!empty($group['PROPERTIES'])):?>
                                    <?foreach ($group['PROPERTIES'] as $index => $prop):?>
                                        <li class="compare-table__item <?if ($index % 2 == 0):?>compare-table__item_bg<?endif?>">
                                            <p class="compare-table__text"><?=$prop['NAME']?></p>
                                        </li>
                                    <?endforeach?>
                                <?endif?>
                            </ul>
                        </div>
                    </div>
                <?endforeach?>
            <?endif?>
        </div>
    </div>
    <div class="result">
        <div class="filters-toggle js-filters">Фильтры</div>
        <div class="tab-wrap">
            <div class="box-tab-cont">
                <div class="tab-cont">
                    <div class="catalog">
                        <?/*?>
                        <div class="compare-radio-mob">
                            <form>
                                <div class="box-check">
                                    <label class="box-check__label" for="all">
                                        <input id="all"  checked type="radio" name="name1" required>
                                        <span></span>
                                        <?=GetMessage("BITRONIC2_CATALOG_ALL_CHARACTERISTICS")?>
                                    </label>
                                    <label class="box-check__label" for="diff">
                                        <input id="diff"  type="radio" name="name1" required>
                                        <span></span>
                                        <?=GetMessage("BITRONIC2_CATALOG_DIFFERENT")?>
                                    </label>
                                </div>
                            </form>
                        </div>
                        <?*/?>
                        <ul class="catalog__list catalog__list_1 js-catalog__list">
                            <?foreach($arResult['ITEMS'] as &$arItem):
                            $strMainID = $this->GetEditAreaId('compare_'.$arItem['ID']);
                            $arItemIDs = array(
                                'ID' => $strMainID,
                                'PICT' => $strMainID.'_pict',
                                'SECOND_PICT' => $strMainID.'_secondpict',
                                'BUY_LINK' => $strMainID.'_buy_link',
                                'BUY_ONECLICK' => $strMainID.'_buy_oneclick',
                                'BASKET_ACTIONS' => $strMainID.'_basket_actions',
                                'NOT_AVAILABLE_MESS' => $strMainID.'_not_avail',
                                'SUBSCRIBE_LINK' => $strMainID.'_subscribe',
                                'COMPARE_LINK' => $strMainID.'_compare_link',
                                'FAVORITE_LINK' => $strMainID.'_favorite_link',
                                'REQUEST_LINK' => $strMainID.'_request_link',
                                'OLD_PRICE' => $strMainID.'_old_price',
                                'PRICE' => $strMainID.'_price',
                                'DSC_PERC' => $strMainID.'_dsc_perc',
                                'SECOND_DSC_PERC' => $strMainID.'_second_dsc_perc',
                                'BASKET_PROP_DIV' => $strMainID.'_basket_prop',
                                'AVAILABILITY' => $strMainID . '_availability',
                            );
                            $strObName = 'ob'.preg_replace("/[^a-zA-Z0-9_]/", "x", $strMainID);

                            $productTitle = (
                                !empty($arItem['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'])
                                    ? $arItem['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']
                                    : $arItem['NAME']
                            );

                            $imgTitle = (
                                !empty($arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"])
                                    ? $arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"]
                                    : $arItem['NAME']
                            );
                            $arItem['strMainID'] = $strMainID;
                            $arItem['arItemIDs'] = $arItemIDs;
                            $arItem['strObName'] = $strObName;

                            $bEmptyProductProperties = empty($arItem['PRODUCT_PROPERTIES']);
                            $bBuyProps = ('Y' == $arParams['ADD_PROPERTIES_TO_BASKET'] && !$bEmptyProductProperties);
                            ?>
                                <li class="catalog-item" id="<?= $arItemIDs['ID'] ?>">
                                    <div class="catalog-item__main">
                                        <a class="catalog-item_remove" href="<?=$arItem['~DELETE_URL']?>" data-id="<?=$arItem['ID']?>">+</a>
                                        <a class="catalog-image" title="<?=$imgTitle?>" href="<?=$arItem['DETAIL_PAGE_URL']?>">
                                            <div class="catalog-image__fix">
                                                <img alt="<?=$arItem['PICTURE_PRINT']['ALT']?>" class="lazy placeholder" src="<?=SITE_TEMPLATE_PATH?>/img/placetransparent.png" data-original="<?=$arItem['PICTURE_PRINT']['SRC']?>" data-original-jpg="<?=$arItem['PICTURE_PRINT']['SRC']?>">
                                            </div>
                                        </a>
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
                                            <h4 class="catalog-title">
                                                <a class="catalog-title__link js-ellip-2" href="<?=$arItem['DETAIL_PAGE_URL']?>">
                                                    <span><?=$productTitle?></span>
                                                </a>
                                            </h4>
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
                                                <div class="catalog-bottom__left">
                                                    <p class="current-price" id="<?= $arItemIDs['PRICE'] ?>">
                                                        <?= CRZBitronic2CatalogUtils::getElementPriceFormat($arItem['MIN_PRICE']['CURRENCY'], $arItem['MIN_PRICE']['DISCOUNT_VALUE'], $arItem['MIN_PRICE']['PRINT_DISCOUNT_VALUE']); ?>
                                                        <span class="hidden"
                                                              itemprop="lowPrice"><?= $arItem['MIN_PRICE']['DISCOUNT_VALUE'] ?: 0 ?></span>
                                                        <span class="hidden" itemprop="priceCurrency"><?= $arItem['MIN_PRICE']['CURRENCY'] ?></span>
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
                                    <?if (!empty($arResult['PROPERTY_GROUPS'])):?>
                                        <?foreach ($arResult['PROPERTY_GROUPS'] as $groupIndex => $group):?>
                                            <div class="catalog-item__compare item<?=$groupIndex?> catalog-item__compare_slide">
                                                <ul class="compare-table compare__table_info js-compare-table_<?=$groupIndex?>">
                                                    <?if (!empty($group['PROPERTIES'])):?>
                                                        <?foreach ($group['PROPERTIES'] as $index => $prop):?>
                                                            <li class="compare-table__item <?if ($index % 2 == 0):?>compare-table__item_bg<?endif?>">
                                                                <p class="compare-table__text">
                                                                    <?=is_array($arItem["DISPLAY_PROPERTIES"][$prop['CODE']]["DISPLAY_VALUE"]) ?
                                                                        implode("/ ", $arItem["DISPLAY_PROPERTIES"][$prop['CODE']]["DISPLAY_VALUE"]) :
                                                                        $arItem["DISPLAY_PROPERTIES"][$prop['CODE']]["DISPLAY_VALUE"]?>
                                                                </p>
                                                            </li>
                                                        <?endforeach?>
                                                    <?endif?>
                                                </ul>
                                            </div>
                                        <?endforeach?>
                                    <?endif?>
                                </li>
                            <?endforeach; unset($arItem);?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
	$(window).on('b2ready', function(){
	<?if(isset($_GET['action']) && !empty($_GET['action']) && ($_GET['action'] == 'DELETE_FROM_COMPARE_RESULT' || $_GET['action'] == 'DELETE_FROM_COMPARE_LIST')):?>
		// refresh small compare list (in header)
		RZB2.ajax.Compare.Refresh();
	<?endif?>
	// var CatalogCompareObj = new BX.Iblock.Catalog.CompareClass("compare-table");
	<?foreach ($arResult['ITEMS'] as $arItem) {
		$strMainID = $arItem['strMainID'];
		$arItemIDs = $arItem['arItemIDs'];
		$strObName = $arItem['strObName'];
		include 'js_params.php';
	}?>
	});
</script>