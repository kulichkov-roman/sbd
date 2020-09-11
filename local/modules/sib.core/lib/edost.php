<?
namespace Sib\Core;

use \Bitrix\Main\Loader;
\Bitrix\Main\Loader::includeModule('catalog');
\Bitrix\Main\Loader::includeModule('iblock');
\Bitrix\Main\Loader::includeModule('sale');

class Edost
{
    private static function setDefaultValues()
    {
        $prop = [
            'EDOST_CUSTOM_PRICE_37' => [
                ['VALUE' => '0-2999;0-6000=290|6001-20000=390|20001=390'],
                ['VALUE' => '3000;0-6000=990|6001-20000=990|20001=990']
            ],
            'EDOST_CUSTOM_PRICE_38' => [
                ['VALUE' => '0-2999;0-6000=390|6001-20000=490|20001=490'],
                ['VALUE' => '3000;0-6000=1990|6001-20000=1990|20001=1990']
            ]
        ];

        $rs = \CIblockElement::GetList(['ID' => 'ASC'], ['IBLOCK_ID' => 46], false, false, ['ID']);
        while($ob = $rs->GetNext()){
            \CIblockElement::SetPropertyValuesEx($ob['ID'], 46, $prop);
        }
    }

    public static function getCustomDate(){
        if(Catalog::isMskRegion($_SESSION['VREGIONS_REGION']['ID'])){
            return '<span class="rbs-today-delivery js-card-question">сегодня<span class="card-tooltipe">При заказе до 19:00</span></span>';
        }
        return '<span class="rbs-today-delivery js-card-question">сегодня<span class="card-tooltipe">При заказе до 17:00</span></span>';
    }

    public static function getCustomFree(){
        /* global $USER;
        if($USER->IsAdmin()){
            return '<a href="#popup-contacts" class="rbs-card-contact-link"><span>из магазина</span></a>';
        } */
        if(Catalog::isMskRegion($_SESSION['VREGIONS_REGION']['ID'])){
            return '<a href="#popup-contacts" target="_blank" class="rbs-card-contact-link"><span>из магазина</span></a>';
        }
        return '<a href="#popup-contacts" target="_blank" class="rbs-card-contact-link"><span>из магазина</span></a>';
    }

    public static function getPopupMapParams()
    {
        $params = [];
        if(Catalog::isMskRegion($_SESSION['VREGIONS_REGION']['ID'])){
            $params['SRC'] = '//api-maps.yandex.ru/services/constructor/1.0/js/?um=constructor%3A9a151d91dc47c3822a4299239c50b2e56efb67935224295c2aed066d87926987&amp;width=100%&amp;height=100%&amp;lang=ru_RU&amp;scroll=true';
            $params['HEAD'] = 'г. Москва, Багратионовский проезд 7 корпус 3 ТК Горбушкин Двор пав. h2-003';
        } else {
            $params['SRC'] = '//api-maps.yandex.ru/services/constructor/1.0/js/?um=constructor%3A20j3NZT9-ERpbbmW1pJtdQR88yMjg9TV&amp;width=100%25&amp;height=100%&amp;lang=ru_RU&amp;scroll=true';
            $params['HEAD'] = 'г. Новосибирск ул. Новогодняя 17';
        }

        return $params;
    }
	
