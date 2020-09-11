<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
use Bitrix\Main\Loader;
use Bitrix\Main\Page\Asset;
use \Yenisite\Core\Page;
use \Yenisite\Core\Tools;

// @var $moduleId
// @var $moduleCode
// @var $settingsClass
include 'include/module_code.php';

if ($_POST['rz_ajax_no_header'] === 'y') {
    $APPLICATION->IncludeComponent("yenisite:settings.panel", "empty", array(
        "SOLUTION" => $moduleId,
        "SETTINGS_CLASS" => $settingsClass,
        "GLOBAL_VAR" => "rz_b2_options"
    ),
        false
    );
    return;
}

?>
<!DOCTYPE html>
<html lang="<?= LANGUAGE_ID ?>">
    <head>
        <?if(!$_SESSION['is_bot']):?>
            <script data-skip-moving="true">
                (function(d) {var ref=d.getElementsByTagName('script')[0];var js, jsId='488f7af9c6da9d467e28f29b23fc4f95';if (d.getElementById(jsId)) return;js=d.createElement('script');js.id=jsId;js.async=true;js.src='https://apps.azhelp.ru/connect?ts='+escape(Math.round(+new Date()/1000))+'&id='+escape(jsId)+'&e='+escape(document.characterSet)+'&d='+escape(window.location.href)+'&b='+escape(window.navigator.userAgent);ref.parentNode.insertBefore(js, ref);}(document));
            </script>
        <?endif?>
        <script>window.jsDebug = true;</script>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <title><? $APPLICATION->ShowTitle() ?></title>
        <?
        \Bitrix\Main\Localization\Loc::loadMessages(__FILE__);

        global $rz_b2_options;
        global $rz_banner_num;
        global $USER;
        global $isNewTemplate;
        $isNewTemplate = true;
        global $isDev;
        $isDev = $_SERVER["HTTP_HOST"] == 'dev.sibdroid.ru:443' || $_SERVER["HTTP_HOST"] == 'dev2.sibdroid.ru:443';

        if (!isset($rz_banner_num)) $rz_banner_num = 0;

        $bMainPage = $APPLICATION->GetCurPage(false) == SITE_DIR;
        $isCatalogPage = strpos($APPLICATION->GetCurDir(), 'catalog') !== false || strpos($APPLICATION->GetCurDir(), 'discount') !== false;
        $isComparePage = $isCatalogPage && strpos($APPLICATION->GetCurPage(), 'compare') !== false;
        $isDetailPage = $isCatalogPage && strpos($APPLICATION->GetCurPage(), '.html') !== false;
        $isPersonalPage = strpos($APPLICATION->GetCurPage(), SITE_DIR.'personal/') !== false;
        $isCartPage = $APPLICATION->GetCurDir() === SITE_DIR.'personal/cart/';
        $isOrderPage = $APPLICATION->GetCurDir() === SITE_DIR.'personal/order/' || $APPLICATION->GetCurDir() === SITE_DIR.'personal/order-test/';
        $isConfirm = $isOrderPage && (isset($_REQUEST["ORDER_ID"]) && (strlen($_REQUEST["ORDER_ID"]) > 0));
        $arDefIncludeParams = array(
            "AREA_FILE_SHOW" => "file",
            "EDIT_TEMPLATE" => "include_areas_template.php"
        );

        if (!Loader::includeModule($moduleId)) die('Module ' . $moduleId . ' not installed!');
        if (!Loader::includeModule("yenisite.core")) die('Module yenisite.core not installed!');

        use \Bitronic2\Mobile;

        Mobile::Init();
        ?>
        <?/*if($bMainPage):?>
            <meta name="yandex-verification" content="0d5530d24fe31a27" />
        <?endif*/?>
        <?if(!$isDev && !$_SESSION['is_google_pagespeed']):?>
            <!-- Google Tag Manager -->
            <script data-skip-moving="true">     
                var whileJqueryLoading = function() {
                    if ('jQuery' in window) {  
                        clearInterval(jqLoadingTimer);
                        (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                                new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
                            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
                            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
                        })(window,document,'script','dataLayer','GTM-5WXCGK');
                    }
                }
                var jqLoadingTimer = setInterval(whileJqueryLoading, 1000);
                
                var fbqAsyncInitCallbacks = [];
                var fbqCountTrys = 0;
                var whileFbqLoading = function() {
                    if ('fbq' in window) {  
                        clearInterval(fbqLoadingTimer);
                        if (window.fbqAsyncInitCallbacks && fbqAsyncInitCallbacks.length) {
                            setTimeout(function() {
                                var callback;
                                while (callback = fbqAsyncInitCallbacks.pop()) {
                                    try {
                                        callback();
                                    } catch(e) {
                                        try {
                                            console.error(e);
                                        } catch (e2) {}
                                    }
                                }
                            }, 0);
                        }
                    }

                    if(fbqCountTrys > 5){
                        clearInterval(fbqLoadingTimer);
                    }
                    fbqCountTrys++;
                }
                var fbqLoadingTimer = setInterval(whileFbqLoading, 1000);
            </script>
            <!-- End Google Tag Manager -->
        <?endif?>
        <?//канонический     урл страниц с пагинацией?>
        <?if(isset($_GET["PAGEN_1"]) || isset($_GET["SHOWALL_1"])):?>
            <?
                $arUrl = explode( "?", $_SERVER["REQUEST_URI"] );
                $query = $_GET;
                unset($query["PAGEN_1"]);
                unset($query["SHOWALL_1"]);

                $queryStr = http_build_query($query);
                if($queryStr) {
                    $arUrl[0].= "?".htmlspecialchars($queryStr);
                }
            ?>
            <link rel="canonical" href="https://<?=$_SERVER["HTTP_HOST"].$arUrl[0]?>" />
        <?endif;?>
        <!-- fonts -->
        <!-- styles -->
        <?include_once 'include/css.php';?>
        <!-- Respond.js - IE8 support of media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!-- selectivizr - IE8- support for css3 classes like :checked -->
        <!--[if lt IE 9]>
        <script async src="<?=SITE_TEMPLATE_PATH?>/js/3rd-party-libs/selectivizr-min.js"></script>
        <script async src="<?=SITE_TEMPLATE_PATH?>/js/custom-scripts/respond.js"></script>
        <![endif]-->
        <!--<script async src="//oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>-->
        <?
            ob_start();
                $APPLICATION->IncludeComponent("bitrix:main.include", "", array_merge($arDefIncludeParams, array("PATH" => SITE_DIR . "include_areas/sib/header/settings.php")), false, array("HIDE_ICONS" => "Y"));
            $panelSettings = ob_get_clean();
        
            //!!!SET CATALOG PARAMS FOR HIDE ITEMS
            $arParams = array();
            $arParams['HIDE_ITEMS_NOT_AVAILABLE'] = $rz_b2_options['hide-not-available'] == 'Y' ? true : false;
            $arParams['HIDE_ITEMS_ZER_PRICE'] = $rz_b2_options['hide-zero-price'] == 'Y' ? true : false;
            $arParams['HIDE_ITEMS_WITHOUT_IMG'] = $rz_b2_options['hide-empty-img'] == 'Y' ? true : false;

            $rz_b2_options['product-hover-effect'] =  $rz_b2_options['product-hover-effect'];
            CRZBitronic2CatalogUtils::reSafeParamsCatalog($arParams);
            //!!!SET CAPTCHA FOR REGISTRATION
            //COption::SetOptionString("main", "captcha_registration", $rz_b2_options['captcha-registration'])
        ?>
        
        <script type="text/javascript" data-skip-moving="true">
            <?
            $arSettings = array();
            foreach ($rz_b2_options as $key => $value) {
                if ('theme-custom' == $key) continue;
                $key = preg_replace("/[^a-z]+/i", " ", strtolower($key));
                $key = str_replace(' ', '', substr_replace($key, substr(ucwords($key), 1), 1));
                $arSettings[$key] = $value;
            }
            //correct settings names
            $arSettings['colorTheme'] = $arSettings['themeDemo'];
            $arSettings['photoViewType'] = $arSettings['detailGalleryType'];
            $arSettings['productInfoMode'] = $arSettings['detailInfoMode'];
            $arSettings['productInfoModeDefExpanded'] = ($arSettings['detailInfoFullExpanded'] === 'Y');
            $arSettings['stylingType'] = substr($arSettings['themeDemo'], -4);
            $arSettings['showStock'] = ($arSettings['showStock'] === 'Y');
            $arSettings['limitSliders'] = ($arSettings['limitSliders'] === 'Y');
            $arSettings['sassWorkerUrl'] = SITE_TEMPLATE_PATH . '/js/3rd-party-libs/sass.js_0.9.11/sass.worker.js';
            $arSettings['isFrontend'] = false;
            ?>
            serverSettings = <?= CUtil::PhpToJSObject($arSettings)?><?unset($arSettings)?>;
            SITE_DIR = '<?=SITE_DIR?>';
            SITE_ID = '<?=SITE_ID?>';
            SITE_TEMPLATE_PATH = '<?=SITE_TEMPLATE_PATH?>';
            COOKIE_PREFIX = '<?=COption::GetOptionString("main", "cookie_name", "BITRIX_SM")?>';
            GOOGLE_KEY = '<?=COption::GetOptionString(CRZBitronic2Settings::getModuleId(), "google_key", "AIzaSyBaUmBHLdq8sLVQmfh8fGsbNzx6rtofKy4")?>';
        </script>
        
        <?
            $APPLICATION->ShowHead();

            include_once 'include/js_colors.php';

            CJSCore::Init(array('window'));
            if (Loader::includeModule('currency')) {
                CJSCore::Init(array('currency'));
            }

            $asset = Asset::getInstance();

            if ('Y' == $rz_b2_options['custom-theme'] && !empty($rz_b2_options['theme-custom'])) {
                $rz_b2_options['theme-custom'] = str_replace('#NEED_REPLACE#', 'select', $rz_b2_options['theme-custom']);
                $asset->addString('<style type="text/css" id="custom-theme">' . $rz_b2_options['theme-custom'] . '</style>');
                $asset->addString("<style type=\"text/css\">
                .custom-theme .hurry header {
                    background-image: url(' ". SITE_TEMPLATE_PATH . "'/new_img/bg/hurry-banner_'". $rz_b2_options['theme-demo'] . '.png);
                }</style>');
            }

            include_once 'include/js.php';
        ?>
        <meta name="theme-color" content="<?= $color ?>">
        <?if(!$isDev && !$_SESSION['is_google_pagespeed']):?>
            <!-- Yandex.Metrika counter -->
                <script type="text/javascript">
                window.dataLayer = window.dataLayer || [];
                (function (d, w, c) {
                    (w[c] = w[c] || []).push(function() {
                        try {
                            w.yaCounter24101851 = new Ya.Metrika({
                                id:24101851,
                                clickmap:true,
                                trackLinks:true,
                                accurateTrackBounce:true,
                                webvisor:true,
                                trackHash:true,
                                ecommerce:"dataLayer"
                            });
                        } catch(e) { }
                    });

                    var n = d.getElementsByTagName("script")[0],
                        s = d.createElement("script"),
                        f = function () { n.parentNode.insertBefore(s, n); };
                    s.type = "text/javascript";
                    s.async = true;
                    s.src = "https://mc.yandex.ru/metrika/watch.js";

                    if (w.opera == "[object Opera]") {
                        d.addEventListener("DOMContentLoaded", f, false);
                    } else { f(); }
                })(document, window, "yandex_metrika_callbacks");
            </script>
            <!-- /Yandex.Metrika counter -->
            <!-- GOOGLE counter -->
            <script>
            (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
            })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

            ga('create', 'UA-49968931-1', 'auto');
            ga('send', 'pageview');

            </script>
            <!-- GOOGLE counter-->
            <!-- ВСТАВИТЬ ПОСЛЕ ОБНОВЫ -->
        <?endif?>
        <?
        // Structured schema.org data, Open Graph tags
        Page::setOGProperty('type', 'website');
        Page::setOGProperty('type', 'website');

        if (method_exists('\Yenisite\Core\Catalog', 'getSiteInfo') && $arSiteInfo = \Yenisite\Core\Catalog::getSiteInfo()):
            $storeUrl = (CMain::IsHTTPS() ? "https://" : "http://") . $arSiteInfo['SERVER_NAME'];

            Page::setOGProperty('url', $storeUrl . $APPLICATION->GetCurPage(false));
            ?>
            <link itemprop="url" href="<?= $storeUrl ?>"/>
            <meta itemprop="name" content="<?= str_replace('"', "'", $arSiteInfo['SITE_NAME']) ?>"/>
        <? endif;
        $storeImagePath = str_replace('//', '/', $_SERVER['DOCUMENT_ROOT'] . SITE_DIR . 'store_image.jpg');
        if (@file_exists($storeImagePath)):
            Page::setOGProperty('image', $storeUrl . SITE_DIR . 'store_image.jpg');
            Page::setOGProperty('image:height', '400');
            Page::setOGProperty('image:width', '400');
            ?>
            <meta itemprop="image" content="<?= $storeUrl . SITE_DIR ?>store_image.jpg"/>
        <? endif ?>

        <?
        /* ob_start();
            Tools::IncludeAreaEdit('header', 'mobile_phone');
        $mobilePhone = ob_get_clean(); */

        $frame = new \Bitrix\Main\Page\FrameBuffered('rz_dynamic_full_mode_meta');
        $frame->begin('');
            if (mobile::isMobile(false) && mobile::isFullMode()):?>
                <script type="text/javascript" data-skip-moving="true">
                    var viewPortTag = document.createElement('meta');
                    viewPortTag.name = "viewport";
                    viewPortTag.content = "";
                    document.getElementsByTagName('head')[0].appendChild(viewPortTag);
                </script>
            <?endif;
        $frame->end(); ?>
        <script>
            //PHP Magic starts here
            b2.s.hoverEffect = "<?=$rz_b2_options['product-hover-effect']?>";
            BX.message({
                'tooltip-last-price': "<?=GetMessage('BITRONIC2_TOOLTIP_LAST_PRICE')?>",
                'available-limit-msg': "<?=GetMessage('BITRONIC2_PRODUCT_AVAILABLE_LIMIT_MSG')?>",
                'b-rub': "<?=GetMessage('BITRONIC2_RUB_CHAR')?>",
                'error-favorite': "<?=GetMessage('BITRONIC2_WRONG_ADD_FAVORITES')?>",
                'file-ots': "<?=GetMessage('BITRONIC2_FILE_DEFER')?>",
                'file-type': "<?=GetMessage('BITRONIC2_TYPE')?>",
            });
        </script>
    <?if($_SESSION["VREGIONS_REGION"]['ID'] == 14646):?>
        <meta name="yandex-verification" content="ad6b7a396bf62ae7" />
        <meta name="yandex-verification" content="b3df775c01f1ba13" />
    <?endif?>
