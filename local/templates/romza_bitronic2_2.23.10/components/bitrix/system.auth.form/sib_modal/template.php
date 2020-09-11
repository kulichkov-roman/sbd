<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use \Bitronic2\Mobile;
\Bitrix\Main\Localization\Loc::loadMessages($_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH . '/header.php');
$pathToRules = COption::GetOptionString(CRZBitronic2Settings::getModuleId(),'path_tu_rules_privacy',SITE_DIR.'personal/rules/personal_data.php');

$id = 'bxdinamic_bitronic2_auth_authorized';

$isNewPass = strpos($arResult['ERROR_MESSAGE'], 'Пароль успешно сменен') !== false;
//$isNewPass = true;
if($isNewPass){
	$arResult["USER_LOGIN"] = $_REQUEST['USER_LOGIN']?:$arResult["USER_LOGIN"];
	$arResult["BACKURL"] = '/personal/';
}
//echo $_REQUEST['USER_LOGIN'];
$arResult['ERROR_MESSAGE'] = str_replace('На ваш EMail высланы новые регистрационные данные.', '', $arResult['ERROR_MESSAGE']);

$frame = $this->createFrame()->begin();
?>
<?if($USER->IsAuthorized()):?>

	<div class="login js-fade" id="<?=$id?>">
		<?//if(method_exists($this, 'createFrame')) $frame = $this->createFrame($id, false)->begin(CRZBitronic2Composite::insertCompositLoader());?>
			
				<a class="login__button" href="/personal/">
					<span class="login__icon">
						<?=GetMessage('BITRONIC2_AUTH_LOGOUT_BUTTON');?>
					</span>
				</a>
				<div class="login-hide js-fade-hide">
					<button class="login-hide__close button-close js-fade-close"></button>
					<div class="login-hide__buttons">
						<a class="login-hide__button-1 button button_white js-go-tab-1" href="/personal/"><?=GetMessage('BITRONIC2_AUTH_LOGIN_KAB');?></a>
					</div>
					<!-- <a class="login-hide__status" href="#">Статус заказа</a> -->
					<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "EDIT_TEMPLATE" => "include_areas_template.php", "PATH" => SITE_DIR."include_areas/sib/header/user_menu.php"), false, array("HIDE_ICONS"=>"Y"));?>
				</div>
			
		<?//if(method_exists($this, 'createFrame')) $frame->end();?>         
	</div> 

<?else:?>

	<?//$id = 'bxdinamic_bitronic2_auth_not_authorized';?>
	<div class="login js-fade" id="<?=$id?>">
		<?//if(method_exists($this, 'createFrame')) $frame = $this->createFrame($id, false)->begin('Войти');?>
			<?if(!$USER->IsAuthorized()):?>
				<a class="login__button js-fade-button" href="javascript:void(0);">
					<span class="login__icon">
						<?=GetMessage('BITRONIC2_AUTH_LOGIN_LINKI');?>
					</span>
				</a>
				<div class="login-hide js-fade-hide">
					<button class="login-hide__close button-close js-fade-close"></button>
					<div class="login-hide__buttons">
						<a class="login-hide__button-1 button button_white js-go-tab-1" data-fancybox href="#popup-login"><?=GetMessage('BITRONIC2_AUTH_LOGIN_LINKI');?></a>
						<a class="login-hide__button-2 button button_transparent js-go-tab-2" data-fancybox href="#popup-login"><span><?=GetMessage('BITRONIC2_AUTH_FORM_REGISTER');?></span></a>
					</div>
					<div class="login-hide__buttons login-hide__buttons_mobile">
						<a class="login-hide__button-1 button button_white js-go-tab-1" data-fancybox href="#popup-login-mobile"><?=GetMessage('BITRONIC2_AUTH_LOGIN_LINKI');?></a>
						<a class="login-hide__button-2 button button_transparent js-go-tab-2" data-fancybox href="#popup-reg-mobile"><span><?=GetMessage('BITRONIC2_AUTH_FORM_REGISTER');?></span></a>
					</div>
					<?//$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "EDIT_TEMPLATE" => "include_areas_template.php", "PATH" => SITE_DIR."include_areas/sib/header/user_menu.php"), false, array("HIDE_ICONS"=>"Y"));?>
				</div>
			<?endif;?>
		<?//if(method_exists($this, 'createFrame')) $frame->end();?>         
	</div>

