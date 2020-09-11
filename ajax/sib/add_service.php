<?
use Bitrix\Main\Loader;
use \Bitronic2\Mobile;

include_once "include_stop_statistic.php";
if(isset($_GET["ajax_basket"]) && $_GET["ajax_basket"] === "Y")
{
    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
}else{
    if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
}

if(!CModule::IncludeModule('iblock')
    || !CModule::IncludeModule('catalog')
    || !CModule::IncludeModule('sale')
    || !$_GET['action']
    || !$_GET['id']
)
    die();

if($_GET['action'] == 'ADD2BASKET' /* && (int)$_GET['GOOD_PRICE'] > 0 */){

    $id = (int)$_GET['id'];

    $basket = \Bitrix\Sale\Basket::loadItemsForFUser(
        \Bitrix\Sale\Fuser::getId(), 
        \Bitrix\Main\Context::getCurrent()->getSite()
    );

    $item = $basket->createItem('catalog', $id);
	$iName = $_GET['ATTACHED_GOOD_SECTION_NAME'];
    $item->setFields([
        'QUANTITY' => 1,
        'CURRENCY' => \Bitrix\Currency\CurrencyManager::getBaseCurrency(),
        'LID' => \Bitrix\Main\Context::getCurrent()->getSite(),
        'PRICE' => (int)$_GET['GOOD_PRICE'],
        'CUSTOM_PRICE' => 'Y',
        'NAME' => htmlspecialchars($iName)
     ]);
    
     $basket->save();

     echo json_encode((object)[
         'STATUS' => 'OK',
         'MESSAGE' => "Товар успешно добавлен в корзину"
     ]);
}
