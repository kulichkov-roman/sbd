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
if (empty($arResult['ITEMS'])) return;
//$arParams['RESIZER_SET'] = 38;
?>
<section class="main-block main-block_video">
    <div class="main-block__top">
        <div class="main-block__left">
            <p class="main-title"><?=GetMessage('MAIN_TITLE_VIDEO')?></p>
            <a class="video-icon" target="_blank" href="<?=$arParams['LINK_ALL_ITEMS_VIDEO']?>"></a>
        </div>
        <div class="main-block__right">
            <a class="arrow-link" target="_blank" href="<?=$arParams['LINK_ALL_ITEMS_VIDEO']?>"><span><?=GetMessage('ALL_ITEMS_VIDEO')?></span></a>
        </div>
    </div>
    <div class="video">
        <ul class="rbs-hor-catalog__list video__list video__list_1 js-slider-8 dots-1 arrows-2">
            <?
            foreach($arResult["ITEMS"] as $arItem):
                $this->AddEditAction($prefix.$arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
                $this->AddDeleteAction($prefix.$arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));

                $itemTitle = (
                    isset($arItem['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']) && $arItem['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'] != ''
                        ? $arItem['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']
                        : $arItem['NAME']
                );
                $video = $arItem['PROPERTIES']['LINK_ON_VIDEO_REVIEW']['VALUE'];

                if($arItem['PREVIEW_PICTURE']['SRC']){
                    $file['src'] = CResizer2Resize::ResizeGD2($arItem['PREVIEW_PICTURE']['SRC'], $arParams['RESIZER_SET']);
                    $fileJpg['src'] = CResizer2Resize::ResizeGD2($arItem['PREVIEW_PICTURE']['SRC'], 44);
                }
                
            ?>
                <li class="video-item">
                    <a class="video-item__link js-fancybox-video" data-fancybox href="https://www.youtube.com/watch?v=<?=$video?>&amp;autoplay=1&amp;rel=0&amp;controls=1&amp;showinfo=0">
                        <div class="video-item__image-wrap">
                            <div class="video-item__image" data-lazy=<?=$file['src']?> data-lazy-jpg=<?=$fileJpg['src']?>></div>
                        </div>
                        <div class="video-item__content">
                            <p class="video-item__title js-ellip-2"><?=$itemTitle?></p>
                            <p class="video-item__text js-ellip-2"><?=$arItem['PREVIEW_TEXT']?></p>
                        </div>
                    </a>
                </li>
            <?endforeach?>
        </ul>
    </div>
</section>
