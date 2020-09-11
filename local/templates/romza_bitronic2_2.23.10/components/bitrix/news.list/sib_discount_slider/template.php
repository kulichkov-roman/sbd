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

global $rz_b2_options;
if(count($arResult['ITEMS']) <= 0) return;
$this->setFrameMode(true);
include $_SERVER["DOCUMENT_ROOT"].SITE_TEMPLATE_PATH.'/include/debug_info_dynamic.php';
$bsSibCore = \Bitrix\Main\Loader::includeModule('sib.core');
?>
<div class="main-slider arrows-1 dots-1 dots-2 js-slider-1">
    <? foreach($arResult['ITEMS'] as $arItem):
        if(empty($arItem['PROPERTIES']['RBS_IMG']['VALUE'])) continue;
        //$file = CFile::ResizeImageGet($arItem['PROPERTIES']['RBS_IMG']['VALUE'], array('width'=>1139, 'height'=>460), BX_RESIZE_IMAGE_PROPORTIONAL, true); 
        $originalPath = CFile::GetPath($arItem['PROPERTIES']['RBS_IMG']['VALUE']);               
        $file['src'] = CResizer2Resize::ResizeGD2($originalPath, 48);
        $fileJpg['src'] = CResizer2Resize::ResizeGD2($originalPath, 49);


        $strLink = $arItem['PROPERTIES']['RBS_LINK']['VALUE'];
        if($bsSibCore){
            if(\Sib\Core\Catalog::isMskRegion($_SESSION['VREGIONS_REGION']['ID']) && $arItem['PROPERTIES']['RBS_LINK_MSK']['VALUE']){
                $strLink = $arItem['PROPERTIES']['RBS_LINK_MSK']['VALUE'];
            }
        }
        //global $USER; if($USER->IsAdmin()){echo '<pre>'; print_r($arItem['PROPERTIES']['RBS_IMG']['VALUE']); echo '</pre>';};
        //$arParams['RESIZER_SET'] = 'false';
        //echo $arParams['RESIZER_SET'];
    ?>
        <article class="main-slider__item" data-lazy="<?=$file['src']?>" data-lazy-jpg="<?=$fileJpg['src']?>"  style="">
            <? if (!empty($strLink)):?>
                <a href="<?=$strLink?>" class="main-slider__content-wrap">
            <?else:?>
                <div class="main-slider__content-wrap">
            <?endif?>
                    <div class="main-slider__content">
                        <h2 class="main-slider__title"><?=$arItem['PROPERTIES']['RBS_TITLE']['VALUE']?></h2>
                        <p class="main-slider__text"><?=$arItem['PROPERTIES']['RBS_DESC']['VALUE']?></p>
                        <? if (!empty($strLink)):?>
                        <!--  <a class="main-slider__button button button_shadow" href="<?=$arItem['PROPERTIES']['LINK']['VALUE']?>"><?=GetMessage('SHOW_MORE')?></a> -->
                        <?endif?>
                    </div>
            <? if (!empty($strLink)):?>
                </a>
            <?else:?>
                </div>
            <?endif?>

        </article><!-- /.slide -->
    <?endforeach?>
</div>
