<?

namespace Aristov\Vregions\EventHandlers;

use Aristov\Vregions\Tools;
use Bitrix\Main\Localization;

\CModule::IncludeModule('aristov.vregions');

Localization\Loc::loadMessages(__FILE__);

class CvRegionsOnPageLoad{

    static $MODULE_ID = "aristov.vregions";

    public static function is_bot()
    {
        if (!empty($_SERVER['HTTP_USER_AGENT'])) {
            $options = array(
                'YandexBot', 'YandexAccessibilityBot', 'YandexMobileBot','YandexDirectDyn',
                'YandexScreenshotBot', 'YandexImages', 'YandexVideo', 'YandexVideoParser',
                'YandexMedia', 'YandexBlogs', 'YandexFavicons', 'YandexWebmaster',
                'YandexPagechecker', 'YandexImageResizer','YandexAdNet', 'YandexDirect',
                'YaDirectFetcher', 'YandexCalendar', 'YandexSitelinks', 'YandexMetrika',
                'YandexNews', 'YandexNewslinks', 'YandexCatalog', 'YandexAntivirus',
                'YandexMarket', 'YandexVertis', 'YandexForDomain', 'YandexSpravBot',
                'YandexSearchShop', 'YandexMedianaBot', 'YandexOntoDB', 'YandexOntoDBAPI',
                'Googlebot', 'Googlebot-Image', 'Mediapartners-Google', 'AdsBot-Google',
                'Mail.RU_Bot', 'bingbot', 'Accoona', 'ia_archiver', 'Ask Jeeves', 
                'OmniExplorer_Bot', 'W3C_Validator', 'WebAlta', 'YahooFeedSeeker', 'Yahoo!',
                'Ezooms', '', 'Tourlentabot', 'MJ12bot', 'AhrefsBot', 'SearchBot', 'SiteStatus', 
                'Nigma.ru', 'Baiduspider', 'Statsbot', 'SISTRIX', 'AcoonBot', 'findlinks', 
                'proximic', 'OpenindexSpider','statdom.ru', 'Exabot', 'Spider', 'SeznamBot', 
                'oBot', 'C-T bot', 'Updownerbot', 'Snoopy', 'heritrix', 'Yeti',
                'DomainVader', 'DCPbot', 'PaperLiBot', 'Lighthouse'
            );

            foreach($options as $row) {
                if (stripos($_SERVER['HTTP_USER_AGENT'], $row) !== false) {
                    return true;
                }
            }
        }

        return false;
    }

    public static function is_google_pagespeed()
    {
        if (!empty($_SERVER['HTTP_USER_AGENT']) && stripos($_SERVER['HTTP_USER_AGENT'], 'Lighthouse') !== false){
            return true;
        }
        return false;
    }

    public static function vRegionsMainHandler(){
        \CModule::IncludeModule("iblock");

        // na sluchaj esli kto-to pytaetsya otkryt' sajt po ip, to ne delaem nichego
        if (preg_match("/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/", $_SERVER['SERVER_NAME'])){
            return;
        }

        $_SESSION['VREGIONS_DEBUG'] = Array();

        if (\VRegionsPageLoadHelper::isThereIsTooMuchSubdomains()){
            \VRegionsPageLoadHelper::handleRegionDetectError();
        }

        $regionCode                                = \VRegionsPageLoadHelper::getRegionCodeOfCurrentDomain();
        $_SESSION['VREGIONS_DEBUG']['REGION_CODE'] = $regionCode;

        /* if('89.22.178.168' == $_SERVER['REMOTE_ADDR']){
            define("LOG_FILENAME", $_SERVER["DOCUMENT_ROOT"]."/local/modules/aristov.vregions/log_update.txt");
            AddMessage2Log($regionCode);
        } */
        
        $_SESSION['is_dev'] = !(strpos(explode('.', $_SERVER["HTTP_HOST"])[0], 'dev') === false);
        $_SESSION['is_bot'] = self::is_bot() || isset($_REQUEST['noads']);
        $_SESSION['is_google_pagespeed'] = self::is_google_pagespeed() || isset($_REQUEST['noads']);
        if(
            !$_SESSION['is_dev'] &&
            !$_SESSION['is_bot'] &&
            !isset($_REQUEST['noredirect']) &&
            strpos($_SERVER['PHP_SELF'], '/bitrix/') === false &&
            strpos($_SERVER['PHP_SELF'], 'ajax') === false &&
            strpos($_SERVER['PHP_SELF'], 'robots.php') === false &&
            strpos($_SERVER['PHP_SELF'], 'cron_events.php') === false &&
            !(defined("ADMIN_SECTION") && ADMIN_SECTION === true)
        ){
            global $APPLICATION;
            if(!$APPLICATION->get_cookie("VREGION_SUBDOMAIN")){
                $userIP = \Aristov\Vregions\Tools::getUserIP();
                $city   = \Aristov\Vregions\Tools::getLocationByIP($userIP);
                if ($city["city"]['lat'] && $city["city"]['lon']){
                    $currentRegion = \Aristov\Vregions\Tools::getClosestToCoordsRegion($city["city"]['lat'], $city["city"]['lon']);
                    if(!empty($currentRegion['CODE']) && $regionCode == 'dev' && $currentRegion['CODE'] != $regionCode){
                        $link = \Aristov\Vregions\Tools::generateRegionLink($currentRegion['CODE'], 'https') . $_SERVER['REQUEST_URI'];
                        //$httpCode = \Aristov\Vregions\Tools::getModuleOption("vregions_redirect_http_code") ?: 301;
                        $httpCode = 302;
                        //LocalRedirect($link, false, $httpCode);
                    }
                }
            }
        }

        /* $phpRedirect = (\Aristov\VRegions\Tools::getModuleOption("vregions_php_redirect") == "Y") ? 1 : 0;
        if ($phpRedirect || $_GET['redirect_test'] == 'y'){
            //echo $regionCode;
            if (\VRegionsPageLoadHelper::isNeedCookieRedirect($regionCode)){
                \VRegionsPageLoadHelper::redirectToRegionDomain(\VRegionsPageLoadHelper::getRegionCookie());
            }
        } */

        \VRegionsPageLoadHelper::setVregionsDefault();

        if (!\VRegionsPageLoadHelper::setVregionsRegion($regionCode)){
            \VRegionsPageLoadHelper::handleRegionDetectError();
        }

        \VRegionsPageLoadHelper::setVregionsPhp();

        \VRegionsPageLoadHelper::setLang();

        \VRegionsPageLoadHelper::setVregionsImLocation();

        \VRegionsPageLoadHelper::fireEvents();
    }

}

?>