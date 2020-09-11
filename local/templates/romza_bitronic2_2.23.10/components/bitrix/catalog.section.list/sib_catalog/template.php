<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
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

include $_SERVER["DOCUMENT_ROOT"].SITE_TEMPLATE_PATH.'/include/debug_info_dynamic.php';
$arDesc = explode('#DELIMETER#', $arResult['SECTION']['DESCRIPTION']);
if (!$arParams['IS_BOTTOM']) {
    $desc = $arDesc[0];
} elseif (count($arDesc) > 1) {
    $desc = $arDesc[1];
}else{
    $desc = $arDesc[0];
}

if($arResult['SECTION']['DEPTH_LEVEL'] > 1)
    krsort($arResult['SECTIONS']);
?>
<?if ($arParams['SHOW_SUBSECTIONS'] == 'Y'): ?>
    <div class="filter-top filter-top_desktop">
        <ul class="filter-list">
            <?foreach ($arResult['SECTIONS'] as $arSection):?>
                <li class="filter-list__item">
                    <label class="filter-list__label">
                        <input type="checkbox" name="check_2" class="">
                        <a class="label-text" href="<?=$arSection['SECTION_PAGE_URL']?>"><?=$arSection['NAME']?></a>
                    </label>
                </li>
            <?endforeach?>
            <li class="filter-list__item">
                <a href="#" class="filter-list__link"><?=GetMessage('BITRONIC2_MORE')?></a>
            </li>
        </ul>
    </div>
<? endif ?>