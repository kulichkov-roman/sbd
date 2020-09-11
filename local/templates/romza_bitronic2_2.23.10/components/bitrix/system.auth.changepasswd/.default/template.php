<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
// ShowMessage($arParams["~AUTH_RESULT"]);
$APPLICATION->AddChainItem($APPLICATION->GetTitle());

$arFormFields = array(
	'LOGIN' => array(
		'REQUIRED' => true,
	),
	'CHECKWORD' => array(
		'REQUIRED' => true,
	),
	'PASSWORD' => array(
		'REQUIRED' => true,
	),	
	'CONFIRM_PASSWORD' => array(
		'REQUIRED' => true,
	),	
);
$arResult['USER_LOGIN'] = $arResult['LAST_LOGIN'];
\Bitrix\Main\Localization\Loc::loadMessages($_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH . '/header.php');
$asset = Bitrix\Main\Page\Asset::getInstance();
$asset->addCss(SITE_TEMPLATE_PATH . "/new_css/style_1.css");
?>
<section class="main-block">
	<h2><?$APPLICATION->ShowTitle()?></h2>

	<form method="post" action="<?=$arResult["AUTH_FORM"]?>" name="bform" class="form_forgot-pass">
		
		<?if (strlen($arResult["BACKURL"]) > 0): ?>
			<input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
		<? endif ?>
		<input type="hidden" name="AUTH_FORM" value="Y">
		<input type="hidden" name="TYPE" value="CHANGE_PWD">
		
		<?foreach($arFormFields as $code => $arField):?>
			<?switch($code):
				case 'CONFIRM_PASSWORD':
				case 'PASSWORD':?>
					<label>
						<input type="password" placeholder="<?=GetMessage("AUTH_".$code)?>" name="USER_<?=$code?>" maxlength="50" value="<?=$arResult["USER_".$code]?>" class="input" />
					</label>
				<?break;
				default:?>
					<label <?=in_array($code, ['LOGIN', 'CHECKWORD']) ? 'style="display:none"' : '';?>>
						<span class="text"><?=GetMessage("AUTH_".$code)?><?if($arField['REQUIRED']):?><span class="required-asterisk">*</span><?endif?></span>
						<input type="text" name="USER_<?=$code?>" maxlength="50" value="<?=str_replace('%40','@',$arResult["USER_".$code])?>" class="input" />
					</label>
			<?endswitch?>
		<?endforeach?>
		<?/* CAPTCHA */
		if ($arResult["USE_CAPTCHA"] == "Y"):?>
			<label class="textinput-wrapper">
				<input type="hidden" name="captcha_sid" value="<?=$arResult["CAPTCHA_CODE"]?>" />
				<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA" />
			</label>
			<label class="textinput-wrapper">
				<span style="width: 100%" class="text"><?=GetMessage("CAPTCHA_REGF_TITLE")?><span class="required-asterisk">*</span>:</span>
				<input type="text" name="captcha_word" maxlength="50" value="" class="input"/>
			</label>
		<?endif?>
		<div class="info"></div>
		<div>
			<button type="submit" class="button" name="change_pwd" value="Y"><span class="text"><?=GetMessage("AUTH_CHANGE")?></span></button>
		</div>
	</form>

	<script type="text/javascript">
		document.bform.USER_PASSWORD.focus();
		$('.form_forgot-pass').on('submit', function(e){
			
			$errMsg = [];
			if($(this).find('[name="USER_PASSWORD"]').val() !== $(this).find('[name="USER_CONFIRM_PASSWORD"]').val()){
				$errMsg.push('Пароли не совпадают');
			}
			if($(this).find('[name="USER_PASSWORD"]').val().length < 6){
				$errMsg.push('Длина пароля должна быть не менее 6 символов');
			}			
			if($errMsg.length){
				e.preventDefault();
				$errHtml = '';
				for(let i = 0; i < $errMsg.length; i++){
					$errHtml += '<div>' + $errMsg[i] + '</div>';
				}
				$(this).find('.info').html($errHtml);
				$(this).find('.info').show();
			} else {
				$(this).find('.info').hide();
			}
		});
	</script>
</section>