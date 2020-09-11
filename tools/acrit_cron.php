<? $_SERVER["DOCUMENT_ROOT"] = "/home/bitrix/www";
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

global $USER; 
$USER->Authorize(2); 

CModule::IncludeModule('acrit.exportproplus');
CExportproplusAgent::StartExport(85,2,true);	

echo "\nexport done\n\n";
?>