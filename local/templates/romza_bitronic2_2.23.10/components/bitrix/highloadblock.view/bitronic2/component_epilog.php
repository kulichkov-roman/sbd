<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Page\Asset;

CJSCore::Init(array('rz_b2_bx_catalog_item'));
Asset::getInstance()->addJs(SITE_TEMPLATE_PATH."/js/custom-scripts/inits/sliders/initHorizontalCarousels.js");
Asset::getInstance()->addJs(SITE_TEMPLATE_PATH."/js/custom-scripts/inits/pages/initNewsItemPage.js");

$APPLICATION->SetTitle($arResult['row']['UF_NAME']);
$APPLICATION->AddChainItem($arResult['row']['UF_NAME']);
$title = $APPLICATION->GetDirProperty('title') ?: '';
if (!empty($title)) $title .= ' :: ';
$title .= $arResult['row']['UF_NAME'];
$APPLICATION->SetPageProperty('title', $title);
