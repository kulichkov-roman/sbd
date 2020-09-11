<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<? 
$this->setFrameMode(true);
$isAjax = $_SERVER['SCRIPT_URL'] == "/ajax/sib/callme.php";
//global $USER; if($USER->IsAdmin()){echo '<pre style="max-height:100px;overflow:auto;">'; print_r($_SERVER['SCRIPT_URL']); echo '</pre>';};
if(!$isAjax):
?>
    <p class="call-form__title"><?=$arParams['TITLE']?></p>
    <p class="call-form__descr">Оставьте нам свой номер телефона и мы Вам перезвоним!</p>
    <form id="rbs-send-call" method="POST" action="<?=$APPLICATION->GetCurPageParam()?>" enctype="multipart/form-data">
        <input type="hidden" value="Y" name="ajax">
        <?foreach($arResult['FIELDS'] as $arItem):?>
            <?if(in_array($arItem['CODE'], $arParams['HIDE_PROPS'])):?>
                <input name="romza_feedback[<?=$arItem['CODE'];?>]" type="hidden" value="<?=$arParams[$arItem['CODE']]?>">
            <?else:?>
                <div class="input-wrap js-focus">
                    <input class="input" name="romza_feedback[<?=$arItem['CODE'];?>]" type="text" required>
                    <a class="input-fix js-focus-fix" href="javascript:void(0);">
                        <?if($arItem['CODE'] === 'PHONE'):?>
                            +7(___)___ - ____
                        <?else:?>
                            <?=$arItem['NAME'];?>
                        <?endif?>
                    </a>
                </div>
            <?endif?>
        <?endforeach;?>
        <?if(!empty($arResult["CAPTCHA_CODE"])):?>
            <div class="input-wrap js-focus rbs-captcha">
                <img alt="<?=GetMessage("CAPTCHA_ALT")?>" src="/bitrix/tools/captcha.php?captcha_code=<?=$arResult["CAPTCHA_CODE"]?>" />
                <input class="input" type="text" name="captcha_word" required/><br />
                <input type="hidden" name="captcha_code" value="<?=$arResult["CAPTCHA_CODE"]?>"/>
                <a class="input-fix js-focus-fix" href="javascript:void(0);"><?=GetMessage("CAPTCHA_TITLE")?></a>
            </div>
        <?endif;?>
        <?
            $APPLICATION->IncludeComponent(
                "developx:gcaptcha",
                "",
                [], 
                false
            );
        ?>
        <div class="info"></div>
        <button class="call-form__button button" type="submit">Отправить</button>
    </form>
<?elseif($arResult['ERROR']):?>
    <div class="error"><?=$arResult['ERROR']?></div>
<?else:?>
    <div class="success"><?=$arParams['SUCCESS_TEXT']?></div>
<?endif;?>