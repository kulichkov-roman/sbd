<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
\Bitrix\Main\Loader::includeModule('yenisite.core');
$isAjax = Yenisite\Core\Tools::isAjax();
\Bitrix\Main\Localization\Loc::loadMessages($_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH . '/header.php');
$pathToRules = COption::GetOptionString(CRZBitronic2Settings::getModuleId(),'path_tu_rules_privacy',SITE_DIR.'personal/rules/personal_data.php');
if (!$isAjax) {
	$this->setFrameMode(true);
}
?>
<? if ($isAjax && $arResult['SUCCESS'] === true) {
	CRZBitronic2CatalogUtils::ShowMessage(array('MESSAGE' => $arResult['SUCCESS_TEXT'], 'TYPE' => 'OK')); ?>
	<script type="text/javascript">
		(typeof(jQuery) != 'undefined')
		&& (function ($) {
            $.fancybox.close();
			//$('#popup-notify').hide();
		})(jQuery);
	</script>
<? } else if ($isAjax && !empty($arResult['ERROR'])) {
	CRZBitronic2CatalogUtils::ShowMessage(array('MESSAGE' => $arResult['ERROR'], 'TYPE' => 'ERROR'));
} ?>
<? if (!$isAjax): ?>
	<? \Yenisite\Core\Ajax::saveParams($this, $arParams, $templateName); ?>
    <div class="popup" id="popup-notify">
        <div class="popup__main">
            <p class="popup__title"><?=GetMessage('RZ_SUBSCRIBE_TITLE');?></p>
            <p class="popup__text"><?=GetMessage('RZ_SUBSCRIBE_TEXT');?></p>
            <div class="notify-form">
                <form method="post" action="#" data-rzoption="captcha-when-in-stock" id="popup-notify_form"
                    <? \Yenisite\Core\Ajax::printAjaxDataAttr($this) ?>>
<? endif ?>
                    <label class="input-label"><?=GetMessage('RZ_EMAIL');?><span>*</span>:</label>
                    <input type="hidden" name="<?= $arResult['CODE'] ?>[PRODUCT]" id="product_subscribe" value=""/>
                    <input class="input" type="email" name="<?= $arResult['CODE'] ?>[EMAIL]" value="example@gmail.com">
                    <div class="checkbox active">
                        <label><input class="js-formstyler" type="checkbox" checked><?=GetMessage('BITRONIC2_I_ACCEPT')?></label>
                        <a class="checkbox__link" href="<?=$pathToRules?>"><?=GetMessage('BITRONIC2_POLITIC_PRIVICE')?></a>
                    </div>
                    <button class="notify-form__button button button_white" type="submit"><?=GetMessage('RZ_SUBMIT');?></button>
<? if (!$isAjax): ?>
                </form>
                <p class="notify-form__remark"><span>*</span><?=GetMessage('RZ_REQUIRED');?></p>
            </div>
        </div>
    </div>
<? endif ?>