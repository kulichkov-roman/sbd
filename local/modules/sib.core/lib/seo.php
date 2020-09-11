<?
namespace Sib\Core;

/* use \Bitrix\Main\Loader;

\Bitrix\Main\Loader::includeModule('catalog');
\Bitrix\Main\Loader::includeModule('iblock');
\Bitrix\Main\Loader::includeModule('sale'); */

class Seo
{
    public static function setPriceSeo($elementId = false, $priceType = false)
    {
        if($elementId && $priceType && \Bitrix\Main\Loader::includeModule('catalog')){
            $price = '';
            $prices = Helper::getDiscountPriceArray($elementId);
            if($prices['PRICE_DISCOUNT'] > 0){
                $price = $prices['PRICE_DISCOUNT'];
            }else if($prices['BASE_PRICE'] > 0){
                $price = $prices['BASE_PRICE'];
            }
            self::replaceSeoTemplate('#PRICE#', $price);
        }        
    }    

    public static function getSectionHeadStringBefore($arCurSection)
    {
        $result = '';
        //Раздел смартфоны
        if($arCurSection['DEPTH_LEVEL'] == 3 && Helper::isSmartphoneSectionChild($arCurSection['LEFT_MARGIN'], $arCurSection['RIGHT_MARGIN'])){
            $result = 'Смартфоны';
        }
        
        //Раздел аксессуары
        $sectionsAccessIds = [1038,1028,1043,1261];
        if($_SESSION['is_dev']){$sectionsAccessIds = [1029];}
        if(
            $arCurSection['DEPTH_LEVEL'] == 3 &&
            in_array($arCurSection['ID'], Helper::getSubSectionFromParents(['IBLOCK_ID' => 6, 'ACTIVE' => 'Y'], ['ID' => $sectionsAccessIds]))
        ){
            $result = 'Аксессуары для';
        }

        return $result;
    }

    public static function getSectionHeadStringAfter($arCurSection)
    {
        $result = '';
               
        //Раздел аксессуары (чехлы ДЛЯ ...)
        $sectionsAccessIds = [1038,1028,1043,1261];
        if($_SESSION['is_dev']){$sectionsAccessIds = [1029];}
        if(
            $arCurSection['DEPTH_LEVEL'] == 4 &&
            $arCurSection['IBLOCK_SECTION_ID'] > 0 &&
            in_array($arCurSection['ID'], Helper::getSubSectionFromParents(['IBLOCK_ID' => 6, 'ACTIVE' => 'Y'], ['ID' => $sectionsAccessIds]))
        ){
            $rs = \CIblockSection::GetById($arCurSection['IBLOCK_SECTION_ID'])->GetNext();
            $name = $rs['NAME']?:'';

            if($name){
                $result = 'для ' . $name;
            }            
        }

        return $result;
    }


    public static function setParentSection($arCurSection = false)
    {
        if ($arCurSection && \Bitrix\Main\Loader::includeModule('iblock')) {
            $name = '';

            //ставим метку PARENT если нашли родителя
            if ((int)$arCurSection['IBLOCK_SECTION_ID'] > 0) {
                $rs = \CIblockSection::GetById($arCurSection['IBLOCK_SECTION_ID'])->GetNext();
                $name = $rs['NAME']?:'';
            }            

            self::replaceSeoTemplate('#PARENT#', $name);
        }
    }

    public static function setMinPriceOfSection($arCurSection = false)
    {
        global $APPLICATION;
        if ($arCurSection && \Bitrix\Main\Loader::includeModule('iblock')){
            $minPrice = Catalog::findMinPriceFormSection($arCurSection['ID']);
            self::replaceSeoTemplate('#MIN_PRICE#', $minPrice);
        }
    }

    public static function setSeoSection($sections = false, $filterChildrens = [], $arMetaInfo = false)
    {
        if(!\Bitrix\Main\Loader::includeModule('iblock') || !is_array($sections) || !$arMetaInfo) return false;

        //SECTION_META_DESCRIPTION
        //SECTION_META_KEYWORDS
        //SECTION_META_TITLE

        $sectionIds = Helper::getSubSectionFromParents(['IBLOCK_ID' => 6], ['ID' => $sections], $filterChildrens);
        foreach($sectionIds as $sectionId){
            $ipropTemplates = new \Bitrix\Iblock\InheritedProperty\SectionTemplates(6, $sectionId);
            $ipropTemplates->set($arMetaInfo);
        }
    }

    public static function replaceSeoTemplate($template = false, $value = false, $arMetaChange = false)
    {
        global $APPLICATION;
        $arMetaChange = $arMetaChange?:['title', 'description', 'keywords'];
        foreach($arMetaChange as $meta){
            $metaText = $APPLICATION->GetPageProperty($meta);                
            $metaText = str_replace($template, $value, $metaText);
            $APPLICATION->SetPageProperty($meta, $metaText);
        }
    }
}