<?php

namespace Aristov\VRegions;

use Bitrix\Main\Localization;

Localization\Loc::loadMessages(__FILE__);

class Meta{

    public static $moduleID = 'aristov.vregions';

    public static function addStringToTheEndOfMetaProperty($property, $string){
        global $APPLICATION;

        /*RBS_CUSTOM_START*/
        $exeptions = \COption::GetOptionString(self::$moduleID, 'vregions_add_string_to_meta_ignore', '', 's1');
        $exeptions = explode("\n", $exeptions);
        foreach ($exeptions as $exp) {
            if (trim($exp) == $APPLICATION->GetCurPage()) {
                return false;
            }
        }
        /*RBS_CUSTOM_END*/

        $fromDir = false;

        $keywords = $APPLICATION->GetPageProperty($property);
        if (!$keywords){
            $fromDir  = true;
            $keywords = $APPLICATION->GetDirProperty($property);
        }

        /*RBS_CUSTOM_START*/
        $new = str_replace(['#CITY#'], [$string], $keywords);
        if($new == $keywords){
            $new .= $string;
        }
        $keywords = $new;
        /*RBS_CUSTOM_END*/

        if ($fromDir){
            $APPLICATION->SetDirProperty($property, $keywords);
        } else{
            $APPLICATION->SetPageProperty($property, $keywords);
        }
    }
}