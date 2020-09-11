<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arTemplateParameters = array(
	"ALLOW_NEW_PROFILE" => array(
		"NAME"=>GetMessage("T_ALLOW_NEW_PROFILE"),
		"TYPE" => "CHECKBOX",
		"DEFAULT"=>"Y",
		"PARENT" => "BASE",
	),
	"SHOW_PAYMENT_SERVICES_NAMES" => array(
		"NAME" => GetMessage("T_PAYMENT_SERVICES_NAMES"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" =>"Y",
		"PARENT" => "BASE",
	),
	"SHOW_STORES_IMAGES" => array(
		"NAME" => GetMessage("T_SHOW_STORES_IMAGES"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" =>"N",
		"PARENT" => "BASE",
	),
	
	"URL_SHOP_RULES"=>array(
		"NAME" => GetMessage("URL_SHOP_RULES"),
		"TYPE" => "STRING",
		"DEFAULT" => '={SITE_DIR."about/"}',	
	),
	"PATH_TO_SETTINGS" => array(
		"NAME" => GetMessage("PATH_TO_SETTINGS"),
		"TYPE" => "STRING",
		"DEFAULT" => '={SITE_DIR."personal/profile/"}',
	),
);