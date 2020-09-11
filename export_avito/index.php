<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Yandex market");
global $arrFilter; 
$arrFilter = array("!PROPERTY_YML_VALUE" => "Y",
	"!CATALOG_QUANTITY" => 0);
?><?$APPLICATION->IncludeComponent(
	"yenisite:yandex.market_new", 
	"vendor.model_new_avito", 
	array(
		"IBLOCK_TYPE" => "catalog",
		"IBLOCK_ID_IN" => array(
			0 => "6",
		),
		"IBLOCK_ID_EX" => array(
			0 => "0",
		),
		"IBLOCK_SECTION" => array(
			0 => "0",
		),
		"SITE" => "sibdroid.ru",
		"COMPANY" => "Sibdroid",
		"FILTER_NAME" => "arrFilter",
		"MORE_PHOTO" => "MORE_PHOTO",
		"CACHE_TYPE" => "N",
		"CACHE_TIME" => "86400",
		"CACHE_FILTER" => "N",
		"PRICE_CODE" => array(
			0 => "BASE",
		),
		"IBLOCK_ORDER" => "Y",
		"CURRENCY" => "RUR",
		"LOCAL_DELIVERY_COST" => "0",
		"COMPONENT_TEMPLATE" => "vendor.model_new_avito",
		"IBLOCK_CATALOG" => "Y",
		"DO_NOT_INCLUDE_SUBSECTIONS" => "N",
		"IBLOCK_AS_CATEGORY" => "Y",
		"CACHE_NON_MANAGED" => "Y",
		"SKU_NAME" => "PRODUCT_AND_SKU_NAME",
		"SKU_PROPERTY" => "PROPERTY_CML2_LINK",
		"CURRENCIES_CONVERT" => "NOT_CONVERT",
		"NAME_PROP" => "NAZVANIE_YANDEX",
		"DETAIL_TEXT_PRIORITET" => "N",
		"PARAMS" => array(
			0 => "0",
		),
		"COND_PARAMS" => array(
			0 => "0",
		),
		"DISCOUNTS" => "DISCOUNT_API",
		"OLD_PRICE_LIST" => "FROM_DISCOUNT",
		"SELF_SALES_NOTES" => "Y",
		"SELF_SALES_NOTES_INPUT" => "В наличии в Новосибирске!",
		"CATEGORY" => "DEFAULT_CATEGORY",
		"MARKET_CATEGORY_CHECK" => "N",
		"MARKET_CATEGORY_PROP" => "EMPTY",
		"SECTION_AS_VENDOR" => "Y",
		"MULTI_STRING_PROP" => array(
		),
		"DEVELOPER" => "TURBO_YANDEX_STATUS",
		"MODEL" => "TURBO_YANDEX_STATUS",
		"COUNTRY" => "TURBO_YANDEX_STATUS",
		"VENDOR_CODE" => "EMPTY",
		"MANUFACTURER_WARRANTY" => "GARANTIYA_PROIZVODITELYA",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO"
	),
	false
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>