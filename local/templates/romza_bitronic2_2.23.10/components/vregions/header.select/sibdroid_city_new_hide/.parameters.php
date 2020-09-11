<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$colsCount = Array(
	'1' => 1,
	'2' => 2,
	'3' => 3,
	'4' => 4,
	'5' => 5,
	'6' => 6,
);

$arTemplateParameters = array(
	"SHOW_POPUP_QUESTION"       => Array(
		"PARENT"  => "ADDITIONAL_SETTINGS",
		"NAME"    => GetMessage("VR_SHOW_POPUP_QUESTION"),
		"TYPE"    => "CHECKBOX",
		"DEFAULT" => "Y"
	),
	"POPUP_QUESTION_TITLE"      => Array(
		"PARENT"  => "ADDITIONAL_SETTINGS",
		"NAME"    => GetMessage("VR_POPUP_QUESTION_TITLE"),
		"TYPE"    => "TEXT",
		"DEFAULT" => GetMessage("VR_POPUP_QUESTION_TITLE_DEFAULT")
	),
	"COLS_COUNT"                => Array(
		"PARENT"  => "ADDITIONAL_SETTINGS",
		"NAME"    => GetMessage("VR_COLS_COUNT"),
		"TYPE"    => "LIST",
		"VALUES"  => $colsCount,
		"DEFAULT" => 3
	),
	"SHOW_SEARCH_FORM"          => Array(
		"PARENT"  => "ADDITIONAL_SETTINGS",
		"NAME"    => GetMessage("VR_SHOW_SEARCH_FORM"),
		"TYPE"    => "CHECKBOX",
		"DEFAULT" => "N"
	),
	"STRING_BEFORE_REGION_LINK" => Array(
		"PARENT"  => "ADDITIONAL_SETTINGS",
		"NAME"    => GetMessage("VR_STRING_BEFORE_REGION_LINK"),
		"TYPE"    => "TEXT",
		"DEFAULT" => GetMessage("VR_STRING_BEFORE_REGION_LINK_DEFAULT")
	),
	"ALLOW_OBLAST_FILTER"       => Array(
		"PARENT"  => "ADDITIONAL_SETTINGS",
		"NAME"    => GetMessage("VR_ALLOW_OBLAST_FILTER"),
		"TYPE"    => "CHECKBOX",
		"DEFAULT" => "N"
	),
	"FIXED"                     => Array(
		"PARENT"  => "ADDITIONAL_SETTINGS",
		"NAME"    => GetMessage("VR_FIXED"),
		"TYPE"    => "CHECKBOX",
		"DEFAULT" => "N"
	),
	"SHOW_ANOTHER_REGION_BTN"   => Array(
		"PARENT"  => "ADDITIONAL_SETTINGS",
		"NAME"    => GetMessage("VR_SHOW_ANOTHER_REGION_BTN"),
		"TYPE"    => "CHECKBOX",
		"DEFAULT" => "N"
	),
);