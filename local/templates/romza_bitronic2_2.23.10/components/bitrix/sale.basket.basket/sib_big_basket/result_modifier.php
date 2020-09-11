<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Yenisite\Core\Ajax;
use Bitrix\Main\Loader;
use Bitrix\Sale\DiscountCouponsManager;

global $rz_b2_options;

if (strlen($arParams['DELIVERY_URL']) < 1) {
    $arParams['DELIVERY_URL'] = SITE_DIR.'about/delivery/';
}
$arParams['SHOW_ARTICLE'] = ($rz_b2_options["block_show_article"] !== 'N');
$arParams['SHOW_ONECLICK'] = ($rz_b2_options["block_show_oneclick"] !== "N" && CModule::IncludeModule("yenisite.oneclick"));

$arResult['FULL_PRICE_WITHOUT_DISCOUNT'] = 0;
$arResult['CATALOG_PARAMS'] = array();
$arResult['USE_STORE'] = false;
$bSibCore = \Bitrix\Main\Loader::includeModule('sib.core');
$recommendSections = array();
$propRecomendSections = 'TIP_AKSESSUARA_1';
if (is_array($arResult["GRID"]["ROWS"]))
{
    if (Loader::IncludeModule('yenisite.core')) {
        $catalogParams = \Yenisite\Core\Ajax::getParams('bitrix:catalog', false, CRZBitronic2CatalogUtils::getCatalogPathForUpdate());
        if (is_array($catalogParams) && !empty($catalogParams)) {
            $catalogParams['STORE_DISPLAY_TYPE'] = $rz_b2_options['store_amount_type'];
            $arResult['USE_STORE'] = $catalogParams["USE_STORE"] == "Y" && Bitrix\Main\ModuleManager::isModuleInstalled("catalog");
            $arResult['CATALOG_PARAMS'] = $catalogParams;
        }
    }
    foreach($arResult["GRID"]["ROWS"] as &$arItem)
    {
        $arItem['CAN_BUY'] = CRZBitronic2CatalogUtils::getAvailableStatus($arItem['PRODUCT_ID'], $arItem['CAN_BUY']) ? 'Y' : 'N';
        //$arItem['AVAILABLE_QUANTITY'] = CRZBitronic2CatalogUtils::getStoresCount($arItem['PRODUCT_ID'], $arItem['AVAILABLE_QUANTITY']);
        if ($bSibCore){
            $arItem['AVAILABLE_QUANTITY'] = \Sib\Core\Regions::getQty($arItem['PRODUCT_ID']);
        }
       

        $arItem['PICTURE_PRINT']['SRC'] = CRZBitronic2CatalogUtils::getElementPictureById($arItem['PRODUCT_ID'], 3);
        $arItem['PICTURE_PRINT']['SRC_JPG'] = CRZBitronic2CatalogUtils::getElementPictureById($arItem['PRODUCT_ID'], 43);
        $arItem['SUM_NOT_FORMATED'] = $arItem['QUANTITY'] * $arItem['PRICE'];
        $arItem['FULL_SUM_NOT_FORMATED'] = $arItem['QUANTITY'] * $arItem['FULL_PRICE'];
        $arItem['FOR_ORDER'] = false;


        $arProduct = CCatalogProduct::GetByID($arItem['PRODUCT_ID']);
        $arItem['SUBSCRIBE'] = $arProduct['SUBSCRIBE'];
        if ($arItem['CAN_BUY'] == 'Y') {
            $arResult['FULL_PRICE_WITHOUT_DISCOUNT'] += $arItem['FULL_SUM_NOT_FORMATED'];
            $arItem['FOR_ORDER'] = ('Y' == $arProduct['QUANTITY_TRACE'] && 'Y' == $arProduct['CAN_BUY_ZERO'] && 0 >= $arItem['AVAILABLE_QUANTITY']);
        }

        $recommendSections = [];
        if($bSibCore){
            $hydroEls = \Sib\Core\Helper::getHydrogelElements('back');
            if(\Sib\Core\Helper::isSmarPhoneItem($arItem["PRODUCT_ID"]) && count($hydroEls) > 0){
                $recommendSections['HYDROGEL']['NAME'] = 'Гидрогелевое покрытие';
                $recommendSections['HYDROGEL']['SECTION_ID'] = 'HYDROGEL';
                $recommendSections['HYDROGEL']['COUNTS'] = 1;
                $recommendSections['HYDROGEL']['VALUES'] = [
                    'HYDROGEL'
                ];
            }
        }

        $blockID = CIBlockElement::GetIBlockByID($arItem["PRODUCT_ID"]);
        $recommend = CIBlockElement::GetProperty($blockID, $arItem["PRODUCT_ID"], "sort", "asc", array("CODE" => "RECOMMEND"));
        //echo $recommend->SelectedRowsCount();
        while ($value = $recommend->GetNext())
        {
            $arFilter = array('ID' => $value['VALUE'], 'IBLOCK_ID' => $blockID);
            $element = CIBlockElement::GetList(array(), $arFilter, false, false, array('IBLOCK_SECTION_ID', 'ID', 'CATALOG_AVAILABLE', 'NAME', 'PROPERTY_' . $propRecomendSections));

            if ($arElement = $element->GetNext())
            {
                if ($bSibCore){
                    $arElement['QTY'] = \Sib\Core\Regions::getQty($arElement['ID']);
                }
                //global $USER; if($USER->IsAdmin()){echo '<pre>'; print_r($arElement); echo '</pre>';};
                if (!$arElement['IBLOCK_SECTION_ID'] || $arElement['QTY'] <= 0 || empty($arElement['PROPERTY_'.$propRecomendSections.'_VALUE']))
                    continue;

                /* $section = CIBlockSection::GetByID($arElement['IBLOCK_SECTION_ID']);
                if ($arSection = $section->GetNext())
                { */

                   

                    /* if ($arSection['DEPTH_LEVEL'] <= 2)
                    {
                        $arParentSection = $arSection;
                    }
                    else
                    {
                        while ($arParentSection['DEPTH_LEVEL'] != 2)
                        {
                            $sectionID = $arParentSection['IBLOCK_SECTION_ID'] ? : $arSection['IBLOCK_SECTION_ID'];
                            $parentSection = CIBlockSection::GetByID($sectionID);
                            $arParentSection = $parentSection->GetNext();
                        }
                    } */

                    $arParentSection = [
                        'NAME' => $arElement['PROPERTY_'.$propRecomendSections.'_VALUE'],
                        'ID' => $arElement['PROPERTY_'.$propRecomendSections.'_ENUM_ID']
                    ];
                    
                    if (empty($recommendSections[ $arParentSection['ID'] ]))
                    {
                        $recommendSections[$arParentSection['ID']]['NAME'] = $arParentSection['NAME'];
                        $recommendSections[$arParentSection['ID']]['SECTION_ID'] = $arElement['PROPERTY_'.$propRecomendSections.'_ENUM_ID'];//$arElement['IBLOCK_SECTION_ID'];
                        $recommendSections[$arParentSection['ID']]['PRODUCT_ID'] = $arItem["ID"];
                        $recommendSections[$arParentSection['ID']]['COUNTS'] = 1;
                    }
                    else
                    {
                        $recommendSections[$arParentSection['ID']]['COUNTS']++;
                    }

                    $recommendSections[ $arParentSection['ID'] ]['VALUES'][] = $arElement['ID'];

                //}
            }

            unset($arParentSection);
        }
        $arItem['RECOMMEND'] = $recommendSections;
        unset($recommendSections);
    }
    unset($arItem);
}

if (!empty($arResult['COUPON_LIST']))
{
    $lastCoupon = end($arResult['COUPON_LIST']);
    if ($lastCoupon['STATUS'] === DiscountCouponsManager::STATUS_NOT_FOUND ||
        $lastCoupon['STATUS'] === DiscountCouponsManager::STATUS_FREEZE
    )
        $arResult['LAST_INCORRECT_COUPON'] = $lastCoupon;
}

$arResult['CURRENCIES'] = CRZBitronic2CatalogUtils::getCurrencyArray();

if(!\Bitrix\Main\Loader::includeModule('yenisite.core')) {
    die('Module yenisite.core not installed!');
}
Ajax::saveParams($this, $arParams, 'main_basket');