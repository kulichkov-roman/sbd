<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
global $rz_b2_options;

$path = $_SERVER['DOCUMENT_ROOT'] . SITE_DIR . 'pricelist/price' . ($rz_b2_options['GEOIP']['ITEM']['ID'] ?: '');
$arExt = array('xls', 'doc', 'pdf');

foreach ($arExt as $ext) {
    $bExists = @file_exists($path.'.'.$ext);
    if (!$bExists) continue;

    $path .= '.' . $ext;
    break;
}

if ($bExists && CModule::IncludeModule('yenisite.core')) {
    $fileSize = \Yenisite\Core\Tools::rusFilesize(fileSize($path));
} else {
    $ext = 'xls';
}

?>
<div class="pricelist-download">
    <a href="/pricelist/" class="link with-icon">
                        <span class="svg-wrap">
							<svg><use xlink:href="#icon-xls"></use></svg>
						</span>
        <span class="text">Прайс-лист</span><?
        if(!empty($rz_b2_options['GEOIP']['ITEM'])):?> (<?=$rz_b2_options['GEOIP']['ITEM']['NAME']?>)<?endif?></a>
    <?if($bExists):?>
        <span class="small">(от <?=date ("d.m.Y", filemtime($path))?> <?=$fileSize?>)</span>
    <?endif?>
</div>
