<?
if (\Bitrix\Main\Loader::IncludeModule("yenisite.feedback")) {
	$APPLICATION->IncludeComponent("yenisite:feedback.add", "modal_price_cry",
		array(
			"IBLOCK_TYPE" => "bitronic2_feedback",
			"IBLOCK" => "11",
			"SUCCESS_TEXT" => "Спасибо! После обработки запроса наш специалист свяжется с Вами.",
			"PRINT_FIELDS" => array(
				0 => "PRICE",
				1 => "EMAIL",
				2 => "PRODUCT",
				3 => "PRICE_TYPE_ID",
				4 => "URL",
				5 => "PHONE",
				6 => "FIO",
				7 => "PRICE_OTHER",
			),
			"ACTIVE" => "Y",
			"EVENT_NAME" => "FOUND_CHEAP",
			"EMAIL" => "EMAIL",
			"PHONE" => "PHONE",
		), false);
}