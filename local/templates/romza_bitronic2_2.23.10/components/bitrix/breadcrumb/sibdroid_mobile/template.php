<?
use \Yenisite\Core\Catalog;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
CModule::IncludeModule('yenisite.core');
//delayed function must return a string
if (empty($arResult))
	return "";
global $rz_b2_options, $rz_current_sectionID;
$num_items = count($arResult);
$index = $num_items - 1;
$title = htmlspecialcharsex($arResult[$index - 1]["TITLE"]);

$arResult[$index]["LINK"] = isset($arResult[$index - 1]["LINK"]) ? $arResult[$index - 1]["LINK"] : SITE_DIR;

$strReturn = '<ul class="bread-crumbs bread-crumbs_mobile" itemscope itemtype="http://schema.org/BreadcrumbList">';
$strReturn .= '<li class="bread-crambs__item" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
$strReturn .= '<a href="' . $arResult[$index]["LINK"] . '" class="bread-crambs__link"><meta itemprop="name" content="' . SITE_SERVER_NAME . '" />Назад</a>';
$strReturn .= '<meta itemprop="position" content="1" /></li> ';

$strReturn .= '<li class="bread-crambs__item bread-crumb__last-item" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
$strReturn .= '<a href="' . $arResult[$index]["LINK"] . '">';
$strReturn .=  $title;
$strReturn .= '<meta itemprop="position" content="' . ($index + 2) . '" />';
$strReturn .= '</a>';
$strReturn .= '</li> ';


$strReturn .= '</ul>' . $strBacknav;

return $strReturn;
