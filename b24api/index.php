<?
require_once("includes/connection.php");
require_once("includes/functions.php");

logSave(print_r($_REQUEST, true));

$action = trim($_REQUEST['action']);
$secret_code = trim($_REQUEST['skey']);
$params = $_REQUEST['params'];
$arResult = [];
$arResult['status'] = 'error';
//$arResult['log'] = [];
$lock_result = false;

if ($secret_code != SECRET_CODE) {
	$arResult['message'] = 'Not valid secret code';
}
else {
	switch ($action) {

//		case 'test':
//			try {
//				$res = executeMethod('crm.timeline.comment.list', [
//					'filter' => [
//						"ENTITY_ID" => 161214,
//	                    "ENTITY_TYPE"  => "deal",
//					],
//					'select' => [ "ID", "COMMENT ", "FILES"]
//				]);
//				$arResult['result'] = $res;
//				$arResult['status'] = 'success';
//			} catch (Exception $e) {
//				$arResult['message'] = $e->getMessage();
//			}
//			break;

	}
}

if (!$lock_result) {
	echo json_encode($arResult);
}
