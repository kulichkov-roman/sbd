<?
namespace Sib\Core;

/* use \Bitrix\Main\Loader;

\Bitrix\Main\Loader::includeModule('catalog');
\Bitrix\Main\Loader::includeModule('iblock');
\Bitrix\Main\Loader::includeModule('sale'); */

class Helper
{
    private static $cachePostfix = '_sib_core_helper';
    private static $cacheDir = "/sibcore/helper";
    private static $cacheTime = 60 * 60 * 24 * 7;

    private static $catalogIblockId = 6;
    private static $regionIblockId = 46;

    public static function getDefRegionId()
    {
        return Catalog::getDefRegion($_SESSION["VREGIONS_REGION"]["ID"]);
    }

    public static function getRealReviewYm()
    {
        $result = 0;

        if(\Bitrix\Main\Loader::includeModule('iblock')){
            $currentReview = \CIBlock::GetElementCount(54);
            $result = ceil($currentReview + ($currentReview * 0.81));
        }
        
        return $result;
    }

    static function getVideoReviews($sectionId = 0)
    {
        if(!$sectionId) return [];

        $rs = \CIblockElement::GetList(
            ['SORT' => 'ASC'],
            ['IBLOCK_ID' => 57, 'PROPERTY_ITEM_CATALOG' => $sectionId, '!PROPERTY_LINK' => false],
            false,
            false,
            ['ID', 'PROPERTY_LINK']
        );

        $result = [];
        while($ob = $rs->GetNext()){
            $result[] = $ob['PROPERTY_LINK_VALUE'];
        }

        return $result;
    }

    static function getBlogReviews($sectionId = 0)
    {
        if(!$sectionId) return [];

        $rs = \CIblockElement::GetList(
            ['SORT' => 'ASC'],
            ['IBLOCK_ID' => 1, 'PROPERTY_BLOG_ITEM_CATALOG' => $sectionId],
            false,
            false,
            ['ID', 'NAME', 'PREVIEW_PICTURE', 'DETAIL_PICTURE', 'DETAIL_PAGE_URL']
        );

        $result = [];
        while($ob = $rs->GetNext()){
            $imgArray = [];
            if($ob['PREVIEW_PICTURE']){
                $imgArray['WEBP'] = \CResizer2Resize::ResizeGD2(\CFile::GetPath($ob['PREVIEW_PICTURE']), 50);
                $imgArray['JPG'] = \CResizer2Resize::ResizeGD2(\CFile::GetPath($ob['PREVIEW_PICTURE']), 51);
            } else if($ob['DETAIL_PICTURE']){
                $imgArray['WEBP'] = \CResizer2Resize::ResizeGD2(\CFile::GetPath($ob['DETAIL_PICTURE']), 50);
                $imgArray['JPG'] = \CResizer2Resize::ResizeGD2(\CFile::GetPath($ob['DETAIL_PICTURE']), 51);
            }
            $ob['IMG'] = $imgArray;
            $result[] = $ob;
        }

        return $result;
    }

    public static function getDiscountPrice($pid = 0)
    {
        $obCache = new \CPHPCache();
        if ($obCache->InitCache(3600, 'getDiscountPrice_upd' . Prices::getPriceTypeRegion() . self::$cachePostfix . $pid, self::$cacheDir)) {
            $result = $obCache->GetVars();
        } else {
            $result = Catalog::getDiscountPrice($pid);
            if($obCache->StartDataCache()){
                $obCache->EndDataCache($result);
            }            
        }
        return $result;
    }

    public static function getDiscountPriceArray($pid = 0)
    {
        $obCache = new \CPHPCache();
        if ($obCache->InitCache(3600, 'getDiscountPriceArray_upd'  . Prices::getPriceTypeRegion() . self::$cachePostfix . $pid, self::$cacheDir)) {
            $result = $obCache->GetVars();
        } else {
            $result = Catalog::getDiscountPriceArray($pid);
            if($obCache->StartDataCache()){
                $obCache->EndDataCache($result);
            }            
        }
        return $result;
    }

    public static function onFileDelete($fileDelete)
    {
        //define("LOG_FILENAME", $_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/test2.txt");
        if($fileDelete['MODULE_ID'] === 'iblock'){
            $key = md5('/upload/' . $fileDelete['SUBDIR'] . '/' . $fileDelete['FILE_NAME']);
            if(\Bitrix\Main\Loader::includeModule('yenisite.resizer2')){
                $rs = \Yenisite\Resizer2\SetFileTable::getList(array(
                    'filter' => array(
                        '=KEY' => $key
                    )
                ));
                $arPrimaryKey = array_flip(\Yenisite\Resizer2\SetFileTable::getEntity()->getPrimaryArray());
                while ($arSetFile = $rs->Fetch()) {
                    $arPrimary = array_intersect_key($arSetFile, $arPrimaryKey);

                    unlink($_SERVER['DOCUMENT_ROOT'] . \CFile::GetPath($arSetFile['FILE_ID']));
                    \CFile::Delete($arSetFile['FILE_ID']);
                    \Yenisite\Resizer2\SetFileTable::delete($arPrimary);
                }
            }
        }
    }

    public static function isSmartphoneSectionChild($leftMargin = 0, $rightMargin = 0)
    {
        $obCache = new \CPHPCache();
        $arSmartPhoneSection = [];
        if ($obCache->InitCache(self::$cacheTime, 'smartphone_section' . self::$cachePostfix, self::$cacheDir)) {
            $arSmartPhoneSection = $obCache->GetVars();
        } elseif ($obCache->StartDataCache()) {
            if(\Bitrix\Main\Loader::includeModule('iblock')){
                $rs = \CIblockSection::GetList([], ['IBLOCK_ID' => self::$catalogIblockId, 'ID' => 52]);
                if($arSmartPhoneSection = $rs->GetNext()){
                    $obCache->EndDataCache($arSmartPhoneSection);
                }
            }
        }

        return $arSmartPhoneSection['LEFT_MARGIN'] < $leftMargin && $arSmartPhoneSection['RIGHT_MARGIN'] > $rightMargin;
    }

