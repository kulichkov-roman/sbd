<?
namespace Sib\Core;

use \Bitrix\Main\Loader;
use \Bitrix\Main\Diag\Debug;
use \Bitrix\Main\Data\Cache;

\Bitrix\Main\Loader::includeModule('catalog');
\Bitrix\Main\Loader::includeModule('iblock');
\Bitrix\Main\Loader::includeModule('sale');

class Catalog
{    
    private static $catalogIblockId = 6;
    private static $regionIblock = 46;
    private static $arCodes = [
        'AVAILABLE' => 'AVAILABLE',
        'FOR_ORDER' => 'FOR_ORDER',
        'ON_REQUEST' => 'ON_REQUEST',
        'NOT_AVAILABLE' => 'NOT_AVAILABLE',
        'REMAIN' => 'REMAIN'
    ];

    public static $defRegion = 14647;
    public static $mskRegionId = 14646;

    /*Привязка свойств*/
    private static $propSaleStick = 'SALE';
    private static $propSaleStickValue = 7;

    private static $propSaleStickMsk = 'SALE_MSK';
    private static $propSaleStickMskValue = 2509;

    private static $propHitStick = 'HIT';
    private static $propHitStickValue = 6;

    private static $propHitStickMsk = 'HIT_MSK';
    private static $propHitStickMskValue = 2510;

    private static $propNewStick = 'NEW';
    private static $propNewStickValue = 4;

    private static $propNewStickMsk = 'NEW_MSK';
    private static $propNewStickMskValue = 2559;

    public static function getGeoSiffux($regionId = false){return '_' . self::getDefRegion($regionId);}
    public static function getSort($regionId){return "propertysort_SIB_AVAIL_" . self::getDefRegion($regionId);}
    public static function getAvailablePropFilter($status = 'AVAILABLE'){
        $region = self::getDefRegion();
        return ['PROPERTY_SIB_AVAIL_' . $region => Helper::getPropValue('SIB_AVAIL_' . $region, $status)];
    }
    public static function getPriceTypeFilter()
    {
        //return !self::isMskRegion() ? [1, 3] : [2, 4];
        return Prices::getPriceTypesForRegion();
    }
    public static function getDefRegion($regionId = false){

        if(!$regionId){
            $regionId = $_SESSION['VREGIONS_REGION']['ID'];
        }

        if(self::isMskRegion($regionId)){
            $regionId = self::$mskRegionId;
        } else {
            $regionId = self::$defRegion;
        }        
        return $regionId;
    }
    public static function isMskRegion($regionId = false){
        if(!$regionId){
            $regionId = $_SESSION['VREGIONS_REGION']['ID'];
        }

        if($regionId == self::$mskRegionId) return true;

        $nearMsk = Regions::getLinkedCities(self::$mskRegionId);

        if(count($nearMsk) > 0){
            return in_array($regionId, $nearMsk);
        }

        return false;
    }
    
    public static function isDefRegion()
    {
        return $_SESSION['VREGIONS_REGION']['ID'] == self::$defRegion;
    }
    
