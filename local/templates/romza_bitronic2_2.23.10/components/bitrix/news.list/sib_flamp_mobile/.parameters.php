<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Loader;

$resizer_sets_list = array ();
if (Loader::IncludeModule("yenisite.resizer2")){
    $arSets = CResizer2Set::GetList();
    while($arr = $arSets->Fetch())
    {
        $resizer_sets_list[$arr["id"]] = "[".$arr["id"]."] ".$arr["NAME"];
    }
}

$arTemplateParameters = array(
    "LINK_ALL_ITEMS_FLAMP" => Array(
        "NAME" => GetMessage("LINK_ALL_ITEMS_FLAMP"),
        "TYPE" => "STRING",
    ),
    "RESIZER_SET" => array(
        "NAME" => GetMessage("RESIZER_SET"),
        "TYPE" => "LIST",
        "VALUES" => $resizer_sets_list,
        "DEFAULT" => "36",
    ),
);

$arTemplateParameters['SORT_BY1']['HIDDEN'] = 'Y';
$arTemplateParameters['SORT_ORDER1']['HIDDEN'] = 'Y';
$arTemplateParameters['SORT_BY2']['HIDDEN'] = 'Y';
$arTemplateParameters['SORT_ORDER2']['HIDDEN'] = 'Y';
$arTemplateParameters['AJAX_MODE']['HIDDEN'] = 'Y';
$arTemplateParameters['AJAX_OPTION_JUMP']['HIDDEN'] = 'Y';
$arTemplateParameters['AJAX_OPTION_STYLE']['HIDDEN'] = 'Y';
$arTemplateParameters['AJAX_OPTION_HISTORY']['HIDDEN'] = 'Y';
$arTemplateParameters['PREVIEW_TRUNCATE_LEN']['HIDDEN'] = 'Y';
$arTemplateParameters['ACTIVE_DATE_FORMAT']['HIDDEN'] = 'Y';
$arTemplateParameters['INCLUDE_IBLOCK_INTO_CHAIN']['HIDDEN'] = 'Y';
$arTemplateParameters['ADD_SECTIONS_CHAIN']['HIDDEN'] = 'Y';
$arTemplateParameters['HIDE_LINK_WHEN_NO_DETAIL']['HIDDEN'] = 'Y';
$arTemplateParameters['PARENT_SECTION']['HIDDEN'] = 'Y';
$arTemplateParameters['PARENT_SECTION_CODE']['HIDDEN'] = 'Y';
$arTemplateParameters['INCLUDE_SUBSECTIONS']['HIDDEN'] = 'Y';
$arTemplateParameters['DETAIL_URL']['HIDDEN'] = 'Y';
$arTemplateParameters['PAGER_TEMPLATE']['HIDDEN'] = 'Y';
$arTemplateParameters['DISPLAY_TOP_PAGER']['HIDDEN'] = 'Y';
$arTemplateParameters['DISPLAY_BOTTOM_PAGER']['HIDDEN'] = 'Y';
$arTemplateParameters['PAGER_TITLE']['HIDDEN'] = 'Y';
$arTemplateParameters['PAGER_SHOW_ALWAYS']['HIDDEN'] = 'Y';
$arTemplateParameters['PAGER_DESC_NUMBERING']['HIDDEN'] = 'Y';
$arTemplateParameters['PAGER_DESC_NUMBERING_CACHE_TIME']['HIDDEN'] = 'Y';
$arTemplateParameters['PAGER_SHOW_ALL']['HIDDEN'] = 'Y';
