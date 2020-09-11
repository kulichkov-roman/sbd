<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
//Not cacheable
$this->setFrameMode(true);
if($arParams['EMPTY'])
{
	echo '<div>', GetMessage('BITRONIC2_ONECLICK_TITLE'), '</div>';
	return;
}

if (!empty($arResult['ERROR']))
{
	$err = '';
	foreach($arResult['ERROR'] as $arError)
	{
		$err .= $arError/*['TEXT']*/.'<br>';
	}
	CRZBitronic2CatalogUtils::ShowMessage(Array("MESSAGE" => $err, "TYPE" => "ERROR"));
}

if (isset($arResult['SUCCESS']))
{
	CRZBitronic2CatalogUtils::ShowMessage(Array("MESSAGE" => $arResult['SUCCESS'], "TYPE" => "OK"));
	return;
}
if(empty($arResult['FIELDS']))
{
	CRZBitronic2CatalogUtils::ShowMessage(Array("MESSAGE" => GetMessage('BITRONIC2_ONECLICK_EMPTY_FILEDS'), "TYPE" => "ERROR"));
	return;
}

\Bitrix\Main\Localization\Loc::loadMessages($_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH . '/header.php');
$pathToRules = COption::GetOptionString(CRZBitronic2Settings::getModuleId(),'path_tu_rules_privacy',SITE_DIR.'personal/rules/personal_data.php');
?>

<div class="wait__email">
	<p>Узнать о поступлении товара вы сможете, указав номер вашего телефона</p>
	<form id="<?=$arParams['FORM_ID']?>" method="post" action="<?= $APPLICATION->GetCurPage(true) ?>" class="contacts-form__email">
		<input type="hidden" name="MESSAGE_OK" value="<?= htmlspecialcharsbx($arParams['MESSAGE_OK']) ?>"/>
		<input type="hidden" name="FORM_ID" value="<?=$arParams['FORM_ID']?>"/>
		<input type="hidden" name="BUY_SUBMIT" value="Y"/>
		<input type="hidden" name="privacy_policy" value="Y"/>
		<input type="hidden" name="rz_ajax" value="Y"/>
		<input type="hidden" name="template_name" value="<?=$templateName?>"/>
		<?= bitrix_sessid_post() ?>
		<? foreach($arResult['HIDDEN_FIELDS'] as $arField) {
			echo $arField['HTML'],"\n";
		}?>
		<input type="hidden" name="id" value="<?= $arParams['IBLOCK_ELEMENT_ID'] ?>" title=""/>
		<input type="hidden" name="RZ_BASKET" value="<?=htmlspecialcharsbx($_REQUEST['RZ_BASKET'])?>" />

		<input type="hidden" name="FIELDS[FIO]">
		<input type="hidden" name="FIELDS[EMAIL]">

		<input type="text" name="FIELDS[PHONE]" placeholder="Введите ваш телефон" class="contacts__input" required>

		<button class="button" type="submit">Отправить</button>    
		
		<label style="display:none;">
			<input value="Y" type="checkbox" name="privacy_policy" checked>
		</label>
	
	</form>
	<div class="checkbox active">
		<label><input name="agree_policy_detail_phone" class="js-formstyler" type="checkbox" checked>Я согласен с условиями</label>
		<a class="checkbox__link" href="<?=$pathToRules?>">обработки персональных данных</a>
		<span class="card-tooltipe agree-tooltip">Необходимо согласилться с условиями обработки персональных данных</span>
	</div>
	<div class="wait__info">При поступлении товара, цена может отличаться</div>
</div>           