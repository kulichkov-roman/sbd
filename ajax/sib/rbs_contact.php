<?
include_once "include_stop_statistic.php";
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

include_once "include_module.php";

\Yenisite\Core\Tools::encodeAjaxRequest($_REQUEST);
\Yenisite\Core\Tools::encodeAjaxRequest($_POST);

include_once($_SERVER["DOCUMENT_ROOT"].SITE_TEMPLATE_PATH."/lang/".LANGUAGE_ID."/header.php");

if(!isset($_SESSION['CNT_CONTACT'])){
    $_SESSION['CNT_CONTACT'] = 0;
} else {
    $_SESSION['CNT_CONTACT']++;
}

if($_SESSION['CNT_CONTACT'] > 2){
    if(\Bitrix\Main\Loader::includeModule('yenisite.bitronic2'))
        CRZBitronic2CatalogUtils::ShowMessage(Array("MESSAGE" => "Вы отправили 3 сообщения, пожалуйста подождите, для отправки следующего сообщения.", "TYPE" => "ERROR"));

    return;
}

//print_r($_SERVER['REMOTE_ADDR']);

$APPLICATION->IncludeComponent(
    "yenisite:feedback.add", 
    "rbs_sib", 
    array(
        "ACTIVE" => "Y",
        "AJAX_MODE" => "Y",
        "AJAX_OPTION_ADDITIONAL" => "",
        "AJAX_OPTION_HISTORY" => "N",
        "AJAX_OPTION_JUMP" => "N",
        "AJAX_OPTION_STYLE" => "Y",
        "AJAX_REDIRECT" => "Y",
        "CACHE_TIME" => "300",
        "CACHE_TYPE" => "A",
        "COLOR_SCHEME" => "green",
        "ELEMENT_ID" => "",
        "EMAIL" => "EMAIL",
        "EVENT_NAME" => "FEEDBACK",
        "IBLOCK" => "3",
        "IBLOCK_TYPE" => "bitronic2_feedback",
        "NAME" => "NAME",
        "NAME_FIELD" => "EMAIL",
        "PHONE" => "PHONE",
        "PRINT_FIELDS" => array(
            0 => "NAME",
            1 => "EMAIL",
            2 => "TEXT"
        ),
        "SECTION_CODE" => "",
        "SHOW_SECTIONS" => "N",
        "SUCCESS_TEXT" => "Спасибо! Ваше сообщение отправлено!",
        "TEXT_REQUIRED" => "N",
        "TEXT_SHOW" => "N",
        "TITLE" => "",
        "USE_CAPTCHA" => "N",
        "COMPONENT_TEMPLATE" => "rbs_sib"
    ),
    false
);