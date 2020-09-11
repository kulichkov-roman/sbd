<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die(); 

// echo "<pre style='text-align:left;'>";print_r($arResult);echo "</pre>";
/* ВСТАВИТЬ ПОСЛЕ ОБНОВЛЕНИЯ */
$meta = \Aristov\Vregions\Sections::getMetaFromThirdIblock(36, $arResult['SECTION']['ID']);
// если есть тайтл
if ($meta['TITLE']){
	// ставим тайтл
	$APPLICATION->SetPageProperty("title", $meta['TITLE']);
}
// если есть дескрипшион
if ($meta['DESCRIPTION']){
	// ставим дескрипшион
	$APPLICATION->SetPageProperty("description", $meta['DESCRIPTION']);
}
// если есть кейвордс
if ($meta['KEYWORDS']){
	// ставим кейвордс
	$APPLICATION->SetPageProperty("keywords", $meta['KEYWORDS']);
}
/* /ВСТАВИТЬ ПОСЛЕ ОБНОВЛЕНИЯ */
$arParams['RESIZER_SET'] = intval($arParams['RESIZER_SET']);

if (0 >= $arParams['RESIZER_SET']) $arParams['RESIZER_SET'] = 5;

if ($arParams['VIEW_MODE'] != 'TEXT') {
	foreach ($arResult['SECTIONS'] as &$arSection) {
		if ($arParams['VIEW_MODE'] == 'BOTH' && empty($arSection['PICTURE'])) continue;
		if (empty($arSection['PICTURE'])) {
			$arSection['PICTURE'] = array(
				'ALT' => (
					'' != $arSection["IPROPERTY_VALUES"]["SECTION_PICTURE_FILE_ALT"]
					? $arSection["IPROPERTY_VALUES"]["SECTION_PICTURE_FILE_ALT"]
					: $arSection["NAME"]
				),
				'TITLE' => (
					'' != $arSection["IPROPERTY_VALUES"]["SECTION_PICTURE_FILE_TITLE"]
					? $arSection["IPROPERTY_VALUES"]["SECTION_PICTURE_FILE_TITLE"]
					: $arSection["NAME"]
				)
			);
		}
		$arSection['PICTURE']['SRC'] = CRZBitronic2CatalogUtils::getSectionPictureById($arSection['ID'], $arParams['RESIZER_SET']);
	}
}
unset($arSection);
