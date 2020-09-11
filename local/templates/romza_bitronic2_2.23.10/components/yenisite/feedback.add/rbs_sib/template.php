<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
//if($_POST['rz_ajax'] == 'y'){
    if(!empty($arResult['ERROR'])){
        CRZBitronic2CatalogUtils::ShowMessage(Array("MESSAGE" => $arResult['ERROR'], "TYPE" => "ERROR"));
        return;
    } else if($arResult['SUCCESS'] && $arResult['SUCCESS_TEXT']) {
        CRZBitronic2CatalogUtils::ShowMessage(Array("MESSAGE" => $arResult['SUCCESS_TEXT'], "TYPE" => "OK"));
        return;
    }
//}
?>
<div>
	<form method='POST' id="ys-guestbook" name="guestbook" action="<?=$APPLICATION->GetCurPageParam()?>" enctype="multipart/form-data">
            <?foreach($arResult['FIELDS'] as $arItem):?>
                <? if ($arItem['PROPERTY_TYPE'] == 'E'): ?>
                    <?=$arItem['HTML'];?>
                <? endif; ?>
            <?endforeach;?>
                <div id="ys-feedback_add">
                    <input type="hidden" name="rz_ajax" value="y">
                    
                    <? if (!empty($arResult['SECTIONS_SELECT'])): ?>
                        <?= GetMessage('SECTION_SELECT') . ': ' . $arResult['SECTIONS_SELECT']; ?>
                    <? endif; ?>
                    
                    
                        
                            <div class='contacts-form__username'>
                                <label class='contacts__label'><?=$arResult['FIELDS'][1]['NAME'];?><?=($arResult['FIELDS'][1]['IS_REQUIRED'] == 'Y')?'<span style="color: red">*</span>':'';?>:</label>
                                <?=$arResult['FIELDS'][1]['HTML'];?>
                            </div>
                            <div class='contacts-form__email'>
                                <label class='contacts__label contacts__label_email'><?=$arResult['FIELDS'][2]['NAME'];?><?=($arResult['FIELDS'][2]['IS_REQUIRED'] == 'Y')?'<span style="color: red">*</span>':'';?>:</label>
                                <?=$arResult['FIELDS'][2]['HTML'];?>
                            </div>
                            <div class='contacts-form__text'>
                                <label class='contacts__label'><?=$arResult['FIELDS'][0]['NAME'];?><?=($arResult['FIELDS'][0]['IS_REQUIRED'] == 'Y')?'<span style="color: red">*</span>':'';?>:</label>
                                <?=$arResult['FIELDS'][0]['HTML'];?>
                            </div>
                        
                    
                    <?//echo "<pre style='text-align:left;'>";print_r($arResult);echo "</pre>";?>
                    <? if ($arParams['TEXT_SHOW'] == 'Y'): ?>
                        <div class='field'>
                            <label><?=GetMessage("MESSAGE");?><?=($arParams['TEXT_REQUIRED'] == 'Y')? '<span style="color: red">*</span>': '' ?>:</label>
                            <textarea name='<?=$arResult['CODE'];?>[text]'><?=$arResult['DATA']['text'];?></textarea>
                        </div>
                    <? endif; ?>
                    <?if(!empty($arResult["CAPTCHA_CODE"])):?>
                        <div class="ys-captcha contacts-form__captcha">
                                <p class='contacts__label contacts__label_captcha'><?=GetMessage("CAPTCHA_TITLE")?>:</p>
                                <div class="contacts-form__captcha-image">
                                <img alt="<?=GetMessage("CAPTCHA_ALT")?>" src="/bitrix/tools/captcha.php?captcha_code=<?=$arResult["CAPTCHA_CODE"]?>" />
                                </div>
                                <br>
                                <input class="contacts__input contacts-form__captcha-input" type="text" name="captcha_word" /><br />
                                <input type="hidden" name="captcha_code" value="<?=$arResult["CAPTCHA_CODE"]?>"/>
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
                    <br/>
                    <div><button class='contacts-form__button'><?=GetMessage("SEND");?></button></div>
                </div>
	</form>
</div>