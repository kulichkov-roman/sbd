<?
/**
 * Страница авторизации и интерфейса в Б24
 */

require("includes/connection.php");
require("includes/functions.php");

// Страница в Б24
if (isset($_REQUEST['PLACEMENT']) && $_REQUEST['PLACEMENT'] == 'DEFAULT') {

}
// Авторизация
else {
    $step = 0;
    if (isset($_REQUEST['code'])) {
        $step = 2;
    }
    else {
	    $step = 1;
    }
    switch ($step) {
        case 1:
	        echo '<a href="'.getAuthLink(PORTAL_ADDRESS) . '">' . getAuthLink(PORTAL_ADDRESS) . '</a>';
            //requestCode(SERVER_ADDRESS);
            break;
        case 2:
            $arAccessParams = requestAccessToken($_REQUEST['code']);
            if ($arAccessParams['member_id'] && $arAccessParams['access_token'] && $arAccessParams['refresh_token']) {
                // Сохранить ключ
	            $arRes = executeREST('https://'.PORTAL_ADDRESS.'/rest/', 'profile', [], $arAccessParams['access_token']);
	            if (!empty($arRes['result'])) {
		            saveUserAuth($_REQUEST['domain'], $arAccessParams['member_id'], $arAccessParams['access_token'], $arAccessParams['refresh_token']);
	            }
            }
            break;
    }
}
