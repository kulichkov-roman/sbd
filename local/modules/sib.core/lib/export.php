<?
namespace Sib\Core;

/* use \Bitrix\Main\Loader;

\Bitrix\Main\Loader::includeModule('catalog');
\Bitrix\Main\Loader::includeModule('iblock');
\Bitrix\Main\Loader::includeModule('sale'); */

class Export
{
    public static function globalExportAddon(&$defaultFields)
    {
        self::addonPurchase($defaultFields);
    }

    public static function globalCheckItems($aritem)
    {
        self::checkForPusrchase($aritem);
    }

    private static function checkForPusrchase($aritem)
    {
        $subSectionSmartPhone = Helper::getSubSectionFromParents(['IBLOCK_ID' => 6, 'ACTIVE' => 'Y'], ['ID' => 52]);
        
        if(!in_array($aritem['IBLOCK_SECTION_ID'], $subSectionSmartPhone)){
            return false;
        }

        $_SESSION['FOR_PURCHASE'][] = $aritem['ID'];
    }

    private static function addonPurchase(&$defaultFields)
    {
        $defaultFields['#ITEMS_PURCHASE#'] = '';
        foreach($_SESSION['FOR_PURCHASE'] as $pid){
            $defaultFields['#ITEMS_PURCHASE#'] .= '<product offer-id="'.$pid.'"/>' . PHP_EOL;
        }
    }
}