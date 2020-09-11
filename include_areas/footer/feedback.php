<?if (CModule::IncludeModule("yenisite.feedback")):?>
	<?global $rz_b2_options;
        $APPLICATION->IncludeComponent(
	"yenisite:feedback.add", 
	"modal_feedback", 
	array(
		"IBLOCK_TYPE" => "bitronic2_feedback",
		"IBLOCK" => "3",
		"NAME_FIELD" => "NAME",
		"COLOR_SCHEME" => "green",
		"TITLE" => "Обратная связь",
		"SUCCESS_TEXT" => "Спасибо! Ваше обращение принято. После обработки наш специалист свяжется с Вами.",
		//"USE_CAPTCHA" => "N",
		"SHOW_SECTIONS" => "N",
		"PRINT_FIELDS" => array(
			0 => "TEXT",
			1 => "ORDER_NUMBER",
			2 => "NAME",
			3 => "EMAIL",
			4 => "PHONE",
		),
		"AJAX_MODE" => "Y",
		"CACHE_TYPE" => "A",
        "USE_CAPTCHA" => $rz_b2_options['captcha-feedback'],
		"CACHE_TIME" => "300",
		"ACTIVE" => "Y",
		"EVENT_NAME" => "FEEDBACK_ADD",
		"TEXT_REQUIRED" => "N",
		"TEXT_SHOW" => "N",
		"NAME" => "NAME",
		"PHONE" => "PHONE",
		"FORM" => "form_feedback",
		"EMPTY" => $arParams["EMPTY"],
		"COMPONENT_TEMPLATE" => "modal_feedback",
		"SECTION_CODE" => "",
		"ELEMENT_ID" => "",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_ADDITIONAL" => "",
		"EMAIL" => "TEXT",
		"AJAX_REDIRECT" => "Y",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO"
	),
	false,
	array(
		"HIDE_ICONS" => "N"
	)
);
?>
<? endif ?>