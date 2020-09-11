<?
$aMenuLinks = Array(
    Array(
        "Мой кабинет",
        "/personal/",
        Array(),
        Array("ITEM_CLASS"=>"", "ICON_SVG"=>"home"),
        ""
    ),
    Array(
        "Мои заказы",
        "/personal/orders/",
        Array(),
        Array("ITEM_CLASS"=>"", "ICON_SVG"=>"orders"),
        ""
    ),
    Array(
        "Личные данные",
        "/personal/profile/",
        Array(),
        Array("ITEM_CLASS"=>"", "ICON_SVG"=>"data"),
        ""
    ),
   /*  Array(
        "Подписки на товары",
        "/personal/products/",
        Array(),
        Array("ITEM_CLASS"=>"", "ICON_SVG"=>"subscribe"),
        "CModule::IncludeModule('yenisite.feedback')"
    ), */
    Array(
        "Настройки рассылки",
        "/personal/subscribe/",
        Array(),
        Array("ITEM_CLASS"=>"", "ICON_SVG"=>"settings"),
        "IsModuleInstalled('subscribe')"
    ),
    Array(
        "Выход",
        "?logout=yes",
        Array(),
        Array("ITEM_CLASS"=>"", "ICON_SVG"=>"exit"),
        ""
    )
);
?>