<?
use Bitrix\Main\Loader;

Loader::includeModule('yenisite.core');

$moduleId = "yenisite.bitronic2";	/// !!!!!!!!!!!!!!
$moduleCode = 'bitronic2'; // !!!!!!!!
$settingsClass = 'CRZBitronic2Settings';

if (!Loader::includeModule($moduleId)) die("Module {$moduleId} not installed!");
