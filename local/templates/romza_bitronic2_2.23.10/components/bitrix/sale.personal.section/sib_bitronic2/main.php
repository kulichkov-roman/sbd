<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

global $rz_b2_options;

$APPLICATION->SetPageProperty("showSubscribe", "Y");

$availablePages = array();

if ($arParams['SHOW_ORDER_PAGE'] === 'Y')
{
	$availablePages[] = array(
		"path" => $arResult['PATH_TO_ORDERS'],
		"name" => Loc::getMessage("SPS_ORDER_PAGE_NAME"),
		"icon" => 'orders',
	);
}

if ($arParams['SHOW_PRIVATE_PAGE'] === 'Y')
{
	$availablePages[] = array(
		"path" => $arResult['PATH_TO_PRIVATE'],
		"name" => Loc::getMessage("SPS_PERSONAL_PAGE_NAME"),
		"icon" => 'data'
	);
}

if ($arParams['SHOW_SUBSCRIBE_PAGE'] === 'Y' && Loader::includeModule('yenisite.favorite'))
{
	$availablePages[] = array(
		"path" => $arResult['PATH_TO_SUBSCRIBE'],
		"name" => Loc::getMessage("SPS_SUBSCRIBE_PAGE_NAME"),
		"icon" => 'subscribe'
	);
}

$customPagesList = CUtil::JsObjectToPhp($arParams['~CUSTOM_PAGES']);
if ($customPagesList)
{
	foreach ($customPagesList as $page)
	{
		$availablePages[] = array(
			"path" => $page[0],
			"name" => $page[1],
			"icon" => $page[2]
		);
	}
}
?>
<h2 class="account-page-title"><?$APPLICATION->ShowTitle(false)?></h2>
<? include $_SERVER['DOCUMENT_ROOT'] . SITE_DIR . 'personal/left_menu.php' ?>
<div class="personal-account-main">
    <? if (empty($availablePages)):?>
        <? ShowError(Loc::getMessage("SPS_ERROR_NOT_CHOSEN_ELEMENT")); ?>
    <? else: ?>
        <ul class="main-list">
            <? foreach ($availablePages as $blockElement): ?>
                <li class="main-list__item">
                    <a href="<?= htmlspecialcharsbx($blockElement['path']) ?>" class="main-list__link">
                        <div class="main-list__icon">
                            <i class="icon-account-<?=$blockElement['icon']?>"></i>
                        </div>
                        <p><?= htmlspecialcharsbx($blockElement['name']) ?></p>
                    </a>
                </li>
            <? endforeach ?>
        </ul>
    <? endif ?>
</div>
