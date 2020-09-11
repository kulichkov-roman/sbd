<?
include_once("PortalData.php");
include_once("StatusHistory.php");


/**
 * REST
 */

function getAuthLink($portal_addr, $app_id=false) {
    $url = false;
    if ($portal_addr) {
        $url = 'https://' . $portal_addr . '/oauth/authorize/?client_id=' . urlencode($app_id ? $app_id : APP_ID);
    }
    return $url;
}

// Обновление связи с порталом
function refreshToken() {
    $arAuthData = getAuthData();
    if ($arAuthData['REFRESH_TOKEN']) {
        // Получение нового ключа
        $queryUrl = 'https://oauth.bitrix.info/oauth/token/';
        $queryData = http_build_query($queryParams = array(
            'grant_type' => 'refresh_token',
            'client_id' => APP_ID,
            'client_secret' => APP_SECRET_CODE,
            'refresh_token' => $arAuthData['REFRESH_TOKEN'],
            //'scope' => $this->SCOPE,
        ));
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $queryUrl . '?' . $queryData,
        ));
        $result = curl_exec($curl);
        curl_close($curl);
        $arAuthNew = json_decode($result, 1);
        // Сохранение ключа
        if (!$arAuthNew['error']) {
            refreshUserAuth($arAuthNew['access_token'], $arAuthNew['refresh_token']);
        }
    }
}

// Периодическое обновление связи с порталом
function cronRefreshToken() {
    $arAuthData = getAuthData();
    if ($arAuthData) {
        // Проверка связи
        $method = 'profile';
        $params = [];
        $arResp = executeREST('https://' . $arAuthData['PORTAL'] . '/rest/', $method, $params, $arAuthData['ACCESS_TOKEN']);
        if (isset($arResp['error']) && in_array($arResp['error'], array('expired_token', 'invalid_token'))) {
            refreshToken();
        }
    }
}

// Выполнение REST-запроса
function executeMethod($method, $params=[], $only_res=true) {
    $result = false;
    $arAuthData = getAuthData();
    if ($arAuthData) {
        $rest_url = 'https://' . $arAuthData['PORTAL'] . '/rest/';
        $arResp = executeREST($rest_url, $method, $params, $arAuthData['ACCESS_TOKEN']);
        if (isset($arResp['error']) && in_array($arResp['error'], array('expired_token', 'invalid_token'))) {
            refreshToken();
            $arAuthData = getAuthData();
            $arResp = executeREST($rest_url, $method, $params, $arAuthData['ACCESS_TOKEN']);
        }
        if ($only_res) {
            $result = $arResp['result'];
        }
        else {
            $result = $arResp;
        }
    }
    return $result;
}

function executeMethodAuth($method, $params, $domain, $token, $only_res=true) {
    $result = false;
    if ($token) {
        $rest_url = 'https://' . $domain . '/rest/';
        $arResp = executeREST($rest_url, $method, $params, $token);
        if ($only_res) {
            $result = $arResp['result'];
        }
        else {
            $result = $arResp;
        }
    }
    return $result;
}

function executeREST( $rest_url, $method, $params, $access_token ) {
    $url = $rest_url . $method . '.json';
    usleep(100);
    return executeHTTPRequest( $url, array_merge( $params, array( "auth" => $access_token ) ) );
}

function executeHTTPRequest( $queryUrl, array $params = array() ) {
    $result    = array();
    $queryData = http_build_query( $params );

    $curl = curl_init();
    curl_setopt_array( $curl, array(
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_POST           => 1,
        CURLOPT_HEADER         => 0,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL            => $queryUrl,
        CURLOPT_POSTFIELDS     => $queryData,
    ) );

    $curlResult = curl_exec( $curl );
    curl_close( $curl );

    if ( $curlResult != '' ) {
        $result = json_decode( $curlResult, true );
    }

    return $result;
}

function requestCode( $domain ) {
	$url = getAuthLink($domain);
	redirect( $url );
}

function requestAccessToken( $code ) {
	$url = 'https://oauth.bitrix.info/oauth/token/';

    $arParams = array(
        'grant_type' => 'authorization_code',
        'client_id' => urlencode( APP_ID ),
        'client_secret' => urlencode( APP_SECRET_CODE ),
        'code' => urlencode( $code ),
    );

	return executeHTTPRequest( $url, $arParams );
}

function redirect($url) {
    Header( "HTTP 302 Found" );
    Header( "Location: " . $url );
    die();
}


/**
 * Подключение к порталу
 */

// Сохранение данных для доступа
function saveUserAuth($portal, $member_id, $access_token, $refresh_token) {
    $res = true;
	saveCred([$access_token, $refresh_token]);
    return $res;
}

function refreshUserAuth($access_token, $refresh_token) {
    $res = false;
	saveCred([$access_token, $refresh_token]);
    return $res;
}

function getAuthData() {
	$res = false;
	$cred = getCred();
	if ($cred) {
		$res = [
			'ACCESS_TOKEN' => $cred[0],
			'REFRESH_TOKEN' => $cred[1],
			'PORTAL' => PORTAL_ADDRESS,
		];
	}
	return $res;
}

function saveCred($array) {
	$str = serialize($array);
	$str = base64_encode($str . SECRET_SALT);
	$res = file_put_contents($_SERVER['DOCUMENT_ROOT'] . BASE_DIR . CRED_FILE, $str);
}

