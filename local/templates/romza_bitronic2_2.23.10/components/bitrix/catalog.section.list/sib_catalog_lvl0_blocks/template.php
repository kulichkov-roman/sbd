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
if (method_exists($this, 'setFrameMode')) $this->setFrameMode(true);

include $_SERVER["DOCUMENT_ROOT"] . SITE_TEMPLATE_PATH . '/include/debug_info_dynamic.php';

if (empty($arResult['ALL_ITEMS'])) {
    return false;
}
?>
<div class="cards-ctl">
    <?foreach ($arResult['ALL_ITEMS_ID'] as $idItem_1 => $arItem_1):?>
        <a href="<?=$arResult['ALL_ITEMS'][$idItem_1]['SECTION_PAGE_URL']?>" class="card-ctl">

            <?if($arResult['ALL_ITEMS'][$idItem_1]["PICTURE"]['SRC']):?>
                <div class="card-ctl__img">
                    <img src="<?=$arResult['ALL_ITEMS'][$idItem_1]["PICTURE"]['SRC']?>"
                        alt="<?=$arResult['ALL_ITEMS'][$idItem_1]['NAME']?>"
                        title="<?=$arResult['ALL_ITEMS'][$idItem_1]['NAME']?>">
                </div>
            <?endif;?>
            <?
                $classMenu = '';
                if($arResult['ALL_ITEMS'][$idItem_1]['UF_CLASS_MENU']){
                    $classMenu = $arResult['ALL_ITEMS'][$idItem_1]['UF_CLASS_MENU'] . ' main-nav__link';
                }
            ?>
            <div class="card-ctl__heading  <?=$classMenu?>"><?=$arResult['ALL_ITEMS'][$idItem_1]['NAME']?></div>
        </a>
    <?endforeach?>
</div>