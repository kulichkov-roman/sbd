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
//echo '<pre>', var_export($arParams,1), '</pre>';

if (strtolower($_POST['rz_ajax']) == 'y') return;
global $rz_b2_options;

include 'functions.php';
$arBoolean = array(
	'bEmptyFilter' => true,
	'bActiveFilters' => false,
	'bShowFullFilters' => false,
	'bManualType' => true,
);
?>
<?// print_r($arResult['DISPLAY_TYPE']); ?>
<form name="<?echo $arResult["FILTER_NAME"]."_form"?>" action="<?= htmlspecialcharsbx($arResult["FORM_ACTION"])?>" method="get" id="form_filter">
    <?foreach($arResult["HIDDEN"] as $arItem):?>
        <input type="hidden" name="<?echo $arItem["CONTROL_NAME"]?>" value="<?echo $arItem["HTML_VALUE"]?>" />
    <?endforeach;?>
    <div class="accordion">
        <?
        $visibleCount = 0;
        $bOpenFilter = false;
        foreach($arResult["ITEMS"] as $key=>$arItem)
        {
            ob_start();
            $bShown = showFilterItem($arItem, $arBoolean, $arParams, $this);
            $filterItem = ob_get_clean();

            if ($visibleCount >= $arParams['VISIBLE_PROPS_COUNT'] && !$bOpenFilter)
            {
                $bOpenFilter = strpos($filterItem, 'data-open-filter') !== false;
            }
            if ($bShown)
            {

                if ($visibleCount == $arParams['VISIBLE_PROPS_COUNT'])
                {
                    $arBoolean['bShowFullFilters'] = true;
                    $allItems .= $filterItem;
                }
                elseif (!$arBoolean['bShowFullFilters'])
                {
                    echo $filterItem;
                }
                else if ($arBoolean['bShowFullFilters'])
                {
                    $allItems .= $filterItem;
                }
                $visibleCount++;
            }
            else
            {
                echo $filterItem;
            }
        }
        ?>
        <?if ($arBoolean['bShowFullFilters']):?>
            <div class="filter-full <?=$bOpenFilter ? 'filter-opened' : ''?>" <?if ($bOpenFilter):?>style="display: block"<?endif?>>
                <?echo $allItems?>
            </div>
            <div class="all__cnt">
                <div class="all">
                    <div class="all__heading btn-toggle-full-filter <?if ($bOpenFilter):?>toggled<?endif?>">
                        <span class="when-minified">
                            <span class="text">
                                <span class="show-icon">+</span>
                                <?=GetMessage('BITRONIC2_BCSF_FILTER_SHOW')?>
                            </span>
                        </span>
                        <span class="when-expanded">
                            <span class="text">
                                <span class="show-icon">-</span>
                                <?=GetMessage('BITRONIC2_BCSF_FILTER_HIDE')?>
                            </span>
                        </span>
                    </div>
                </div>
            </div>
        <?endif?>

    </div>
    <div class="show rbs-filter-shows">
        <p class="filter-results" id="modef" style="display:none">
            <?=GetMessage('BITRONIC2_BCSF_FIND')?> <strong id="modef_num">0</strong> <?=GetMessage('BITRONIC2_BCSF_FIND_GOODS')?>
        </p>
        <button class="show__button button show-results <?=(!$arBoolean['bActiveFilters']) ? ' disabled' : ''?><?= ($arBoolean['bManualType'])? '': ' hide'?>"
                id="set_filter" name="set_filter" value="y"
        >
            <?=GetMessage("BITRONIC2_BCSF_SET_FILTER")?>
        </button>
        <a href="#" class="show__link reset-filter <?=(!$arBoolean['bActiveFilters']) ? ' disabled' : ''?>"
           id="del_filter" name="del_filter" value="y"
            <?if (!empty($arResult['JS_FILTER_PARAMS']['SEF_DEL_FILTER_URL'])):?> data-sef-del="<?= $arResult['JS_FILTER_PARAMS']['SEF_DEL_FILTER_URL']?>"<?endif?>
        >
            <?=GetMessage("BITRONIC2_BCSF_DEL_FILTER")?>
        </a>
    </div>
    <script>
        var smartFilter = new JCSmartFilter('<?echo CUtil::JSEscape($arResult["FORM_ACTION"])?>', 'form_filter',
            <?=(!$arBoolean['bManualType']       ?'true':'false')?>,
            <?=($arParams['HIDE_DISABLED_PROPS']?'true':'false')?>);
        smartFilter.brandPropCode = "<?=$arParams['BRAND_PROP_CODE']?>";
    </script>
</form>
