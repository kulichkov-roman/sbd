<?
use \Yenisite\Core\Catalog;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
CModule::IncludeModule('yenisite.core');
//delayed function must return a string
if (empty($arResult))
	return "";
global $rz_b2_options, $rz_current_sectionID;
$bShowBacknav = ($rz_b2_options['backnav_enabled'] == 'Y') && (int)$rz_current_sectionID > 0;
$strReturn = '<ul class="bread-crumbs bread-crumbs_desktop" itemscope itemtype="http://schema.org/BreadcrumbList">';
$strReturn .= '<li class="bread-crambs__item" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
$strReturn .= '<a href="' . SITE_DIR . '" class="bread-crambs__link"><meta itemprop="name" content="' . SITE_SERVER_NAME . '" />Главная</a>';
$strReturn .= '<meta itemprop="position" content="1" /></li> ';
$num_items = count($arResult);
$strBacknav = '';
for ($index = 0, $itemSize = $num_items; $index < $itemSize; $index++) {
	$arSiblings = array();
	if ($bShowBacknav) {
		$arSiblings = Catalog::getChainSiblings($rz_current_sectionID, $arResult[$index]["LINK"]);
	}
	$title = htmlspecialcharsex($arResult[$index]["TITLE"]);

	if ($arResult[$index]["LINK"] <> "" && $index != $itemSize - 1) {
		$strReturn .= '<li class="bread-crambs__item" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
		$strReturn .= '<a class="bread-crambs__link" itemprop="item" href="' . $arResult[$index]["LINK"] . '" title="' . $title . '"';
		/*if ($arSiblings) {
			$strReturn .= ' data-popup="^.breadcrumbs-content>.backnav.backnav_'. $index .'"';
			$strBacknav .= '<ul class="backnav backnav_' . $index . '">';
			$backnavIndex = 0;
			$j = 0;
			foreach ($arSiblings as $arSibling) {
				if($arSibling['LINK'] == $arResult[$index]['LINK']) {
					$backnavIndex = $j;
				}
				$strBacknav .= '<li><a href="' . $arSibling["LINK"] . '">' . $arSibling["NAME"] . '<i class="flaticon-arrow492 arrow arrow-up"></i></a></li>';
				++$j;
			}
			$strBacknav .= '</ul>';
			$strReturn .=' data-backnav="' . $backnavIndex . '"';
		}*/
		$strReturn .= '>' . $title . '</a>';
		$strReturn .= '<meta itemprop="position" content="' . ($index + 2) . '" />';
		$strReturn .= '</li> ';
	} else {
		$strReturn .= '<li class="bread-crambs__item">' . $title . '</li>';
	}
}

$strReturn .= '</ul>' . $strBacknav;

return $strReturn;
