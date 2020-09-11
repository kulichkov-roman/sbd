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

        
    



#SECTION_PLACE#

<?if($arParams["DISPLAY_TOP_PAGER"]):?>
	<?=$arResult["NAV_STRING"]?>
<?endif;?>
<section class="main-block main-block_sale news_rbs">
    <h1 class="main-title"><?=($APPLICATION->GetTitle("h1") ?: $APPLICATION->GetTitle('title'))?></h1>
        <div class="cards-wrap cards-wrap_news">
<?foreach($arResult["ITEMS"] as $arItem):?>
	<?
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	?>
	<a href="<?echo $arItem["DETAIL_PAGE_URL"]?>" class="card card_yellow" itemscope itemtype="http://schema.org/ImageObject" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
        <?if($arParams["DISPLAY_PICTURE"]!="N" && is_array($arItem["PREVIEW_PICTURE"])):?>   
            <div class="card__img">                            
            
                    <img itemprop="contentUrl"  class="lazy" data-original="<?//=CResizer2Resize::ResizeGD2($arItem["PREVIEW_PICTURE"]["SRC"], $arParams["RESIZER_NEWS_LIST"])?>" src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" title="<?=$arItem["PREVIEW_PICTURE"]["ALT"]?>" alt="<?=$arItem["PREVIEW_PICTURE"]["ALT"]?>">
                
            </div>
        <?endif?>
       <div class="card__cnt">
           <div class="card__date">
               <?echo $arItem["DISPLAY_ACTIVE_FROM"]?>
           </div>
           <div class="card__heading">
               <?echo $arItem["NAME"]?>
           </div>
           <div class="card__text">
               <?echo $arItem["PREVIEW_TEXT"];?>
           </div>
       </div>
    </a>
    
<?endforeach;?>
    </div>
    <?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
        <div>
            <?=$arResult["NAV_STRING"]?>
        </div>
    <?endif;?>
</section>

