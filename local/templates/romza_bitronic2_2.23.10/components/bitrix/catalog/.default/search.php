<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Page\Asset;
use Sib\Core\Helper;

\Bitrix\Main\Loader::includeModule('sib.core');

$APPLICATION->SetTitle(GetMessage("BITRONIC2_SEARCH_RESULTS"));

$APPLICATION->AddChainItem('Поиск', '#');
$_GET['q'] = htmlspecialchars($_GET['q']);
$APPLICATION->AddChainItem($_GET['q'], '/catalog/?q='.$_GET['q']);

$this->setFrameMode(true);


global $rz_b2_options;

// include css and js
$asset = Asset::getInstance();
$asset->addJs(SITE_TEMPLATE_PATH . "/js/custom-scripts/libs/flexGreedSort.js");
$asset->addJs(SITE_TEMPLATE_PATH . "/js/3rd-party-libs/jquery.countdown.2.0.2/jquery.plugin.js");
$asset->addJs(SITE_TEMPLATE_PATH . "/js/3rd-party-libs/jquery.countdown.2.0.2/jquery.countdown.min.js");
$asset->addJs(SITE_TEMPLATE_PATH . "/js/custom-scripts/inits/initTimers.js");
$asset->addJs(SITE_TEMPLATE_PATH . "/js/custom-scripts/libs/UmTabs.js");
$asset->addJs(SITE_TEMPLATE_PATH . "/js/custom-scripts/inits/pages/initSearchResultsPage.js");
$asset->addJs(SITE_TEMPLATE_PATH . "/js/custom-scripts/inits/sliders/initPhotoThumbs.js");
$asset->addJs(SITE_TEMPLATE_PATH . "/js/custom-scripts/inits/initCatalogHover.js");
CJSCore::Init(array('rz_b2_um_countdown', 'rz_b2_bx_catalog_item'));
if ('Y' == $rz_b2_options['wow-effect']) {
    $asset->addJs(SITE_TEMPLATE_PATH . "/js/3rd-party-libs/wow.min.js");
}
if ($rz_b2_options['quick-view'] === 'Y') {
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/js/3rd-party-libs/jquery.mobile.just-touch.min.js");
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/js/custom-scripts/inits/initMainGallery.js");
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/js/custom-scripts/inits/toggles/initGenInfoToggle.js");
}
$asset->addJs(SITE_TEMPLATE_PATH . "/js/custom-scripts/inits/pages/initCatalogPage.js");

// Search category for catalog products
$catalogCategory = 'iblock_' . $arParams['IBLOCK_TYPE'];
$catalogIblockId = $arParams['IBLOCK_ID'];

if (!empty($_GET['where'])) {
    $catalogCategory = $_GET['where'] ?: 'iblock_' . $arParams['IBLOCK_TYPE'];
    $arParamsSearchTitle = \Yenisite\Core\Tools::getPararmsOfCMP($_SERVER['DOCUMENT_ROOT'] . SITE_DIR . 'include_areas/sib/header/search.php', true);
    $arValuesWhere = array();
    if (!empty($arParamsSearchTitle)) {
        $countCategory = $arParamsSearchTitle['NUM_CATEGORIES'];
        $i = 0;
        $arWherre = array();
        for ($i = 0; $i <= $countCategory; $i++) {
            if (!empty($arParamsSearchTitle['CATEGORY_' . $i . '_' . $_GET['where']])) {
                $arParams["IBLOCK_ID"] = $arParamsSearchTitle['CATEGORY_' . $i . '_' . $_GET['where']];
            }
            foreach ($arParamsSearchTitle['CATEGORY_' . $i] as $key => $value) {
                $arWherre[] = $value;
                $arValuesWhere[$value] = $arParamsSearchTitle['CATEGORY_' . $i .'_'.$value];
            }
        }
    }
}

if (strpos($_GET['where'], 'iblock') !== false) {
    $arParams["IBLOCK_ID"] = strpos($_GET['where'], 'catalog') === false ? CRZBitronic2CatalogUtils::getIblockbyType(str_replace('iblock_', '', $_GET['where'])) : $catalogIblockId;
    $arParams["IBLOCK_ID"] = array($arParams["IBLOCK_ID"]);
}

if ($_GET['where'] === 'ALL' || $_REQUEST['where'] === 'ALL' || empty($_GET['where']) || empty($_REQUEST['where'])) {
    $_GET['where'] = $_REQUEST['where'] = '';
    $bAll = true;
}

