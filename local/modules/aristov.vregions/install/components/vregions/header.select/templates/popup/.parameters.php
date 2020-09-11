<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arTemplateParameters = array(
	"SHOW_POPUP_QUESTION"  =>  Array(
		"PARENT" => "ADDITIONAL_SETTINGS",
		"NAME" => GetMessage("VR_SHOW_POPUP_QUESTION"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N"
	),
	"POPUP_QUESTION_TITLE"  =>  Array(
		"PARENT" => "ADDITIONAL_SETTINGS",
		"NAME" => GetMessage("VR_POPUP_QUESTION_TITLE"),
		"TYPE" => "TEXT",
		"DEFAULT" => GetMessage("VR_POPUP_QUESTION_TITLE_DEFAULT")
	)
);
?>