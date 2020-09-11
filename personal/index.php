<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Личный кабинет");

if (!$USER->IsAuthorized()){
	$APPLICATION->AuthForm("");
	return;
}

global $isNewTemplate;
$tmpl = 'bitronic2';
$nav = '.default';
if($isNewTemplate){
	$tmpl = 'sib_bitronic2';
	$nav = 'sib_default';
}
?>
<? $APPLICATION->IncludeComponent(
	"bitrix:sale.personal.section", 
	$tmpl,
	array(
		"COMPONENT_TEMPLATE" => $tmpl,
		"SHOW_ORDER_PAGE" => "Y",
		"SHOW_PRIVATE_PAGE" => "Y",
		"SHOW_SUBSCRIBE_PAGE" => "Y",
		"CUSTOM_PAGES" => "[[\"subscribe/\",\"Настройки рассылки\",\"settings\"]]",
		"PATH_TO_PAYMENT" => "/personal/payment",
		"PATH_TO_CATALOG" => "/catalog/",
		"SEF_MODE" => "Y",
		"SHOW_ACCOUNT_COMPONENT" => "Y",
		"SHOW_ACCOUNT_PAY_COMPONENT" => "Y",
		"ACCOUNT_PAYMENT_SELL_CURRENCY" => "RUB",
		"ACCOUNT_PAYMENT_PERSON_TYPE" => "1",
		"ACCOUNT_PAYMENT_ELIMINATED_PAY_SYSTEMS" => array(
			0 => "0",
		),
		"ACCOUNT_PAYMENT_SELL_SHOW_FIXED_VALUES" => "Y",
		"ACCOUNT_PAYMENT_SELL_TOTAL" => array(
			0 => "100",
			1 => "200",
			2 => "500",
			3 => "1000",
			4 => "5000",
			5 => "",
		),
		"ACCOUNT_PAYMENT_SELL_USER_INPUT" => "Y",
		"SAVE_IN_SESSION" => "Y",
		"ACTIVE_DATE_FORMAT" => "d.m.Y",
		"CUSTOM_SELECT_PROPS" => array(
		),
		"PROP_1" => array(
		),
		"PROP_3" => "",
		"PROP_2" => array(
		),
		"PROP_4" => "",
		"ORDER_HISTORIC_STATUSES" => array(
			0 => "F",
		),
		"USE_AJAX_LOCATIONS_PROFILE" => "N",
		"COMPATIBLE_LOCATION_MODE_PROFILE" => "N",
		"SEND_INFO_PRIVATE" => "N",
		"CHECK_RIGHTS_PRIVATE" => "N",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600",
		"CACHE_GROUPS" => "Y",
		"PER_PAGE" => "20",
		"NAV_TEMPLATE" => $nav,
		"SET_TITLE" => "N",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO",
		"SEF_FOLDER" => "/personal/",
		"FEEDBACK_RESIZER_SET" => "13",
		"PAYMENT_RESIZER_SET" => "31",
		"FEEDBACK_IBLOCK_TYPE" => "bitronic2_feedback",
		"FEEDBACK_ELEMENT_EXIST_IBLOCK_ID" => "13",
		"FEEDBACK_ELEMENT_EXIST_TITLE" => "Сообщить о поступлении",
		"FEEDBACK_ELEMENT_EXIST_PROP_1_TITLE" => "Дата заявки",
		"FEEDBACK_ELEMENT_EXIST_PROP_1" => array(
		),
		"FEEDBACK_ELEMENT_EXIST_PROP_2_TITLE" => "Контакты",
		"FEEDBACK_ELEMENT_EXIST_PROP_2" => array(
			0 => "EMAIL",
		),
		"FEEDBACK_ELEMENT_CONTACT_IBLOCK_ID" => "14",
		"FEEDBACK_ELEMENT_CONTACT_TITLE" => "Товары по запросу",
		"FEEDBACK_ELEMENT_CONTACT_PROP_1_TITLE" => "Комментарий",
		"FEEDBACK_ELEMENT_CONTACT_PROP_1" => array(
			0 => "QUANTITY",
			1 => "COMMENT",
		),
		"FEEDBACK_ELEMENT_CONTACT_PROP_2_TITLE" => "Контакты",
		"FEEDBACK_ELEMENT_CONTACT_PROP_2" => array(
			0 => "NAME",
			1 => "EMAIL",
			2 => "PHONE",
		),
		"FEEDBACK_FOUND_CHEAP_IBLOCK_ID" => "11",
		"FEEDBACK_FOUND_CHEAP_TITLE" => "Нашли дешевле",
		"FEEDBACK_FOUND_CHEAP_PROP_1_TITLE" => "Информация",
		"FEEDBACK_FOUND_CHEAP_PROP_1" => array(
			0 => "PRICE_OTHER",
			1 => "PRICE",
			2 => "URL",
		),
		"FEEDBACK_FOUND_CHEAP_PROP_2_TITLE" => "Контакты",
		"FEEDBACK_FOUND_CHEAP_PROP_2" => array(
			0 => "EMAIL",
			1 => "FIO",
			2 => "PHONE",
		),
		"FEEDBACK_PRICE_LOWER_IBLOCK_ID" => "12",
		"FEEDBACK_PRICE_LOWER_TITLE" => "Сообщить о снижении цены",
		"FEEDBACK_PRICE_LOWER_PROP_1_TITLE" => "Цена",
		"FEEDBACK_PRICE_LOWER_PROP_1" => array(
			0 => "PRICE",
		),
		"FEEDBACK_PRICE_LOWER_PROP_2_TITLE" => "Контакты",
		"FEEDBACK_PRICE_LOWER_PROP_2" => array(
			0 => "EMAIL",
		),
		"ORDER_DETAIL_RESIZER_SET" => "6",
		"ORDER_DEFAULT_SORT" => "STATUS",
		"ALLOW_INNER" => "N",
		"ONLY_INNER_FULL" => "N",
		"ORDER_HIDE_USER_INFO" => array(
			0 => "0",
		),
		"ORDER_RESTRICT_CHANGE_PAYSYSTEM" => array(
			0 => "N",
			1 => "NN",
			2 => "DK",
			3 => "AC",
			4 => "ZP",
			5 => "YT",
			6 => "LO",
			7 => "A",
			8 => "D",
			9 => "O",
			10 => "AS",
			11 => "KM",
			12 => "KO",
			13 => "C",
			14 => "B",
			15 => "E",
			16 => "P",
			17 => "Y",
			18 => "W",
			19 => "Z",
			20 => "F",
		),
		"ORDERS_PER_PAGE" => "10",
		"MAIN_CHAIN_NAME" => "Мой кабинет",
		"SEF_URL_TEMPLATES" => array(
			"index" => "",
			"orders" => "orders/?show_all=Y",
			"account" => "account/",
			"subscribe" => "products/",
			"profile" => "profiles/",
			"profile_detail" => "profiles/#ID#/",
			"private" => "profile/",
			"order_detail" => "orders/#ID#/",
			"order_cancel" => "cancel/#ID#/",
		)
	),
	false
);
?>
<? require $_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php" ?>