<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

require_once("../includes/connection.php");
require_once("../includes/functions.php");
require_once("../includes/PortalData.php");

if(!$USER->IsAdmin()) {
	return;
}

$script_url = SERVER_ADDRESS . BASE_DIR . '/events/listener.php';

$list = executeMethod('event.get', []);
$check = false;
foreach ($list as $item) {
	if ($item['event'] == 'ONCRMDEALADD' && $item['handler'] == $script_url) {
		$check = true;
	}
}
if ($check) {
//	$resp = executeMethod('userfieldtype.delete', [
//		'USER_TYPE_ID' => 'crm_deal_products',
//	], false);
//	echo '<pre>'; print_r($resp); echo '</pre>';
	echo 'Обработчик события уже установлен';
}
else {
	$resp = executeMethod('event.bind', [
		'event' => 'onCrmDealAdd',
		'handler' => $script_url,
	], false);
	if ($resp['result']) {
		echo 'Обработчик события установлен';
	}
	else {
		echo 'Не удалось установить обработчик';
	}
}
