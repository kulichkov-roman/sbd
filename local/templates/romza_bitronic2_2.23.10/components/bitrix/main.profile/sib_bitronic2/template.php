<?
/**
 * @global CMain $APPLICATION
 * @param array $arParams
 * @param array $arResult
 */
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

CRZBitronic2CatalogUtils::ShowMessage(Array("MESSAGE" => $arResult["strProfileError"], "TYPE" => "ERROR"));
if ($arResult['DATA_SAVED'] == 'Y') {
    CRZBitronic2CatalogUtils::ShowMessage(Array("MESSAGE" => GetMessage('PROFILE_DATA_SAVED'), "TYPE" => "OK"));
}

$arFields = array(
    'MAIN' => array(
        'LOGIN' => array(
            'REQUIRED' => true
        ),
        'EMAIL' => array(
        ),
        'NEW_PASSWORD' => array(
            'REQUIRED' => false
        ),
        'NEW_PASSWORD_CONFIRM' => array(
            'REQUIRED' => false
        ),
        'NAME' => array(
            'REQUIRED' => false
        ),
        'LAST_NAME' => array(
            'REQUIRED' => false
        ),
        'PERSONAL_BIRTHDAY' => array(
            'REQUIRED' => false
        ),
        'PERSONAL_PHONE' => array(
            'REQUIRED' => false
        ),
        /*'PERSONAL_PHOTO' => array(
            'REQUIRED' => false
        ),*/
    ),
);
\Bitrix\Main\Localization\Loc::loadMessages($_SERVER['DOCUMENT_ROOT'] . SITE_TEMPLATE_PATH . '/header.php');
//$pathToRules = COption::GetOptionString(CRZBitronic2Settings::getModuleId(), 'path_tu_rules_privacy', SITE_DIR . 'personal/rules/personal_data.php');
?>
<div class="main-data">
    <h3 class="main-data__title"><?= GetMessage('REG_SHOW_HIDE') ?></h3>
    <form method="post" name="form1" action="<?= $arResult["FORM_TARGET"] ?>" enctype="multipart/form-data"
          class="form_account-settings">
        <?= $arResult["BX_SESSION_CHECK"] ?>
        <input type="hidden" name="lang" value="<?= LANG ?>"/>
        <input type="hidden" name="ID" value=<?= $arResult["ID"] ?>/>
        <input type="hidden" name="privacy_policy" value="Y"/>
        <? foreach ($arFields['MAIN'] as $code => $arFiled): ?>
            <div class="box-field">
                <? switch ($code):
                    case 'LOGIN':
                        ?>
                        <input type="hidden" name="<?= $code ?>" id="<?= $code ?>"
                               value="<?= $arResult["arUser"][$code] ?>"/>
                        <? break;
                    case 'NEW_PASSWORD':
                    case 'NEW_PASSWORD_CONFIRM':?>
                        <label class="box-field__label" for="<?= $code ?>">
                            <?= GetMessage($code) ?>
                            <? if ($arFiled['REQUIRED']):?>
                                <span class="required-asterisk">*</span>
                            <?endif ?>
                        </label>
                        <div class="box-field__input box-field__input_inl">
                            <input
                                type="password"
                                name="<?= $code ?>"
                                id="<?= $code ?>"
                                class="input"
                                maxlength="50"
                                value=""
                                autocomplete="new-password"
                            >
                        </div>
                        <? break;
                    case 'PERSONAL_BIRTHDAY':
                        ?>
                        <label class="box-field__label"><?= GetMessage('USER_BIRTHDAY_DT') ?></label>
                        <div class="box-field__input box-field__input_inl box-field-date">
                            <?
                            $APPLICATION->IncludeComponent(
                                'bitrix:main.calendar',
                                '',
                                array(
                                    'SHOW_INPUT' => 'Y',
                                    'FORM_NAME' => 'form1',
                                    'INPUT_NAME' => 'PERSONAL_BIRTHDAY',
                                    'INPUT_VALUE' => $arResult["arUser"]["PERSONAL_BIRTHDAY"],
                                    'SHOW_TIME' => 'N',
                                    'INPUT_ADDITIONAL_ATTR' => 'class="input"'
                                ),
                                null,
                                array('HIDE_ICONS' => 'Y')
                            );
                            ?>
                        </div>
                        <? break;
                    case 'PERSONAL_PHOTO':
                        ?>
                        <div class="line-wrap avatar">
                            <label for="avatar" class="text"><?= GetMessage("USER_PHOTO") ?>:</label>
                            <div class="content">
                                <div class="img-container">
                                    <?= $arResult["arUser"]["PERSONAL_PHOTO_HTML"] ?>
                                </div>
                                <div class="fileinput-styled">
                                    <input name="PERSONAL_PHOTO" id="PERSONAL_PHOTO" size="20" type="file">
                                    <label for="PERSONAL_PHOTO" class="btn-fileinput">
                                        <span class="text"><?= GetMessage("CHOOSE_FILE") ?></span>
                                    </label>
                                    <div class="clearfix">
                                        <span class="chosen-file"><?= GetMessage("NOT_CHOOSEN_FILE") ?></span>
                                        <div class="item-actions">
                                            <? if (intval($arResult["arUser"]['PERSONAL_PHOTO']) > 0):?>
                                                <label class="checkbox-styled">
                                                    <input type="checkbox" id="PERSONAL_PHOTO_del"
                                                           name="PERSONAL_PHOTO_del" value="Y">
                                                    <span class="checkbox-content" tabindex="3">
                                                <i class="flaticon-check14"></i>
                                                        <?= GetMessage("DELETE_FILE") ?>
                                            </span>
                                                </label>
                                            <?endif ?>
                                        </div>
                                    </div>
                                </div><!-- /.fileinput-styled -->
                            </div><!-- /.content -->
                        </div><!-- /.line-wrap.avater -->
                        <? break;
                    case 'PERSONAL_PHONE':
                        ?>
                        <label class="box-field__label" for="<?= $code ?>">
                            <?= GetMessage($code) ?>
                            <? if ($arFiled['REQUIRED']):?>
                                <span class="required-asterisk">*</span>
                            <?endif ?>
                        </label>
                        <div class="box-field__input">
                            <input
                                type="text"
                                name="<?= $code ?>"
                                id="<?= $code ?>"
                                class="input mask-phone js-mask-phone"
                                maxlength="50"
                                value="<?= $arResult["arUser"][$code] ?>"
                                placeholder="+7 (__) ___ - __ - __"
                            >
                        </div>
                        <? break;
                    case 'EMAIL':
                        ?>
                        <label class="box-field__label" for="<?= $code?>">
                            <?= GetMessage($code) ?>
                        </label>
                        <div class="box-field__input">
                            <input
                                type="email"
                                name="<?= $code ?>"
                                id="<?= $code ?>"
                                class="input"
                                value="<?= $arResult["arUser"][$code] ?>"
                                disabled
                            >
                        </div>
                        <? break;
                    default:
                        ?>
                        <label class="box-field__label" for="<?= $code ?>">
                            <?= GetMessage($code) ?>
                            <? if ($arFiled['REQUIRED']):?>
                                <span class="required-asterisk">*</span>
                            <?endif ?>
                        </label>
                        <div class="box-field__input">
                            <input
                                type="text"
                                name="<?= $code ?>"
                                id="<?= $code ?>"
                                class="input"
                                maxlength="50"
                                value="<?= $arResult["arUser"][$code] ?>"
                            >
                        </div>
                <?endswitch?>
            </div>
        <? endforeach ?>
        
        <button class="button disabled" name="save" value="Y"><span class="text"><?= GetMessage('SAVE') ?></span></button>
    </form>
</div>

<script>
    $('[name="form1"]').on('keydown', 'input', function(){
        $('[name="save"]').removeClass('disabled');
    });
    maskPhoneInit($('.mask-phone'));
</script>