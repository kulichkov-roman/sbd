<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);
\Bitrix\Main\Loader::includeModule('yenisite.bitronic2');
$bSibCore = \Bitrix\Main\Loader::includeModule('sib.core');
$itemCount = count($arResult["CATEGORIES"]["DELAY"]);
$id = 'bxdinamic_bitronic2_favorite_list_mobile';
?>
<a class="main-nav__link main-nav__link_com favorite-mode js-nav-link js-nav-height" href="#">
	<span class="main-nav__fix" id="<?=$id?>">
		<?
			$frame = $this->createFrame($id, false)->begin(CRZBitronic2Composite::insertCompositLoader());
		?>
			<span class="main-nav__text">Избранное<span class="rbs-favorite-count-mobile"><?=$itemCount?></span></span>
		<?
			$frame->end();?>
	</span>
</a>
<ul id="rbs-favorite-list-mobile" class="inner-nav js-nav-hide">
	<li class="rbs-basket-hide-mobile">
		<div class="inner-nav__head">
			<button class="button-back js-click-back normalize-height"></button>                                            
			<div class="inner-nav__head-txt">
				<p class="inner-nav__title">Избранное</p>                                                
			</div>
			<button class="inner-nav__button button-close js-click-close"></button>
		</div>
		<?$frame = $this->createFrame()->begin(CRZBitronic2Composite::insertCompositLoader());?>
			<div class="favorite-desktop-result">
				<?if($itemCount <= 0):?>
					<p class="basket-hide__text">Здесь будут отображаться ваши избранные товары.</p>
					<a class="basket-hide__button button button_white" href="/catalog/">Перейти в каталог</a>
				<?endif?>
				<?include $_SERVER["DOCUMENT_ROOT"].SITE_TEMPLATE_PATH.'/include/debug_info.php';?>
				<?if($itemCount > 0):?>
					<ul class="basket-items">
							<?foreach($arResult["CATEGORIES"]["DELAY"] as $arItem):
								$arProductId[$arItem['PRODUCT_ID']] = $arItem['PRODUCT_ID'];
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
								$element = \Sib\Core\Helper::getElementInfo($arItem['PRODUCT_ID']);
								?>
								<li class="basket-item js-remove">
									<div class="basket-item__remove">
										<button class="button-remove js-del-btn-comp-fav" data-type="favorite" data-product-id=<?=$arItem['PRODUCT_ID']?>></button>
									</div>
									<div class="basket-item__image-wrap">
										<a class="basket-item__image" href="<?=$element['DETAIL_PAGE_URL']?>" itemtype="http://schema.org/ImageObject">
											<img class="lazy" alt="<?=$arItem['NAME']?>" data-original="<?=$arItem['MAIN_PHOTO']?>" data-original-jpg="<?=$arItem['MAIN_PHOTO_JPG']?>" src="<?=ConsVar::showLoaderWithTemplatePath()?>">
										</a>
									</div>
									<div class="basket-item__content">
										<!-- <p class="basket-item__category">
											<a class="basket-item__link" href="<?=$element['DETAIL_PAGE_URL']?>">Мобильный телефон</a>
										</p> -->
										<p class="basket-item__name">
											<a class="basket-item__link" href="<?=$element['DETAIL_PAGE_URL']?>"><?=$arItem['NAME']?></a>
										</p>
									</div>
									<div class="basket-item__price">
										<p class="current-price">
										<? //global $USER; if($USER->IsAdmin()){echo '<pre>'; print_r($arItem['MIN_PRICE']); echo '</pre>';}; ?>
										<?
											if($bSibCore){
												$prices = \Sib\Core\Catalog::getDiscountPriceArray($arItem['PRODUCT_ID']);
												//$defRegion = \Sib\Core\Catalog::getDefRegion();
												$price =$prices; //$prices[$defRegion];

												if($price['PRICE_DISCOUNT'] !== 0 && $price['PRICE_DISCOUNT'] <= $price['BASE_PRICE']){
													$arItem['PRICE'] = $price['PRICE_DISCOUNT'];													
												} else {
													$arItem['PRICE'] = $price['BASE_PRICE'];
												}
											}
										?>
										<?=CRZBitronic2CatalogUtils::getElementPriceFormat($arItem["CURRENCY"], $arItem['PRICE'], $arItem["PRICE_FORMATED"], array('ID'=> 'mobile_fav_id_'.$arItem["ID"]))?>
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

				<?if($itemCount > 0):?>					
					<a class="basket-hide__button button button_white" href="javascript:void(0)" onclick="RZB2.ajax.Favorite.DeleteAll()">Очистить избранное</a>   
				<?else:/*?>
					<a class="basket-hide__button button button_white" href="/catalog/"><?=GetMessage('BITRONIC2_COMPARE_CATALOG')?></a>   
				<?*/endif;?>

			</div>

			<script>
				RZB2.ajax.Favorite.ElementsList = <?=CUtil::PhpToJSObject($arProductId, false, true, true)?>;
				RZB2.ajax.Favorite.RefreshButtons();
			</script>
		<?$frame->end();?>

	</li>
</ul>