<?
include_once "include_stop_statistic.php";

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

include_once "include_module.php";
include_once "include_options.php";

include_once($_SERVER["DOCUMENT_ROOT"].SITE_TEMPLATE_PATH."/lang/".LANGUAGE_ID."/header.php");

if(!isset($_POST['mobile']) && $_POST['mobile'] != 'y'){
    include $_SERVER["DOCUMENT_ROOT"].SITE_DIR."include_areas/sib/header/watched.php";
} else {
    include $_SERVER["DOCUMENT_ROOT"].SITE_DIR."include_areas/sib/header/watched_mobile.php";
}

