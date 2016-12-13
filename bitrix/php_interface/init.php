<?
//-- Конфигурация сайта
$configuration = \Bitrix\Main\Config\Configuration::getInstance();
$configuration->add('partnerId', 'a06m00000018y7rAAA');
$configuration->add('partnerOrderId', 'test_order_'.uniqid());
$configuration->add('secretKeyId', 'grid-secret-18y7r72a');
$configuration->add('catalogIBlockId', 6);

//-- Добавление обработчика события

AddEventHandler("sale", "OnOrderStatusSendEmail", "bxModifyStatusMails");

AddEventHandler("sale", "OnOrderNewSendEmail", "bxModifySaleMails");

AddEventHandler("iblock", "OnAfterIBlockElementAdd", 'email_notify');

function bxModifyStatusMails($orderID, &$eventName, &$arFields, $val)
{
  if (!CModule::IncludeModule("sale")) return;
  $arOrder = CSaleOrder::GetByID($orderID);
  
  //-- получаем телефоны и адрес
  $order_props = CSaleOrderPropsValue::GetOrderProps($orderID);
  $phone="";
  $index = ""; 
  $country_name = "";
  $city_name = "";  
  $address = "";
  while ($arProps = $order_props->Fetch())
  {
    if ($arProps["CODE"] == "PHONE")
    {
       $phone = htmlspecialchars($arProps["VALUE"]);
    }

    if ($arProps["CODE"] == "LOCATION")
    {
        $arLocs = CSaleLocation::GetByID($arProps["VALUE"]);
        $country_name =  $arLocs["COUNTRY_NAME_ORIG"];
        $city_name = $arLocs["CITY_NAME_ORIG"];
    }

    if ($arProps["CODE"] == "INDEX")
    {
      $index = $arProps["VALUE"];   
    }

    if ($arProps["CODE"] == "ADDRESS")
    {
      $address = $arProps["VALUE"];
    }
  }

  $full_address = $index.", ".$country_name."-".$city_name.", ".$address;
  $short_address = $city_name.", ".$address;

  $track = $arOrder["TRACKING_NUMBER"];
 
  //-- получаем название службы доставки
  $deliveryID = $arOrder["DELIVERY_ID"];
  $arDeliv = CSaleDelivery::GetByID($deliveryID);
  $delivery_name = "";
  $delivery_description = "";
  $delivery_price = "";
  if ($arDeliv)
  {
    $delivery_name = $arDeliv["NAME"];
    $delivery_description = $arDeliv["DESCRIPTION"];
  }

  switch ($deliveryID) {
  	case 'edost:3':	
  	//ПОЧТА РОССИИ
  	
  	$delivery_message_tittle = "Ваш заказ отправлен почтой России";
  	$delivery_message = " Трек-номер отправления: <b>".$track."</b> вы можете отслеживать вашу посылку на сайте почты России по ссылке: <a href=\"https://www.pochta.ru/tracking#".$track."\">https://www.pochta.ru/tracking#</a>";  
  	$delivery_message_nl = "Ваш заказ отправлен наложенным платежом. Трек-номер отправления: <b>".$track."</b> вы можете отслеживать вашу посылку на сайте почты России по ссылке: <a href=\"https://www.pochta.ru/tracking#".$track."\">https://www.pochta.ru/tracking#</a>"; 
  	break;
  	case 'edost:74':	
  	//СДЭК до склада
  	$delivery_message_tittle = "Ваш заказ отправлен транспортной компанией \"СДЭК\" до склада компании";
  	$delivery_message = "Трек-номер отправления: <b>".$track."</b> вы можете отслеживать вашу посылку на сайте транспортной компании по ссылке: <a href=\"http://www.edostavka.ru/track.html?order_id=".$track."\">http://www.edostavka.ru/track.html</a>";  
  	$delivery_message_nl = "Ваш заказ отправлен наложенным платежом.Трек-номер отправления: <b>".$track."</b> вы можете отслеживать вашу посылку на сайте транспортной компании по ссылке: <a href=\"http://www.edostavka.ru/track.html?order_id=".$track."\">http://www.edostavka.ru/track.html</a>";  
  	
  	break;
  	case 'edost:76':	
  	//СДЭК до двери
  	$delivery_message_tittle = "Ваш заказ отправлен транспортной компанией \"СДЭК\" до вашей двери";
  	$delivery_message = "Трек-номер отправления: <b>".$track."</b> вы можете отслеживать вашу посылку на сайте транспортной компании по ссылке: <a href=\"http://www.edostavka.ru/track.html?order_id=".$track."\">http://www.edostavka.ru/track.html</a>";  
  	$delivery_message_nl = "Ваш заказ отправлен наложенным платежом.Трек-номер отправления: <b>".$track."</b> вы можете отслеживать вашу посылку на сайте транспортной компании по ссылке: <a href=\"http://www.edostavka.ru/track.html?order_id=".$track."\">http://www.edostavka.ru/track.html</a>";  
  	break;
  	case 'edost:43':	
  	//Деловые линии
  	$delivery_message_tittle = "Ваш заказ отправлен транспортной компанией \"Деловые линии\"";
  	$delivery_message = "Трек-номер отправления: <b>".$track."</b> вы можете отслеживать вашу посылку на сайте транспортной компании по ссылке: <a href=\"http://www.dellin.ru/tracker/?mode=search&rwID=".$track."\">http://www.dellin.ru/tracker/</a>";  
  	$delivery_message_nl = "Мы тщательно упаковали ваш товар и отправли наложенным платежом.Трек-номер отправления: <b>".$track."</b> вы можете отслеживать вашу посылку на сайте транспортной компании по ссылке: <a href=\"http://www.dellin.ru/tracker/?mode=search&rwID=".$track."\">http://www.dellin.ru/tracker/</a>";  
  	break;
  	case 'edost:6':	
  	//EMS
  	$delivery_message_tittle = "Ваш заказ отправлен ускоренной почтой \"EMS\"";
  	$delivery_message = "Трек-номер отправления: <b>".$track."</b> вы можете отслеживать вашу посылку на сайте EMS по ссылке: <a href=\"http://www.emspost.ru/ru/tracking/\">http://www.emspost.ru/ru/tracking/</a>";  
  	$delivery_message_nl = "Мы тщательно упаковали ваш товар и отправли наложенным платежом.Трек-номер отправления: <b>".$track."</b> вы можете отслеживать вашу посылку на сайте EMS по ссылке: <a href=\"http://www.emspost.ru/ru/tracking/\">http://www.emspost.ru/ru/tracking/</a>";  
  	break;
  }
  //-- получаем название платежной системы   
  $arPaySystem = CSalePaySystem::GetByID($arOrder["PAY_SYSTEM_ID"]);
  $pay_system_name = "";
  if ($arPaySystem)
  {
    $pay_system_name = $arPaySystem["NAME"];
  }


  //-- добавляем новые поля в массив результатов

  $arFields["ORDER_DESCRIPTION"] = $arOrder["USER_DESCRIPTION"]; 
  $arFields["PHONE"] =  $phone;
  $arFields["DELIVERY_NAME"] =  $delivery_name;
  $arFields["DELIVERY_DESCRIPTION"] = $delivery_description;
  $arFields["DELIVERY_PRICE"] = $arOrder["PRICE_DELIVERY"];
  $arFields["PAY_SYSTEM_NAME"] =  $pay_system_name;
  $arFields["FULL_ADDRESS"] = $full_address;  
  $arFields["TRACKING_NUMBER"] = $track;
  $arFields["SHORT_ADDRESS"] = $short_address;  
  $arFields["TOTAL_PRICE"] = $arOrder["PRICE"];

  $arFields["DELIVERY_MESSAGE"] = $delivery_message;
  $arFields["DELIVERY_MESSAGE_TITTLE"] = $delivery_message_tittle;
  $arFields["DELIVERY_MESSAGE_NL"] = $delivery_message_nl;

}

