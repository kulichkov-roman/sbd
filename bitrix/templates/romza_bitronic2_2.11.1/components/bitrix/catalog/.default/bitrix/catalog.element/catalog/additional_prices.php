<?
foreach ($arItemPrices as $priceCode => $arPrice): ?>
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