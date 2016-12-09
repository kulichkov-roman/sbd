<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use \Bitrix\Main\Page\Asset;
use \Bitrix\Main\Page\AssetLocation;
/** @var array $templateData */
/** @var @global CMain $APPLICATION */
if (isset($templateData['TEMPLATE_LIBRARY']) && !empty($templateData['TEMPLATE_LIBRARY']))
{
	$loadCurrency = false;
	if (!empty($templateData['CURRENCIES']))
		$loadCurrency = Bitrix\Main\Loader::includeModule('currency');
	CJSCore::Init($templateData['TEMPLATE_LIBRARY']);
	if ($loadCurrency)
	{
		$frame = new \Bitrix\Main\Page\FrameBuffered("element_epilog_currency");
		$frame->begin('');
	?>
	<script type="text/javascript">
		BX.Currency.setCurrencies(<? echo $templateData['CURRENCIES']; ?>);
	</script>
<?
		$frame->end();
	}
}

if($arParams['DETAIL_YM_API_USE'] == 'Y')
{
	$this->__parent->arResult["PROPERTIES"]["TURBO_YANDEX_LINK"]["VALUE"] = $arResult["PROPERTIES"]["TURBO_YANDEX_LINK"]["VALUE"];
}

//Needs for iblock.vote to not break composite
IncludeAJAX();

$frame = new \Bitrix\Main\Page\FrameBuffered("element_epilog_JCCatalogItem");
$frame->begin('');?>
<?
// Set active offer from $_GET parameter
do {
	if (empty($templateData['OFFERS_KEYS'])) break;
	if (empty($arParams['OFFER_VAR_NAME']))  break;

	$pid = $_GET[$arParams['OFFER_VAR_NAME']];
	if (empty($pid)) break;
	if (!array_key_exists($pid, $templateData['OFFERS_KEYS'])) break;

	$keySelected = $templateData['OFFERS_KEYS'][$pid];

} while (0);
if (empty($_SESSION['RZ_DETAIL_JS_FILE']) && isset($keySelected)) {
	$templateData['arJSParams']['OFFER_SELECTED'] = $keySelected;
}
$jsString = 'var '. $templateData['strObName'] .' = new JCCatalogItem('. CUtil::PhpToJSObject($templateData['arJSParams'], false, true) .');';
if (!empty($_SESSION['RZ_DETAIL_JS_FILE'])) {
	fwrite($_SESSION['RZ_DETAIL_JS_FILE'], $jsString);
	fclose($_SESSION['RZ_DETAIL_JS_FILE']);
	unset($_SESSION['RZ_DETAIL_JS_FILE']);
}
?>
<?if (!empty($templateData['jsFile']) && file_exists($templateData['jsFullPath']) && !\Yenisite\Core\Tools::isAjax()):?>
<script type="text/javascript" src="<?=$templateData['jsFile']?>?<?echo time()?>"></script>
	<?if (isset($keySelected)):?>
		<script><?=$templateData['strObName']?>.offerNum = <?=$keySelected?>;</script>
	<?endif?>
<?else:?>
<script type="text/javascript">
	<?=$jsString?>
</script>
<?
endif;
$frame->end();


// update view counter
if (isset($templateData['strObName']))
{
	$frame = new \Bitrix\Main\Page\FrameBuffered("element_epilog_viewCounter");
	$frame->begin('');
	?><script type="text/javascript">
		<?=$templateData['strObName']?>.allowViewedCount(true);
	</script><?
	$frame->end();
}