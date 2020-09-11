<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
if (method_exists($this, 'setFrameMode')) $this->setFrameMode(true);
?>
<div class="footer-block">
    <p class="footer-block__title"><?= (!empty($arParams['TITLE'])) ? $arParams['TITLE'] : GetMessage('MENU_TITLE') ?>:</p>
    <nav class="footer-nav-1">
        <ul class="footer-nav-1__list">
            <?foreach ($arResult as $arItem):?>
                <? if ($arItem['DEPTH_LEVEL'] > 1) continue;?>
                <li class="footer-nav-1__item">
                    <a class="footer-nav-1__link" href="<?=$arItem['LINK']?>">
                        <span><?=$arItem['TEXT']?></span>
                    </a>
                </li>
            <?endforeach?>
        </ul>
    </nav>
</div>