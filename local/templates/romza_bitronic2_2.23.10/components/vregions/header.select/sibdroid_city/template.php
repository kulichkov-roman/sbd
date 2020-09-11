<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<? $this->setFrameMode(true); ?>
<?
$rand = rand();
?>
<? if (!empty($arResult["ITEMS"])){ ?>
	<div class="vr-template <? if ($arParams["FIXED"] == "Y"){ ?>vr-template__fixed<?}?>"
		 data-rand="<?=$rand;?>">
		<span class="vr-template__label"><?=html_entity_decode($arParams["STRING_BEFORE_REGION_LINK"] ?: GetMessage("YOUR_REGION"));?></span>
		<a class="vr-template__link js-vr-template__link-region-name <?=($arParams['INCLUDE_SESSION_ARRAY_IN_CACHE'] == 'N' ? 'vr-template__link_hidden' : '');?>"
		   href="#"
		   onclick="OpenVregionsPopUp('open', 'vregions-popup<?=$rand;?>', 'vregions-sepia<?=$rand;?>'); return false;"><?=(strlen($arResult["CURRENT_SESSION_ARRAY"]["NAME"]) ? $arResult["CURRENT_SESSION_ARRAY"]["NAME"] : $arResult["DEFAULT"]["NAME"]);?></a>
	</div>
	<div id="vregions-sepia<?=$rand;?>"
		 class="vregions-sepia"
		 onclick="OpenVregionsPopUp('close'); return false;"></div>
	<? if ($arParams["SHOW_POPUP_QUESTION"] == "Y"){ ?>
		<? include "popup-question.php"; ?>
	<? } ?>
	<? include "popup.php"; ?>
<? }else{ ?>
	<?=GetMessage("ERROR_OF_NO_ELS");?>
<? } ?>
