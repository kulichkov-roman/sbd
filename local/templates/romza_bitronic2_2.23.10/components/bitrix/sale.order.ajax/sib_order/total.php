<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
use \Bitronic2\Mobile;
$bDefaultColumns = $arResult["GRID"]["DEFAULT_COLUMNS"];
$colspan = ($bDefaultColumns) ? count($arResult["GRID"]["HEADERS"]) : count($arResult["GRID"]["HEADERS"]) - 1;
$bPropsColumn = false;
$bUseDiscount = false;
$bPriceType = false;
$bShowNameWithPicture = ($bDefaultColumns) ? true : false; // flat to show name and picture column in one column
global $activeChoose;

if(!empty($_SESSION['TYPE_DELIVERY'])){
    $_SESSION['DELIVERY_NAME_CONFIRM'] = $activeChoose['DELIVERY'];
}


$arResult['DISCOUNT_PRICE'] = $arResult['DISCOUNT_PRICE'] >= 0 ? $arResult['DISCOUNT_PRICE'] : 0;
?>
<div class="total total_order">
    <div class="total__heading">
        <?=GetMessage('RBS_YOUR_ORDER');?>
    </div>
    <ul class="order-list">
    <?foreach ($arResult["GRID"]["ROWS"] as $k => $arData):
            $arItem = $arData["data"];
            //global $USER; if($USER->IsAdmin()){echo '<pre>'; print_r($arItem); echo '</pre>';};
    ?>
        <li class="order-list__item">
            <?=$arItem["NAME"]?> <?if($arItem["QUANTITY"] > 1):?>(<?=$arItem["QUANTITY"]?>)<?endif?>
            <?=CRZBitronic2CatalogUtils::getElementPriceFormat(false, $arItem['SUM_BASE'], $arItem['SUM_BASE_FORMATED'], array('ID'=>'ITEM_PRICE_' . $arItem["ID"]))?>
        </li>
	<?endforeach;?>
    </ul>
    <div class="total__delivery">
        <div class="total__heading">
            <?=GetMessage('RBS_YOUR_PAY_DELIVERY');?>
        </div>
        <p><?=GetMessage('BITRONIC2_SOA_TEMPL_SUM_DELIVERY');?> <span><?=!empty($_SESSION['TYPE_DELIVERY']) ? $activeChoose['DELIVERY'] : 'Не выбрано'?></span></p>
        <p><?=GetMessage('BITRONIC2_SOA_TEMPL_PAY');?> <span><?=$activeChoose['PAYSYSTEM']?></span></p>
    </div>
    <div class="total__info">
        <p><?=GetMessage('BITRONIC2_SOA_TEMPL_SUM_DISCOUNT');?> <?=CRZBitronic2CatalogUtils::getElementPriceFormat(false, $arResult['DISCOUNT_PRICE'], $arResult["DISCOUNT_PRICE_FORMATED"], array('ID'=>'DISCOUNT_PRICE'))?></p>
        <p><?=GetMessage('BITRONIC2_SOA_TEMPL_SUM_DELIVERY_2');?> <?=CRZBitronic2CatalogUtils::getElementPriceFormat(false, $arResult['DELIVERY_PRICE'], $arResult["DELIVERY_PRICE_FORMATED"], array('ID'=>'DELIVERY_PRICE'))?></p></p>
    </div>
    <div class="total__btm">
        <div class="total__sum">
        <?=GetMessage('BITRONIC2_SOA_TEMPL_SUM_IT');?> <?=CRZBitronic2CatalogUtils::getElementPriceFormat(false, $arResult['ORDER_TOTAL_PRICE'], $arResult["ORDER_TOTAL_PRICE_FORMATED"], array('ID'=>'TOTAL_PRICE'))?>
        </div>
        <div class="total__btn">
            <button class="button <?if(!$_SESSION['CAN_ORDER']):?>button_disabled<?endif?>" onclick="submitForm('Y'); return false;" id="ORDER_CONFIRM_BUTTON"><?=GetMessage("BITRONIC2_SOA_TEMPL_BUTTON")?></button>
            <!-- <button class="button button_disabled" type="submit" disabled>Оформить заказ</button> -->
        </div>
    </div>
    <div class="comment-order-wrap">
        <a href="#" class="total__link"><?=GetMessage('BITRONIC2_SOA_TEMPL_SUM_COMMENTS');?></a>
        <textarea class="comment-order" name="ORDER_DESCRIPTION" id="ORDER_DESCRIPTION" style=""><?=$arResult["USER_VALS"]["ORDER_DESCRIPTION"]?></textarea>
    </div>                    
</div>
<?if(Mobile::isMobile()):?>
<div class="rbs-mobile-order-button is-mobile-only" <?if(!$_SESSION['CAN_ORDER']):?>style="display:none;"<?endif?>>
    <div class="total-left">
        <p><?=CRZBitronic2CatalogUtils::getElementPriceFormat(false, $arResult['ORDER_TOTAL_PRICE'], $arResult["ORDER_TOTAL_PRICE_FORMATED"], array('ID'=>'TOTAL_PRICE'))?></p>
    </div>
    <div class="total-right">
        <button class="button" onclick="submitForm('Y'); return false;" id="ORDER_CONFIRM_BUTTON_MOBILE"><?=GetMessage("BITRONIC2_SOA_TEMPL_BUTTON")?></button>
    </div>
</div>
<?endif?>
<!-- <div class="total__btn_mobile <?/*if($_POST["is_ajax_post"] != "Y"):?>hide<?endif*/?>">
    <button class="button" onclick="submitForm('Y'); return false;" id="ORDER_CONFIRM_BUTTON_MOBILE"><?//=GetMessage("BITRONIC2_SOA_TEMPL_BUTTON")?></button>
</div> -->