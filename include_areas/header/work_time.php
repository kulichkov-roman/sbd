<br>
<?
if(\Bitrix\Main\Loader::includeModule('yenisite.worktime'))
{

    switch($_SESSION['VREGIONS_REGION']['ID']) {

        // москва
        case 14646:
            $worktime = array(
                "LUNCH" => "Время работы в будние дни",
                "MONDAY" => "Y",
                "TUESDAY" => "N",
                "WEDNESDAY" => "Y",
                "THURSDAY" => "N",
                "FRIDAY" => "Y",
                "SATURDAY" => "N",
                "SUNDAY" => "Y",
                "TIME_WORK_FROM" => "01:00",
                "TIME_WORK_TO" => "24:00",
                "TIME_WEEKEND_FROM" => "09:00",
                "TIME_WEEKEND_TO" => "16:00",
                "LUNCH_WEEKEND" => "Время работы в выходные",
                "TIME_WORK" => "",
                "TIME_WEEKEND" => "",
            );
            break;

        // новосибирск
        case 14647:
            $worktime = array(
                "LUNCH" => "Время работы в будние дни",
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
            );
            break;

        // по умолчанию
        default:
            $worktime = array(
                "LUNCH" => "Время работы в будние дни",
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
            );
    }

    $work_component = array(
        "COMPOSITE_FRAME_MODE" => "A",
        "COMPOSITE_FRAME_TYPE" => "AUTO",
        "COMPONENT_TEMPLATE" => "bitronic2",
        "CACHE_TYPE" => "Y",
        "CACHE_TIME" => "360000",
    );


    $APPLICATION->IncludeComponent(
    "yenisite:bitronic.worktime", 
    "bitronic2", 
    array_merge($worktime, $work_component),
    false
);
}