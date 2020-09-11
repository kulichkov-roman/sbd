<?$isDescr = false;?>
<div class="accordion accordion_mob">
    <? if (strlen(trim($arResult['DETAIL_TEXT'])) > 0) : ?>        
        <?$isDescr = true;?>                       
        <div class="accordion__item active" id="tab_1">
            <div class="accordion__heading">
                <div class="accordion__head"><?=GetMessage('BITRONIC2_DESCR')?></div>
                <div class="accordion__arrow"></div>
            </div>
            <div class="accordion__text open" style="display:block">                                      
                <div class="tab-cont">
                    <div class="tab-inner">
                        <div class="tab-text tab-text_desc rbs-section-description-block">
                            <?=$arResult['DETAIL_TEXT']?>
                        </div>
                    </div>
                </div>                                        
            </div>
        </div>
    <?endif;?>
    <div class="accordion__item <?=$isDescr ? '' : 'active'?>" id="tab_2">
        <div class="accordion__heading">                                    
            <div class="accordion__head"><?=GetMessage('BITRONIC2_CHARACTERISTICS')?></div>
            <div class="accordion__arrow"></div>    
        </div>        
        <div class="accordion__text <?=$isDescr ? '' : 'open'?>"  <?=$isDescr ? '' : 'style="display:block;"'?>>
            <div class="tab-cont" >
                <div class="tab-inner characteristics">
                    <div class="tab-text">
                        <? include 'new_caracteristrics.php' ?>
                    </div>
                </div>
                <div class="characteristics-gears">
                    <img src="<?=SITE_TEMPLATE_PATH?>/new_img/gears.svg" alt="">
                    <div class="characteristics-gears__sm">
                        <img src="<?=SITE_TEMPLATE_PATH?>/new_img/gear.svg" alt="">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?if($bTabAccess):?>
        <div class="accordion__item" id="tab_3_mobile" onclick="$tabAccess.template='sib_detail_list_tab_mobile';$tabAccess.getFullTab();">
            <div class="accordion__heading">                                    
                <div class="accordion__head"><?=GetMessage('BITRONIC2_ACCESS')?></div>
                <div class="accordion__arrow"></div>    
            </div>
            <div class="accordion__text">
                <div class="tab-cont tab-accessories" id="tab_3">
                </div>
            </div>
        </div>
    <?endif;?>
    <?if($bTabServices):?>
        <div class="accordion__item" id="tab_4_mobile" onclick="$tabServices.template='sib_detail_list_tab_mobile';$tabServices.getFullTab();">
            <div class="accordion__heading">
                <div class="accordion__head"><?=GetMessage('BITRONIC2_SERV')?></div>
                <div class="accordion__arrow"></div>
            </div>
            <div class="accordion__text">
                <div class="tab-cont tab-accessories" id="tab_4">
                </div>
            </div>
        </div>
    <?endif;?>
    <?/*?>
    <div class="accordion__item" id="tab_5">
        <div class="accordion__heading">
            <div class="accordion__head"><?=GetMessage('BITRONIC2_REVIEWS')?></div>
            <div class="accordion__arrow"></div>
        </div>
        
        <div class="accordion__text">
            <div class="tab-cont">
                <div class="reviews-cap rbs-padding-10">
                    <? include 'market_reviews_for_sibdroid.php' ?>
                </div>
            </div>
        </div>
    </div>
    <?*/?>
    <?if($arResult['RBS_ASK_TAB'] && \Bitrix\Main\Loader::includeModule('sib.core')):?>
        <div class="accordion__item">
            <div class="accordion__heading">
                <div class="accordion__head"><?=GetMessage('BITRONIC2_ASK')?></div>
                <div class="accordion__arrow"></div>
            </div>
            
            <div class="accordion__text">
                <div class="tab-cont" id="tab_6">
                    <div class="tab-inner ques-answer">
                        <?\Sib\Core\Ask::getComponent($arResult['ID']);?>
                    </div>
                </div>
            </div>
            
        </div>
    <?endif;?>
    <? if ($bShowVideo): ?>
        <div class="accordion__item rbs-accordion__item-video">
            <div class="accordion__heading">
                <div class="accordion__head">Обзоры</div>
                <div class="accordion__arrow"></div>
            </div>
                
            <div class="accordion__text">
                <div class="tab-cont">
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
                                                   <!--  <img
                                                        src="<?=$placeHoldeImg?>"
                                                        alt="<?=$item['NAME']?>"
                                                        title="<?=$item['NAME']?>"
                                                        class=""
                                                        data-original="<?=$item['IMG']['WEBP']?>"
                                                        data-original-jpg="<?=$item['IMG']['JPG']?>"
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
            </div>
        </div>
    <?endif;?>
    
</div>