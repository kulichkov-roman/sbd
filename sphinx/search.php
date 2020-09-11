<?php

ini_set('display_errors', true);
error_reporting(E_ALL);

include_once("lib/SphinxSearch.php");

if(isset($_REQUEST['search']['value'])) {
    $query = $_REQUEST['search']['value'];
} elseif(empty($_REQUEST['query'])) {
    $query = '';
} else {
    $query = $_REQUEST['query'];
}
/*$sphinx = new SphinxSearch(
    'mysql:host=127.0.0.1;port=9306',
    'mysql:host=mysql;port=3306;dbname=sibdroid',
    'xbmc',
    'xbmc'
);*/

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

if(isset($_REQUEST['json'])) {
//    array_walk($results, function(&$item) {
//        $item = json_encode($item);
//    });

    echo json_encode(
        array(
            "recordsTotal" => count($results),
            "recordsFiltered" => count($results),
            "sphinx" => $sphinx->sphinxQuery,
            "data"  => array_values($results)
        )
    );

    //"{ \"data\": [".implode(",", $results)."]}";

} else {
    echo '<pre>'.print_r($results, true).'</pre>';
}