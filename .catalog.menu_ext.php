<?
global $APPLICATION, $rz_b2_options;
$aMenuLinksExt=$APPLICATION->IncludeComponent("yenisite:menu.ext", "", array(
	"IBLOCK_TYPE" => array(
		0 => "catalog",
	),
	"IBLOCK_TYPE_MASK" => array(
	),
	"IBLOCK_ID" => array(
		0 => "6", // TODO
	),
	"DEPTH_LEVEL_START" => "3",
	"DEPTH_LEVEL_FINISH" => "3",
	"DEPTH_LEVEL_SECTIONS" => "3",
	"IBLOCK_TYPE_URL" => "/#IBLOCK_TYPE#/",
	"IBLOCK_TYPE_URL_REPLACE" => "",
	"HIDE_ELEMENT" => "N",
	"ELEMENT_CNT" => 'N',
	"ELEMENT_CNT_AVAILABLE" => "N",
	"CACHE_TYPE" => "Y",
	"CACHE_TIME" => "3600",
	"IBLOCK_TYPE_SORT_FIELD" => "sort",
	"IBLOCK_TYPE_SORT_ORDER" => "asc",
	"IBLOCK_SORT_FIELD" => "sort",
	"IBLOCK_SORT_ORDER" => "asc",
	"SECTION_SORT_FIELD" => "NAME",
	"SECTION_SORT_ORDER" => "ASC",
	"ELEMENT_SORT_FIELD" => "sort",
	"ELEMENT_SORT_ORDER" => "asc",
	"GET_SECTION_UF" => "Y"
	),
	false
);
    $aMenuLinks = array_merge($aMenuLinksExt, $aMenuLinks);
?>