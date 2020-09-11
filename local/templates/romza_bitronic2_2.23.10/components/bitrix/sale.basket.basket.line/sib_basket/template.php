<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
// echo "<pre style='text-align:left;'>";print_r($arResult);echo "</pre>";
$this->setFrameMode(true);

global $rz_b2_options;

$arBasketIds = [
	'DESKTOP' => [
		'CNT' => 'rbs_bsk_cnt',
		'SUM' => 'rbs_bsk_sum',
		'FOOTER_SUM' => 'rbs_bsk_sum_footer',
	],
	'MOBILE' => [
		'CNT' => 'rbs_bsk_cnt_mob',
		'SUM' => 'rbs_bsk_sum_mob',
		'FOOTER_SUM' => 'rbs_bsk_sum_footer_mobile',
	]
];
?>

<div class="header-basket header-basket_desk js-click" id="basket">

	<a href="<?=$arParams['PATH_TO_BASKET']?>" class="header-basket__button js-click-button" href="javascript:void(0);">
			<span class="header-basket__count">
				<span id="<?=$arBasketIds['DESKTOP']['CNT']?>">
					<?$frame = $this->createFrame($arBasketIds['DESKTOP']['CNT'], false)->begin('0');?>
						<?=$arResult['TOTAL_COUNT']?>
					<?$frame->end();?>
				</span>
			</span>
			<span class="header-basket__label"><?=GetMessage('BITRONIC2_BASKET_ON_SUMM')?></span>
			<span class="header-basket__total" id="<?=$arBasketIds['DESKTOP']['SUM']?>">
				<?$frame = $this->createFrame($arBasketIds['DESKTOP']['SUM'], false)->begin('0');?>
					<?=CRZBitronic2CatalogUtils::getElementPriceFormat($arResult["CURRENCY"], $arResult["TOTAL_SUM"], $arResult["TOTAL_SUM_FORMATED"], array('ID'=> 'total_small_basket_top'))?>
				<?$frame->end();?>
			</span>
	</a>
	<div class="basket-hide js-click-hide">
		<button class="basket-hide__close button-close js-click-close"></button>
		<p class="basket-hide__title"><?=GetMessage('BITRONIC2_CART_TITLE')?></p>
		<?$frame = $this->createFrame()->begin('');?>
			<? $arProductId = []; ?>
			<ul class="basket-items">
			<?
				foreach($arResult["CATEGORIES"]["READY"] as $arItem):
					$arProductId[$arItem['PRODUCT_ID']] = $arItem['PRODUCT_ID'];
			?>
					<li class="basket-item js-remove">
						<div class="basket-item__remove">
							<button class="button-remove js-remove-button" data-product-id=<?=$arItem['PRODUCT_ID']?> data-basket-id="<?=$arItem['ID']?>"></button>
						</div>
						<div class="basket-item__image-wrap">
							<a class="basket-item__image" href="#" itemscope itemtype="http://schema.org/ImageObject">
								<img class="lazy" alt="<?=$arItem['NAME']?>" data-original="<?=$arItem['MAIN_PHOTO']?>" data-original-jpg="<?=$arItem['MAIN_PHOTO_JPG']?>" src="<?=ConsVar::showLoaderWithTemplatePath()?>">
							</a>
						</div>
						<div class="basket-item__content">
							<!-- <p class="basket-item__category">
								<a class="basket-item__link" href="#">Мобильный телефон</a>
							</p> -->
							<p class="basket-item__name">
								<a class="basket-item__link" href="<?=$arItem['DETAIL_PAGE_URL']?>"><?=$arItem['NAME']?> <?=$arItem['QUANTITY'] > 1 ?'('.$arItem['QUANTITY'].'шт)':'';?></a>
							</p>
						</div>
						<div class="basket-item__price">
							<p class="current-price"><?=CRZBitronic2CatalogUtils::getElementPriceFormat($arItem["CURRENCY"], $arItem['PRICE'], $arItem["PRICE_FORMATED"], array('ID'=> 'small_basket_id_'.$arItem["ID"]))?></p>
						</div>                                                                                                                        
					</li>   
			<? 	endforeach; ?>                                                                   
			</ul>
			<script type="text/javascript">
				RZB2.ajax.BasketSmall.ElementsList = <?=CUtil::PhpToJSObject($arProductId, false, true, true)?>;
			</script>
		<?$frame->end();?>
		
		<div class="basket-total" id="<?=$arBasketIds['DESKTOP']['FOOTER_SUM']?>">
			<?$frame = $this->createFrame($arBasketIds['DESKTOP']['FOOTER_SUM'], false)->begin(CRZBitronic2Composite::insertCompositLoader());?>
				<div class="basket-total__left">
					<span class="basket-total__label"><?=GetMessage('BITRONIC2_BASKET_ITOGO')?></span>
					<span class="basket-total__price"><?=CRZBitronic2CatalogUtils::getElementPriceFormat($arResult["CURRENCY"], $arResult["TOTAL_SUM"], $arResult["TOTAL_SUM_FORMATED"], array('ID'=> 'total_small_basket_bottom'))?></span>
				</div>
				<div class="basket-total__right">
					<a class="basket-total__button button" href="<?=$arParams['PATH_TO_BASKET']?>"><?=GetMessage('BITRONIC2_BASKET_MAKE_ORDER')?></a>
				</div>       
			<?$frame->end();?>                             
		</div>
	</div>
</div>

<div class="header-basket header-basket_mob">
	<a class="header-basket__button" href="<?=$arParams['PATH_TO_BASKET']?>">
			<span class="header-basket__count">
				<span id="<?=$arBasketIds['MOBILE']['CNT']?>">
					<?$frame = $this->createFrame($arBasketIds['MOBILE']['CNT'], false)->begin('0');?>
						<?=$arResult['TOTAL_COUNT']?>
					<?$frame->end();?>
				</span>
			</span>
			<span class="header-basket__label"><?=GetMessage('BITRONIC2_BASKET_ON_SUMM')?></span>
			<span class="header-basket__total" id="<?=$arBasketIds['MOBILE']['SUM']?>">
				<?$frame = $this->createFrame($arBasketIds['MOBILE']['SUM'], false)->begin('0');?>
					<?=CRZBitronic2CatalogUtils::getElementPriceFormat($arResult["CURRENCY"], $arResult["TOTAL_SUM"], $arResult["TOTAL_SUM_FORMATED"], array('ID'=> 'total_small_basket_top'))?>
				<?$frame->end();?>
			</span>
	</a>                            
</div>