<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Sale\DiscountCouponsManager;
use Bitrix\Main\Loader;
use Yenisite\Core\Ajax;

include 'include/section_prepare_params.php';

global $rz_b2_options;
global $arrBasketFilter;

 //echo '<pre>';
 //print_r($arResult["GRID"]["ROWS"][0]["RECOMMEND"]);
 //echo '</pre>';

if (!empty($arResult["ERROR_MESSAGE"]))
	ShowError($arResult["ERROR_MESSAGE"]);

$isShowedFirstSection = false;
$showLastCoupon = Ajax::isAjax() && !empty($arResult['LAST_INCORRECT_COUPON']);
$bSibCore = \Bitrix\Main\Loader::includeModule('sib.core');
?>

<div class="cart__items" id="basket-big">
	<div id="basket_items">
    <?foreach ($arResult["GRID"]["ROWS"] as $k => $arItem):?>
		<?if ($arItem["DELAY"] == "N" && !isset($arItem["NOT_AVAILABLE"]) && $arItem["NOT_AVAILABLE"] != true):?>
			<div class="cart-item cart-item_small" id="<?=$arItem["ID"]?>" data-product="<?=$arItem["PRODUCT_ID"]?>">
				<div class="cart-item__table" >
					<div class="cart-item__img">
						<img itemprop="contentUrl" title="<?=$arItem['NAME']?>" class="" src="<?=$arItem['PICTURE_PRINT']['SRC_JPG']?>" alt="<?=$arItem['NAME']?>">
					</div>
					<div class="cart-item__heading">
                        <?if($bSibCore):?>
                            <?if(\Sib\Core\Helper::isSmarPhoneItem($arItem['PRODUCT_ID'])):?>
                                <div class="rbs-pre-head">Смартфон</div>
                            <?endif?>
                        <?endif?>
                        <?if($arItem["PRICE"] == 0):?>
                            <div class="rbs-pre-head">Подарок</div>
                        <?endif?>
						<div class="cart-item__head"><?=$arItem['NAME']?></div>
					</div>
					<div class="qty" data-tooltip data-placement="right">
						<?
							if (!isset($arItem["MEASURE_RATIO"]))
							{
								$arItem["MEASURE_RATIO"] = 1;
							}
							$ratio = isset($arItem["MEASURE_RATIO"]) ? $arItem["MEASURE_RATIO"] : 1;
							$max = isset($arItem["AVAILABLE_QUANTITY"]) ? "max=\"".$arItem["AVAILABLE_QUANTITY"]."\"" : "";
							$useFloatQuantity = ($arParams["QUANTITY_FLOAT"] == "Y") ? true : false;
							$useFloatQuantityJS = ($useFloatQuantity ? "true" : "false");
						?>
						<button 
							id="QUANTITY_DOWN_<?=$arItem['ID']?>" 
							class="qty__btn down decrease" 
							<?=($arItem['QUANTITY']<=$ratio?'disabled':'')?>
							data-ratio="<?=$ratio?>">
							<span>-</span>
						</button>
						<div class="qty__num"><input class="qty-val" type="text" id="QUANTITY_INPUT_<?=$arItem["ID"]?>" name="QUANTITY_INPUT_<?=$arItem["ID"]?>" value="<?=$arItem["QUANTITY"]?>" onchange="updateQuantity('QUANTITY_INPUT_<?=$arItem["ID"]?>', '<?=$arItem["ID"]?>', <?=$ratio?>, <?=$useFloatQuantityJS?>)"/></div>
						<button
							class="qty__btn up"
							data-ratio="<?=$ratio?>">
							<span>+</span>
						</button>
						<input type="hidden" id="QUANTITY_<?=$arItem['ID']?>" name="QUANTITY_<?=$arItem['ID']?>" value="<?=$arItem["QUANTITY"]?>" />
						<input type="hidden" name="DELAY_<?=$arItem["ID"]?>" id="DELAY_<?=$arItem["ID"]?>" value="N"/>
						<input type="hidden" name="DELETE_<?=$arItem["ID"]?>" id="DELETE_<?=$arItem["ID"]?>" value="N"/>
                    </div>
                    <div class="price__total mobile">
                        <p class="current-price"><?=CRZBitronic2CatalogUtils::getElementPriceFormat($arItem['CURRENCY'], $arItem["SUM_NOT_FORMATED"], $arResult["SUM"], array('ID'=> 'sum_'.$arItem["ID"]))?></p>
                    </div>
					<div class="price price_sale">
						<div class="price__qty"><?=$arItem["QUANTITY"]?> шт. x <?=CRZBitronic2CatalogUtils::getElementPriceFormat($arItem['CURRENCY'], $arItem["PRICE"], $arResult["PRICE_FORMATED"], array('ID'=> 'current_price_'.$arItem["ID"]))?></div>
						<div class="price__total">
							<p class="current-price"><?=CRZBitronic2CatalogUtils::getElementPriceFormat($arItem['CURRENCY'], $arItem["SUM_NOT_FORMATED"], $arResult["SUM"], array('ID'=> 'sum_'.$arItem["ID"]))?></p>
						</div>
						<?/*if ($arItem["DISCOUNT_PRICE_PERCENT"] != 0):?>
						<div class="price__old" id="discount_value_<?=$arItem['ID']?>">
							<?=CRZBitronic2CatalogUtils::getElementPriceFormat($arItem['CURRENCY'], $arItem["FULL_PRICE"], $arResult["FULL_PRICE_FORMATED"], array('ID'=> 'old_price_'.$arItem["ID"]))?>
						</div>
						<div class="price__sale">
							<?=GetMessage('BITRONIC2_SALE_CONTENT_DISCOUNT')?> <strong><?=$arItem['DISCOUNT_PRICE_PERCENT_FORMATED']?></strong>
						</div>
                        <?endif*/?>	
                        <a href="<?=htmlspecialcharsbx(str_replace("#ID#", $arItem["ID"], $arUrls["delete"]))?>" class="cart-item__del" data-action="delete" data-id="<?=$arItem['ID']?>" data-tooltip title="<?=GetMessage('BITRONIC2_SALE_DELETE')?>" data-placement="bottom"><?=GetMessage('BITRONIC2_SALE_DELETE')?></a>
					</div>
				</div>
                <?if ( !empty($arItem["RECOMMEND"]) ):?>
                    <div class="cart-item__info">
                        <p><?=GetMessage('BITRONIC2_SALE_ACCESSOIRES')?></p>
                    </div>
                <?endif?>
				<div class="cart-filter">
				<?if (!empty($arItem["RECOMMEND"])):?>
					<ul class="cart-filter-list">
                        <?foreach ($arItem["RECOMMEND"] as $index => $arSection):?>
                            <li class="cart-filter-list__item">
                                <a class="cart-filter-list__link" href="" data-section="<?=$arSection['SECTION_ID']?>"> <?=$arSection["NAME"]?> <span>(<?=$arSection["COUNTS"]?>)</span></a>
                            </li>
                        <?endforeach?>
                        <?/*?>
                        <li class="cart-filter-list__item last-item">
                            <a class="cart-filter-list__link last_link" href=""><span><?='Смотреть все'?></span></a>
                        </li>
                        <?*/?>
					</ul>
				<?endif?>	
				</div>
                <div class="cart-slider">
                <? //global $USER; if($USER->IsAdmin()){echo '<pre>'; print_r($arItem["RECOMMEND"]); echo '</pre>';}; ?>
                    <?if ( !empty($arItem["RECOMMEND"]) ):?>
                        <?if (!$isShowedFirstSection):?>
                            <?
                                $firstSection = current($arItem["RECOMMEND"]);
                                //global $USER; if($USER->IsAdmin()){echo '<pre>';print_r($firstSection);echo '</pre>';}
                                if($firstSection['VALUES'][0] == 'HYDROGEL'){
                                    $arrBasketFilter = array("=ID" => [0]);
                                    $arSectionParams['IS_SMARTPHONE_ITEM'] = 'Y';
                                } else {
                                    $arrBasketFilter = array("=ID" => $firstSection['VALUES']);
                                    $arSectionParams['IS_SMARTPHONE_ITEM'] = 'N';
                                }
                                $APPLICATION->IncludeComponent(
                                    "bitrix:catalog.section",
                                    "sib_basket",
                                    $arSectionParams
                                );

                                $isShowedFirstSection = true;
                            ?>
                        <?endif?>
                    <?endif?>
                </div>
			</div>
		<?endif?>
	<?endforeach;?>
	</div>
