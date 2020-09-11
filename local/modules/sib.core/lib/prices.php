<?
namespace Sib\Core;

\Bitrix\Main\Loader::includeModule('iblock');
\Bitrix\Main\Loader::includeModule('catalog');

class Prices
{
    static function getPriceRegion()
    {
        $regionId = $_SESSION['VREGIONS_REGION']['ID'];
        if($regionId > 0){
            return $_SESSION["VREGIONS_REGION"]["PRICE_CODE"][0];
        } else {
            return 'BASE';
        }
    }

    static function getPriceTypeRegion()
    {
        $priceCode = self::getPriceRegion();
        $types = self::getPriceTypesAssoc();

        return $types[$priceCode];
    }

    static function getPriceStickers()
    {
        $def = \COption::GetOptionString('sib.core', 'price_stickers');
        if(!empty($def)){
            $explPrices = \explode('|', $def);
            $result = [];
            foreach($explPrices as $price){
                $explType = explode(':', $price);
                $explVal = explode('#', $explType[1]);
                $result[$explType[0]] = [
                    'CODE' => $explVal[0],
                    'VALUE' => $explVal[1]
                ];
            }
            return $result;
        }

        return [];
    }

    static function getPriceTypesForRegion()
    {
        $types = self::getPriceTypesAssoc();
        $discount = self::getPriceEquals();
        $mainType = $types[self::getPriceRegion()];

        return [$mainType, $discount[$mainType]];
    }

    static function getDiscountPriceRegion()
    {
        //self::getPriceRegion()
    }

    static function getPriceEquals()
    {
        $def = \COption::GetOptionString('sib.core', 'price_equals');
        if(!empty($def)){
            $explPrices = \explode('|', $def);
            $result = [];
            foreach($explPrices as $price){
                $explType = explode(':', $price);
                $result[$explType[0]] = $explType[1];
            }
            return $result;
        } else {
            return [
                1 => 3,
                2 => 4
            ];
        }
    }

    static function getPriceStickerInfo($pricTypeId = 0)
    {
        $obCache = new \CPHPCache();
        $arResult = [];

        if ($obCache->InitCache(86400 * 365, 'getPriceStickerInfo' . $pricTypeId, '/sib_core')) {
            $arResult = $obCache->GetVars();
        } else {
            $priceStickerList = self::getPriceStickers();
            if(isset($priceStickerList[$pricTypeId])){
                $arResult = [
                    'PROP_CODE' => $priceStickerList[$pricTypeId]['CODE'],
                    'PROP_VALUE' => $priceStickerList[$pricTypeId]['VALUE']
                ];
            }      
            if($obCache->StartDataCache()){
                $obCache->EndDataCache($arResult);
            }     
        }

        return $arResult;
    }

    static function getMinPriceSortSuffix()
    {
        return '_' . self::getPriceRegion();


        /* $minPriceRegions = self::getMinPricesInfo();
        if(isset($minPriceRegions[$_SESSION['VREGIONS_REGION']['ID']])){
            return '_' . $_SESSION['VREGIONS_REGION']['ID'];
        }

        return Catalog::getGeoSiffux(); */
    }

    static function getMinPricesInfo()
    {
        $obCache = new \CPHPCache();
        if ($obCache->InitCache(86400 * 365, 'getMinPricesInfo', '/sib_core')) {
            $storesProp = $obCache->GetVars();
        } else {
            $rsProps = \CIblockProperty::GetList([],['IBLOCK_ID' => 6, 'CODE' => 'SIB_MIN_PRICE_%']);
            $allPrices = \Bitrix\Catalog\GroupTable::getList()->fetchAll();
            $priceType = self::getPriceTypesAssoc();
            $storesProp = [];
            while($obProp = $rsProps->GetNext()){
                $regionId = str_replace('SIB_MIN_PRICE_','',$obProp['CODE']);
                $rsRegion = \CIblockElement::GetList([],['IBLOCK_ID' => 46, 'ID' => $regionId], false, false, ['PROPERTY_PRICE_CODE'])->GetNext();
                $storesProp[$regionId] = $priceType[$rsRegion['PROPERTY_PRICE_CODE_VALUE']];
            }
        }

        return $storesProp;
    }

    static function getPriceTypesAssoc()
    {
        $allPrices = \Bitrix\Catalog\GroupTable::getList(['cache' => ['ttl' => 86400*365]])->fetchAll();
        $priceType = [];
        foreach($allPrices as $price){
            $priceType[$price['NAME']] = $price['ID'];
        }
        return $priceType;
    }
}