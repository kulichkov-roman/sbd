<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;


$resizer_sets_list = array () ;
if(Loader::IncludeModule("yenisite.resizer2")){
	$arSets = CResizer2Set::GetList();
	while($arr = $arSets->Fetch())
	{
		$resizer_sets_list[$arr["id"]] = "[".$arr["id"]."] ".$arr["NAME"];
	}
}

global $arComponentParameters;

// RESIZER:
$arComponentParameters["GROUPS"]["RESIZER_SETS"]= array(
	"NAME" => GetMessage("RESIZER_SETS"),
	"SORT" => 1
);

$arTemplateParameters = array(
	"RESIZER_SET" => array(
		"PARENT" => "RESIZER_SETS",
		"NAME" => GetMessage("RESIZER_SET"),
		"TYPE" => "LIST",
		"VALUES" => $resizer_sets_list,
		"DEFAULT" => "3",
	),
	'CATALOG_PATH' => array(
		"NAME" => GetMessage("CATALOG_PATH"),
		"TYPE" => "STRING",
		"DEFAULT" => "={SITE_DIR.\"catalog/\"}",
	)
);
