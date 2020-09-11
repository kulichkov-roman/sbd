<?
use Bitrix\Main\Application;
use Bitrix\Main\Web\Cookie;

require $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php";
if(\Bitrix\Main\Loader::includeModule('aristov.vregions')){
    $userIP = \Aristov\Vregions\Tools::getUserIP();
    $city   = \Aristov\Vregions\Tools::getLocationByIP($userIP);

    if ($city["city"]['lat'] && $city["city"]['lon']){
        
        $currentRegion = \Aristov\Vregions\Tools::getClosestToCoordsRegion($city["city"]['lat'], $city["city"]['lon']);

        if(!empty($currentRegion['CODE'])){

            if($currentRegion['CODE'] == $_SESSION["VREGIONS_REGION"]['CODE']){
                $link = 'default';
            } else {
                $link = \Aristov\Vregions\Tools::generateRegionLink($currentRegion['CODE'], 'https') . $_REQUEST['request_uri'];
            }

            echo json_encode((object)['LINK' => $link, 'NAME' => $city['city']['name_ru']]);
            die();
        }    

    }
}

echo json_encode((object)['LINK' => 'default']); 