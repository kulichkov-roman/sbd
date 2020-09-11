<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
LocalRedirect("/", false, "301 Moved permanently");
$APPLICATION->SetTitle("Бренды");
?><?$APPLICATION->IncludeComponent(
	"yenisite:highloadblock",
	"bitronic2",
	array(
		"BLOCK_ID" => "2",
		"NAV_TEMPLATE" => ".default",
		"COMPONENT_TEMPLATE" => "bitronic2",
		"PATH_TO_CATALOG" => "/catalog/",
		"SEF_MODE" => "Y",
		"LIST_RESIZER_SET" => "12",
		"VIEW_RESIZER_SET" => "15",
		"SET_TITLE" => "Y",
		"SET_BROWSER_TITLE" => "Y",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO",
		"SEF_FOLDER" => "/brands/",
		"BROWSER_TITLE" => "-",
		"ADD_ELEMENT_CHAIN" => "Y",
		"SEF_URL_TEMPLATES" => array(
			"list" => "",
			"view" => "#ID#/",
        "PROP_FOR_LINK_COMPONY" => "UF_LINK",
        "SHOW_PROPS_OF_HLB" => array(
            0 => "UF_COUNTRY",
            1 => "UF_DATE_FOUNDATION",
        ),
		)
	),
	false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>