function getCred() {
	$array = false;
	if (file_exists($_SERVER['DOCUMENT_ROOT'] . BASE_DIR . CRED_FILE)) {
		$str = file_get_contents($_SERVER['DOCUMENT_ROOT'] . BASE_DIR . CRED_FILE);
		$str = base64_decode($str);
		$str = str_replace(SECRET_SALT, '', $str);
		$array = unserialize($str);
	}
	return $array;
}


/**
 * DB
 */

function db_query($query) {
	$arRes = false;

	// Соединяемся, выбираем базу данных
	$db_link = mysqli_connect(MYSQL_HOST, MYSQL_LOGIN, MYSQL_PASSWORD)
	or die('Не удалось соединиться: ' . mysqli_error($db_link));

	mysqli_select_db($db_link, MYSQL_DATABASE)
	or die('Не удалось выбрать базу данных');

	// Выполняем SQL-запрос
	$db_res = mysqli_query($db_link, $query);

	// Получаем результаты
	if ($db_res) {
		$i = 0;
		while ($arRow = mysqli_fetch_array($db_res, MYSQLI_ASSOC)) {
			foreach ($arRow as $k => $val) {
				$arRes[$i][$k] = $val;
			}
			$i++;
		}
		if (!$arRes) {
			$arRes = true;
		}
	}

	if (mysqli_insert_id($db_link)) {
		$arRes = mysqli_insert_id($db_link);
	}

	// Освобождаем память от результата
	mysqli_free_result($db_res);

	// Закрываем соединение
	mysqli_close($db_link);

	return $arRes;
}


/**
 * Работа с временем
 */

function timeTextToTs($text, $full=false) {
	$ts = 0;
	if ($text) {
		if ($full) {
			$ts = strtotime($text);
		}
		else {
			$text = date('d.m.Y') . ' ' . $text;
			$ts = strtotime($text);
//                if ($ts > time()) {
//                    $ts - 3600 * 24;
//                }
		}
	}
	return $ts;
}

function timeTsToText($ts) {
	$text = date('H:i:s', $ts);
	return $text;
}

// Конвертация текущего времени во время на портале
function convTsLocalToServer($ts, $portal_offset=false) {
	if (!$portal_offset) {
		$arUserData = executeMethod('profile');
		$portal_offset = $arUserData['TIME_ZONE_OFFSET'];
	}
	$ts -= $portal_offset;
	$ts += SERVER_TIME_OFFSET;
	return $ts;
}

// Конвертация времени на сервере (Мск) в текущее время
function convTsServerToLocal($ts, $portal_offset=false) {
	if (!$portal_offset) {
		$arUserData = executeMethod('profile');
		$portal_offset = $arUserData['TIME_ZONE_OFFSET'];
	}
	$ts -= SERVER_TIME_OFFSET;
	$ts += $portal_offset;
	return $ts;
}

// Конвертация времени на сервере во время на портале
function convTsServerToPortal($ts) {
	$ts -= SERVER_TIME_OFFSET;
	return $ts;
}

// Конвертация времени на портале (UTC) во время на сервере (Мск)
function convTsPortalToServer($ts) {
	$ts += SERVER_TIME_OFFSET;
	return $ts;
}

// Конвертация текущего времени во время на портале (UTC)
function convTsLocalToPortal($ts, $portal_offset=false) {
	if (!$portal_offset) {
		$arUserData = executeMethod('profile');
		$portal_offset = $arUserData['TIME_ZONE_OFFSET'];
	}
	$ts -= $portal_offset;
	return $ts;
}

// Конвертация времени на портале (UTC) в текущее время
function convTsPortalToLocal($ts, $portal_offset=false) {
	if (!$portal_offset) {
		$arUserData = executeMethod('profile');
		$portal_offset = $arUserData['TIME_ZONE_OFFSET'];
	}
	$ts += $portal_offset;
	return $ts;
}


/**
 * Utilites
 */

function logSave($string) {
    $res = file_put_contents($_SERVER['DOCUMENT_ROOT'].'/log.txt', $string, FILE_APPEND);
    file_put_contents($_SERVER['DOCUMENT_ROOT'].'/log.txt', "\n---\n".date('d.m.Y H:i:s')."\n\n", FILE_APPEND);
}

function checkSpchars($string) {
    return preg_match('/[<>%\*]/i', $string);
}

function convOffsetToTimezone($offset) {
    $tz_str = $offset / 3600;
    $tz_str = "GMT" . ($tz_str < 0 ? $tz_str : "+".$tz_str);
    return $tz_str;
}

function xml2array($block) {
	if (is_object($block) && strpos(get_class($block), 'SimpleXML') !== false) {
		$block = (array)$block;
	}

	if (!is_array($block)) {
		return (string)$block;
	}

	foreach ($block as $key => $value) {
		$block[$key] = xml2array($value);
	}

	return $block;
}

function array2xml($data, &$xml_data, $parent_key='') {
	foreach($data as $key => $value) {
		if (is_numeric($key)){
			$key = $parent_key;
		}
		if (is_array($value)) {
			// Если дочерний массив состоит из числовых элементов
			if (is_numeric(key($value))) {
				array2xml($value, $xml_data, $key);
			}
			else {
				$subnode = $xml_data->addChild($key);
				array2xml($value, $subnode, $key);
			}
		}
		else {
			$xml_data->addChild("$key", htmlspecialchars("$value"));
		}
	}
}
