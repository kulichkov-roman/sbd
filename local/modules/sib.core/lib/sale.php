<?
namespace Sib\Core;

use \Bitrix\Main\Loader;
\Bitrix\Main\Loader::includeModule('sale');

class Sale
{
    private static $propRegionId = 20;
    private static $regionIblockId = 46;
    private static $defaultRegion = 14647;

    public static function onOrderSaveHandler($orderId, $fields, $props, $isNew)
    {
        return;
        if($isNew)
        {
            $order = \Bitrix\Sale\Order::load($orderId);
            $propertyCollection = $order->getPropertyCollection();
            $somePropValue = $propertyCollection->getItemByOrderPropertyId(self::$propRegionId);
            //AddMessage2Log($_SESSION["VREGIONS_REGION"]);
            if($_SESSION["VREGIONS_REGION"]["ID"])
            {
                $somePropValue->setValue($_SESSION["VREGIONS_REGION"]["ID"]);
                $order->save();
            }
            
        }
    }

    public static function OnSaleComponentOrderProperties(&$arFields)
    {
        $arFields['ORDER_PROP'][self::$propRegionId] = $_SESSION["VREGIONS_REGION"]["ID"];
    }

    /**
     * OnOrderNewSendEmail
     * OnOrderDeliverSendEmail
     * OnOrderPaySendEmail
     * OnOrderCancelSendEmail
     * OnOrderStatusSendEmail
     * OnOrderRemindSendEmail
     * OnOrderRecurringSendEmail
     * OnOrderRecurringCancelSendEmail
     */

    public static function onSaleEmailSend($orderId, &$eventName, &$arFields)
    {
        $arFields = array_merge($arFields, self::getRegionProps($orderId));
    }

    public static function onBeforeEventAddHandler(&$event, &$lid, &$arFields, &$message_id)
    {
        if ($event == 'SALE_ORDER_TRACKING_NUMBER' && (int)$arFields['ORDER_REAL_ID'] > 0)
        {
            $arFields = array_merge($arFields, self::getRegionProps($arFields['ORDER_REAL_ID']));
        }  
    }

    public static function getRegionProps($orderId)
    {
        $arFields = [];

        $order = \Bitrix\Sale\Order::load($orderId);
        $propertyCollection = $order->getPropertyCollection();
        $somePropValue = $propertyCollection->getItemByOrderPropertyId(self::$propRegionId);
        $regionId = $somePropValue->getValue();

        if($regionId && \Bitrix\Main\Loader::includeModule('iblock'))
        {
            $dbProps = \CIBlockProperty::GetList([], ["ACTIVE"=>"Y", "IBLOCK_ID"=> self::$regionIblockId, 'MULTIPLE' => 'N']);
            $arProps = [];
            while ($obProp = $dbProps->GetNext())
            {
                $arProps[] = 'PROPERTY_' . strtoupper($obProp['CODE']);
            }

            $arDefFields = self::getDefFields();

            $arSelect = array_merge(['ID', 'IBLOCK_ID', 'NAME', 'CODE'], $arProps);
            $dbRegion = \CIblockElement::GetList([],['IBLOCK_ID' => self::$regionIblockId, 'ID' => $regionId], false, false, $arSelect);
            if($obRegion = $dbRegion->GetNext())
            {
                $arFields['REGION_ID'] = $obRegion['ID'];
                $arFields['REGION_NAME'] = $obRegion['NAME'];
                $arFields['REGION_CODE'] = $obRegion['CODE'];

                foreach($arProps as $propCode)
                {
                    if(is_array($obRegion[$propCode . '_VALUE']))
                    {
                        if($obRegion[$propCode . '_VALUE']['TYPE'] == 'HTML')
                        {
                            $obRegion[$propCode . '_VALUE'] = htmlspecialchars_decode($obRegion[$propCode . '_VALUE']['TEXT']);
                        }
                        else
                        {
                            $obRegion[$propCode . '_VALUE'] = $obRegion[$propCode . '_VALUE']['TEXT'];
                        }
                    }

                    $arFields['REGION_' . $propCode] = $obRegion[$propCode . '_VALUE']?:$arDefFields['REGION_' . $propCode];
                }
            }
        }

        return $arFields;
    }

