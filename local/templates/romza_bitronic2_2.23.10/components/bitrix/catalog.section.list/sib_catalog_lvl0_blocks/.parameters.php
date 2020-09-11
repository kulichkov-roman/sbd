<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;
global $USER_FIELD_MANAGER;

if (!\Bitrix\Main\Loader::includeModule("iblock"))
    return;

$resizer_sets_list = array () ;
if (Loader::IncludeModule("yenisite.resizer2")){
	$arSets = CResizer2Set::GetList();
	while($arr = $arSets->Fetch())
	{
		$resizer_sets_list[$arr["id"]] = "[".$arr["id"]."] ".$arr["NAME"];
	}
}

global $arComponentParameters;

$arIBlockType = CIBlockParameters::GetIBlockTypes();

$arIBlock = array();
$rsIBlock = CIBlock::GetList(Array("sort" => "asc"), Array("TYPE" => $arCurrentValues["IBLOCK_TYPE"], "ACTIVE"=>"Y"));
while ($arr = $rsIBlock->Fetch())
{
    $arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];
}

$arProperty_UF = array();
$arUserFields = $USER_FIELD_MANAGER->GetUserFields("IBLOCK_".$arCurrentValues["IBLOCK_ID"]."_SECTION", 0, LANGUAGE_ID);

foreach($arUserFields as $FIELD_NAME=>$arUserField)
{
    $arUserField['LIST_COLUMN_LABEL'] = (string)$arUserField['LIST_COLUMN_LABEL'];
    $arProperty_UF[$FIELD_NAME] = $arUserField['LIST_COLUMN_LABEL'] ? '['.$FIELD_NAME.']'.$arUserField['LIST_COLUMN_LABEL'] : $FIELD_NAME;
}

$arTemplateParameters = array(
	"RESIZER_SET" => array(
		"PARENT" => "BASE",
		"NAME" => GetMessage("RESIZER_SECTION_ICON"),
		"TYPE" => "LIST",
		"VALUES" => $resizer_sets_list,
		"DEFAULT" => "6",
	),
    "PROP_OF_IMG" => array(
        "PARENT" => "BASE",
        "NAME" => GetMessage("PROP_OF_IMG"),
        "TYPE" => "LIST",
        "VALUES" => $arProperty_UF,
        "DEFAULT" => "UF_IMG_BLOCK_FOTO",
    ),
);
?>