<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (empty($arResult['SECTIONS']))
	return;
	
$arMenuItemsIDs = array();
$arAllItems = array();

foreach($arResult['SECTIONS'] as $key => $arItem)
{
	if(in_array($arItem['ID'], $arParams['FILTER_SUB_SECTIONS'])){
		unset($arResult['SECTIONS'][$key]);
		continue;
	}

	if (isset($arResult['SECTIONS'][$key+1]["DEPTH_LEVEL"]))
	{
		$arItem["IS_PARENT"] = $arItem["DEPTH_LEVEL"] < $arResult['SECTIONS'][$key+1]["DEPTH_LEVEL"];
	}

	if($arItem["DEPTH_LEVEL"] > $arParams["TOP_DEPTH"])
	{
		unset($arResult['SECTIONS'][$key]);
		continue;
	}

	if ($arItem["DEPTH_LEVEL"] == "1")
	{
		$arMenuItemsIDs[$arItem['ID']] = array();
		if ($arItem["IS_PARENT"])
		{
			$curItemLevel_1 = $arItem['ID'];
		}
		$arAllItems[$arItem["ID"]] = $arItem;
	}
	elseif($arItem["DEPTH_LEVEL"] == "2")
	{
		$arMenuItemsIDs[$curItemLevel_1][$arItem['ID']] = array();
		if ($arItem["IS_PARENT"])
		{
			$curItemLevel_2 = $arItem['ID'];
		}
		$arAllItems[$arItem['ID']] = $arItem;
	}
	elseif($arItem["DEPTH_LEVEL"] == "3")
	{
		$arMenuItemsIDs[$curItemLevel_1][$curItemLevel_2][] = $arItem['ID'];
		$arAllItems[$arItem['ID']] = $arItem;
	}
}

$arResult = array();
$arResult["ALL_ITEMS"] = $arAllItems;
$arResult["ALL_ITEMS_ID"] = $arMenuItemsIDs;
