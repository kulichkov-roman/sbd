<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);

define('MIN_PAGE', 8);
define('FIRST_PAGE', 1);

if (!$arResult["NavShowAlways"])
{
    if ($arResult["NavRecordCount"] == 0 || ($arResult["NavPageCount"] == 1 && $arResult["NavShowAll"] == false))
        return;
}
if ($arResult["sUrlPath"] === "/ajax/sib/personal_orders.php")
    $arResult["sUrlPath"] = '/personal/orders/';

if ( isset($_REQUEST['ORDERS_STATUS']) || isset($_REQUEST['sort_status']))
{
    $sortStatus = $_REQUEST['ORDERS_STATUS'] ? : $_REQUEST['sort_status'];
    $sortStatus = $sortStatus === 'undefined' ? 'all' : $sortStatus;
    $strNavQueryString = 'ORDERS_STATUS=' . $sortStatus . '&';
}

$elementName = !empty($arResult["NavTitle"]) ? $arResult["NavTitle"] : GetMessage('BITRONIC2_PAGEN_GOODS');
?>
<ul class="paging-list pagination">
    <? if ($arResult["NavPageCount"] < MIN_PAGE): ?>
        <? $curPage = FIRST_PAGE; ?>
        <? while ($curPage <= $arResult['NavPageCount']): ?>
            <li class="paging-list__item <?if ($curPage == $arResult["NavPageNomer"]):?>active<?endif?>">
                <a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($curPage)?>"
                   class="paging-list__link"
                   data-pagen-key="PAGEN_<?=$arResult['NavNum']?>"
                   data-page="<?=$curPage?>"
                >
                    <?=$curPage?>
                </a>
            </li>
            <? $curPage++ ?>
        <? endwhile ?>
    <? else: ?>
        <?
        $bFirstPage = $arResult["NavPageNomer"] == 1;
        $bLastPage = $arResult["NavPageNomer"] == $arResult["NavPageCount"];
        ?>

        <? if ($arResult["NavPageNomer"] > 2): ?>
            <li class="paging-list__item">
                <a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=FIRST_PAGE?>"
                   class="paging-list__link"
                   data-pagen-key="PAGEN_<?=$arResult['NavNum']?>"
                   data-page="<?=FIRST_PAGE?>"
                ><?=FIRST_PAGE?></a>
            </li>
            <li class="paging-list__item paging-prev">
                <a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult['NavPageNomer'] - 1?>"
                   class="paging-list__link"
                   data-pagen-key="PAGEN_<?=$arResult['NavNum']?>"
                   data-page="<?=$arResult['NavPageNomer'] - 1?>"
                >
                    <span class="arrow-paging"></span>
                </a>
            </li>
        <? endif ?>

        <? if ($bFirstPage): ?>
            <li class="paging-list__item active">
                <a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult['NavPageNomer']?>"
                   class="paging-list__link"
                   data-pagen-key="PAGEN_<?=$arResult['NavNum']?>"
                   data-page="<?=$arResult['NavPageNomer']?>"
                >
                    <?=$arResult['NavPageNomer']?>
                </a>
            </li>
            <li class="paging-list__item">
                <a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult['NavPageNomer'] + 1?>"
                   class="paging-list__link"
                   data-pagen-key="PAGEN_<?=$arResult['NavNum']?>"
                   data-page="<?=$arResult['NavPageNomer'] + 1?>"
                >
                    <?=$arResult['NavPageNomer'] + 1?>
                </a>
            </li>
            <li class="paging-list__item">
                <a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult['NavPageNomer'] + 2?>"
                   class="paging-list__link"
                   data-pagen-key="PAGEN_<?=$arResult['NavNum']?>"
                   data-page="<?=$arResult['NavPageNomer'] + 2?>"
                >
                    <?=$arResult['NavPageNomer'] + 2?>
                </a>
            </li>
        <? elseif ($bLastPage): ?>
            <li class="paging-list__item">
                <a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult['NavPageNomer'] - 2?>"
                   class="paging-list__link"
                   data-pagen-key="PAGEN_<?=$arResult['NavNum']?>"
                   data-page="<?=$arResult['NavPageNomer'] - 2?>"
                >
                    <?=$arResult['NavPageNomer'] - 2?>
                </a>
            </li>
            <li class="paging-list__item">
                <a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult['NavPageNomer'] - 1?>"
                   class="paging-list__link"
                   data-pagen-key="PAGEN_<?=$arResult['NavNum']?>"
                   data-page="<?=$arResult['NavPageNomer'] - 1?>"
                >
                    <?=$arResult['NavPageNomer'] - 1?>
                </a>
            </li>
            <li class="paging-list__item active">
                <a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult['NavPageNomer']?>"
                   class="paging-list__link"
                   data-pagen-key="PAGEN_<?=$arResult['NavNum']?>"
                   data-page="<?=$arResult['NavPageNomer']?>"
                >
                    <?=$arResult['NavPageNomer']?>
                </a>
            </li>
        <? else: ?>
            <li class="paging-list__item">
                <a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult['NavPageNomer'] - 1?>"
                   class="paging-list__link"
                   data-pagen-key="PAGEN_<?=$arResult['NavNum']?>"
                   data-page="<?=$arResult['NavPageNomer'] - 1?>"
                >
                    <?=$arResult['NavPageNomer'] - 1?>
                </a>
            </li>
            <li class="paging-list__item active">
                <a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult['NavPageNomer']?>"
                   class="paging-list__link"
                   data-pagen-key="PAGEN_<?=$arResult['NavNum']?>"
                   data-page="<?=$arResult['NavPageNomer']?>"
                >
                    <?=$arResult['NavPageNomer']?>
                </a>
            </li>
            <li class="paging-list__item">
                <a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult['NavPageNomer'] + 1?>"
                   class="paging-list__link"
                   data-pagen-key="PAGEN_<?=$arResult['NavNum']?>"
                   data-page="<?=$arResult['NavPageNomer'] + 1?>"
                >
                    <?=$arResult['NavPageNomer'] + 1?>
                </a>
            </li>
        <? endif ?>

        <? if ($arResult["NavPageNomer"] < $arResult["NavPageCount"] - 1): ?>
            <li class="paging-list__item paging-next">
                <a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult['NavPageNomer'] + 1?>"
                   class="paging-list__link"
                   data-pagen-key="PAGEN_<?=$arResult['NavNum']?>"
                   data-page="<?=$arResult['NavPageNomer'] + 1?>"
                >
                    <span class="arrow-paging"></span>
                </a>
            </li>
            <li class="paging-list__item">
                <a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult['NavPageCount']?>"
                   class="paging-list__link"
                   data-pagen-key="PAGEN_<?=$arResult['NavNum']?>"
                   data-page="<?=$arResult['NavPageCount']?>"
                >
                    <?=$arResult['NavPageCount']?>
                </a>
            </li>
        <? endif ?>
    <? endif ?>
</ul>