    public static function isSmarPhoneItem($itemId = false)
    {
        if(!\Bitrix\Main\Loader::includeModule('iblock') || !$itemId) return false;

        $rs = \CIblockElement::GetList([], ['IBLOCK_ID' => 6, 'ID' => $itemId], false, false, ['IBLOCK_SECTION_ID']);
        if($ob = $rs->GetNext()){
            if((int)$ob['IBLOCK_SECTION_ID'] > 0){
                return in_array($ob['IBLOCK_SECTION_ID'], self::getSubSectionFromParents(['IBLOCK_ID' => 6], ['ID' => 52]));
            }
        }

        return false;
    }

    public static function getItemInf($itemId = false)
    {
        if(!$itemId || !\Bitrix\Main\Loader::includeModule('iblock')) return false;

        $smartSubSec = self::getSubSectionFromParents(['IBLOCK_ID' => 6, 'ACTIVE' => 'Y'], ['ID' => 52]);
        $smartSubSecCute = self::getSubSectionFromParents(['IBLOCK_ID' => 6, 'ACTIVE' => 'Y'], ['UF_CUTE_FROM_YM' => 1]);
        $itemInf = \CIblockElement::GetList([], ['IBLOCK_ID' => 6, 'ID' => $itemId], false, false, ['ID', 'NAME', 'IBLOCK_SECTION_ID'])->GetNext();

        if(in_array($itemInf['IBLOCK_SECTION_ID'], $smartSubSec) && !in_array($itemInf['IBLOCK_SECTION_ID'], $smartSubSecCute)){
            $itemInf['IS_SMARTPHONE'] = true;
        }

        return $itemInf;
    }

    public static function getOtherItemFromSection($pid = 0)
    {
        $obCache = new \CPHPCache();
        $arElementInfo = [];
        if ($obCache->InitCache(self::$cacheTime, 'el_info' . self::$cachePostfix . $pid, self::$cacheDir)) {
            $result = $obCache->GetVars();
        } elseif ($obCache->StartDataCache()) {
            if(\Bitrix\Main\Loader::includeModule('iblock')){
                $rs = \CIblockElement::GetList([], ['IBLOCK_ID' => self::$catalogIblockId, 'ID' => $pid]);
                $result = [];
                if($arElementInfo = $rs->GetNext()){


                    $rsSiblings = \CIblockElement::GetList([], ['IBLOCK_ID' => self::$catalogIblockId, 'IBLOCK_SECTION_ID' => $arElementInfo['IBLOCK_SECTION_ID']]);
                    
                    while($obSibling = $rsSiblings->GetNext()){
                        $result[] = $obSibling['ID'];
                    }
                    
                    $obCache->EndDataCache($result);
                }
            }
        }

        return $result;
    }

    public static function getSubdomains()
    {
        if(!\Bitrix\Main\Loader::includeModule('iblock')) return false;

        $rs = \CIblockElement::GetList([], ['IBLOCK_ID' => 46, 'ACTIVE' => 'Y'], false, false, ['ID', 'CODE']);
        $subDomains = '';
        while($ob = $rs->GetNext()){
            if(!empty($ob['CODE']))
                $subDomains .= $ob['CODE'] . '.sibdroid.ru' . PHP_EOL;
        }

        return $subDomains;
    }

    public static function getElementInfo($pid = 0)
    {
        $obCache = new \CPHPCache();
        $arElementInfo = [];
        if ($obCache->InitCache(self::$cacheTime, 'el_info_2' . self::$cachePostfix . $pid, self::$cacheDir)) {
            $arElementInfo = $obCache->GetVars();
        } elseif ($obCache->StartDataCache()) {
            if(\Bitrix\Main\Loader::includeModule('iblock')){
                $rs = \CIblockElement::GetList([], ['IBLOCK_ID' => self::$catalogIblockId, 'ID' => $pid]);
                if($arElementInfo = $rs->GetNext()){
                    $obCache->EndDataCache($arElementInfo);
                }
            }
        }

        return $arElementInfo;
    }

    public static function addFreeServices($itemName = false, $productId = 0)
    {
        \Bitrix\Main\Loader::includeModule('iblock');
        \Bitrix\Main\Loader::includeModule('sale');

        if(!$itemName) return false;

        $rs = \CIblockElement::GetList([], ['IBLOCK_ID' => 5, '=PROPERTY_IS_FREE_VALUE' => 'Y'], false, false, ['ID', 'NAME', 'PROPERTY_IS_FREE', 'PROPERTY_CUSTOM_NAME']);
        if($rs->SelectedRowsCount() > 0){
            $basket = \Bitrix\Sale\Basket::loadItemsForFUser(
                \Bitrix\Sale\Fuser::getId(), 
                \Bitrix\Main\Context::getCurrent()->getSite()
            );
            $allIds = [];
            while($ob = $rs->GetNext()){
                $item = $basket->createItem('catalog', $ob['ID']);
                $ob['NAME'] = $ob['PROPERTY_CUSTOM_NAME_VALUE']?:$ob['NAME'];
                $item->setFields([
                    'QUANTITY' => 1,
                    'CURRENCY' => \Bitrix\Currency\CurrencyManager::getBaseCurrency(),
                    'LID' => \Bitrix\Main\Context::getCurrent()->getSite(),
                    'PRICE' => 0,
                    'CUSTOM_PRICE' => 'Y',
                    'NAME' => $ob['NAME'] . ' для ' . $itemName 
                 ]);
                 $allIds[] = $ob['ID'];
            }        
            $basket->save();

            foreach($allIds as $id){
                $item = $basket->getExistsItem('catalog', $id);
                if($item){
                    $basketPropertyCollection = $item->getPropertyCollection(); 
                    $basketPropertyCollection->getPropertyValues();
                    $basketPropertyCollection->setProperty(array(
                        array(
                           'NAME' => 'Для товара',
                           'CODE' => 'FOR_PID',
                           'VALUE' => $productId,
                           'SORT' => 100,
                        ),
                    ));
                    $basketPropertyCollection->save();
                }                
            }
        }
    }