    public static function updateAvailableStatus(&$arFields)
    {
        $returnStatus = [];

        if($arFields['IBLOCK_ID'] == self::$catalogIblockId) {

            $arStatusRemain = self::getRemainStatus();
            $arCodes = self::$arCodes;

            $dbStores = \CIblockElement::GetList([], ['IBLOCK_ID' => self::$regionIblock, '=ID' => [self::$defRegion, self::$mskRegionId]], false, false, ['ID', 'IBLOCK_ID', 'PROPERTY_ID_SKLADA']);
            $arStores = [];

            
            while($store = $dbStores->GetNext()) {
                
                $availStatus = false;
                $stores = explode(',', $store['PROPERTY_ID_SKLADA_VALUE']);

                //считаем наличие на складах
                if(count($stores) > 0) {
                    $returnQty = 0;
                    $rsStore = \CCatalogStoreProduct::GetList([],['PRODUCT_ID' => $arFields['ID'], 'STORE_ID' => $stores], false, false, ['AMOUNT']);
                    while($arStore = $rsStore->Fetch()) {
                        $returnQty += $arStore['AMOUNT'];
                    }
                                        
                    if($returnQty > 0) {   
                        $availStatus =  $arCodes['AVAILABLE'];                        
                    } else if($returnQty == 0) {
                        $availStatus =  $arCodes['NOT_AVAILABLE'];
                    }
                }

                //планируемая дата поступления
                if ($availStatus != $arCodes['AVAILABLE']) {
                    $dbRes = \CIBlockElement::GetProperty(self::$catalogIblockId, $arFields['ID'], "sort", "asc", array("CODE" => "CML2_TRAITS"));
                    while ($arRes = $dbRes->GetNext()) {
                        if ($arRes['DESCRIPTION'] == $arStatusRemain[$store['ID']]) {
                            if (strlen($arRes['VALUE']) > 0) {
                                if(strtotime($arRes['VALUE']) > time()){
                                    $availStatus = 'REMAIN';
                                }
                            }
                        }
                    }
                }

                if($availStatus){
                    $propCode = 'SIB_AVAIL_' . $store['ID'];
                    $propValue = Helper::getPropValue($propCode, $availStatus);
                
                    if(!isset($arFields['PROPERTY_' . $propCode . '_ENUM_ID']) || $arFields['PROPERTY_' . $propCode . '_ENUM_ID'] != $propValue){
                        $arSave = array($propCode => $propValue);
                        \CIBlockElement::SetPropertyValuesEx($arFields['ID'], self::$catalogIblockId, $arSave);
                    }

                    $returnStatus[$store['ID']] = $availStatus;
                }  
            }    
        }
        return $returnStatus;
    }

    public static function onSuccessCatalogImport1CHandler()
    {
        
        Debug::startTimeLabel("onSuccessCatalogImport1CHandler"); 
        if(\Bitrix\Main\Loader::includeModule('catalog') && \Bitrix\Main\Loader::includeModule('sale') && $_SESSION['BX_CML2_IMPORT']['NS']['IBLOCK_ID'] == 58){
           
            $cache = Cache::createInstance();
            if ($cache->initCache(60, "onSuccessCatalogImport1CHandler")) {
                $vars = $cache->getVars();


                self::checkHits();
            
                $arFilter = array(
                    'IBLOCK_ID' => self::$catalogIblockId,
                    'ACTIVE' => 'Y'
                );
            
                $arSelect = ['ID', 'IBLOCK_SECTION_ID', 'IBLOCK_ID', 'SHOW_COUNTER', 'PROPERTY_RBS_STORE_DATE_INFO'];
                $minPricesProps = Prices::getMinPricesInfo();
                foreach($minPricesProps as $storeId => $priceTypeId) {
                    $arSelect[] = 'PROPERTY_SIB_AVAIL_' . $storeId;
                    $arSelect[] = 'PROPERTY_SIB_MIN_PRICE_' . $storeId;
                }

                $priceTypes = Prices::getPriceTypesAssoc();
                foreach($priceTypes as $priceName => $priceTypeId) {
                    $arSelect[] = 'PROPERTY_SIB_MIN_PRICE_' . $priceName;
                    $arSelect[] = 'PRICE_' . $priceTypeId;
                }
                
                $cacheSault = md5(date('d-m-Y H:i:s'));
                
                $priceEquals = Prices::getPriceEquals();
                $arSelectProps = ['ID'];
                foreach($priceEquals as $priceTypeId => $priceDiscountId){
                    $arSelectProps[] = 'PROPERTY_' . Prices::getPriceStickerInfo($priceTypeId)['PROP_CODE'];
                }           
                
                $res = \CIBlockElement::GetList(array('ID' => 'ASC'), $arFilter, false, false, $arSelectProps);
                $props = [];
                while($ob = $res->GetNext()){
                    $props[$ob['ID']] = $ob;
                }

                $res = \CIBlockElement::GetList(array('ID' => 'ASC'), $arFilter, false, false, $arSelect);
                while ($arItem = $res->Fetch()) {
                    if(isset($props[$arItem['ID']]) && is_array($props[$arItem['ID']]) && count($props[$arItem['ID']]) > 0) {
                        $arItem = array_merge($arItem, $props[$arItem['ID']]);
                    }
                    //Debug::startTimeLabel("status_" . $arItem['ID']); 
                    $arStatus = self::updateAvailableStatus($arItem);
                    //Debug::endTimeLabel("status_" . $arItem['ID']);

                    //Debug::startTimeLabel("checkNew_" . $arItem['ID']);
                    self::checkNew($arItem, $arStatus, $cacheSault);
                    //Debug::endTimeLabel("checkNew_" . $arItem['ID']);

                    //Debug::startTimeLabel("checkDiscount_" . $arItem['ID']); 
                    self::checkDiscount($arItem, true, $priceEquals, $priceTypes);
                    //Debug::endTimeLabel("checkDiscount_" . $arItem['ID']);
                }
                
                self::clearUnstickNew();
                //self::clearDiscountsDouble();


            } elseif ($cache->startDataCache()) {
                $cache->endDataCache(array("key" => "value"));
            }
        }         
        Debug::endTimeLabel("onSuccessCatalogImport1CHandler"); 

        $timeLabels = Debug::getTimeLabels();
        //Debug::writeToFile([$timeLabels, $_SESSION['BX_CML2_IMPORT']['NS']['IBLOCK_ID']], '', '/local/logs/exchange.log');
    }
            static function clearDiscountsDouble()
            {
                $db_res = \CSaleDiscount::GetList(array("SORT" => "ASC"),array("ACTIVE" => "Y"),false,false,array());
                $arAllDiscounts = [];
                while($ob = $db_res->GetNext()){
                    $arAllDiscounts[$ob['NAME']][] = $ob['ID'];
                }
                foreach($arAllDiscounts as $discount){
                    if(count($discount) > 1){
                        \CSaleDiscount::Delete($discount[0]);
                    }
                }
            }

