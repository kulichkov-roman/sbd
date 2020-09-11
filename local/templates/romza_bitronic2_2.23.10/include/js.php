<?php
global $rz_b2_options;
//$asset->addJs(SITE_TEMPLATE_PATH . "/js/custom-scripts/ns.js");
$asset->addJs(SITE_TEMPLATE_PATH . "/js/back-end/utils.js");

//new js files in /new_js/ folder
//$asset->addJs(SITE_TEMPLATE_PATH . "/new_js/jquery-3.2.1.min.js");
$asset->addJs(SITE_TEMPLATE_PATH . "/new_js/jquery-migrate-1.4.1.min.js");

if ($isCatalogPage){
    $asset->addJs(SITE_TEMPLATE_PATH . "/new_js/jquery-ui-1.12.1.min.js");
    $asset->addJs(SITE_TEMPLATE_PATH . "/new_js/jquery.ui.touch-punch.js");
    //jquery.ui.touch-punch.min.js

    
}


// basic js libraries
$asset->addJs(SITE_TEMPLATE_PATH . "/js/3rd-party-libs/jquery.unevent.js");

$asset->addJs(SITE_TEMPLATE_PATH . "/js/back-end/ajax/search.js");
$asset->addJs(SITE_TEMPLATE_PATH . "/js/3rd-party-libs/modernizr-custom.js");
$asset->addJs(SITE_TEMPLATE_PATH . "/js/3rd-party-libs/requestAnimationFrame.min.js");
$asset->addJs(SITE_TEMPLATE_PATH . "/js/3rd-party-libs/velocity.min.js");
$asset->addJs(SITE_TEMPLATE_PATH . "/js/3rd-party-libs/wNumb.js");
$asset->addJs(SITE_TEMPLATE_PATH . "/js/3rd-party-libs/jquery.maskedinput.min.js");
$asset->addJs(SITE_TEMPLATE_PATH . "/js/3rd-party-libs/jquery.lazyload.js");
$asset->addJs(SITE_TEMPLATE_PATH . "/js/custom-scripts/libs/require.custom.js");


$asset->addJs(SITE_TEMPLATE_PATH . "/js/custom-scripts/inits/initGlobals.js");
$asset->addJs(SITE_TEMPLATE_PATH . "/js/custom-scripts/ready.js");


$asset->addJs(SITE_TEMPLATE_PATH . "/new_js/fancybox.js");
$asset->addJs(SITE_TEMPLATE_PATH . "/new_js/shave.js");
$asset->addJs(SITE_TEMPLATE_PATH . "/new_js/formstyler.js");
$asset->addJs(SITE_TEMPLATE_PATH . "/new_js/rating.js");
$asset->addJs(SITE_TEMPLATE_PATH . "/new_js/slick_190.js");
$asset->addJs(SITE_TEMPLATE_PATH . "/new_js/jquery.mCustomScrollbar.concat.min.js");

if(!$USER->IsAuthorized()){
    $asset->addJs(SITE_TEMPLATE_PATH . "/new_js/rbs-scripts/registration.js");
}

$asset->addJs(SITE_TEMPLATE_PATH . "/new_js/custom.js");


if (\Bitrix\Main\Loader::includeModule('developx.gcaptcha')) {
    $moduleObj = Developx\Gcaptcha\Options::getInstance();
    if ($moduleObj->checkCaptchaActive()) {
        $optCaptcha = $moduleObj->getOptions();
        $APPLICATION->AddHeadString('<script type="text/javascript" src="//www.google.com/recaptcha/api.js?render=' . $optCaptcha["CAPTCHA_KEY"] . '"></script>', true);
        $asset->addJs(SITE_TEMPLATE_PATH . "/new_js/gcaptcha.js");
    }
}

// AJAX
CJSCore::RegisterExt('rz_b2_ajax_core', array(
    'js' => SITE_TEMPLATE_PATH . "/js/back-end/ajax/core.js",
    'lang' => SITE_TEMPLATE_PATH . '/lang/' . LANGUAGE_ID . '/ajax.php',
    'rel' => array('core', 'currency')
));
CJSCore::RegisterExt('rz_b2_bx_catalog_item', array(
    'js' => SITE_TEMPLATE_PATH . "/js/back-end/bx_catalog_item.js",
    'lang' => SITE_TEMPLATE_PATH . '/lang/' . LANGUAGE_ID . '/ajax.php',
));
CJSCore::Init(array('rz_b2_ajax_core'));

