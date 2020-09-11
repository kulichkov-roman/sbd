<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Localization\Loc;

$APPLICATION->AddChainItem(Loc::getMessage("SPS_CHAIN_PRIVATE"));
?>
<h2 class="account-page-title"><? $APPLICATION->ShowTitle(false) ?></h2>
<? include $_SERVER['DOCUMENT_ROOT'] . SITE_DIR . 'personal/left_menu.php' ?>
<div class="personal-account-main personal-account-main_data">
    <?$APPLICATION->IncludeComponent(
        "bitrix:main.profile",
        "sib_bitronic2",
        Array(
            "SET_TITLE" => "N",
            "AJAX_OPTION_JUMP" => "Y",
            "AJAX_OPTION_STYLE" => "Y",
            "AJAX_OPTION_HISTORY" => "N",
            "AJAX_MODE" => $arParams['AJAX_MODE_PRIVATE'],
            "SEND_INFO" => $arParams["SEND_INFO_PRIVATE"],
            "CHECK_RIGHTS" => $arParams['CHECK_RIGHTS_PRIVATE'],
        ),
        $component
    );?>
</div>