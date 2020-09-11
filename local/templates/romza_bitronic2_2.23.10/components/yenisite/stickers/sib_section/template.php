<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if (method_exists($this, 'setFrameMode')) $this->setFrameMode(true);?>
<div class="catalog-labels">
    <?foreach ($arResult['CUSTOM'] as $arCustom):?>
        <div class="catalog-label <?=$arCustom['CLASS']?>"><?=$arCustom['TEXT']?></div>
    <?endforeach?>

    <?if ('N' != $arParams['STICKER_CATCHBUY'] && $arResult["CATCHBUY"] || $arParams['SKU_EXT'] === true):?>
        <span class="catalog-label catalog-label_hurry" ><?=GetMessage('STICKER_SECT_CATCHBUY')?></span>
    <?endif?>

    <?if ($arResult["NEW"]):?>
        <span class="catalog-label catalog-label_new" ><?=GetMessage('STICKER_SECT_NEW')?></span>
    <?endif?>
    
    <?if ($arResult["SALE"] && $arResult["SALE_DISC"] > 0):?>
        <span class="catalog-label catalog-label_discount" ><?=GetMessage('STICKER_SECT_SALE')?> <?=round($arResult["SALE_DISC"])?></span>
    <?endif;?>

    <?if ($arResult["HIT"]):?>
        <span class="catalog-label catalog-label_hit" "><?=GetMessage('STICKER_SECT_HIT')?></span>
    <?endif?>

    <?if ($arResult["BESTSELLER"]):?>
        <span class="catalog-label catalog-label_rec" ><?=GetMessage('STICKER_SECT_BESTSELLER')?></span>
    <?endif?>
    
</div>

