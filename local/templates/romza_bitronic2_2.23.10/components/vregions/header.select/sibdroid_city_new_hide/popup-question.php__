<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<div class="vr-popup vregions-popup-que"
	 id="vregions-popup-que<?=$rand;?>">
	<div class="vr-popup__content vr-popup__content_que">
		<!--noindex-->
		<a class="vr-popup__close"
		   onclick="OpenVregionsPopUp('close'); return false;">close
		</a>
		<div class="vr-popup__header">
			<div class="vr-popup__title"><?=$arParams["POPUP_QUESTION_TITLE"] ? $arParams["POPUP_QUESTION_TITLE"] : GetMessage("DID_WE_GUESS");?></div>
		</div>
		<div class="vr-popup__body clearfix">
			<div class="vr-popup__paragraph"><?=GetMessage("YOUR_REGION_IS");?>
				<span class="vr-popup__suggested-region"
					  id="suggested-region"></span>
				<b>?</b>
			</div>
			<a href="#"
			   onclick="ChangeVRegion(this); return false;"
			   id="we_guessed"
			   data-cookie=""
			   class="vr-popup__button"><?=GetMessage("YES_MY_REGION");?></a>
			<a class="vr-popup__button vr-popup__button_danger"
			   onclick="OpenVregionsPopUp('open', 'vregions-popup<?=$rand;?>', 'vregions-sepia<?=$rand;?>');"><?=GetMessage("NOT_MY_REGION");?></a>
		</div>
		<!--/noindex-->
	</div>
</div>
