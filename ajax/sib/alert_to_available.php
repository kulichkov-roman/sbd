<?
include_once "include_stop_statistic.php";

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

include_once "include_module.php";
include_once "include_options.php";

include_once($_SERVER["DOCUMENT_ROOT"].SITE_TEMPLATE_PATH."/lang/".LANGUAGE_ID."/ajax.php");

if($_SERVER["REQUEST_METHOD"] == "POST" && !defined('BX_UTF')) 
{
	\Yenisite\Core\Tools::encodeAjaxRequest($_REQUEST);
	\Yenisite\Core\Tools::encodeAjaxRequest($_POST);
	CRZBitronic2Handlers::$encodeRegisterHandlers = true;
}
$arProps = array();
if (!empty($_REQUEST['PROPS'])) {
	$arProps = \Yenisite\Core\Tools::GetDecodedArParams($_REQUEST['PROPS']);
}

if(!function_exists('generateRandomString')){
    function generateRandomString($length = 5) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}


if (\Bitrix\Main\Loader::includeModule('yenisite.oneclick') && \Bitrix\Main\Loader::includeModule('catalog') && \Bitrix\Main\Loader::includeModule('iblock') && (int)$_REQUEST["id"] > 0) {

    global $USER;
    if(!$USER->IsAuthorized()){
        $_POST['FIELDS']['EMAIL'] = $_REQUEST['FIELDS']['EMAIL'] = generateRandomString(5) . '_' . $_POST['FIELDS']['EMAIL'];
    } else {
        $_POST['FIELDS']['EMAIL'] = $USER->GetEmail() ?:  $_POST['FIELDS']['EMAIL'];
        $rs = CIblockElement::GetList([], ['IBLOCK_ID' => 13, '=PROPERTY_PRODUCT' => (int)$_REQUEST["id"], '=PROPERTY_EMAIL' => $_POST['FIELDS']['EMAIL']]);
        if($rs->SelectedRowsCount() <= 0){
            $el = new CIBlockElement;
            $arLoadProductArray = [
                'IBLOCK_ID' => 13,
                'PROPERTY_VALUES' => [
                    '400' => (int)$_REQUEST["id"],
                    '401' =>  $_POST['FIELDS']['EMAIL']
                ],
                'NAME' =>  $_POST['FIELDS']['EMAIL']
            ];
            $el->Add($arLoadProductArray);
        }
        /* if($el->Add($arLoadProductArray)){
            echo json_encode((object)['TYPE' => 'OK', 'MSG' => 'Спасибо за заявку! При поступлении товара на склад мы сообщим вам на оставленный email.']);
        } else {
            echo json_encode((object)['TYPE' => 'ERROR', 'MSG' => $el->LAST_ERROR]);
        } */
    }

    CCatalogProduct::Update($_REQUEST["id"], [
        'CAN_BUY_ZERO' => 'Y'
    ]);

    global $rz_b2_options;
    $rz_b2_options["captcha-quick-buy"] = 'N';
    $APPLICATION->IncludeComponent(
        "yenisite:oneclick.buy",
        $_REQUEST["template_name"],
        array(
            "COMPONENT_TEMPLATE" => $_REQUEST["template_name"],
            "IBLOCK_TYPE" => "catalog",
            "IBLOCK_ID" => "6",
            "IBLOCK_ELEMENT_ID" => $_REQUEST["id"],
            "FORM_ID" => $_REQUEST["FORM_ID"],
            "PERSON_TYPE_ID" => "1",
            "SHOW_FIELDS" => array(
                0 => "FIO",
                1 => "EMAIL",
                2 => "PHONE",
            ),
            "REQ_FIELDS" => array(
                0 => "FIO",
                1 => "EMAIL",
                2 => "PHONE",
            ),
            "ALLOW_AUTO_REGISTER" => "Y",
            "MESSAGE_OK" => "Ваша заявка принята, её номер - #ID#. Менеджер свяжется с вами в ближайшее время. Спасибо, что выбрали нас!",
            "PAY_SYSTEM_ID" => "11",
            "DELIVERY_ID" => "412",
            "AS_EMAIL" => "0",
            "AS_NAME" => "0",
            "FIELD_CLASS" => "textinput",
            "FIELD_PLACEHOLDER" => "Y",
            "FIELD_QUANTITY" => "N",
            "SEND_REGISTER_EMAIL" => "N",
            "EMPTY" => $arParams["EMPTY"],
            "USE_CAPTCHA" => $rz_b2_options["captcha-quick-buy"],
            "USE_CAPTCHA_FORCE" => $rz_b2_options["captcha-quick-buy"],
            //"USER_REGISTER_EVENT_NAME" => "[SALE_NEW_ORDER]",
            "OFFER_PROPS" => $arProps,
            "COMMENTS" => "Заявка на товар",
            "COMPOSITE_FRAME_MODE" => "A",
            "COMPOSITE_FRAME_TYPE" => "AUTO"
        ),
        false
    );

    CCatalogProduct::Update($_REQUEST["id"], [
        'CAN_BUY_ZERO' => 'D'
    ]);
}