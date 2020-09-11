<?global $rz_b2_options?>
        <?$APPLICATION->IncludeComponent(
	"bitrix:advertising.banner",
	"bitronic2",
	Array(
		"TYPE" => "b2_index_single",
		"NOINDEX" => "Y",
		"PLACE_CLASS" => "container sBannerOne",
		"CACHE_TYPE" => "A",
        "ORDER_BANNER" => $rz_b2_options['order-sBannerOne'],
		"CACHE_TIME" => "3600"
	),
	false
);?>
