<?php
$arUrlRewrite=array (
  0 => 
  array (
    'CONDITION' => '#^/online/([\\.\\-0-9a-zA-Z]+)(/?)([^/]*)#',
    'RULE' => 'alias=$1',
    'ID' => '',
    'PATH' => '/desktop_app/router.php',
    'SORT' => '100',
  ),
  1 => 
  array (
    'CONDITION' => '#^/acrit.exportproplus/(.*)#',
    'RULE' => 'path=$1',
    'ID' => '',
    'PATH' => '/acrit.exportproplus/index.php',
    'SORT' => '100',
  ),
  2 => 
  array (
    'CONDITION' => '#^/bitrix/services/ymarket/#',
    'RULE' => '',
    'ID' => '',
    'PATH' => '/bitrix/services/ymarket/index.php',
    'SORT' => '100',
  ),
  3 => 
  array (
    'CONDITION' => '#^/acrit.exportpro/(.*)#',
    'RULE' => 'path=$1',
    'ID' => '',
    'PATH' => '/acrit.exportpro/index.php',
    'SORT' => '100',
  ),
  4 => 
  array (
    'CONDITION' => '#^/online/(/?)([^/]*)#',
    'RULE' => '',
    'ID' => '',
    'PATH' => '/desktop_app/router.php',
    'SORT' => '100',
  ),
  5 => 
  array (
    'CONDITION' => '#^/stssync/calendar/#',
    'RULE' => '',
    'ID' => 'bitrix:stssync.server',
    'PATH' => '/bitrix/services/stssync/calendar/index.php',
    'SORT' => '100',
  ),
  6 => 
  array (
    'CONDITION' => '#^/company/services/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/uslugi/index.php',
    'SORT' => '100',
  ),
  7 => 
  array (
    'CONDITION' => '#^/company/services/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/company/services/index.php',
    'SORT' => '100',
  ),
  8 => 
  array (
    'CONDITION' => '#^/personal/#',
    'RULE' => '',
    'ID' => 'bitrix:sale.personal.section',
    'PATH' => '/personal/index.php',
    'SORT' => '100',
  ),
  20 => 
  array (
    'CONDITION' => '#^/services/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/services/index.php',
    'SORT' => 100,
  ),
  10 => 
  array (
    'CONDITION' => '#^/reviews/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/reviews/index.php',
    'SORT' => '100',
  ),
  22 => 
  array (
    'CONDITION' => '#^/actions/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/actions/index.php',
    'SORT' => 100,
  ),
  25 => 
  array (
    'CONDITION' => '#^/catalog/#',
    'RULE' => '',
    'ID' => 'bitrix:catalog',
    'PATH' => '/catalog/index.php',
    'SORT' => 100,
  ),
  13 => 
  array (
    'CONDITION' => '#^/brands/#',
    'RULE' => '',
    'ID' => 'yenisite:highloadblock',
    'PATH' => '/brands/index.php',
    'SORT' => '100',
  ),
  14 => 
  array (
    'CONDITION' => '#^/rest/#',
    'RULE' => '',
    'ID' => '',
    'PATH' => '/bitrix/services/rest/index.php',
    'SORT' => '100',
  ),
  23 => 
  array (
    'CONDITION' => '#^/blog/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/blog/index.php',
    'SORT' => 100,
  ),
  24 => 
  array (
    'CONDITION' => '#^/news/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/news/index.php',
    'SORT' => 100,
  ),
);