</div>
</div>
<div class="total">
    <div class="total__sum">
        <p class="cart-discount-price"><?=GetMessage('BITRONIC2_SALE_DISCOUNT')?><?=CRZBitronic2CatalogUtils::getElementPriceFormat(false, $arResult["DISCOUNT_PRICE_ALL"], $arResult["DISCOUNT_PRICE_ALL_FORMATED"], array('ID'=>'DISCOUNT_PRICE_ALL'))?></p>
        <p class="cart-total-price"><?=GetMessage('BITRONIC2_SALE_TOTAL')?><?=CRZBitronic2CatalogUtils::getElementPriceFormat(false, $arResult["allSum"], $arResult['allSum_FORMATED'], array('ID'=>'allSum_FORMATED'))?></p>
    </div>
    <div class="total__text">
        <?=GetMessage('BITRONIC2_SALE_NOT_DEVILERY')?>
    </div>
    <div class="total__btm">
        <a href="<?=$arParams['PATH_TO_ORDER']?>" onclick="checkOut();">
            <button type="button" class="button"><?=GetMessage('BITRONIC2_SALE_ORDER')?></button>
        </a>
        <div class="entered_coupon">
            <input class="total__input" id="coupon" name="COUPON"
                   value="<?if ($showLastCoupon):?><?=$arResult['LAST_INCORRECT_COUPON']['COUPON']?><?endif?>"
                   onchange="enterCoupon();" class="textinput"	placeholder="Ввести промокод">
            <span class="b-submit-btn active icon-arrow-top" ></span>
            <?if ($showLastCoupon):?>
                <span class="incorrect-coupon-text"><?=GetMessage('INCORRECT_COUPON_TEXT')?></span>
            <?endif?>
        </div>
        <?if (!empty($arResult['COUPON_LIST']))
            {
                foreach ($arResult['COUPON_LIST'] as $oneCoupon)
                {
                    if ($oneCoupon['STATUS'] === DiscountCouponsManager::STATUS_NOT_FOUND ||
                        $oneCoupon['STATUS'] === DiscountCouponsManager::STATUS_FREEZE
                    )
                        continue;

                    $couponClass = $oneCoupon['STATUS'] === DiscountCouponsManager::STATUS_APPLYED ? 'valid' : 'disabled';
                    ?>
                    <div class="entered_coupon entered <?=$couponClass?>">
                        <input title="<?echo (is_array($oneCoupon['CHECK_CODE_TEXT']) ? implode(",\n", $oneCoupon['CHECK_CODE_TEXT']) : $oneCoupon['CHECK_CODE_TEXT']);?>" class="total__input inputed" id="coupon" readonly name="COUPON" value="<?=htmlspecialcharsbx($oneCoupon['COUPON']);?>">
                        <button type="button" class="apply-coupon <?=$couponClass?>" data-coupon="<? echo htmlspecialcharsbx($oneCoupon['COUPON']); ?>">
                            <i class="icon-<?=$couponClass?> flaticon-<?=($couponClass=='valid')?'check33':'x5'?>"></i>
                        </button>
                    </div>
                    <?
                }
                unset($oneCoupon);
            }
        ?>
    </div>
</div>
<div class="is-mobile-only">
    <div class="total-left">
        <p><?=CRZBitronic2CatalogUtils::getElementPriceFormat(false, $arResult["allSum"], $arResult['allSum_FORMATED'], array('ID'=>'allSum_FORMATED'))?></p>
    </div>
    <div class="total-right">
        <a href="<?=$arParams['PATH_TO_ORDER']?>" onclick="checkOut();">
            <button type="button" class="button"><?=GetMessage('BITRONIC2_SALE_ORDER')?></button>
        </a>
    </div>
</div>