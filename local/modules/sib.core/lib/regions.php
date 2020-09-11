<?
namespace Sib\Core;

\Bitrix\Main\Loader::includeModule('iblock');

class Regions
{
    private static $regionIblock = 46;
    private static $availablePropId = 1361;

    private static $cachePostfix = '_sib_core_regions';
    private static $cacheDir = "/sibcore/region";
    private static $cacheTime = 60 * 60 * 24 * 7;

    public static function onRegionUpdate(&$arFields)
    {
        if($arFields['IBLOCK_ID'] == self::$regionIblock && is_array($arFields['PROPERTY_VALUES'][self::$availablePropId]))
        {
            $propCode = 'SIB_AVAIL_' . $arFields['ID'];
            Catalog::createAvailProp($propCode, $arFields['NAME']);
        }
    }

    public static function getQty($productId)
    {
        if($productId)
        {
            self::updateRegionStores();
            $returnQty = 0;
            $rsStore = \CCatalogStoreProduct::GetList([],['PRODUCT_ID' => $productId, 'STORE_ID' => $_SESSION["VREGIONS_REGION"]["ID_SKLADA"]], false, false, ['AMOUNT']);
            while($arStore = $rsStore->Fetch())
            {
                $returnQty += $arStore['AMOUNT'];
            }
            return $returnQty;
        }
        return false;
    }

    public static function updateRegionStores()
    {
        /*
        global $USER;
        if(!$USER->IsAdmin())
        {
            $_SESSION["VREGIONS_REGION"]["ID_SKLADA"] = [1, 2];
            $_SESSION["VREGIONS_REGION"]["PRICE_CODE"] = ['BASE'];
        }
        */
        if($_SESSION["VREGIONS_REGION"]["ID_SKLADA"] && !is_array($_SESSION["VREGIONS_REGION"]["ID_SKLADA"]))
        {
            $_SESSION["VREGIONS_REGION"]["ID_SKLADA"] = explode(',', $_SESSION["VREGIONS_REGION"]["ID_SKLADA"]);
        }
        elseif($_SESSION["VREGIONS_REGION"]["~ID_SKLADA"])
        {
            $_SESSION["VREGIONS_REGION"]["ID_SKLADA"] = explode(',', $_SESSION["VREGIONS_REGION"]["~ID_SKLADA"]);
        }
        
    }

    public static function getLinkedCities($cityId = false)
    {
        $result = [];
        if((int)$cityId <= 0) return $result;

        $obCache = new \CPHPCache();
        if ($obCache->InitCache(self::$cacheTime, 'getLinkedCities' . $cityId . self::$cachePostfix, self::$cacheDir)) {
            $result = $obCache->GetVars();
        } elseif ($obCache->StartDataCache()) {
            if(\Bitrix\Main\Loader::includeModule('iblock')){
                $rs = \CIblockElement::GetList([], ['IBLOCK_ID' => self::$regionIblock, '=PROPERTY_REF_CITY' => $cityId], false, false, ['ID']);
                while($ob = $rs->GetNext()){
                    $result[] = $ob['ID'];
                }
            }
            $obCache->EndDataCache($result);
        }

        return $result;
    }

    public static function getRegionIdByLoc($locId = false)
    {
        $result = false;
        if(!$locId) return $result;

        $obCache = new \CPHPCache();
        if ($obCache->InitCache(self::$cacheTime, 'getRegionIdByLoc' . $locId . self::$cachePostfix, self::$cacheDir)) {
            $result = $obCache->GetVars();
            $result = $result['ID'];
        } elseif ($obCache->StartDataCache()) {
            if(\Bitrix\Main\Loader::includeModule('iblock') && \Bitrix\Main\Loader::includeModule('sale')){
                $arLocs = \CSaleLocation::GetByID($locId);
                if(!empty($arLocs['CITY_NAME_ORIG'])){
                    $rs = \CIblockElement::GetList([], ['IBLOCK_ID' => self::$regionIblock, '=NAME' => $arLocs['CITY_NAME_ORIG']], false, false, ['ID']);
                    if($ob = $rs->GetNext()){
                        $result = $ob['ID'];
                    }
                }
                
            }
            $obCache->EndDataCache(['ID' => $result]);
        }

        return $result;
    }

    public static function getRegionProps($regionId = false)
    {
        $arProps = false;
        if(!$regionId) return $arProps;

        $obCache = new \CPHPCache();
        if ($obCache->InitCache(self::$cacheTime, 'getRegionProps' . $regionId . self::$cachePostfix, self::$cacheDir)) {
            $result = $obCache->GetVars();
            $arProps = $result['RESULT'];
        } elseif ($obCache->StartDataCache()) {
            if(\Bitrix\Main\Loader::includeModule('iblock')){
                $res = \CIBlockElement::GetList(Array(), Array(
					"IBLOCK_ID" => self::$regionIblock,
					"ID"      => $regionId
				),
					false,
					false,
					Array()
				);
				if ($ob = $res->GetNextElement()){
					$arProps = $ob->GetProperties();
				}	
                
            }
            $obCache->EndDataCache(['RESULT' => $arProps]);
        }

        return $arProps;
    }

    public static function replaceRobotsTxt($search = '', $replace = '')
    {
        $rs = \CIblockElement::GetList([], ['IBLOCK_ID' => self::$regionIblock], false, false, ['ID', 'PROPERTY_ROBOTS_TXT']);
        while($ob = $rs->GetNext()){
            $ob['PROPERTY_ROBOTS_TXT_VALUE']['TEXT'] = str_replace($search, $replace, $ob['PROPERTY_ROBOTS_TXT_VALUE']['TEXT']);
            \CIBlockElement::SetPropertyValuesEx($ob['ID'], self::$regionIblock, ['ROBOTS_TXT' => $ob['PROPERTY_ROBOTS_TXT_VALUE']['TEXT']]);
        }
    }
}