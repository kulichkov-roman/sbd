<?
$_SERVER["DOCUMENT_ROOT"] = "/home/bitrix/www";
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if (file_exists($_SERVER["DOCUMENT_ROOT"]."/local/lib/telegrambot.php"))
	require_once($_SERVER['DOCUMENT_ROOT'].'/local/lib/telegrambot.php') ;

$message = 'Тест';
$token = "756896270:AAGPumkCmbd9MVNJFY5OJfg32DVriaHooOg";
$chat_id = "-1001269487222";

$cTgBot = new TelegramBot();
$cTgBot->setToken($token);
$cTgBot->setChatID($chat_id);
$res = $cTgBot->send($message);
echo '<pre>'; print_r($res); echo '</pre>';
