<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true);?>
<?
global $shop_count_opinions;
global $shop_rating;
?>

<div style="text-align: right; margin-bottom: 1em"><a target="_blank" href="https://market.yandex.ru/shop/307694/reviews/add">Оставить свой отзыв</a></div>
<?//reviews...
    foreach ($arResult["ITEMS"] as $review) {
    ?>

           <noindex>
        <div id="review-<?=$review["id"];?>" class="b-aura-review b-aura-review_collapsed js-review js-review-shop" data-grade-id=<?=$review["id"];?> itemprop="review" itemscope="itemscope" itemtype="http://schema.org/Review">
        <div class="b-aura-review__title">
            <div class="b-aura-user">
                    <span class="b-aura-username" itemprop="author"><?if(strlen($review["PROPERTIES"]["AUTOR"]["VALUE"])>0):?><span itemprop="author"><? echo $review["PROPERTIES"]["AUTOR"]["VALUE"]; ?></span><?else:?>Пользователь скрыл свои данные<?endif;?></span>
                    <div class="b-aura-user__social">
                    </div>
            </div>
            <div class="b-aura-usergeo">
                <span class="b-aura-usergeo__date">
                <?=date_text(strtotime($review["PROPERTIES"]["DATE"]["VALUE"]));?>

                <?/*<meta itemprop="datePublished" content="2013-05-30T16:38:14">*/?>
                </span>
            </div>
        </div>
        <div class="b-aura-review__rate i-clearfix">
            <span class="b-aura-rating b-aura-rating_state_<?=($review["PROPERTIES"]["RATING"]["VALUE"]);?>" title="" data-title="" data-rate=<?=($review["PROPERTIES"]["RATING"]["VALUE"]);?>>
                <i class="b-aura-rating__item b-aura-rating__item_1"></i>
                <i class="b-aura-rating__item b-aura-rating__item_2"></i>
                <i class="b-aura-rating__item b-aura-rating__item_3"></i>
                <i class="b-aura-rating__item b-aura-rating__item_4"></i>
                <i class="b-aura-rating__item b-aura-rating__item_5"></i>
            </span>
            <span class="b-aura-rating__text">
                <?=rate_text($review["PROPERTIES"]["RATING"]["VALUE"]-3);?>
            </span>
            <span itemprop="reviewRating" itemscope="itemscope" itemtype="http://schema.org/Rating">
            <meta itemprop="ratingValue" content="1"><meta itemprop="bestRating" content="5"></span>
        </div>
        
        <div class="b-aura-review__verdict">
            <?if(isset($review["PROPERTIES"]["PRO"]["VALUE"]["TEXT"]) && $review["PROPERTIES"]["PRO"]["VALUE"]["TEXT"] !== ""):?>
                <div class="b-aura-userverdict">
                    <div class="b-aura-userverdict__title"><?=GetMessage('PRO');?></div>
                    <div class="b-aura-userverdict__text" itemprop="pro"><?=$review["PROPERTIES"]["PRO"]["VALUE"]["TEXT"];?></div>
                </div>
            <?endif;?>
            <?if(isset($review["PROPERTIES"]["CONTRA"]["VALUE"]["TEXT"]) && $review["PROPERTIES"]["CONTRA"]["VALUE"]["TEXT"] !== ""):?>
                <div class="b-aura-userverdict">
                    <div class="b-aura-userverdict__title"><?=GetMessage('CONTRA');?></div>
                    <div class="b-aura-userverdict__text" itemprop="contra"><?=$review["PROPERTIES"]["CONTRA"]["VALUE"]["TEXT"];?></div>
                </div>
            <?endif;?>
            <?if(isset($review["PROPERTIES"]["TEXT"]["VALUE"]["TEXT"]) && $review["PROPERTIES"]["TEXT"]["VALUE"]["TEXT"] !== ""):?>
                <div class="b-aura-userverdict  b-aura-userverdict_type_newline">
                    <?if((isset($review["PROPERTIES"]["PRO"]["VALUE"]["TEXT"]) && $review["PROPERTIES"]["PRO"]["VALUE"]["TEXT"] !== "")||(isset($review["PROPERTIES"]["CONTRA"]["VALUE"]["TEXT"]) && $review["PROPERTIES"]["CONTRA"]["VALUE"]["TEXT"] !== "")):?>
                        <div class="b-aura-userverdict__title"><?=GetMessage('TEXT');?></div>
                    <?endif;?>
                    <div class="b-aura-userverdict__text" itemprop="description"><?=$review["PROPERTIES"]["TEXT"]["VALUE"]["TEXT"];?></div>
                </div>
            <?endif;?>
        </div>
        
        <? /*<div class="b-aura-review__footer">
            <div class="b-aura-usergrade b-aura-usergrade_active_yes">
                <div class="b-spin b-spin_size_27 b-spin_theme_grey-27 i-bem b-spin_js_inited" onclick="return {'b-spin':{name:'b-spin'}}">
                <img alt="" src="//yandex.st/lego/_/La6qi18Z8LwgnZdsAr1qy1GwCwo.gif" class="b-icon b-spin__icon">
                </div><?=GetMessage('HELPFULNESS');?><span class="b-aura-usergrade__votes">
                <span class="b-aura-usergrade__pro"><?=GetMessage('HELPFULNESS_YES');?></span> 
                <span class="b-aura-usergrade__pro-num"><?=$review["agree"];?></span>
                / 
                <span class="b-aura-usergrade__contra"><?=GetMessage('HELPFULNESS_NO');?></span> 
                <span class="b-aura-usergrade__contra-num"></span><?=$review["reject"];?></span>
            </div>
        </div>
        */?>
        </div>
        </noindex>
    <?}?>
<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
    <div>
        <?=$arResult["NAV_STRING"]?>
    </div>
<?endif;?>