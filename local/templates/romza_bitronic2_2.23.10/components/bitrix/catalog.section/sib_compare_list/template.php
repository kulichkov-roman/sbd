<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);

$templateData = $arResult['TEMPLATE_DATA'];

$bStores = $arParams["SHOW_AMOUNT_STORE"] == "Y" && Bitrix\Main\ModuleManager::isModuleInstalled("catalog");
$bExpandedStore = false;
$itemCount = count($arResult['ITEMS']);
$id = 'bxdinamic_bitronic2_compare_list_'.$arParams['IBLOCK_ID'];
?>

<div id="rbs-compare-list" class="compared js-click">
	<a class="compared__button js-click-button" href="javascript:void(0);">
		<span class="rbs-compare-count" id="<?=$id?>">
			<?
			$frame = $this->createFrame($id, false)->begin(CRZBitronic2Composite::insertCompositLoader());
			?>
			<?=$itemCount?>
			<?$frame->end();?>
		</span>
	</a>
	<div class="rbs-basket-hide basket-hide js-click-hide">
		<button class="basket-hide__close button-close js-click-close"></button>
		<p class="basket-hide__title"><?=GetMessage('BITRONIC2_COMPARE_TITLE')?></p>
		<p class="basket-hide__text"><?=GetMessage('BITRONIC2_COMPARE_DESCR')?></p>

		<?$frame = $this->createFrame()->begin(CRZBitronic2Composite::insertCompositLoader());?>
				<?include $_SERVER["DOCUMENT_ROOT"].SITE_TEMPLATE_PATH.'/include/debug_info.php';?>
				<?if($itemCount > 0):?>
				<ul class="basket-items">
						<?foreach($arResult['ITEMS'] as $arItem):
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
							<li class="basket-item js-remove">
								<div class="basket-item__remove">
									<button class="button-remove js-remove-button" data-product-id=<?=$arItem['ID']?>></button>
								</div>
								<div class="basket-item__image-wrap">
									<a class="basket-item__image" href="<?=$arItem['DETAIL_PAGE_URL']?>" itemtype="http://schema.org/ImageObject">
										<img title="<?= $imgTitle ?>" alt="<?=$arItem['PICTURE_PRINT']['ALT']?>" class="lazy" data-original="<?=$arItem['PICTURE_PRINT']['SRC']?>" data-original-jpg="<?=$arItem['PICTURE_PRINT']['SRC_JPG']?>" src="<?=ConsVar::showLoaderWithTemplatePath()?>">
									</a>
								</div>
								<div class="basket-item__content">
									<!-- <p class="basket-item__category">
										<a class="basket-item__link" href="<?=$arItem['DETAIL_PAGE_URL']?>">Мобильный телефон</a>
									</p> -->
									<p class="basket-item__name">
										<a class="basket-item__link" href="<?=$arItem['DETAIL_PAGE_URL']?>"><?=$arItem['NAME']?></a>
									</p>
								</div>
								<div class="basket-item__price">
									<p class="current-price">
									<? //global $USER; if($USER->IsAdmin()){echo '<pre>'; print_r($arItem['MIN_PRICE']); echo '</pre>';}; ?>
										<?=CRZBitronic2CatalogUtils::getElementPriceFormat($arItem['MIN_PRICE']["CURRENCY"], $arItem['MIN_PRICE']['DISCOUNT_VALUE'], $arItem['MIN_PRICE']["PRINT_DISCOUNT_VALUE"], array('ID'=> 'price_compare_item_desktop_'.$arItem['ID']))?>
										<?//=$arItem['MIN_PRICE']['DISCOUNT_VALUE'];//CRZBitronic2CatalogUtils::getElementPriceFormat($arItem['MIN_PRICE']['CURRENCY'], $arItem['MIN_PRICE']['DISCOUNT_VALUE'], $arItem['MIN_PRICE']['PRINT_DISCOUNT_VALUE']);?>
									</p>
									<?/*if(!$arItem['ON_REQUEST']):?>
										<span class="price-new">
										<?=($arItem['bOffers'] && $arItem['bOffersNotEqual']) ? GetMessage('BITRONIC2_COMPARE_FROM') : ''?>
										<?=CRZBitronic2CatalogUtils::getElementPriceFormat($arItem['MIN_PRICE']['CURRENCY'], $arItem['MIN_PRICE']['DISCOUNT_VALUE'], $arItem['MIN_PRICE']['PRINT_DISCOUNT_VALUE']);?></span>
										<div>
											<?if($arItem['MIN_PRICE']['DISCOUNT_DIFF'] > 0):?>
											<span class="price-old"><?=CRZBitronic2CatalogUtils::getElementPriceFormat($arItem['MIN_PRICE']['CURRENCY'], $arItem['MIN_PRICE']['VALUE'], $arItem['MIN_PRICE']['PRINT_VALUE']);?></span>
											<?endif?>
										</div>
									<?endif*/?>
								</div>                                                                                                                        
							</li>
						<?endforeach;?>
				</ul>
				<?endif?>

				<script>
					RZB2.ajax.Compare.ElementsList = <?=CUtil::PhpToJSObject($arParams['COMPARE_LIST'], false, true, true)?>;
					RZB2.ajax.Compare.Params = {actionVar: '<?=$arParams['ACTION_CATALOG_VARIABLE']?>', productVar: '<?=$arParams['PRODUCT_ID_CATALOG_VARIABLE']?>'};
				</script>
				<?$frame->end();?>
		<?if($itemCount > 0):?>					
			<a class="basket-hide__button button button_white" href="<?=$arParams['COMPARE_URL']?>"><?=GetMessage('BITRONIC2_COMPARE_COMPARE')?></a>
			<a class="basket-hide__button button button_white" href="#" onclick="RZB2.ajax.Compare.DeleteAll()"><?=GetMessage('BITRONIC2_COMPARE_CLEAR')?></a>   
		<?else:?>
			<a class="basket-hide__button button button_white" href="/catalog/"><?=GetMessage('BITRONIC2_COMPARE_CATALOG')?></a>   
		<?endif;?>
	</div>
</div>