<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$arSelect = [];
foreach($arParams['IBLOCK_PROPS_IDS'] as $propCode => $propId){
    $arSelect[] = 'PROPERTY_' . $propCode;
}

$arItems = [];
foreach($arResult['ITEMS'] as $arItem){
    $arItem['DATE_CREATE'] = FormatDate($arParams['ACTIVE_DATE_FORMAT'], MakeTimeStamp($arItem['DATE_CREATE']));

    $rsAns = CIblockElement::GetList(
        ['property_LIKE' => 'desc', 'id' => 'desc'],
        ['IBLOCK_ID' => $arParams['IBLOCK_ID'], 'ACTIVE' => 'Y', 'PROPERTY_ASK_ID' => $arItem['ID'], 'PROPERTY_TYPE' => $arParams['PROP_TYPE_IDS']['ANS']],
        false,
        false,
        array_merge(['ID', 'NAME', 'PREVIEW_TEXT', 'DATE_CREATE'], $arSelect)
    );
    $arAnswers = [];
    while($obAns = $rsAns->GetNext()){
        $obAns['DATE_CREATE'] = FormatDate($arParams['ACTIVE_DATE_FORMAT'], MakeTimeStamp($obAns['DATE_CREATE']));
        $obAns['IS_COMPANY_USER'] = $obAns['PROPERTY_USER_ID_VALUE'] > 0;
        $arAnswers[] = $obAns;
    }

    $arItems[$arItem['ID']] = $arItem;
    $arItems[$arItem['ID']]['ANSWERS'] = $arAnswers;
}

$rsAns = CIblockElement::GetList(
    ['property_LIKE' => 'desc', 'id' => 'desc'],
    ['IBLOCK_ID' => $arParams['IBLOCK_ID'], 'ACTIVE' => 'Y', 'PROPERTY_ITEM_ID' => $arParams['ITEM_ID'], 'PROPERTY_TYPE' => $arParams['PROP_TYPE_IDS']['ANS']],
    false,
    ["nPageSize"=> 1],
    array_merge(['ID', 'NAME', 'PREVIEW_TEXT', 'DATE_CREATE'], $arSelect)
);
if($obAnsTop = $rsAns->GetNext()){
    if($obAnsTop['PROPERTY_LIKE_VALUE'] > 0 && $obAnsTop['PROPERTY_ASK_ID_VALUE'] > 0){
        $rsMainAsk = CIblockElement::GetList(
            ['property_LIKE' => 'desc', 'id' => 'desc'],
            ['IBLOCK_ID' => $arParams['IBLOCK_ID'], 'ID' => $obAnsTop['PROPERTY_ASK_ID_VALUE'], 'ACTIVE' => 'Y'],
            false,
            false,
            array_merge(['ID', 'NAME', 'PREVIEW_TEXT', 'DATE_CREATE'], $arSelect)
        );
        if($obMainAsk = $rsMainAsk->GetNext()){
            $arResult['MAIN_ASK'][0] = $obMainAsk;
            $rsMainAns = CIblockElement::GetList(
                ['property_LIKE' => 'desc', 'id' => 'desc'],
                ['IBLOCK_ID' => $arParams['IBLOCK_ID'], 'ACTIVE' => 'Y', 'PROPERTY_ASK_ID' => $obAnsTop['PROPERTY_ASK_ID_VALUE'], 'PROPERTY_TYPE' => $arParams['PROP_TYPE_IDS']['ANS']],
                false,
                false,
                array_merge(['ID', 'NAME', 'PREVIEW_TEXT', 'DATE_CREATE'], $arSelect)
            );
            if($rsMainAns->SelectedRowsCount()){
                //if(isset($arItems[$obAnsTop['PROPERTY_ASK_ID_VALUE']])) unset($arItems[$obAnsTop['PROPERTY_ASK_ID_VALUE']]);

                while($obMainAns = $rsMainAns->GetNext()){
                    $obMainAns['DATE_CREATE'] = FormatDate($arParams['ACTIVE_DATE_FORMAT'], MakeTimeStamp($obMainAns['DATE_CREATE']));
                    $arResult['MAIN_ASK'][0]['ANSWERS'][] = $obMainAns;
                }
            }
        }
    }
}

//global $USER; if($USER->IsAdmin()){echo '<pre>'; print_r($arResult['MAIN_ASK']); echo '</pre>';};
$arResult['ITEMS'] = $arItems;