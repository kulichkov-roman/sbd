<?
global $mainBanners;
$mainBanners['!PROPERTY_RBS_DISCOUNT_VALUE'] = 'Y';
$regionId = $_SESSION["VREGIONS_REGION"]["ID"];

$APPLICATION->IncludeComponent(
	"bitrix:news.list", 
	"sib_big_slider",
	array(
		"IBLOCK_TYPE" => "services",
		"IBLOCK_ID" => "4",
		"CACHE_TYPE" => "Y",
		"CACHE_TIME" => "36000000",
		"CACHE_GROUPS" => "N",
		"NEWS_COUNT" => "30",
		"FILTER_NAME" => "mainBanners",
		"USE_FILTER" => 'Y',
		"FIELD_CODE" => array(
			
		),
		"PROPERTY_CODE" => array(
			0 => "RBS_IMG",
			1 => "RBS_LINK",
			2 => "RBS_TITLE",
			3 => "RBS_DESC"
		),
		"CHECK_DATES" => "Y",
		"CACHE_FILTER" => "Y",
		"SET_TITLE" => "N",
		"SET_BROWSER_TITLE" => "N",
		"SET_META_KEYWORDS" => "N",
		"SET_META_DESCRIPTION" => "N",
		"SET_STATUS_404" => "N",
		"SORT_BY1" => "sort",
		"SORT_ORDER1" => "asc",
		"SORT_BY2" => "id",
		"SORT_ORDER2" => "desc",
		"DETAIL_URL" => "",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_ADDITIONAL" => "",
		"PREVIEW_TRUNCATE_LEN" => "",
		"ACTIVE_DATE_FORMAT" => "d.m.Y",
		"INCLUDE_IBLOCK_INTO_CHAIN" => "Y",
		"ADD_SECTIONS_CHAIN" => "Y",
		"HIDE_LINK_WHEN_NO_DETAIL" => "N",
		"PARENT_SECTION" => "",
		"PARENT_SECTION_CODE" => "",
		"INCLUDE_SUBSECTIONS" => "Y",
		"PAGER_TEMPLATE" => ".default",
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"PAGER_TITLE" => "Новости",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"PAGER_SHOW_ALL" => "N",
		"COMPONENT_TEMPLATE" => "sib_big_slider",
		"SET_LAST_MODIFIED" => "N",
		"STRICT_SECTION_CHECK" => "N",
		"YOUTUBE_PARAMETERS" => "",
		"RESIZER_SET" => "41",
		"PAGER_BASE_LINK_ENABLE" => "N",
		"SHOW_404" => "N",
		"MESSAGE_404" => "",
		"REGION_ID" => $regionId
	),
	false
);
?>
