<?
include_once "include_stop_statistic.php";

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

include_once "include_module.php";
include_once "include_options.php";

include_once($_SERVER["DOCUMENT_ROOT"].SITE_TEMPLATE_PATH."/lang/".LANGUAGE_ID."/header.php");
include $_SERVER["DOCUMENT_ROOT"].SITE_DIR."include_areas/sib/header/compare.php";
include $_SERVER["DOCUMENT_ROOT"].SITE_DIR."include_areas/sib/header/compare_mobile.php";