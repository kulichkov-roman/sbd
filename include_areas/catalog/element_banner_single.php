<?global $rz_b2_options?>
        <?$APPLICATION->IncludeComponent(
	"bitrix:advertising.banner",
	"bitronic2",
	Array(
		"NUM" => $GLOBALS["rz_banner_num"]++,
		"TYPE" => "b2_catalog_element_single",
		"NOINDEX" => "Y",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600"
	),
	false
);?>
