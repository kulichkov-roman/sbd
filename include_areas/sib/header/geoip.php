<? global $rz_b2_options;
if ($rz_b2_options['block_show_geoip'] == 'Y' && CModule::IncludeModule('yenisite.geoip')): ?>
	<? ob_start() ?>
	<? $APPLICATION->IncludeComponent(
		"yenisite:geoip.city",
		"bitronic2",
		array(
			"CACHE_TYPE" => "A",
			"CACHE_TIME" => "360000",
			"AUTOCONFIRM" => "Y",
			"DISABLE_CONFIRM_POPUP" => "Y",
			"COMPONENT_TEMPLATE" => "bitronic2",
			"INCLUDE_JQUERY" => "N",
		),
		false
	); ?>
	<? $geoip = ob_get_clean() ?>
<? endif ?>
<? if (CModule::IncludeModule('catalog') && CRZBitronic2Settings::isPro($bGeoipStore = true)): ?>
	<?
	$arRes = $APPLICATION->IncludeComponent(
		"yenisite:geoip.store",
		"bitronic2",
		array(
			"CACHE_TYPE" => "A",
			"CACHE_TIME" => "360000",
			"COLOR_SCHEME" => '',
			"INCLUDE_JQUERY" => "N",
			"NEW_FONTS" => "Y",
			"ONLY_GEOIP" => $rz_b2_options["geoip_unite"],
			"DETERMINE_CURRENCY" => $rz_b2_options["geoip_currency"],
		),
		false
	);
	$rz_b2_options['GEOIP'] = $arRes;
	?>
<? endif; ?>
<? if ($geoip) echo $geoip ?>