<?
$aboutText = '';
if(\Bitrix\Main\Loader::includeModule('sib.core')){
    $aboutText = \Sib\Core\Helper::getAboutText();
}
?>
<article class="seo wrapper">
    <div class="seo__main-wrap">
        <div class="seo__main js-seo">
            <div class="seo__content js-seo-content">
                <?if($aboutText):?>
                    <?=$aboutText?>
                <?else:?>
                    <h1>Смартфоны в Новосибирске</h1>
                    <p>Главное преимущество покупателя — возможность выбора. Вы можете купить дорогой и раскрученный смартфон, а можете выбрать недорогой, но обладающий отличными характеристиками. Это мы и называем разумным выбором!<br>Именно покупателю, способному отличить блеск маркетинговых ходов от реальных возможностей смартфонов, и адресован наш интернет-магазин, основной ассортимент которого составляют китайские смартфоны.
                    </p>
                <?endif?>
            </div>
            <a class="seo__more js-seo-more" href="javascript:void(0);"></a>
        </div>
    </div>
</article>