            public static function checkNew($arFields, $arStatus = false, $cacheSault = false)
            {
                if(empty($arStatus)) return false;

                $arAvailableDateInfo = unserialize(htmlspecialchars_decode($arFields['PROPERTY_RBS_STORE_DATE_INFO_VALUE']));

                $arAvailableDateInfoUpdated = $arAvailableDateInfo;
                $arCodes = self::$arCodes;
                foreach($arStatus as $storeId => $status){
                    if($status == $arCodes['AVAILABLE'] || $status == $arCodes['REMAIN']){
                        $arAvailableDateInfoUpdated[$storeId]['DATE'] = date('Y-m-d');
                    }

                    if(empty($arAvailableDateInfoUpdated[$storeId]['DATE'])){
                        $arAvailableDateInfoUpdated[$storeId]['DATE'] = date('Y-m-d');
                    }
    
                    if (empty($arAvailableDateInfoUpdated[$storeId]['STICK'])) {
                        $arAvailableDateInfoUpdated[$storeId]['STICK'] = '';
                    }

                    $arAvailableDateInfoUpdated[$storeId]['STATUS'] = $status;
                }
                
                $isUnstickItem = in_array($arFields['IBLOCK_SECTION_ID'], Helper::getUnstickSections('UF_NEWS', $cacheSault));

                $needToUpd = false;            
                foreach($arAvailableDateInfoUpdated as $storeId => $info){
                    if(!isset($arAvailableDateInfo[$storeId])){$needToUpd = true; continue;}
                                    
                    $arSetPropNew = [self::$propNewStick => self::$propNewStickValue];
                    if($storeId == self::$mskRegionId){
                        $arSetPropNew = [self::$propNewStickMsk => self::$propNewStickMskValue];
                    }
    
                    $lastInfo = $arAvailableDateInfo[$storeId];
                    if($info['STATUS'] != $lastInfo['STATUS'] || $info['DATE'] != $lastInfo['DATE']){$needToUpd = true;}
                    
                    $chagedStatusToAvail = $info['STATUS'] == $arCodes['AVAILABLE'];
                    $isNew = empty($lastInfo['STICK']);
                    
                    if($chagedStatusToAvail && $isNew && !$isUnstickItem){
                        $arAvailableDateInfoUpdated[$storeId]['STICK'] = date('Y-m-d');                    
                        \CIBlockElement::SetPropertyValuesEx($arFields['ID'], self::$catalogIblockId, $arSetPropNew);
                        $needToUpd = true;
                    } else if(!empty($arAvailableDateInfoUpdated[$storeId]['STICK']) && !$isUnstickItem){
                        $dateTimeLast = new \DateTime($arAvailableDateInfoUpdated[$storeId]['STICK']);
                        $dateTimeCurrent = new \DateTime(date('Y-m-d'));
                        $isOld = $dateTimeCurrent->diff($dateTimeLast)->days >= 30 || $info['STATUS'] != $arCodes['AVAILABLE'];
                        $newStick = $storeId == self::$mskRegionId ? self::$propNewStickMsk : self::$propNewStick;

                        if($isOld){                            
                            \CIBlockElement::SetPropertyValuesEx($arFields['ID'], self::$catalogIblockId, [$newStick => false]);
                        } else {
                            \CIBlockElement::SetPropertyValuesEx($arFields['ID'], self::$catalogIblockId, $arSetPropNew);
                        }
                    }
                }
                
                if($needToUpd){
                    \CIBlockElement::SetPropertyValuesEx($arFields['ID'], self::$catalogIblockId, ['RBS_STORE_DATE_INFO' => serialize($arAvailableDateInfoUpdated)]);
                }     
            }

