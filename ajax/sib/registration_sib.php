<?
include_once "include_stop_statistic.php";

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

include_once "include_module.php";

\Yenisite\Core\Tools::encodeAjaxRequest($_REQUEST);
\Yenisite\Core\Tools::encodeAjaxRequest($_POST);

/*
include_once($_SERVER["DOCUMENT_ROOT"].SITE_TEMPLATE_PATH."/lang/".LANGUAGE_ID."/header.php");
include $_SERVER["DOCUMENT_ROOT"].SITE_DIR."include_areas/sib/header/user_reg.php";
*/

global $USER;
$arResult = $USER->Register($_REQUEST['email'], $_REQUEST['name'], "", $_REQUEST['pass'], $_REQUEST['repass'], $_REQUEST['email']);
$response = $arResult;
if((int)$response['ID'] > 0 && $_REQUEST['is_subs'] == 'on' && CModule::IncludeModule('subscribe')){
    
    $arFields = Array(
        "USER_ID" => $response['ID'] ,
        "FORMAT" => "html",
        "EMAIL" => $_REQUEST['email'],
        "ACTIVE" => "Y",
        "RUB_ID" => 1
    );

    $subscr = new CSubscription;
    $ID = $subscr->Add($arFields);
    if ($ID > 0){
        CSubscription::Authorize($ID);
        //$response['STATUS'] = 'SUCCESS';
    }
}
echo json_encode($response);