<?endif;?>

<?if(!$USER->IsAuthorized()):?>
<div class="popups" id="rbs_auth_popups">
	<?//$frame = $this->createFrame('rbs_auth_popups', false)->begin('');?>
	<?if(!\Bitronic2\Mobile::isMobile()):?>

			<?
			if (($arResult['SHOW_ERRORS'] == 'Y' && $arResult['ERROR']) || $_GET['show_auth'] == 'y'):?>
				<script>
					if($("#popup-login").css('display') !== 'inline-block')
					{
						$('.js-go-tab-1[href="#popup-login"]').click();
					}
				</script>
			<?endif;?>

			<!-- BEGIN LOGIN POPUP -->
			<div class="popup" id="popup-login">
				<ul class="popup-tabs">
					<li class="popup-tabs__item js-tabs-item active">
						<a class="popup-tabs__link js-tabs-link js-tab-1" href="#login-tab-1">Войти</a>
					</li>
					<li class="popup-tabs__item js-tabs-item">
						<a class="popup-tabs__link js-tabs-link js-tab-2" href="#login-tab-2">Зарегистрироваться</a>
					</li>            
				</ul>
				<!-- BEGIN TAB -->
				<div class="popup-tab tab js-tabs-content active" id="login-tab-1">
					<div class="login-form">
						<form name="system_auth_form<?=$arResult["RND"]?>" method="post" target="_top" action="<?=$arResult["AUTH_URL"]?>" <?=$isNewPass?'autocomplete="nope"':''?>  >
							<?if($arResult["BACKURL"] <> ''):?>
								<input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
							<?endif?>
							<?foreach ($arResult["POST"] as $key => $value):?>
								<input type="hidden" name="<?=$key?>" value="<?=$value?>" />
							<?endforeach?>
							
							<input type="hidden" name="AUTH_FORM" value="Y" />
							<input type="hidden" name="TYPE" value="AUTH" />

							<?
							if ($arResult['SHOW_ERRORS'] == 'Y' && $arResult['ERROR']):?>
								<?ShowMessage($arResult['ERROR_MESSAGE']);?>
							<?endif;?>				

							<input class="input" type="email" name="USER_LOGIN" value="<?=$arResult["USER_LOGIN"]?>" <?=$isNewPass?'autocomplete="nope"':''?> placeholder="Введите адрес электронной почты" required>
							<script>
								<?if(!$isNewPass):?>
									BX.ready(function() {
										var loginCookie = BX.getCookie("<?=CUtil::JSEscape($arResult["~LOGIN_COOKIE_NAME"])?>");
										if (loginCookie)
										{
											var form = document.forms["system_auth_form<?=$arResult["RND"]?>"];
											var loginInput = form.elements["USER_LOGIN"];
											loginInput.value = loginCookie;
										}
									});
								<?endif;?>
							</script>

							<input class="input" type="password" name="USER_PASSWORD" id="USER_PASSWORD" placeholder="Введите пароль" <?=$isNewPass?'autocomplete="new-password"':''?> required>
							
							<div class="login-form__buttons">
								<button class="login-form__button button button_white" type="submit">Войти</button>
								<a class="login-form__password" data-fancybox href="#popup_forgot_pass">Забыли пароль?</a>
							</div>
							
							<?
								/*$APPLICATION->IncludeComponent("bitrix:socserv.auth.form", "sib_modal", 
									array(
										"AUTH_SERVICES"=>$arResult["AUTH_SERVICES"],
										"AUTH_URL"=>$arResult["AUTH_URL"],
										"POST"=>$arResult["POST"],
										"POPUP"=>"N",
										"SUFFIX"=>"form",
									), 
									$component, 
									array("HIDE_ICONS"=>"Y")
								);*/
							?>
							
							<!-- <div class="socials-1">
								<p class="socials-1__title">Войти через соц. сети (тут нужно использовать модуль uLogin, т.к. в битриксе нет инсты и ютуба)</p>
								<ul class="socials-1__list">
									<li class="socials-1__item">
										<a class="socials-1__link socials-1__link_icon-1" href="#"></a>
									</li>
									<li class="socials-1__item">
										<a class="socials-1__link socials-1__link_icon-2" href="#"></a>
									</li>    
									<li class="socials-1__item">
										<a class="socials-1__link socials-1__link_icon-3" href="#"></a>
									</li>
									<li class="socials-1__item">
										<a class="socials-1__link socials-1__link_icon-4" href="#"></a>
									</li>                                                       
								</ul>
							</div> -->
						</form>
					</div>
				</div>
				<!-- TAB EOF -->
				<!-- BEGIN TAB -->
				<div class="popup-tab tab js-tabs-content" id="login-tab-2">
					<div class="login-form">
						<?
						//if(method_exists($this, 'createFrame')) $frame = $this->createFrame()->begin(CRZBitronic2Composite::insertCompositLoader());
						if (!$USER->IsAuthorized()):?>
						<form id="user_registration">
							
							<input class="input" name="name" type="text" placeholder="Имя" required>
							<input class="input" name="email" type="email" placeholder="Email" required>
							<div class="checkbox active">
								<label><div class="jq-checkbox js-formstyler checked" unselectable="on" style="user-select: none; display: inline-block; position: relative; overflow: hidden;"><input name="is_subs" class="js-formstyler" type="checkbox" checked="" style="position: absolute; z-index: -1; opacity: 0; margin: 0px; padding: 0px;"><div class="jq-checkbox__div"></div></div>Я согласен с</label>
								<a class="checkbox__link" href="/personal/rules/personal_data.php" target="_blank">условиями подписки</a>
							</div>    
							<input class="input" name="pass" type="password" placeholder="Пароль" required>                   
							<input class="input" name="repass" type="password" placeholder="Подтвердите пароль" required>
							<div class="info"></div>
							<button class="login-form__button button button_white" type="submit">Регистрация</button>
							<div class="checkbox active">
								<label><div class="jq-checkbox js-formstyler checked" unselectable="on" style="user-select: none; display: inline-block; position: relative; overflow: hidden;"><input name="is_pers" class="js-formstyler" type="checkbox" checked="" style="position: absolute; z-index: -1; opacity: 0; margin: 0px; padding: 0px;"><div class="jq-checkbox__div"></div></div>Я согласен на обработку</label>
								<a class="checkbox__link" href="/personal/rules/personal_data.php" target="_blank">персональных данных</a>
							</div>      

							<?
								/*$APPLICATION->IncludeComponent("bitrix:socserv.auth.form", "sib_modal", 
									array(
										"AUTH_SERVICES"=>$arResult["AUTH_SERVICES"],
										"AUTH_URL"=>$arResult["AUTH_URL"],
										"POST"=>$arResult["POST"],
										"POPUP"=>"N",
										"SUFFIX"=>"form",
									), 
									$component, 
									array("HIDE_ICONS"=>"Y")
								);*/
							?>

							<!-- <div class="socials-1">
								<p class="socials-1__title">Войти через соц. сети</p>
								<ul class="socials-1__list">
									<li class="socials-1__item">
										<a class="socials-1__link socials-1__link_icon-1" href="#"></a>
									</li>
									<li class="socials-1__item">
										<a class="socials-1__link socials-1__link_icon-2" href="#"></a>
									</li>    
									<li class="socials-1__item">
										<a class="socials-1__link socials-1__link_icon-3" href="#"></a>
									</li>
									<li class="socials-1__item">
										<a class="socials-1__link socials-1__link_icon-4" href="#"></a>
									</li>                                                       
								</ul>
							</div> -->
						</form>
						<?endif;?>
						<?//if(method_exists($this, 'createFrame')) $frame->end();?>
					</div>
				</div>
				<!-- TAB EOF -->        
			</div>
			<!-- LOGIN POPUP EOF -->
	<?else:?>

			<?
			if ($arResult['SHOW_ERRORS'] == 'Y' && $arResult['ERROR']):?>
				<script>
					if($("#popup-login-mobile").css('display') !== 'inline-block')
					{
						$('[href="#popup-login-mobile"]').click();
					}
				</script>
			<?endif;?>
			<!-- BEGIN LOGIN POPUP MOBILE-->
			<script>
				/* $('#popup-login-mobile').fancybox({
					beforeClose: function(){
						console.log('closess');
					}
				}); */
			</script>
			<div class="popup popup_mobile popup_mobile-sign" id="popup-login-mobile">        
				<div class="popup-tab">
					<div class="popup__head">
					<a href="#" class="popup__head-link"><i class="icon-arrow-filter"></i></a>
					<div class="popup__head-txt">Войти в личный кабинет</div>
					</div>
					<div class="login-form">
						<form name="system_auth_form<?=$arResult["RND"]?>" method="post" target="_top" action="<?=$arResult["AUTH_URL"]?>"  <?=$isNewPass?'autocomplete="nope"':''?> >
							<?if($arResult["BACKURL"] <> ''):?>
								<input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
							<?endif?>
							<?foreach ($arResult["POST"] as $key => $value):?>
								<input type="hidden" name="<?=$key?>" value="<?=$value?>" />
							<?endforeach?>
							
							<input type="hidden" name="AUTH_FORM" value="Y" />
							<input type="hidden" name="TYPE" value="AUTH" />

							<?
							if ($arResult['SHOW_ERRORS'] == 'Y' && $arResult['ERROR']):?>
								<?ShowMessage($arResult['ERROR_MESSAGE']);?>
							<?endif;?>				

							<input class="input" type="email" name="USER_LOGIN" value="<?=$arResult["USER_LOGIN"]?>" <?=$isNewPass?'autocomplete="nope"':''?> placeholder="Введите адрес электронной почты" required>
							<script>
								<?if(!$isNewPass):?>
									BX.ready(function() {
										var loginCookie = BX.getCookie("<?=CUtil::JSEscape($arResult["~LOGIN_COOKIE_NAME"])?>");
										if (loginCookie)
										{
											var form = document.forms["system_auth_form<?=$arResult["RND"]?>"];
											var loginInput = form.elements["USER_LOGIN"];
											loginInput.value = loginCookie;
										}
									});
								<?endif?>
							</script>

							<input class="input" type="password" name="USER_PASSWORD" id="USER_PASSWORD" <?=$isNewPass?'autocomplete="new-password"':''?> placeholder="Введите пароль" required>
							
							<div class="login-form__buttons">
								<button class="login-form__button button button_white" type="submit">Войти</button>
								<a class="login-form__password" data-fancybox href="#popup_forgot_pass">Забыли пароль?</a>
							</div>
							
							<?
								/*$APPLICATION->IncludeComponent("bitrix:socserv.auth.form", "sib_modal", 
									array(
										"AUTH_SERVICES"=>$arResult["AUTH_SERVICES"],
										"AUTH_URL"=>$arResult["AUTH_URL"],
										"POST"=>$arResult["POST"],
										"POPUP"=>"N",
										"SUFFIX"=>"form",
									), 
									$component, 
									array("HIDE_ICONS"=>"Y")
								);*/
							?>
							
							<!-- <div class="socials-1">
								<p class="socials-1__title">Войти через соц. сети (тут нужно использовать модуль uLogin, т.к. в битриксе нет инсты и ютуба)</p>
								<ul class="socials-1__list">
									<li class="socials-1__item">
										<a class="socials-1__link socials-1__link_icon-1" href="#"></a>
									</li>
									<li class="socials-1__item">
										<a class="socials-1__link socials-1__link_icon-2" href="#"></a>
									</li>    
									<li class="socials-1__item">
										<a class="socials-1__link socials-1__link_icon-3" href="#"></a>
									</li>
									<li class="socials-1__item">
										<a class="socials-1__link socials-1__link_icon-4" href="#"></a>
									</li>                                                       
								</ul>
							</div> -->
						</form>
					</div>
				</div>
				<!-- TAB EOF -->              
			</div>
			<!-- LOGIN POPUP EOF MOBILE-->
			<!-- BEGIN LOGIN POPUP MOBILE-->
			<div class="popup popup_mobile" id="popup-reg-mobile">
				<div class="popup-tab">
					<div class="popup__head">
					<a href="#" class="popup__head-link"><i class="icon-arrow-filter"></i></a>
					<div class="popup__head-txt">Регистрация</div>
					</div>
					<div class="login-form">
						<form id="user_registration">
							<input class="input" name="name" type="text" placeholder="Имя" required>
							<input class="input" name="email" type="email" placeholder="Email" required>
							<div class="checkbox active">
								<label><div class="jq-checkbox js-formstyler checked" unselectable="on" style="user-select: none; display: inline-block; position: relative; overflow: hidden;"><input name="is_subs" class="js-formstyler" type="checkbox" checked="" style="position: absolute; z-index: -1; opacity: 0; margin: 0px; padding: 0px;"><div class="jq-checkbox__div"></div></div>Я согласен с</label>
								<a class="checkbox__link" href="/personal/rules/personal_data.php" target="_blank">условиями подписки</a>
							</div>    
							<input class="input" name="pass" type="password" placeholder="Пароль" required>                   
							<input class="input" name="repass" type="password" placeholder="Подтвердите пароль" required>
							<div class="info"></div>
							<button class="login-form__button button button_white" type="submit">Регистрация</button>
							<div class="checkbox active">
								<label><div class="jq-checkbox js-formstyler checked" unselectable="on" style="user-select: none; display: inline-block; position: relative; overflow: hidden;"><input name="is_pers" class="js-formstyler" type="checkbox" checked="" style="position: absolute; z-index: -1; opacity: 0; margin: 0px; padding: 0px;"><div class="jq-checkbox__div"></div></div>Я согласен на обработку</label>
								<a class="checkbox__link" href="/personal/rules/personal_data.php" target="_blank">персональных данных</a>
							</div>      

							<?
								/*$APPLICATION->IncludeComponent("bitrix:socserv.auth.form", "sib_modal", 
									array(
										"AUTH_SERVICES"=>$arResult["AUTH_SERVICES"],
										"AUTH_URL"=>$arResult["AUTH_URL"],
										"POST"=>$arResult["POST"],
										"POPUP"=>"N",
										"SUFFIX"=>"form",
									), 
									$component, 
									array("HIDE_ICONS"=>"Y")
								);*/
							?>

							<!-- <div class="socials-1">
								<p class="socials-1__title">Войти через соц. сети</p>
								<ul class="socials-1__list">
									<li class="socials-1__item">
										<a class="socials-1__link socials-1__link_icon-1" href="#"></a>
									</li>
									<li class="socials-1__item">
										<a class="socials-1__link socials-1__link_icon-2" href="#"></a>
									</li>    
									<li class="socials-1__item">
										<a class="socials-1__link socials-1__link_icon-3" href="#"></a>
									</li>
									<li class="socials-1__item">
										<a class="socials-1__link socials-1__link_icon-4" href="#"></a>
									</li>                                                       
								</ul>
							</div> -->
						</form>
					</div>
				</div>
					
			</div>
	<?endif;?>
	<?//$frame->end();?>
</div>
<?endif;?>
<?$frame->beginStub();?>
<div class="login js-fade" id="<?=$id?>">
	<a class="login__button js-fade-button" href="javascript:void(0);">
		<span class="login__icon">
			...
		</span>
	</a>	
</div>
<?
$frame->end();?>

