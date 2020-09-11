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
$templateData = $arResult;
?>
<?
foreach ($arResult["DISPLAY_PROPERTIES"] as $pid => $arProperty):?>
		
<?
endforeach ?>
<section class="main-block main-block_service main-block_service-open">
		<div class="cards-wrap cards-wrap_service">
			<div class="card card_open">
				<div class="card__top">
					<h1 class="main-title"><?=$arResult["NAME"]?></h1> 
					<div class="card__price">
                        <p class=""><?= $arProperty["DISPLAY_VALUE"] ?></p>
                    </div>
				</div>
				<?if(!empty($arResult["DETAIL_PICTURE"]["SRC"])):?>
				<div class="card__img">
                    <div itemscope itemtype="http://schema.org/ImageObject" class="img-container">
	                    <?if($arParams["DISPLAY_PICTURE"]!="N" && is_array($arResult["DETAIL_PICTURE"])):?>
	                   	<img itemprop="contentUrl" class="lazy" data-original="<?=CResizer2Resize::ResizeGD2($arResult["DETAIL_PICTURE"]["SRC"], $arParams["RESIZER_NEWS_DETAIL"])?>" src="<?=ConsVar::showLoaderWithTemplatePath()?>" alt="<?=$arResult["DETAIL_PICTURE"]["ALT"]?>" title="<?=$arResult["DETAIL_PICTURE"]["TITLE"]?>">
	                   	<?endif;?>
                   </div>
               </div>
				<?endif;?>
               <div class="card__cnt">
               		<div class="card__text">
               			<?if(strlen($arResult["DETAIL_TEXT"])>0):?>
							<?echo $arResult["DETAIL_TEXT"];?>
						<?else:?>
							<?echo $arResult["PREVIEW_TEXT"];?>
						<?endif?>
               		</div>
               </div>
			</div>
		
		
		
		<div>
		
</section>
<p>
			<strong><?=$arProperty["NAME"]?>:</strong>&nbsp;<?
	if (is_array($arProperty["DISPLAY_VALUE"])):
			?><?= implode("&nbsp;/&nbsp;", $arProperty["DISPLAY_VALUE"]) ?><?
	else:
			?><?= $arProperty["DISPLAY_VALUE"] ?><?
	endif?>

		</p>
<?// echo '<pre>', htmlspecialcharsBx(var_export($arResult, 1)), '</pre>'; ?>
