<div class="vr-popup" id="vregions-popup<?=$rand;?>">
	<div class="vr-popup__content">
		<a class="vr-popup__close"
		   onclick="OpenVregionsPopUp('close'); return false;">close
		</a>
		<div class="vr-popup__header">
			<div class="vr-popup__title"><?=GetMessage("SELECT_YOUR_REGION");?></div>
		</div>
		<div class="vr-popup__body clearfix">

			<div><p><?=GetMessage("SEARCH_YOU_REGION");?></p></div>

			<? if ($arParams["SHOW_SEARCH_FORM"] == 'Y'){ ?>
				<div class="vr-popup__search-wrap js-vregions-search-wrap">
					<input type="text"
						   class="vr-popup__search-input js-vregions-search-input"
						   placeholder="<?=GetMessage("FIND_YOUR_REGION");?>">
				</div>
			<? } ?>
			<? if (count($arResult["CHOSEN_ITEMS"])){ ?>
				<div class="vregions-chosen-list clearfix">
					<? foreach ($arResult["CHOSEN_ITEMS"] as $arItem){ ?>
						<a class="vregions-chosen-list__item <?=$arResult['CHOSEN_ITEMS_CLASS'];?>"
						   href="<?=$arItem["HREF"];?>"
						   data-cookie="<?=$arItem["~CODE"];?>"
						   onclick="ChangeVRegion(this); return false;"><?=$arItem["NAME"];?></a>
					<? } ?>
				</div>
			<? } ?>
			<? if ($arParams['ALLOW_OBLAST_FILTER'] == 'Y'){ ?>
				<div class="vregions-oblast">
					<label><?=GetMessage('OBLAST');?></label>
					<select name="VREGIONS_OBLAST"
							class="vregions-oblast__select js-vregions-oblast__select">
						<option value=""><?=GetMessage('ALL');?></option>
						<? foreach ($arResult["OBLASTI"] as $oblast){ ?>
							<option value="<?=$oblast;?>"><?=$oblast;?></option>
						<? } ?>
					</select>
				</div>
			<? } ?>
			<? /*
			<div class="vregions-list clearfix">
				<? foreach ($arResult["COLS"] as $items){ ?>
					<div class="vregions-list__col <?=$arResult["COL_CLASS"];?>">
						<? foreach ($items as $arItem){ ?>
							<a class="vr-popup__region-link <?=($arItem["ACTIVE"] ? 'vr-popup__region-link_active' : '');?> js-vr-popup__region-link"
							   href="<?=$arItem["HREF"];?>"
							   data-cookie="<?=$arItem["~CODE"];?>"
								<? if ($arParams['ALLOW_OBLAST_FILTER'] == 'Y'){ ?>
									data-oblast="<?=$arItem['OBLAST'];?>"
								<? } ?>
							   onclick="ChangeVRegion(this); return false;"><?=$arItem["NAME"];?></a>
						<? } ?>
					</div>
				<? } ?>
			</div>
			*/ ?>
			<? if ($arParams['SHOW_ANOTHER_REGION_BTN'] == 'Y'){ ?>
				<div class="vregions-another-region">
					<a href="#"
					   class="vregions-another-region__btn js-another-region-btn">
						<?=GetMessage('ANOTHER_REGION_BTN');?><br>
						<small><?=GetMessage('ANOTHER_REGION_ADDITION');?></small>
					</a>
				</div>
			<? } ?>
		</div>
	</div>
</div>
