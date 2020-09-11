<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */
/** @global CDatabase $DB */
use \Bitrix\Main\Page\FrameStatic;

$this->setFrameMode(true);

// @var $moduleId
// @var $moduleCode
include $_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/include/debug_info.php';
include $_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/lang/'.LANGUAGE_ID.'/header.php';

if (empty($arResult['ITEMS']))
{
	return;
}
$bHoverMode = $arParams['HOVER-MODE'] == 'detailed-expand';
?>
<div class="card-recommend main-block_index" itemprop="isRelatedTo" itemscope itemtype="https://schema.org/Product">
	<div class="card-recommend__heading">
		Рекомендуем
	</div>
	<div class="rec-items js-items">
		<?
			foreach($arResult['ITEMS'] as $arItem):
				$jsString = '';
				$strMainID = $this->GetEditAreaId($arItem['ID'].rand());
				$arItemIDs = array(
					'ID' => $strMainID,
					'PICT' => $strMainID . '_pict',
					'SLIDER_CONT_OF_ID' => $strMainID . '_slider_cont_',
					'SECOND_PICT' => $strMainID . '_secondpict',
					'STICKER_ID' => $strMainID . '_sticker',
					'SECOND_STICKER_ID' => $strMainID . '_secondsticker',
					'QUANTITY' => $strMainID . '_quantity',
					'QUANTITY_DOWN' => $strMainID . '_quant_down',
					'QUANTITY_UP' => $strMainID . '_quant_up',
					'QUANTITY_MEASURE' => $strMainID . '_quant_measure',
					'BUY_LINK' => $strMainID . '_buy_link',
					'BUY_ONECLICK' => $strMainID . '_buy_oneclick',
					'BASKET_ACTIONS' => $strMainID . '_basket_actions',
					'AVAILABLE_INFO' => $strMainID . '_avail_info',
					'AVAILABLE_INFO_FULL' => $strMainID . '_avail_info_full',
					'SUBSCRIBE_LINK' => $strMainID . '_subscribe',
					'COMPARE_LINK' => $strMainID . '_compare_link',
					'FAVORITE_LINK' => $strMainID . '_favorite_link',
					'REQUEST_LINK' => $strMainID . '_request_link',
		
					'PRICE' => $strMainID . '_price',
					'PRICE_CONTAINER' => $strMainID . '_price_container',
					'PRICE_OLD' => $strMainID . '_price_old',
					'DSC_PERC' => $strMainID . '_dsc_perc',
					'SECOND_DSC_PERC' => $strMainID . '_second_dsc_perc',
					'PROP_DIV' => $strMainID . '_sku_tree',
					'PROP' => $strMainID . '_prop_',
					'ARTICUL' => $strMainID . '_articul',
					'DISPLAY_PROP_DIV' => $strMainID . '_sku_prop',
					'BASKET_PROP_DIV' => $strMainID . '_basket_prop',
					'BASKET_BUTTON' => $strMainID . '_basket_button',
					'STORES' => $strMainID . '_stores',
					'PRICE_ADDITIONAL' => $strMainID . '_price_additional',
				);
				$strObName = 'ob'.preg_replace("/[^a-zA-Z0-9_]/", "x", $strMainID).'_recomm';
				$imgTitle = (
					!empty($arItem['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_TITLE'])
					? $arItem['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_TITLE']
					: $arItem['NAME']
				);
				$bShowOneClick = ($arParams["DISPLAY_ONECLICK"] && !$arItem['bOffers']);

				$bEmptyProductProperties = empty($arItem['PRODUCT_PROPERTIES']);
				$bBuyProps = ('Y' == $arParams['ADD_PROPERTIES_TO_BASKET'] && !$bEmptyProductProperties);
		?>
			<div class="rec-item catalog-item" id="<?=$arItemIDs['ID']?>">
				<div class="rec-item__img">
				<img itemprop="contentUrl" class="lazy" title="<?= $imgTitle ?>" data-original="<?=$arItem['PICTURE_PRINT']['SRC']?>" src="<?=consVar::showLoaderWithTemplatePath()?>" alt="<?=$arItem['PICTURE_PRINT']['ALT']?>">
				</div>
				<div class="catalog-item__content">
					<h4 class="catalog-title">
						<a class="catalog-title__link js-ellip-2" href="<?=$arItem['DETAIL_PAGE_URL']?>" tabindex="0" data-ellipsis-id="25"><span><?=$arItem['NAME']?></span></a>
					</h4>
					<?
						if ($arParams['SHOW_STARS'] == 'Y')
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
							<p class="current-price" id="<?=$arItemIDs['PRICE']?>">
								<?=CRZBitronic2CatalogUtils::getElementPriceFormat($arItem['MIN_PRICE']['CURRENCY'], $arItem['MIN_PRICE']['DISCOUNT_VALUE'], $arItem['MIN_PRICE']['PRINT_DISCOUNT_VALUE']);?>
								<?//=$arItem['MIN_PRICE']['DISCOUNT_VALUE']?>
							</p>
						</div>
						<div class="catalog-bottom__right" id="<?=$arItemIDs['BASKET_ACTIONS']?>">
							<?if($arItem['CAN_BUY']):?>
								<button type="button" class="buy catalog-bottom__button button" id=<?=$arItemIDs['BUY_LINK']?> data-product-id="<?=$arItem['ID']?>">
									<?= COption::GetOptionString($moduleId, 'button_text_buy') ?>
								</button>
							<?endif;?>
						</div>
					</div>
				</div>
			</div>
			
			<? include 'js_params.php'; ?>
			<script type="text/javascript">
				<?=$jsString?>
			</script>
		<?endforeach;?>
	</div>
</div>
