<?
$_SERVER['REQUEST_URI'] = $_REQUEST['URL'];
?>
<?require_once($_SERVER['DOCUMENT_ROOT']. "/bitrix/modules/main/include/prolog_before.php");?>
<?
$reviews=array();
$rsElements = CIBlockElement::GetList(array("PROPERTY_DATE" => "DESC"), array("IBLOCK_ID"=> 37,">PROPERTY_RATING"=>"4"), false, Array("nTopCount"=>$_REQUEST['COUNT']?:10), array("ID","NAME","PROPERTY_AUTOR","PROPERTY_RATING","PROPERTY_DATE","PROPERTY_PRO","PROPERTY_CONTRA","PROPERTY_TEXT","PROPERTY_ANSWER"));
while($obElement = $rsElements->GetNext())
{
    $reviews[]=$obElement;
}
$f = 1;

?>
<? foreach ($reviews as $review):
    // echo "<pre>";
    // var_dump($review);
    // echo "</pre>";
    // break; ?>
    <div class="comment item<? if ($f): ?> active<? $f = 0; endif ?>">
        <div class="text">
            <? if (!empty($review["PROPERTY_TEXT_VALUE"]["TEXT"])): ?>
                <div class="comment__text-main">
                    <?= $review["PROPERTY_TEXT_VALUE"]["TEXT"] ?>
                </div>
            <? endif ?>
            <? if (!empty($review["PROPERTY_PRO_VALUE"]["TEXT"])): ?>
                <div class="comment__text-dignity">
                    <span class="info-title">Достоинства:</span>
                    <div class="item__text">
                        <?= $review["PROPERTY_PRO_VALUE"]["TEXT"] ?>
                    </div>
                </div>
            <? endif ?>
            <? if (!empty($review["PROPERTY_CONTRA_VALUE"]["TEXT"])): ?>
                <div class="comment__text-lack">
                    <span class="info-title">Недостатки:</span>
                    <div class="item__text">
                        <?= $review["PROPERTY_CONTRA_VALUE"]["TEXT"] ?>
                    </div>
                </div>
            <? endif ?>
            <button type="button" class="height-toggle" title="Раскрыть">
                <span class="pseudolink-bd link-std" data-when-opened="<?= GetMessage('SHRINK_REVIEW_HEIGHT') ?>" data-when-minified="<?= GetMessage('EXPAND_REVIEW_HEIGHT') ?>"></span>
            </button>
        </div>
        <div class="info">
            <div class="rating-stars" data-rating="<?=($review["PROPERTY_RATING_VALUE"])?>" data-disabled="true">
                <i class="flaticon-black13" data-index="1"></i>
                <i class="flaticon-black13" data-index="2"></i>
                <i class="flaticon-black13" data-index="3"></i>
                <i class="flaticon-black13" data-index="4"></i>
                <i class="flaticon-black13" data-index="5"></i>
            </div>
            <div class="author"><?if(strlen($review["PROPERTY_AUTOR_VALUE"])>0):?><? echo $review["PROPERTY_AUTOR_VALUE"]; ?><?else:?>Пользователь скрыл свои данные<?endif;?></div>
            <div class="date"><?=date_text(strtotime($review["PROPERTY_DATE_VALUE"]))?></div>
        </div>
    </div>
<? endforeach; ?>

<?
// $APPLICATION->IncludeComponent("yenisite:yandex.market_reviews_store.query", "main", array(), false);
?>