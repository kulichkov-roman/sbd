<?
if (\Bitrix\Main\Loader::IncludeModule("yenisite.feedback")) {
	$APPLICATION->IncludeComponent(
	"yenisite:feedback.add", 
	"modal_subscribe", 
	array(
		"IBLOCK_TYPE" => "bitronic2_feedback",
		"IBLOCK" => "13",
		"SUCCESS_TEXT" => "Спасибо! В случае поступления товара на склад мы сообщим Вам.",
		"PRINT_FIELDS" => array(
			0 => "PRODUCT",
			1 => "EMAIL",
		),
		"ACTIVE" => "Y",
		"EVENT_NAME" => "ELEMENT_EXIST_ADMIN",
		"EMAIL" => "EMAIL",
		"PHONE" => "PRODUCT",
		"COMPONENT_TEMPLATE" => "modal_subscribe",
		"NAME_FIELD" => "PRODUCT",
		"COLOR_SCHEME" => "",
		"TITLE" => "",
		"USE_CAPTCHA" => "N",
		"TEXT_SHOW" => "N",
		"TEXT_REQUIRED" => "N",
		"SHOW_SECTIONS" => "N",
		"NAME" => "",
		"SECTION_CODE" => "",
		"ELEMENT_ID" => "",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_ADDITIONAL" => "",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "300",
		"AJAX_REDIRECT" => "Y",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO"
	),
	false
);
}