    public static function deleteFreeServices($services = [])
    {
        if(count($services) <= 0) return false;

        \Bitrix\Main\Loader::includeModule('iblock');
        \Bitrix\Main\Loader::includeModule('sale');

        $basket = \Bitrix\Sale\Basket::loadItemsForFUser(
            \Bitrix\Sale\Fuser::getId(), 
            \Bitrix\Main\Context::getCurrent()->getSite()
        );

        foreach($services as $id){
            if($item = $basket->getItemById($id)){
                $item->delete();
            }
        }
        $basket->save();
    }

    public static function getReviewsYandex()
    {
        $reviews = [];

        $obCache = new \CPHPCache();
        if ($obCache->InitCache(86400, 'reviewsYandexUpd2' . self::$cachePostfix, self::$cacheDir)) {
            $reviews = $obCache->GetVars();
        } elseif ($obCache->StartDataCache()) {
            if(\Bitrix\Main\Loader::includeModule('iblock')){
                $rsElements = \CIBlockElement::GetList(array("PROPERTY_DATE" => "DESC"), array("IBLOCK_ID"=> 54,">PROPERTY_RATING"=>"4", "!PROPERTY_AUTOR" => 'NULL'), false, Array("nTopCount"=> $_REQUEST['COUNT']?:10), array("ID","NAME","PROPERTY_AUTOR","PROPERTY_RATING","PROPERTY_DATE","PROPERTY_PRO","PROPERTY_CONTRA","PROPERTY_TEXT","PROPERTY_ANSWER", "PREVIEW_PICTURE", "PROPERTY_ISTOCNIK",'PROPERTY_DOMAIN'));
                while($obElement = $rsElements->GetNext()){
                    if(!$obElement['PROPERTY_ISTOCNIK_VALUE']){
                        $obElement['PROPERTY_ISTOCNIK_VALUE'] = 'https://market.yandex.ru/shop--sibdroid-ru/307694/reviews';
                    }
                    $reviews[] = $obElement;
                }
                $obCache->EndDataCache($reviews);
            }
        }
        return $reviews;
    }

    public static function getReviewsYandexCount()
    {
        return (int)\CIBlockElement::GetList(array("PROPERTY_DATE" => "DESC"), array("IBLOCK_ID"=> 54), false, false, array("ID"))->SelectedRowsCount();       
    }

    public static function getHydrogelElements($type = 'back')
    {
        $result = [];

        $obCache = new \CPHPCache();
        $regionType = $_SESSION['VREGIONS_REGION']['ID'];//Catalog::isMskRegion() ? 'msk' : 'nsk';
        if ($obCache->InitCache(500, 'hydrogelupd5' . $regionType . $type . self::$cachePostfix, self::$cacheDir)) {
            $result = $obCache->GetVars();
        } elseif ($obCache->StartDataCache()) {
            if(\Bitrix\Main\Loader::includeModule('iblock')){
                $arFilterForStore = ['LOGIC' => 'OR'];
                foreach($_SESSION['VREGIONS_REGION']['ID_SKLADA'] as $storeId){
                    $arFilterForStore[] = ['>CATALOG_STORE_AMOUNT_' . $storeId => '0'];
                }
                $arFilter = [
                    'IBLOCK_ID' => self::$catalogIblockId,
                    'ACTIVE' => 'Y',
                    'PROPERTY_REKOMENDUEYE_TOVARY_VALUE' => $type === 'back' ? 'На заднюю панель' : 'На переднюю панель',
                    $arFilterForStore
                ];
                $arFilterPrice = Catalog::getPriceTypeFilter();
                $select = ['ID', 'NAME', 'DETAIL_PICTURE', 'DETAIL_PAGE_URL'];
                foreach($arFilterPrice as $priceId){
                    $select[] = 'PRICE_' . $priceId;
                }

                $rsElements = \CIBlockElement::GetList(['SORT' => 'ASC'], $arFilter, false, false, $select);
                while($obElement = $rsElements->GetNext()){
                    foreach($arFilterPrice as $priceId){
                        if((int)$obElement['PRICE_' . $priceId] > 0){
                            $obElement['PRICES'][] = (int)$obElement['PRICE_' . $priceId];
                        }
                    }
                    sort($obElement['PRICES']);
                    if($obElement['DETAIL_PICTURE']){
                        $obElement['PIC']['WEBP'] = \CResizer2Resize::ResizeGD2(\CFile::GetPath($obElement['DETAIL_PICTURE']), 16);
                        $obElement['PIC']['JPG'] = \CResizer2Resize::ResizeGD2(\CFile::GetPath($obElement['DETAIL_PICTURE']), 46);
                    }
                    $result[] = $obElement;
                }
                $obCache->EndDataCache($result);
            }
        }
        return $result;
    }

