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
?>
<section class="main-block main-block_insta">
    <div class="main-block__top">
        <div class="main-block__left">
            <p class="main-title"><?=GetMessage('MAIN_TITLE_INST')?></p>
            <a class="instagram-icon" target="_blank" href="<?=$arParams['LINK_ALL_ITEMS_INST']?>"></a>
        </div>
        <div class="main-block__right">
            <a class="arrow-link" target="_blank" href="<?=$arParams['LINK_ALL_ITEMS_INST']?>"><span><?=GetMessage('ALL_ITEMS_INST')?></span></a>
        </div>
    </div>
    <div class="instagram">
        <ul class="rbs-hor-catalog__list instagram__list instagram__list_1 js-slider-3 dots-1 arrows-2">
            <?foreach($arResult["ITEMS"] as $arItem):
                $this->AddEditAction($prefix.$arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
                $this->AddDeleteAction($prefix.$arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));

                if($arItem['PREVIEW_PICTURE']['SRC']){
                    $file['src'] = CResizer2Resize::ResizeGD2($arItem['PREVIEW_PICTURE']['SRC'], $arParams['RESIZER_SET']);
                    $fileJpg['src'] = CResizer2Resize::ResizeGD2($arItem['PREVIEW_PICTURE']['SRC'], 45);
                }
            ?>
                <li class="instagram-item">
                    <a class="instagram-item__link" target="_blank" href="<?=$arItem['PROPERTIES']['LINK_ON_PHOTO']['VALUE']?>" data-lazy=<?=$file['src']?> data-lazy-jpg=<?=$fileJpg['src']?>></a>
                </li>
            <?endforeach?>
        </ul>
    </div>
</section>
