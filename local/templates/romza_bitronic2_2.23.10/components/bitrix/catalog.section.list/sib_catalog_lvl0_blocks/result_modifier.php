<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arParams['PROP_OF_IMG'] = $arParams['PROP_OF_IMG'] ? : 'UF_IMG_BLOCK_FOTO';

$arMenuItemsIDs = array();
$arAllItems = array();
$bResizer = Bitrix\Main\Loader::IncludeModule("yenisite.resizer2");

foreach ($arResult['SECTIONS'] as $key => $arItem)
{
    if(in_array($arItem['ID'], $arParams['FILTER_SUB_SECTIONS'])){
		unset($arResult['SECTIONS'][$key]);
		continue;
    }
    
   //global $USER; if($USER->IsAdmin()){echo '<pre>'; print_r($arItem); echo '</pre>';};
    if ($arItem["DEPTH_LEVEL"] > 1)
        continue;

    if ( $bResizer && !empty($arItem[$arParams['PROP_OF_IMG']]) )
    {
        $bigImg = CFile::GetPath($arItem[$arParams['PROP_OF_IMG']]);
        $arItem["PICTURE"]['SRC'] = CResizer2Resize::ResizeGD2($bigImg, $arParams['RESIZER_SET']);
    }

    $arMenuItemsIDs[$arItem['ID']] = array();
    $arAllItems[$arItem["ID"]] = $arItem;
}

$arResult = array();
$arResult["ALL_ITEMS"] = $arAllItems;
$arResult["ALL_ITEMS_ID"] = $arMenuItemsIDs;