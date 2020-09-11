<?
$_SERVER['REQUEST_URI'] = $_REQUEST['URL'];
?>
<?require_once($_SERVER['DOCUMENT_ROOT']. "/bitrix/modules/main/include/prolog_before.php");?>
<?
$reviews = array();
if(\Bitrix\Main\Loader::includeModule('sib.core')){
    $reviews = \Sib\Core\Helper::getReviewsYandex();
} else {
    $rsElements = CIBlockElement::GetList(array("PROPERTY_DATE" => "DESC"), array("IBLOCK_ID"=>37,">PROPERTY_RATING"=>"4"), false, Array("nTopCount"=> $_REQUEST['COUNT']?:10), array("ID","NAME","PROPERTY_AUTOR","PROPERTY_RATING","PROPERTY_DATE","PROPERTY_PRO","PROPERTY_CONTRA","PROPERTY_TEXT","PROPERTY_ANSWER", "PREVIEW_PICTURE", "PROPERTY_ISTOCNIK"));
    while($obElement = $rsElements->GetNext()){
        if(!$obElement['PROPERTY_ISTOCNIK_VALUE']){
            $obElement['PROPERTY_ISTOCNIK_VALUE'] = 'https://market.yandex.ru/shop--sibdroid-ru/307694/reviews';
        }
        $reviews[] = $obElement;
    }
}
$f = 1;
?>
<? foreach ($reviews as $review):?>
    <article class="response-2">
        <div class="user-2">
            <?/*?>
            <div class="user-2__col">
                <div class="user-2__photo">
                    <img alt="" class="lazy" data-original="<?=CResizer2Resize::ResizeGD2($arItem['PREVIEW_PICTURE']['SRC'], $arParams['RESIZER_SET'])?>" src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>">
                </div>
            </div>
            <?*/?>
            <div class="user-2__col">
                <h4 class="user-2__name"><?=$review["PROPERTY_AUTOR_VALUE"]?></h4>
            </div>
        </div>
        <div class="rating-all">
            <div class="rating-all__col">
                <div class="rating rating_small">
                    <select class="js-rating rating-stars" name="user-rating-10" autocomplete="off" data-rating="<?=($review["PROPERTY_RATING_VALUE"])?>">
                        <?for ($value = 1; $value <= 5; $value++):?>
                            <option value="<?=$value?>"
                                    <?if ($value == $review['PROPERTY_RATING']):?>selected<?endif?>
                            >
                                <?=$value?>
                            </option>
                        <?endfor?>
                    </select>
                </div>
            </div>
            <?/*?>
            <div class="rating-all__col">
                <p class="rating-all__text">отличный магазин</p>
                <p class="rating-all__text">Способ покупки: покупка в магазине</p>
            </div>
            <?*/?>
        </div>
        <div class="response-2__content">
            <?if (!empty($review["PROPERTY_PRO_VALUE"]["TEXT"])):?>
                <div class="response-2__text">
                    <p><b><?=GetMessage('YANDEX_REVIEW_ADVANTAGES');?>:</b></p><br>
                    <p class="js-ellip-3"><?=$review["PROPERTY_PRO_VALUE"]["TEXT"]?></p>
                </div>
            <?endif?>
            <?if (!empty($review["PROPERTY_CONTRA_VALUE"]["TEXT"])):?>
                <div class="response-2__text">
                    <p><b><?=GetMessage('YANDEX_REVIEW_DISADVANTAGES');?>:</b></p>
                    <p class="js-ellip-3"><?=$review["PROPERTY_CONTRA_VALUE"]["TEXT"]?></p>
                </div>
            <?endif?>
            <?if (!empty($review["PROPERTY_TEXT_VALUE"]["TEXT"])):?>
                <div class="response-2__text">
                    <p><b><?=GetMessage('YANDEX_REVIEW_COMMENTS');?>:</b></p><br>
                    <p class="js-ellip-4"><?=$review["PROPERTY_TEXT_VALUE"]["TEXT"]?></p>
                </div>
            <?endif?>
        </div>
        <p class="response-2__date"><b style="color:#333;">Отзыв опубликован:</b> <?=date_text(strtotime($review["PROPERTY_DATE_VALUE"]))?></p>
        <p class="response-2__date">г. <?=$review["PROPERTY_DOMAIN_VALUE"]?></p>
        <a class="more-link" href="<?=$review['PROPERTY_ISTOCNIK_VALUE']?>" target="_blank"><?=GetMessage('READ_MORE_YANDEX');?></a>
    </article>
<? endforeach; ?>