    public static function isUcenkaSectionChild($sectionId)
    {
        $ucenkaSectionId = 608;
        if($sectionId == $ucenkaSectionId) return true;

        $obCache = new \CPHPCache();
        $arUcenkaPhoneSection = [];
        if ($obCache->InitCache(self::$cacheTime, 'ucenka_section_new' . self::$cachePostfix, self::$cacheDir)) {
            $arUcenkaPhoneSection = $obCache->GetVars();
        } elseif ($obCache->StartDataCache()) {
            if(\Bitrix\Main\Loader::includeModule('iblock')){
                $rs = \CIblockSection::GetList([], ['IBLOCK_ID' => self::$catalogIblockId, 'SECTION_ID' => $ucenkaSectionId, 'ACTIVE' => 'Y']);
                while($item = $rs->GetNext()){
                    $arUcenkaPhoneSection[] = $item['ID'];
                }
                $obCache->EndDataCache($arUcenkaPhoneSection);
            }
        }

        return in_array($sectionId, $arUcenkaPhoneSection);
    }

    public static function getAboutText()
    {
        $obCache = new \CPHPCache();
        $aboutText = '';
        if ($obCache->InitCache(self::$cacheTime, 'about_text_s' . $_SESSION['VREGIONS_REGION']['ID'] . self::$cachePostfix, self::$cacheDir)) {
            $aboutText = $obCache->GetVars()['result'];
        } elseif ($obCache->StartDataCache()) {
            if(\Bitrix\Main\Loader::includeModule('iblock')){
                $rs = \CIblockElement::GetList([], ['IBLOCK_ID' => self::$regionIblockId, 'ID' => $_SESSION['VREGIONS_REGION']['ID']], false, false, ['PROPERTY_ABOUT_TEXT']);
                if($item = $rs->GetNext()){
                    $aboutText = htmlspecialchars_decode($item['PROPERTY_ABOUT_TEXT_VALUE']['TEXT']); 
                }
                $obCache->EndDataCache(['result' => $aboutText]);
            }
        }

        return $aboutText;
    }

    public static function getSubSectionFromParents($defaultFilter = [], $filterParents = [], $filterChildrens = [])
    {
        if(empty($defaultFilter) || empty($filterParents)) return [];

        $obCache = new \CPHPCache();
        $arAllIds = [];
        if ($obCache->InitCache(self::$cacheTime, 'getSubSectionFromParents_s' . serialize($defaultFilter) . serialize($filterParents) . self::$cachePostfix, self::$cacheDir)) {
            $arAllIds = $obCache->GetVars();
        } elseif ($obCache->StartDataCache()) {            
            if (\Bitrix\Main\Loader::includeModule('iblock')) {
                $rsParents = \CIblockSection::GetList([], array_merge($defaultFilter, $filterParents), false, ['ID', 'LEFT_MARGIN', 'RIGHT_MARGIN', 'IBLOCK_ID']);
                
                while($obParent = $rsParents->GetNext()){
                    $arFilterParent = ['>LEFT_MARGIN' => $obParent['LEFT_MARGIN'], '<RIGHT_MARGIN' => $obParent['RIGHT_MARGIN']];
                    $arAllIds[] = $obParent['ID'];
                    $rsIDs = \CIblockSection::GetList([], array_merge($defaultFilter, $arFilterParent, $filterChildrens), false, ['ID', 'IBLOCK_ID']);
                    while($ob = $rsIDs->GetNext()){
                        $arAllIds[] = $ob['ID'];
                    }
                }
            }
            $obCache->EndDataCache($arAllIds);
        }

        return $arAllIds;
    }

    public static function getUnstickSections($sectionPropCode = false, $cacheSault = 1)
    {
        $result = [];        
        if(!$sectionPropCode) return $result;     

        if(!$cacheSault){
            $cacheSault = '1';
        }

        $obCache = new \CPHPCache();
        if ($obCache->InitCache(self::$cacheTime, $cacheSault . 'getUnstickSections_' . $sectionPropCode . self::$cachePostfix, self::$cacheDir)) {
            $result = $obCache->GetVars();
        } elseif ($obCache->StartDataCache()) {
            if (\Bitrix\Main\Loader::includeModule('iblock')) {
                $rs = \CIblockSection::GetList([], ['IBLOCK_ID' => self::$catalogIblockId, '!' . $sectionPropCode => false]);
                while($ob = $rs->GetNext()){
                    $rsSub = \CIblockSection::GetList([], ['IBLOCK_ID' => self::$catalogIblockId, '>=LEFT_MARGIN' => $ob['LEFT_MARGIN'], '<RIGHT_MARGIN' => $ob['RIGHT_MARGIN']], false, ['ID']);
                    while($obSub = $rsSub->GetNext()){
                        $result[] = $obSub['ID'];
                    }
                }
            }
            $obCache->EndDataCache($result);
        }

        return $result;
    }

    public static function getUfPropSection($iblockId = false, $sectionId = false, $sectionPropCode = false){
        if(!$iblockId || !$sectionId || !$sectionPropCode){
            return false;
        }

        $rsSect = \CIBlockSection::GetList([],['IBLOCK_ID' => $iblockId, 'ID' => $sectionId], false, ['ID', 'IBLOCK_SECTION_ID', $sectionPropCode]);
        if($arSect = $rsSect->GetNext()){
            if($arSect[$sectionPropCode] !== NULL){
                return $arSect[$sectionPropCode];
            }
            if((int)$arSect['IBLOCK_SECTION_ID'] > 0){
                return self::getUfPropSection($iblockId, $arSect['IBLOCK_SECTION_ID'], $sectionPropCode);
            }
        }
        return false;
    }