	public static function modifyDetailTemplate(&$item, $params)
	{
        //$arPrices = Helper::getDiscountPrice($params['PARAM']['product_id'])[Helper::getDefRegionId()];
        $arPrices = Helper::getDiscountPriceArray($params['PARAM']['product_id']);
        $price = $arPrices['PRICE_DISCOUNT'] > 0 ? $arPrices['PRICE_DISCOUNT'] : $arPrices['BASE_PRICE'];
 
        //define("LOG_FILENAME", __DIR__."/log.txt"); 
        //AddMessage2Log($params);
		switch((int)$item['tariff_id']){
            case 37:
            case 38:
                
                if($item['tariff_id'] == 37){
                    $deliveryType = 'self';
                } else {
                    $deliveryType = 'courier';
                }

                $arDelivery = \Bitrix\Sale\Delivery\Services\Table::getList([
                    'filter' => [
                        '=CLASS_NAME' => '\Sale\Handlers\Delivery\SibdroidHandler',
                        '=XML_ID' => 'kin_' . $_SESSION['VREGIONS_REGION']['CODE'] . '_' . $deliveryType,
                        '=ACTIVE' => 'Y'
                    ],
                    'cache' => ['ttl' => 86400 * 365]
                ])->fetch();
                if($arDelivery['ID'] > 0){
                    $item['day'] = $arDelivery['CONFIG']['DEFAULTS']['DEFAULT_PERIOD'];
                    $priceBase = (float)$arDelivery['CONFIG']['DEFAULTS']['DEFAULT_PRICE'];
                    $priceAddPercent = (float)$arDelivery['CONFIG']['DEFAULTS']['DEFAULT_PRICE_ADD'];
                    if($priceAddPercent > 0){
                        $priceBase = $priceBase + ($price * ($priceAddPercent / 100));
                    }

                    $item['price'] = $item['pricetotal'] = ceil($priceBase / 10) * 10;;
                    $item['pricetotal_formatted'] = $item['price_formatted'] = $item['price'] . ' руб.';

                    return;
                }
                
                $interval = explode('-', explode(' ', $item['day'])[0]);
                if(count($interval) === 2){
                    if((int)$interval[0] > 0 && (int)$interval[1] > 0){
                        $item['day'] = self::getDayIntervalText((int)$interval[0], (int)$interval[1]);
                    }                    
                }
            break;

            case 46:
            case 47:
                    
                    if($item['tariff_id'] == 46){
                        $deliveryType = 'self';
                    } else {
                        $deliveryType = 'courier';
                    }
    
                   
                    $interval = explode('-', explode(' ', $item['day'])[0]);
                    if(count($interval) === 2){
                        if((int)$interval[0] > 0 && (int)$interval[1] > 0){
                            $item['day'] = self::getDayIntervalText((int)$interval[0], (int)$interval[1]);
                        }                    
                    }
            break;

            case 36:
            case 43:
                        
                        if($item['tariff_id'] == 36){
                            $deliveryType = 'self';
                        } else {
                            $deliveryType = 'courier';
                        }
        
                       
                        $interval = explode('-', explode(' ', $item['day'])[0]);
                        if(count($interval) === 2){
                            if((int)$interval[0] > 0 && (int)$interval[1] > 0){
                                $item['day'] = self::getDayIntervalText((int)$interval[0], (int)$interval[1]);
                            }                    
                        }
            break;

            default:
            
                $arDelivery = \Bitrix\Sale\Delivery\Services\Table::getList([
                    'filter' => [
                        '=CLASS_NAME' => '\Sale\Handlers\Delivery\SibdroidHandler',
                        '=ID' => (int)$item['id'],
                        '=ACTIVE' => 'Y'
                    ],
                    'cache' => ['ttl' => 3600]
                ])->fetch();
                
                if($arDelivery['ID'] > 0){

                    $item['asd'] = $arDelivery;

                    $item['custom_date'] = $arDelivery['CONFIG']['DEFAULTS']['DEFAULT_PERIOD'];
                    $priceBase = (float)$arDelivery['CONFIG']['DEFAULTS']['DEFAULT_PRICE'];
                    $priceAddPercent = (float)$arDelivery['CONFIG']['DEFAULTS']['DEFAULT_PRICE_ADD'];
                    if($priceAddPercent > 0){
                        $priceBase = $priceBase + ($price * ($priceAddPercent / 100));
                    }

                    if($arDelivery['CONFIG']['MAIN']['TYPE'] === 'courier'){
                        $item['company'] = 'Доставка курьером';
                    } else {
                        $item['company'] = 'Самовывоз';
                    }

                    $item['price'] = $item['pricetotal'] = ceil($priceBase / 10) * 10;;
                    $item['pricetotal_formatted'] = $item['price_formatted'] = $item['price'] . ' руб.';

                    return;
                }
                
        }

        $product = \Bitrix\Catalog\ProductTable::getList(['filter'=> ['ID' => $params['PARAM']['product_id']], 'cache' => ['ttl' => 86400]])->fetch();
        $customPrice = self::getCustomPrice($item['tariff_id'], (int)$product['WEIGHT'], (int)$price);
        if($customPrice > 0){
            $item['price'] = $item['pricetotal'] = $customPrice;
            $item['pricetotal_formatted'] = $item['price_formatted'] = $customPrice . ' руб.';
            if(isset($item['free'])){
                unset($item['free']);
            }
        } else {
            if($_SESSION['VREGIONS_REGION']['ID'] == '14647'){
                if($customPrice == 0){
                    $item['price'] = $item['pricetotal'] = $customPrice;
                    $item['pricetotal_formatted'] = $item['price_formatted'] = '<span class="delivery-options__free">Бесплатно</span>';
                }
            }
        }
    }