            public static function clearUnstickNew()
            {
                $sectionIds = Helper::getUnstickSections('UF_NEWS', md5(date('d-m-Y H:i:s')));

                $arNewStickers = [
                    'PROPERTY_NEW_VALUE' => self::$propNewStick,
                    'PROPERTY_NEW__MSK_VALUE' => self::$propNewStickMsk
                ];

                foreach($arNewStickers as $prop => $stick){
                    $arFilterNews = [
                        'IBLOCK_ID' => self::$catalogIblockId,
                        'IBLOCK_SECTION_ID' => $sectionIds,
                        $prop => 'Y'
                    ];
                    $rs = \CIblockElement::GetList([],$arFilterNews,false,false,['ID']);
                    while($ob = $rs->GetNext()){
                        \CIBlockElement::SetPropertyValuesEx($ob['ID'], self::$catalogIblockId, [$stick => false]);
                    }
                }
            }

            public static function isNewProduct($arResult = false, $cityId = false)
            {
                $isNew = false;

                $arAvailableDateInfo = $arResult['PROPERTIES']['RBS_STORE_DATE_INFO']['VALUE'];
                if(!empty($arAvailableDateInfo)){
                    $arAvailableDateInfo = unserialize(htmlspecialchars_decode($arAvailableDateInfo));
                    if(self::isMskRegion($cityId)){
                        $cityId = self::$mskRegionId;
                    } else {
                        $cityId = self::$defRegion;
                    }
                    if(isset($arAvailableDateInfo[$cityId])){
                        $isNew = empty($arAvailableDateInfo[$cityId]['STICK']);
                    }
                }
                
                return $isNew;
            }

            public static function checkHits()
            {
                if (\Bitrix\Main\Loader::includeModule('iblock')) {
                    
                    //fill nsk
                    $arrFilters = [
                        'ALL' => [['LOGIC' => 'OR', ['>CATALOG_STORE_AMOUNT_1' => '0'], ['>CATALOG_STORE_AMOUNT_2' => '0']]],
                        'MSK' => [['LOGIC' => 'OR', ['>CATALOG_STORE_AMOUNT_15' => '0'], ['>CATALOG_STORE_AMOUNT_16' => '0']]]
                    ];
                    $unStickSections = Helper::getUnstickSections('UF_HITS', 1);

                    foreach($arrFilters  as $codeFilter => $filter){

                        //clear
                        $propHitStickFilter = self::$propHitStick;
                        $arSetProp = [self::$propHitStick => self::$propHitStickValue];
                        if($codeFilter == 'MSK'){
                            $propHitStickFilter = self::$propHitStickMsk;
                            $arSetProp = [self::$propHitStickMsk => self::$propHitStickMskValue];
                        }

                        if(count($unStickSections) > 0){
                            $filter[] = ['!IBLOCK_SECTION_ID' => $unStickSections];
                        }

                        $elRes = \CIblockElement::GetList([], ['IBLOCK_ID' => self::$catalogIblockId, '!PROPERTY_' . $propHitStickFilter => false], false, false, ['ID']);
                        $elWithProp = [];
                        while($obEl = $elRes->Fetch()){
                            \CIBlockElement::SetPropertyValuesEx($obEl['ID'], self::$catalogIblockId, [$propHitStickFilter => false]);
                        }

                        $res = \CIblockElement::GetList(['SHOWS' => 'DESC'], array_merge(['IBLOCK_ID' => self::$catalogIblockId], $filter), false, ['nTopCount' => 12], ['ID']);
                        while($ob = $res->GetNext()){                    
                            \CIBlockElement::SetPropertyValuesEx($ob['ID'], self::$catalogIblockId, $arSetProp);
                        }
                    }
                }
            }

