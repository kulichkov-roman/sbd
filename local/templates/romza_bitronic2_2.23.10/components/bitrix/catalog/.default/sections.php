<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Page\Asset;

if (defined("ERROR_404")) return;

//WOAH!!!! HACK HACK HACK =)
//Show items from the entire catalog if we have filter set from brands
$isDiscountSection = $_SERVER['PHP_SELF'] == '/discount/index.php' || (array_key_exists('CUSTOM_CACHE_KEY', $_REQUEST) && $_REQUEST['CUSTOM_CACHE_KEY'] === 'discount');
if ((array_key_exists('rz_all_elements', $_REQUEST) && $_REQUEST['rz_all_elements'] === 'y') || $isDiscountSection) {
	$arParams['SHOW_ALL_WO_SECTION'] = 'Y';
    Loc::loadMessages(__DIR__.'/section.php');
    if($isDiscountSection){
        include 'section_discount.php';
    } else {
        include 'section.php';
    }
	
	return;
} 
global $rz_b2_options;

$APPLICATION->SetPageProperty("showReviews", "Y");

$bSibCore = \Bitrix\Main\Loader::includeModule('sib.core');
if ($bSibCore) {
    $filterSubSections = [];
    $filterSubSections = \Sib\Core\Helper::getSubSectionFromParents(['IBLOCK_ID' => $arParams['IBLOCK_ID'], 'ACTIVE' => 'Y'], ['UF_IS_SHOW' => 1]);
}

// ON COMPOSITE ON THIS PAGE
$this->setFrameMode(true);
?>
<section class="main-block main-block_search main-block_catalog2 main-block_catalog2-2 main-block_catalog1">
    <h1 style="text-indent:-9999px;position:absolute;">Каталог</h1>
    <div class="search-box">
        <div class="filters-toggle js-filters"><?=GetMessage('FILTERS')?></div>
        <div class="category">
            <?$APPLICATION->IncludeComponent("bitrix:catalog.section.list", "sib_catalog_lvl0", array(
                "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
                "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                "COUNT_ELEMENTS" => $arParams["SECTION_COUNT_ELEMENTS"],
                "TOP_DEPTH" => '3',
                "SECTION_URL" => "",
                "CACHE_TYPE" => $arParams["CACHE_TYPE"],
                "CACHE_TIME" => $arParams["CACHE_TIME"],
                "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
                "RESIZER_SECTION_LVL0" => $arParams["RESIZER_SECTION_LVL0"],
                "ADD_SECTIONS_CHAIN" => "N",
                "VIEW_MODE" => "TEXT",
                "SHOW_ICONS" => $rz_b2_options["menu-show-icons"],
                "SHOW_PARENT_NAME" => "N",
                "FILTER_SUB_SECTIONS" => $filterSubSections
                ),
                $component
            );?>
        </div>
        <div class="result">
            <?$APPLICATION->IncludeComponent("bitrix:catalog.section.list", "sib_catalog_lvl0_blocks", array(
                "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
                "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                "COUNT_ELEMENTS" => $arParams["SECTION_COUNT_ELEMENTS"],
                "TOP_DEPTH" => '1',
                "SECTION_USER_FIELDS" => array(
                    0 => "UF_IMG_BLOCK_FOTO",
                    1 => "UF_CLASS_MENU",
                ),
                "SECTION_URL" => "",
                "CACHE_TYPE" => $arParams["CACHE_TYPE"],
                "CACHE_TIME" => $arParams["CACHE_TIME"],
                "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
                "RESIZER_SET" => "29",
                "ADD_SECTIONS_CHAIN" => "N",
                "VIEW_MODE" => "TEXT",
                "SHOW_ICONS" => $rz_b2_options["menu-show-icons"],
                "SHOW_PARENT_NAME" => "N",
                "FILTER_SUB_SECTIONS" => $filterSubSections
            ),
                $component
            );?>
        </div>
    </div>
</section>
