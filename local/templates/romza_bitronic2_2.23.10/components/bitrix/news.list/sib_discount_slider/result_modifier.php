<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

foreach($arResult['ITEMS'] as $key => $arItem){
    $isDisabled = false;
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
    //global $USER; if($USER->IsAdmin()){echo '<pre>'; print_r($arItem['PROPERTIES']['RBS_CITY']); echo '</pre>';};
}