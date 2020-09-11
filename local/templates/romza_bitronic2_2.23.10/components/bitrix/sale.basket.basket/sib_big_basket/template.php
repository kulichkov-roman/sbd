<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Page\Asset;
use \Bitronic2\Mobile;
use Bitrix\Sale\DiscountCouponsManager;

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
/** @var CBitrixBasketComponent $component */
$asset = Asset::getInstance();
$asset->addJs(SITE_TEMPLATE_PATH."/js/custom-scripts/libs/UmTabs.js");
$asset->addJs(SITE_TEMPLATE_PATH."/js/custom-scripts/inits/pages/initBigBasketPage.js");

global $rz_b2_options;
$rz_b2_options['product-hover-effect'] = $rz_b2_options['product-hover-effect'];

if ($rz_b2_options['product-hover-effect'] == 'detailed-expand') {
	$asset->addJs(SITE_TEMPLATE_PATH . "/js/custom-scripts/inits/initCatalogHover.js");
}

$curPage = $APPLICATION->GetCurPage().'?'.$arParams["ACTION_VARIABLE"].'=';
$arUrls = array(
	"delete" => $curPage."delete&id=#ID#",
	"delay" => $curPage."delay&id=#ID#",
	"add" => $curPage."add&id=#ID#",
);
unset($curPage);

if (!empty($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], '/personal/') === false) {
	$_SESSION['RZ_B2_BASKET_PREV_PAGE'] = $_SERVER['HTTP_REFERER'];
}

$arBasketJSParams = array(
	'BASKET_CURRENCY' => CSaleLang::GetLangCurrency(SITE_ID),
	'SALE_DELETE' => GetMessage("BITRONIC2_SALE_DELETE"),
	'SALE_DELAY' => GetMessage("BITRONIC2_SALE_DELAY"),
	'SALE_TYPE' => GetMessage("BITRONIC2_SALE_TYPE"),
	'TEMPLATE_FOLDER' => $templateFolder,
	'DELETE_URL' => $arUrls["delete"],
	'DELAY_URL' => $arUrls["delay"],
	'ADD_URL' => $arUrls["add"],
	'SELF_URL' => $APPLICATION->GetCurPage(true)
);
?>
<script type="text/javascript">
	var basketJSParams = <?=CUtil::PhpToJSObject($arBasketJSParams)?>;
	BX.message({basket_sku_not_available: "<?=GetMessage('BITRONIC2_SKU_NOT_AVAILABLE')?>"});
</script>

