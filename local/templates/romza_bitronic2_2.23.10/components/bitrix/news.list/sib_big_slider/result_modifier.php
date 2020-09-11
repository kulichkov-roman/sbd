<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
\Bitrix\Main\Loader::includeModule('sib.core');
foreach($arResult['ITEMS'] as $key => $arItem){
    $isDisabled = false;

    if($arItem['PROPERTIES']['RBS_LINKED_CITY']['VALUE'] == 'Y'){
        $linkedCities = [];
        if(is_array($arItem['PROPERTIES']['RBS_CITY']['VALUE'])){
            foreach ($arItem['PROPERTIES']['RBS_CITY']['VALUE'] as $cityId) {
                $linked = \Sib\Core\Regions::getLinkedCities($cityId);
                if(is_array($linked) && count($linked) > 0){
                    $linkedCities[] = $linked;
                }
            }
        }
        if(count($linkedCities) > 0){
            foreach($linkedCities as $linked){
                $arItem['PROPERTIES']['RBS_CITY']['VALUE'] = array_merge($arItem['PROPERTIES']['RBS_CITY']['VALUE'], $linked);
            }
        }
    }

    if($arItem['PROPERTIES']['RBS_ALL_CITY']['VALUE'] == 'Y'){
        $isDisabled = true;
        if(is_array($arItem['PROPERTIES']['RBS_CITY']['VALUE'])){
            foreach($arItem['PROPERTIES']['RBS_CITY']['VALUE'] as $cityId){
                if($cityId == $arParams['REGION_ID']){
                    $isDisabled = false;
                }
            }
        }
    } else {
        if(is_array($arItem['PROPERTIES']['RBS_CITY']['VALUE'])){
            foreach($arItem['PROPERTIES']['RBS_CITY']['VALUE'] as $cityId){
                if($cityId == $arParams['REGION_ID']){
                    $isDisabled = true;
                }
            }
        }
    }
    if($isDisabled){
        unset($arResult['ITEMS'][$key]);
    }
}