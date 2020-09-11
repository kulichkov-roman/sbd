<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<?
//***********************************
//setting section
//***********************************
\Bitrix\Main\Localization\Loc::loadMessages($_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH . '/header.php');
//$pathToRules = COption::GetOptionString(CRZBitronic2Settings::getModuleId(),'path_tu_rules_privacy',SITE_DIR.'personal/rules/personal_data.php');
?>
<form action="<?= $arResult["FORM_ACTION"] ?>" method="post">
    <input type="hidden" name="privacy_policy" value="Y"/>
    <input type="hidden" name="EMAIL"
           value="<?=($arResult["SUBSCRIPTION"]["EMAIL"] != "" ? $arResult["SUBSCRIPTION"]["EMAIL"] : $arResult["REQUEST"]["EMAIL"])?>"/>
    <?= bitrix_sessid_post(); ?>
    <h3><?= GetMessage("subscr_title_settings") ?></h3>
    <div class="main-settings__checkboxes">
        <div class="checkbox">
            <label>
                <input class="rbs-all-subs-check js-formstyler js-dispatch" type="checkbox">Получать рассылки
            </label>
        </div>
        <div class="checkbox-dispatch">
            <? foreach ($arResult["RUBRICS"] as $itemID => $itemValue): ?>
                <div class="checkbox">
                    <label>
                        <input class="rbs-subs-check js-formstyler" type="checkbox" name="RUB_ID[]"
                               value="<?= $itemValue["ID"] ?>"
                            <? if ($itemValue["CHECKED"]) echo " checked" ?>
                        >
                        <?=$itemValue["NAME"]?>
                    </label>
                    <div class="checkbox-desc"><?=$itemValue["DESCRIPTION"]?></div>
                </div>
            <? endforeach; ?>
        </div>
    </div>
    
    <button name="Save" class="button disabled rbs-subs-send">
        <span class="text"><?= GetMessage("subscr_save") ?></span>
    </button>
    <input type="hidden" name="PostAction" value="<?= ($arResult["ID"] > 0 ? "Update" : "Add") ?>"/>
    <input type="hidden" name="ID" value="<?= $arResult["SUBSCRIPTION"]["ID"]; ?>"/>
</form>

<script>
    BX.ready(function(){
        if($('.rbs-subs-check:checked').length > 0){
            $('.rbs-all-subs-check').prop('checked', 'checked');
        }

        $('.rbs-subs-check').on('change', function(){
            $('.rbs-subs-send').removeClass('disabled');
        });

        $('.rbs-all-subs-check').on('change', function(){
            if($(this).is(':checked')){
                $('.rbs-subs-check').prop('checked', 'checked');
            } else {
                $('.rbs-subs-check').prop('checked', false);
            }
            $('.rbs-subs-check').trigger('change');
            $('.rbs-subs-send').removeClass('disabled');
        });
    });
</script>
