<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */
/** @global CDatabase $DB */

$this->setFrameMode(true);
use \Bitrix\Catalog\CatalogViewedProductTable as CatalogViewedProductTable;
CatalogViewedProductTable::refresh(973, CSaleBasket::GetBasketUserID());
// @var $moduleId
// @var $moduleCode
include $_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/include/debug_info.php';
include $_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/lang/'.LANGUAGE_ID.'/header.php';
if (empty($arResult['ITEMS']))
{
	return;
}

$itemCount = count($arResult['ITEMS']);
$id = 'bxdinamic_bitronic2_viewed_list_'.$arParams['IBLOCK_ID'];
//$arJsCache = CRZBitronic2CatalogUtils::getJSCache($component); - not need because this component not cached template
$bHoverMode = $arParams['HOVER-MODE'] == 'detailed-expand';
?>


<a class="main-nav__link main-nav__link_watch js-nav-link js-nav-height" href="#">
	<span class="main-nav__fix">
		<span class="main-nav__text">
			<?=htmlspecialcharsBx($arParams['HEADER_TEXT'])?>
			<span id="<?=$id?>">
				<?
				$frame = $this->createFrame($id, false)->begin(CRZBitronic2Composite::insertCompositLoader());
				?>
				<?=$itemCount?>
				<?$frame->end();?>
			</span>
		</span>
	</span>
</a>
<ul class="inner-nav js-nav-hide">
	<li>
		<div class="inner-nav__head">
			<button class="button-back js-click-back normalize-height"></button>                                           
				<div class="inner-nav__head-txt">
					<p class="inner-nav__title"><?=htmlspecialcharsBx($arParams['HEADER_TEXT'])?></p>                                           
				</div>
			<button class="inner-nav__button button-close js-click-close"></button>
		</div>
		<?$frame = $this->createFrame()->begin(CRZBitronic2Composite::insertCompositLoader());?>
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
					<li class="basket-item">
						<div class="basket-item__image-wrap">
							<a class="basket-item__image" href="<?=$arItem['DETAIL_PAGE_URL']?>" itemtype="http://schema.org/ImageObject">
								<img title="<?= $imgTitle ?>" alt="<?=$arItem['PICTURE_PRINT']['ALT']?>" src="<?=$arItem['PICTURE_PRINT']['SRC']?>">
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
								<?=$arItem['MIN_PRICE']['DISCOUNT_VALUE'];//CRZBitronic2CatalogUtils::getElementPriceFormat($arItem['MIN_PRICE']['CURRENCY'], $arItem['MIN_PRICE']['DISCOUNT_VALUE'], $arItem['MIN_PRICE']['PRINT_DISCOUNT_VALUE']);?>
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
		<?$frame->end();?>
		<a class="basket-hide__button button button_white" href="/catalog/"><?=GetMessage('BITRONIC2_VIEWED_HEADER_CATALOG');?></a>   
	</li>
</ul>  