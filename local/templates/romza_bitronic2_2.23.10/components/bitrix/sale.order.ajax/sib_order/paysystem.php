 <?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?CModule::IncludeModule('yenisite.resizer2');?>
<? /* шаблон edost - НАЧАЛО */ ?>
<? if (isset($arResult['edost']['format'])) { ?>

    <script type="text/javascript">
        function changePaySystem(param) {
            if (BX("account_only") && BX("PAY_CURRENT_ACCOUNT"))
                if (BX("account_only").value == 'Y') {
                    if (param == 'account') {
                        if (BX("PAY_CURRENT_ACCOUNT").checked) BX("PAY_CURRENT_ACCOUNT").setAttribute("checked", "checked");
                        else BX("PAY_CURRENT_ACCOUNT").removeAttribute("checked");

                        var el = document.getElementsByName("PAY_SYSTEM_ID");
                        for(var i = 0; i < el.length; i++) el[i].checked = false;
                    }
                    else {
                        BX("PAY_CURRENT_ACCOUNT").checked = false;
                        BX("PAY_CURRENT_ACCOUNT").removeAttribute("checked");
                    }
                }
                else if (BX("account_only").value == 'N') {
                    if (param == 'account') {
                        if (BX("PAY_CURRENT_ACCOUNT").checked) BX("PAY_CURRENT_ACCOUNT").setAttribute("checked", "checked");
                        else BX("PAY_CURRENT_ACCOUNT").removeAttribute("checked");
                    }
                }

            submitForm();
        }
    </script>

    <? if ((!empty($arResult['PAY_SYSTEM']) || $arResult['PAY_FROM_ACCOUNT'] == 'Y') && !empty($arResult['edost']['format']['active']['id'])) { ?>
        <div class="edost edost_main edost_template_div"<?=(!empty($arResult['edost']['format']['active']['cod_tariff']) ? ' style="display: none;"' : '')?>>
            <?
            if (!isset($table_width)) $table_width = 645;
            $hide_radio = (count($arResult['PAY_SYSTEM']) == 1 && $arResult['PAY_FROM_ACCOUNT'] != 'Y' ? true : false);
            $ico_default = $templateFolder.'/images/logo-default-ps.gif';
            ?>
            <h4><?=GetMessage("SOA_TEMPL_PAY_SYSTEM")?></h4>

            <div style="width: <?=$table_width?>px;">
                <?
                $i2 = 0;
                if ($arResult['PAY_FROM_ACCOUNT'] == 'Y') {
                    $i2++;
                    $id = 'PAY_CURRENT_ACCOUNT';
                    $accountOnly = ($arParams['ONLY_FULL_PAY_FROM_ACCOUNT'] == 'Y') ? 'Y' : 'N';
                    ?>
                    <input type="hidden" id="account_only" value="<?=$accountOnly?>">
                    <input type="hidden" name="PAY_CURRENT_ACCOUNT" value="N">
                    <table width="100%" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td class="edost_format_ico" width="95">
                                <input class="edost_format_radio" type="checkbox" name="<?=$id?>" id="<?=$id?>" value="Y" <?=($arResult['USER_VALS']['PAY_CURRENT_ACCOUNT'] == 'Y' ? 'checked="checked"' : '')?> onclick="changePaySystem('account');">

                                <?					if (!empty($ico_default)) { ?>
                                    <label class="edost_format_radio" for="<?=$id?>"><img class="edost_ico edost_ico_normal" src="<?=$ico_default?>" border="0"></label>
                                <?					} else { ?>
                                    <div class="edost_ico"></div>
                                <?					} ?>
                            </td>
                            <td class="edost_format_tariff">
                                <label for="<?=$id?>">
                                    <span class="edost_format_tariff"><?=GetMessage('SOA_TEMPL_PAY_ACCOUNT')?></span>

                                    <div class="edost_format_description edost_description"><?=GetMessage('SOA_TEMPL_PAY_ACCOUNT1').' <b>'.$arResult['CURRENT_BUDGET_FORMATED']?></b></div>
                                    <div class="edost_format_description edost_description">
                                        <?=($arParams['ONLY_FULL_PAY_FROM_ACCOUNT'] == 'Y' ? GetMessage('SOA_TEMPL_PAY_ACCOUNT3') : GetMessage('SOA_TEMPL_PAY_ACCOUNT2'))?>
                                    </div>
                                </label>
                            </td>
                        </tr>
                    </table>
                <?	}

                foreach($arResult['PAY_SYSTEM'] as $v) {
                    if ($i2 != 0) echo '<div class="edost_delimiter edost_delimiter_ms"></div>';
                    $i2++;

                    $id = 'ID_PAY_SYSTEM_ID_'.$v['ID'];
                    $value = $v['ID'];
                    $checked = ($v['CHECKED'] == 'Y' && !($arParams['ONLY_FULL_PAY_FROM_ACCOUNT'] == 'Y' && $arResult['USER_VALS']['PAY_CURRENT_ACCOUNT'] == 'Y') ? true : false);

                    if (!empty($v['PSA_LOGOTIP']['SRC'])) $ico = $v['PSA_LOGOTIP']['SRC'];
                    else if (!empty($ico_default)) $ico = $ico_default;
                    else $ico = false;
                    ?>
                    <table width="100%" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td class="edost_format_ico" width="<?=($hide_radio ? '70' : '95')?>">
                                <input class="edost_format_radio" <?=($hide_radio ? 'style="display: none;"' : '')?> type="radio" id="<?=$id?>" name="PAY_SYSTEM_ID" value="<?=$value?>" <?=($checked ? 'checked="checked"' : '')?> onclick="changePaySystem();">

                                <?					if ($ico !== false) { ?>
                                    <label class="edost_format_radio" for="<?=$id?>"><img class="edost_ico edost_ico_normal" src="<?=$ico?>" border="0"></label>
                                <?					} else { ?>
                                    <div class="edost_ico"></div>
                                <?					} ?>
                            </td>

                            <td class="edost_format_tariff">
                                <label for="<?=$id?>">
                                    <span class="edost_format_tariff"><?=$v['PSA_NAME']?></span>

                                    <?					if (!empty($v['DESCRIPTION'])) { ?>
                                        <div class="edost_format_description edost_description"><?=nl2br($v['DESCRIPTION'])?></div>
                                    <?					} ?>

                                    <?					if (!empty($v['PRICE'])) { ?>
                                        <div class="edost_format_description edost_warning">
                                            <?=str_replace('#PAYSYSTEM_PRICE#', SaleFormatCurrency(roundEx($v['PRICE'], SALE_VALUE_PRECISION), $arResult['BASE_LANG_CURRENCY']), GetMessage('SOA_TEMPL_PAYSYSTEM_PRICE'))?>
                                        </div>
                                    <?					} ?>

                                    <?					if (!empty($v['codplus'])) { ?>
                                        <div class="edost_format_description edost_description"><?=$v['codplus']?></div>
                                    <?					} ?>

                                    <?					if (!empty($v['transfer'])) { ?>
                                        <div class="edost_format_description edost_warning"><?=$v['transfer']?></div>
                                    <?					} ?>

                                    <?					if (!empty($v['codtotal'])) { ?>
                                        <div class="edost_format_description edost_description"><?=$v['codtotal']?></div>
                                    <?					} ?>
                                </label>
                            </td>
                        </tr>
                    </table>
                <?	} ?>

            </div>

        </div>
    <? } ?>

<? } ?>
<? /* шаблон edost - КОНЕЦ */ ?>




