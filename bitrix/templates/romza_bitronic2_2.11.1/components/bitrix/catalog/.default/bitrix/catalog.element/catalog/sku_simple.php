<div class="product-modifications wow fadeIn">
	<table class="table_product-modifications" id="<?=$arItemIDs['SKU_TABLE']?>">
		<caption><?=GetMessage('BITRONIC2_OFFERS_TABLE_NAME')?></caption>
		<thead>
			<tr>
				<th class="name"><span class="text no-sort"><?=GetMessage('BITRONIC2_OFFERS_NAME')?></span></th>
				<?foreach($arParams['OFFERS_PROPERTY_CODE'] as $propCode):
					if(!array_key_exists($propCode, $arResult['OFFERS_DISPLAY_PROPS'])) continue;?>
					<th><span class="text no-sort"><?=$arResult['OFFERS_DISPLAY_PROPS'][$propCode]?></span></th>
				<?endforeach?>
				<th class="price"><span class="text no-sort"><?=GetMessage('BITRONIC2_OFFERS_PRICE')?></span></th>
				<th></th>
				<th class="actions"></th>
			</tr>
		</thead>
		<tbody>
			<?foreach($arResult['OFFERS'] as $arOffer):
				$availableClass = (!$arOffer['CAN_BUY'] && !$arOffer['ON_REQUEST'] ? 'out-of-stock' : ($arOffer['FOR_ORDER'] || $arOffer['ON_REQUEST'] ? 'available-for-order' : ''));
				$availableClass = ($availableClass ? ' class="'.$availableClass.'"' : '');
				?>
				<tr<?=$availableClass?>>
					<td class="name">
						<span class="text"><?=$arOffer['NAME']?></span>
					</td>
					<?foreach($arParams['OFFERS_PROPERTY_CODE'] as $propCode):
						if(!array_key_exists($propCode, $arResult['OFFERS_DISPLAY_PROPS'])) continue;?>
						<td><?=is_array($arOffer['DISPLAY_PROPERTIES'][$propCode]['DISPLAY_VALUE']) 
							? implode(' / ', $arOffer['DISPLAY_PROPERTIES'][$propCode]['DISPLAY_VALUE'])
							: $arOffer['DISPLAY_PROPERTIES'][$propCode]['DISPLAY_VALUE']?></td>
					<?endforeach?>
					<td class="price">
						<? if(!$arOffer['ON_REQUEST']):
						?><?=CRZBitronic2CatalogUtils::getElementPriceFormat($arOffer['MIN_PRICE']['CURRENCY'],$arOffer['MIN_PRICE']['VALUE'], $arOffer['MIN_PRICE']['PRINT_VALUE'])?><?
						endif?> 
					</td>
					<td>
						<span class="avail-dot when-in-stock" title="<?=GetMessage('BITRONIC2_ITEM_AVAILABLE_TRUE')?>" data-tooltip></span>
						<span class="avail-dot when-out-of-stock" title="<?=GetMessage('BITRONIC2_ITEM_AVAILABLE_FALSE')?>" data-tooltip></span>
						<span class="avail-dot when-available-for-order" title="<?=GetMessage('BITRONIC2_ITEM_AVAILABLE_FOR_ORDER')?>" data-tooltip></span>
					</td>
					<td class="actions <?=$arOffer['CAN_BUY'] ? '' : ($arOffer['ON_REQUEST'] ? 'on-request' : 'out-of-stock')?>">
						<?/* TODO when favorites selects from many iblocks
						if ($arParams['DISPLAY_FAVORITE']):?>
							<button 
								type="button" 
								class="btn-action favorite" 
								data-favorite-id="<?=$arOffer['ID']?>" 
								data-offer-id="<?=$arOffer['ID']?>"
								data-tooltip title="<?=GetMessage('BITRONIC2_ADD_FAVORITE')?>"
								>
								<i class="flaticon-heart3"></i>
							</button>
						<?endif*/?>
					<?
						/* TODO
						include '_/buttons/btn-action_to-comp.html';
						*/
						?>
						
						<?if($arOffer['CAN_BUY']):?>
							<button type="button"
								data-tooltip title="<?=GetMessage('BITRONIC2_ONECLICK')?>"
								class="btn-action one-click-buy"
								data-toggle="modal" data-target="#modal_quick-buy"
								data-id="<?=$arOffer['ID']?>">
								<i class="flaticon-shopping220"></i>
							</button>
							<span class="quantity-counter">
								<!-- parent must have class .quantity-counter! -->
								<button type="button" class="btn-silver quantity-change decrease"><span class="minus">&ndash;</span></button>
								<input type="text" class="quantity-input textinput" value="<?=$arResult['CATALOG_MEASURE_RATIO']?>">
								<button type="button" class="btn-silver quantity-change increase"><span class="plus">+</span></button>
							</span>
							<span class="btn-buy-wrap text-only">
								<button type="button" class="btn-action buy <?=($arOffer['FOR_ORDER']?'when-available-for-order':'when-in-stock')?>" data-offer-id="<?=$arOffer['ID']?>" data-offer-name="<?=$arOffer['NAME']?>">
									<span class="text"><?=GetMessage('BITRONIC2_ADD_BASKET')?></span>
									<span class="text in-cart"><?=GetMessage('BITRONIC2_PRODUCT_IN_CART')?></span>
								</button>
							</span>
						<?elseif($arOffer['ON_REQUEST']):?>
							<span class="btn-buy-wrap text-only on-request">
								<button type="button" class="btn-action buy on-request"
									data-offer-id="<?=$arOffer['ID']?>" data-offer-name="<?=$arOffer['NAME']?>" data-measure-name="<?=$arOffer['CATALOG_MEASURE_NAME']?>"
									data-toggle="modal" data-target="#modal_contact_product">
									<span class="text"><?=GetMessage('BITRONIC2_PRODUCT_REQUEST')?></span>
								</button>
							</span>
							<span class="when-out-of-stock on-request"><?=GetMessage('BITRONIC2_PRODUCT_AVAILABLE_ON_REQUEST')?></span>
						<?else:?>
							<span class="when-out-of-stock"><?=GetMessage('BITRONIC2_PRODUCT_AVAILABLE_FALSE')?></span>
						<?endif?>
					</td>
				</tr>
			<?endforeach?>
		</tbody>
	</table>
</div>