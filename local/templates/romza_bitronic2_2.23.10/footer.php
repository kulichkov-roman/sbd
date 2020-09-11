<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
use Bitronic2\Mobile;
use Yenisite\Core\Tools;
use Yenisite\Core\Page;

global $rz_b2_options;
//@var $arDefIncludeParams set in header.php
\Bitrix\Main\Localization\Loc::loadMessages('header');

if ($APPLICATION->GetPageProperty("showReviews") === 'Y'){
    require_once('include/footer/reviews.php');
}
if ($APPLICATION->GetPageProperty("showServices") === 'Y'){
    $APPLICATION->IncludeComponent("bitrix:main.include", "", array_merge($arDefIncludeParams, array("PATH" => SITE_DIR . "include_areas/sib/footer/services.php")), false, array("HIDE_ICONS" => "N"));
}
?>
    </div><!-- /wrapper -->
<?
if ($APPLICATION->GetPageProperty("showSubscribe") === 'Y'){
    require_once('include/footer/subscribe.php');
}
if ($APPLICATION->GetPageProperty("showCompanyDesc") === 'Y'){
    $APPLICATION->IncludeComponent("bitrix:main.include", "", array_merge($arDefIncludeParams, array("PATH" => SITE_DIR . "include_areas/sib/footer/company_desc.php")), false, array("HIDE_ICONS" => "N"));
}
?>
    </main><!-- /main -->
    <?if(!$isOrderPage):?>
    <footer class="footer <?if ($isCartPage):?>footer_small footer_empty<?endif?>">
        <div class="footer__main wrapper">
            <div class="footer__cols">
                <div class="footer__left">
                    <a class="footer-logo" href="<?= $isCartPage ? '#' : '/'?>">
                        <?$APPLICATION->IncludeComponent("bitrix:main.include", "", array_merge($arDefIncludeParams, array("PATH" => SITE_DIR . "include_areas/sib/footer/logo.php")), false, array("HIDE_ICONS" => "N"));?>
                    </a>
                    <?if (!$isCartPage):?>
                        <p class="footer-copyrights"><?$APPLICATION->IncludeComponent("bitrix:main.include", "", array_merge($arDefIncludeParams, array("PATH" => SITE_DIR . "include_areas/sib/footer/copyright.php")), false, array("HIDE_ICONS" => "N"));?></p>
                        <p class="footer-name"><?$APPLICATION->IncludeComponent("bitrix:main.include", "", array_merge($arDefIncludeParams, array("PATH" => SITE_DIR . "include_areas/sib/footer/company_name.php")), false, array("HIDE_ICONS" => "N"));?></p>
                        <?$APPLICATION->IncludeComponent("bitrix:main.include", "", array_merge($arDefIncludeParams, array("PATH" => SITE_DIR . "include_areas/sib/footer/social_icons.php")), false, array("HIDE_ICONS" => "N"));?>
                        <?/*?><p class="footer-team"><?$APPLICATION->IncludeComponent("bitrix:main.include", "", array_merge($arDefIncludeParams, array("PATH" => SITE_DIR . "include_areas/sib/footer/developer.php")), false, array("HIDE_ICONS" => "N"));?></p>
                        <?*/?>
                    <?endif?>
                </div>
                <div class="footer__center">
                    <div class="footer-in">
                        <?if (!$isCartPage):?>
                            <div class="footer-in__col">
                                <?$APPLICATION->IncludeComponent("bitrix:main.include", "", array_merge($arDefIncludeParams, array("PATH" => SITE_DIR . "include_areas/sib/footer/info_menu.php")), false, array("HIDE_ICONS" => "Y"));?>
                                <div class="footer-block footer-block_right">
                                    <p class="footer-block__title"><?$APPLICATION->IncludeComponent("bitrix:main.include", "", array_merge($arDefIncludeParams, array("PATH" => SITE_DIR . "include_areas/sib/footer/contact_title.php")), false, array("HIDE_ICONS" => "N"));?></p>
                                    <?$APPLICATION->IncludeComponent("bitrix:main.include", "", array_merge($arDefIncludeParams, array("PATH" => SITE_DIR . "include_areas/sib/footer/contact_email.php")), false, array("HIDE_ICONS" => "N"));?>
                                    
                                    <?
                                        //\Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("rbs_footer_phone");
                                            $APPLICATION->IncludeComponent("bitrix:main.include", "", array_merge($arDefIncludeParams, array("PATH" => SITE_DIR . "include_areas/sib/footer/contact_phone.php")), false, array("HIDE_ICONS" => "N"));
                                        //\Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("rbs_footer_phone")
                                    ?>
                                </div>
                            </div>
                            <div class="footer-in__col">
                                <?$APPLICATION->IncludeComponent("bitrix:main.include", "", array_merge($arDefIncludeParams, array("PATH" => SITE_DIR . "include_areas/sib/footer/customer_menu.php")), false, array("HIDE_ICONS" => "Y"));?>
                            </div>
                            <div class="footer-in__col">
                                <?$APPLICATION->IncludeComponent("bitrix:main.include", "", array_merge($arDefIncludeParams, array("PATH" => SITE_DIR . "include_areas/sib/footer/catalog_menu.php")), false, array("HIDE_ICONS" => "Y"));?>
                            </div>
                        <?else:?>
                            <div class="footer-in__col">
                                <div class="footer-block footer-block_right">
                                    <?$APPLICATION->IncludeComponent("bitrix:main.include", "", array_merge($arDefIncludeParams, array("PATH" => SITE_DIR . "include_areas/sib/footer/contact_phone.php")), false, array("HIDE_ICONS" => "N"));?>
                                </div>
                            </div>
                        <?endif?>
                    </div>
                </div>
                <?if (!$isCartPage):?>
                    <div class="footer__right">
                    <div class="yandex" onclick="window.open('https://market.yandex.ru/shop--sibdroid-ru/307694/reviews?cpc=srxrGCIO1EEklClLRuasO1uVNrZIignkzyaRrTT8LznXcaNRLFTLa0egn8-TubYFjFRT40IMH_oy6cysa4H6_I15nJJtLtfdaJnnbLV2x6nT3YdeJrDG-ny0o9LCle0ePknkpwJFWlEHtzq5Or3P4Q%2C%2C&cmid=M8NLG09T1LTe4RhKr4axvQ&track=default_offer_reviews_link', '_blank')">
                        <div class="yandex__image">
                            <?$APPLICATION->IncludeComponent("bitrix:main.include", "", array_merge($arDefIncludeParams, array("PATH" => SITE_DIR . "include_areas/sib/footer/yandex/logo.php")), false, array("HIDE_ICONS" => "N"));?>
                        </div>
                        <div class="rating">
                            <?$APPLICATION->IncludeComponent("bitrix:main.include", "", array_merge($arDefIncludeParams, array("PATH" => SITE_DIR . "include_areas/sib/footer/yandex/rating.php")), false, array("HIDE_ICONS" => "N"));?>
                        </div>
                        <p class="yandex__text"><?$APPLICATION->IncludeComponent("bitrix:main.include", "", array_merge($arDefIncludeParams, array("PATH" => SITE_DIR . "include_areas/sib/footer/yandex/trust.php")), false, array("HIDE_ICONS" => "N"));?></p>
                    </div>
                    <div class="yandex yazen">
                        <?$APPLICATION->IncludeComponent("bitrix:main.include", "", array_merge($arDefIncludeParams, array("PATH" => SITE_DIR . "include_areas/sib/footer/yandex/dzen.php")), false, array("HIDE_ICONS" => "N"));?>
                    </div>
                    <div class="footer-pays">
                        <p class="footer-pays__title"><?$APPLICATION->IncludeComponent("bitrix:main.include", "", array_merge($arDefIncludeParams, array("PATH" => SITE_DIR . "include_areas/sib/footer/payment/payment_title.php")), false, array("HIDE_ICONS" => "N"));?></p>
                        <?$APPLICATION->IncludeComponent("bitrix:main.include", "", array_merge($arDefIncludeParams, array("PATH" => SITE_DIR . "include_areas/sib/footer/payment/payment_logo.php")), false, array("HIDE_ICONS" => "N"));?>
                        <p class="footer-pays__text"><?$APPLICATION->IncludeComponent("bitrix:main.include", "", array_merge($arDefIncludeParams, array("PATH" => SITE_DIR . "include_areas/sib/footer/payment/payment_info.php")), false, array("HIDE_ICONS" => "N"));?></p>
                    </div>
                </div>
                <?endif?>
            </div>
        </div>
    </footer>
    <?endif?>
    </div><!-- /main-wrap --><? // opened in header.php ?>
        <? include 'include/footer/modals.php';?>
        <script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/new_js/jquery-3.2.1.min.js" data-skip-moving="true"></script>
        <?if(!$_SESSION['is_bot'] && !isset($_REQUEST['noredirect'])  /* && !$_SESSION['is_dev'] */):?>
            <script>
                var askCity = function(){
                    if(RZB2.utils.getCookie('IS_ASKED_CITY') !== 'asked'){
                        if($(window).width() < 767){
                            $('.is-your-city').removeClass('popup-arrow');
                            $('.is-your-city').css({position:'static'});
                            $('header').prepend($('.is-your-city'));
                        }
                        $('.is-your-city .location__city').text($('#rbs_city_name_header').text());
                        $('.is-your-city').show();
                        RZB2.utils.setCookie('IS_ASKED_CITY', 'asked');
                    }
                }
                if(RZB2.utils.getCookie('IS_REDIRECTED') !== 'redirected'){
                    $.ajax({
                        url: '/ajax/sib/city_detect.php',
                        async: true,
                        dataType: 'json',
                        data: {request_uri: '<?=$_SERVER['REQUEST_URI']?>'},
                        success: function(data){ 
                            if(!!data.LINK){

                                RZB2.utils.setCookie('IS_REDIRECTED', 'redirected');

                                if(data.LINK !== 'default'){
                                    window.location.href = data.LINK;
                                } else {
                                    askCity();
                                }                          
                            }                        
                        }
                    });
                } else {
                    askCity();
                }
            </script> 
        <?endif?>
            <?
                $sessid = bitrix_sessid();
                $callbaCookie = $APPLICATION->get_cookie($sessid);
                if ($_GET['callback'] == 'Y' || !empty($callbaCookie)) {
                    $APPLICATION->set_cookie($sessid, 156);
                    \Yenisite\Core\Tools::IncludeArea('sib/footer_sib', 'callbackform', false, false);
                }
            ?>
        <script async onload="initSvgSprites()" type="text/javascript" data-skip-moving="true" src="<?= SITE_TEMPLATE_PATH ?>/fonts/svg.js"></script>
        <?if (!$USER->IsAdmin() && !$_SESSION['is_bot']): ?>
            <?//$bSibCore = \Bitrix\Main\Loader::includeModule('sib.core');?>
            <?//\Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("rbs_jivo_site");?>
            <script type='text/javascript'>
                function jivo_onLoadCallback() {document.jivo_container.Audio.prototype.play = function() {};}
                (function(){ var widget_id =  '<?=\Sib\Core\Catalog::isMskRegion($_SESSION["VREGIONS_REGION"]['ID']) ? '8A6pBGdcUq' : '5dkEEcBUgv'?>';var d=document;var w=window;function l(){
                var s = document.createElement('script'); s.type = 'text/javascript'; s.async = true; s.src = '//code.jivosite.com/script/widget/'+widget_id; var ss = document.getElementsByTagName('script')[0]; ss.parentNode.insertBefore(s, ss);}if(d.readyState=='complete'){l();}else{if(w.attachEvent){w.attachEvent('onload',l);}else{w.addEventListener('load',l,false);}}})();
            </script>    
            <?//\Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("rbs_jivo_site");?>
        <? endif; ?>

        <script type="text/javascript" data-skip-moving="true">
            window.vkAsyncInitCallbacks.push(function(){
                if(bMainPage){
                    const eventParams = {};
                    VK.Retargeting.ProductEvent(PRICE_LIST_ID, "view_home", eventParams);
                }
            });
            window.vkAsyncInit = function() {
                VK.Retargeting.Init('VK-RTRG-140081-bUeHl');
                //VK.Retargeting.Hit();
            };
        </script>
        <?if(!$isCartPage && !$isOrderPage):?>
            <script async src="<?=SITE_TEMPLATE_PATH?>/new_js/rbs-scripts/up.min.js"></script>
        <?endif?>
        <div id="vk_api_transport">
            <script src="//vk.com/js/api/openapi.js" async data-skip-moving="true"></script>
        </div>
    </body>
</html>
<? Page::setOGProperty('title', $APPLICATION->GetTitle(false));