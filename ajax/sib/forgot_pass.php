<?
include_once "include_stop_statistic.php";

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

include_once "include_module.php";

//\Yenisite\Core\Tools::encodeAjaxRequest($_REQUEST);
//\Yenisite\Core\Tools::encodeAjaxRequest($_POST);


global $USER;

$arResult = $USER->SendPassword('', $_POST['forgot_email']);

if($arResult["TYPE"] == "OK"){
    $response = (object)['TYPE' => 'OK', 'MESSAGE' => 'Ссылка отправлена на указанную вами почту, перейдите по ней и задайте новый пароль.'];
} else {
    $response = (object)['TYPE' => 'ERROR', 'MESSAGE' => 'Введенный email не найден.'];
}

echo json_encode($response);