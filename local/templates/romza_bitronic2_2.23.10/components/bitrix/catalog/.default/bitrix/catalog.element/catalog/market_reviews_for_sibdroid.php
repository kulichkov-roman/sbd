<?  //if ($USER->IsAdmin()): ?>
<?

$reviews=array();
$rsElements = CIBlockElement::GetList(array("PROPERTY_DATE"=>"DESC"), array("IBLOCK_ID"=>38,'PROPERTY_PRODUCT_ID'=>$arResult["ID"]), false, Array("nTopCount"=>10), array("ID","NAME","PROPERTY_AUTOR","PROPERTY_RATING","PROPERTY_DATE","PROPERTY_PRO","PROPERTY_CONTRA","PROPERTY_TEXT","PROPERTY_ANSWER"));
while($obElement = $rsElements->GetNext())
{
    $reviews[]=$obElement;
}
// echo "<pre>";
// var_dump();
// echo "</pre>";
?>
<?//reviews...
foreach ($reviews as $review):

    ?>
    <noindex>
        <div id="review-<?=$review["id"];?>" class="b-aura-review b-aura-review_collapsed js-review js-review-shop" data-grade-id=<?=$review["id"];?> itemprop="review" itemscope="itemscope" itemtype="http://schema.org/Review">
            <div class="b-aura-review__title">
                <div class="b-aura-user">
                    <span class="b-aura-username" itemprop="author"><?if(strlen($review["PROPERTY_AUTOR_VALUE"])>0):?><span itemprop="author"><? echo $review["PROPERTY_AUTOR_VALUE"]; ?></span><?else:?>Пользователь скрыл свои данные<?endif;?></span>
                    <div class="b-aura-user__social">
                    </div>
                </div>
                <div class="b-aura-usergeo">
                <span class="b-aura-usergeo__date">
                <?=date_text(strtotime($review["PROPERTY_DATE_VALUE"]));?>

                <?/*<meta itemprop="datePublished" content="2013-05-30T16:38:14">*/?>
                </span>
                </div>
            </div>
            <div class="b-aura-review__rate i-clearfix">
            <span class="b-aura-rating b-aura-rating_state_<?=($review["PROPERTY_RATING_VALUE"]);?>" title="" data-title="" data-rate=<?=($review["PROPERTY_RATING_VALUE"]);?>>
                <i class="b-aura-rating__item b-aura-rating__item_1"></i>
                <i class="b-aura-rating__item b-aura-rating__item_2"></i>
                <i class="b-aura-rating__item b-aura-rating__item_3"></i>
                <i class="b-aura-rating__item b-aura-rating__item_4"></i>
                <i class="b-aura-rating__item b-aura-rating__item_5"></i>
            </span>
                <span class="b-aura-rating__text">
                <?=rate_text($review["PROPERTY_RATING_VALUE"]-3);?>
            </span>
                <span itemprop="reviewRating" itemscope="itemscope" itemtype="http://schema.org/Rating">
            <meta itemprop="ratingValue" content="1"><meta itemprop="bestRating" content="5"></span>
            </div>

            <div class="b-aura-review__verdict">
                <?if(isset($review["PROPERTY_PRO_VALUE"]["TEXT"]) && $review["PROPERTY_PRO_VALUE"]["TEXT"] !== ""):?>
                    <div class="b-aura-userverdict">
                        <div class="b-aura-userverdict__title"><?=GetMessage('PRO');?></div>
                        <div class="b-aura-userverdict__text" itemprop="pro"><?=$review["PROPERTY_PRO_VALUE"]["TEXT"];?></div>
                    </div>
                <?endif;?>
                <?if(isset($review["PROPERTY_CONTRA_VALUE"]["TEXT"]) && $review["PROPERTY_CONTRA_VALUE"]["TEXT"] !== ""):?>
                    <div class="b-aura-userverdict">
                        <div class="b-aura-userverdict__title"><?=GetMessage('CONTRA');?></div>
                        <div class="b-aura-userverdict__text" itemprop="contra"><?=$review["PROPERTY_CONTRA_VALUE"]["TEXT"];?></div>
                    </div>
                <?endif;?>
                <?if(isset($review["PROPERTY_TEXT_VALUE"]["TEXT"]) && $review["PROPERTY_TEXT_VALUE"]["TEXT"] !== ""):?>
                    <div class="b-aura-userverdict  b-aura-userverdict_type_newline">
                        <?if((isset($review["PROPERTY_PRO_VALUE"]["TEXT"]) && $review["PROPERTY_PRO_VALUE"]["TEXT"] !== "")||(isset($review["PROPERTY_CONTRA_VALUE"]["TEXT"]) && $review["PROPERTY_CONTRA_VALUE"]["TEXT"] !== "")):?>
                            <div class="b-aura-userverdict__title"><?=GetMessage('TEXT');?></div>
                        <?endif;?>
                        <div class="b-aura-userverdict__text" itemprop="description"><?=$review["PROPERTY_TEXT_VALUE"]["TEXT"];?></div>
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
<?endforeach?>
<?if (1 > count($reviews)):?>
    <div style="overflow: hidden;">
        <h4><?=GetMessage('NO_REVIEW_TITLE')?></h4>
        <p><?=GetMessage('NO_REVIEW_LINE_1')?><br><?=GetMessage('NO_REVIEW_LINE_2')?></p>
        <a class="link" target="_blank" href="https://market.yandex.ru/product/<?=$arResult['PROPERTIES']["YANDEX_ID"]["VALUE"]?>/reviews/add" style="line-height: 20px;">
            <span class="text"><?=GetMessage('NO_REVIEW_LINK')?></span>
        </a>
    </div>
<?endif?>
<?if (10 == count($reviews)):?>
    <div style="overflow: hidden;">
        <a class="link" target="_blank" href="https://market.yandex.ru/product/<?=$arResult['PROPERTIES']["YANDEX_ID"]["VALUE"]?>/reviews" style="line-height: 20px;">
            <span class="text">Посмотреть все отзывы</span>
        </a>
    </div>
<?endif?>
<?  //endif; ?>