            public static function checkDiscountPrice(&$arItem)
            {
                $priceArray = \Sib\Core\Catalog::getDiscountPriceArray($arItem['ID']);
                if((int)$priceArray['PRICE_DISCOUNT'] > 0){
                    $arItem['MIN_PRICE']['DISCOUNT_DIFF'] = $priceArray['BASE_PRICE'] - $priceArray['PRICE_DISCOUNT'];
                    $arItem['MIN_PRICE']['VALUE'] = $priceArray['BASE_PRICE'];
                    $arItem['MIN_PRICE']['DISCOUNT_VALUE'] = $priceArray['PRICE_DISCOUNT'];
                    foreach ($arItem['PRICES'] as $k => $price) {
                        $arItem['PRICES'][$k]['DISCOUNT_DIFF'] = $arItem['MIN_PRICE']['DISCOUNT_DIFF'];
                        $arItem['PRICES'][$k]['DISCOUNT_VALUE'] = $arItem['MIN_PRICE']['DISCOUNT_VALUE'];
                        $arItem['PRICES'][$k]['VALUE'] = $arItem['MIN_PRICE']['VALUE'];
                        break;
                    }
                }
            }
            
            public static function getDiscountPrice($productId)
            {
                $arPrices = \CPrice::GetList([],['PRODUCT_ID' => $productId]);
                $prices = [];
                while($obPrice = $arPrices->GetNext()){
                    $prices[$obPrice['CATALOG_GROUP_ID']] = $obPrice['PRICE'];
                }

                $arPriceTypesParams = [];
                $minPricesProps = Prices::getMinPricesInfo();
                $priceEquals = Prices::getPriceEquals();
                foreach($minPricesProps as $storeId => $pricieType){
                    $arPriceTypesParams[$storeId] = [
                        'PRICE_DISCOUNT' => (int)$prices[$priceEquals[$pricieType]],
                        'BASE_PRICE' => (int)$prices[$pricieType]
                    ];
                }

                return $arPriceTypesParams;
            }

            public static function getDiscountPriceArray($productId)
            {
                $arPrices = \CPrice::GetList([],['PRODUCT_ID' => $productId]);
                $prices = [];
                while($obPrice = $arPrices->GetNext()){
                    $prices[$obPrice['CATALOG_GROUP_ID']] = $obPrice['PRICE'];
                }

                $priceTypeRegion = Prices::getPriceTypeRegion();
                $priceEquals = Prices::getPriceEquals();

                return [
                    'PRICE_DISCOUNT' =>  (int)$prices[$priceEquals[$priceTypeRegion]],
                    'BASE_PRICE' => (int)$prices[$priceTypeRegion]
                ];
            }

