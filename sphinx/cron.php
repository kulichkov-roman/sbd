<?php

ini_set('display_errors', true);
error_reporting(E_ALL);

include_once("lib/SphinxSearch.php");

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
$sphinx->connect();

$sphinx->reindex();
$sphinx->generateKeywords();
$sphinx->reindex('keywords');
