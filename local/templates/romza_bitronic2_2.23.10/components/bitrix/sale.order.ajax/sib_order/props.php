<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
include($_SERVER["DOCUMENT_ROOT"] . $templateFolder . "/props_format.php");
use \Bitronic2\Mobile;
?>
<div class="order-step__heading">
    <div class="order-step__head"><span>1</span><?= GetMessage("BITRONIC2_SOA_TEMPL_PROP_INFO") ?></div>
    <div class="order-step__icon"></div>
    <a href="#" class="order-change js-order-change"><i class="icon-pencil"></i><span>Изменить</span></a>
</div>

<div class="order-step__cont">                                      
    <div class="contacts">
        <?if(!$USER->IsAuthorized()):?>
            <div class="warn">
                <p><strong>Уже регистрировались?</strong></p>
                <p><a class="warn__link js-go-tab-1" data-fancybox="" href="<?=Mobile::isMobile() ? '#popup-login-mobile' : '#popup-login'?>">Авторизуйтесь</a> для отслеживания статуса заказа в "Личном кабинете", накопления бонусов и автоматического заполнения форм</p>
            </div>
        <?endif?>
        <div class="box-form" id="sale_order_props">
            <?
                PrintPropsForm($arResult["ORDER_PROP"]["USER_PROPS_N"], $arParams["TEMPLATE_LOCATION"]);
                PrintPropsForm($arResult["ORDER_PROP"]["USER_PROPS_Y"], $arParams["TEMPLATE_LOCATION"]);
            ?>
                <!-- <div class="box-field">
                    <label class="box-field__label">Имя</label>
                    <div class="box-field__input">
                        <input class="input" type="text" placeholder="Имя">        
                    </div>
                </div>
                <div class="box-field">
                    <label class="box-field__label">Email</label>
                    <div class="box-field__input input-ok">
                        <input class="input " type="email" placeholder="Email">
                    </div>
                </div>
                <div class="box-field">
                    <label class="box-field__label">Телефон</label>
                    <div class="input-wrap js-focus input-error">
                        <div class="input-error__req">Обязательно</div>          
                        <input class="input mask-phone js-mask-phone" type="tel" placeholder="" required>
                        <a class="input-fix js-focus-fix" href="javascript:void(0);" style=""></a>
                    </div>    
                </div>    
                <div class="input-info">Без 8 и +7, например: 9122456587</div> -->
                <div class="checkbox">
                    <label><input class="js-formstyler" type="checkbox" id="personal_check" checked>Я согласен на обработку <a href="https://sibdroid.ru/personal/rules/personal_data.php" target="_blank">персональных данных</a>, а также с условиями <a href="https://sibdroid.ru/about/oferta/oferta.pdf" target="_blank">оферты</a></label>
                </div>  
                <div class="checkbox">
                    <label><input class="js-formstyler" type="checkbox" checked>Подписаться на рассылку выгодных предложений</label>
                </div> 
                <button class="button button_white js-next-step-order" data-block="props">Продолжить</button>                                                      
            
        </div>
    </div>
</div>
<?/*
$bHideProps = true;

if (is_array($arResult["ORDER_PROP"]["USER_PROFILES"]) && !empty($arResult["ORDER_PROP"]["USER_PROFILES"])):
    if ($arParams["ALLOW_NEW_PROFILE"] == "Y"):
        ?>
        <div class="delivery-info">
            <span class="text"><?= GetMessage("BITRONIC2_SOA_TEMPL_PROP_CHOOSE") ?></span>

            <select name="PROFILE_ID" id="ID_PROFILE_ID" onChange="SetContact(this.value)" class="select-styled">
                <option value="0"><?= GetMessage("BITRONIC2_SOA_TEMPL_PROP_NEW_PROFILE") ?></option>
                <?
                foreach ($arResult["ORDER_PROP"]["USER_PROFILES"] as $arUserProfiles) {
                    ?>
                    <option value="<?= $arUserProfiles["ID"] ?>"<? if ($arUserProfiles["CHECKED"] == "Y") echo " selected"; ?>><?= $arUserProfiles["NAME"] ?></option>
                    <?
                }
                ?>
            </select>
        </div>
        <?
    else:
        ?>
        <div class="delivery-info">
            <span class="text"><?= GetMessage("BITRONIC2_SOA_TEMPL_EXISTING_PROFILE") ?></span>

            <?
            if (count($arResult["ORDER_PROP"]["USER_PROFILES"]) == 1) {
                foreach ($arResult["ORDER_PROP"]["USER_PROFILES"] as $arUserProfiles) {
                    echo "<strong>" . $arUserProfiles["NAME"] . "</strong>";
                    ?>
                    <input type="hidden" name="PROFILE_ID" id="ID_PROFILE_ID" value="<?= $arUserProfiles["ID"] ?>"/>
                    <?
                }
            } else {
                ?>
                <select name="PROFILE_ID" id="ID_PROFILE_ID" onChange="SetContact(this.value)" class="select-styled">
                    <?
                    foreach ($arResult["ORDER_PROP"]["USER_PROFILES"] as $arUserProfiles) {
                        ?>
                        <option value="<?= $arUserProfiles["ID"] ?>"<? if ($arUserProfiles["CHECKED"] == "Y") echo " selected"; ?>><?= $arUserProfiles["NAME"] ?></option>
                        <?
                    }
                    ?>
                </select>
                <?
            }
            ?>
        </div>
        <?
    endif;
else:
    $bHideProps = false;
endif;*/
?>

<?/* $bExpanded = (!$bHideProps || $_POST["showProps"] == "Y"); ?>
<? if (!$arParams['SHOW_H3']): ?>
<div class="title-h3 buyer-info-header">
    <?
    else: ?>
    <div class="title-h3 buyer-info-header">
        <? endif; ?>

        <?//= GetMessage("BITRONIC2_SOA_TEMPL_BUYER_INFO") ?>
        <?
        if (array_key_exists('ERROR', $arResult) && is_array($arResult['ERROR']) && !empty($arResult['ERROR'])) {
            $bHideProps = false;
        }
        ?>
        <button type="button" class="buyer-info-toggle btn-expand <?= $bExpanded ? 'expanded' : '' ?>"></button>
        <input type="hidden" name="showProps" id="showProps" value="<?= ($_POST["showProps"] == 'Y' ? 'Y' : 'N') ?>"/>
<? if (!$arParams['SHOW_H3']): ?>
    </div>
<?
else: ?>
    </div>
<? endif; */?>

<? if (!CSaleLocation::isLocationProEnabled()): ?>
    <div style="display:none;">

        <? $APPLICATION->IncludeComponent(
            "bitrix:sale.ajax.locations",
            $arParams["TEMPLATE_LOCATION"],
            array(
                "AJAX_CALL" => "N",
                "COUNTRY_INPUT_NAME" => "COUNTRY_tmp",
                "REGION_INPUT_NAME" => "REGION_tmp",
                "CITY_INPUT_NAME" => "tmp",
                "CITY_OUT_LOCATION" => "Y",
                "LOCATION_VALUE" => "",
                "ONCITYCHANGE" => "submitForm()",
            ),
            null,
            array('HIDE_ICONS' => 'Y')
        ); ?>

    </div>
<? endif ?>
