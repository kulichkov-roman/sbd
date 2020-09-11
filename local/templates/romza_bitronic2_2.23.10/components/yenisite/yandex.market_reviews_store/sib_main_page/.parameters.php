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
    "RESIZER_SET" => array(
        "NAME" => GetMessage("RESIZER_SET"),
        "TYPE" => "LIST",
        "VALUES" => $resizer_sets_list,
        "DEFAULT" => "39",
    ),
    "REVIEWS_COUNT" => array(
        "NAME" => GetMessage("REVIEWS_COUNT"),
        "TYPE" => "STRING",
    ),
    "SHOP_RATING" => array(
        "NAME" => GetMessage("SHOP_RATING"),
        "TYPE" => "STRING",
    ),
);