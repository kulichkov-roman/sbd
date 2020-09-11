<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

require_once("../includes/connection.php");
require_once("../includes/functions.php");
require_once("../includes/PortalData.php");

if(!$USER->IsAdmin()) {
	return;
}

$script_url = SERVER_ADDRESS . BASE_DIR . '/placements/uf_goods.php';

$list = executeMethod('userfieldtype.list', []);
$check = false;
foreach ($list as $item) {
	if ($item['USER_TYPE_ID'] == 'crm_deal_products' && $item['HANDLER'] == $script_url) {
		$check = true;
	}
}
if ($check) {
//	$resp = executeMethod('userfieldtype.delete', [
//		'USER_TYPE_ID' => 'crm_deal_products',
//	], false);
//	echo '<pre>'; print_r($resp); echo '</pre>';
	echo 'Пользовательский тип полей уже создан';
}
else {
	$resp = executeMethod('userfieldtype.add', [
		'USER_TYPE_ID' => 'crm_deal_products',
		'HANDLER' => $script_url,
		'TITLE' => 'Товары сделки',
	], false);
	if ($resp['result']) {
		echo 'Пользовательский тип полей создан';
	}
	else {
		echo 'Не удалось создать пользовательский тип полей';
	}
}