    public static function getSectionBanner($sectionId = 0)
    {
        $regionId = $_SESSION['VREGIONS_REGION']['ID'];
        $result = [];

        if((int)$sectionId <= 0 || (int)$regionId <= 0) return [];

        $obCache = new \CPHPCache();
        if ($obCache->InitCache(86400, 'getSectionBanner5_' . $sectionId . '_' . $regionId . self::$cachePostfix, self::$cacheDir)) {
            $result = $obCache->GetVars();
        } elseif ($obCache->StartDataCache()) {
            if (\Bitrix\Main\Loader::includeModule('iblock') && \Bitrix\Main\Loader::includeModule('yenisite.resizer2')) {
                $arSelect = ['ID', 'NAME'];
                $arSelectProps = ['SBANNER_IMG', 'SBANNER_POSITION_BLOCKS', 'SBANNER_POSITION_LIST', 'SBANNER_LINK'];
                foreach($arSelectProps as $selectProp){
                    $arSelect[] = 'PROPERTY_' . $selectProp;
                }
        
                $arFilter = [
                    'IBLOCK_ID' => 53,
                    'ACTIVE' => 'Y',
                    [
                        'LOGIC' => 'OR',
                        [
                            '!PROPERTY_SBANNER_IMG' => false,
                            '=PROPERTY_SBANNER_SECTION' => $sectionId, 
                            '=PROPERTY_RBS_ALL_CITY_VALUE' => 'Y',
                            '=PROPERTY_RBS_CITY' => $regionId
                        ],
                        [
                            '!PROPERTY_SBANNER_IMG' => false,
                            '=PROPERTY_SBANNER_SECTION' => $sectionId,
                            '!PROPERTY_RBS_ALL_CITY_VALUE' => 'Y',
                            '!PROPERTY_RBS_CITY' => $regionId
                        ]
                    ]
                ];
                $rs = \CIblockElement::GetList(['SORT' => 'ASC'], $arFilter, false, false, $arSelect);
                while($ob = $rs->GetNext()){
                    $pathImg = \CFile::GetPath($ob['PROPERTY_SBANNER_IMG_VALUE']);
                    $result[] = [
                        'NAME' => $ob['NAME'],
                        'LINK' => $ob['PROPERTY_SBANNER_LINK_VALUE'],
                        'POSITION_BLOCKS' => (int)$ob['PROPERTY_SBANNER_POSITION_BLOCKS_VALUE'],
                        'POSITION_LIST' => (int)$ob['PROPERTY_SBANNER_POSITION_LIST_VALUE'],
                        'PIC' => [
                            'JPG' => \CResizer2Resize::ResizeGD2($pathImg, 53),
                            'WEBP' => \CResizer2Resize::ResizeGD2($pathImg, 52),
                            'ORIG' => $pathImg
                        ]
                    ];
                }
            }
            $obCache->EndDataCache($result);
        }

        

        return $result;
    }

    public static function getSectionTreeForDepthFirst()
    {
        $obCache = new \CPHPCache();
        $result = [];
        if ($obCache->InitCache(self::$cacheTime, 'getSectionTreeForDepthFirst' . self::$cachePostfix, self::$cacheDir)) {
            $result = $obCache->GetVars();
        } elseif ($obCache->StartDataCache()) {       
            $rsSecTree = \CIblockSection::GetList(["left_margin" => "asc"], ['IBLOCK_ID' => self::$catalogIblockId], false, ['ID', 'DEPTH_LEVEL']);
            while($obSec = $rsSecTree->GetNext()){
                if($obSec['DEPTH_LEVEL'] == 1){
                    $currentDepth = $obSec['ID'];
                    continue;
                }
                $result[$obSec['ID']] = $currentDepth;
            }
            $obCache->EndDataCache($result);
        }

        return $result;
    }

    public static function initLazyLoadImgFromText($text, $cacheId, $arSetsIds = [])
    {
        if(empty($arSetsIds)){
            $arSetsIds = ['WEBP' => 40, 'JPG' => 47];
        }
        $cacheId = $cacheId . '_new_' . $_SESSION['VREGIONS_REGION']['ID'];
        if(strlen(trim($text)) > 0){
            $obCache = new \CPHPCache();
            if ($obCache->InitCache(self::$cacheTime, $cacheId . self::$cachePostfix, self::$cacheDir)) {
                $text = $obCache->GetVars();
            } elseif ($obCache->StartDataCache()) {
                if (\Bitrix\Main\Loader::includeModule('yenisite.resizer2')){						
                    preg_match_all("/\<img.+src\=(?:\"|\')(.+?)(?:\"|\')(?:.+?)\>/", $text, $matches);
                    if(count($matches[1]) > 0){
                        $replace = [];
                        foreach($matches[1] as $match){
                            $replace[] = '<div><img style="" src="'.SITE_TEMPLATE_PATH.'/img/placetransparent.png" class="rbs-find-img-detail-descr placeholder rbs-lazy-img-text" data-original="'.\CResizer2Resize::ResizeGD2($match, $arSetsIds['WEBP']).'" data-original-jpg="'.\CResizer2Resize::ResizeGD2($match, $arSetsIds['JPG']).'"></div>';
                        }
                        $text = str_replace($matches[0], $replace, $text);
                    }
                }    
                $obCache->EndDataCache($text);
            }
        }
        return $text;
    }

