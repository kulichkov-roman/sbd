<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
if(!function_exists('get_avg_luminance')){
    function get_avg_luminance($filename, $num_samples=10) {
        $img = imagecreatefromjpeg($filename);

        $width = imagesx($img);
        $height = imagesy($img);

        $x_step = intval($width/$num_samples);
        $y_step = intval($height/$num_samples);

        $total_lum = 0;

        $sample_no = 1;

        for ($x=0; $x<$width; $x+=$x_step) {
            for ($y=0; $y<$height; $y+=$y_step) {

                $rgb = imagecolorat($img, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;

                // choose a simple luminance formula from here
                // http://stackoverflow.com/questions/596216/formula-to-determine-brightness-of-rgb-color
                $lum = ($r+$r+$b+$g+$g+$g)/6;

                $total_lum += $lum;

                // debugging code
     //           echo "$sample_no - XY: $x,$y = $r, $g, $b = $lum<br />";
                $sample_no++;
            }
        }

        // work out the average
        $avg_lum  = $total_lum/$sample_no;

        return $avg_lum;
    }
}
if (!function_exists('showFilterItem'))
{
    function showFilterItem($arItem, &$arBoolean, $arParams, $obTemplate)
    {
        //echo '<pre>', var_export($arItem, 1), '</pre>';
        $bSticker = ($arItem['CODE'] == 'RZ_STICKERS');
        $arItem["DISPLAY_TYPE"] = $arItem["DISPLAY_TYPE"] ? : 'F';
        $isColor = $arItem["CODE"] == "TSVET";
        if (isset($arItem["PRICE"]))
        {
            $type = "PRICE";
        }
        else
        {
            switch ($arItem["DISPLAY_TYPE"]) {
                case 'A':
                case 'B': $type = "SLIDER";   break;
                case 'P':
                case 'R': $type = "SELECT";   break;
                case 'K': $type = 'RADIO';    break;
                default:  $type = "CHECKBOX"; break;
            }
            if ($bBrandBlock) $type = 'CHECKBOX';
        }

        if($isColor){
            $type = "COLOR";            
            //global $USER; if($USER->IsAdmin()){echo '<pre>'; print_r($arItem); echo '</pre>';};
        }
        $bExpanded = !array_key_exists('DISPLAY_EXPANDED', $arItem) || ($arItem['DISPLAY_EXPANDED'] == 'Y');
        $bShowImg = in_array($arItem['DISPLAY_TYPE'], array('G','H','R'));
        $bShowText = in_array($arItem['DISPLAY_TYPE'], array('F','H','K','P','R'));
        switch($type)
        {
            case "COLOR":
                if (!empty($arItem["VALUES"]))
                {       
                    ?>
                    <div class="accordion__item">
                        <div class="accordion__heading">
                            <div class="accordion__head"><?=$arItem['NAME']?></div>
                            <div class="accordion__arrow rotate"></div>
                        </div>

                        <div class="accordion__text" style="display:block;">
                            <ul class="list-check">
                                <? $cCount = 0; ?>
                                <?foreach($arItem["VALUES"] as $keyVal => $arValue):
                                    //global $USER; if($USER->IsAdmin()){echo '<pre>'; print_r($arValue["FILE"]); echo '</pre>';};
                                    $img = '';
                                    if(strlen($arValue["FILE"]['SRC']) != 0){
                                        $img = CResizer2Resize::ResizeGD2($arValue["FILE"]['SRC'], $arParams['RESIZER_FILTER']);
                                    } else {
                                        continue;
                                    }            

                                    $il = 180;
                                    if ($img) {
                                        $il = get_avg_luminance($_SERVER['DOCUMENT_ROOT'] . $img);
                                    }

                                    $bDisabled = $arValue["DISABLED"] && !$arValue["CHECKED"];
                                    if (!$arBoolean['bActiveFilters'] && $arValue["CHECKED"])
                                    {
                                        $arBoolean['bActiveFilters'] = true;
                                    }?>
                                    <li class="rbs-clr-item <?=$il < 170 ? 'rbs-clr-white':''?>">
                                        <label class="list-check__label rbs-clr-label <?=$arValue["CHECKED"]?'rbs-clr-label-choosen':''?>" onclick="smartFilter.click(this)">
                                            <input type="checkbox" value="<?=$arValue["HTML_VALUE"]?>" class="check-color-input"
                                                name="<?echo $arValue["CONTROL_NAME"]?>" id="<?echo $arValue["CONTROL_ID"]?>"
                                                    <?echo $arValue["CHECKED"] ? 'checked="checked"' : ''?>
                                                    <?echo $arValue["CHECKED"] ? 'data-open-filter="Y"' : ''?>
                                                    <?echo $bDisabled ? 'disabled="disabled"': ''?>
                                            />                                           
                                            <div class="check_color_img_container">
                                                <img
                                                    <?if(!$bShowText):?>
                                                        data-tooltip title="<?=$arValue["VALUE"];?>"
                                                        data-placement="right"
                                                    <?endif?>
                                                    title="<?=$arValue["VALUE"];?>"
                                                    alt="<?=$arValue["VALUE"];?>"
                                                    class="check-color-img_color"
                                                    src="<?=$img?>"
                                                >
                                            </div>
                                            <div class="rbs-clr-name">
                                                <?=$arValue["VALUE"]?>
                                            </div>
                                            <?/*if ($arParams["DISPLAY_ELEMENT_COUNT"] !== "N" && isset($arValue["ELEMENT_COUNT"])):?>
                                                <span class="list-check__tooltip" data-role="count_<?=$arValue["CONTROL_ID"]?>">
                                                    Найдено <span class="count"><?=$arValue["ELEMENT_COUNT"]?></span> объектов
                                                </span>
                                            <?endif*/?>
                                        </label>
                                    </li>
                                    <? $cCount++; ?>
                                <?endforeach?>
                            </ul>
                        </div>
                    </div>
                    <?
                }
                else
                {
                    return false;
                }
            break;
            case "CHECKBOX":
                if (!empty($arItem["VALUES"]))
                {
                    ?>
                    <div class="accordion__item">
                        <div class="accordion__heading">
                            <div class="accordion__head"><?=$arItem['NAME']?></div>
                            <div class="accordion__arrow rotate"></div>
                        </div>

                        <div class="accordion__text" style="display:block;">
                            <ul class="list-check">
                                <?foreach($arItem["VALUES"] as $keyVal => $arValue):
                                    $bDisabled = $arValue["DISABLED"] && !$arValue["CHECKED"];
                                    if (!$arBoolean['bActiveFilters'] && $arValue["CHECKED"])
                                    {
                                        $arBoolean['bActiveFilters'] = true;
                                    }?>
                                    <li class="list-check__item">
                                        <label class="list-check__label" onclick="smartFilter.click(this)">
                                            <input type="checkbox" value="<?=$arValue["HTML_VALUE"]?>" class="js-formstyler"
                                                   name="<?echo $arValue["CONTROL_NAME"]?>" id="<?echo $arValue["CONTROL_ID"]?>"
                                                    <?echo $arValue["CHECKED"] ? 'checked="checked"' : ''?>
                                                    <?echo $arValue["CHECKED"] ? 'data-open-filter="Y"' : ''?>
                                                    <?echo $bDisabled ? 'disabled="disabled"': ''?>
                                            />
                                            <?if($bSticker && $arValue['CLASS']):?>
                                                <?echo $arValue['CLASS'];?>
                                            <?else:?>
                                                <span class="label-text"><?=$arValue["VALUE"];?></span>
                                            <?endif?>

                                            <?if ($arParams["DISPLAY_ELEMENT_COUNT"] !== "N" && isset($arValue["ELEMENT_COUNT"])):?>
                                                <span class="list-check__tooltip" data-role="count_<?=$arValue["CONTROL_ID"]?>">
                                                    Найдено <span class="count"><?=$arValue["ELEMENT_COUNT"]?></span> объектов
                                                </span>
                                            <?endif?>
                                        </label>
                                    </li>
                                <?endforeach?>
                            </ul>
                        </div>
                    </div>
                    <?
                }
                else
                {
                    return false;
                }
            break;

            case 'RADIO':
                if (!empty($arItem["VALUES"]))
                {
                    $arCur = current($arItem['VALUES']);
                    ?><div class="filter-section allow-multiple-expanded<?=$bExpanded ? ' expanded' : ''?><?=($bBrandBlock?' brands':'')?><?=($arItem['HIDDEN']?' hidden':'')?>">
                        <header>
                            <span class="text"><?=$arItem['NAME']?></span>
                            <?if(!empty($arItem['FILTER_HINT'])):?>
                            <sup class="help" title="<?=$arItem['FILTER_HINT']?>" data-tooltip>?</sup>
                            <?endif?>
                            <button type="button" class="btn-expand"></button>
                        </header>
                        <div class="expand-content content-with-scroll">
                            <div class="baron__scroller" data-line-height="27" data-line="<?=$arParams['FILTER_SHOW_CNT']?>">
                                <label class="radio-styled">
                                    <input
                                        type="radio"
                                        value=""
                                        name="<?echo $arCur["CONTROL_NAME_ALT"]?>"
                                        id="<?echo "all_".$arCur["CONTROL_ID"]?>"
                                        onclick="smartFilter.click(this)"
                                    />
                                    <span class="radio-content">
                                        <span class="radio-fake"></span>
                                        <span class="text"><?=GetMessage("BITRONIC2_BCSF_FILTER_ALL")?></span>
                                    </span>
                                </label>
                                <?foreach($arItem["VALUES"] as $keyVal => $arValue):
                                    $bDisabled = $arValue["DISABLED"] && !$arValue["CHECKED"];
                                    if(!$arBoolean['bActiveFilters'] && $arValue["CHECKED"])
                                    {
                                        $arBoolean['bActiveFilters'] = true;
                                    }?>
                                    <label class="radio-styled<?=($bDisabled && $arParams['HIDE_DISABLED_PROPS'])?' hidden':''?>">
                                        <input
                                            type="radio"
                                            value="<?=$arValue["HTML_VALUE_ALT"]?>"
                                            name="<?echo $arValue["CONTROL_NAME_ALT"]?>"
                                            id="<?echo $arValue["CONTROL_ID"]?>"
                                            <?echo $arValue["CHECKED"]? 'checked="checked"': ''?>
                                            <?echo $arValue["CHECKED"]? 'data-open-filter="Y"' : ''?>

                                            <?echo $bDisabled ? 'disabled="disabled"': ''?>

                                            onclick="smartFilter.click(this)"
                                        />
                                        <span class="radio-content">
                                            <span class="radio-fake"></span>
                                            <?if($bShowImg && isset($arValue["FILE"]['SRC']) && !empty($arValue["FILE"]['SRC'])):?>
                                                <img
                                                    <?if(!$bShowText):?>
                                                        data-tooltip title="<?=$arValue["VALUE"];?>"
                                                        data-placement="right"
                                                    <?endif?>
                                                    title="<?=$arValue["VALUE"];?>"
                                                    alt="<?=$arValue["VALUE"];?>"
                                                    height="20"
                                                    src="<?=CResizer2Resize::ResizeGD2($arValue["FILE"]['SRC'], $arParams['RESIZER_FILTER'])?>"
                                                >
                                            <?endif?>
                                            <?if($bShowText):?>
                                                <span class="text"><?=$arValue["VALUE"];?><?
                                                if ($arParams["DISPLAY_ELEMENT_COUNT"] !== "N" && isset($arValue["ELEMENT_COUNT"])):
                                                    ?><sup data-role="count_<?=$arValue["CONTROL_ID"]?>">&nbsp;<?=$arValue["ELEMENT_COUNT"]?></sup><?
                                                endif;?></span>
                                            <?endif?>
                                        </span>
                                    </label>
                                <?endforeach;?>
                                <div class="scroller__track scroller__track_v">
                                    <div class="scroller__bar scroller__bar_v"></div>
                                </div>
                            </div>
                        </div>
                    </div><?
                }
                else
                {
                    return false;
                }
            break;

            case 'SELECT':
                if (!empty($arItem["VALUES"]))
                {
                    $arCur = current($arItem['VALUES']);
                    ?><div class="filter-section allow-multiple-expanded<?=$bExpanded ? ' expanded' : ''?><?=($bBrandBlock?' brands':'')?><?=($arItem['HIDDEN']?' hidden':'')?>">
                        <header>
                            <span class="text"><?=$arItem['NAME']?></span>
                            <?if(!empty($arItem['FILTER_HINT'])):?>
                            <sup class="help" title="<?=$arItem['FILTER_HINT']?>" data-tooltip>?</sup>
                            <?endif?>
                            <button type="button" class="btn-expand"></button>
                        </header>
                        <div class="expand-content">
                            <select name="<?echo $arCur["CONTROL_NAME_ALT"]?>" id="<?echo $arCur["CONTROL_ID"]?>" onchange="smartFilter.click(this)"<?if($arParams['HIDE_DISABLED_PROPS']):?> data-customclass="sku"<?endif?>>
                                <option value=""><?=GetMessage('BITRONIC2_BCSF_FILTER_ALL')?></option>
                                <?foreach($arItem["VALUES"] as $keyVal => $arValue):
                                    if(!$arBoolean['bActiveFilters'] && $arValue["CHECKED"])
                                    {
                                        $arBoolean['bActiveFilters'] = true;
                                    }?>

                                    <option
                                        value="<?=$arValue["HTML_VALUE_ALT"]?>" <?
                                        echo $arValue["CHECKED"]? 'selected="selected"': ''?>
                                        <?echo $arValue["CHECKED"]? 'data-open-filter="Y"' : ''?><?
                                        echo $arValue["DISABLED"] && !$arValue["CHECKED"]? 'disabled="disabled"': ''?><?
                                        if($bShowImg && isset($arValue["FILE"]['SRC']) && !empty($arValue["FILE"]['SRC'])):?>

                                        data-img="<?=CResizer2Resize::ResizeGD2($arValue["FILE"]['SRC'], $arParams['RESIZER_FILTER'])?>"<?
                                        endif?>>
                                        <?if($bShowText):?>
                                            <?=$arValue["VALUE"];?><?
                                            if ($arParams["DISPLAY_ELEMENT_COUNT"] !== "N" && isset($arValue["ELEMENT_COUNT"])):?>
                                            (<?=$arValue["ELEMENT_COUNT"]?>)<?
                                            endif;?>
                                        <?endif?>

                                    </option>
                                <?endforeach;?>
                            </select>
                        </div>
                    </div><?
                }
                else
                {
                    return false;
                }
            break;

            case "SLIDER":
                $bSlider = ($arItem['DISPLAY_TYPE'] !== 'B');

            case "PRICE":
                if (!isset($arItem["VALUES"]["MIN"]["VALUE"])
                || !isset($arItem["VALUES"]["MAX"]["VALUE"])
                //|| 0 >    $arItem["VALUES"]["MIN"]["VALUE"]
                || 0 >    $arItem["VALUES"]["MAX"]["VALUE"]
                || 0 >=   $arItem["VALUES"]["MAX"]["VALUE"] - $arItem["VALUES"]["MIN"]["VALUE"])
                {
                    return false;
                }

                if (!isset($bSlider)) {
                    $bSlider = true;
                }

                $sliderId = "slider_".((isset($arItem["PRICE"])) ? 'price_' : '').$arItem['ID'];
                $min = (empty($arItem["VALUES"]["MIN"]["HTML_VALUE"])) ? "" : round($arItem["VALUES"]["MIN"]["VALUE"]);
                $max = (empty($arItem["VALUES"]["MAX"]["HTML_VALUE"])) ? "" : round($arItem["VALUES"]["MAX"]["VALUE"]);
                if ($min != '' || $max != '') $arBoolean['bActiveFilters'] = true;
                ?>
                <div class="accordion__item active">
                    <div class="accordion__heading">
                        <div class="accordion__head"><?=$arItem["NAME"]?></div>
                        <div class="accordion__arrow rotate"></div>
                    </div>
                    <div class="accordion__text" style="display: block;">
                        <div class="range">
                            <div class="range__inputs">
                                <div class="range__input">
                                    <span class="text"><?=GetMessage('BITRONIC2_BCSF_FILTER_FROM')?></span>
                                    <input type="text" name="<?echo $arItem["VALUES"]["MIN"]["CONTROL_NAME"]?>"
                                           id="<?=$arItem["VALUES"]["MIN"]["CONTROL_ID"]?>" value="<?=$min?>"
                                           size="5" onkeyup="smartFilter.keyup(this)"
                                           data-range-min="<?=$arItem["VALUES"]["MIN"]["VALUE"]?>"
                                           class="range-input-lower"
                                        <?=$arItem["VALUES"]["MIN"]["VALUE"] != $min && !empty($min) ? 'data-open-filter="Y"' : ''?> <?if(!$bSlider):?>placeholder="<?=$arItem["VALUES"]["MIN"]["VALUE"]?>"<?endif?>
                                    >
                                </div>
                                <div class="range__betw"></div>
                                <div class="range__input">
                                    <span class="text"><?=GetMessage('BITRONIC2_BCSF_FILTER_TO')?></span>
                                    <input type="text" name="<?echo $arItem["VALUES"]["MAX"]["CONTROL_NAME"]?>"
                                           id="<?=$arItem["VALUES"]["MAX"]["CONTROL_ID"]?>" value="<?=$max?>"
                                           size="5" onkeyup="smartFilter.keyup(this)"
                                           class="range-input-upper"
                                           data-range-max="<?=$arItem["VALUES"]["MAX"]["VALUE"]?>" <?if(!$bSlider):?>placeholder="<?=$arItem["VALUES"]["MAX"]["VALUE"]?>"
                                        <?=$arItem["VALUES"]["MAX"]["VALUE"] != $max && !empty($min) ? 'data-open-filter="Y"' : ''?><?endif?>
                                    >
                                </div>
                            </div>
                        </div>
                        <div style="padding:0 10px;"><div id="<?=$sliderId?>"></div></div>
                        <?
                        //global $USER; if($USER->IsAdmin()){echo '<pre>'; print_r($arItem["VALUES"]); echo '</pre>';};
                        $arSliderParams = array(
                            "SLIDER_ID" => $sliderId,
                            "VALUES" => array(
                                'MIN'=>$arItem["VALUES"]["MIN"]["VALUE"],
                                'MAX'=>$arItem["VALUES"]["MAX"]["VALUE"]
                            ),
                            "HTML_VALUES" => array(
                                'MIN'=>$arItem["VALUES"]["MIN"]["HTML_VALUE"],
                                'MAX'=>$arItem["VALUES"]["MAX"]["HTML_VALUE"]
                            ),
                            "FILTERED_VALUES" => array(
                                'MIN'=>intval($arItem["VALUES"]["MIN"]["FILTERED_VALUE"]) ? $arItem["VALUES"]["MIN"]["FILTERED_VALUE"] : $arItem["VALUES"]["MIN"]["VALUE"],
                                'MAX'=>intval($arItem["VALUES"]["MAX"]["FILTERED_VALUE"]) ? $arItem["VALUES"]["MAX"]["FILTERED_VALUE"] : $arItem["VALUES"]["MAX"]["VALUE"]
                            ),
                            "INPUT_ID" => array(
                                'MIN'=>$arItem["VALUES"]["MIN"]["CONTROL_ID"],
                                'MAX'=>$arItem["VALUES"]["MAX"]["CONTROL_ID"]
                            ),
                        );?>
                        <script type="text/javascript">
                            createSlider(<? echo CUtil::PhpToJSObject($arSliderParams, false, true); ?>);
                        </script>
                    </div>
                </div>
                <?
            break;
        }

        return true;
    }
}