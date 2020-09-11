<div class="tab-wrap tab-wrap_card ">                            
    <ul class="nav-tab-list tabs js-tabs-carousel arrows-2">
        <? if (strlen(trim($arResult['DETAIL_TEXT'])) > 0) : ?>
            <li class="nav-tab-list__item active">
                <a href="#tab_1" class="nav-tab-list__link"><span><?=GetMessage('BITRONIC2_DESCR')?></span></a>
            </li>
        <? endif; ?>
        <li class="nav-tab-list__item">
            <a href="#tab_2" class="nav-tab-list__link"><span><?=GetMessage('BITRONIC2_CHARACTERISTICS')?></span></a>
        </li>
        <?if($bTabAccess):?>
            <li class="nav-tab-list__item">
                <a href="#tab_3" onclick="$tabAccess.getFullTab()" class="nav-tab-list__link"><span><?=GetMessage('BITRONIC2_ACCESS')?> </span><span><?=count($arResult['PROPERTIES']['RECOMMEND']['VALUE'])?></span></a>
            </li> 
        <?endif;?>
        <?if($bTabServices):?>
            <li class="nav-tab-list__item">
                <a href="#tab_4" onclick="$tabServices.getFullTab()" class="nav-tab-list__link"><span><?=GetMessage('BITRONIC2_SERV')?> </span><span><?=count($arResult['PROPERTIES']['SERVICE']['VALUE'])?></span></a>
            </li>
        <?endif;?>
        <?/*?>
        <li class="nav-tab-list__item">
            <a href="#tab_5" class="nav-tab-list__link"><span><?=GetMessage('BITRONIC2_REVIEWS')?> </span><span><?=(int)$arResult['PROPERTIES']['RESP_COUNT']['VALUE']?></span></a>
        </li>
        <?*/?>
        <?if($arResult['RBS_ASK_TAB']):?>
        <li class="nav-tab-list__item">
            <a href="#tab_6" class="nav-tab-list__link"><span><?=GetMessage('BITRONIC2_ASK')?> </span><span><?=$arResult['RBS_ASK_TAB_COUNT']?></span></a>
        </li>
        <?endif?>
        <? if ($bShowVideo): ?>
            <li class="nav-tab-list__item">
                <a href="#tab_7" class="nav-tab-list__link"><span>Обзоры</span><span> <?=$arResult['RBS_REVIEWS']['COUNT']?></span></a>
            </li>
        <?endif;?>
    </ul>
    <div class="box-tab-cont">
        <? if (strlen(trim($arResult['DETAIL_TEXT'])) > 0) : ?>
            <div class="tab-cont" id="tab_1">
                <div class="tab-inner">
                    <div class="tab-text tab-text_desc rbs-section-description-block">
                        <?=$arResult['DETAIL_TEXT']?>
                    </div>
                </div>
            </div>
        <? endif; ?>
                                        
        <div class="tab-cont hide" id="tab_2">
            <div class="tab-inner characteristics">
                <div class="tab-text">
                    <? include 'new_caracteristrics.php' ?>
                </div>
                <div class="characteristics-gears">
                    <img src="<?=SITE_TEMPLATE_PATH?>/new_img/gears.svg" alt="">
                    <div class="characteristics-gears__sm">
                        <img src="<?=SITE_TEMPLATE_PATH?>/new_img/gear.svg" alt="">
                    </div>
                </div>
            </div>
        </div>
        <?if($bTabAccess):?>
            <div class="tab-cont hide tab-accessories" id="tab_3">
            </div>
        <?endif;?>
        <?if($bTabServices):?>
            <div class="tab-cont hide tab-accessories" id="tab_4">
            </div>
        <?endif;?>
        <?/*?>
        <div class="tab-cont hide" id="tab_5">
            <div class="reviews-cap">
                <? include 'market_reviews_for_sibdroid.php' ?>
            </div>
        </div>
         <?*/?>
        <?if($arResult['RBS_ASK_TAB'] && \Bitrix\Main\Loader::includeModule('sib.core')):?>
            <div class="tab-cont tab-cont_question hide" id="tab_6">
                <div class="tab-inner ques-answer">
                    <?\Sib\Core\Ask::getComponent($arResult['ID']);?>
                </div>
            </div>
        <?endif?>
        <?if($bShowVideo):?>
            <div class="tab-cont tab-cont_video hide" id="tab_7">
                <div class="tab-inner rbs-video-tab">
                    <?if(count($arResult['RBS_REVIEWS']['VIDEO']) > 0):?>
                        <div class="review-block">
                            <p class="h2"><?= $arParams['TITLE_TAB_VIDEO'] ?: GetMessage('BITRONIC2_VIDEO_REVIEWS') ?></p>
                            <div class="rbs-video-tab-content">                                                    
                                <? foreach ($arResult['RBS_REVIEWS']['VIDEO'] as $value):
                                    preg_match('#(\.be/|/embed/|/v/|/watch\?v=)([A-Za-z0-9_-]{5,11})#', $value, $matches); ?>
                                    <span class="hide rbs-video-to-show" data-src="//www.youtube.com/embed/<?= $matches[2] ?>"></span>
                                <? endforeach; ?>
                            </div>
                        </div>
                    <?endif?>
                    <?if(count($arResult['RBS_REVIEWS']['BLOG']) > 0):?>
                        <div class="review-block">
                            <p class="h2">Статьи</p>
                            <div class="rbs-video-tab-content">                                                    
                                <? foreach ($arResult['RBS_REVIEWS']['BLOG'] as $item):?>
                                    <div class="rbs-blog-detail-item">
                                        <!-- <div>Title</div> -->
                                        <div data-original="<?=$item['IMG']['WEBP']?>" data-original-jpg="<?=$item['IMG']['JPG']?>">
                                            <a target="_blank" href="<?=$item['DETAIL_PAGE_URL']?>">
                                                <!-- <img
                                                    src="<?=$placeHoldeImg?>"
                                                    alt="<?=$item['NAME']?>"
                                                    title="<?=$item['NAME']?>"
                                                    class=""
                                                    data-original="<?=$item['IMG']['WEBP']?>"
                                                    data-original-jpg="<?=$item['IMG']['JPG']?>" 
                                                    style=""
                                                > -->
                                            </a>
                                        </div>
                                        <!-- <div>
                                            <a href="<?=$item['DETAIL_PAGE_URL']?>" target="_blank" class="button">Подробнее</a>
                                        </div> -->
                                    </div>
                                <? endforeach; ?>
                            </div>
                        </div>
                    <?endif?>
                </div>
            </div>
        <?endif;?>
    </div>
</div>

   
                        

