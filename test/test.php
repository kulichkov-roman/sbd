<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Application,
	SProduction\Integration\Rest,
	SProduction\Integration\ProfilesTable,
	SProduction\Integration\ProfileInfo,
	SProduction\Integration\Integration,
	SProduction\Integration\OrderAddLock,
	SProduction\Integration\Settings,
	SProduction\Integration\AddSync,
	Bitrix\Sale,
	Bitrix\Currency\CurrencyManager,
	Bitrix\Main\Context;

/*
\Bitrix\Main\Loader::includeModule('sale');

$order_id = 54624;
$order = Bitrix\Sale\Order::load($order_id);
$order_num = $order->getField('ACCOUNT_NUMBER');
$order_date = $order->getDateInsert()->format("d.m.Y H:i:s");
$order_comment = $order->getField('USER_DESCRIPTION');
if (strpos($order_comment, 'Это контрольный заказ Яндекс.Маркета') !== false) {
	$message = 'Заказ #' . $order_id . "\n" .
	           'Дата заказа: ' . $order_date . "\n" .
	           'Комментарий: ' . $order_comment . "\n\n" .
	           'Ссылка на заказ: https://sibdroid.ru/bitrix/admin/sale_order_view.php?ID='.$order_id.'&filter=Y&set_filter=Y&lang=ru';
	echo $message;
	$token = "756896270:AAGPumkCmbd9MVNJFY5OJfg32DVriaHooOg";
	$chat_id = "-324175883";
	//saveLog($message);
	$url = "https://api.telegram.org/bot".$token."/sendMessage?disable_web_page_preview=true&parse_mode=HTML&chat_id=".$chat_id."&text=".urlencode($message);
	if ($res = json_decode(curl_get_contents($url))) {
		if($res->ok != 1) {
			//		print_r($res);
		}
	}
	echo '<pre>'; print_r($res); echo '</pre>';
}
*/

/*
\Bitrix\Main\Loader::includeModule('sproduction.integration');

$order_id = 82136;
$order = Sale\Order::load($order_id);
$order_data = Integration::getOrderArray($order);
Integration::syncOrderToDeal($order_data);
echo '<pre>'; print_r($order_data); echo '</pre>';
//$profile = Integration::getOrderProfile($order_data);
//$deal_id = 1357;
//$deal_info = Integration::getDealInfo($profile, $deal_id);
//$res = Integration::getDealContactDataByProfile($order_data, $profile);
*/