            public static function checkDiscount($arItem, $addSticker = true, $priceEquals = [], $priceTypes = [])
            {
                if(is_numeric($arItem)){
                    $productId = $arItem;
                } else if(is_numeric($arItem['ID'])) {
                    $productId = $arItem['ID'];
                } else {
                    return;
                }

                if(empty($priceEquals)){
                    $priceEquals = Prices::getPriceEquals();
                }

                $prices = [];
                foreach($arItem as $field => $value){
                    if(strpos($field, 'PRICE_') === 0){
                        $prices[str_replace('PRICE_', '', $field)] = $value;
                    }
                }

                $arPriceTypesParams = [];
                foreach($priceEquals as $priceTypeId => $priceDiscountId){
                    $arPriceTypesParams[$priceTypeId] = [
                        'PRICE_DISCOUNT' => (int)$prices[$priceDiscountId],
                        'BASE_PRICE' => (int)$prices[$priceTypeId],
                        'STICKER' => Prices::getPriceStickerInfo($priceTypeId)
                    ];
                }
             
                foreach($arPriceTypesParams as $priceTypeId => $arPriceParams){
                    
                    //$arDiscount = \CSaleDiscount::GetList([],['NAME' => '[AUTO] ' . $productId . ' (' . $priceTypeId . ')']);

                    $diff = $arPriceParams['BASE_PRICE'] - $arPriceParams['PRICE_DISCOUNT'];

                    $isAvailableDiscount = $arPriceParams['PRICE_DISCOUNT'] > 0 && $diff > 0;

                    $isStickerSet = $arItem['PROPERTY_' . $arPriceParams['STICKER']['PROP_CODE'] . '_VALUE'] === 'Y';
                    /* if($obDiscount = $arDiscount->GetNext()){
                        $isAvailableDiscount = true;
                    } */

                    
                    if($isAvailableDiscount){        
                        /* $discountFields = Helper::getDiscountArray($productId, $diff, $priceTypeId);  

                        if($isAvailableDiscount){
                            if($diff != $obDiscount['DISCOUNT_VALUE'])
                                \CSaleDiscount::Update($obDiscount['ID'], $discountFields);
                        } else {                    
                            \CSaleDiscount::Add($discountFields);
                        } */

                        if($addSticker && !$isStickerSet){
                            \CIBlockElement::SetPropertyValuesEx($productId, self::$catalogIblockId, [$arPriceParams['STICKER']['PROP_CODE'] => $arPriceParams['STICKER']['PROP_VALUE']]);
                        }
                        
                    } else if($isStickerSet) {
                        \CIBlockElement::SetPropertyValuesEx($productId, self::$catalogIblockId, [$arPriceParams['STICKER']['PROP_CODE'] => false]);
                        //\CSaleDiscount::Delete($obDiscount['ID']);
                    }
                }

                if(is_numeric($arItem['ID'])) {
                    $arChangedVals = [];

                    if(empty($priceTypes)){
                        $priceTypes = Prices::getPriceTypesAssoc();
                    }
                    
                    foreach($priceTypes as $priceName => $priceTypeId) {
                        $currentValue = (int)$arItem['PROPERTY_SIB_MIN_PRICE_' . $priceName . '_VALUE'];
                        $arCurrentPrices = [];
                        $arCurrentPrices = $arPriceTypesParams[$priceTypeId];

                        $currentMinPrice = $arCurrentPrices['BASE_PRICE'];
                        $diff = $arCurrentPrices['BASE_PRICE'] - $arCurrentPrices['PRICE_DISCOUNT'];
                        if ($arCurrentPrices['PRICE_DISCOUNT'] > 0 && $diff > 0) {
                            $currentMinPrice = $arCurrentPrices['PRICE_DISCOUNT'];
                        }
    
                        if((int)$currentMinPrice !== (int)$currentValue){
                            $arChangedVals['SIB_MIN_PRICE_' . $priceName] = (int)$currentMinPrice;
                        }
                    }

                    if(count($arChangedVals) > 0){
                        \CIBlockElement::SetPropertyValuesEx($productId, self::$catalogIblockId, $arChangedVals);
                    }
                }
               
            }

            public static function checkRemainStatus($cml2traits)
            {
                if(empty($cml2traits)) return false;
                
                $cityId = $_SESSION["VREGIONS_REGION"]["ID"];
                if(self::isMskRegion($cityId)){
                    $cityId = self::$mskRegionId;
                } else {
                    $cityId = self::$defRegion;
                }

                $arStatusRemain = self::getRemainStatus();
                $datePostup = '';
                if(!is_array($cml2traits) && (int)$cml2traits > 0){
                    $dbRes = \CIBlockElement::GetProperty(self::$catalogIblockId, (int)$cml2traits, "sort", "asc", array("CODE" => "CML2_TRAITS"));
                    while ($arRes = $dbRes->GetNext()) {
                        if ($arRes['DESCRIPTION'] == $arStatusRemain[$cityId]) {
                            $datePostup = $arRes['VALUE'];
                            break;
                        }
                    } 
                } else {
                    foreach ($cml2traits['DESCRIPTION'] as $keyTemp => $itemTemp) {
                        if ($itemTemp == $arStatusRemain[$cityId]) {
                            $datePostup = $cml2traits['VALUE'][$keyTemp];
                            break;
                        }
                    }
                }
                return $datePostup;
            }