    public static function getCustomPrice($tarrifId = 0, $weight = 0, $price = 0, $arTarrifs = false, $regiodId = false)
    {
        $priceResult = 0;
        global $USER;

        
        if(!$arTarrifs){
            $regiodId = $_SESSION['VREGIONS_REGION']['ID'];
            $arTarrifs = $_SESSION['VREGIONS_REGION']['EDOST_CUSTOM_PRICE_' . $tarrifId];            
        }
        
        if (is_array($arTarrifs) && count($arTarrifs) > 0) {        
            //if ($USER->IsAdmin()) {
                // define("LOG_FILENAME", __DIR__."/getCustomPrice.txt"); 
                $obCache = new \CPHPCache();
                if ($obCache->InitCache(86400, "getCustomPrice1|{$tarrifId}|{$weight}|{$price}|{$regiodId}", "/sibcore/edost")) {
                    $result = $obCache->GetVars();
                    $priceResult = $result['price'];
                } else {                    
                    foreach ($arTarrifs as $tarrif) {
                        $tarrifExplode = explode(';', $tarrif);
                        if (count($tarrifExplode) === 2) {
                            $obWeight = explode('-', $tarrifExplode[0]);
                            $isWeightChecked = false;                        
                            //проходим проверку на вес
                            if (count($obWeight) === 2) {
                                if ($weight >= (int)$obWeight[0] && $weight <= (int)$obWeight[1]) {
                                    $isWeightChecked = true;
                                }
                            } else {
                                if ($weight >= (int)$obWeight[0]) {
                                    $isWeightChecked = true;
                                }
                            }                        
                            //если прошли, то идем проверять цены
                            if ($isWeightChecked) {
                                $prices = explode('|', $tarrifExplode[1]);
                                if (count($prices) > 0) {
                                    foreach ($prices as $arPrice) {
                                        $obPrice = explode('=', $arPrice);
                                        if (count($obPrice) === 2) {
                                            $range = explode('-', $obPrice[0]);
                                            $customPrice = (int)$obPrice[1];
                                            if (count($range) === 2) {
                                                if ($price >= (int)$range[0] && $price <= (int)$range[1]) {
                                                    $priceResult = $customPrice;
                                                    break;
                                                }
                                            } else {
                                                if ($price >= (int)$range[0]) {
                                                    $priceResult = $customPrice;
                                                    break;
                                                }
                                            }
                                        }
                                    }
                                    if($priceResult > 0){
                                        break;
                                    }
                                }
                            }
                        }                    
                    }
                        
                    if($obCache->StartDataCache()){
                        $obCache->EndDataCache(['price' => $priceResult]);
                    }            
                }                 
            //}
        
        }
      
       
        return $priceResult;
    }
    
    public static function getDayIntervalText($min = 1, $max = 2)
    {
        if(isset($_SESSION['VREGIONS_REGION']['EDOST_WEEKEND']) && !empty($_SESSION['VREGIONS_REGION']['EDOST_WEEKEND'])){
            //EDOST_CURR_DAY
            $current = 0;
            if(isset($_SESSION['VREGIONS_REGION']['EDOST_CURR_DAY'])){
                $currentSess = (int)$_SESSION['VREGIONS_REGION']['EDOST_CURR_DAY'];
                if($currentSess > 0 && $currentSess < 8){
                    global $USER;
                    if($USER->IsAdmin()){
                        $current = $currentSess;
                    }
                }
            }

            $currentDate = new \DateTime();
            $currentDay = $current > 0 ? $current : $currentDate->format('N');
            $nextDay = $currentDay + 1;
            //$intervalDayMin = $min + $currentDay; //через сколько дней доставляем минимум
            //$intervalDayMax = $max + $currentDay; //через сколько дней доставляем максимум

            //$toWeekDayMin = $intervalDayMin <= 7 ? $intervalDayMin : $intervalDayMin - 7;
            //$toWeekDayMax = $intervalDayMax <= 7 ? $intervalDayMax : $intervalDayMax - 7;

            //$excludeDays = [];

            $daysDelayed = 0;
            switch($_SESSION['VREGIONS_REGION']['EDOST_WEEKEND']){
                case 'SUN':
                    //$excludeDays = [6];
                    if($nextDay === 6){
                        $daysDelayed = 1;
                    }
                break;
                case 'SAT':
                    //$excludeDays = [7];
                    if($nextDay === 7){
                        $daysDelayed = 1;
                    }
                break;
                case 'N':
                    //$excludeDays = [6, 7];
                    if($nextDay === 6){
                        $daysDelayed = 2;
                    }
                    if($nextDay === 7){
                        $daysDelayed = 1;
                    }
                break;
                case 'Y':
                    //TO DO
                break;
            }

            $min += $daysDelayed;
            $max += $daysDelayed;

            /* $daysDelayed = count($excludeDays);
            if($daysDelayed > 0){

                if(in_array($toWeekDayMin, $excludeDays) || in_array($toWeekDayMax, $excludeDays)){
                    $min += $daysDelayed;
                    $max += $daysDelayed;                    
                }
            } */
        }

        $dayText = Helper::getPlural((int)$max, 'день', 'дня', 'дней');
        return "{$min}-{$max} {$dayText}";
    }
}