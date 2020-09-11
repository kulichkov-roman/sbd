<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
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
include $_SERVER["DOCUMENT_ROOT"].SITE_TEMPLATE_PATH.'/include/debug_info_dynamic.php';

?>
<? if ($arParams["DISPLAY_TOP_PAGER"]): ?>
	<?=$arResult["NAV_STRING"]?>
<? endif ?>

			<div class="isotope__grid">
<? foreach ($arResult["ITEMS"] as $arItem): ?>
<?
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
?>
				<div class="element-item" id="<?= $this->GetEditAreaId($arItem['ID']) ?>">
					<div class="title"><?= $arItem["NAME"] ?></div>
					<div class="salary"><?= $arItem["PREVIEW_TEXT"] ?></div>
					<div class="desc"><?= $arItem['DETAIL_TEXT'] ?></div>
				</div>
<? endforeach ?>
			</div>

<? if ($arParams["DISPLAY_BOTTOM_PAGER"]): ?>
	<?=$arResult["NAV_STRING"]?>
<? endif ?>
<?
