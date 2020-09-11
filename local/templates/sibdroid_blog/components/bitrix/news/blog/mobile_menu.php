<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
global $blogApp;
?>
<div class="mobile-menu">
    <div class="aside__block">
        <div class="aside__block_head">Темы</div>
        <div class="aside__block_body aside__block_body__menu">

            <div class="list-aside">
                <?=$blogApp->getAsideSectionsListTemplate();?>
            </div>

            <div class="list-aside">
                <?=$blogApp->getAsideTagListTemplate();?>
            </div>
        </div>
    </div>
</div>