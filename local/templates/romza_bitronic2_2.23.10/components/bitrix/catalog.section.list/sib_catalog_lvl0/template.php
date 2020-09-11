<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
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

if (empty($arResult['ALL_ITEMS']))
	return false;
?>
<div class="accordion">
    <h3><?=GetMessage('TITLE')?></h3>
    <?foreach ($arResult['ALL_ITEMS_ID'] as $idItem_1 => $arItem_1):?>
        <div class="accordion__item">
            <div class="accordion__heading">
                <div class="accordion__head"><?=$arResult['ALL_ITEMS'][$idItem_1]['NAME']?></div>
                <div class="accordion__arrow"></div>
            </div>
            <div class="accordion__text">
                <ul class="smart-list">
                    <?foreach($arItem_1 as $idItem_2=>$arItem_2):?>
                        <li class="smart-list__item">
                            <a href="<?=$arResult["ALL_ITEMS"][$idItem_2]['SECTION_PAGE_URL']?>" class="smart-list__link">
                                <?=$arResult["ALL_ITEMS"][$idItem_2]['NAME']?>
                            </a>
                        </li>
                    <?endforeach?>
                </ul>
            </div>
        </div>
    <?endforeach?>
</div>