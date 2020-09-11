<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("tester");
?><?$APPLICATION->IncludeComponent(
	"bitrix:sale.affiliate.register",
	"",
Array(),
false
);?><br>
<?
//Инициализация события для работы отправки маркета
//RegisterModuleDependences("test", "TestModul", "vbcherepanov.ordertoamo", "Cvbchamo", "OnYmOrderAdd");

$rsEvents = GetModuleEvents("test", "TestModul");
while ($arEvent = $rsEvents->Fetch())
{
echo "<pre>";
print_r($arEvent);
echo "</pre>";
}
CModule::IncludeModule("sale");

$arOrder = CSaleOrder::GetByID(9155);
if(strpos($arOrder['XML_ID'], 'ymarket') === false) {
   echo "<pre>";
	//print_r($arOrder);
   echo "</pre>";
}

?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>