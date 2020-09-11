<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="main-settings">
    <?if (count($arResult["MESSAGE"]) > 0):?>
        <div class="message message-success">
            <?= implode('<br/>',$arResult["MESSAGE"])?>
        </div>
    <?endif;?>
    <?if (count($arResult["ERROR"]) > 0):?>
        <div class="message message-error">
            <?= implode('<br/>',$arResult["ERROR"])?>
        </div>
    <?endif;?>
    <?
    if ($arResult["ID"] == 0 && empty($_REQUEST["action"]) || CSubscription::IsAuthorized($arResult["ID"]))
    {
        include("setting.php");
    }
    else
    {
        include("authorization_full.php");
    }
    ?>
</div>