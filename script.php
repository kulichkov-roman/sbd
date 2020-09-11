<?
//require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

exit();
require_once($_SERVER["DOCUMENT_ROOT"].'/bitrix/php_interface/class/config.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/bitrix/php_interface/class/yamarket.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/bitrix/php_interface/class/agent.php');

/*$indexsesProductCNT=CIBlockElement::GetList(array(), array("IBLOCK_ID"=>IBLOCK_PRODUCT,'PROPERTY_UPDATE_OPINIONS'=>1),array());
var_dump($indexsesProductCNT);
exit();*/
//ini_set("display_errors",1);
//error_reporting(E_ALL);
/*CIBlockElement::SetPropertyValues(1322, IBLOCK_PRODUCT, 0, "RESP_COUNT");
CIBlockElement::SetPropertyValues(1322, IBLOCK_PRODUCT, 0, "RESP_QUANT");
exit();*/
AgentYandexOpinionLoad();
exit();

echo 'test';
$shop_info=GetShopInfo();
echo "<pre>";
print_r($shop_info);
echo "</pre>";
$opinions=GetShopOpinions(1,30);
echo '<pre>';
print_r($opinions);
echo '</pre>';
exit();
CModule::IncludeModule("iblock");
CModule::IncludeModule("sale");

$arNoSectionID=array();
$rsParentSection = CIBlockSection::GetByID(52);
if ($arParentSection = $rsParentSection->GetNext())
{
    $arFilter = array('IBLOCK_ID' => $arParentSection['IBLOCK_ID'],'>LEFT_MARGIN' => $arParentSection['LEFT_MARGIN'],'<RIGHT_MARGIN' => $arParentSection['RIGHT_MARGIN'],'>DEPTH_LEVEL' => $arParentSection['DEPTH_LEVEL']); // выберет потомков без учета активности
    $rsSect = CIBlockSection::GetList(array('left_margin' => 'asc'),$arFilter);
    while ($arSect = $rsSect->GetNext())
    {
        $arNoSectionID[]=$arSect['ID'];
    }
}

$dbRes=CIBlockElement::GetList(array(),array('IBLOCK_ID'=>6,'ACTIVE'=>'Y','!PROPERTY_REKOMENDUEYE_TOVARY'=>false,'SECTION_ID'=>52,'INCLUDE_SUBSECTIONS'=>'Y'),false,false,array('ID','IBLOCK_ID','NAME','DETAIL_PAGE_URL','PROPERTY_REKOMENDUEYE_TOVARY','PROPERTY_RECOMMEND'));
while($arRes=$dbRes->GetNext()){
    $arRecomentID=array();
    $dbRes2=CIBlockElement::GetList(array(),array('IBLOCK_ID'=>6,'PROPERTY_REKOMENDUEYE_TOVARY'=>$arRes['PROPERTY_REKOMENDUEYE_TOVARY_ENUM_ID'],'!SECTION_ID'=>$arNoSectionID, '>CATALOG_QUANTITY'=>0),false,false,array('ID','IBLOCK_ID','NAME','IBLOCK_SECTION_ID','PROPERTY_REKOMENDUEYE_TOVARY'));
    while($arRes2=$dbRes2->GetNext()){
        $arRecomentID[]=$arRes2['ID'];
    }
    CIBlockElement::SetPropertyValues($arRes['ID'], $arRes['IBLOCK_ID'], $arRecomentID, "RECOMMEND");
}
?>