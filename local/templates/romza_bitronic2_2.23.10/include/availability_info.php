<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/**
 * @var bool $availableFrame - do we need to create dynamic frame
 * @var bool $availableOnRequest - is our product available on request only
 * @var bool $bShowEveryStatus - do we need to put every status into html (for SKU switch)
 * @var bool $bShowStore - do we need to show stores popup
 * @var float $availableQuantity - CATALOG_QUANTITY
 * @var int $availableItemID - iblock element id
 * @var string $availableClass - add text inside class attribute
 * @var string $availableForOrderText - text to show instead of 'include_areas/sib/catalog/for_order_text.php'
 * @var string $availableID - add attribute "id" with variable's content
 * @var string $availableMeasure - CATALOG_MEASURE_NAME
 * @var string $availableStoresPostfix - postfix to add to stores container id attribute
 * @var string $availableSubscribe - Y if can subscribe
 **/

/* ВСТАВИТЬ ПОСЛЕ ОБНОВЛЕНИЯ*/
/* Выключаем склады */
$bShowStore = false;
/* /ВСТАВИТЬ ПОСЛЕ ОБНОВЛЕНИЯ*/
global $rz_b2_options;
global $rz_b2_storeCount;

//
if ($headerLangIncluded !== true) {
    \Bitrix\Main\Localization\Loc::loadMessages(SITE_TEMPLATE_PATH . '/header.php');
    $headerLangIncluded = true;
}

if ($bShowStore) {
    if (!isset($rz_b2_storeCount)) {
        CModule::IncludeModule('catalog');

        $filter = array(
            "ACTIVE" => "Y",
            "+SITE_ID" => SITE_ID,
            "ISSUING_CENTER" => 'Y'
        );

        $rz_b2_storeCount = CCatalogStore::GetList(
            array('TITLE' => 'ASC', 'ID' => 'ASC'),
            $filter,
            array() // to fetch only count of stores
        );
    }
    $bShowStore = ($rz_b2_storeCount > 0);
}
//$bExpandedStore = $rz_b2_options['product-availability'] == 'expanded';

$availableFrameID = 'bxdinamic_availability_' . $this->randString();

if ($availableFrame === true) {
    $frame = $this->createFrame()->begin();
}
?>
<span id="<?=$availableFrameID?>">
    <? if (!empty($bShowEveryStatus) || $availableClass == 'in-stock' || empty($availableClass)): ?>
        <p class="catalog-status"><?=GetMessage('BITRONIC2_PRODUCT_AVAILABLE_TRUE')?></p>
    <? endif ?>
    <? if (!empty($bShowEveryStatus) || $availableClass == 'available-for-order' || $availableClass == 'available-on-request'): ?>
        <p class="catalog-status catalog-status_order">Под заказ</p>
    <? endif ?>
    <? if (!empty($bShowEveryStatus) || $availableClass == 'out-of-stock'): ?>
        <?
        if(\Bitrix\Main\Loader::includeModule('sib.core')) {
            
            $datePostup = '';
            $cml2traits = false;
            if (isset($arResult['PROPERTIES']['CML2_TRAITS']['VALUE'])) {
                $cml2traits = $arResult['PROPERTIES']['CML2_TRAITS'];
            } else if (isset($arItem['PROPERTIES']['CML2_TRAITS']['VALUE'])) {
                $cml2traits = $arItem['PROPERTIES']['CML2_TRAITS'];
            } else if (!isset($arItem['PROPERTIES']['CML2_TRAITS']['VALUE']) && isset($arItem['PRODUCT_ID'])) {
                $cml2traits = $arItem["PRODUCT_ID"];
            }
            
            if($cml2traits){
                $datePostup = \Sib\Core\Catalog::checkRemainStatus($cml2traits);
            }
            
        }    
        ?>
        <? if (strlen($datePostup) > 0 ): ?>
            <p class="catalog-status catalog-status_wait">Поступление: <?= date('d.m.Y', strtotime($datePostup)) ?></p>
        <? else: ?>
            <p class="catalog-status catalog-status_not"><?=GetMessage('BITRONIC2_PRODUCT_AVAILABLE_FALSE')?></p>
        <? endif ?>
        
    <? endif ?>
    <? if ($availableFrame === true) $frame->end() ?>
</span>