    //for clear on demand
    public static function clearDiscountsNew()
    {
        if(\Bitrix\Main\Loader::includeModule('sale')){
            $dbDiscounts = \CSaleDiscount::GetList([],['%NAME' => '[AUTO]']);
            while($ob = $dbDiscounts->GetNext())
                \CSaleDiscount::Delete($ob['ID']);
        }     
        if(\Bitrix\Main\Loader::includeModule('iblock')){
            $elRes = \CIblockElement::GetList([], ['IBLOCK_ID' => self::$catalogIblockId, '!PROPERTY_' . self::$propSaleStick => false], false, false, ['ID']);
            //$elWithProp = [];
            while($obEl = $elRes->Fetch()){
                \CIBlockElement::SetPropertyValuesEx($obEl['ID'], self::$catalogIblockId, [self::$propSaleStick => false]);
            }
            
            $elRes = \CIblockElement::GetList([], ['IBLOCK_ID' => self::$catalogIblockId, '!PROPERTY_' . self::$propSaleStickMsk => false], false, false, ['ID']);
            //$elWithProp = [];
            while($obEl = $elRes->Fetch()){
                \CIBlockElement::SetPropertyValuesEx($obEl['ID'], self::$catalogIblockId, [self::$propSaleStickMsk => false]);
            }
        }    
    }

    public static function updateAvailableStatusAll()
    {
        //if(!Helper::checkTime('updateAvailableStatusAll')) return;
        if (\Bitrix\Main\Loader::includeModule('iblock')) {
            $arFilter = array(
                'IBLOCK_ID' => self::$catalogIblockId,
                'ACTIVE' => 'Y', 
            ); 

            $arSelect = ['ID', 'IBLOCK_SECTION_ID', 'IBLOCK_ID'];
            $dbStores = \CIblockElement::GetList([], ['IBLOCK_ID' => self::$regionIblock, '!PROPERTY_AVAILABLE' => false], false, false, ['ID', 'IBLOCK_ID', 'PROPERTY_ID_SKLADA']);
            $arStores = [];
            while ($store = $dbStores->GetNext()) {
                if(!isset($arStores[$store['PROPERTY_ID_SKLADA_VALUE']])){
                    $arStores[$store['PROPERTY_ID_SKLADA_VALUE']] = true;
                    $arSelect[] = 'PROPERTY_SIB_AVAIL_' . $store['ID'];
                } else {
                    continue;
                }
            }
            
            $res = \CIBlockElement::GetList(array('ID' => 'ASC'), $arFilter, false, false, $arSelect);
            while ($ob = $res->GetNext()) {
                self::updateAvailableStatus($ob);
            }
        }
    }
    
    public static function getRemainStatus()
    {
        return [
            self::$mskRegionId => 'Планируемая дата поступления Москва',
            self::$defRegion => 'Планируемая дата поступления Новосибирск',
        ];
    }

    public static function findMinPriceFormSection($sectionId = false)
    {
        $arFilter = ['IBLOCK_ID' => self::$catalogIblockId, 'SECTION_ID' => $sectionId, 'INCLUDE_SUBSECTIONS' => 'Y'];
        $rs = \CIblockElement::GetList([], array_merge($arFilter, self::getAvailablePropFilter()), false, false, ['ID', 'IBLOCK_ID']);
        if($rs->SelectedRowsCount() <= 0){
            $rs = \CIblockElement::GetList([], $arFilter, false, false, ['ID', 'IBLOCK_ID']);
        }

        $pids = [];
        while($ob = $rs->GetNext()){
            $pids[] = $ob['ID'];
        }

        if(count($pids) > 0){
            $rsPrice = \CPrice::GetList(['PRICE' => 'ASC'], ['PRODUCT_ID' => $pids, 'CATALOG_GROUP_ID' => Prices::getPriceTypesForRegion()]);
            while($obPrice = $rsPrice->GetNext()){
                if($obPrice['PRICE'] > 0){
                    return (int)$obPrice['PRICE'];
                }
            }
        }       

        return '';
    }

    public static function getTabPropertySale()
    {
        $stickers = Prices::getPriceStickers();
        $priceTypeRegion = Prices::getPriceTypeRegion();

        if(isset($stickers[$priceTypeRegion])){
            return $stickers[$priceTypeRegion]['CODE'];
        }

        return  self::isMskRegion($_SESSION["VREGIONS_REGION"]["ID"]) ? self::$propSaleStickMsk : self::$propSaleStick;
    }

