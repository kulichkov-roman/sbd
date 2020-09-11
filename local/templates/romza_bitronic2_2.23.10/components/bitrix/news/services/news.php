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
$this->setFrameMode(true);

global $rz_b2_options;

$asset = Bitrix\Main\Page\Asset::getInstance();
$asset->addJs(SITE_TEMPLATE_PATH . '/js/3rd-party-libs/isotope.pkgd.min.js');
$asset->addJs(SITE_TEMPLATE_PATH . '/js/custom-scripts/inits/pages/initServicesPage.js');

?>



<section class="main-block main-block_sale service_rbs">
    <h1 class="main-title"><? $APPLICATION->ShowTitle('h1') ?></h1>
        <? $APPLICATION->IncludeComponent(
            "bitrix:news.list",
            "",
            Array(
                "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
                "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                "NEWS_COUNT" => $arParams['ACTIONS_USE'] ? $arParams["OUNT_ELEMENT_ACTIONS"] : $arParams["NEWS_COUNT"],
                "SORT_BY1" => $arParams["SORT_BY1"],
                "SORT_ORDER1" => $arParams["SORT_ORDER1"],
                "SORT_BY2" => $arParams["SORT_BY2"],
                "SORT_ORDER2" => $arParams["SORT_ORDER2"],
                "FIELD_CODE" => $arParams["LIST_FIELD_CODE"],
                "PROPERTY_CODE" => $arParams["DETAIL_PROPERTY_CODE"],
                "DETAIL_URL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["detail"],
                "SECTION_URL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["section"],
                "IBLOCK_URL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["news"],
                "DISPLAY_PANEL" => $arParams["DISPLAY_PANEL"],
                "SET_TITLE" => $arParams["SET_TITLE"],
                "SET_STATUS_404" => $arParams["SET_STATUS_404"],
                "INCLUDE_IBLOCK_INTO_CHAIN" => $arParams["INCLUDE_IBLOCK_INTO_CHAIN"],
                "CACHE_TYPE" => $arParams["CACHE_TYPE"],
                "CACHE_TIME" => $arParams["CACHE_TIME"],
                "CACHE_FILTER" => $arParams["CACHE_FILTER"],
                "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
                "DISPLAY_TOP_PAGER" => $arParams["DISPLAY_TOP_PAGER"],
                "DISPLAY_BOTTOM_PAGER" => $arParams["DISPLAY_BOTTOM_PAGER"],
                "PAGER_TITLE" => $arParams["PAGER_TITLE"],
                "PAGER_TEMPLATE" => $arParams["PAGER_TEMPLATE"],
                "PAGER_SHOW_ALWAYS" => $arParams['PAGER_SHOW_ALWAYS'],
                "PAGER_DESC_NUMBERING" => $arParams["PAGER_DESC_NUMBERING"],
                "PAGER_DESC_NUMBERING_CACHE_TIME" => $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"],
                "PAGER_SHOW_ALL" => $arParams["PAGER_SHOW_ALL"],
                "DISPLAY_DATE" => $arParams["DISPLAY_DATE"],
                "DISPLAY_NAME" => "Y",
                "DISPLAY_PICTURE" => $arParams["DISPLAY_PICTURE"],
                "DISPLAY_PREVIEW_TEXT" => $arParams["DISPLAY_PREVIEW_TEXT"],
                "PREVIEW_TRUNCATE_LEN" => $arParams["PREVIEW_TRUNCATE_LEN"],
                "ACTIVE_DATE_FORMAT" => $arParams["LIST_ACTIVE_DATE_FORMAT"],
                "USE_PERMISSIONS" => $arParams["USE_PERMISSIONS"],
                "GROUP_PERMISSIONS" => $arParams["GROUP_PERMISSIONS"],
                "FILTER_NAME" => $arParams["FILTER_NAME"],
                "HIDE_LINK_WHEN_NO_DETAIL" => $arParams["HIDE_LINK_WHEN_NO_DETAIL"],
                "CHECK_DATES" => $arParams["CHECK_DATES"],

                "RESIZER_NEWS_LIST" => $arParams["RESIZER_NEWS_LIST"],
                'ACTIONS_USE' => $arParams['ACTIONS_USE'] ?: false,
            ),
            $component
        ); ?>
</section>

<?
	if ('N' !== $rz_b2_options['block_404-viewed']
	||  'N' !== $rz_b2_options['block_404-bestseller']
	||  'N' !== $rz_b2_options['block_404-recommend']
	) {
		CJSCore::Init(array('rz_b2_bx_catalog_item'));
		$asset->addJs(SITE_TEMPLATE_PATH . '/js/custom-scripts/inits/sliders/initHorizontalCarousels.js');

		$arNewsParams = $arParams;
		$arParams = array();
		if (Bitrix\Main\Loader::includeModule('yenisite.core')) {
			$arParams = Yenisite\Core\Ajax::getParams('bitrix:catalog', false, CRZBitronic2CatalogUtils::getCatalogPathForUpdate());
		}
		// @var array $arPrepareParams
		include $_SERVER['DOCUMENT_ROOT'] . SITE_TEMPLATE_PATH . '/components/bitrix/catalog/.default/include/prepare_params_element.php';

		$frame = $this->createFrame()->begin('');
		if ('N' !== $rz_b2_options['block_404-viewed']) {
			include $_SERVER['DOCUMENT_ROOT'] . SITE_TEMPLATE_PATH . '/components/bitrix/catalog/.default/include/viewed_products.php';
		}
		if ('N' !== $rz_b2_options['block_404-bestseller']) {
			$arPrepareParams['HEADER_TEXT'] = $arParams['BIGDATA_BESTSELL_TITLE'] ?: GetMessage('LIDERS_SALES');
			$arPrepareParams['RCM_TYPE'] = 'bestsell';
			include $_SERVER['DOCUMENT_ROOT'] . SITE_TEMPLATE_PATH . '/components/bitrix/catalog/.default/include/bigdata.php';
		}
		if ('N' !== $rz_b2_options['block_404-recommend']) {
			$arPrepareParams['HEADER_TEXT'] = $arParams['BIGDATA_PERSONAL_TITLE'] ?: GetMessage('RECOMENDATIONS');
			$arPrepareParams['RCM_TYPE'] = 'personal';
			include $_SERVER['DOCUMENT_ROOT'] . SITE_TEMPLATE_PATH . '/components/bitrix/catalog/.default/include/bigdata.php';
		}
		$frame->end();
		$arParams = $arNewsParams;
	}
?>
