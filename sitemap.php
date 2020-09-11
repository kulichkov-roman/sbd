<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
use Bitrix\Seo\SitemapTable;
$sitemapList = SitemapTable::getList(array(
	'order' => array('ID' =>'ASC')
));

$arHost = explode( ":", $_SERVER["HTTP_HOST"]);
$_SERVER["HTTP_HOST"] = $arHost[0];
$hostname = $_SERVER['HTTP_HOST'];

function echoTextFile($file) {
	if (! file_exists($file)) return false;
	if (! is_readable($file)) return false;

	$timestamp = filemtime($file);
	$tsstring = gmdate('D, d M Y H:i:s ', $timestamp) . 'GMT';
	$etag = md5($file . $timestamp);

	header('Content-Type: application/xml');
	header('Content-Length: '.filesize($file));
	header("Last-Modified: $tsstring");
    header("ETag: \"{$etag}\"");

	readfile($file);

	return true;
}

$sitemapHost = dirname(__FILE__) . "/sitemap.xml";
while($ob = $sitemapList->fetch()){
    $settings = unserialize($ob['SETTINGS']);
    if($settings['DOMAIN'] == $hostname){
        $sitemapHost = dirname(__FILE__) . "/" . $settings['FILENAME_INDEX'];
    }
}

if(!echoTextFile($sitemapHost)){
	header('HTTP/1.0 404 Not Found');
}