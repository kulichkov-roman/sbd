<?
define("STOP_STATISTICS", true);
define('NO_AGENT_CHECK', true);
require $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php";

$isCheckRequest = false;
if(isset($_POST['check'])){
    $isCheckRequest = true;
    $checkList = $_POST['check'];
    unset($_POST['check']);
}
$params = array_map('htmlspecialchars', $_POST);

use Sib\Core\BlogRating as BR;

if(\Bitrix\Main\Loader::includeModule('sib.core') && check_bitrix_sessid()){
    if($isCheckRequest){
        echo json_encode(BR::getUserRateList($checkList));  
    } else {
        echo json_encode(BR::updateRate($params['entity'], (int)$params['id'], (int)$params['rate'], (int)$params['currentRate']));  
    }
    
    die();
}

echo json_encode(['ID' => 0]);