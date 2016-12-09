<? // ###############  SKU   ###############
	foreach ($arResult['SKU_PROPS'] as &$arProp)
	{
		if (!isset($arResult['OFFERS_PROP'][$arProp['CODE']]))
			continue;
		$arSkuProps[] = array(
			'ID' => $arProp['ID'],
			'SHOW_MODE' => $arProp['SHOW_MODE'],
			'VALUES_COUNT' => $arProp['VALUES_COUNT']
		);
		if ('TEXT' == $arProp['SHOW_MODE'])
		{
			?>
			<div class="select-wrap sku" id="<? echo $arItemIDs['PROP'].$arProp['ID']; ?>_cont">
				<select name="<? echo $arItemIDs['PROP'].$arProp['ID']; ?>" class="select-styled" id="<? echo $arItemIDs['PROP'].$arProp['ID']; ?>_list">
					<?foreach ($arProp['VALUES'] as $arOneValue):
						$arOneValue['NAME'] = htmlspecialcharsbx($arOneValue['NAME']);?>
						<option 
							data-treevalue="<? echo $arProp['ID'].'_'.$arOneValue['ID']; ?>" 
							data-onevalue="<? echo $arOneValue['ID']; ?>"
							data-showmode="<? echo $arProp['SHOW_MODE']; ?>"
							value="<? echo $arProp['ID'].'_'.$arOneValue['ID']; ?>"
							>
							<? echo $arOneValue['NAME']; ?>
						</option>
					<?endforeach?>
				</select>
			</div>
			<?
		}
		elseif ('PICT' == $arProp['SHOW_MODE'])
		{
			?>
				<div class="color-selection sku" id="<? echo $arItemIDs['PROP'].$arProp['ID']; ?>_cont">
					<span class="text"><? echo htmlspecialcharsex($arProp['NAME']); ?>:</span>
						<span id="<? echo $arItemIDs['PROP'].$arProp['ID']; ?>_list">
							<?foreach ($arProp['VALUES'] as $arOneValue):
							$arOneValue['NAME'] = htmlspecialcharsbx($arOneValue['NAME']);?>
								<span 
									class="color"
									data-treevalue="<? echo $arProp['ID'].'_'.$arOneValue['ID'] ?>" 
									data-onevalue="<? echo $arOneValue['ID']; ?>"
									data-showmode="<? echo $arProp['SHOW_MODE']; ?>"
								>
									<img src="<? echo $arOneValue['PICT']['SRC']; ?>" alt="<? echo $arOneValue['NAME']; ?>" data-tooltip title="<? echo $arOneValue['NAME']; ?>">
								</span>
							<?endforeach;?>
						</span>
				</div>
			<?
		}
	}
	unset($arProp);
?>	