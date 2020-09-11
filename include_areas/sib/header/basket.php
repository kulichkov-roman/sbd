<?
$APPLICATION->IncludeComponent(
	"bitrix:sale.basket.basket.line", 
	"sib_basket", 
	array(
		"PATH_TO_BASKET" => SITE_DIR."personal/cart/",
		"SHOW_NUM_PRODUCTS" => "Y",
		"SHOW_TOTAL_PRICE" => "Y",
		"SHOW_EMPTY_VALUES" => "Y",
		"SHOW_PERSONAL_LINK" => "N",
		"PATH_TO_PERSONAL" => SITE_DIR."personal/settings.php",
		"SHOW_AUTHOR" => "N",
		"PATH_TO_REGISTER" => SITE_DIR."auth/",
		"PATH_TO_PROFILE" => SITE_DIR."personal/settings.php",
		"SHOW_PRODUCTS" => "Y",
		"SHOW_DELAY" => "N",
		"SHOW_NOTAVAIL" => "N",
		"SHOW_SUBSCRIBE" => "N",
		"SHOW_IMAGE" => "N",
		"SHOW_PRICE" => "Y",
		"SHOW_SUMMARY" => "Y",
		"PATH_TO_ORDER" => SITE_DIR."personal/order/",
		"POSITION_FIXED" => "N",
		"RESIZER_BASKET_ICON" => "9"
	),
	false
);