// fill params for bitrix:search.page
$arSearchPageParams = array(
    "RESTART" => "Y",
    "NO_WORD_LOGIC" => "Y",
    "USE_LANGUAGE_GUESS" => "N",
    "CHECK_DATES" => "Y",
    "USE_TITLE_RANK" => "N",
    "DEFAULT_SORT" => "rank",
    "FILTER_NAME" => "offerFilter",
    "arrFILTER" => array(
        0 => $catalogCategory,
        /*1 => "iblock_news"*/
    ),
    "arrFILTER_" . $catalogCategory => $arParams["IBLOCK_ID"],
    /*"arrFILTER_iblock_news" => array(
        0 => "all",
    ),*/
    "SHOW_WHERE" => "Y",
    "arrWHERE" => array(
        0 => $catalogCategory,
        /*1 => "iblock_news",*/
    ),
    "SHOW_WHEN" => "N",
    "PAGE_RESULT_COUNT" => 20,
    "DISPLAY_TOP_PAGER" => "N",
    "DISPLAY_BOTTOM_PAGER" => "Y",
    "PAGER_TITLE" => GetMessage("BITRONIC2_SEARCH_RESULTS"),
    "PAGER_SHOW_ALWAYS" => "N",
    "PAGER_TEMPLATE" => "",

    "SEARCH_WITH_OFFERS" => false,
    "IBLOCK_TYPE" => $arParams['IBLOCK_TYPE'],
);

if (!empty($arWherre)) {
    $arSearchPageParams['arrWHERE'] = array_merge($arSearchPageParams['arrWHERE'], $arWherre);
    if ($bAll){
        $arSearchPageParams['arrFILTER'] = array_merge($arSearchPageParams['arrFILTER'], $arWherre);
        foreach ($arValuesWhere as $key => $arValueWhere){
            $arSearchPageParams['arrFilter_'.$key] = $arValueWhere;
        }
    }
}

global $offerFilter;
$offerFilter = array();

$bOffers = false;
$whereBackup = $_REQUEST['where'];

// check catalog offers
do {
    if (!empty($_REQUEST['where']) && ($_REQUEST['where'] !== $catalogCategory)) break;
    if (!CModule::IncludeModule('catalog')) break;

    $arOfferIBlock = CCatalogSKU::GetInfoByProductIBlock(reset($arParams['IBLOCK_ID']));
    if (!is_array($arOfferIBlock)) break;

    // fill params to search with offers
    $bOffers = true;
    $offerIBlockType = CIBlock::GetArrayByID($arOfferIBlock['IBLOCK_ID'], 'IBLOCK_TYPE_ID');
    $offerCategory = 'iblock_' . $offerIBlockType;

    if (in_array($offerCategory, $arSearchPageParams['arrFILTER'])) {
        $arSearchPageParams['arrFILTER_' . $offerCategory][] = $arOfferIBlock['IBLOCK_ID'];
    } else {
        $arSearchPageParams['arrFILTER_' . $offerCategory] = array(0 => $arOfferIBlock['IBLOCK_ID']);
        $arSearchPageParams['arrFILTER'][] = $offerCategory;
    }

    if (
        $catalogCategory === $_REQUEST['where'] &&
        $catalogCategory !== $offerCategory
    ) {
        // make custom filter for search with offers
        // because catalog iblock and offer iblock has different types
        $arSearchPageParams['SEARCH_WITH_OFFERS'] = true;
        $offerFilter['MODULE_ID'] = 'iblock';
        $offerFilter['PARAM1'] = array($arParams['IBLOCK_TYPE'], $offerIBlockType);

        $_GET['where'] = $_REQUEST['where'] = '';
    }
} while (0);

// perform search, fill elements ID list
/*$arElements = $APPLICATION->IncludeComponent(
    "bitrix:search.page",
    "search",
    $arSearchPageParams,
    $component,
    array('HIDE_ICONS' => 'Y')
);*/

if($_REQUEST['where']=='iblock_catalog'){

    include_once($_SERVER["DOCUMENT_ROOT"]."/sphinx/lib/SphinxSearch.php");
    global $DB;
    $query=$DB->ForSql($_REQUEST['q']);
    $sphinx = new SphinxSearch(
        'mysql:host=127.0.0.1;port=9306',
        'mysql:host=127.0.0.1;port=3306;dbname='.$DB->DBName,
        $DB->DBLogin,
        $DB->DBPassword
    );
    if(!empty($query)) {
        $sphinx->connect();
        $results = $sphinx->search($query, true);
    } else {
        $results = array();
    }
    $arElements=array();
    if(!empty($results)){
        foreach($results as $item){
            $arElements[]=$item['id'];
        }
    }

    $bOffers=false;
}else{
    $arElements = $APPLICATION->IncludeComponent(
        "bitrix:search.page",
        "search",
        $arSearchPageParams,
        $component,
        array('HIDE_ICONS' => 'Y')
    );
}

