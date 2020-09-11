<?
global $rz_b2_options;
        if(CModule::IncludeModule("yenisite.feedback"))
{
	$APPLICATION->IncludeComponent(
		"yenisite:feedback.add", 
		"modal", 
		array(
			"IBLOCK_TYPE" => "bitronic2_feedback",
			"IBLOCK" => "2",
			"NAME_FIELD" => "NAME",
			"COLOR_SCHEME" => "green",
			"TITLE" => "Заказать звонок",
			"SUCCESS_TEXT" => "Спасибо! Ваше обращение принято. После обработки наш специалист свяжется с Вами.",
			//"USE_CAPTCHA" => "Y",
			"SHOW_SECTIONS" => "N",
			"PRINT_FIELDS" => array(
				0 => "NAME",
				1 => "PHONE",
			),
			"AJAX_MODE" => "Y",
			"CACHE_TYPE" => "A",
        "USE_CAPTCHA" => $rz_b2_options['captcha-callme'],
			"CACHE_TIME" => "300",
			"ACTIVE" => "Y",
			"EVENT_NAME" => "CALLME",
			"TEXT_REQUIRED" => "N",
			"TEXT_SHOW" => "N",
			"NAME" => "NAME",
			"PHONE" => "PHONE",
			"FORM" => "form_callme",
			"EMPTY" => $arParams["EMPTY"],
			"COMPONENT_TEMPLATE" => "modal",
			"SECTION_CODE" => "",
			"ELEMENT_ID" => "",
			"AJAX_OPTION_JUMP" => "N",
			"AJAX_OPTION_STYLE" => "Y",
			"AJAX_OPTION_HISTORY" => "N"
		),
		false,
		array(
			"HIDE_ICONS" => "N"
		)
	);
}
?>