<? if (strlen($address = $APPLICATION->GetFileContent($_SERVER['DOCUMENT_ROOT'] . SITE_DIR . 'include_areas/sib/footer_sib/address.php')) > 0): ?>
	<? $APPLICATION->IncludeComponent(
	"bitrix:map.yandex.view", 
	"popup", 
	array(
		"ADDRESS_STR" => $address,
		"COMPONENT_TEMPLATE" => "popup",
		"INIT_MAP_TYPE" => "PUBLIC",
		"MAP_DATA" => "a:4:{s:10:\"yandex_lat\";d:54.982435669471215;s:10:\"yandex_lon\";d:82.90092409685865;s:12:\"yandex_scale\";i:16;s:10:\"PLACEMARKS\";a:1:{i:0;a:3:{s:3:\"LON\";d:82.90399254397168;s:3:\"LAT\";d:54.98339203341071;s:4:\"TEXT\";s:11:\"Sibdroid.ru\";}}}",
		"MAP_WIDTH" => "600",
		"MAP_HEIGHT" => "500",
		"CONTROLS" => array(
			0 => "ZOOM",
			1 => "MINIMAP",
			2 => "TYPECONTROL",
			3 => "SCALELINE",
		),
		"OPTIONS" => array(
			0 => "ENABLE_SCROLL_ZOOM",
			1 => "ENABLE_DBLCLICK_ZOOM",
			2 => "ENABLE_DRAGGING",
		),
		"MAP_ID" => "address_modal_map"
	),
	false
); ?>
<? endif ?>