if($_SESSION['is_dev'])
    $arElements = array ( 3270 => 'S909', 2511 => '11551', 3081 => '16799', 3082 => '16800', 2310 => '3178', 3004 => '16685', 3005 => '16686', 3006 => '16687', 3007 => '16688', 3008 => '16689', 8311 => '17523', 8312 => '17524', 8313 => '17525', 8314 => '17526', 8286 => '17498', 8287 => '17499', 8288 => '17500', 8289 => '17501', 8290 => '17502', 3133 => '17093', );

// return request param to original value
$_GET['where'] = $_REQUEST['where'] = $whereBackup;

if (empty($_GET['where'])) {
    $_GET['where'] = $_REQUEST['where'] = 'ALL';
}

if ($_REQUEST['where'] !== 'iblock_' . $arParams['IBLOCK_TYPE']) return;

// search made in catalog, output result through catalog.section
if (
    !empty($arElements) &&
    is_array($arElements)
):
    global $searchFilter;

    /**
     * @var array $arSectionParams ;
     */
    include 'include/service_var.php';
    include 'include/prepare_params_section.php';

    $arSearchParams = array(
        "FILTER_NAME" => "searchFilter",
        "SECTION_ID" => "",
        "SECTION_CODE" => "",
        "SECTION_USER_FIELDS" => array(),
        "INCLUDE_SUBSECTIONS" => "Y",
        "SHOW_ALL_WO_SECTION" => "Y",
        "META_KEYWORDS" => "",
        "META_DESCRIPTION" => "",
        "BROWSER_TITLE" => "",
        "ADD_SECTIONS_CHAIN" => "N",
        "SET_TITLE" => "N",
        "SET_STATUS_404" => "N",
        "CACHE_FILTER" => "N",
        "CACHE_GROUPS" => "N",
		"PAGE_ELEMENT_COUNT" => 12,
        "DISPLAY_TOP_PAGER" => "Y",
        "DISPLAY_BOTTOM_PAGER" => "Y",
        /* "ELEMENT_SORT_FIELD" => $arSectionParams['ELEMENT_SORT_FIELD'],
        "ELEMENT_SORT_ORDER" => $arSectionParams['ELEMENT_SORT_ORDER'],
        "ELEMENT_SORT_FIELD2" => $arSectionParams['ELEMENT_SORT_FIELD2'],
        "ELEMENT_SORT_ORDER2" => $arSectionParams['ELEMENT_SORT_ORDER2'], */
        'SEARCH_PAGE' => 'Y'
    );
    //global $USER; if($USER->IsAdmin()){echo '<pre>'; print_r($arSectionParams); echo '</pre>';};
    $arSearchParams = array_merge($arSectionParams, $arSearchParams);
    $searchFilter = array(
        "=ID" => $arElements,
    );
    if((int)$_GET['section'] > 0){
        $arSearchParams['SECTION_ID'] = (int)$_GET['section'];
    }
    if(\Bitrix\Main\Loader::includeModule('sib.core')){
        \Sib\Core\Regions::updateRegionStores();
        $arSearchParams['STORES'] = $_SESSION["VREGIONS_REGION"]["ID_SKLADA"];
        $arSearchParams['PRICE_CODE'] = $_SESSION["VREGIONS_REGION"]["PRICE_CODE"];
        $arTreeFirst = \Sib\Core\Helper::getSectionTreeForDepthFirst();
    }
    $arSearchParams['IBLOCK_ID'] = is_array($arSearchParams['IBLOCK_ID']) ? $arSearchParams['IBLOCK_ID'][0] : $arSearchParams['IBLOCK_ID'];
    $arSearchParams['SEARCH_PAGE_ONLY_CATALOG'] = $bOffers && !empty($arOffers) ? '' : 'Y';

    //global $USER; if($USER->IsAdmin()){echo '<pre>'; print_r($arSearchParams); echo '</pre>';};
    
?>
<style>
.category{margin-top:0;}
@media(max-width:1023px){
    .main-block_search-found a.catalog-title__link{
        max-height: none !important;
    }
    h3.main-title{
        width: 100%;
        padding-left: 10px;
    }
}
</style>

