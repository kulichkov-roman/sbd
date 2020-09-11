<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
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
<div class="subscribe__col">
    <p class="subscribe__title"><?=GetMessage("MAIN_TITLE")?></p>
    <p class="subscribe__text"><?=GetMessage("MAIN_TEXT")?></p>
</div>
<div class="subscribe__col">
    <div id="bxdynamic-subscribe-form">
    <?
    $frame = $this->createFrame("bxdynamic-subscribe-form", false)->begin();
    ?>
    <form action="<?= $arResult["FORM_ACTION"] ?>" class="form_footer-subscribe">
        <div class="hidden">
            <? foreach ($arResult["RUBRICS"] as $itemID => $itemValue): ?>
                <input type="checkbox" name="sf_RUB_ID[]" id="sf_RUB_ID_<?= $itemValue["ID"] ?>"
                       value="<?= $itemValue["ID"] ?>" checked /> <?= $itemValue["NAME"] ?>
            <? endforeach; ?>
        </div>
        <div class="sub-form">
            <div class="sub-form__col">
                <div class="sub-form__input-wrap">
                    <input class="sub-form__input" type="email" name="sf_EMAIL"
                           size="20" value="<?=$arResult["EMAIL"]?>" placeholder="<?=GetMessage("INPUT_PLACEHOLDER")?>">
                </div>
            </div>
            <div class="sub-form__col">
                <button class="sub-form__button button" type="submit" data-src="#popup-subscribe-form"><?=GetMessage("BUTTON_TEXT")?></button>
            </div>
        </div>
    </form>
    <?
    $frame->beginStub();
    ?>
    
    <form action="<?= $arResult["FORM_ACTION"] ?>" class="form_footer-subscribe">
        <div class="sub-form">
            <div class="sub-form__col">
                <div class="sub-form__input-wrap">
                    <input class="sub-form__input" type="email" name="sf_EMAIL"
                           size="20" value="<?=$arResult["EMAIL"]?>" placeholder="<?=GetMessage("subscr_form_email_title")?>">
                </div>
            </div>
            <div class="sub-form__col">
                <button class="sub-form__button button" type="submit" data-src="#popup-subscribe-form"><?=GetMessage("subscr_form_button")?></button>
            </div>
        </div>
    </form>
    <?
    $frame->end();
    ?>
    </div>
</div>