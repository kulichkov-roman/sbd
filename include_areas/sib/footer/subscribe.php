<?
$APPLICATION->IncludeComponent("bitrix:subscribe.form", "sib_default", Array(
    "COMPONENT_TEMPLATE" => "sib_default",
    "USE_PERSONALIZATION" => "Y",
    "SHOW_HIDDEN" => "N",
    "PAGE" => "/personal/subscribe/subscr_edit.php",
    "CACHE_TYPE" => "A",
    "CACHE_TIME" => "3600",
),
    false
);
?>