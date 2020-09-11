<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/* 
$arResult['USER'] = CUser::GetByID($USER->GetID())->Fetch();

$userName = "{$arResult['USER']['NAME']} {$arResult['USER']['LAST_NAME']}";
$arResult['USER']['PRINT_NAME'] = !empty($userName) ? $userName : $arResult['USER']['LOGIN'];
\Bitrix\Main\Loader::includeModule('yenisite.core');
$cfile = CFile::GetPath($arResult['USER']['PERSONAL_PHOTO']);

if (empty($cfile))
	$cfile = SITE_TEMPLATE_PATH . $arParams['DEFAULT_IMG_SRC'];

$arResult['USER']['PERSONAL_PHOTO'] = \Yenisite\Core\Resize::GetResizedImg($cfile, array('WIDTH' => 74, 'HEIGHT' => 74)); */