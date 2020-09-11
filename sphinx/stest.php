<?
ini_set('display_errors', true);
error_reporting(E_ALL);

include_once("lib/SphinxSearch.php");
$query="наушнеки";

$sphinx = new SphinxSearch(
    'mysql:host=127.0.0.1;port=9306',
    'mysql:host=127.0.0.1;port=3306;dbname=sibdroid',
    'root',
    'ayJ721Z7q3s74NT'
);
if(!empty($query)) {
    $sphinx->connect();
    $results = $sphinx->search($query, true);
} else {
    $results = array();
}
?>