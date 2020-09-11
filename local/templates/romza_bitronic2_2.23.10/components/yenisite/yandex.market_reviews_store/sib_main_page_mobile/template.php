<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
if (method_exists($this, 'setFrameMode')) $this->setFrameMode(true);
define('MAX_YEARS_FOR_FIRST_ENDING', 4);
?>
<div class="tab-cont tab-cont_payment hide" id="tab_2">
    <div class="reviews__col">
        <div class="reviews__top">
            <div class="reviews__left">
                <p class="reviews__text"><?=GetMessage("TITLE_YANDEX");?></p>
                <p class="reviews__text">
                    <?
                        $ending = $arResult['REVIEW_WORKS_PERIOD'] > MAX_YEARS_FOR_FIRST_ENDING ? GetMessage("SECOND_VARIANT_YEAR") : GetMessage("FIRST_VARIANT_YEAR");
                        echo $arResult['REVIEW_WORKS_PERIOD'] . $ending . GetMessage("DESCRIPTION_YANDEX");
                    ?>
                </p>
            </div>
            <div class="reviews__right">
                <div class="rating rating_small">
                    <select class="js-rating" name="reviews-rating-2" autocomplete="off">
                        <?for ($value = 1; $value <= 5; $value++):?>
                            <option value="<?=$value?>"
                                    <?if ($value == $arParams['SHOP_RATING']):?>selected<?endif?>
                            >
                                <?=$value?>
                            </option>
                        <?endfor?>
                    </select>
                </div>
                <p class="rating-total"><?=$arResult['REVIEWS_COUNT']+583?> <?=GetMessage("RATING_YANDEX");?></p>
            </div>
        </div>
        <div class="reviews__middle">
            <div class="responses-2 js-slider-5" id="main-shop-reviews">
            </div>
        </div>
        <div class="reviews__bottom-wrap">
            <div class="reviews__bottom">
                <div class="reviews__left">
                    <button class="arrow-prev js-prev-5"></button>
                    <button class="arrow-next js-next-5"></button>
                </div>
                <div class="reviews__center">
                    <a class="reviews__button button button_white" target="_blank" href="http://market.yandex.ru/shop/<?=$arParams['SHOPID']?>/reviews"><?=GetMessage("ALL_YANDEX");?></a>
                </div>
                <div class="reviews__right dots-1 dots-3 js-dots-2"></div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
	$(document).ready(function () {
        $('.rbs-ajax-yandex-mobile').one('click', function(){
            updateYRMS(1, '<?=$templateFolder?>', '<?=$arParams["COUNT"]?>', '<?=$APPLICATION->GetCurPage(true)?>');
        })
	})
</script>