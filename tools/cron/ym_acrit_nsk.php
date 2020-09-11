<? $_SERVER["DOCUMENT_ROOT"] = "/home/bitrix/www";
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

CModule::IncludeModule('acrit.exportproplus');
CExportproplusAgent::StartExport(36,2,true);	

echo "\nexport done\n\n";
?>