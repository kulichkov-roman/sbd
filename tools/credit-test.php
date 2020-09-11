<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php"); ?>
<html>

<? 
CModule::IncludeModule('sale');
$HMAC_Generator = new HMAC_Generator;
echo "<pre>";
// берем id заказа
$orderID = preg_replace('/[^-a-zA-Z0-9_]/', '', $_GET['special_id']);


$orders = CSaleOrder::GetList(array(), array("ACCOUNT_NUMBER"=>$orderID));
while ($arProps = $orders->Fetch())
  {
  	$order = $arProps;
  	$the_id = intval($order['ID']);

  	}

if($order['ID']==false) exit();
// берем свойства заказа
$order_props = CSaleOrderPropsValue::GetOrderProps($the_id);
while ($arProps = $order_props->Fetch())
  {
  	$order[] = $arProps;
  	}

// берем корзину заказа
$dbBasketItems = CSaleBasket::GetList(array(),
        array("ORDER_ID" => $the_id));
    while ($arItems = $dbBasketItems->Fetch()) {
        $arBasketItems[] = $arItems;
        $arProductID[] = $arItems['PRODUCT_ID'];
    }

// если есть доставка, то берем ее в заказ    
if(intval($order["PRICE_DELIVERY"])>0) $delivery=intval($order["PRICE_DELIVERY"]);



$basket = array();
$order_summ = 0;
// полученные данные корзины расставляем в массив
$i = 1; foreach($arBasketItems as $item):
			$basket[$i]['itemName']			= $item['NAME'];
			$basket[$i]['itemPrice']		= number_format($item['PRICE'], 2, ".", '');
			$basket[$i]['itemQuantity']		= number_format($item['QUANTITY'], 0);
			$i++;
			$order_summ = $order_summ + $item['PRICE']*$item['QUANTITY'];
		endforeach;

$order_summ = number_format($order_summ+$delivery, 2,  ".", '');
// var_dump($basket);


// берем данные заказа
$arOrder = CSaleOrder::GetByID($the_id);


foreach ($order as $prop) {
	if($prop["CODE"] == "EMAIL") $arOrder["EMAIL"] = $prop["VALUE"];
	if($prop["CODE"] == "PHONE") $arOrder["PHONE"] = $prop["VALUE"];
}
// собираем поля заказа
$order = array(
	"sum"				=> $order_summ,
	"orderNumber"		=> $arOrder["ACCOUNT_NUMBER"],
	"customerNumber"	=> $arOrder["USER_ID"],
	"customerEmail"		=> $arOrder["EMAIL"],
	"customerPhone"		=> $arOrder["PHONE"]
);

// добавляем подпись в массив заказа
$sign = array("sign_param_name" => $_GET["sign_param_name"]);
// $sign = array("sign_param_name" => $HMAC_Generator->make_data_hmac($order));

$order = array_merge($order,$sign);
var_dump($order);
// echo $HMAC_Generator->check_data_hmac($order, NULL, "sign_param_name");
echo "</pre>";


?>
<?
if($delivery):
// if ($HMAC_Generator->check_data_hmac($order, NULL, "sign_param_name") == 1):
 ?>
<form action="https://loans-qa.tcsbank.ru/api/partners/v1/lightweight/create" method="post" name="credit">
<input name="shopId" value="test_online" type="hidden"/>
<input name="showcaseId" value="test_online" type="hidden"/>
<input name="promoCode" value="default" type="hidden"/>
<input name="sum" value="<?=$order_summ?>" type="hidden">
<input name="orderNumber" value="<?=$arOrder["ACCOUNT_NUMBER"]?>" type="hidden"/>


<? $i=0; foreach($basket as $item):  ?>
<input name="itemName_<?=$i?>" value="<?=$item["itemName"]?>" type="hidden"/>
<input name="itemQuantity_<?=$i?>" value="<?=$item["itemQuantity"]?>" type="hidden"/>
<input name="itemPrice_<?=$i?>" value="<?=$item["itemPrice"]?>" type="hidden"/>
<? $i++; endforeach; ?>

<? if($delivery>0):?>
<input name="itemName_<?=$i?>" value="Доставка" type="hidden"/>
<input name="itemQuantity_<?=$i?>" value="1" type="hidden"/>
<input name="itemPrice_<?=$i?>" value="<?=number_format($delivery, 2,  ".", '')?>" type="hidden"/>
<?endif;?>

<input name="customerNumber" value="<?=$arOrder["USER_ID"]?>" type="hidden"/>
<input name="customerEmail" value="<?=$arOrder["EMAIL"]?>" type="hidden"/>
<input name="customerPhone" value="<?=$arOrder["PHONE"]?>" type="hidden"/>
<input type="submit" value="Перенаправляю..."/>
</form>
<? else: ?>
	<h1>Ошибка проверки данных о заказе</h1>
<? endif; ?>
</body>
</html>