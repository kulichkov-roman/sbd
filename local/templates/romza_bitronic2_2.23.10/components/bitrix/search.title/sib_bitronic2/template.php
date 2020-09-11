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
use Bitrix\Main\Page\Asset;
use \Bitronic2\Mobile;

$asset = Asset::getInstance();
$asset->addJs(SITE_TEMPLATE_PATH."/js/back-end/handlers/search_handler.js");

$this->setFrameMode(true);
$INPUT_ID = trim($arParams["~INPUT_ID"]);
if(strlen($INPUT_ID) <= 0)
    $INPUT_ID = "search-field";
$INPUT_ID = CUtil::JSEscape($INPUT_ID);

$CONTAINER_ID = trim($arParams["~CONTAINER_ID"]);
if(strlen($CONTAINER_ID) <= 0)
    $CONTAINER_ID = "title-search";
$CONTAINER_ID = CUtil::JSEscape($CONTAINER_ID);

$bSelect = ($arParams['SHOW_CATEGORY_SWITCH'] !== 'N');
$example = true;
?>

<div class="search-form">
    <form action="<?echo $arResult["FORM_ACTION"]?>" method="get" id="<?echo $CONTAINER_ID?>">
        <?$frame = $this->createFrame($CONTAINER_ID, false)->begin('');?>
            <input type="hidden" name="where" id="search-area" value="iblock_catalog">
            <div class="search-form__cols" id="rbs_search_form_cols">
            
                <?if(\Bitronic2\Mobile::isMobile()):?>
                    <div class="search-form__mob js-click">
                        <button class="search-form__button js-click-button"></button>
                        <div class="search-form__cnt js-click-hide">
                            <button class="search-form__close button-close js-click-close"></button>
                            <div class="search-form-cnt">
                                <?if(\Bitronic2\Mobile::isMobile()):?>
                                    <form>
                                        <button type="submit" name="s" class="search-form__btn"></button>
                                        <input class="search-form-mob__input" id="<?echo $INPUT_ID?>" name="q" type="text" placeholder="Поиск товара" required autocomplete="off">
                                        <button class="search-form__reset" type="reset"></button>
                                    </form>
                                <?endif;?>
                            </div>
                            <div class="popup_ajax-search mobile-result-search" id="popup_ajax-search"></div>
                        </div>
                    </div>
                <?else:?>
                    <div class="search-form__col">
                    <?if($arParams["EXAMPLE_ENABLE"] == "Y"):
                        $list = $arParams["EXAMPLES"];
                        if(!end($list))
                            array_pop($list);
                        $example = $list[array_rand($list)];
                        if($example):
                            $id = 'bxdinamic_bitronic2_search_example';
                            ?>
                            <span class="search-example-wrap<?=(isset($_REQUEST['q'])?' hidden':'')?>"><?=GetMessage('BITRONIC2_EXAMPLE');?>
                                <span id="<?=$id?>">
                                    <?//$frame = $this->createFrame($id, false)->begin(CRZBitronic2Composite::insertCompositLoader());?>
                                        <span class="search-example"><?echo $example;?></span>
                                    <?//$frame->end();?>
                                </span>
                            </span>
                    <?endif;endif;?>
                        <input type="hidden" name="type" value="new">
                        <input class="search-form__input" type="text" id="<?echo $INPUT_ID?>" name="q" value="<?=htmlspecialcharsbx($_REQUEST["q"])?>" autocomplete="off" <?if(!$example):?> placeholder="<?=GetMessage('BITRONIC2_SEARCH_PLACEHOLDER')?>"<?endif?> required>
                    </div>
                    <div class="search-form__col">
                        <button class="search-form__button button" name="s" type="submit">Найти</button>
                    </div>
                <?endif;?>
            
            </div>
            <?if(!\Bitronic2\Mobile::isMobile()):?>
                <div class="popup_ajax-search" id="popup_ajax-search"></div>
            <?endif;?>
            <script>
                searchAreaHandler ($);
                
                /* jQuery(window).load(function(){
                    require(['back-end/ajax/search'], function(){ */
                        new JCRZB2TitleSearch({
                            'AJAX_PAGE' : '<?echo CUtil::JSEscape($arResult["FORM_ACTION"])?>',
                            'CONTAINER_ID': '<?echo $CONTAINER_ID?>',
                            'RESULT_ID': 'popup_ajax-search',
                            'INPUT_ID': '<?echo $INPUT_ID?>',
                            'MIN_QUERY_LEN': 2,
                            'SEARCH_PAGE': '<?echo $arParams['PAGE'] == $APPLICATION->GetCurPage() ? 'Y' : 'N'?>'
                        });
                    /* });
                }); */
                BASKET_URL = '<?=$arParams['BASKET_URL']?>';
            </script>
        <?$frame->end();?>
    </form>
</div>

