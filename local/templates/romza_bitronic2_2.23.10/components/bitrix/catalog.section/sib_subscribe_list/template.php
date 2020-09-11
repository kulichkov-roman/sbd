<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);

use Bitrix\Main\Localization\Loc;

$bStores = $arParams["SHOW_AMOUNT_STORE"] == "Y" && Bitrix\Main\ModuleManager::isModuleInstalled("catalog");

foreach ($arParams['SUBSCRIBE_LIST'] as $obSubscribe):
	$arItem = $arResult['ITEMS'][(int)$obSubscribe->props['PRODUCT']['VALUE']];
	if (empty($arItem)) continue;

	$imgTitle = (
		!empty($arItem['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_TITLE'])
		? $arItem['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_TITLE']
		: $arItem['NAME']
	);
	$bShowStore = $bStores && !$arItem['bOffers'];
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
	?>
    <li class="table-list__item <?=$availableClass?>">
        <div class="col col_1">
            <a href="<?=$arItem['DETAIL_PAGE_URL']?>" class="product-img">
                <img itemprop="contentUrl" class="lazy" data-original="<?=$arItem['PICTURE_PRINT']['SRC']?>" data-original-jpg="<?=$arItem['PICTURE_PRINT']['SRC_JPG']?>" src="<?=ConsVar::showLoaderWithTemplatePath()?>" alt="<?=$arItem['PICTURE_PRINT']['ALT']?>" title="<?= $imgTitle ?>">
            </a>
            <a class="product-name" href="<?=$arItem['DETAIL_PAGE_URL']?>"><span><?=$arItem['NAME']?></span></a>
            <? if (!$availableOnRequest): ?>
                <div class="product-price">
                    <span class="product-price__title"><?= Loc::getMessage('BITRONIC2_SUBSCRIBE_PRICE') ?>:</span>
                    <span class="product-price__value">
                        <?= CRZBitronic2CatalogUtils::getElementPriceFormat($arItem['MIN_PRICE']['CURRENCY'], $arItem['MIN_PRICE']['DISCOUNT_VALUE'], $arItem['MIN_PRICE']['PRINT_DISCOUNT_VALUE']) ?>
                    </span>
                </div>
            <? endif ?>
        </div>
        <div class="col col_2">
            <div class="date"><?= $obSubscribe->fields['DATE_CREATE'] ?></div>
        </div>
        <? if (!empty($arParams['PROP_2']) && is_array($arParams['PROP_2'])): ?>
            <div class="col col_3">
                <? foreach ($arParams['PROP_2'] as $propCode):
                    if (empty($obSubscribe->props[$propCode])) continue;
                    if (empty($obSubscribe->props[$propCode]['VALUE'])) continue;
                    ?>
                    <div class="email-title"><?= $obSubscribe->props[$propCode]['NAME'] ?>:</div>
                    <div class="email-value"><?= $obSubscribe->props[$propCode]['DISPLAY_VALUE'] ?></div>
                <? endforeach ?>
            </div>
        <? endif ?>
        <div class="col col_4">
            <a href="#" data-placement="bottom" data-id="<?= $obSubscribe->fields['ID'] ?>" class="btn-delete">
                <div class="action-icon">
                    <i class="icon-personal-basket"></i>
                </div>
                <div class="action-value"><?= Loc::getMessage('BITRONIC2_SUBSCRIBE_DELETE') ?></div>
            </a>
        </div>
    </li>
<?endforeach?>