<?if (!empty($arResult["GRID"]["ROWS"])):?>
<div data-page="big-basket-page">
	<form method="post" name="basket_form" id="basket_form">
		<!-- <section class="main-block_cart">  -->
		<?
			$APPLICATION->AddHeadScript($templateFolder."/script.js");
			
			if ($_POST['rz_ajax_no_header'] === 'y'){
				$APPLICATION->RestartBuffer();
			}

			if (strlen($arResult["ERROR_MESSAGE"]) <= 0)
			{
				if (!empty($arResult["WARNING_MESSAGE"]) && is_array($arResult["WARNING_MESSAGE"]))
				{
					CRZBitronic2CatalogUtils::ShowMessage(array("MESSAGE"=>$arResult["WARNING_MESSAGE"], "TYPE"=>"ERROR"));
				}
				
				$arResult['COLUMNS'] = array();
				$bShowName = $bShowProps = $bShowDelay = $bShowDelete = false;
				
				if (is_array($arResult['GRID']['HEADERS'])) {
					foreach ($arResult['GRID']['HEADERS'] as $id => $arHeader) {
						switch ($arHeader['id']) {
							case 'NAME':  $arResult['COLUMNS']['NAME'] = $bShowName  = true; break;
							case 'PROPS': $arResult['COLUMNS']['NAME'] = $bShowProps = true; break;
							case 'QUANTITY': $arResult['COLUMNS']['QUANTITY'] = true; break;
							case 'PRICE':    $arResult['COLUMNS']['PRICE']    = true; break;
							case 'DISCOUNT': $arResult['COLUMNS']['DISCOUNT'] = true; break;
							case 'SUM':      $arResult['COLUMNS']['SUM']      = true; break;
							case 'DELAY':  $arResult['COLUMNS']['ACTIONS'] = $bShowDelay  = true; break;
							case 'DELETE': $arResult['COLUMNS']['ACTIONS'] = $bShowDelete = true; break;
							default: break;
						};
					}
				}
				
				$normalCount    = count($arResult["ITEMS"]["AnDelCanBuy"]);
				$delayCount     = count($arResult["ITEMS"]["DelDelCanBuy"]);
				$subscribeCount = count($arResult["ITEMS"]["ProdSubscribe"]);
				$naCount        = count($arResult["ITEMS"]["nAnCanBuy"]);
				
				if ($naCount > 0) {
					foreach ($arResult["ITEMS"]["nAnCanBuy"] as $key => $arItem) {
						if ($arItem['DELAY'] == "Y") {
							$delayCount++;
							$naCount--;
							continue;
						}
					}
				}
				
				$normalShow    = ($normalCount    > 0 || true);
				$delayShow     = ($delayCount     > 0);
				$subscribeShow = ($subscribeCount > 0);
				$naShow        = ($naCount        > 0);
				
				$delayActive     = ($_SESSION['RZ_BASKET_TAB'] == 'delay'     && $delayShow)     ? ' active' : '';
				$subscribeActive = ($_SESSION['RZ_BASKET_TAB'] == 'subscribe' && $subscribeShow) ? ' active' : '';
				$naActive        = ($_SESSION['RZ_BASKET_TAB'] == 'na'        && $naShow)        ? ' active' : '';
				$normalActive    = ($_SESSION['RZ_BASKET_TAB'] == 'items'
				|| !($delayActive || $subscribeActive || $naActive))        ? ' active' : '';
				
				$noJSpage = CRZBitronic2CatalogUtils::noJsPage();
				$bShowStore = $arResult['USE_STORE'];
				$catalogParams = $arResult['CATALOG_PARAMS'];
				?>
				
				<div class="cart-head">
					<h3 class="main-title"><?$APPLICATION->ShowTitle()?></h3>
					<a class="cart-head__link" href="/"><?=GetMessage('BITRONIC2_SALE_CONTINUE')?></a>
				</div>

				<section class="main-block_cart" id="basket_form_container">
				<div class="cart" >
					
		<?
				if ($normalShow)     include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/basket_items.php");
				if ($delayShow)      include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/basket_items_delayed.php");
				/* TODO
				if ($subscribedShow) include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/basket_items_subscribed.php");*/
				?>
				</section>
				<input type="hidden" id="column_headers" value="<?=CUtil::JSEscape(implode(array('NAME','DISCOUNT','PROPS','DELETE','DELAY','PRICE','QUANTITY','SUM'), ","))?>" />
				<input type="hidden" id="offers_props" value="<?=CUtil::JSEscape(implode($arParams["OFFERS_PROPS"], ","))?>" />
				<input type="hidden" id="action_var" value="<?=CUtil::JSEscape($arParams["ACTION_VARIABLE"])?>" />
				<input type="hidden" id="quantity_float" value="<?=$arParams["QUANTITY_FLOAT"]?>" />
				<input type="hidden" id="count_discount_4_all_quantity" value="<?=($arParams["COUNT_DISCOUNT_4_ALL_QUANTITY"] == "Y") ? "Y" : "N"?>" />
				<input type="hidden" id="price_vat_show_value" value="<?=($arParams["PRICE_VAT_SHOW_VALUE"] == "Y") ? "Y" : "N"?>" />
				<input type="hidden" id="hide_coupon" value="<?=($arParams["HIDE_COUPON"] == "Y") ? "Y" : "N"?>" />
				<input type="hidden" id="use_prepayment" value="<?=($arParams["USE_PREPAYMENT"] == "Y") ? "Y" : "N"?>" />
		<?
			}
			if ($_POST['rz_ajax_no_header'] === 'y') die();
			?>

		<!-- </section> -->
	</form>
</div>
<?else:?>
	<?include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/basket_items_empty.php")?>
<?endif?>
<?
// echo "<pre style='text-align:left;'>";var_export($arResult);echo "</pre>";