function bxModifySaleMails($orderID, &$eventName, &$arFields)
{
  if (!CModule::IncludeModule("sale")) return;
  $arOrder = CSaleOrder::GetByID($orderID);
  
  //-- получаем телефоны и адрес
  $order_props = CSaleOrderPropsValue::GetOrderProps($orderID);
  $phone="";
  $index = ""; 
  $country_name = "";
  $city_name = "";  
  $address = "";
  while ($arProps = $order_props->Fetch())
  {
    if ($arProps["CODE"] == "PHONE")
    {
       $phone = htmlspecialchars($arProps["VALUE"]);
    }
    if ($arProps["CODE"] == "LOCATION")
    {
        $arLocs = CSaleLocation::GetByID($arProps["VALUE"]);
        $country_name =  $arLocs["COUNTRY_NAME_ORIG"];
        $city_name = $arLocs["CITY_NAME_ORIG"];
    }

    if ($arProps["CODE"] == "INDEX")
    {
      $index = $arProps["VALUE"];   
    }

    if ($arProps["CODE"] == "ADDRESS")
    {
      $address = $arProps["VALUE"];
    }
  }

  $full_address = $index.", ".$country_name."-".$city_name.", ".$address;
  $short_address = $city_name.", ".$address;
    
    $cart_items = "";

    // -- получаем товары, цену, кол-во 
    $dbBasketItems = CSaleBasket::GetList(array("NAME" => "ASC","ID" => "ASC"),array("ORDER_ID" => $orderID));
    
    while ($arItems = $dbBasketItems->Fetch()) {

        $cart_items .= "<tr>";
        $cart_items .= '<td height="32" align="left" valign="middle" style="border-bottom:1px dashed #494949;">&nbsp;</td>';
        $cart_items .= '<td height="32" align="left" valign="middle" style="border-bottom:1px dashed #494949; font:13px/18px Arial, Helvetica, sans-serif; color:#545454;">'.$arItems["NAME"].'</td>';
        $cart_items .= '<td align="center" valign="middle" style="border-bottom:1px dashed #494949; font:13px/18px Arial, Helvetica, sans-serif; color:#545454;">'.$arItems["PRICE"].'</td>';
        $cart_items .= '<td align="center" valign="middle" style="border-bottom:1px dashed #494949; font:13px/18px Arial, Helvetica, sans-serif; color:#545454;">'.$arItems["QUANTITY"].'</td>';
        $cart_items .= '<td align="center" valign="middle" style="border-bottom:1px dashed #494949; font:13px/18px Arial, Helvetica, sans-serif; color:#545454;">'.$arItems["PRICE"]*$arItems["QUANTITY"].'</td>';
        $cart_items .= '</tr>'."\n";
    }

    // получаем итоговую цену без доставки

    $total_price_without_delivery = $arOrder["PRICE"]-$arOrder["PRICE_DELIVERY"];

  //-- получаем название службы доставки
  $arDeliv = CSaleDelivery::GetByID($arOrder["DELIVERY_ID"]);
  $delivery_name = "";
  $delivery_description = "";
  $delivery_price = "";
  if ($arDeliv)
  {
    $delivery_name = $arDeliv["NAME"];
    $delivery_description = $arDeliv["DESCRIPTION"];
  }

  //-- получаем название платежной системы   
  $arPaySystem = CSalePaySystem::GetByID($arOrder["PAY_SYSTEM_ID"]);
  $pay_system_name = "";
  if ($arPaySystem)
  {
    $pay_system_name = $arPaySystem["NAME"];
  }

  //-- добавляем новые поля в массив результатов

  $arFields["TOTAL_PRICE"] = $arOrder["PRICE"];
  $arFields["ORDER_BASKET"] = $cart_items;
  $arFields["TOTAL_PRICE_NO_DELIVERY"] = $total_price_without_delivery;
  $arFields["ORDER_DESCRIPTION"] = $arOrder["USER_DESCRIPTION"]; 
  $arFields["PHONE"] =  $phone;
  $arFields["DELIVERY_NAME"] =  $delivery_name;
  $arFields["DELIVERY_DESCRIPTION"] = $delivery_description;
  $arFields["DELIVERY_PRICE"] = $arOrder["PRICE_DELIVERY"];
  $arFields["PAY_SYSTEM_NAME"] =  $pay_system_name;
  $arFields["FULL_ADDRESS"] = $full_address;  
  $arFields["SHORT_ADDRESS"] = $short_address;  

   // $handle = fopen($_SERVER["DOCUMENT_ROOT"]."/email_log.txt","a-");
   // fwrite($handle, print_r($arFields, 1));
   // fclose($handle);
}
function custom_mail($to,$subject,$body,$headers) { 
$f=fopen($_SERVER["DOCUMENT_ROOT"]."/maillog.txt", "a+"); 
fwrite($f, print_r(array('TO' => $to, 'SUBJECT' => $subject, 'BODY' => $body, 'HEADERS' => $headers),1)."\n========\n"); 
fclose($f); 
return mail($to,$subject,$body,$headers); 
}
?>
