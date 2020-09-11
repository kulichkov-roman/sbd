<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

/* ВСТАВИТЬ ПОСЛЕ ОБНОВЛЕНИЯ */
$meta = \Aristov\Vregions\Sections::getMetaFromThirdIblock(36, $arResult['SECTION']['ID']);

if ($meta['TITLE'])
{
	$APPLICATION->SetPageProperty("title", $meta['TITLE']);
}

if ($meta['DESCRIPTION'])
{
	$APPLICATION->SetPageProperty("description", $meta['DESCRIPTION']);
}

if ($meta['KEYWORDS'])
{
	$APPLICATION->SetPageProperty("keywords", $meta['KEYWORDS']);
}
/* /ВСТАВИТЬ ПОСЛЕ ОБНОВЛЕНИЯ */
$arParams['RESIZER_SET'] = intval($arParams['RESIZER_SET']);

if (0 >= $arParams['RESIZER_SET']) $arParams['RESIZER_SET'] = 5;

if ($arParams['VIEW_MODE'] != 'TEXT')
{
	foreach ($arResult['SECTIONS'] as $key => &$arSection)
	{
		if(in_array($arSection['ID'], $arParams['FILTER_SUB_SECTIONS'])){
			unset($arResult['SECTIONS'][$key]);
			continue;
		}
		if ($arParams['VIEW_MODE'] == 'BOTH' && empty($arSection['PICTURE'])) continue;
		if (empty($arSection['PICTURE']))
		{
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
