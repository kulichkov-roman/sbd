<div class="wait">
<?
    $cityName = 'Новосибирске';
    if(\Bitrix\Main\Loader::includeModule('sib.core')){
        if(\Sib\Core\Catalog::isMskRegion($_SESSION['VREGIONS_REGION']['ID'])){
            $cityName = 'Москве';
        }
    }
    
?>
    <p>Товар поступит на склад в <?=$cityName?> <?= date('d.m.Y', strtotime($arStatus['DATE'])) ?>
    <br>
    <?if($arResult['MIN_PRICE']['VALUE'] || $arResult['PRICE_DISCOUNT']):?> <?if(!$arResult['IS_NEW_PRODUCT']):?>последняя цена продажи<?else:?>ориентировочная цена<?endif?> <span><?=$arResult['PRICE_DISCOUNT']?:$arResult['MIN_PRICE']['VALUE'] ?></span><?endif;?>
    </p>
    <?if (\Bitrix\Main\Loader::includeModule('yenisite.oneclick')) {
            global $rz_b2_options;
            $rz_b2_options["captcha-quick-buy"] = 'N';
            $APPLICATION->IncludeComponent(
                "yenisite:oneclick.buy",
                "bitronic2_detail_available",
                array(
                    "COMPONENT_TEMPLATE" => "bitronic2",
                    "IBLOCK_TYPE" => "catalog",
                    "IBLOCK_ID" => "6",
                    "IBLOCK_ELEMENT_ID" => $arResult['ID'],
                    "FORM_ID" => "detail_available",
                    "PERSON_TYPE_ID" => "1",
                    "SHOW_FIELDS" => array(
                        0 => "FIO",
                        1 => "EMAIL",
                        2 => "PHONE",
                    ),
                    "REQ_FIELDS" => array(
                        0 => "FIO",
                        1 => "EMAIL",
                        2 => "PHONE",
                    ),
                    "ALLOW_AUTO_REGISTER" => "N",
                    "MESSAGE_OK" => "Ваша заявка принята, её номер - #ID#. Менеджер свяжется с вами в ближайшее время. Спасибо, что выбрали нас!",
                    "PAY_SYSTEM_ID" => "11",
                    "DELIVERY_ID" => "412",
                    "AS_EMAIL" => "0",
                    "AS_NAME" => "0",
                    "FIELD_CLASS" => "textinput",
                    "FIELD_PLACEHOLDER" => "Y",
                    "FIELD_QUANTITY" => "N",
                    "SEND_REGISTER_EMAIL" => "N",
                    "EMPTY" => $arParams["EMPTY"],
                    "USE_CAPTCHA" => $rz_b2_options["captcha-quick-buy"],
                    "USE_CAPTCHA_FORCE" => $rz_b2_options["captcha-quick-buy"],
                    //"USER_REGISTER_EVENT_NAME" => "[SALE_NEW_ORDER]",
                    "OFFER_PROPS" => $arProps,
                    "COMMENTS" => "Заявка на товар " . $arResult['NAME'],
                    "COMPOSITE_FRAME_MODE" => "A",
                    "COMPOSITE_FRAME_TYPE" => "AUTO"
                ),
                false
            );
        }
    ?>                                                       
</div>