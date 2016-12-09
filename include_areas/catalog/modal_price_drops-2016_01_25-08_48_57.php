<?
if (\Bitrix\Main\Loader::IncludeModule("yenisite.feedback")) {
	$APPLICATION->IncludeComponent("yenisite:feedback.add", "modal_price_drops",
		array(
			"IBLOCK_TYPE" => "bitronic2_feedback",
			"IBLOCK" => "12",
			"SUCCESS_TEXT" => "Спасибо! В случае снижения цены до заданной мы сообщим Вам.",
			"PRINT_FIELDS" => array(
				0 => "PRICE",
				1 => "EMAIL",
				2 => "PRODUCT",
				3 => "PRICE_TYPE_ID",
			),
			"ACTIVE" => "Y",
		), false);
}
