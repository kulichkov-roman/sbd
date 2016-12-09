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
use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;

$this->setFrameMode(true);
$compositeLoader = CRZBitronic2Composite::insertCompositLoader();
$templateLibrary = array();
$currencyList = '';
if (!empty($arResult['CURRENCIES']))
{
	$templateLibrary[] = 'currency';
	$currencyList = CUtil::PhpToJSObject($arResult['CURRENCIES'], false, true, true);
}
$templateData = array(
	'TEMPLATE_LIBRARY' => $templateLibrary,
	'CURRENCIES' => $currencyList
);

if ($arResult['CATALOG'] && isset($arResult['OFFERS']) && !empty($arResult['OFFERS'])) {
	$templateData['OFFERS_KEYS'] = array();
	foreach ($arResult['OFFERS'] as $keyOffer => $arOffer) {
		$templateData['OFFERS_KEYS'][$arOffer['ID']] = $keyOffer;
	}
}

$arJsCache = CRZBitronic2CatalogUtils::getJSCache($component);
$_SESSION['RZ_DETAIL_JS_FILE'] = $arJsCache['file'];
$templateData['jsFile'] = $arJsCache['path'].'/'.$arJsCache['idJS'];
$templateData['jsFullPath'] = $arJsCache['path-full'];

$strMainID = $this->GetEditAreaId($arResult['ID']);
$arItemIDs = array(
	'ID' => $strMainID,
	'PICT' => $strMainID.'_pict',
	'PICT_MODAL' => $strMainID.'_pict_modal',
	'PICT_FLY' => $strMainID.'_pict_fly',
	'DISCOUNT_PICT_ID' => $strMainID.'_dsc_pict',
	'STICKER_ID' => $strMainID.'_sticker',
	'BIG_SLIDER_ID' => $strMainID.'_big_slider',
	'BIG_IMG_CONT_ID' => $strMainID.'_bigimg_cont',
	'SLIDER_CONT_ID' => $strMainID.'_slider_cont',
	'SLIDER_LIST' => $strMainID.'_slider_list',
	'SLIDER_LEFT' => $strMainID.'_slider_left',
	'SLIDER_RIGHT' => $strMainID.'_slider_right',
	'OLD_PRICE' => $strMainID.'_old_price',
	'PRICE' => $strMainID.'_price',
	'DSC_PERC' => $strMainID.'_dsc_perc',
	'DISCOUNT_PRICE' => $strMainID.'_price_discount',
	'SLIDER_CONT_OF_ID' => $strMainID.'_slider_cont_',
	'SLIDER_MODAL_CONT_OF_ID' => $strMainID.'_slider_modal_cont_',
	'SLIDER_LIST_OF_ID' => $strMainID.'_slider_list_',
	'SLIDER_LEFT_OF_ID' => $strMainID.'_slider_left_',
	'SLIDER_RIGHT_OF_ID' => $strMainID.'_slider_right_',
	'QUANTITY' => $strMainID.'_quantity',
	'QUANTITY_DOWN' => $strMainID.'_quant_down',
	'QUANTITY_UP' => $strMainID.'_quant_up',
	'QUANTITY_MEASURE' => $strMainID.'_quant_measure',
	'QUANTITY_LIMIT' => $strMainID.'_quant_limit',
	'BASKET_ACTIONS' => $strMainID.'_basket_actions',
	'AVAILABLE_INFO' => $strMainID.'_avail_info',
	'BUY_LINK' => $strMainID.'_buy_link',
	'BUY_ONECLICK' => $strMainID.'_buy_oneclick',
	'ADD_BASKET_LINK' => $strMainID.'_add_basket_link',
	'COMPARE_LINK' => $strMainID.'_compare_link',
	'FAVORITE_LINK' => $strMainID.'_favorite_link',
	'REQUEST_LINK' => $strMainID.'_request_link',
	'PROP' => $strMainID.'_prop_',
	'PROP_DIV' => $strMainID.'_skudiv',
	'DISPLAY_PROP_DIV' => $strMainID.'_sku_prop',
	'OFFER_GROUP' => $strMainID.'_set_group_',
	'BASKET_PROP_DIV' => $strMainID.'_basket_prop',
	'ARTICUL' => $strMainID.'_articul',
	'PRICE_ADDITIONAL' => $strMainID.'_price_additional',
	'PRICE_ACTIONS' => $strMainID.'_price_actions',

	//SKU
	'SKU_TABLE' => $strMainID.'_sku_table',
);
$arItemCLASSes = array(
	'LINK' => $strMainID.'_link',
);
$strObName = 'ob'.preg_replace("/[^a-zA-Z0-9_]/", "x", $strMainID);
$templateData['strObName'] = $strObName;

$strTitle = (
	isset($arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_TITLE"]) && $arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_TITLE"] != ''
	? $arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_TITLE"]
	: $arResult['NAME']
);
$strAlt = (
	isset($arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_ALT"]) && $arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_ALT"] != ''
	? $arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_ALT"]
	: $arResult['NAME']
);

$bUseBrands = ('Y' == $arParams['BRAND_USE']);

