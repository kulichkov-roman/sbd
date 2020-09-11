<?
include_once "include_stop_statistic.php";
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if(\Bitrix\Main\Loader::includeModule('sib.core') && \Bitrix\Main\Loader::includeModule('iblock')){
    $defaultFilter = ['IBLOCK_ID' => 6, 'ACTIVE' => 'Y'];

    $defaultFilter['IBLOCK_SECTION_ID'] = \Sib\Core\Helper::getSubSectionFromParents($defaultFilter, ['ID' => [608]]);
    $regionId = \Sib\Core\Catalog::getDefRegion($_SESSION['VREGIONS_REGION']['ID']);
    $defaultFilter['PROPERTY_SIB_AVAIL_' . $regionId] = \Sib\Core\Helper::getPropValue('SIB_AVAIL_' . $regionId, 'AVAILABLE');    
    $ucenkaCount = CIblockElement::GetList([], $defaultFilter)->SelectedRowsCount();

    $defaultFilter['IBLOCK_SECTION_ID'] = \Sib\Core\Helper::getSubSectionFromParents($defaultFilter, ['ID' => [56]]);
    $defaultFilter['PROPERTY_' . \Sib\Core\Catalog::getTabPropertySale() . '_VALUE'] = 'Y';
    $acessCount = CIblockElement::GetList([], $defaultFilter)->SelectedRowsCount();

    echo json_encode([
        'UCENKA' => $ucenkaCount,
        'ACCESS' => $acessCount
    ]);
    return;
}

echo json_encode([
    'UCENKA' => 0,
    'ACCESS' => 0
]);