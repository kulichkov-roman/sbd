<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$firstReviewDate = new DateTime('2015-8-29');
$currentDate = new DateTime();

$diff = $currentDate->diff($firstReviewDate);

$arResult['REVIEW_WORKS_PERIOD'] = $diff->y;


if(\Bitrix\Main\Loader::includeModule('sib.core')){
    $arResult['REVIEWS_COUNT'] = \Sib\Core\Helper::getRealReviewYm();
} else {
    $arResult['REVIEWS_COUNT'] = CIBlock::GetElementCount(54);
    $arResult['REVIEWS_COUNT'] = ceil($arResult['REVIEWS_COUNT'] + ($arResult['REVIEWS_COUNT'] * 0.81));
}

