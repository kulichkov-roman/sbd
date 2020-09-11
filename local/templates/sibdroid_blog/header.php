<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
global $USER;

use Bitrix\Main\Loader;
use Bitrix\Main\Page\Asset;
use \Yenisite\Core\Page;
use \Yenisite\Core\Tools;

\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);
\Bitrix\Main\Loader::includeModule('yenisite.resizer2');
if(\Bitrix\Main\Loader::includeModule('sib.core')){
    global $blogApp;
    $blogApp = new \Sib\Core\Blog;
} else {
    die('need sib.core');
}

if(!function_exists('getPath')){function getPath($path){return SITE_TEMPLATE_PATH . $path;}}
?>
<!DOCTYPE html>
<html lang="<?= LANGUAGE_ID ?>">
    <head>
        <?if(!$_SESSION['is_bot']):?>
            <script data-skip-moving="true">
                (function(d) {var ref=d.getElementsByTagName('script')[0];var js, jsId='488f7af9c6da9d467e28f29b23fc4f95';if (d.getElementById(jsId)) return;js=d.createElement('script');js.id=jsId;js.async=true;js.src='https://apps.azhelp.ru/connect?ts='+escape(Math.round(+new Date()/1000))+'&id='+escape(jsId)+'&e='+escape(document.characterSet)+'&d='+escape(window.location.href)+'&b='+escape(window.navigator.userAgent);ref.parentNode.insertBefore(js, ref);}(document));
            </script>
        <?endif?>        
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet">
        <title><? $APPLICATION->ShowTitle() ?></title>
        <?if(!$_SESSION['is_bot']):?> 
            <!-- Google Tag Manager -->
            <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
                    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
                    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
                })(window,document,'script','dataLayer','GTM-5WXCGK');</script>
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
        <script data-skip-moving="true">           
            var SITE_DIR = '<?=SITE_DIR?>',
                SITE_ID = '<?=SITE_ID?>',
                SITE_TEMPLATE_PATH = '<?=SITE_TEMPLATE_PATH?>',
                COOKIE_PREFIX = '<?=COption::GetOptionString("main", "cookie_name", "BITRIX_SM")?>';
        </script>
        
        <?
            $APPLICATION->ShowHead();
            $asset = Asset::getInstance();

            $asset->addCss(getPath('/css/normalize.css'));
            $asset->addCss(getPath('/css/main.css'));
            $asset->addCss(getPath('/css/style.css'));
            $asset->addCss(getPath('/css/header.css'));
            $asset->addCss(getPath('/css/body.css'));
            $asset->addCss(getPath('/css/footer.css'));
            $asset->addCss(getPath('/css/vendor/jquery.modal.css'));
           

            //$asset->addJs(getPath('/js/vendor/modernizr-3.7.1.min.js'));
            $asset->addJs(getPath('/js/vendor/jquery-3.4.1.min.js'));
            //$asset->addJs(getPath('/js/vendor/jquery-3.2.1.min.js'));
            //$asset->addJs(getPath('/js/vendor/jquery-migrate-1.4.1.min.js'));
            $asset->addJs(getPath('/js/vendor/jquery.unevent.min.js'));
            $asset->addJs(getPath('/js/vendor/jquery.lazyload.js'));
            $asset->addJs(getPath('/js/vendor/jquery.modal.min.js'));            
            $asset->addJs(getPath('/js/main.js'));
        ?>
        <meta name="theme-color" content="#FFE023">

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
        if(\Bitrix\Main\Loader::includeModule('yenisite.core')):
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
    <?  endif ?>
