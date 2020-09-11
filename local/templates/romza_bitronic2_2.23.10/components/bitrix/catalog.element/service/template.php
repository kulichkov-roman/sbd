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
include $_SERVER["DOCUMENT_ROOT"].SITE_TEMPLATE_PATH.'/include/debug_info_dynamic.php';
?>
	

	<h1><?=$arResult["NAME"]?></h1>
		
		<?if($arParams["DISPLAY_PICTURE"]!="N" && is_array($arResult["DETAIL_PICTURE"])):?>
		<div class="row images-row">
			<div itemscope itemtype="http://schema.org/ImageObject" class="img-container">
				<img itemprop="contentUrl" class="lazy" data-original="<?=CResizer2Resize::ResizeGD2($arResult["DETAIL_PICTURE"]["SRC"], $arParams["RESIZER_SET"])?>" src="<?=consVar::showLoaderWithTemplatePath()?>" alt="<?=$arResult["DETAIL_PICTURE"]["ALT"]?>" title="<?=$arResult["DETAIL_PICTURE"]["TITLE"]?>">
			</div>
		</div>
		<?endif;?>
		
		<p>
		<?if(strlen($arResult["DETAIL_TEXT"])>0):?>
			<?echo $arResult["DETAIL_TEXT"];?>
		<?else:?>
			<?echo $arResult["PREVIEW_TEXT"];?>
		<?endif?>
		</p>
<?
foreach ($arResult["DISPLAY_PROPERTIES"] as $pid => $arProperty):?>
		<p>
			<strong><?=$arProperty["NAME"]?>:</strong>&nbsp;<?
	if (is_array($arProperty["DISPLAY_VALUE"])):
			?><?= implode("&nbsp;/&nbsp;", $arProperty["DISPLAY_VALUE"]) ?><?
	else:
			?><?= $arProperty["DISPLAY_VALUE"] ?><?
	endif?>

		</p>
<?
endforeach;

$frame = $this->createFrame()->begin(CRZBitronic2Composite::insertCompositLoader());
if (!empty($arResult['MIN_PRICE'])): ?>
		<p><strong><?= GetMessage('BITRONIC2_SERVICE_PRICE') ?></strong>:&nbsp;<?=
			CRZBitronic2CatalogUtils::getElementPriceFormat(
				$arResult['MIN_PRICE']['CURRENCY'],
				$arResult['MIN_PRICE']['DISCOUNT_VALUE'],
				$arResult['MIN_PRICE']['PRINT_DISCOUNT_VALUE']
			)
		?></p>
<?
endif;
$frame->end();
?>


<?// echo '<pre>', htmlspecialcharsBx(var_export($arResult, 1)), '</pre>'; ?>
