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



      
<div class="cards-wrap cards-wrap_service">
<?if($arParams["DISPLAY_TOP_PAGER"]):?>
	<?=$arResult["NAV_STRING"]?>
<?endif;?>

<?foreach($arResult["ITEMS"] as $arItem):?>
	<?
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	?>
    <a href="<?echo $arItem["DETAIL_PAGE_URL"]?>" class="card">
        <div itemscope itemtype="http://schema.org/ImageObject" class="item" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
            <?if($arParams["DISPLAY_PICTURE"]!="N" && is_array($arItem["PREVIEW_PICTURE"])):?>
            <div class="card__img">
                <div class="card__icon">
                     <span class="icon-shield-icon"></span>
                </div>
                <img itemprop="contentUrl"  class="lazy" data-original="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" title="<?=$arItem["PREVIEW_PICTURE"]["ALT"]?>" alt="<?=$arItem["PREVIEW_PICTURE"]["ALT"]?>">
            </div>
            <?endif?>
    		<div class="card__cnt">
                <div class="card__date">
                    <?if (!empty($arItem["DISPLAY_ACTIVE_FROM"])):?>
                        Акция действует с <?=$arItem["DISPLAY_ACTIVE_FROM"]?> по <?=$arItem["DISPLAY_ACTIVE_TO"]?>
                    <?endif;?>
                </div>
                <div class="card__heading">
                    <?echo $arItem["NAME"]?>
                </div>
                <div class="card__text">
                    <?echo $arItem["PREVIEW_TEXT"]?>
               </div>
    		</div><!-- /.content -->
        </div>
    </a>
    
		
<?endforeach;?>
</div>

<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
	<div>
		<?=$arResult["NAV_STRING"]?>
	</div>
<?endif;?>
