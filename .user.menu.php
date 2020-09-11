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
		"Текущие заказы",
		"/personal/orders/",
		Array(),
		Array("ITEM_CLASS"=>"", "ICON_SVG"=>"interface"),
		""
	),
	Array(
		"Личный счет",
		"/personal/account/",
		Array(),
		Array("ITEM_CLASS"=>"", "ICON_SVG"=>"tool"),
		""
	),
	Array(
		"Личные данные",
		"/personal/profile/",
		Array(),
		Array("ITEM_CLASS"=>"", "ICON_SVG"=>"user-card"),
		""
	),
	Array(
		"История заказов",
		"/personal/orders/?filter_history=Y",
		Array(),
		Array("ITEM_CLASS"=>"hide", "ICON_SVG"=>"list-on-window"),
		""
	),
	Array(
		"Профили заказов",
		"/personal/profiles/",
		Array(),
		Array("ITEM_CLASS"=>"", "ICON_SVG"=>"profiles"),
		"CRZBitronic2Settings::isPro()"
	),
	Array(
		"Корзина",
		"/personal/cart/",
		Array(),
		Array("ITEM_CLASS"=>"hide", "ICON_SVG"=>"cart"),
		""
	),
	Array(
		"Подписки на товары",
		"/personal/products/",
		Array(),
		Array("ITEM_CLASS"=>"", "ICON_SVG"=>"megaphone"),
		"CModule::IncludeModule('yenisite.feedback')"
	),
	Array(
		"Контакты",
		"/contacts/",
		Array(),
		Array("ITEM_CLASS"=>"hide", "ICON_SVG"=>"contacts-shop"),
		""
	),
	Array(
		"Избранное",
		"#favorite",
		Array(),
		Array("ITEM_CLASS"=>"hide", "ICON_SVG"=>"favorite"),
		"\$GLOBALS['rz_b2_options']['block_show_favorite'] == 'Y' && CModule::IncludeModule('yenisite.favorite')"
	),
	Array(
		"Список сравнения",
		"/catalog/compare/list/?action=COMPARE",
		Array(),
		Array("ITEM_CLASS"=>"hide", "ICON_SVG"=>"compare"),
		"\$GLOBALS['rz_b2_options']['block_show_compare'] == 'Y'"
	),
	Array(
		"Подписки",
		"/personal/subscribe/",
		Array(),
		Array("ITEM_CLASS"=>"", "ICON_SVG"=>"messege"),
		"IsModuleInstalled('subscribe')"
	),
	Array(
		"Выход",
		"?logout=yes",
		Array(),
		Array("ITEM_CLASS"=>"", "ICON_SVG"=>"logout"),
		""
	)
);
?>