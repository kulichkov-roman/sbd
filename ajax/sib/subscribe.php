<?php
include_once "include_stop_statistic.php";

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

$response = array('STATUS' => 'FAIL');

if (!empty($_REQUEST['sf_EMAIL']) && !empty($_REQUEST['sf_RUB_ID']))
{
    CModule::IncludeModule('subscribe');

    $arFields = Array(
        "USER_ID" => $USER->IsAuthorized() ? $USER->GetID() : false,
        "FORMAT" => "html",
        "EMAIL" => $_REQUEST['sf_EMAIL'],
        "ACTIVE" => "Y",
        "RUB_ID" => $_REQUEST['sf_RUB_ID']
    );

    $subscr = new CSubscription;
    $arSubscr = CSubscription::GetUserSubscription();

    if (!empty($arSubscr['ID']))
    {
        $subscr->Update($arSubscr['ID'], $arFields);
        $response['STATUS'] = 'SUCCESS';
    }
    else
    {
        $ID = $subscr->Add($arFields);

        if ($ID > 0)
        {
            CSubscription::Authorize($ID);
            $response['STATUS'] = 'SUCCESS';
        }
    }
}

echo json_encode($response);