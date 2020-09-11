<?
$_SERVER["DOCUMENT_ROOT"] = "/home/bitrix/www";
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if (file_exists($_SERVER["DOCUMENT_ROOT"]."/local/lib/telegrambot.php"))
	require_once($_SERVER['DOCUMENT_ROOT'].'/local/lib/telegrambot.php') ;


// echo "<pre>";


$proxy['host'] = 'socks5://217.160.214.98:8080';
$proxy['user'] = 'proxyuser';
$proxy['pass'] = 'Crmcontrol654321-TELEGRAM';



        Bitrix\Main\Loader::includeModule('sale');
        Bitrix\Main\Loader::includeModule('catalog');

$order = Bitrix\Sale\Order::load(77086);

		// $order = $event->getParameter("ENTITY");
		$order_id = $order->getId();
		// $order = Bitrix\Sale\Order::load($order_id);
		$order_num = $order->getField('ACCOUNT_NUMBER');
		$order_date = $order->getDateInsert()->format("d.m.Y H:i:s");
		$order_comment = [];
		$order_comment[] = $order->getField('USER_DESCRIPTION');


	$propertyCollection = $order->getPropertyCollection();
	$address = $propertyCollection->getItemByOrderPropertyId(7); // 7 = ADDRESS
	$test = $order->getField('USER_DESCRIPTION');

	// var_dump($test);


// Фразы для идентификации контрольного заказа
		$phrases = [
			'Это контрольный заказ Яндекс.Маркета','Это контрольный заказ Маркета', 
		];
		
		$is_ym_check = false;

		foreach ($order_comment as $comment) {
			foreach ($phrases as $check_string) {
				if (strpos($comment, $check_string) !== false) {
					$is_ym_check = true;
					$total_comment = $comment;
					break;
				}
			}
			if($is_ym_check) break;
		}


		var_dump($is_ym_check);
		exit();
		if ($is_ym_check==true) {

			$message = 'Заказ #' . $order_id . "\n" .
			           'Дата заказа: ' . $order_date . "\n" .
			           'Комментарий: ' . $total_comment . "\n\n" .
					   'https://sibdroid.ru/bitrix/admin/sale_order_view.php?ID='.$order_id.'&filter=Y&set_filter=Y&lang=ru';
			$token = "756896270:AAGPumkCmbd9MVNJFY5OJfg32DVriaHooOg";
			$chat_id = "-1001269487222";
			//saveLog($message);


			$cTgBot = new TelegramBot();
			$cTgBot->setToken($token);
			$cTgBot->setChatID($chat_id);
			$result = $cTgBot->send($message);

			/*$url = "https://api.telegram.org/bot".$token."/sendMessage?disable_web_page_preview=true&parse_mode=HTML&chat_id=".$chat_id."&text=".urlencode($message);
			var_dump($url);
			// print_r($url);

	$ch = curl_init();
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
    curl_setopt($ch, CURLOPT_PROXY, $proxy['host']);
    curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy['user'].':'.$proxy['pass']);
    curl_setopt($ch, CURLOPT_PROXYTYPE, "CURLPROXY_HTTP");
	// var_dump($url);
	$result = curl_exec($ch);
	var_dump($result);
	curl_close($ch);*/



			// exit();
			if ($res = json_decode($result)) {
				if($res->ok != 1) {
					
				}
				// 
			}
			var_dump($res);
			
		}