<h3 class="main-title">По запросу «<?=$_GET['q']?>» найдено <?=count($arElements)?> <?=Helper::getPlural((int)count($arElements), 'товар', 'товара', 'товаров');?></h3>

<section class="main-block main-block_search main-block_catalog2-2 main-block_search-found">
    
    <?//include 'include/section_list.php';?>
    <div class="search-box">
        <div class="category">
            <h4>Поиск по категориям:</h4>
            <?
                $sections = [];
                $rsEls = \CIblockElement::GetList([], ['IBLOCK_ID' => $arSearchParams['IBLOCK_ID'], 'ID' => $arElements], false, false, ['ID', 'IBLOCK_SECTION_ID']);
                while($obEl = $rsEls->fetch()){
                    $sectionFirstId = isset($arTreeFirst[$obEl['IBLOCK_SECTION_ID']]) ? $arTreeFirst[$obEl['IBLOCK_SECTION_ID']] : $obEl['IBLOCK_SECTION_ID'];
                    $sections[$sectionFirstId][] = $obEl['ID'];
                }
                //global $USER; if($USER->IsAdmin()){echo '<pre>'; print_r($sections); echo '</pre>';};
                $rsSec = \CIblockSection::GetList([], ['IBLOCK_ID' => $arSearchParams['IBLOCK_ID'], 'ID' => array_keys($sections)], false, ['ID', 'NAME', 'DETAIL_PAGE_URL']);
                $arSecSearch = [];
                while($obSec = $rsSec->GetNext()){
                    $arSecSearch[] = $obSec;
                }
            ?>
            <ul class="category-list">
                <?foreach($arSecSearch as $obSec):?>
                    <li class="category-list__item">
                        <a href="#" class="category-list__link" data-section-id="<?=$obSec['ID']?>"><?=$obSec['NAME']?> <span><?=count($sections[$obSec['ID']])?></span></a>
                    </li>
                <?endforeach?>
                <hr>
                <li class="category-list__item">
                    <a href="#" class="category-list__link" data-section-id="0">Все товары <span><?=count($arElements)?></span></a>
                </li>
            </ul>
        </div> 
        <div class="result">
            <div class="tab-wrap">
                <div class="tab-top">
                    <?
                        include 'include/sort_search.php';
                        //include 'include/view.php';
                    ?>
                </div>
                <hr class="m-sort">
                <div class="box-tab-cont">
                    <div id="catalog_section">
                         <?
                            $APPLICATION->IncludeComponent(
                                "bitrix:catalog.section",
                                $view,
                                $arSearchParams,
                                $component,
                                array('HIDE_ICONS' => 'Y')
                            );
                        ?>
                    <div class="box-paging">
                        <?include 'include/pagination.php';?>
                    </div>
                    </div>
                   
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    if (/iPhone/i.test(navigator.userAgent)) {$('html').addClass('iphone');}
    
    RZB2.ajax.params['q'] = '<?=$_GET['q']?>';
    RZB2.ajax.params['where'] = '<?=$_GET['where']?>';
    RZB2.ajax.params['section'] = '';
    $('.category-list .category-list__item').on('click', 'a', function(e){
        
        e.stopPropagation();
        e.preventDefault();

        $(this).parent().siblings().removeClass('active');
        var $_ = $(this);
        var parent = $_.parent();

        if (parent.hasClass('active')){
            return;
        } else {
            parent.addClass('active');
            RZB2.ajax.CatalogSection.Start(this, {'PAGEN_2': 1, 'section': $_.data('section-id')});
        }
    });
    $('.search-sections').on('change', function(e){
        RZB2.ajax.CatalogSection.Start(this, {'PAGEN_2': 1, 'section': $(this).val()});
        $('.search-sections option').each(function(){
            if($(this).is(':checked')){
                $(this).text($(this).data('name'));
                $('.active-count').text($(this).data('count'));
            } else {
                $(this).text($(this).data('name') + ' (' + $(this).data('count') + ')');
            }
        });
    });
    $(document).find('.js-ellip-2').css({
        'max-height':'none',
        'min-height':'auto',
        'overflow':'auto'
    });
</script>
<?else:?>
<section class="main-block main-block_search-none">
    <h3 class="main-title">Поиск – <?=$_GET['q']?></h3>
    <p>Товары, соответствующие критериям поиска, не найдены</p>
    <a href="/catalog/" class="button">Перейти в каталог</a>
</section>
<?endif?>

