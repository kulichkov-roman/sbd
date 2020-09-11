<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("История заказов");

if(!$USER->IsAuthorized())
{
	$APPLICATION->AuthForm("");
	return;
}
?>
<main class="container">
	<h1><?$APPLICATION->ShowTitle()?></h1>
	<div class="account row">
		<?include '../left_menu.php';?>
		<div class="account-content col-xs-12 col-sm-9 col-xl-10">
			<?$APPLICATION->IncludeComponent(
				"bitrix:sale.personal.order", 
				"bitronic2", 
				array(
					"PROP_1" => "",
					"PROP_2" => "",
					"SEF_MODE" => "N",
					"SEF_FOLDER" => "/personal/",
					"ORDERS_PER_PAGE" => "20",
					"PATH_TO_PAYMENT" => "payment.php",
					"PATH_TO_BASKET" => "/personal/cart/",
					"SET_TITLE" => "N",
					"SAVE_IN_SESSION" => "Y",
					"NAV_TEMPLATE" => "",
					"PROP_5" => array(
					),
					"PROP_6" => array(
					),
					"ACTIVE_DATE_FORMAT" => "d.m.Y",
					"CACHE_TYPE" => "A",
					"CACHE_TIME" => "3600",
					"CACHE_GROUPS" => "Y",
					"CUSTOM_SELECT_PROPS" => array(
					),
					"HISTORIC_STATUSES" => array(
						0 => "F",
					),
					"RESIZER_BASKET_ICON" => "13"
				),
				false
			);?>
		</div>
	</div>
</main>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>