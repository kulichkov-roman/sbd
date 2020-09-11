<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Настройки профиля");

if(!$USER->IsAuthorized())
{
	$APPLICATION->AuthForm("");
	return;
}
?>
<main class="container account-page account-settings">
	<h1><?$APPLICATION->ShowTitle()?></h1>
	<div class="account row">
		<?include '../left_menu.php';?>
		<div class="account-content col-xs-12 col-sm-9 col-xl-10">
			<?$APPLICATION->IncludeComponent(
				"bitrix:main.profile", 
				"bitronic2", 
				array(
					"AJAX_MODE" => "Y",
					"AJAX_OPTION_JUMP" => "Y",
					"AJAX_OPTION_STYLE" => "Y",
					"AJAX_OPTION_HISTORY" => "N",
					"SET_TITLE" => "N",
					"USER_PROPERTY" => array(
					),
					"SEND_INFO" => "N",
					"CHECK_RIGHTS" => "Y",
					"AJAX_OPTION_ADDITIONAL" => ""
				),
				false
			);?>
		</div>
	</div>
</main>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>