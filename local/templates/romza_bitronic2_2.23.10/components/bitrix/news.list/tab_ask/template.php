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
?>

<?if(count($arResult['ITEMS']) <= 0):?>
    <div class="answer-tille">
        <p class="h2"><?=GetMessage('EMPTY_ASK')?></p>
        <a href="#popup-faq" class="button js-fancybox"><?=GetMessage('ASK')?></a>
    </div>
<?else:?>
    <?if($arResult['MAIN_ASK']):?>
        <div class="answer-tille">
            <p class="h2"><?=GetMessage('MAIN_ASK')?></p>
            <a href="#popup-faq" class="button js-fancybox"><?=GetMessage('ASK')?></a>
        </div>
        <ul class="faq-list">
            <?foreach($arResult['MAIN_ASK'] as $mainAsk):?>
                <li class="faq-list__item">
                    <div class="faq-list__row">
                        <div class="faq-list__user">
                            <div class="faq-list__name"><?=$mainAsk['PROPERTY_NAME_VALUE']?></div>
                            <div class="faq-list__date"><?=$mainAsk['DATE_CREATE']?></div>
                        </div>
                        <div class="faq-list-question">
                            <div class="faq-list-question__title"><?=GetMessage('ASK_TEXT')?></div>
                            <p><?=$mainAsk['PREVIEW_TEXT']?></p>
                            <div class="faq-list-question__count">
                                <?$lastNum = count($mainAsk['ANSWERS']) % 10;?>
                                <a href="#" class="js-show-answer"><i class="icon-comments"></i></a><span><?=GetMessage('COUNT_ANSWERS_' . $lastNum, array('#COUNT#' => count($mainAsk['ANSWERS'])))?></span>
                            </div>
                            <div class="text-right">
                                <a href="#popup-answer" data-ask-id="<?=$mainAsk['ID']?>" class="button js-fancybox"><?=GetMessage('ANS')?></a>
                            </div>
                        </div>
                    </div>
                    <?if($mainAsk['ANSWERS']):?>
                        <?foreach($mainAsk['ANSWERS'] as $mainAns):?>
                            <div class="faq-list__row faq-list__row_answer">
                                <div class="faq-list-question faq-list-answer">
                                    <div class="faq-list-answer__title">
                                        <i class="icon-comments"></i>
                                        <span class="faq-list-answer__name"><?=$mainAns['PROPERTY_NAME_VALUE']?></span>
                                        <?if($mainAns['IS_COMPANY_USER']):?>
                                            <span class="faq-list-answer__prof"> <?=GetMessage('COMPANY_USER')?></span>
                                            <span class="faq-list-answer__logo"><img src="<?=SITE_TEMPLATE_PATH?>/new_img/svg/logo.svg" alt=""></span>
                                        <?endif;?>
                                        
                                    </div>
                                    <div class="faq-list-answer__date"><?=$mainAns['DATE_CREATE']?></div>
                                    <p><?=$mainAns['PREVIEW_TEXT']?></p>
                                    <ul class="answer-social">
                                        <li class="answer-social__item">
                                            <a href="#" data-type="like" data-ans-id="<?=$mainAns['ID']?>" data-ask-id="<?=$mainAsk['ID']?>" class="answer-social__link"><i class="icon-like"></i></a>
                                            <span><?=$mainAns['PROPERTY_LIKE_VALUE']?:0?></span>
                                        </li>
                                        <li class="answer-social__item">
                                            <a href="#" data-type="dislike" data-ans-id="<?=$mainAns['ID']?>" data-ask-id="<?=$mainAsk['ID']?>" class="answer-social__link"><i class="icon-dislike"></i></a>
                                            <span><?=$mainAns['PROPERTY_DIS_LIKE_VALUE']?:0?></span>
                                        </li>
                                    </ul>
                                    <button class="location__close button-close js-close-answer"></button> 
                                </div>
                            </div>
                        <?endforeach;?>  
                    <?endif;?>
                </li>
            <?endforeach;?>
        </ul>
    <?endif;?>

    <div class="answer-tille rbs-ask-header">
        <p class="h2"><?=GetMessage('NEW_ANS')?></p>
        <select class="js-styled" id="rbs-tab-ask-sort">
            <option value="date"><?=GetMessage('SORT_BY_DATE')?></option>
            <option value="shows"><?=GetMessage('SORT_BY_SHOWS')?></option>            
        </select>
    </div>

    <ul class="faq-list rbs-ask-content">
        <?foreach($arResult['ITEMS'] as $item):?>

            
            <li class="faq-list__item">
                <div class="faq-list__row">
                    <div class="faq-list__user">
                        <div class="faq-list__name"><?=$item['PROPERTIES']['NAME']['VALUE']?></div>
                        <div class="faq-list__date"><?=$item['DATE_CREATE']?></div>
                    </div>
                    <div class="faq-list-question">
                        <div class="faq-list-question__title"><?=GetMessage('ASK_TEXT')?></div>
                        <p><?=$item['PREVIEW_TEXT']?></p>
                        <div class="faq-list-question__count">
                            <?$lastNum = count($item['ANSWERS']) % 10;?>
                            <a href="#" class="js-show-answer"><i class="icon-comments"></i></a><span><?=GetMessage('COUNT_ANSWERS_' . $lastNum, array('#COUNT#' => count($item['ANSWERS'])))?></span>
                        </div>
                        <div class="text-right">
                            <a href="#popup-answer" data-ask-id="<?=$item['ID']?>" class="button js-fancybox"><?=GetMessage('ANS')?></a>
                        </div>
                    </div>
                </div>
                <?if($item['ANSWERS']):?>
                    <?foreach($item['ANSWERS'] as $itemAns):?>
                        <div class="faq-list__row faq-list__row_answer">
                                <div class="faq-list-question faq-list-answer">
                                    <div class="faq-list-answer__title">
                                        <i class="icon-comments"></i>
                                        <span class="faq-list-answer__name"><?=$itemAns['PROPERTY_NAME_VALUE']?></span>
                                        <?if($itemAns['IS_COMPANY_USER']):?>
                                            <span class="faq-list-answer__prof"> <?=GetMessage('COMPANY_USER')?></span>
                                            <span class="faq-list-answer__logo"><img src="<?=SITE_TEMPLATE_PATH?>/new_img/svg/logo.svg" alt=""></span>
                                        <?endif;?>
                                    </div>
                                    <div class="faq-list-answer__date"><?=$itemAns['DATE_CREATE']?></div>
                                    <p><?=$itemAns['PREVIEW_TEXT']?></p>
                                    <ul class="answer-social">
                                        <li class="answer-social__item">
                                            <a href="#" data-type="like" data-ans-id="<?=$itemAns['ID']?>" data-ask-id="<?=$item['ID']?>" class="answer-social__link"><i class="icon-like"></i></a>
                                            <span><?=$itemAns['PROPERTY_LIKE_VALUE']?:0?></span>
                                        </li>
                                        <li class="answer-social__item">
                                            <a href="#" data-type="dislike" data-ans-id="<?=$itemAns['ID']?>" data-ask-id="<?=$item['ID']?>" class="answer-social__link"><i class="icon-dislike"></i></a>
                                            <span><?=$itemAns['PROPERTY_DIS_LIKE_VALUE']?:0?></span>
                                        </li>
                                    </ul>
                                    <button class="location__close button-close js-close-answer"></button> 
                                </div>
                        </div>
                    <?endforeach?>
                <?endif?>
            </li>


        <?endforeach;?>
    </ul>
    
    <div class="faq-answer-paging-wrap">
        <div class="box-paging faq-answer-paging">
            <a href="#popup-faq" class="button js-fancybox"><?=GetMessage('ASK')?></a>
            <?=$arResult['NAV_STRING'];?>                                       
        </div>
    </div>
