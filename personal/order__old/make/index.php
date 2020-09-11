<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Оформление заказа");
?>
<?if(CModule::IncludeModule('yenisite.bitronic2lite')):
	if(CModule::IncludeModule('yenisite.market')):?>
<?$APPLICATION->IncludeComponent("yenisite:catalog.basket", "bitronic2", Array(
	"PROPERTY_CODE" => array(
			0 => "FIO",
			1 => "EMAIL",
			2 => "PHONE",
			3 => "ABOUT",
			4 => "DELIVERY_E",
			5 => "PAYMENT_E",
		),
		"EVENT" => "SALE_ORDER",
		"EVENT_ADMIN" => "SALE_ORDER_ADMIN",
		"YENISITE_BS_FLY" => "",
		"EMPTY_URL" => "/personal/order/make/empty.php",
		"THANK_URL" => "/personal/order/make/thank_you.php",
		"ORDER_URL" => "/personal/order/?ID=#ID#",
		"UE" => "Р",
		"ADMIN_MAIL" => "admin@email.ru",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600",
		"INCLUDE_JQUERY" => "Y",
		"RESIZER_BASKET_PHOTO" => "13",
	),
	false
);?><?
	else:?>
<main class="container">
<p style="margin-top:30px; color:red">
Ошибка. Модуль "Киоск" не установлен.
</p>
</main><?
	endif?>
<?else:?>
<?$APPLICATION->IncludeComponent("bitrix:sale.order.ajax", "bitronic2_new", array(
	"PAY_FROM_ACCOUNT" => "Y",
	"COUNT_DELIVERY_TAX" => "N",
	"COUNT_DISCOUNT_4_ALL_QUANTITY" => "N",
	"ONLY_FULL_PAY_FROM_ACCOUNT" => "N",
	"ALLOW_AUTO_REGISTER" => "Y",
	"SEND_NEW_USER_NOTIFY" => "Y",
	"DELIVERY_NO_AJAX" => "N",
	"TEMPLATE_LOCATION" => "",
	"PROP_1" => array(
	),
	"PATH_TO_AUTH" => "/auth/",
	"PATH_TO_BASKET" => "/personal/cart/",
	"PATH_TO_PERSONAL" => "/personal/",
	"PATH_TO_PAYMENT" => "/personal/payment/",
	"PATH_TO_ORDER" => "/personal/order/?ID=#ID#",
	"URL_SHOP_RULES" => '/about/',
	"RESIZER_BASKET_PHOTO" => "13",
	"SET_TITLE" => "Y" ,
	"DELIVERY2PAY_SYSTEM" => "",
	"SHOW_ACCOUNT_NUMBER" => "Y"
	),
	false
);?>
<?endif?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>