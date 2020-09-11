<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
global $rz_b2_options;
LocalRedirect("/", false, "301 Moved permanently");
$APPLICATION->SetTitle("О бренде");
?><?$APPLICATION->IncludeComponent("bitrix:highloadblock.view", "bitronic2", Array(
		"BLOCK_ID" => "2",	// ID инфоблока
		"LIST_URL" => "/brands/",	// Путь к странице списка записей
		"ROW_ID" => $_REQUEST["ID"],	// ID записи
		"CATALOG_PATH" => "/catalog/",	// Путь к корневой странице каталога
		"RESIZER_SET" => "15",
		"HOVER-MODE" => $rz_b2_options["product-hover-effect"],
	),
	false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>