    public static function setAvailableDateBySectionId($sectionId = false, $date = false)
    {
        
        if(!$sectionId || !\Bitrix\Main\Loader::includeModule('iblock') || !$date) return;
        
        $rs = \CIBlockElement::GetList([], ['IBLOCK_ID' => self::$catalogIblockId, 'ACTIVE' => 'Y', 'IBLOCK_SECTION_ID' => $sectionId], false, false, ['ID', 'PROPERTY_RBS_STORE_DATE_INFO']);
       
        while($ob = $rs->GetNext()){                       
            $val = unserialize(htmlspecialchars_decode($ob['PROPERTY_RBS_STORE_DATE_INFO_VALUE']));            
            foreach($val as $storeId => $v){$val[$storeId]['DATE'] = $date;}
            \CIBlockElement::SetPropertyValuesEx($ob['ID'], self::$catalogIblockId, ['RBS_STORE_DATE_INFO' => serialize($val)]);
        }
    }

    public static function setDateStickToAllItems()
    {        
        if(!\Bitrix\Main\Loader::includeModule('iblock')) return;

        $date = date('Y-m-d');
        $rs = \CIBlockElement::GetList([], ['IBLOCK_ID' => self::$catalogIblockId, 'ACTIVE' => 'Y'], false, false, ['ID', 'PROPERTY_RBS_STORE_DATE_INFO']);       
        while($ob = $rs->GetNext()){                       
            $val = unserialize(htmlspecialchars_decode($ob['PROPERTY_RBS_STORE_DATE_INFO_VALUE']));            
            foreach($val as $storeId => $v){
                $val[$storeId]['STICK'] = $date;
            }
            \CIBlockElement::SetPropertyValuesEx($ob['ID'], self::$catalogIblockId, ['RBS_STORE_DATE_INFO' => serialize($val)]);
        }
    }

    public static function checkItemsForTime()
    {
        if (\Bitrix\Main\Loader::includeModule('iblock')) {
            $rs = \CIblockSection::GetList([], ['IBLOCK_ID' => self::$catalogIblockId, '!UF_INTERVAL_YA' => false], false, ['ID', 'UF_INTERVAL_YA']);
            while ($ob = $rs->GetNext()) {
                AddMessage2Log($ob);
                $ob['UF_INTERVAL_YA'] = trim($ob['UF_INTERVAL_YA']);
                if (!empty($ob['UF_INTERVAL_YA'])) {
                    $expl = explode('-', $ob['UF_INTERVAL_YA']);
                    if (count($expl) == 2) {
                        $startTime = strtotime(date('Y-m-d ') . trim($expl[0]));
                        $endTime = strtotime(date('Y-m-d ') . trim($expl[1]));
                        $currentTime = strtotime(date('Y-m-d H:i'));
    
                        if ($currentTime >= $startTime && $currentTime <= $endTime) {
                            $els = \CIblockElement::GetList([], ['IBLOCK_ID' => self::$catalogIblockId, 'SECTION_ID' => $ob['ID'], 'INCLUDE_SUBSECTIONS' => 'Y'], false, false, ['ID']);
                            while ($obEl = $els->GetNext()) {
                                \CIBlockElement::SetPropertyValuesEx($obEl['ID'], self::$catalogIblockId, ['RBS_INTERVAL_YA' => self::getPropValue('RBS_INTERVAL_YA', 'RBS_INTERVAL_YA_Y')]); //Y
                            }
                        } else {
                            $els = \CIblockElement::GetList([], ['IBLOCK_ID' => self::$catalogIblockId, 'SECTION_ID' => $ob['ID'], 'INCLUDE_SUBSECTIONS' => 'Y', 'PROPERTY_RBS_INTERVAL_YA_VALUE' => 'Y'], false, false, ['ID']);
                            while ($obEl = $els->GetNext()) {
                                \CIBlockElement::SetPropertyValuesEx($obEl['ID'], self::$catalogIblockId, ['RBS_INTERVAL_YA' => false]);
                            }
                        }
                    }
                }
            }
        }
    }

    public static function checkTime($optionString, $interval = 86400)
    {
        $currTime = \COption::GetOptionInt("sib.core", $optionString, time());
        if(time() - $currTime >= $interval){
            \COption::SetOptionInt("sib.core", $optionString, time());
            return true;
        }
        return false;
    }

    public static function setTimeOptions()
    {
        \COption::SetOptionInt("sib.core", 'checkHits', time());
        \COption::SetOptionInt("sib.core", 'updateAvailableStatusAll', time());
    }

    public static function getPropValue($propCode, $propXmlValue)
    {
        $obCache = new \CPHPCache();
        $propCodeValue = [];
        if ($obCache->InitCache(60 * 60 * 24 * 365, 'propValue_' . $propCode . '_' . $propXmlValue . self::$cachePostfix, self::$cacheDir)) {
            $propCodeValue = $obCache->GetVars();
        } elseif ($obCache->StartDataCache()) {
            if(\Bitrix\Main\Loader::includeModule('iblock')){
                $rs = \CIBlockPropertyEnum::GetList([], ['IBLOCK_ID' => self::$catalogIblockId, 'CODE' => $propCode]);
                while($v = $rs->GetNext()){
                    $propCodeValue[$v['XML_ID']] = $v['ID'];
                }
                $obCache->EndDataCache($propCodeValue);
            }
        }
        return isset($propCodeValue[$propXmlValue]) ? $propCodeValue[$propXmlValue] : false;
    }

    public static function diffHours(DateTime $datetime1, DateTime $datetime2 = null)
    {
        if(!isset($datetime2)){$datetime2 = new DateTime('now');}    
        $interval = $datetime1->diff($datetime2, false);    
        return floor(($datetime2->getTimestamp() - $datetime1->getTimestamp()) / (60 * 60));
    }

