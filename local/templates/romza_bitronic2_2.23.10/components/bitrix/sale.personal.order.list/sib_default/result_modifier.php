<?
foreach ($arResult['ORDERS'] as $key => &$order)
{
    foreach ($order['SHIPMENT'] as &$shipment)
    {
        if ($parentDeliveryID = $arResult['INFO']['DELIVERY'][$shipment['DELIVERY_ID']]['PARENT_ID'])
        {
            $parentDeliveryName = $arResult['INFO']['DELIVERY'][$parentDeliveryID]['NAME'].':';
            $arResult['INFO']['DELIVERY'][$shipment['DELIVERY_ID']]['NAME'] = str_replace($parentDeliveryName, '', $arResult['INFO']['DELIVERY'][$shipment['DELIVERY_ID']]['NAME']);
        }
        if ( isset($shipment['TRACKING_NUMBER']) )
        {
            if ( substr($shipment['TRACKING_NUMBER'], 0, 2) === 'RU' )
                $shipment['LINK'] = 'https://www.dpd.ru/';
            elseif ( substr($shipment['TRACKING_NUMBER'], 0, 1) === 'E' )
                $shipment['LINK'] = 'https://www.pochta.ru/tracking';
            else
                $shipment['LINK'] = 'https://www.cdek.ru/';
        }
    }
}

$arResult['ORDERS_STATUS'] = array();
$arFilter = array("USER_ID" => $USER->GetID());
$arSelect = array('STATUS_ID');

$dbOrders = CSaleOrder::GetList(array(), $arFilter, false, false, $arSelect);

while ($arOrder = $dbOrders->Fetch())
{
    if (!in_array($arOrder['STATUS_ID'], $arResult['ORDERS_STATUS']))
        $arResult['ORDERS_STATUS'][] = $arOrder['STATUS_ID'];
}