    public static function getDefFields()
    {
        $defaultRegion = self::$defaultRegion;
        $arFields = [];
        if($defaultRegion && \Bitrix\Main\Loader::includeModule('iblock'))
        {
            $dbProps = \CIBlockProperty::GetList([], ["ACTIVE"=>"Y", "IBLOCK_ID"=> self::$regionIblockId, 'MULTIPLE' => 'N']);
            $arProps = [];
            while ($obProp = $dbProps->GetNext())
            {
                $arProps[] = 'PROPERTY_' . strtoupper($obProp['CODE']);
            }

            $arSelect = array_merge(['ID', 'IBLOCK_ID', 'NAME', 'CODE'], $arProps);
            $dbRegion = \CIblockElement::GetList([],['IBLOCK_ID' => self::$regionIblockId, 'ID' => $defaultRegion], false, false, $arSelect);
            if($obRegion = $dbRegion->GetNext())
            {
                $arFields['REGION_ID'] = $obRegion['ID'];
                $arFields['REGION_NAME'] = $obRegion['NAME'];
                $arFields['REGION_CODE'] = $obRegion['CODE'];

                foreach($arProps as $propCode)
                {
                    if(is_array($obRegion[$propCode . '_VALUE']))
                    {
                        if($obRegion[$propCode . '_VALUE']['TYPE'] == 'HTML')
                        {
                            $obRegion[$propCode . '_VALUE'] = htmlspecialchars_decode($obRegion[$propCode . '_VALUE']['TEXT']);
                        }
                        else
                        {
                            $obRegion[$propCode . '_VALUE'] = $obRegion[$propCode . '_VALUE']['TEXT'];
                        }
                    }

                    $arFields['REGION_' . $propCode] = $obRegion[$propCode . '_VALUE'];
                }
            }
        }
        return $arFields;
    }

    public static function getDeliveryTypesArray()
    {
        $result = [];
        $arDiliveyCheck = explode(',', \COption::GetOptionString("sib.core", "sib_core_edost_self", ""));
        foreach($arDiliveyCheck as $id){
            $result[$id] = 'self';
        }
        $arDiliveyCheck = explode(',', \COption::GetOptionString("sib.core", "sib_core_edost_courier", ""));
        foreach($arDiliveyCheck as $id){
            $result[$id] = 'courier';
        }

        $deliveryCustomArray = \Bitrix\Sale\Delivery\Services\Manager::getActiveList(); 
        foreach($deliveryCustomArray as $dId => $delivery){
            if($delivery['CLASS_NAME'] === '\Sale\Handlers\Delivery\SibdroidHandler'){
                $result[$dId] = $delivery['CONFIG']['MAIN']['TYPE'];
            }
        }

        return $result;
    }

    public static function checkDelivery($deliveryId, $deliveryType)
    {
        $arDiliveyCheck = [];
        switch ($deliveryType) {
            case 'self': $arDiliveyCheck = explode(',', \COption::GetOptionString("sib.core", "sib_core_edost_self", ""));
            break;
            case 'courier': $arDiliveyCheck = explode(',', \COption::GetOptionString("sib.core", "sib_core_edost_courier", ""));
            break;
        }

        return in_array($deliveryId, $arDiliveyCheck);
    }

    public static function getDeliveryType($deliveryId)
    {
        $arDiliveySelf = explode(',', \COption::GetOptionString("sib.core", "sib_core_edost_self", ""));
        $arDiliveyCourier = explode(',', \COption::GetOptionString("sib.core", "sib_core_edost_courier", ""));

        if(in_array($deliveryId, $arDiliveySelf)){
            return 'self';
        }

        if(in_array($deliveryId, $arDiliveyCourier)){
            return 'courier';
        }
    }
}