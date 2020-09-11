<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

require_once("includes/connection.php");
require_once("includes/functions.php");
require_once("includes/PortalData.php");

if(!$USER->IsAdmin()) {
	return;
}

$script_url = '/crmorders/order_edit.php';

$obRest = SProdCRMSyncGetRestObj();
if ($obRest) {
	$arCred = $obRest->getCred();
	$arRes = $obRest->restCommand('placement.get', [], $arCred);
	$check = false;
	foreach ($arRes['result'] as $placement) {
		if ($placement['placement'] == 'CRM_DEAL_DETAIL_TAB' && $placement['handler'] == $script_url) {
			$check = true;
		}
	}
	if ($check) {
		echo 'Плейсмент уже установлен';
	}
	else {
		$arRes = $obRest->restCommand('placement.bind', [
			'PLACEMENT' => 'CRM_DEAL_DETAIL_TOOLBAR',
			'HANDLER' => $script_url,
			'TITLE' => 'Правка заказа',
		], $arCred);
		if ($arRes['result']) {
			echo 'Плейсмент установлен';
		}
		else {
			echo 'Не удалось установить плейсмент';
		}
	}
}
