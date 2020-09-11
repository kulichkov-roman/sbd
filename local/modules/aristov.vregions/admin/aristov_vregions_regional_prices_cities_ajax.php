<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

global $USER, $APPLICATION, $DB;
$moduleID = 'aristov.vregions';

CModule::IncludeModule('iblock');

$fromPriceID = $_REQUEST['fromPriceID'];
$multiplier  = trim($_REQUEST['multiplier']);
$toPriceID   = $_REQUEST['toPriceID'];
$toPriceName = iconv('utf-8', LANG_CHARSET, $_REQUEST['toPriceName']);
$productIDs  = explode(',', $_REQUEST['productIds']);

$productIds = Array();
$res        = CIBlockElement::GetList(
    Array(
        "SORT" => "ASC"
    ),
    Array(
        'ID' => $productIDs,
    ),
    false,
    false,
    Array(
        'ID',
        'NAME',
        'IBLOCK_ID',
        'IBLOCK_SECTION_ID',
        'catalog_GROUP_'.$fromPriceID,
    )
);
while($ob = $res->GetNextElement()){
    $arFields = $ob->GetFields();

    $success = false;

    if (!$multiplier){
        $dbSections = CIBlockSection::GetList(
            Array(
                "SORT" => "ASC"
            ),
            Array(
                'IBLOCK_ID' => $arFields['IBLOCK_ID'],
                'ID'        => $arFields['IBLOCK_SECTION_ID'],
            ),
            false,
            Array(
                'ID',
                'IBLOCK_ID',
                'UF_VR_PRICE_MUL',
            )
        );
        while($arSection = $dbSections->GetNext()){
            if ($arSection['UF_VR_PRICE_MUL']){
                $multiplier = $arSection['UF_VR_PRICE_MUL'];
            }
        }
    }

    if (!$multiplier){
        $multiplier = 1;
    }

    // устанавливаем цену
    $newPriceVal   = $arFields['CATALOG_PRICE_'.$fromPriceID] * $multiplier;
    $arPriceFields = Array(
        "PRODUCT_ID"       => $arFields['ID'],
        "CATALOG_GROUP_ID" => $toPriceID,
        "PRICE"            => $newPriceVal,
    );
    $priceRes      = CPrice::GetList(
        array(),
        array(
            "PRODUCT_ID"       => $arFields['ID'],
            "CATALOG_GROUP_ID" => $toPriceID
        )
    );
    if ($priceArr = $priceRes->Fetch()){
        if (CPrice::Update($priceArr["ID"], $arPriceFields)){
            $success = true;
        }
    } else{
        $arPriceFields['CURRENCY'] = $arFields['CATALOG_CURRENCY_'.$fromPriceID]; // обязательный параметр при создании
        if (CPrice::Add($arPriceFields)){
            $success = true;
        }
    }

    if ($success){
        echo CAdminMessage::ShowNote(Loc::getMessage("FOR_PRODUCT").' "'.$arFields['NAME'].'" '.Loc::getMessage("IS_SET_PRICE").' "'.$toPriceName.'" '.Loc::getMessage("IN_VALUE").' '.$newPriceVal);
    }
}