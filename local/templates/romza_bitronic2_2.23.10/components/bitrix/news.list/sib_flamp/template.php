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
<div class="reviews__col">
    <div class="reviews__top">
        <div class="reviews__left">
            <p class="reviews__text reviews__text_flag"><?=GetMessage('MAIN_TITLE_FLAMP');?></p>
        </div>
        <div class="reviews__right">
            <div class="rating rating_small rating_red">
                <select class="js-rating" name="reviews-rating-1" autocomplete="off">
                    <?for ($value = 1; $value <= 5; $value++):?>
                        <option value="<?=$value?>"
                                <?if ($value == $arParams['SHOP_RATING']):?>selected<?endif?>
                        >
                            <?=$value?>
                        </option>
                    <?endfor?>
                </select>
            </div>
            <p class="rating-total"><?=$arResult['REVIEWS_COUNT']+359?> <?=GetMessage("RATING_FLAMP");?></p>
        </div>
    </div>
    <div class="reviews__middle">
        <div class="responses-1 js-slider-4">
            <?foreach($arResult["GROUPS"] as $group):?>
                <div class="responses-1__slide">
                    <?foreach($group as $arItem):
                        $this->AddEditAction($prefix.$arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
                        $this->AddDeleteAction($prefix.$arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
                    ?>
                        <article class="response-1">
                        <div class="user-1">
                            <?/*?>
                            <div class="user-1__col">
                                <a class="user-1__photo" href="<?=$arItem['PROPERTIES']['REVIEW_LINK']['VALUE']?>">
                                    <img alt="" class="lazy" data-original="<?=CResizer2Resize::ResizeGD2($arItem['PREVIEW_PICTURE']['SRC'], $arParams['RESIZER_SET'])?>" src="<?=ConsVar::showLoaderWithTemplatePath()?>">
                                </a>
                            </div>
                            <?*/?>
                            <div class="user-1__col">
                                <p class="user-1__name">
                                    <a class="user-1__link" href="<?=$arItem['PROPERTIES']['REVIEW_LINK']['VALUE']?>" target="_blank"><?=$arItem['PROPERTIES']['AUTHOR_NAME']['VALUE']?></a>
                                </p>
                                <span class="user-1__rating"><?=$arItem['PROPERTIES']['AUTHOR_RATING']['VALUE']?></span>
                                <div class="rating rating_smallest rating_red">
                                    <select class="js-rating" name="user-rating-1" autocomplete="off">
                                        <?for ($value = 1; $value <= 5; $value++):?>
                                            <option value="<?=$value?>"
                                                    <?if ($value == $arItem['PROPERTIES']['REVIEW_RATING']['VALUE']):?>selected<?endif?>
                                            >
                                                <?=$value?>
                                            </option>
                                        <?endfor?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <p class="response-1__content"><?=$arItem['PREVIEW_TEXT']?></p>
                        <a class="more-link" href="<?=$arItem['PROPERTIES']['REVIEW_LINK']['VALUE']?>" target="_blank"><?=GetMessage('READ_MORE_FLAMP');?></a>
                    </article>
                    <?endforeach?>
                </div>
            <?endforeach?>
        </div>
    </div>
    <div class="reviews__bottom-wrap">
        <div class="reviews__bottom">
            <div class="reviews__left">
                <button class="arrow-prev js-prev-4"></button>
                <button class="arrow-next js-next-4"></button>
            </div>
            <div class="reviews__center">
                <a class="reviews__button button button_white" href="<?=$arParams['LINK_ALL_ITEMS_FLAMP']?>" target="_blank"><?=GetMessage('READ_ALL_REVIEWS');?></a>
            </div>
            <div class="reviews__right dots-1 dots-3 js-dots-1"></div>
        </div>
    </div>
</div>