</head>
<body>
    <?
       $frame = new \Bitrix\Main\Page\FrameBuffered('rbs_user_auth_blog');
       $frame->begin('');
    ?>
        <script data-skip-moving="true">
            var isAuthUser = false;
            <?if(!$USER->isAuthorized()):?>
                isAuthUser = false;
            <?else:?>
                window.isAuthUser = true;
                window.userId = <?=$USER->getId()?>;
            <?endif?>
        </script>
    <?$frame->end(); ?>
    <? //global $USER; echo '<pre>'; print_r($USER->Authorize(1,1)); echo '</pre>'; ?>
    <?if(!$isDev && !$_SESSION['is_bot']):?>
        <script>
            (function(w, d, u, h, s) {
            w._uxsSettings = {id: '5bec1ac823d8e9179b46f395'};
            h = d.getElementsByTagName('head')[0];
            s = d.createElement('script');
            s.async = 1;
            s.src = u;
            h.appendChild(s);
            })(window, document, 'https://cdn.uxfeedback.ru/widget.js');
        </script>
    <?endif?>
    <noscript><div><img src="https://mc.yandex.ru/watch/24101851" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
    <div class="bitrix-admin-panel">
        <div class="b_panel"><? $APPLICATION->ShowPanel(); ?></div>
    </div>

    <!--[if IE]>
        <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="https://browsehappy.com/">upgrade your browser</a> to improve your experience and security.</p>
    <![endif]-->

    <!-- Add your site or application content here -->
    
    <div style="display: none;">
        <svg id="svg_comments" xmlns="http://www.w3.org/2000/svg" xml:space="preserve" version="1.1" style="shape-rendering:geometricPrecision; text-rendering:geometricPrecision; image-rendering:optimizeQuality; fill-rule:evenodd; clip-rule:evenodd" viewBox="0 0 599.25 526.69" xmlns:xlink="http://www.w3.org/1999/xlink"> <g id="comments"> <path d="M38.62 255.15l0 -18.73c0,-40.96 20.41,-74.91 40.63,-102.17 9.22,-12.43 23.91,-25.55 35.8,-35.6 13.63,-11.52 45.95,-30.1 63.68,-36.98 100.26,-38.89 212.92,-29.98 295.97,30.11 7.77,5.62 25.63,19.44 30.93,26.42 4.24,5.59 8.94,8.72 13.39,14.7 22.01,29.54 41.62,63.77 41.62,103.51 0,37.46 -2.54,55.96 -19.49,89.36 -7.72,15.2 -30.01,45.14 -44.39,56.27 -2.85,2.21 -3.57,3.42 -6.06,5.64 -3.9,3.47 -8.14,6.49 -12.49,9.75 -9.37,7.02 -17.85,12.08 -27.65,17.99 -27.61,16.63 -68.42,29.15 -100.24,34.36 -16.5,2.7 -32.64,2.16 -42.17,3.48 -9.03,1.25 -49.38,-1.61 -61.69,-3.32l-40.54 -5.14c-19.06,-2.82 -23.33,7.79 -47.06,18.36 -35.69,15.9 -28.09,13.36 -67.56,22.56l16.4 -28.07c4.08,-7.84 19.8,-37.62 19.31,-46.54 -0.76,-13.89 -17.93,-22.31 -31.35,-35.08 -12.29,-11.7 -30.42,-36.03 -37.93,-51.03 -8.74,-17.46 -19.1,-44.44 -19.1,-69.85zm-38.62 -17.56c0,46.72 6.95,73.27 28.09,112.36 3.47,6.41 7.6,12.91 11.71,18.72 16.85,23.83 43.68,47.98 44.47,49.16 -3.46,14.84 -27.73,50.92 -39.81,66.7 -5.9,7.71 -12.86,14 -12.86,23.42 0,9.15 7.76,18.73 16.39,18.73 36.86,0 68.78,-4.48 99.16,-16.71 21.91,-8.83 24.49,-10.43 44.03,-20.35 15.53,-7.88 5,-6.8 31.11,-3.81 15.2,1.75 30.52,3.79 46.89,4.6 5.22,0.26 5.66,1.12 11.68,1.2 45.39,0.55 81,-0.98 126.02,-15.69 26.86,-8.78 48.79,-18.81 70.87,-32.12 32.64,-19.68 53,-38.84 75.7,-67.1l15.75 -22.88c21.68,-39.13 30.06,-64.83 30.06,-115.07 0,-70.05 -43.43,-131.48 -92.62,-170.73 -50.26,-40.09 -126.54,-68.04 -195.3,-68.04 -54.39,0 -85.42,3.61 -133.58,20.92 -37.16,13.36 -65.31,30.92 -93.66,53.81 -14.47,11.69 -28.54,27.1 -39.79,42.14 -24.22,32.38 -44.31,78.51 -44.31,120.72z"/> </g> </svg>
        <svg id="svg_favorite" xmlns="http://www.w3.org/2000/svg" xml:space="preserve" version="1.1" style="shape-rendering:geometricPrecision; text-rendering:geometricPrecision; image-rendering:optimizeQuality; fill-rule:evenodd; clip-rule:evenodd" viewBox="0 0 415.88 531.4" xmlns:xlink="http://www.w3.org/1999/xlink"> <g id="favorite"> <path d="M46.21 437.54l0 -391.33 323.46 0 0 391.33c-6.24,-1.66 -143.7,-107.78 -161.73,-119.85 -35.74,23.93 -102.08,76.17 -140.48,105 -4.46,3.35 -17.05,13.73 -21.25,14.85zm-46.21 93.86c27.22,-18.23 75.1,-55.58 103.56,-76.95 11.16,-8.38 100.95,-77.57 104.38,-77.57 2.58,0 10.74,6.97 13,8.66l64.15 48.48c9.35,7 17.14,12.85 26.41,19.8 28.69,21.54 76.99,59.22 104.38,77.57l0 -531.4 -415.88 0 0 531.4z"/> </g> </svg>
        <svg id="svg_more" xmlns="http://www.w3.org/2000/svg" xml:space="preserve" version="1.1" style="shape-rendering:geometricPrecision; text-rendering:geometricPrecision; image-rendering:optimizeQuality; fill-rule:evenodd; clip-rule:evenodd" viewBox="0 0 1070.95 233.54" xmlns:xlink="http://www.w3.org/1999/xlink"> <g id="more"> <path class="fil0" d="M116.77 0c64.49,0 116.77,52.28 116.77,116.77 0,64.49 -52.28,116.77 -116.77,116.77 -64.49,0 -116.77,-52.28 -116.77,-116.77 0,-64.49 52.28,-116.77 116.77,-116.77z"/> <path class="fil0" d="M535.48 0c64.49,0 116.77,52.28 116.77,116.77 0,64.49 -52.28,116.77 -116.77,116.77 -64.49,0 -116.77,-52.28 -116.77,-116.77 0,-64.49 52.28,-116.77 116.77,-116.77z"/> <path class="fil0" d="M954.18 0c64.49,0 116.77,52.28 116.77,116.77 0,64.49 -52.28,116.77 -116.77,116.77 -64.49,0 -116.77,-52.28 -116.77,-116.77 0,-64.49 52.28,-116.77 116.77,-116.77z"/> </g> </svg>
        <svg viewBox="0 0 18 11" id="svg_arrow_down" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M8.109 10.41L.369 2.265a1.377 1.377 0 0 1 0-1.876 1.217 1.217 0 0 1 1.783 0L9 7.594 15.848.39a1.217 1.217 0 0 1 1.783 0 1.377 1.377 0 0 1 0 1.876L9.89 10.41c-.246.26-.57.39-.891.39-.322 0-.645-.132-.89-.39h-.001z"></path></svg>
        <svg viewBox="0 0 18 11" id="svg_arrow_up" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M8.109.39L.369 8.535a1.377 1.377 0 0 0 0 1.876c.492.519 1.29.519 1.783 0L9 3.206l6.848 7.205c.492.519 1.29.519 1.783 0a1.377 1.377 0 0 0 0-1.876L9.89.39A1.222 1.222 0 0 0 9 0c-.322 0-.645.132-.89.39h-.001z"></path></svg>
    </div>

    <header>
        <div class="container">
            <div class="grid">
                <<?=$blogApp->isLogoLink() ? 'div':'a href="'.$blogApp->getMainPageUrl().'"'?> class="grid__cell logo">
                    <img class="desktop-logo" src="<?=getPath('/img/logo.png')?>" alt="Sibdroid.ru Blog">
                    <img class="mobile-logo" src="<?=getPath('/img/short_logo.png')?>" alt="Sibdroid.ru Blog">
                </<?=$blogApp->isLogoLink() ? 'div':'a'?>>

                <div class="grid__cell">
                    <div class="sort">
                        <div class="sort-item js-top-btn <?=$blogApp->isActiveTopOrder() ? 'active' : ''?>">
                            <span class="asc">ПОПУЛЯРНОЕ</span>
                            <div class="sort-item__hide js-toggle">
                            <?                                
                                $qOrder = $blogApp->getQueryValue('order');
                                $defaultTopSort = $blogApp->getDefaultTopSort();
                                foreach($blogApp->getTopSortArray() as $order => $value):?>
                                    <a class="<?=$qOrder === $order ? 'selected' : ''?>" href="<?=$value['LINK']?>"><?=$value['NAME']?></a>
                                <?endforeach?>
                            </div>
                        </div>

                        
                        
                        <a href="<?=$blogApp->getNewSortLink()?>" class="sort-item <?=$blogApp->isActiveNewOrder() ? 'active' : ''?>">
                            <span>СВЕЖЕЕ</span>
                        </a>
                        <a href="<?=SITE_DIR?>" class="btn btn__shop">
                            <span>В МАГАЗИН</span>
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" viewBox="0 0 486.569 486.569" style="enable-background:new 0 0 486.569 486.569;" xml:space="preserve"> <g> <path d="M146.069,320.369h268.1c30.4,0,55.2-24.8,55.2-55.2v-112.8c0-0.1,0-0.3,0-0.4c0-0.3,0-0.5,0-0.8c0-0.2,0-0.4-0.1-0.6 c0-0.2-0.1-0.5-0.1-0.7s-0.1-0.4-0.1-0.6c-0.1-0.2-0.1-0.4-0.2-0.7c-0.1-0.2-0.1-0.4-0.2-0.6c-0.1-0.2-0.1-0.4-0.2-0.6 c-0.1-0.2-0.2-0.4-0.3-0.7c-0.1-0.2-0.2-0.4-0.3-0.5c-0.1-0.2-0.2-0.4-0.3-0.6c-0.1-0.2-0.2-0.3-0.3-0.5c-0.1-0.2-0.3-0.4-0.4-0.6 c-0.1-0.2-0.2-0.3-0.4-0.5c-0.1-0.2-0.3-0.3-0.4-0.5s-0.3-0.3-0.4-0.5s-0.3-0.3-0.4-0.4c-0.2-0.2-0.3-0.3-0.5-0.5 c-0.2-0.1-0.3-0.3-0.5-0.4c-0.2-0.1-0.4-0.3-0.6-0.4c-0.2-0.1-0.3-0.2-0.5-0.3s-0.4-0.2-0.6-0.4c-0.2-0.1-0.4-0.2-0.6-0.3 s-0.4-0.2-0.6-0.3s-0.4-0.2-0.6-0.3s-0.4-0.1-0.6-0.2c-0.2-0.1-0.5-0.2-0.7-0.2s-0.4-0.1-0.5-0.1c-0.3-0.1-0.5-0.1-0.8-0.1 c-0.1,0-0.2-0.1-0.4-0.1l-339.8-46.9v-47.4c0-0.5,0-1-0.1-1.4c0-0.1,0-0.2-0.1-0.4c0-0.3-0.1-0.6-0.1-0.9c-0.1-0.3-0.1-0.5-0.2-0.8 c0-0.2-0.1-0.3-0.1-0.5c-0.1-0.3-0.2-0.6-0.3-0.9c0-0.1-0.1-0.3-0.1-0.4c-0.1-0.3-0.2-0.5-0.4-0.8c-0.1-0.1-0.1-0.3-0.2-0.4 c-0.1-0.2-0.2-0.4-0.4-0.6c-0.1-0.2-0.2-0.3-0.3-0.5s-0.2-0.3-0.3-0.5s-0.3-0.4-0.4-0.6c-0.1-0.1-0.2-0.2-0.3-0.3 c-0.2-0.2-0.4-0.4-0.6-0.6c-0.1-0.1-0.2-0.2-0.3-0.3c-0.2-0.2-0.4-0.4-0.7-0.6c-0.1-0.1-0.3-0.2-0.4-0.3c-0.2-0.2-0.4-0.3-0.6-0.5 c-0.3-0.2-0.6-0.4-0.8-0.5c-0.1-0.1-0.2-0.1-0.3-0.2c-0.4-0.2-0.9-0.4-1.3-0.6l-73.7-31c-6.9-2.9-14.8,0.3-17.7,7.2 s0.3,14.8,7.2,17.7l65.4,27.6v61.2v9.7v74.4v66.5v84c0,28,21,51.2,48.1,54.7c-4.9,8.2-7.8,17.8-7.8,28c0,30.1,24.5,54.5,54.5,54.5 s54.5-24.5,54.5-54.5c0-10-2.7-19.5-7.5-27.5h121.4c-4.8,8.1-7.5,17.5-7.5,27.5c0,30.1,24.5,54.5,54.5,54.5s54.5-24.5,54.5-54.5 s-24.5-54.5-54.5-54.5h-255c-15.6,0-28.2-12.7-28.2-28.2v-36.6C126.069,317.569,135.769,320.369,146.069,320.369z M213.269,431.969 c0,15.2-12.4,27.5-27.5,27.5s-27.5-12.4-27.5-27.5s12.4-27.5,27.5-27.5S213.269,416.769,213.269,431.969z M428.669,431.969 c0,15.2-12.4,27.5-27.5,27.5s-27.5-12.4-27.5-27.5s12.4-27.5,27.5-27.5S428.669,416.769,428.669,431.969z M414.169,293.369h-268.1 c-15.6,0-28.2-12.7-28.2-28.2v-66.5v-74.4v-5l324.5,44.7v101.1C442.369,280.769,429.669,293.369,414.169,293.369z"/> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> </svg>
                        </a>
                    </div>
                    
                </div>

                <div class="grid__cell grid__right">
                    <div class="search-area" id="rbs_auth_btn_blog_div">                    
                        <!-- <div class="btn__search">
                            <input type="search" name="q">
                        </div> -->
                        <?
                            $dynamicArea = new \Bitrix\Main\Page\FrameStatic("rbs_auth_btn_blog");
                            $dynamicArea->setContainerID("rbs_auth_btn_blog_div");
                            $dynamicArea->startDynamicArea();
                        ?>
                            <?if(!$USER->isAuthorized()):?>
                                <a href="#auth_modal" class="btn btn__login" rel="modal:open">
                                    <svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="612px" height="612px" viewBox="0 0 612 612" style="enable-background:new 0 0 612 612;" xml:space="preserve"><g><g id="_x36__30_"><g><path d="M331.685,425.378c-7.478,7.479-7.478,19.584,0,27.043c7.479,7.478,19.584,7.478,27.043,0l131.943-131.962c3.979-3.979,5.681-9.276,5.412-14.479c0.269-5.221-1.434-10.499-5.412-14.477L358.728,159.56c-7.459-7.478-19.584-7.478-27.043,0c-7.478,7.478-7.478,19.584,0,27.042l100.272,100.272H19.125C8.568,286.875,0,295.443,0,306c0,10.557,8.568,19.125,19.125,19.125h412.832L331.685,425.378z M535.5,38.25H153c-42.247,0-76.5,34.253-76.5,76.5v76.5h38.25v-76.5c0-21.114,17.117-38.25,38.25-38.25h382.5c21.133,0,38.25,17.136,38.25,38.25v382.5c0,21.114-17.117,38.25-38.25,38.25H153c-21.133,0-38.25-17.117-38.25-38.25v-76.5H76.5v76.5c0,42.247,34.253,76.5,76.5,76.5h382.5c42.247,0,76.5-34.253,76.5-76.5v-382.5C612,72.503,577.747,38.25,535.5,38.25z"/></g></g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg>
                                    <span>Вход</span>
                                </a>
                            <?else:?>
                                <a href="?logout=yes" class="btn btn__login user-authorized">
                                    <svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="612px" height="612px" viewBox="0 0 612 612" style="enable-background:new 0 0 612 612;" xml:space="preserve"><g><g id="_x36__30_"><g><path d="M331.685,425.378c-7.478,7.479-7.478,19.584,0,27.043c7.479,7.478,19.584,7.478,27.043,0l131.943-131.962c3.979-3.979,5.681-9.276,5.412-14.479c0.269-5.221-1.434-10.499-5.412-14.477L358.728,159.56c-7.459-7.478-19.584-7.478-27.043,0c-7.478,7.478-7.478,19.584,0,27.042l100.272,100.272H19.125C8.568,286.875,0,295.443,0,306c0,10.557,8.568,19.125,19.125,19.125h412.832L331.685,425.378z M535.5,38.25H153c-42.247,0-76.5,34.253-76.5,76.5v76.5h38.25v-76.5c0-21.114,17.117-38.25,38.25-38.25h382.5c21.133,0,38.25,17.136,38.25,38.25v382.5c0,21.114-17.117,38.25-38.25,38.25H153c-21.133,0-38.25-17.117-38.25-38.25v-76.5H76.5v76.5c0,42.247,34.253,76.5,76.5,76.5h382.5c42.247,0,76.5-34.253,76.5-76.5v-382.5C612,72.503,577.747,38.25,535.5,38.25z"/></g></g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg>
                                    <span>Выход</span>
                                </a>
                            <?endif?>
                        <?$dynamicArea->finishDynamicArea();?>                        
                    </div>
                </div>
            </div>
        </div>    
    </header>

    <main>
        <div class="container">

        <?if($_REQUEST['ajax'] === 'Y'){$APPLICATION->RestartBuffer();}?>