</head>
<body class="geotarget">
    
    <!-- SVG sprite include -->
    <div class="svg-placeholder"
         style="border: 0; clip: rect(0 0 0 0); height: 1px;
	    margin: -1px; overflow: hidden; padding: 0;
	    position: absolute; width: 1px;"></div>
    <script data-skip-moving="true">
        function initSvgSprites() {
            document.querySelector('.svg-placeholder').innerHTML = SVG_SPRITE;
        }
    </script>
    <!-- end SVG sprite include -->

    <noscript><div><img src="https://mc.yandex.ru/watch/24101851" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
    <script data-skip-moving="true">
        var bMainPage = <?=$bMainPage ? 'true' : 'false'?>;
        const PRICE_LIST_ID = 5206; //for vk pixel
        window.vkAsyncInitCallbacks = []; // for event vk pixel
    </script>
    
    <div class="rbs-mask"></div>
<? global $USER; if($USER->IsAdmin()){?>
    <style>
        #bx-panel{z-index: 19992!important;}
    </style>
<?}; ?>
<div class="bitrix-admin-panel">
    <div class="b_panel"><? $APPLICATION->ShowPanel(); ?></div>
</div>
<div class="main-wrap" itemscope itemtype="http://schema.org/Store">
	<!-- <div class="icon-load"></div> -->
	<!-- BEGIN HEADER -->
    <header class="header <?=$isCatalogPage?'header_fix':''?> <?=$isOrderPage || $isCartPage ?'header-top-order':''?>">
        <?if($isOrderPage || $isCartPage):?>
         <!-- BEGIN HEADER TOP -->
         <div class="header-top header-top-order">
            <div class="header-top__main wrapper">
                <div class="header-top__cols js-fade-out">
                    <div class="header-top__left">
                        <?if($isOrderPage):?>
                            <?if (!$isConfirm):?>
                                <a href="<?=SITE_DIR?>personal/cart/" class="order-change"><i class="icon-pencil"></i><span>Изменить заказ</span></a>
                            <?endif?>
                        <?endif?>
                        <span class="header-logo">
                            <?if($isCartPage || $isConfirm):?>
                                <a href="<?=SITE_DIR?>">
                                    <? $APPLICATION->IncludeComponent("bitrix:main.include", "", array_merge($arDefIncludeParams, array("PATH" => SITE_DIR . "include_areas/sib/header/logo.php")), false, array("HIDE_ICONS" => "Y")); ?>
                                </a>
                            <?else:?>
                               <? $APPLICATION->IncludeComponent("bitrix:main.include", "", array_merge($arDefIncludeParams, array("PATH" => SITE_DIR . "include_areas/sib/header/logo.php")), false, array("HIDE_ICONS" => "Y")); ?>
                            <?endif?>
                        </span>
                    </div>

                    <div class="header-top__right">
                        <? $APPLICATION->IncludeComponent("bitrix:main.include", "", array_merge($arDefIncludeParams, array("PATH" => SITE_DIR . "include_areas/sib/header/sib_phone.php")), false, array("HIDE_ICONS" => "Y")); ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- HEADER TOP EOF -->
        <?else:?>
    	<!-- BEGIN HEADER TOP -->
		<div class="header-top">
        	<div class="header-top__main wrapper">
            	<div class="header-top__cols js-fade-out">
                	<div class="header-top__left">
                        <a class="header-logo" href="<?=SITE_DIR?>">
                            <? $APPLICATION->IncludeComponent("bitrix:main.include", "", array_merge($arDefIncludeParams, array("PATH" => SITE_DIR . "include_areas/sib/header/logo.php")), false, array("HIDE_ICONS" => "Y")); ?>
                        </a>
                    </div>
                	<div class="header-top__center">
                        <!-- BEGIN NAVIGATION -->
                        <nav class="header-nav">
                            <? $APPLICATION->IncludeComponent("bitrix:main.include", "", array_merge($arDefIncludeParams, array("PATH" => SITE_DIR . "include_areas/sib/header/menu_top.php")), false, array("HIDE_ICONS" => "Y")); ?>
                        </nav>
                        <!-- NAVIGATION EOF -->
                    </div>  
                	<div class="header-top__right">
                        <!-- BEGIN CITY -->
                        <? $APPLICATION->IncludeComponent("bitrix:main.include", "", array_merge($arDefIncludeParams, array("PATH" => SITE_DIR . "include_areas/sib/header/vregions.php")), false, array("HIDE_ICONS" => "Y")); ?>
                        <!-- CITY EOF -->
                        <div class="header-actions">
                            <!-- BEGIN FAVORITE -->
                            <? $APPLICATION->IncludeComponent("bitrix:main.include", "", array_merge($arDefIncludeParams, array("PATH" => SITE_DIR . "include_areas/sib/header/favorite.php")), false, array("HIDE_ICONS" => "Y")); ?>
                            <!-- <a href="?action=ADD_TO_COMPARE_LIST&id=1349">Добавить в сравнение</a> -->              
                            <!-- FAVORITE EOF -->
                            <!-- BEGIN COMPARED -->
                            <? $APPLICATION->IncludeComponent("bitrix:main.include", "", array_merge($arDefIncludeParams, array("PATH" => SITE_DIR . "include_areas/sib/header/compare.php")), false, array("HIDE_ICONS" => "Y")); ?>
                            <!-- <a href="?action=ADD_TO_COMPARE_LIST&id=1349">Добавить в сравнение</a> -->              
                            <!-- COMPARED EOF -->
                            <!-- BEGIN WATCHED -->
                            <? $APPLICATION->IncludeComponent("bitrix:main.include", "", array_merge($arDefIncludeParams, array("PATH" => SITE_DIR . "include_areas/sib/header/watched.php")), false, array("HIDE_ICONS" => "Y")); ?>  
                            <!-- WATCHED EOF --> 
                        </div>   
                        <!-- BEGIN LOGIN -->                        
                        <?
                            /* $dynamicArea = new \Bitrix\Main\Page\FrameStatic("rbs_login");
                            $dynamicArea->setStub('<div class="login"><a class="login__button" href="javascript:void(0);"><span class="login__icon">...</span></a>');
                            $dynamicArea->startDynamicArea(); */
                        ?>
                            <? $APPLICATION->IncludeComponent("bitrix:main.include", "", array_merge($arDefIncludeParams, array("PATH" => SITE_DIR . "include_areas/sib/header/user_auth.php")), false, array("HIDE_ICONS" => "Y")); ?>            
                        <?//$dynamicArea->finishDynamicArea();?>
                        <!-- LOGIN EOF -->     

                    </div> 
                </div>
            </div>
        </div>
        <!-- HEADER TOP EOF -->
    	<!-- BEGIN HEADER BOTTOM -->
		<div class="header-bottom">
        	<div class="header-bottom__main wrapper">
            	<div class="header-bottom__cols">
                	<div class="header-bottom__left">
                        <? $APPLICATION->IncludeComponent("bitrix:main.include", "", array_merge($arDefIncludeParams, array("PATH" => SITE_DIR . "include_areas/sib/header/catalog_button.php")), false, array("HIDE_ICONS" => "Y")); ?>            
                    </div>
                	<div class="header-bottom__center">
                        <? $APPLICATION->IncludeComponent("bitrix:main.include", "", array_merge($arDefIncludeParams, array("PATH" => SITE_DIR . "include_areas/sib/header/search.php")), false, array("HIDE_ICONS" => "Y")); ?>
                    </div>  
                	<div class="header-bottom__right">
                    	<!-- BEGIN PHONE -->
                        <? 
                            //$phoneDefaultHtml = '<div class="header-phone js-click"><a class="header-phone__number" itemprop="telephone" href="callto:8 (383) 383-00-55"><span>8 (383)</span>  383-00-55</a> <button class="header-phone__button js-click-button"></button></div>';
                            //\Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("rbs_sib_phone");
                                $APPLICATION->IncludeComponent("bitrix:main.include", "", array_merge($arDefIncludeParams, array("PATH" => SITE_DIR . "include_areas/sib/header/sib_phone.php")), false, array("HIDE_ICONS" => "Y"));
                            //\Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("rbs_sib_phone", $phoneDefaultHtml);
                        ?>
                        <!-- PHONE EOF -->
                    	<!-- BEGIN BASKET -->
                        <? $APPLICATION->IncludeComponent("bitrix:main.include", "", array_merge($arDefIncludeParams, array("PATH" => SITE_DIR . "include_areas/sib/header/basket.php")), false, array("HIDE_ICONS" => "Y")); ?>
                        <!-- BASKET EOF -->
                        <!-- BEGIN MOBILE BASKET -->
                        <!-- In basket line -->
                        <!-- BASKET MOBILE EOF -->
                    </div>                                      
                </div>
            </div>
        </div>
        <!-- HEADER BOTTOM EOF -->     
        <?endif?>   
	</header>
	<!-- HEADER EOF -->
    <!-- BEGIN MAIN -->
    <?
        $mainClass = '';
        if ($isCatalogPage && !$isDetailPage){
            $mainClass .= 'main_pd main_catalog1 catalog-page';
        } else if($isDetailPage){
            $mainClass .= 'main_pd main_cart-open';
        } else if($isCartPage){
            $mainClass .= 'main_cart-empty';
        } else if($isOrderPage){
            $mainClass .= 'main_cart-empty main_order';            
        }
    ?>
    <main class="main <?=$mainClass?>">
    	<div class="wrapper">
            <? if($bMainPage):?>
                <div class="main-cols">
            <?endif;?>
                    <div class="main-cols__left">
                        <!-- BEGIN MAIN NAVIGATION -->
                        <?$APPLICATION->IncludeComponent("bitrix:main.include", "", array_merge($arDefIncludeParams, array("PATH" => SITE_DIR . "include_areas/sib/header/menu_catalog.php")), false, array("HIDE_ICONS" => "Y"));?>
                        <!-- MAIN NAVIGATION EOF -->
                        <!-- BEGIN MAIN NAVIGATION -->
                        <?//if(Mobile::isMobile()):?>
                            <?$APPLICATION->IncludeComponent("bitrix:main.include", "", array_merge($arDefIncludeParams, array("PATH" => SITE_DIR . "include_areas/sib/header/menu_mobile_catalog.php")), false, array("HIDE_ICONS" => "Y"));?>
                        <?//endif;?>
                        <!-- MAIN NAVIGATION EOF -->                    
                    </div>
            <? if($bMainPage):?>
                    <div class="main-cols__right">
                        <?//\Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("rbs_sib_big_slider");?>
                        <?$APPLICATION->IncludeComponent("bitrix:main.include", "", Array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR . "include_areas/sib/index/big_slider.php", "EDIT_TEMPLATE" => "include_areas_template.php"), false, array("HIDE_ICONS" => "Y"));?>
                        <?//\Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("rbs_sib_big_slider");?>
                    </div> 
                </div>
            <?endif;?>
        <? if(!$bMainPage && !$isOrderPage && !$isCartPage):?>
            <div class="box-bread-crumbs" id="rbs_breadcrumbs_div">
                <?
                    $dynamicArea = new \Bitrix\Main\Page\FrameStatic("rbs_breadcrumbs");
                    $dynamicArea->setContainerID("rbs_breadcrumbs_div");
                    $dynamicArea->startDynamicArea();
                ?>
                        <?if(!Mobile::isMobile()):?>
                            <?$APPLICATION->IncludeComponent("bitrix:breadcrumb", "sibdroid", array("START_FROM" => "1"),	false );?>
                        <?else:?>
                            <?$APPLICATION->IncludeComponent("bitrix:breadcrumb", "sibdroid_mobile", array("START_FROM" => "1"),	false );?>
                        <?endif;?>
                <?$dynamicArea->finishDynamicArea();?>
            </div>
        <?endif;?>