if (isset($arResult['OFFERS']) && !empty($arResult['OFFERS']))
{
	$arOffer &= $arResult['OFFERS'][$arResult['OFFERS_SELECTED']];
	$canBuy = $arOffer['CAN_BUY'];
	unset($arOffer);
}
else
{
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

$availableClass = (
	!$canBuy && !$availableOnRequest
	? 'out-of-stock'
	: (
		$arResult['FOR_ORDER'] || $availableOnRequest
		? 'available-for-order'
		: ''
	)
);

$bDiscountShow = (0 < $arResult['MIN_PRICE']['DISCOUNT_DIFF'] && $arParams['SHOW_OLD_PRICE'] == 'Y');
$bEmptyProductProperties = empty($arResult['PRODUCT_PROPERTIES']);
$bStores = $arParams["USE_STORE"] == "Y" && Bitrix\Main\ModuleManager::isModuleInstalled("catalog");
$bShowStore = $bStores && !$arResult['bSkuSimple'];
$arParams['MANUAL_PROP'] = empty($arParams['MANUAL_PROP']) ? 'MANUAL' : $arParams['MANUAL_PROP'];
$bShowDocs = is_array($arResult["PROPERTIES"][$arParams['MANUAL_PROP']]['VALUE']);
$bShowVideo = is_array($arResult["PROPERTIES"]['VIDEO']['VALUE']);
$bShowOneClick = Loader::includeModule('yenisite.oneclick') && (!$arResult['bOffers'] || $arResult['bSkuExt']);
$arResult['bTabs'] = $arResult['bTechTab']
                  || $arParams['USE_REVIEW'] == 'Y'
                  || $bShowVideo
                  || $bShowDocs;
?>
<? if ($arParams['QUICK_VIEW']): ?>
<div id="<?=$arItemIDs['ID']?>">
	<h2><?=$productTitle?></h2>
<? else: ?>
<main class="container product-page" itemscope itemtype="http://schema.org/Product" data-page="product-page" id="<? echo $arItemIDs['ID']; ?>">
	<h1 itemprop="name"><?=$productTitle?></h1>
<? endif ?>

	<div class="short-info actions">
		<span class="info price-update-date"><?=GetMessage('BITRONIC2_UPDATE_DATE')?> <?=$arResult["DISPLAY_UPDATE_DATE"]?></span>
		<span class="info art<?=(empty($articul)?' hidden':'')?>" id="<?=$arItemIDs['ARTICUL']?>"><?=$arResult['PROPERTIES'][$arParams['ARTICUL_PROP']]['NAME']?>: <strong<?if(!empty($articul)):?> itemprop="productID"<?endif?>><?=$articul?></strong></span>
		<?$id = 'bxdinamic_BITRONIC2_detail_vote_'.$arResult['ID'];
			?><div id="<?=$id?>" class="inline"><?
			$frame = $this->createFrame($id, false)->begin($compositeLoader);?>
				<?$APPLICATION->IncludeComponent("bitrix:iblock.vote", "detail", array(
				"IBLOCK_TYPE" => $arResult['IBLOCK_TYPE_ID'],
				"IBLOCK_ID" => $arResult['IBLOCK_ID'],
				"ELEMENT_ID" => $arResult['ID'],
				"CACHE_TYPE" => $arParams["CACHE_TYPE"],
				"CACHE_TIME" => $arParams["CACHE_TIME"],
				"MAX_VOTE" => "5",
				"VOTE_NAMES" => array("1", "2", "3", "4", "5"),
				"SET_STATUS_404" => "N",
				),
				$component, array("HIDE_ICONS"=>"Y")
			);?>
			<?$frame->end();?>
			</div>
		<? if($arParams['USE_REVIEW'] == 'Y' && !$arParams['QUICK_VIEW']): ?>

		<a href="#form_comment" class="action comment with-icon write-review_top">
			<i class="flaticon-speech90"></i>
			<span class="text"><?=GetMessage('BITRONIC2_WRITE_REVIEW')?></span>
		</a>
		<? endif;

		if ($arParams['DISPLAY_FAVORITE'] && !$arResult['bOffers']):?>
			<button
				type="button"
				class="action favorite with-icon toggleable"
				id="<?=$arItemIDs['FAVORITE_LINK']?>"
				data-favorite-id="<?=$arResult['ID']?>"
				>
				<i class="flaticon-heart3"></i>
				<span class="text when-not-toggled"><?=GetMessage('BITRONIC2_ADD_FAVORITE')?></span>
				<span class="text when-toggled"><?=GetMessage('BITRONIC2_ADDED_FAVORITE')?></span>
			</button>
		<?endif?>

		<?if ($arParams['DISPLAY_COMPARE']):?>
			<button
				type="button"
				class="action compare with-icon toggleable"
				id="<?=$arItemIDs['COMPARE_LINK']?>"
				data-compare-id="<?=$arResult['ID']?>"
				>
				<i class="flaticon-balance3"></i>
				<span class="text when-not-toggled"><?=GetMessage('BITRONIC2_ADD_COMPARE')?></span>
				<span class="text when-toggled"><?=GetMessage('BITRONIC2_ADDED_COMPARE')?></span>
			</button>
		<?endif?>


		<?
		/* TODO
		include '_/buttons/action_to-wait.html';
		*/
		?>
	</div><!-- /.short-info.actions -->

	<div class="row">
		<div class="col-xs-12 product-main">
			<div class="product-photos" id="photo-block">
				<?$APPLICATION->IncludeComponent("yenisite:stickers", "section", array(
					"ELEMENT" => $arResult,
					"STICKER_NEW" => $arParams['STICKER_NEW'],
					"STICKER_HIT" => $arParams['STICKER_HIT'],
					),
					$component
				);?>
				<?if (is_array($arResult['BRAND_LOGO'])):?>
				<a href="<?=$arResult['BRAND_LOGO']['URL']?>" class="brand">
					<img src="<?=$arResult['BRAND_LOGO']['IMG']['src']?>" alt="<?=$arResult['BRAND_LOGO']['ALT']?>">
				</a>
				<?endif?>
				<?
				/* TODO
				<div class="info-popups">
					................
				</div>
				*/
				?>
				<?if(!$arResult['bSkuExt']):?>
					<div id="<? echo $arItemIDs['SLIDER_CONT_ID']; ?>" style="height:100%">
						<div class="product-photo">
							<img
								id="<?=$arItemIDs['PICT']?>"
								src="<?=CResizer2Resize::ResizeGD2($arResult['MORE_PHOTO'][0]['SRC'], $arParams["RESIZER_SETS"]['RESIZER_DETAIL_SMALL'])?>"
								data-big-src="<?=CResizer2Resize::ResizeGD2($arResult['MORE_PHOTO'][0]['SRC'], $arParams["RESIZER_SETS"]['RESIZER_DETAIL_BIG'])?>"
								alt="<?=$strAlt?>"
								itemprop="image">
						</div>
						<div class="thumbnails-wrap active">
							<?if($arResult['MORE_PHOTO_COUNT'] > 1):?>
								<button type="button" class="thumb-control prev btn-silver">
									<i class="flaticon-key22 arrow-up"></i>
									<i class="flaticon-arrow133 arrow-left"></i>
								</button>
								<button type="button" class="thumb-control next btn-silver">
									<i class="flaticon-arrow128 arrow-down"></i>
									<i class="flaticon-right20 arrow-right"></i>
								</button>
								<div class="thumbnails-frame active">
									<div class="thumbnails-slidee">
									<?foreach($arResult['MORE_PHOTO'] as $arPhoto):?>
										<div class="thumb">
											<img
												src="<?=CResizer2Resize::ResizeGD2($arPhoto['SRC'], $arParams["RESIZER_SETS"]['RESIZER_DETAIL_ICON'])?>"
												alt="<?=$strAlt?>"
												data-med-src="<?=CResizer2Resize::ResizeGD2($arPhoto['SRC'], $arParams["RESIZER_SETS"]['RESIZER_DETAIL_SMALL'])?>"
												data-big-src="<?=CResizer2Resize::ResizeGD2($arPhoto['SRC'], $arParams["RESIZER_SETS"]['RESIZER_DETAIL_BIG'])?>"
											>
										</div>
									<?endforeach?>
									</div><!-- .thumbnails-slidee -->
								</div><!-- .thumbnails-frame -->
							<?endif?>
						</div><!-- /.thumbnails -->
					</div>
				<?else:?>
					<div class="offer-slider">
						<div class="product-photo">
							<img
								id="<?=$arItemIDs['PICT']?>"
								src="<?=CResizer2Resize::ResizeGD2($arResult['OFFERS'][$arResult['OFFERS_SELECTED']]['MORE_PHOTO'][0]['SRC'], $arParams["RESIZER_SETS"]['RESIZER_DETAIL_SMALL'])?>"
								data-big-src="<?=CResizer2Resize::ResizeGD2($arResult['OFFERS'][$arResult['OFFERS_SELECTED']]['MORE_PHOTO'][0]['SRC'], $arParams["RESIZER_SETS"]['RESIZER_DETAIL_BIG'])?>"
								alt="<?=$strAlt?>"
								itemprop="image">
						</div>
						<?foreach($arResult['OFFERS'] as $arOffer):
							if($arOffer['MORE_PHOTO_COUNT'] > 1):?>
							<div class="thumbnails-wrap" id="<? echo $arItemIDs['SLIDER_CONT_OF_ID'].$arOffer['ID']; ?>" style="display:none">
							<button type="button" class="thumb-control prev btn-silver">
									<i class="flaticon-key22 arrow-up"></i>
									<i class="flaticon-arrow133 arrow-left"></i>
								</button>
								<button type="button" class="thumb-control next btn-silver">
									<i class="flaticon-arrow128 arrow-down"></i>
									<i class="flaticon-right20 arrow-right"></i>
								</button>
								<div class="thumbnails-frame">
									<div class="thumbnails-slidee">
									<?foreach($arOffer['MORE_PHOTO'] as $arPhoto):?>
										<div class="thumb">
											<img
												src="<?=CResizer2Resize::ResizeGD2($arPhoto['SRC'], $arParams["RESIZER_SETS"]['RESIZER_DETAIL_ICON'])?>"
												data-src="<?=CResizer2Resize::ResizeGD2($arPhoto['SRC'], $arParams["RESIZER_SETS"]['RESIZER_DETAIL_ICON'])?>"
												alt="<?=$strAlt?>"
												data-med-src="<?=CResizer2Resize::ResizeGD2($arPhoto['SRC'], $arParams["RESIZER_SETS"]['RESIZER_DETAIL_SMALL'])?>"
												data-big-src="<?=CResizer2Resize::ResizeGD2($arPhoto['SRC'], $arParams["RESIZER_SETS"]['RESIZER_DETAIL_BIG'])?>"
											>
										</div>
									<?endforeach?>
									</div><!-- .thumbnails-slidee -->
								</div><!-- .thumbnails-frame -->
							</div><!-- /.thumbnails -->
							<?endif;
						endforeach?>
					</div>
				<?endif?>
				<?
				/* TODO
				<button type="button" class="btn-main view3d">
					................
				</button>
				*/
				?>
<? if ($arParams['QUICK_VIEW']): ?>
				<a href="<?=$arResult['DETAIL_PAGE_URL']?>" class="link go2detailed <?=$arItemCLASSes['LINK']?>" title="<?=GetMessage('BITRONIC2_GO_TO_DETAIL_TITLE')?>">
					<span class="text"><?=GetMessage('BITRONIC2_GO_TO_DETAIL')?></span>
				</a>
<? else: ?>
				<button type="button" class="action zoom">
					<i class="flaticon-zoom62"></i>
					<span class="text"><?=GetMessage('BITRONIC2_CATALOG_INCREASE')?></span>
				</button>
<? endif ?>

			</div><!-- /.product-photos -->
			<?
			/* TODO
			if (!$arParams['QUICK_VIEW']) include '_/elements/short-info.html';
			*/
			?>
			<div class="buy-block-origin">
				<!-- to switch between "in-stock" and "out-of-stock" modes, add or remove class
				 "out-of-stock" on this wrap -->
				<div class="buy-block-wrap">
					<div class="buy-block-main">
						<div class="buy-block-content">
							<div class="product-name" itemprop="name"><?=$productTitle?></div>
							<div class="product-main-photo">
								<img id="<?=$arItemIDs['PICT_FLY']?>" src="<?=CResizer2Resize::ResizeGD2($arResult['MORE_PHOTO'][0]['SRC'], $arParams["RESIZER_SETS"]['RESIZER_DETAIL_FLY_BLOCK'])?>" alt="<?=$strAlt?>" >
							</div>
							<div class="price-wrap<?=(empty($availableOnRequest) ? '' : ' hide')?>" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
								<div class="price-values">
									<span class="text"><?
										echo GetMessage('BITRONIC2_PRICE');
										?><span class="price-old" id="<?=$arItemIDs['OLD_PRICE']?>"><?
										$frame = $this->createFrame($arItemIDs['OLD_PRICE'], false)->begin('');
											if($bDiscountShow):?><?=CRZBitronic2CatalogUtils::getElementPriceFormat($arResult['MIN_PRICE']['CURRENCY'],$arResult['MIN_PRICE']['VALUE'],$arResult['MIN_PRICE']['PRINT_VALUE'])?><?endif?><?
										$frame->end();
										?></span><?
									?></span>
									<div class="price" id="<? echo $arItemIDs['PRICE']; ?>">
<? $frame = $this->createFrame($arItemIDs['PRICE'], false)->begin($compositeLoader) ?>
										<meta itemprop="price" content="<?=$arResult['MIN_PRICE']['DISCOUNT_VALUE']?>">
										<meta itemprop="priceCurrency" content="<?=$arResult['MIN_PRICE']['CURRENCY']?>">
										<?=($arResult['bOffers'] && $arResult['bSkuSimple']) ? GetMessage('BITRONIC2_OFFERS_FROM') : ''?>

										<?=CRZBitronic2CatalogUtils::getElementPriceFormat($arResult['MIN_PRICE']['CURRENCY'],$arResult['MIN_PRICE']['DISCOUNT_VALUE'],$arResult['MIN_PRICE']['PRINT_DISCOUNT_VALUE'])?>

<? $frame->end() ?>
									</div>
									<div id="<?= $arItemIDs['PRICE_ADDITIONAL'] ?>" class="additional-price-container">
										<? $frame = $this->createFrame($arItemIDs['PRICE_ADDITIONAL'], false)->begin(CRZBitronic2Composite::insertCompositLoader()) ?>
										<? foreach ($arResult['PRICES'] as $priceCode => $arPrice): ?>
											<? if ($arPrice['ID'] != $arResult['MIN_PRICE']['ID']): ?>
												<div class="additional-price-type">
													<span class="price-desc"><?= $arResult['PRICES_LANG'][$priceCode]['TITLE'] ?>:</span>
													<span class="price"><?
														echo CRZBitronic2CatalogUtils::getElementPriceFormat(
															$arPrice['CURRENCY'],
															$arPrice['DISCOUNT_VALUE'],
															$arPrice['PRINT_DISCOUNT_VALUE']
														);
													?></span>
												</div>
											<? endif ?>
										<? endforeach ?>
										<? $frame->end() ?>
									</div>
<?
$frame = $this->createFrame()->begin('');
if (is_array($arResult['PROPERTIES']['SERVICE'])
&& !empty($arResult['PROPERTIES']['SERVICE']['VALUE'])
&& !$arParams['QUICK_VIEW']):
?>
									<div class="additionals-price">
										<span class="text"><?=GetMessage('BITRONIC2_ADDITIONALS_PRICE')?></span>
										<div class="price additional">
											<?=CRZBitronic2CatalogUtils::getElementPriceFormat($arResult['MIN_PRICE']['CURRENCY'],0,'0')?>
										</div>
									</div>
<? endif;
$frame->end();
?>
								</div>
								<div class="price-actions" id="<?= $arItemIDs['PRICE_ACTIONS'] ?>">
									<? if (\Bitrix\Main\Loader::IncludeModule("yenisite.feedback")): ?>
										<? $frame = $this->createFrame($arItemIDs['PRICE_ACTIONS'], false)->begin($compositeLoader) ?>
										<? if ($arParams['PRICE_LOWER'] != 'N'): ?>
											<button type="button" class="action inform-when-price-drops" data-toggle="modal"
													id="button_price_drops"
													data-target="#modal_inform-when-price-drops" data-product="<?= $arResult['ID'] ?>"
													data-price="<?= $arResult['MIN_PRICE']['DISCOUNT_VALUE'] ?>"
													data-price_type="<?= $arResult['MIN_PRICE']['PRICE_ID'] ?>"
													data-currency="<?= $arResult['MIN_PRICE']['CURRENCY'] ?>"
												>
												<i class="flaticon-outlined3"></i>
												<span class="text"><?= GetMessage("RZ_SOOBSHIT_O_SNIZHENII_TCENI") ?></span>
											</button>
										<? endif ?>
										<? if ($arParams['FOUND_CHEAP'] != 'N'): ?>
											<button type="button" class="action cry-for-price" data-toggle="modal"
													data-product="<?= $arResult['ID'] ?>"
													data-price="<?= $arResult['MIN_PRICE']['DISCOUNT_VALUE'] ?>"
													data-price_type="<?= $arResult['MIN_PRICE']['PRICE_ID'] ?>"
													data-currency="<?= $arResult['MIN_PRICE']['CURRENCY'] ?>"
													data-target="#modal_cry-for-price">
												<i class="flaticon-info13"></i>
												<span class="text"><?= GetMessage("RZ_POZHALOVATSYA_NA_TCENU") ?></span>
											</button>
										<? endif ?>
										<? $frame->end(); ?>
									<? endif ?>
								</div>
							</div><!-- .price-wrap -->
							<form action="#" method="post" class="product-options" id="<? echo $arItemIDs['PROP_DIV']; ?>">
								<?if($arResult['bSkuExt']){ include 'sku_extended.php'; }?>
								<?if(!$arResult['bSkuSimple']):?>
									<div class="quantity-counter">
										<?
										$availableID = &$arItemIDs['AVAILABLE_INFO'];
										$availableFrame = true;
										$availableItemID = &$arResult['ID'];
										$availableParamsName = 'arParams';
										$availableStoresPostfix = 'detail';
										$availableSubscribe = $arResult['CATALOG_SUBSCRIBE'];
										$bShowEveryStatus = ($arResult['bOffers'] && $arResult['bSkuExt']);
										include $_SERVER['DOCUMENT_ROOT'] . SITE_TEMPLATE_PATH . '/include/availability_info.php';

										if (empty($availableOnRequest)):
										?>
										<div class="inner-quan-wrap">
											<span data-tooltip
											      data-placement="right"
											      title="<?=$arResult['CATALOG_MEASURE_NAME']?>">
												<!-- parent must have class .quantity-counter! -->
												<button type="button" class="btn-silver quantity-change decrease" id="<? echo $arItemIDs['QUANTITY_DOWN']; ?>"><span class="minus">&ndash;</span></button>
												<input type="text" class="quantity-input textinput" id="<? echo $arItemIDs['QUANTITY']; ?>" value="<?=$arResult['CATALOG_MEASURE_RATIO']?>">
												<button type="button" class="btn-silver quantity-change increase" id="<? echo $arItemIDs['QUANTITY_UP']; ?>"><span class="plus">+</span></button>
											</span>
										</div><?
										endif ?>

									</div>
								<?endif?>
							</form><!-- /.product-options -->
							<div class="buy-buttons-wrap<?if($availableOnRequest && (!$arResult['bOffers'] || $arResult['bSkuExt'])):?> on-request<?endif?>"  id="<? echo $arItemIDs['BASKET_ACTIONS']; ?>">
<? if($arParams['QUICK_VIEW']
   && $arResult['bOffers']
   && !$arResult['bSkuExt']): ?>
								<a href="<?=$arResult['DETAIL_PAGE_URL']?>" class="btn-big buy btn-main <?=$arItemCLASSes['LINK']?>">
									<span class="text"><?=GetMessage('BITRONIC2_GO_TO_DETAIL_OFFERS')?></span>
								</a>
<? else: ?>
		<button type="button" class="btn-big buy on-request btn-main" id="<?= $arItemIDs['REQUEST_LINK']; ?>" data-toggle="modal" data-target="#modal_contact_product"
				data-product-id="<?= $arResult['ID'] ?>"<?= ($arResult['bOffers'] && $arResult['bSkuExt'] ? ' data-offer-id="' . $arResult['OFFERS'][$arResult['OFFERS_SELECTED']]['ID'] . '"' : '') ?>
				data-measure-name="<?= ($arResult['bOffers'] && $arResult['bSkuExt'] ? $arResult['OFFERS'][$arResult['OFFERS_SELECTED']]['CATALOG_MEASURE_NAME'] : $arResult['CATALOG_MEASURE_NAME']) ?>">
			<i class="flaticon-speech90"></i>
			<span class="text"><?= GetMessage('BITRONIC2_PRODUCT_REQUEST') ?></span>
		</button>
		<button type="button" class="btn-big buy btn-main <?= ($canBuy || !empty($arResult['OFFERS']) ? '' : ' action disabled') ?>"
				id="<?= $arItemIDs['BUY_LINK']; ?>"
				data-product-id="<?= $arResult['ID'] ?>"<?= ($arResult['bOffers'] && $arResult['bSkuExt'] ? ' data-offer-id="' . $arResult['OFFERS'][$arResult['OFFERS_SELECTED']]['ID'] . '"' : '') ?>>
			<i class="flaticon-shopping109"></i>
			<span class="text"><?= GetMessage('BITRONIC2_ADD_BASKET') ?></span>
			<span class="text in-cart"><?= GetMessage('BITRONIC2_PRODUCT_IN_CART') ?></span>
		</button>
<? endif ?>
								<?if($bShowOneClick && !$availableOnRequest):?>
									<div class="one-click-wrap">
										<span class="text"><?=GetMessage('BITRONIC2_OR')?></span>
										<button id="<?= $arItemIDs['BUY_ONECLICK'] ?>" type="button"
												class="action one-click-buy <?= ($canBuy || !empty($arResult['OFFERS']) ? '' : ' disabled') ?>"
												data-toggle="modal" data-target="#modal_quick-buy" data-id="<?= $arResult['ID'] ?>"
												data-props="<?= \Yenisite\Core\Tools::GetEncodedArParams($arParams['OFFER_TREE_PROPS']) ?>">
											<i class="flaticon-shopping220"></i>
											<span class="text"><?=GetMessage('BITRONIC2_ONECLICK')?></span>
										</button>
									</div>
								<?endif?>
								<?
								/* TODO
								<button type="button" class="btn-big to-waitlist btn-silver">
									................
								</button>
								*/
								?>
							</div>
							<?
							/* TODO
							<div class="availability">
								................
							</div><!-- /.availability -->
							*/
							?>
						</div><!-- /.buy-block-content -->
<?
if ($arParams['QUICK_VIEW']) {
	echo '
							</div><!-- /.buy-block-main -->
						</div><!-- .buy-block-wrap -->
					</div><!-- .buy-block-origin -->
				</div><!-- /.col-xs-12 -->
			</div><!-- /.row -->';?>
			<?if($arParams['QUICK_SHOW_CHARS'] == 'Y'):?>
				<div class="row characteristics">
					<div class="col-xs-12">
						<? include 'characteristics.php' ?>
					</div>
				</div>
			<?endif ?>
		<?
echo	'</div><!-- /#'.$arItemIDs['ID'].' -->';

	include 'js_params.php';
	return;
}
?>
						<div class="buy-block-footer">
							<?if(CModule::IncludeModule('edost.catalogdelivery') && ($canBuy || $arResult['bOffers'])):?>
							<button type="button" class="action calc-delivery"
								<?//TODO data-toggle="modal" data-target="#modal_calc-delivery"
								?>data-id="<?=(isset($arResult['OFFERS'][0]['ID']) ? $arResult['OFFERS'][0]['ID'] : $arResult['ID'])?>"
								data-name="<?=str_replace(array('"', "'"), '&quot;', $arResult['NAME'])?>"
								>
								<i class="flaticon-calculator2"></i>
								<span class="text hidden-sm"><?=GetMessage('BITRONIC2_CALC_DELIVERY')?></span>
								<span class="text visible-sm-inline"><?=GetMessage('BITRONIC2_DELIVERY')?></span>
							</button>
							<?endif?>
							<?
							/* TODO
							<button type="button" class="action use-credit">
								................
							</button>
							*/
							?>
						</div>
					</div><!-- /.buy-block-main -->
<?
$frame = $this->createFrame()->begin('');

if (is_array($arResult['PROPERTIES']['SERVICE']) && !empty($arResult['PROPERTIES']['SERVICE']['VALUE']) && ($canBuy || $arResult['bOffers'])):
	global $arrServiceFilter;
	$arrServiceFilter = array('ID' => $arResult['PROPERTIES']['SERVICE']['VALUE']);
?>
					<?$APPLICATION->IncludeComponent('bitrix:catalog.section', 'services',
						array(
							"SHOW_ALL_WO_SECTION" => "Y",
							"FILTER_NAME" => 'arrServiceFilter',
							"PAGE_ELEMENT_COUNT" => 0,
							"IBLOCK_TYPE" => 'REFERENCES',
							"IBLOCK_ID" => $arResult['PROPERTIES']['SERVICE']['LINK_IBLOCK_ID'],
							"ADD_SECTIONS_CHAIN" => "N",
							"DISPLAY_COMPARE" => $arParams["USE_COMPARE"],
							"PRICE_CODE" => $arParams["PRICE_CODE"],
							"USE_PRICE_COUNT" => 'N',
							"SHOW_PRICE_COUNT" => '1',
							"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
							"PRICE_VAT_SHOW_VALUE" => $arParams["PRICE_VAT_SHOW_VALUE"],
							"USE_PRODUCT_QUANTITY" => "N",
							"CACHE_TYPE" => $arParams["CACHE_TYPE"],
							"CACHE_TIME" => $arParams["CACHE_TIME"],
							"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
							"CACHE_FILTER" => $arParams["CACHE_FILTER"],

							"SECTION_ID" => 0,
							'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
							'CURRENCY_ID' => $arParams['CURRENCY_ID'],
							'HIDE_NOT_AVAILABLE' => 'N',

							// paginator:
							'PAGER_SHOW_ALWAYS' => 'N',
							'PAGER_DESC_NUMBERING' => 'N',
							'PAGER_SHOW_ALL' => 'N',
							'DISPLAY_TOP_PAGER' => 'N',
							'DISPLAY_BOTTOM_PAGER' => 'N',
							'PAGER_TITLE' => '',

						),
						$component);?>
<? endif; $frame->end() ?>
				</div><!-- /.buy-block-wrap -->
			</div><!-- .buy-block-origin -->
			<? if (\Yenisite\Core\Tools::isComponentExist('bitrix:asd.share.buttons')): ?>
<? $APPLICATION->IncludeComponent(
	"bitrix:asd.share.buttons",
	"detail",
	array(
		"COMPONENT_TEMPLATE" => "detail",
		"ASD_ID" => "",
		"ASD_TITLE" => $arResult['~NAME'],
		"ASD_URL" => $arResult['~DETAIL_PAGE_URL'],
		"ASD_PICTURE" => $arResult['MORE_PHOTO'][0]['SRC'],
		"ASD_TEXT" => $arResult['~PREVIEW_TEXT'],
		//"ASD_LINK_TITLE" => GetMessage("RZ_RASSHARIT_V") . " #SERVICE#", //uncomment if you want to set this param from bitrix:catalog
		"ASD_SITE_NAME" => "",
		"ASD_INCLUDE_SCRIPTS" => array(
		)
	),
	false
); ?>
			<? endif ?>

			<?if($arResult['bTabs']):?>
			<div id="product-info-sections" class="product-info-sections combo-blocks <?=($arParams['DETAIL_INFO_MODE'] == 'tabs') ? 'tabs' : 'full'?>"
				 data-product-info-mode="<?=($arParams['DETAIL_INFO_MODE'] == 'tabs') ? 'tabs' : 'full'?>"
				 data-product-info-mode-def-expanded="<?= ($arParams['DETAIL_INFO_MODE'] == 'full' && $arParams['DETAIL_INFO_FULL_EXPANDED'] == 'Y') ? 'true' : 'false' ?>"
				>
				<div class="combo-links">
					<div class="links-wrap">
					<?if($arResult['bTechTab']):?>
						<a href="#characteristics" class="combo-link">
							<i class="flaticon-newspapre"></i>
							<span class="text"><?=GetMessage('BITRONIC2_CHARACTERISTICS')?></span>
						</a>
					<?endif?>

					<?if($arParams['USE_REVIEW'] == 'Y'):?>
						<a href="#comments" class="combo-link">
							<i class="flaticon-speech90"></i>
							<span class="text"><?=GetMessage('BITRONIC2_REVIEWS')?></span><?// TODO <sup></sup>?>
						</a>
					<?endif?>
					<?if($bShowVideo):?>
						<a href="#videos" class="combo-link">
							<i class="flaticon-movie16"></i>
							<span class="text"><?=GetMessage('BITRONIC2_VIDEO_REVIEWS')?></span><sup><?=count($arResult["PROPERTIES"]['VIDEO']['VALUE'])?></sup>
						</a>
					<?endif?>
					<?if($bShowDocs):?>
						<a href="#documentation" class="combo-link">
							<i class="flaticon-folded11"></i>
							<span class="text"><?=GetMessage('BITRONIC2_DOCUMENTATION')?></span><sup><?=count($arResult["PROPERTIES"][$arParams['MANUAL_PROP']]['VALUE'])?></sup>
						</a>
					<?endif?>
					</div>
				</div>
				<div class="tab-targets combo-content">
					<?if($arResult['bTechTab']):?>
					<div class="combo-target shown characteristics wow fadeIn" id="characteristics">
						<div class="combo-header">
							<i class="flaticon-newspapre"></i>
							<span class="text"><?=GetMessage('BITRONIC2_CHARACTERISTICS')?></span>
						</div>
						<div class="combo-target-content">
							<? include 'characteristics.php' ?>
						</div><!-- .combo-target-content -->
					</div><!-- /.tab-target#characteristics -->
					<?endif?>
					<?if($arParams['USE_REVIEW'] == 'Y'):?>
						<div class="combo-target wow fadeIn comments<?= ($arParams['DETAIL_INFO_MODE'] == 'full' && $arParams['DETAIL_INFO_FULL_EXPANDED'] == 'Y') ? ' shown' : '' ?>"
							id="comments">
							<div class="combo-header">
								<i class="flaticon-speech90"></i>
								<span class="text"><?=GetMessage('BITRONIC2_REVIEWS')?></span><?// TODO <sup>3</sup>?>
							</div>
							<div class="combo-target-content">
								<? include 'own_reviews.php' ?>
								#DETAIL_RW_YM_API# <?// mask replace in /../../element.php ?>
							</div><!-- .combo-target-content -->
						</div><!-- /.tab-target#comments -->
					<?endif?>
					<?if($bShowVideo):?>
						<div class="combo-target wow fadeIn videos<?= ($arParams['DETAIL_INFO_MODE'] == 'full' && $arParams['DETAIL_INFO_FULL_EXPANDED'] == 'Y') ? ' shown' : '' ?>" id="videos">
							<div class="combo-header">
								<i class="flaticon-movie16"></i>
								<span class="text"><?=GetMessage('BITRONIC2_VIDEO_REVIEWS')?></span><sup><?=count($arResult["PROPERTIES"]['VIDEO']['VALUE'])?></sup>
							</div>
							<div class="combo-target-content">
								<?foreach ($arResult["PROPERTIES"]['VIDEO']['VALUE'] as $value):?>
								<div class="video">
									<?if(isset($value['path']) && is_array($value)):?>
										<?$APPLICATION->IncludeComponent("bitrix:player","",Array(
											"PATH" => $value['path'],
											"PROVIDER" => "",
											"WIDTH" => "100%",
											"HEIGHT" => "100%",
											"AUTOSTART" => "N",
											"REPEAT" => "none",
											"VOLUME" => "90",
											"ADVANCED_MODE_SETTINGS" => "N",
											"PLAYER_TYPE" => "auto",
											"USE_PLAYLIST" => "N",
											"STREAMER" => "",
											"PREVIEW" => "",
											"FILE_TITLE" => "",
											"FILE_DURATION" => "",
											"FILE_AUTHOR" => "",
											"FILE_DATE" => "",
											"FILE_DESCRIPTION" => "",
											"MUTE" => "N",
											"PLUGINS" => array(
												0 => "",
												1 => "",
											),
											"ADDITIONAL_FLASHVARS" => "",
											"PLAYER_ID" => "",
											"BUFFER_LENGTH" => "10",
											"ALLOW_SWF" => "N",
											"SKIN_PATH" => "/bitrix/components/bitrix/player/mediaplayer/skins",
											"SKIN" => "",
											"CONTROLBAR" => "bottom",
											"WMODE" => "opaque",
											"LOGO" => "",
											"LOGO_LINK" => "",
											"LOGO_POSITION" => "none"
										), $component);?>
									<?else:?>
										<?$APPLICATION->IncludeComponent("bitrix:player","",Array(
											"PLAYER_TYPE" => "flv",
											"PROVIDER" => "youtube",
											"PATH" =>  $value,
											"WIDTH" => "100%",
											"HEIGHT" => "100%",
										), $component);?>
									<?endif;?>
								</div><!-- /.video -->
								<?endforeach;?>
							</div>
						</div><!-- /.tab-target#videos -->
					<?endif?>
					<?if($bShowDocs):
						$arMimeFile = array(
							"DOC" => array(
								"application/vnd.oasis.opendocument.text",
								"application/msword",
								"application/vnd.openxmlformats-officedocument.wordprocessingml.document",
							),
							"PDF" => array(
								"application/pdf",
							),
						);
						$arIconClass = array(
							"DOC" => "flaticon-doc",
							"PDF" => "flaticon-pdf17",
							'DEFAULT' => 'flaticon-newspapre',
						);
						?>
						<div class="combo-target wow fadeIn documentation<?= ($arParams['DETAIL_INFO_MODE'] == 'full' && $arParams['DETAIL_INFO_FULL_EXPANDED'] == 'Y') ? ' shown' : '' ?>" id="documentation">
							<div class="combo-header">
								<i class="flaticon-folded11"></i>
								<span class="text"><?=GetMessage('BITRONIC2_DOCUMENTATION')?></span><sup><?=count($arResult["PROPERTIES"][$arParams['MANUAL_PROP']]['VALUE'])?></sup>
							</div>
							<div class="combo-target-content">
								<?foreach ($arResult["PROPERTIES"][$arParams['MANUAL_PROP']]['VALUE'] as $key => $value):
									$arFile = CFile::GetFileArray($value);
									$icoClass = '';
									foreach($arMimeFile as $type => $arMime)
									{
										if(in_array($arFile['CONTENT_TYPE'],$arMime))
										{
											$icoClass = $arIconClass[$type];
											break;
										}
									}
									$icoClass = !empty($icoClass) ? $icoClass : $arIconClass['DEFAULT'];
									?>
									<div class="document-link">
										<a target="_blank" href="<?=htmlspecialcharsbx($arFile["SRC"])?>" class="link">
											<i class="<?=$icoClass?>"></i>
											<span class="text"><?=(!empty($arFile['DESCRIPTION'])) ? $arFile['DESCRIPTION'] : $arFile['ORIGINAL_NAME']?></span>
											(<?=CFile::FormatSize($arFile['FILE_SIZE'])?>)
										</a>
									</div>
								<?endforeach?>
							</div>
						</div><!-- /.tab-target#documentation -->
					<?endif;?>
				</div><!-- .tab-targets -->
			</div><!-- /.product-info-sections -->
			<?endif?>

			<?// BIG DATA similar_sell
			if($arParams['HIDE_ACCESSORIES'] != 'Y')
			{
				$frame = $this->createFrame()->begin("");
				include 'accessories.php';
				$frame->end();
			}
			?>
		</div><!-- /.col-xs-12 -->
	</div><!-- /.row -->
	<?if ($arResult['CATALOG'] && !$arResult['bOffers']):?>
		<?
		$frame = $this->createFrame()->begin($compositeLoader);
		$APPLICATION->IncludeComponent("bitrix:catalog.set.constructor",
			"bitronic2",
			array(
				"IBLOCK_ID" => $arParams["IBLOCK_ID"],
				"ELEMENT_ID" => $arResult["ID"],
				"PRICE_CODE" => $arParams["PRICE_CODE"],
				"BASKET_URL" => $arParams["BASKET_URL"],
				"CACHE_TYPE" => $arParams["CACHE_TYPE"],
				"CACHE_TIME" => $arParams["CACHE_TIME"],
				"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
				"CONVERT_CURRENCY" => $arParams['CONVERT_CURRENCY'],
				"CURRENCY_ID" => $arParams["CURRENCY_ID"],
				"RESIZER_SET_CONTRUCTOR" => $arParams["RESIZER_SETS"]["RESIZER_SET_CONTRUCTOR"]
			),
			$component
			// array("HIDE_ICONS" => "Y")
		);
		$frame->end();
		?>
	<?endif?>

	<?if($arResult['bSkuSimple']){ include 'sku_simple.php'; }?>

	<?// BIG DATA similar_view
	$frame = $this->createFrame()->begin("");
	if($arParams['HIDE_SIMILAR_VIEW'] != 'Y')
	{
		include 'similar_view.php';
	}
	$frame->end();
	?>
	<?// BIG DATA similar
	$frame = $this->createFrame()->begin("");
	if($arParams['HIDE_SIMILAR'] != 'Y')
	{
		include 'similar.php';
	}
	$frame->end();
	?>
	<?// RECOMMENDED products
	$frame = $this->createFrame()->begin("");
	if($arParams['HIDE_RECOMMENDED'] != 'Y')
	{
		include 'recommended.php';
	}
	$frame->end();
	?>
	<?// VIEWED products
	$frame = $this->createFrame()->begin("");
	if($arParams['HIDE_VIEWED'] != 'Y')
	{
		echo '#DETAIL_RW_VIEWED_PRODUCTS#'; //include 'viewed_products.php';
	}
	$frame->end();
	?>
	<? // JS PARAMS
	include 'js_params.php';
	?>
</main>
<?
	/* TODO
<? include '_/modals/modal_inform-when-price-drops.html'; ?>
<? include '_/modals/modal_cry-for-price.html'; ?>
<? include '_/modals/modal_calc-delivery.html'; ?>
*/
?>

<!-- MODALS -->

<? // MORE_PHOTO
if($arResult['MORE_PHOTO_COUNT'] > 0):?>
<div class="modal modal_big-img <?=$arResult['MORE_PHOTO_COUNT'] == 1 ? ' single-img' : ''?>" id="modal_big-img" role="dialog"
	 tabindex="-1" data-view-type="<?=$arParams['DETAIL_GALLERY_TYPE']?>">
	<button class="btn-close" data-toggle="modal" data-target="#modal_big-img">
		<i class="flaticon-close47"></i>
	</button>
	<div class="bigimg-wrap" data-bigimg-desc="<?= $arParams['DETAIL_GALLERY_DESCRIPTION'] ?>">
		<button type="button" class="img-control prev">
			<i class="flaticon-arrow133 arrow-left"></i>
		</button>
		<button type="button" class="img-control next">
			<i class="flaticon-right20 arrow-right"></i>
		</button>
		<img
			id="<?=$arItemIDs['PICT_MODAL']?>"
			src="<?=CResizer2Resize::ResizeGD2($arResult['MORE_PHOTO'][0]['SRC'], $arParams["RESIZER_SETS"]['RESIZER_DETAIL_BIG'])?>"
			alt="<?=$strAlt?>">
			<div class="img-desc" style="font-size: 18px">
				<?
				if (empty($arResult['MORE_PHOTO'][0]['DESCRIPTION'])) {
					echo $arResult['NAME'];
				} else {
					echo $arResult['MORE_PHOTO'][0]['DESCRIPTION'];
				}
				?>
			</div>
	</div>
	<?if(!$arResult['bSkuExt']):?>
	<div class="bigimg-thumbnails-wrap">
		<?if($arResult['MORE_PHOTO_COUNT'] > 1):?>
		<div class="thumbnails-frame bigimg-thumbs active" id="bigimg-thumbnails-frame">
			<div class="thumbnails-slidee" id="bigimg-thumbnails-slidee">
				<?foreach($arResult['MORE_PHOTO'] as $arPhoto):?>
					<?
					$descr = $arPhoto['DESCRIPTION'];
					if (empty($descr)) {
						$descr = $arResult['NAME'];
					}
					?>
				<div class="thumb">
					<img
						alt="<?=$strAlt?>"
						src="<?=CResizer2Resize::ResizeGD2($arPhoto['SRC'], $arParams["RESIZER_SETS"]['RESIZER_DETAIL_ICON'])?>"
						data-med-src="<?=CResizer2Resize::ResizeGD2($arPhoto['SRC'], $arParams["RESIZER_SETS"]['RESIZER_DETAIL_SMALL'])?>"
						data-big-src="<?=CResizer2Resize::ResizeGD2($arPhoto['SRC'], $arParams["RESIZER_SETS"]['RESIZER_DETAIL_BIG'])?>"
						data-img-desc="<?= htmlspecialcharsEx($descr) ?>">
				</div>
				<?endforeach?>
			</div><!-- #bigimg-thumbnails-slidee -->
		</div><!-- #bigimg-thumbnails-frame -->
		<?endif?>
	</div><!-- /.thumbnails -->
	<?else:?>
		<?foreach($arResult['OFFERS'] as $arOffer):?>
			<?if($arOffer['MORE_PHOTO_COUNT'] > 1):?>
			<div class="bigimg-thumbnails-wrap" id="<? echo $arItemIDs['SLIDER_MODAL_CONT_OF_ID'].$arOffer['ID']; ?>" style="display:none">
				<div class="thumbnails-frame bigimg-thumbs">
					<div class="thumbnails-slidee">
						<?foreach($arOffer['MORE_PHOTO'] as $arPhoto):?>
						<div class="thumb">
							<img
								alt="<?=$strAlt?>"
								src="<?=CResizer2Resize::ResizeGD2($arPhoto['SRC'], $arParams["RESIZER_SETS"]['RESIZER_DETAIL_ICON'])?>"
								data-src="<?=CResizer2Resize::ResizeGD2($arPhoto['SRC'], $arParams["RESIZER_SETS"]['RESIZER_DETAIL_ICON'])?>"
								data-med-src="<?=CResizer2Resize::ResizeGD2($arPhoto['SRC'], $arParams["RESIZER_SETS"]['RESIZER_DETAIL_SMALL'])?>"
								data-big-src="<?=CResizer2Resize::ResizeGD2($arPhoto['SRC'], $arParams["RESIZER_SETS"]['RESIZER_DETAIL_BIG'])?>">
						</div>
						<?endforeach?>
					</div><!-- #bigimg-thumbnails-slidee -->
				</div><!-- #bigimg-thumbnails-frame -->
			</div>
			<?endif?>
		<?endforeach?>
	<?endif?>
</div>
<?endif?>
<?
if (\Bitrix\Main\Loader::IncludeModule("yenisite.feedback")) {
	$this->SetViewTarget('bitronic2_modal_detail');
	if ($arParams['PRICE_LOWER'] != 'N') {
		\Yenisite\Core\Tools::IncludeArea('catalog', 'modal_price_drops');
	}
	if ($arParams['FOUND_CHEAP'] != 'N') {
		\Yenisite\Core\Tools::IncludeArea('catalog', 'modal_price_cry');
	}
	$this->EndViewTarget();
}
