<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if (empty($arResult["CATEGORIES"]))
	return;

// @var $moduleId
// @var $moduleCode
include $_SERVER['DOCUMENT_ROOT'] . SITE_TEMPLATE_PATH . '/include/module_code.php';

\Bitrix\Main\Localization\Loc::loadMessages(SITE_TEMPLATE_PATH . '/header.php');
\Bitrix\Main\Loader::includeModule('sib.core');
?>
	<table>
	<?foreach($arResult["CATEGORIES"] as $category_id => $arCategory):?>
		<?if(is_array($arCategory['ITEMS']))
		foreach($arCategory["ITEMS"] as $i => $arElement):?>
			<?if(isset($arResult["ELEMENTS"][$arElement["ITEM_ID"]])):
				$arItem = $arResult["ELEMENTS"][$arElement["ITEM_ID"]];

                if (CRZBitronic2CatalogUtils::checkAvPrFotoForElement($arItem, $arParams)){
                    continue;
				}
				
				\Sib\Core\Catalog::checkDiscountPrice($arItem);

				$availableClass = !$arItem["CAN_BUY"] ? 'out-of-stock' : ($arItem['FOR_ORDER'] ? 'available-for-order' : 'in-stock');
				$availableClass = !$arItem['ON_REQUEST'] ? $availableClass : 'available-for-order';
				$availableClass = !$arItem["bOffers"]    ? $availableClass : '';
				?>
				<tr class="ajax-search-item category_<?=$category_id?> <?=$availableClass?>">
					<td itemscope itemtype="http://schema.org/ImageObject" class="item-photo">
						<a href="<?echo $arElement["URL"]?>"><img itemprop="contentUrl" src="<?echo $arElement["PICTURE"]?>" alt="<?echo strip_tags($arElement["NAME"])?>"></a>
					</td>
					<td class="item-name" colspan="3">
						<a href="<?echo $arElement["URL"]?>"><?echo $arElement["NAME"]?></a>
					</td>
					<td class="item-price">
						<?if(!$arItem["bOffers"]):?>
							<?include $_SERVER['DOCUMENT_ROOT'] . SITE_TEMPLATE_PATH . '/include/availability_dots.php';?>
						<?endif;?>

						<?if(!$arItem["bOffers"] && !$arItem['ON_REQUEST']):?>
							<?=CRZBitronic2CatalogUtils::getElementPriceFormat($arItem['MIN_PRICE']['CURRENCY'], $arItem['MIN_PRICE']['DISCOUNT_VALUE'], $arItem['MIN_PRICE']['PRINT_DISCOUNT_VALUE']);?>
						<?endif;?>
					</td>
					<?if(false):?>
					<td class="item-availability">
						
					</td>
					
					<td class="item-actions">
						<?if($arItem["bOffers"] || !empty($arItem['PRODUCT_PROPERTIES'])):?>
							<a href="<?=$arElement['URL']?>" class="catalog-bottom__button button btn-buy btn-main small">
								<i class="flaticon-shopping109"></i>
								<span class="text"><?=COption::GetOptionString($moduleId, 'button_text_offers')?></span>
							</a>
						<?else:?>
							<button class="catalog-bottom__button button btn-buy <?=($arItem['ON_REQUEST']?'request':'buy')?>" data-product-id="<?=$arItem["ID"]?>" data-iblock-id="<?=$arItem['IBLOCK_ID']?>"<?if($arItem['ON_REQUEST']):?>
								data-toggle="modal" data-target="#modal_contact_product" data-measure-name="<?=$arItem['CATALOG_MEASURE_NAME']?>"<?endif?>>
									<?=str_replace(' ', '&nbsp;', COption::GetOptionString($moduleId, 'button_text_buy'))?>
							</button>
						<?endif;?>
					</td>
					<?endif;?>
				</tr>
			<?elseif(isset($arElement["ITEM_ID"])):?>
				<tr class="ajax-search-item category_<?=$category_id?>">
					<td itemscope itemtype="http://schema.org/ImageObject" class="item-photo">
					<?if (!empty($arElement['PICTURE'])):?>
						<a href="<?echo $arElement["URL"]?>"><img itemprop="contentUrl"  src="<?echo $arElement["PICTURE"]?>" alt="<?echo $arElement["NAME"]?>"></a>
					<?endif?>
					</td>
					<td class="item-name" colspan="4">
						<a href="<?echo $arElement["URL"]?>"><?echo $arElement["NAME"]?></a>
					</td>
				</tr>
			<?endif;?>
		<?endforeach;?>
	<?endforeach;?>
	</table>
	<?foreach($arResult["CATEGORIES"] as $category_id => $arCategory):?>
		<div class="popup-ajax-footer category_<?=$category_id?>">
			<?if(is_array($arCategory['ITEMS']))
			foreach($arCategory["ITEMS"] as $i => $arElement):?>
				<?if(!isset($arElement["ITEM_ID"])):?>
					<a href="<?echo $arElement["URL"]?>" class="btn-show-all"><?echo $arElement["NAME"]?> <?=$arCategory['TITLE'] ? '('.$arCategory['TITLE'].')': ''?></a>
				<?endif?>
			<?endforeach;?>
		</div>
	<?endforeach;?>
<script type="text/javascript">
    BASKET_URL = '<?=$arParams['BASKET_URL']?>';
</script>
<?
// echo "<pre style='text-align:left;'>";print_r($arResult);echo "</pre>";