<?$_SERVER["DOCUMENT_ROOT"] = "/home/bitrix/www";
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>
<?
set_time_limit(0);

CModule::IncludeModule('yenisite.resizer2');

$setDir = (int)$_GET['id'];
$rs = \Yenisite\Resizer2\SetFileTable::getList(array(
	'filter' => array(
		'LOGIC' => 'OR',
		'SET_DIR' => $setDir,
		'%=SET_DIR' => $setDir . '\\_%'
	),
	'limit' => 1000,
	'offset' => 0
));
$arPrimaryKey = array_flip(\Yenisite\Resizer2\SetFileTable::getEntity()->getPrimaryArray());

$isAvail = false;
while ($arSetFile = $rs->Fetch()) {
	$arPrimary = array_intersect_key($arSetFile, $arPrimaryKey);
	//echo '<pre>'; print_r($arSetFile); echo '</pre>';
	if(!unlink($_SERVER['DOCUMENT_ROOT'] . \CFile::GetPath($arSetFile['FILE_ID']))){
		echo 'error delete';
		break;
	}
	\CFile::Delete($arSetFile['FILE_ID']);
	\Yenisite\Resizer2\SetFileTable::delete($arPrimary);
	$isAvail = true;
}
if($isAvail){
	header("Refresh:1");
} else {
	echo 'done';
}
?>
