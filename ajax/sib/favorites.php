<?
include_once "include_stop_statistic.php";
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$pid = (int)$_REQUEST['ID'];
if(!check_bitrix_sessid() || !\Bitrix\Main\Loader::includeModule('sale') || !\Bitrix\Main\Loader::includeModule('sib.core')){
    echo json_encode((object)['TYPE' => 'ERROR', 'MSG' => 'Ошибка добавления в избранное']);
    die();
}

if($_REQUEST['ACTION'] !== 'FLUSH' && $pid <= 0){
    echo json_encode((object)['TYPE' => 'ERROR', 'MSG' => 'Ошибка добавления в избранное']);
    die();
}

$result = \Bitrix\Sale\Internals\BasketTable::getList(array(
    'filter' => array(
        'FUSER_ID' => \Bitrix\Sale\Fuser::getId(), 
        'ORDER_ID' => null,
        'LID' => \Bitrix\Main\Context::getCurrent()->getSite(),
        'DELAY' => 'Y',
        'PRODUCT_ID' => $pid
    )
))->fetchAll();

$allDelay = \Bitrix\Sale\Internals\BasketTable::getList(array(
    'filter' => array(
        'FUSER_ID' => \Bitrix\Sale\Fuser::getId(), 
        'ORDER_ID' => null,
        'LID' => \Bitrix\Main\Context::getCurrent()->getSite(),
        'DELAY' => 'Y'
    )
))->fetchAll();

$basket = \Bitrix\Sale\Basket::loadItemsForFUser(\Bitrix\Sale\Fuser::getId(), \Bitrix\Main\Context::getCurrent()->getSite());
$response = (object)['TYPE' => 'ERROR', 'MSG' => 'Неизвестное действие'];
switch($_REQUEST['ACTION']){
    case 'ADD':
        if(count($result) > 0){
            $response = (object)['TYPE' => 'ERROR', 'MSG' => 'Товар уже есть в избранном'];
        } else {
            //$defRegion = \Sib\Core\Catalog::getDefRegion();
            //$prices = \Sib\Core\Catalog::getDiscountPrice($pid);
            /* if(!isset($prices[$defRegion])){
                $response = (object)['TYPE' => 'ERROR', 'MSG' => 'Ошибка цены товара'];
            } else { */
                //$price = $prices[$defRegion];
                $item = $basket->createItem('catalog', $pid);

                $element = \Sib\Core\Helper::getElementInfo($pid);

                $fields = array(
                    'QUANTITY' => 1,
                    'CURRENCY' => \Bitrix\Currency\CurrencyManager::getBaseCurrency(),
                    'LID' => \Bitrix\Main\Context::getCurrent()->getSite(),
                    'PRODUCT_PROVIDER_CLASS' => '\Bitrix\Catalog\Product\CatalogProvider',
                    'DELAY' => 'Y',
                    'NAME' => $element['NAME']
                );

                $priceArray = \Sib\Core\Catalog::getDiscountPriceArray($pid);
                if($priceArray['PRICE_DISCOUNT'] > 0){
                    $fields['PRICE'] = $priceArray['PRICE_DISCOUNT'];
                    $fields['BASE_PRICE'] = $priceArray['BASE_PRICE'];
                    $fields['CUSTOM_PRICE'] = 'Y';
                } else {
                    $fields['BASE_PRICE'] = $priceArray['BASE_PRICE'];
                }

                /* if($price['PRICE_DISCOUNT'] !== 0 && $price['PRICE_DISCOUNT'] <= $price['BASE_PRICE']){
                    $fields['PRICE'] = $price['PRICE_DISCOUNT'];
                    $fields['BASE_PRICE'] = $price['BASE_PRICE'];
                    $fields['CUSTOM_PRICE'] = 'Y';
                } else {
                    $fields['BASE_PRICE'] = $price['BASE_PRICE'];
                } */

                $item->setFields($fields);
                $response = (object)['TYPE' => 'OK', 'ACTION' => 'ADD', 'MSG' => 'Товар добавлен в избранное', 'COUNT' => count($allDelay) + 1];
            //}
           
        }
    break;
    case 'FLUSH':
        $basketItems = $basket->getBasketItems();
        foreach ($basketItems as $basketItem) {
            if($basketItem->isDelay()){
                $basketItem->delete();
            }
        }
        $response = (object)['TYPE' => 'OK', 'ACTION' => 'DELETE', 'MSG' => 'Товары удалены из избранного', 'COUNT' =>  0];
    break;
    case 'DELETE':
        if(count($result) > 0){
            $basketItems = $basket->getBasketItems();
            foreach ($basketItems as $basketItem) {
                if($basketItem->isDelay() && (int)$basketItem->getField('PRODUCT_ID') === $pid){
                    $basketItem->delete();
                }
            }
            $response = (object)['TYPE' => 'OK', 'ACTION' => 'DELETE', 'MSG' => 'Товар удален из избранного', 'COUNT' =>  count($allDelay) - 1];
        } else {
            $response = (object)['TYPE' => 'ERROR', 'MSG' => 'Товар уже удален'];
        }
    break;
}
$basket->save();

$tmpl = 'sib_favorite';
if($_REQUEST['IS_MOBILE']){
    $tmpl = 'sib_favorite_mobile';
}

$APPLICATION->IncludeComponent(
	"bitrix:sale.basket.basket.line",  
	$tmpl, 
	array(
		"PATH_TO_BASKET" => SITE_DIR."personal/cart/",
		"SHOW_NUM_PRODUCTS" => "Y",
		"SHOW_TOTAL_PRICE" => "Y",
		"SHOW_EMPTY_VALUES" => "Y",
		"SHOW_PERSONAL_LINK" => "N",
		"PATH_TO_PERSONAL" => SITE_DIR."personal/settings.php",
		"SHOW_AUTHOR" => "N",
		"PATH_TO_REGISTER" => SITE_DIR."auth/",
		"PATH_TO_PROFILE" => SITE_DIR."personal/settings.php",
		"SHOW_PRODUCTS" => "Y",
		"SHOW_DELAY" => "Y",
		"SHOW_NOTAVAIL" => "N",
		"SHOW_SUBSCRIBE" => "N",
		"SHOW_IMAGE" => "N",
		"SHOW_PRICE" => "Y",
		"SHOW_SUMMARY" => "Y",
		"PATH_TO_ORDER" => SITE_DIR."personal/order/",
		"POSITION_FIXED" => "N",
		"RESIZER_BASKET_ICON" => "9"
	),
	false
);