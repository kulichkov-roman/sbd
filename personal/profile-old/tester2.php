<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("tester2");
?><br>
<?$APPLICATION->IncludeComponent(
	"bitrix:sale.affiliate.instructions",
	"",
Array(),
false
);?>
<?$APPLICATION->IncludeComponent(
	"bitrix:sale.affiliate.report",
	"",
Array()
);?>
<?$APPLICATION->IncludeComponent(
	"bitrix:sale.affiliate.account",
	"",
Array()
);?>
<?$APPLICATION->IncludeComponent(
	"bitrix:sale.affiliate.plans",
	"",
Array()
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>