<? /* шаблон bitronic (на базе 2.2) - НАЧАЛО */ ?>
<? if (!isset($arResult['edost']['format'])) { ?>

    <script type="text/javascript">
        function changePaySystem(param)
        {
            if (BX("account_only") && BX("account_only").value == 'Y') // PAY_CURRENT_ACCOUNT checkbox should act as radio
            {
                if (param == 'account')
                {
                    if (BX("PAY_CURRENT_ACCOUNT"))
                    {
                        BX("PAY_CURRENT_ACCOUNT").checked = true;
                        BX("PAY_CURRENT_ACCOUNT").setAttribute("checked", "checked");
                        BX.addClass(BX("PAY_CURRENT_ACCOUNT_LABEL"), 'selected');

                        // deselect all other
                        var el = document.getElementsByName("PAY_SYSTEM_ID");
                        for(var i=0; i<el.length; i++)
                            el[i].checked = false;
                    }
                }
                else
                {
                    BX("PAY_CURRENT_ACCOUNT").checked = false;
                    BX("PAY_CURRENT_ACCOUNT").removeAttribute("checked");
                    BX.removeClass(BX("PAY_CURRENT_ACCOUNT_LABEL"), 'selected');
                }
            }
            else if (BX("account_only") && BX("account_only").value == 'N')
            {
                if (param == 'account')
                {
                    if (BX("PAY_CURRENT_ACCOUNT"))
                    {
                        BX("PAY_CURRENT_ACCOUNT").checked = !BX("PAY_CURRENT_ACCOUNT").checked;

                        if (BX("PAY_CURRENT_ACCOUNT").checked)
                        {
                            BX("PAY_CURRENT_ACCOUNT").setAttribute("checked", "checked");
                            BX.addClass(BX("PAY_CURRENT_ACCOUNT_LABEL"), 'selected');
                        }
                        else
                        {
                            BX("PAY_CURRENT_ACCOUNT").removeAttribute("checked");
                            BX.removeClass(BX("PAY_CURRENT_ACCOUNT_LABEL"), 'selected');
                        }
                    }
                }
            }

            submitForm();
        }
    </script>

    <div class="order-step__heading">                                    
        <div class="order-step__head"><span>3</span><?=GetMessage('RBS_PAYSYS')?></div>
        <div class="order-step__icon"></div>    
    </div>    
    <!-- <div class="payment-system-type row"> -->
    <div class="order-step__cont">
        <div class="payment-list arrows-2 js-payment-options">
            <?
            if (false && $arResult["PAY_FROM_ACCOUNT"] == "Y")
            {
                $accountOnly = ($arParams["ONLY_FULL_PAY_FROM_ACCOUNT"] == "Y") ? "Y" : "N";
                ?>
                <input type="hidden" id="account_only" value="<?=$accountOnly?>" />
                <div class="col-xs-12 pay-from-inner-wrap">
                    <input type="hidden" name="PAY_CURRENT_ACCOUNT" value="N">
                    <label class="checkbox-styled" onclick="changePaySystem('account');">
                        <input type="checkbox" name="PAY_CURRENT_ACCOUNT" id="PAY_CURRENT_ACCOUNT" value="Y"<?if($arResult["USER_VALS"]["PAY_CURRENT_ACCOUNT"]=="Y") echo " checked=\"checked\"";?>>
                        <span class="checkbox-content">
                            <i class="flaticon-check14"></i>
                            <?=GetMessage("BITRONIC2_SOA_TEMPL_PAY_ACCOUNT")?>
                            <div> - <?=GetMessage("BITRONIC2_SOA_TEMPL_PAY_ACCOUNT1")." <b>".$arResult["CURRENT_BUDGET_FORMATED"]?></b></div>
                            <div>
                                <? if ($arParams["ONLY_FULL_PAY_FROM_ACCOUNT"] == "Y"):?>
                                    <?=GetMessage("BITRONIC2_SOA_TEMPL_PAY_ACCOUNT3")?>
                                <? else:?>
                                    <?=GetMessage("BITRONIC2_SOA_TEMPL_PAY_ACCOUNT2")?>
                                <? endif;?>
                            </div>
                        </span>
                    </label>
                </div>
                <?
            }

            uasort($arResult["PAY_SYSTEM"], "cmpBySort"); // resort arrays according to SORT value

            foreach($arResult["PAY_SYSTEM"] as $arPaySystem)
            {
                //if($arPaySystem['ID'] == 15) continue;
                if (in_array($arPaySystem["ID"], array(15, 19)) && $_SESSION["VREGIONS_REGION"]["ID"] == 14646) continue;
                global $activeChoose;
                if($arPaySystem["CHECKED"]=="Y"){
                    $activeChoose['PAYSYSTEM'] = $arPaySystem["PSA_NAME"];
                }

                if (strlen(trim(str_replace("<br />", "", $arPaySystem["DESCRIPTION"]))) > 0 || intval($arPaySystem["PRICE"]) > 0)
                {
                    if (count($arResult["PAY_SYSTEM"]) == 1)
                    {
                        if (count($arPaySystem["PSA_LOGOTIP"]) > 0):
                            $imgUrl = $arPaySystem["PSA_LOGOTIP"]["SRC"];
                        else:
                            $imgUrl = $templateFolder."/images/logo-default-ps.gif";
                        endif;
                        ?>
                            <div class="payment-list__item">
                                <input type="radio"
                                    id="ID_PAY_SYSTEM_ID_<?=$arPaySystem["ID"]?>"
                                    name="PAY_SYSTEM_ID"
                                    value="<?=$arPaySystem["ID"]?>"
                                    <?if ($arPaySystem["CHECKED"]=="Y" && !($arParams["ONLY_FULL_PAY_FROM_ACCOUNT"] == "Y" && $arResult["USER_VALS"]["PAY_CURRENT_ACCOUNT"]=="Y")) echo " checked=\"checked\"";?>
                                    onclick="changePaySystem();"
                                />
                                <span onclick="BX('ID_PAY_SYSTEM_ID_<?=$arPaySystem['ID']?>').checked=true;changePaySystem();" class="js-payment-item payment-item <?=$arPaySystem["CHECKED"]=="Y"?'active':''?>">
                                    <div class="payment-item__img">
                                        <img src="<?=CResizer2Resize::ResizeGD2($imgUrl, 13)?>" alt="<?=$arPaySystem["PSA_NAME"];?>">
                                    </div>
                                    <div class="payment-item__text">
                                        <h4><?=$arPaySystem["PSA_NAME"];?></h4>
                                        <p><?=$arPaySystem["DESCRIPTION"];?></p>
                                    </div>
                                </span>
                            </div>
                        <?
                    }
                    else // more than one
                    {
                        if (count($arPaySystem["PSA_LOGOTIP"]) > 0):
                            $imgUrl = $arPaySystem["PSA_LOGOTIP"]["SRC"];
                        else:
                            $imgUrl = $templateFolder."/images/logo-default-ps.gif";
                        endif;
                        ?>
                            <div class="payment-list__item">
                                <input type="radio"
                                    id="ID_PAY_SYSTEM_ID_<?=$arPaySystem["ID"]?>"
                                    name="PAY_SYSTEM_ID"
                                    value="<?=$arPaySystem["ID"]?>"
                                    <?if ($arPaySystem["CHECKED"]=="Y" && !($arParams["ONLY_FULL_PAY_FROM_ACCOUNT"] == "Y" && $arResult["USER_VALS"]["PAY_CURRENT_ACCOUNT"]=="Y")) echo " checked=\"checked\"";?>
                                    onclick="changePaySystem();"
                                />
                                <span onclick="BX('ID_PAY_SYSTEM_ID_<?=$arPaySystem['ID']?>').checked=true;changePaySystem();" class="js-payment-item payment-item <?=$arPaySystem["CHECKED"]=="Y"?'active':''?>">
                                    <div class="payment-item__img">
                                        <img src="<?=CResizer2Resize::ResizeGD2($imgUrl, 13)?>" alt="<?=$arPaySystem["PSA_NAME"];?>">
                                    </div>
                                    <div class="payment-item__text">
                                        <h4><?=$arPaySystem["PSA_NAME"];?></h4>
                                        <div><?=$arPaySystem["DESCRIPTION"];?></div>
                                    </div>
                                </span>
                            </div>
                        <?
                    }
                }

                if (strlen(trim(str_replace("<br />", "", $arPaySystem["DESCRIPTION"]))) == 0 && intval($arPaySystem["PRICE"]) == 0)
                {
                    if (count($arResult["PAY_SYSTEM"]) == 1)
                    {
                        if (count($arPaySystem["PSA_LOGOTIP"]) > 0):
                            $imgUrl = $arPaySystem["PSA_LOGOTIP"]["SRC"];
                        else:
                            $imgUrl = $templateFolder."/images/logo-default-ps.gif";
                        endif;
                        
                        ?>
                        <div class="payment-list__item <?=$arPaySystem["CHECKED"]=="Y"?'active':''?>">
                            <input type="radio"
                                id="ID_PAY_SYSTEM_ID_<?=$arPaySystem["ID"]?>"
                                name="PAY_SYSTEM_ID"
                                value="<?=$arPaySystem["ID"]?>"
                                <?if ($arPaySystem["CHECKED"]=="Y" && !($arParams["ONLY_FULL_PAY_FROM_ACCOUNT"] == "Y" && $arResult["USER_VALS"]["PAY_CURRENT_ACCOUNT"]=="Y")) echo " checked=\"checked\"";?>
                                onclick="changePaySystem();"
                            />
                            <span onclick="BX('ID_PAY_SYSTEM_ID_<?=$arPaySystem['ID']?>').checked=true;changePaySystem();" class="js-payment-item payment-item">
                                <div class="payment-item__img">
                                    <img src="<?=CResizer2Resize::ResizeGD2($imgUrl, 13)?>" alt="<?=$arPaySystem["PSA_NAME"];?>">
                                </div>
                                <div class="payment-item__text">
                                    <h4><?=$arPaySystem["PSA_NAME"];?></h4>
                                    <p><?=$arPaySystem["DESCRIPTION"];?></p>
                                </div>
                            </span>
                        </div>
                        <?
                    }
                    else // more than one
                    {
                        if (count($arPaySystem["PSA_LOGOTIP"]) > 0):
                            $imgUrl = $arPaySystem["PSA_LOGOTIP"]["SRC"];
                        else:
                            $imgUrl = $templateFolder."/images/logo-default-ps.gif";
                        endif;
                        ?>
                            <div class="payment-list__item <?=$arPaySystem["CHECKED"]=="Y"?'active':''?>">
                                <input type="radio"
                                    id="ID_PAY_SYSTEM_ID_<?=$arPaySystem["ID"]?>"
                                    name="PAY_SYSTEM_ID"
                                    value="<?=$arPaySystem["ID"]?>"
                                    <?if ($arPaySystem["CHECKED"]=="Y" && !($arParams["ONLY_FULL_PAY_FROM_ACCOUNT"] == "Y" && $arResult["USER_VALS"]["PAY_CURRENT_ACCOUNT"]=="Y")) echo " checked=\"checked\"";?>
                                    onclick="changePaySystem();"
                                />
                                <span onclick="BX('ID_PAY_SYSTEM_ID_<?=$arPaySystem['ID']?>').checked=true;changePaySystem();" class="js-payment-item payment-item">
                                    <div class="payment-item__img">
                                        <img src="<?=CResizer2Resize::ResizeGD2($imgUrl, 13)?>" alt="<?=$arPaySystem["PSA_NAME"];?>">
                                    </div>
                                    <div class="payment-item__text">
                                        <h4><?=$arPaySystem["PSA_NAME"];?></h4>
                                        <p><?=$arPaySystem["DESCRIPTION"];?></p>
                                    </div>
                                </span>
                            </div>
                        <?
                    }
                }
            }?>
        </div><!-- /payment-system-type.row -->
    </div>
<? } ?>
<? /* шаблон bitronic - КОНЕЦ */ ?>