    public static function checkDisabledSectionsParents()
    {
        if (\Bitrix\Main\Loader::includeModule('iblock')) {
            $rs = \CIblockSection::GetList([], ['IBLOCK_ID' => 6, 'ACTIVE' => 'Y', '>=DEPTH_LEVEL' => 2, '<=DEPTH_LEVEL' => 3], false, ['ID', 'UF_*', 'IBLOCK_SECTION_ID', 'DEPTH_LEVEL']);
            $arAllSections = [];
            while($ob = $rs->GetNext()){
                $isHidden = $ob['UF_IS_SHOW'] && $ob['UF_DIS_CATALOG'];

                if($ob['DEPTH_LEVEL'] == 2){
                    $arAllSections[$ob['ID']]['HIDDEN'] = $isHidden;
                }
                if($ob['DEPTH_LEVEL'] == 3){
                    $arAllSections[$ob['IBLOCK_SECTION_ID']]['ITEMS'][$ob['ID']] = $isHidden;
                }
            }

            $arHiddenSections = [];
            foreach($arAllSections as $sectionId => $section){
                if(!isset($section['ITEMS'])) continue;

                sort($section['ITEMS']);
                $isNeedHidden = $section['ITEMS'][0];

                $val = -1;
                if($isNeedHidden && !$section['HIDDEN']){$val = 1;}
                if(!$isNeedHidden && $section['HIDDEN']){$val = 0;}

                if ($val > -1) {
                    $GLOBALS["USER_FIELD_MANAGER"]->Update('IBLOCK_6_SECTION', $sectionId, ['UF_IS_SHOW' => $val,'UF_DIS_CATALOG' => $val]);
                }
            }
        }

        return "checkDisabledSectionsParents();";
    }

    public static function checkDisabledSections()
    {
        if (\Bitrix\Main\Loader::includeModule('iblock')) {
            $rs  = \CIblockElement::GetList(['CATALOG_AVAILABLE' => 'ASC'], ['IBLOCK_ID' => 6, 'ACTIVE' => 'Y'], false, false, ['PROPERTY_RBS_STORE_DATE_INFO', 'ID','IBLOCK_SECTION_ID']);
            $arSectionsInfo = [];
            while ($ob = $rs->GetNext()) {
                if ((int)$ob['IBLOCK_SECTION_ID'] <= 0) {
                    continue;
                }
                if ($ob['CATALOG_AVAILABLE'] == 'Y') {
                    $arSectionsInfo[$ob['IBLOCK_SECTION_ID']] = false;
                    continue;
                }
                $arAvailableDateInfo = unserialize(htmlspecialchars_decode($ob['PROPERTY_RBS_STORE_DATE_INFO_VALUE']));
                $maxDays = false;
                foreach ($arAvailableDateInfo as $storeId => $info) {
                    $dateTimeLast = new \DateTime($info['DATE']);
                    $dateTimeCurrent = new \DateTime(date('Y-m-d'));
                    if (!$maxDays) {
                        $maxDays = $dateTimeCurrent->diff($dateTimeLast)->days > 30;
                    }
                }
                $arSectionsInfo[$ob['IBLOCK_SECTION_ID']] = $maxDays;
            }
            
            $rs = \CIblockSection::GetList([], ['IBLOCK_ID' => 6, 'ACTIVE' => 'Y', 'ID' => array_keys($arSectionsInfo)], false, ['ID', 'UF_*']);
            while ($ob = $rs->GetNext()) {            
                $isHidden = $ob['UF_IS_SHOW'] && $ob['UF_DIS_CATALOG'];
                $val = -1;
    
                if (!$arSectionsInfo[$ob['ID']] && $isHidden) {
                    $val = 0;
                } elseif ($arSectionsInfo[$ob['ID']] && !$isHidden) {
                    $val = 1;
                }
                if ($val > -1) {
                    $GLOBALS["USER_FIELD_MANAGER"]->Update('IBLOCK_6_SECTION', $ob['ID'], ['UF_IS_SHOW' => $val,'UF_DIS_CATALOG' => $val]);
                }
            }
        }
        return "checkDisabledSections();";
    }

