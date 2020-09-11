<?
use Bitronic2\Mobile;
mobile::Init();
$setMobile = 'N';
?>
<?$APPLICATION->IncludeComponent("yenisite:settings.panel", ".default", array(
		"SOLUTION" => "yenisite.bitronic2",
		"SETTINGS_CLASS" => "CRZBitronic2Settings",
		"GLOBAL_VAR" => "rz_b2_options",
		"EDIT_SETTINGS" => array(),
		"SET_MOBILE" => $setMobile,
	),
	false
);