    public static function getTabPropertySaleLink()
    {
        $stickers = Prices::getPriceStickers();
        $priceTypeRegion = Prices::getPriceTypeRegion();

        if(isset($stickers[$priceTypeRegion])){
            $r = \Bitrix\Iblock\PropertyTable::getList(['filter' => ['IBLOCK_ID' => self::$catalogIblockId, 'CODE' => $stickers[$priceTypeRegion]['CODE']], 'cache' => ['ttl' => 86400]])->fetchAll();
            if(count($r) === 1){
                $prop = array_pop($r);
                if($prop['ID'] > 0){
                    return 'arrFilter_' . $prop['ID'] . '_' . crc32($stickers[$priceTypeRegion]['VALUE']);
                }
            }
        }

        return self::isMskRegion($_SESSION["VREGIONS_REGION"]["ID"]) ? 'arrFilter_1497_3649402550' : 'arrFilter_24_1790921346';
    }

    public static function getTabPropertyHit()
    {
        return self::isMskRegion($_SESSION["VREGIONS_REGION"]["ID"]) ? self::$propHitStickMsk : self::$propHitStick;
    }

    public static function getTabPropertyHitLink()
    {
        return self::isMskRegion($_SESSION["VREGIONS_REGION"]["ID"]) ? 'arrFilter_1498_3108175699' : 'arrFilter_23_498629140';
    }

    public static function getTabPropertyNew()
    {
        return self::isMskRegion($_SESSION["VREGIONS_REGION"]["ID"]) ? self::$propNewStickMsk : self::$propNewStick;
    }

    public static function getTabPropertyNewLink()
    {
        return self::isMskRegion($_SESSION["VREGIONS_REGION"]["ID"]) ? 'arrFilter_1526_2767356659' : 'arrFilter_21_4088798008';
    }

    public static function createAvailProp($propCode, $propName)
    {
        $rs = \CIBlockProperty::GetByID($propCode, self::$catalogIblockId);
        if(!$ar = $rs->Fetch())
        {
            $arCodes = self::$arCodes;
            $arNewPropFields = array(
                "NAME" => "Наличие",
                "ACTIVE" => "Y",
                "SORT" => "100005",
                "CODE" => $propCode,
                "PROPERTY_TYPE" => "L",
                "DISPLAY_TYPE" => "F",
                "IBLOCK_ID" => self::$catalogIblockId,
                "SMART_FILTER" => "N",
                "DISPLAY_EXPANDED" => "Y",
                "VALUES" => array(
                    0 => array(
                        "VALUE" => "В наличии",
                        "DEF" => "Y",
                        "SORT" => "100",
                        'XML_ID' => $arCodes['AVAILABLE'],
                    ),
                    1 => array(
                        "VALUE" => "Ожидается поступление",
                        "DEF" => "N",
                        "SORT" => "200",
                        'XML_ID' => $arCodes['REMAIN'],
                    ),
                    2 => array(
                        "VALUE" => "По запросу",
                        "DEF" => "N",
                        "SORT" => "300",
                        'XML_ID' => $arCodes['ON_REQUEST'],
                    ),
                    3 => array(
                        "VALUE" => "Под заказ",
                        "DEF" => "N",
                        "SORT" => "400",
                        'XML_ID' => $arCodes['FOR_ORDER'],
                    ),
                    4 => array(
                        "VALUE" => "Нет в наличии",
                        "DEF" => "N",
                        "SORT" => "500",
                        'XML_ID' => $arCodes['NOT_AVAILABLE'],
                    )
                )
            );
            $arNewPropFields['HINT'] = $propName;
            $ibp = new \CIBlockProperty;
			$ibp->Add($arNewPropFields);
        }			
    }

    //Нужно удалить события
    public static function onCompleteCatalogImport1CHandler(){}
    public static function onSuccessCatalogImport1C(){}
    public static function clearDiscounts(){}

    //\RegisterModuleDependences("catalog", 'OnBeforeCatalogImport1C', "sib.core", "\Sib\Core\Catalog", "clearDiscounts");
}