    public static function checkSortSections($step = 0, $stepSize = 1)
    {
        if(!\Bitrix\Main\Loader::includeModule('iblock')) return false;

        $arShowCount = [];
        $arFilterStore = [
            'LOGIC' => 'OR',
            ['>CATALOG_STORE_AMOUNT_1' => '0'],
            ['>CATALOG_STORE_AMOUNT_2' => '0'],
            ['>CATALOG_STORE_AMOUNT_15' => '0'],
            ['>CATALOG_STORE_AMOUNT_16' => '0']
        ];
        $rsItems = \CIBlockElement::GetList(['IBLOCK_SECTION_ID' => 'ASC'], ['IBLOCK_ID' => 6, 'ACTIVE' => 'Y', $arFilterStore], false, false, ['ID', 'SHOW_COUNTER', 'SHOW_COUNTER_START', 'IBLOCK_SECTION_ID']);
        while($item = $rsItems->GetNext()){
            if((int)$item["IBLOCK_SECTION_ID"] > 0){
                $diffHours = self::diffHours(new DateTime($item['SHOW_COUNTER_START']));
                $arShowCount[$item["IBLOCK_SECTION_ID"]][] = round(($item["SHOW_COUNTER"] / ($diffHours + 1)) * 100);
            }
        }
                
        $arSectionSorting = [];
        $arParents = [];
        $rsSections = \CIBlockSection::GetList(['ID' => 'ASC'], ['IBLOCK_ID' => 6, 'ACTIVE' => 'Y', '>=DEPTH_LEVEL' => 3], false, ['ID', 'IBLOCK_SECTION_ID', 'SORT', 'DEPTH_LEVEL']);
        while($section = $rsSections->GetNext()){
            if($section['DEPTH_LEVEL'] == 4){
                $arSectionSorting[$section['IBLOCK_SECTION_ID']] += $section['SORT'];
                $arParents[$section['ID']] = $section['IBLOCK_SECTION_ID'];
            } else {
                $arSectionSorting[$section['ID']] += $section['SORT'];
            }
        }

        foreach($arParents as $id => $parentId){
            if(isset($arShowCount[$id])){
                $sum = 0;
                foreach($arShowCount[$id] as $v){
                    $sum += $v;
                }
                $sum = round($sum / count($arShowCount[$id]));
                if(!isset($arShowCount[$parentId]) || $arShowCount[$parentId][0] < $sum){
                    $arShowCount[$parentId] = [$sum];
                }
                unset($arShowCount[$id]);
            }
        }

        $arAvgCount = [];
        foreach($arShowCount as $key => $elements){
            $sumElements = 0;
            $cntElements = 0;
            foreach($elements as $val){
                if(is_array($val)){
                    foreach($val as $v){
                        $sumElements += $v;
                        $cntElements++;
                    }
                } else {
                    $sumElements += $val;
                    $cntElements++;
                }
            }
            $arAvgCount[$key] = round($sumElements / $cntElements);
        }
        $arShowCount = $arAvgCount;
        

        if($stepSize > 0){
            $tmpSectionSorting = array_chunk($arSectionSorting, $stepSize, true);
            if(isset($tmpSectionSorting[$step])){
                $arSectionSorting = $tmpSectionSorting[$step];
            }            
        }
                
        $upSection = new \CIBlockSection;
        foreach ($arSectionSorting as $id => $val){
            if(isset($arShowCount[$id]) && $val != $arShowCount[$id]){
                $upSection->Update($id, array('SORT' => $arShowCount[$id]));
            }
        }

        $step = $step < (count($tmpSectionSorting) - 1) ? $step + 1 : 0;

        return $step;
    }

    public static function citySort()
    {
        /* $str = "Москва
            Санкт-Петербург
            Новосибирск
            Екатеринбург
            Нижний Новгород
            Казань
            Челябинск
            Омск
            Самара
            Ростов-на-Дону
            Уфа
            Красноярск
            Пермь
            Воронеж
            Волгоград
            Краснодар
            Саратов
            Тюмень
            Тольятти
            Ижевск
            Барнаул
            Ульяновск
            Иркутск
            Хабаровск";

            $tmp = explode("\n", $str);
            $tmp = array_chunk($tmp, 8);

            $sortedCities = [];
            $startSort = 0;
            foreach($tmp as $key => $chunk){
                foreach($chunk as $city){
                    $sortedCities[$city] = $startSort;
                    $startSort = $startSort + 1;
                }
            }

            $rs = CIblockElement::GetList([], ['IBLOCK_ID' => 46]);
            $el = new CIblockElement;
            while($ob = $rs->GetNext()){
                $val = false;
                if(isset($sortedCities[$ob['NAME']])){
                    $el->Update($ob['ID'], ['SORT' => $sortedCities[$ob['NAME']]]);
                    $val = 1925;
                } else if($ob['SORT'] < 24) {
                    $el->Update($ob['ID'], ['SORT' => 500]);
                }
                CIBlockElement::SetPropertyValuesEx($ob['ID'], $ob['IBLOCK_ID'], array('CHOSEN_ONE' => $val));
            }
            print_r($sortedCities); */
    }

    public static function getDiscountArray($productId, $diff, $priceType)
    {
        return [
            'NAME' => '[AUTO] ' . $productId . ' ('.$priceType.')',
            'LID' => 's1',
            'CURRENCY' => 'RUB',
            'DISCOUNT_VALUE' => $diff,
            'DISCOUNT_TYPE' => 'V',
            'ACTIVE' => 'Y',
            'USER_GROUPS' => array(2),
            'SORT' => 100,
            'PRIORITY' => 1560,
            'LAST_DISCOUNT' => 'N',
            'LAST_LEVEL_DISCOUNT' => 'N',
            'ACTIONS' => [
                'CLASS_ID' => 'CondGroup',
                'DATA' => ['All' => 'AND'],
                'CHILDREN' => [[
                    'CLASS_ID' => 'ActSaleBsktGrp',
                    'DATA' => [
                        'Type' => 'Discount',
                        'Value' => $diff,
                        'Unit' => 'CurEach', //'Perc' || 'CurAll'
                        'Max' => 0,
                        'All' => 'AND',
                        'True' => 'True',
                    ],
                    'CHILDREN' => [
                        [
                            'CLASS_ID' => 'CondIBElement',
                            'DATA' => [
                                'logic' => 'Equal',
                                'value' => [$productId]
                            ]
                        ],
                        [
                            'CLASS_ID' => 'CondCatalogPriceType',
                            'DATA' => [
                                'logic' => 'Equal',
                                'value' => [$priceType]
                            ]
                        ]
                    ]
                ]]
            ],
            'CONDITIONS' => [
                'CLASS_ID' => 'CondGroup',
                'DATA' => [
                    'All' => 'AND',
                    'True' => 'True',
                ],
                'CHILDREN' => []
            ]
        ];
    }

    public static function getPlural($n, $form1, $form2, $form3)
    {
        $n = abs($n) % 100;
        $n1 = $n % 10;
        
        if ($n > 10 && $n < 20) {
            return $form3;
        }
        
        if ($n1 > 1 && $n1 < 5) {
            return $form2;
        }
        
        if ($n1 == 1) {
            return $form1;
        }
        
        return $form3;
    }
}