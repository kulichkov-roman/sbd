<?
if (\Bitrix\Main\Loader::IncludeModule("yenisite.feedback")) {
	$APPLICATION->IncludeComponent('yenisite:feedback.add', 'modal_contact',
		array(
			"IBLOCK_TYPE" => "bitronic2_feedback",
			"IBLOCK" => "14",
			'SUCCESS_TEXT' => 'Спасибо! Наши менеджеры свяжутся с вами в ближайшее время.',
			'USE_CAPTCHA' => 'Y',
			"SHOW_SECTIONS" => "N",
			'PRINT_FIELDS' => array(
				0 => 'NAME',
				1 => 'EMAIL',
				2 => 'PHONE',
				3 => 'PRODUCT',
				4 => 'QUANTITY',
				5 => 'COMMENT'
			),
			'TITLE' => "Оставить заявку",
			'ACTIVE' => 'Y',
			'EVENT' => 'ELEMENT_CONTACT',
			'EMAIL' => 'EMAIL',
			"NAME" => "NAME",
			"PHONE" => "PHONE",
			"FORM" => "form_feedback",
			"EMPTY" => $arParams["EMPTY"],
		),
		false);
}