<?endif;?>

<div class="popups"> 

    <div class="popup-answer" id="popup-faq">
        <div class="popup__main">
            <div class="rbs-form-content">
                <p class="popup__title">Задать вопрос </p>
                <p class="popup__text">Не нашли ответа на свой вопрос?<br> Задайте его ниже — мы постараемся ответить как можно скорее.</p>
                <div class="notify-form">
                    <form>
                        <input type="hidden" name="itemId" value="<?=$arParams['ITEM_ID']?>"/>
                        <div class="box-field">
                            <div class="input-wrap js-focus">        
                                <input class="input" name="email" type="email" placeholder="Email">
                            </div>
                            <div class="input-info">Адрес не будет виден другим посетителям</div>    
                        </div>
                            <div class="box-field">
                            <div class="input-wrap js-focus">        
                                <input class="input" type="text" name="name" placeholder="" required>
                                <a class="input-fix js-focus-fix" href="javascript:void(0);">Имя<span>*</span></a>
                            </div>    
                        </div>
                            <div class="box-field">
                            <div class="input-wrap js-focus">   
                                <textarea name="ask" class="input" required></textarea>
                                <a class="input-fix js-focus-fix" href="javascript:void(0);">Вопрос<span>*</span></a>
                            </div>    
                        </div>
                        
                        <button class="notify-form__button button button_white" type="submit">Отправить</button>
                    </form>
                    <p class="notify-form__remark"><span>*</span>— Поля, отмеченные звездочкой, обязательные к заполнению</p>
                </div>
            </div>        
        </div>
    </div>
    <div class="popup-answer" id="popup-answer">
        <div class="popup__main">
            <div class="rbs-form-content">
                <p class="popup__title">Ответить на вопрос</p>
                <div class="notify-form">
                    <form>
                        <input type="hidden" name="itemId" value="<?=$arParams['ITEM_ID']?>"/>
                        <input type="hidden" name="askId" value="0"/>
                        <div class="box-field">
                            <div class="input-wrap js-focus">        
                                <input class="input" name="email" type="email" placeholder="Email">
                            </div>
                            <div class="input-info">Адрес не будет виден другим посетителям</div>    
                        </div>
                            <div class="box-field">
                            <div class="input-wrap js-focus">        
                                <input class="input" name="name" type="text" placeholder="" required>
                                <a class="input-fix js-focus-fix" href="javascript:void(0);">Имя<span>*</span></a>
                            </div>    
                        </div>
                            <div class="box-field">
                            <div class="input-wrap js-focus">   
                                <textarea class="input" name="ans" required></textarea>
                                <a class="input-fix js-focus-fix" href="javascript:void(0);">Ответ<span>*</span></a>
                            </div>    
                        </div>
                        
                        <button class="notify-form__button button button_white" type="submit">Отправить</button>
                    </form>
                    <p class="notify-form__remark"><span>*</span>— Поля, отмеченные звездочкой, обязательные к заполнению</p>
                </div>
            </div>
        </div>
    </div>

</div>
<?if($arParams['AJAX'] != 'Y'):?>
    <script>
        <?global ${$arParams['FILTER_NAME']};?>
        var TabAjaxQuick = TabAjaxQuickAskController({
            params: <?=CUtil::PhpToJSObject($arParams);?>,
            filter: <?=CUtil::PhpToJSObject(${$arParams['FILTER_NAME']});?>,
            template: 'tab_ask',
            tabId: '#tab_6'
        }).init();
    </script>
<?endif?>