<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?
if (method_exists($this, 'setFrameMode')) $this->setFrameMode(false);
include $_SERVER["DOCUMENT_ROOT"].SITE_TEMPLATE_PATH.'/include/debug_info_dynamic.php';
?>
<aside class="personal-account" id="account-menu">
    <div class="personal-account-user">
        <div class="avatar">
            <img src="<?=$arResult['USER']['PERSONAL_PHOTO']?>" alt="<?=GetMessage('BITRONIC2_AVATAR')?>" title="<?=GetMessage('BITRONIC2_AVATAR')?>">
        </div>
        <div class="user-name"><?=$arResult['USER']['PRINT_NAME']?></div>
        <? if (!empty($arResult['USER']['EMAIL'])): ?>
            <div class="user-email">[<?=$arResult['USER']['EMAIL']?>]</div>
        <? endif ?>
    </div>
    <ul class="personal-account-list">
        <? foreach ($arResult as $key => $arItem):
            if ($key === 'USER') continue; ?>
            <li class="personal-account-list__item <?=$arItem['SELECTED'] ? 'active' : ''?>">
                <a href="<?=$arItem['LINK']?>" <?=$arItem['LINK_DATA']?> class="personal-account-list__link">
                    <div class="personal-account-list__icon">
                        <i class="icon-account-<?=$arItem['PARAMS']['ICON_SVG']?>"></i>
                    </div>
                    <span><?=$arItem['TEXT']?></span>
                </a>
            </li>
        <? endforeach ?>
	</ul>
</aside><!-- /account-menu -->