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
	"bitrix:sale.personal.profile", 
	".default", 
	array(
		"COMPONENT_TEMPLATE" => ".default",
		"PER_PAGE" => "20",
		"SEF_MODE" => "N",
		"SET_TITLE" => "Y",
		"USE_AJAX_LOCATIONS" => "Y",
		"AJAX_LOCATIONS_TEMPLATE" => "steps"
	),
	false
);?>
		</div>
	</div>
</main>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>