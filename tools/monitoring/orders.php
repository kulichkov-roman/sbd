<? 
define("NOT_CHECK_PERMISSIONS",true);
$_SERVER["DOCUMENT_ROOT"] = "/home/bitrix/www";
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");


// echo "123";
// exit();


global $USER; 
$USER->Authorize(2); 

\Bitrix\Main\Loader::includeModule('sproduction.integration');
$status = SProduction\Integration\Integration::checkModuleStatus();
if ($status['connect']) {
  echo "1";
} else {
  echo "0";
}
// var_dump($status['connect']);