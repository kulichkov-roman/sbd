<?php
//new css files in /new_css/ folder
//$APPLICATION->AddHeadString('<link href="//fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,400,700&amp;subset=cyrillic-ext,latin" rel="stylesheet" type="text/css">');

//$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/css/s.min.css");
$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/new_css/b2styles.css");
//$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/templates_addon.css");
$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/template_styles.css");
$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/css/developers.css");
//$APPLICATION->SetAdditionalCSS("/bitrix/js/socialservices/css/ss.css");

//new css
$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/new_css/style.css");
if ($isDetailPage) {
    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/new_css/jquery.mCustomScrollbar.css");    
}

if ($isCatalogPage)
{
    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/new_css/jquery.formstyler.css");
    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/new_css/jquery-ui-1.12.1.min.css");
}

if ($isCatalogPage || $isOrderPage || $isPersonalPage)
    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/new_css/style_1.css");

if ($isComparePage || $isOrderPage)
{
    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/new_css/style_2.css");
    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/new_css/jquery.mCustomScrollbar.css");
}

if ($isPersonalPage && !$isCartPage)
    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/new_css/style_3.css");

$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/new_css/rbs_custom_css.css");