<?
if(Bitrix\Main\Loader::includeModule('yenisite.oneclick'))
{
	global $rz_b2_options;
        $APPLICATION->IncludeComponent(
	"yenisite:oneclick.buy", 
	"bitronic2_credit", 
	array(
		"COMPONENT_TEMPLATE" => "bitronic2",
		"IBLOCK_TYPE" => "catalog",
		"IBLOCK_ID" => "6",
		"IBLOCK_ELEMENT_ID" => $_REQUEST["id"],
		"PERSON_TYPE_ID" => "1",
		"SHOW_FIELDS" => array(
			0 => "FIO",
			1 => "EMAIL",
			2 => "PHONE",
		),
		"REQ_FIELDS" => array(
			0 => "FIO",
			1 => "EMAIL",
			2 => "PHONE",
		),
		"ALLOW_AUTO_REGISTER" => "Y",
		"MESSAGE_OK" => "Ваша заявка принята, её номер - #ID#. Менеджер свяжется с вами в ближайшее время. Спасибо что выбрали нас!",
		"PAY_SYSTEM_ID" => "23",
		"DELIVERY_ID" => "421",
		"AS_EMAIL" => "0",
		"AS_NAME" => "0",
		"FIELD_CLASS" => "textinput",
		"FIELD_PLACEHOLDER" => "Y",
		"FIELD_QUANTITY" => "Y",
		"SEND_REGISTER_EMAIL" => "Y",
		"EMPTY" => $arParams["EMPTY"],
		"USE_CAPTCHA" => $rz_b2_options["captcha-quick-buy"],
		"USE_CAPTCHA_FORCE" => $rz_b2_options["captcha-quick-buy"],
		//"USER_REGISTER_EVENT_NAME" => "[SALE_NEW_ORDER]",
		"OFFER_PROPS" => $arProps,
		"COMMENTS" => "Заявка на кредит",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO"
	),
	false
);

}?>