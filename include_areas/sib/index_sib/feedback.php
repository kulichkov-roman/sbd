<?global $rz_b2_options;?>
        <? if (\Bitrix\Main\Loader::IncludeModule('yenisite.ymrs')): ?>
	<? $APPLICATION->IncludeComponent(
	"yenisite:yandex.market_reviews_store", 
	"main_page", 
	array(
		"COMPONENT_TEMPLATE" => "main_page",
		"SHOPID" => "307694",
		"ACCESSTOKEN" => "4dbc3eeb65b3b7a51573ce3604f10be75288f1575a115688e75033fe1ff29696",
		"HEAD" => "Отзывы о нас на Яндекс Маркете",
		"HEAD_SIZE" => "h2",
		"SORT" => "date",
		"HOW" => "desc",
		"GRADE" => "5",
		"COUNT" => "10",
		"CACHE_TYPE" => "A",
		"FEEDBACK_ORDER" => $rz_b2_options["order-sFeedback"],
		"CACHE_TIME" => "86400",
		"INCLUDE_JQUERY" => "Y",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO",
		"HIDE_PRO" => "N",
		"HIDE_CONTRA" => "N",
		"HIDE_TEXT" => "N"
	),
	false,
	array(
		"ACTIVE_COMPONENT" => "Y"
	)
); ?>
<? else: ?>
	<?$APPLICATION->IncludeComponent("bitrix:main.include", "feedback_static", array("FEEDBACK_ORDER" => $rz_b2_options['order-sFeedback'], "PATH" => SITE_DIR."include_areas/sib/index_sib/feedback_static.php", "AREA_FILE_SHOW" => "file", "EDIT_TEMPLATE" => "include_areas_template.php"), $component, false)?>
<? endif ?>
