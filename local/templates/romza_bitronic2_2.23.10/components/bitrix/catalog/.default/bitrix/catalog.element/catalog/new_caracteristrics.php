<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?
if (\Bitrix\Main\Loader::IncludeModule('yenisite.infoblockpropsplus') && !empty($arResult['DISPLAY_PROPERTIES']))
			$APPLICATION->IncludeComponent('yenisite:ipep.props_groups', 'sib_detail', array(
				'DISPLAY_PROPERTIES' => $arResult['DISPLAY_PROPERTIES'],
				'IBLOCK_ID' => $arParams['IBLOCK_ID'],
				'SKU_PROP_ID' => ($arResult['SHOW_OFFERS_PROPS'] && $arResult['bSkuExt']) ? $arItemIDs['DISPLAY_PROP_DIV'] : '',
				'SHOW_PROPERTY_VALUE_DESCRIPTION' => 'Y'
			),

				$component
			);