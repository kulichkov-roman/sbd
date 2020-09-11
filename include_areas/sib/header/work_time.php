<br>
<?
if(\Bitrix\Main\Loader::includeModule('yenisite.worktime'))
{
	$APPLICATION->IncludeComponent(
	"yenisite:bitronic.worktime", 
	"bitronic2", 
	array(
		"COMPONENT_TEMPLATE" => "bitronic2",
		"LUNCH" => "Время работы в будние дни",
		"CACHE_TYPE" => "Y",
		"CACHE_TIME" => "360000",
		"MONDAY" => "Y",
		"TUESDAY" => "Y",
		"WEDNESDAY" => "Y",
		"THURSDAY" => "Y",
		"FRIDAY" => "Y",
		"SATURDAY" => "Y",
		"SUNDAY" => "Y",
		"TIME_WORK_FROM" => "09:00",
		"TIME_WORK_TO" => "21:00",
		"TIME_WEEKEND_FROM" => "09:00",
		"TIME_WEEKEND_TO" => "21:00",
		"LUNCH_WEEKEND" => "Время работы в выходные",
		"TIME_WORK" => "",
		"TIME_WEEKEND" => "",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO"
	),
	false
);
}