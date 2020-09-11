<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main\Page\Asset;
use Bitrix\Main\Page\AssetLocation;
use Yenisite\Core\Tools;
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
define("IS_CATALOG_LIST",true);

$APPLICATION->SetPageProperty("showReviews", "Y");
//$APPLICATION->SetPageProperty("catalogSectionPage", "Y");

$this->setFrameMode(true);
if (defined("ERROR_404")) return;
global $rz_b2_options, $rz_current_sectionID;
// $view, $sort, $by, $pagen, $pagen_key, $page_count
include 'include/service_var.php';
global $rz_b2_options, $rz_current_sectionID;
include 'include/get_cur_section.php'; // @var $arCurSection
$rz_current_sectionID = $arCurSection['ID'];

// THIS EXPRESSION NEEDS TO BE CHANGED IF REVIEWS OR RELATED-CATEGORIES SHOULD BE ADDED
$noAside = ($arResult['MENU_CATALOG'] !== 'side' && ($arParams['USE_FILTER'] !== 'Y' || $arResult['FILTER_PLACE'] !== 'side'))
         ? ' no-aside'
         : '';

// advertising
$arBannerAreas = array('section_banner_single', 'section_banner_double', '');

if (!in_array($arParams['SECTION_BANNER_AREA_1'], $arBannerAreas)) $arParams['SECTION_BANNER_AREA_1'] = $arBannerAreas[0];
if (!in_array($arParams['SECTION_BANNER_AREA_2'], $arBannerAreas)) $arParams['SECTION_BANNER_AREA_2'] = $arBannerAreas[1];

$asset = Asset::getInstance();
/* $asset->addJs(SITE_TEMPLATE_PATH."/js/custom-scripts/libs/flexGreedSort.js");
$asset->addJs(SITE_TEMPLATE_PATH."/js/3rd-party-libs/jquery.countdown.2.0.2/jquery.plugin.js");
$asset->addJs(SITE_TEMPLATE_PATH."/js/3rd-party-libs/jquery.countdown.2.0.2/jquery.countdown.min.js");
$asset->addJs(SITE_TEMPLATE_PATH."/js/custom-scripts/inits/initTimers.js");
$asset->addJs(SITE_TEMPLATE_PATH."/js/custom-scripts/libs/UmTabs.js");
$asset->addJs(SITE_TEMPLATE_PATH."/js/custom-scripts/inits/sliders/initPhotoThumbs.js"); */
$asset->addJs(SITE_TEMPLATE_PATH."/js/custom-scripts/inits/toggles/initGenInfoToggle.js");
/* $asset->addJs(SITE_TEMPLATE_PATH."/js/custom-scripts/inits/initCatalogHover.js");
$asset->addJs(SITE_TEMPLATE_PATH."/js/3rd-party-libs/nouislider.min.js"); */
CJSCore::Init(array('rz_b2_um_countdown', 'rz_b2_bx_catalog_item'));
if ('Y' === $rz_b2_options['quick-view']) {
	$asset->addJs(SITE_TEMPLATE_PATH."/js/3rd-party-libs/jquery.mobile.just-touch.min.js");
	$asset->addJs(SITE_TEMPLATE_PATH."/js/custom-scripts/inits/initMainGallery.js");
}
if ('Y' === $rz_b2_options['wow-effect']) {
	$asset->addJs(SITE_TEMPLATE_PATH."/js/3rd-party-libs/wow.min.js");
}
$asset->addJs(SITE_TEMPLATE_PATH."/js/custom-scripts/inits/pages/initCatalogPage.js");
$asset->addString('<script>RZB2.ajax.CatalogSection.ID = '. (int)$rz_current_sectionID .';</script>', false, AssetLocation::AFTER_JS);

$h1 = false;
$bSibCore = \Bitrix\Main\Loader::includeModule('sib.core');
if($bSibCore){
    global ${$arParams['FILTER_NAME']};

    //пре- и постфикс для заголовка (не виден в крошках)
    $h1_before = \Sib\Core\Seo::getSectionHeadStringBefore($arCurSection);
    $h1_after = \Sib\Core\Seo::getSectionHeadStringAfter($arCurSection);;
    $h1_city = $_SESSION['VREGIONS_REGION']['WHERE'] ? 'в ' . $_SESSION['VREGIONS_REGION']['WHERE'] : '';
    
    //В секции с уценкой отображаем товары в наличии для конкретного города
    if(\Sib\Core\Helper::isUcenkaSectionChild($arCurSection['ID'])){        
        $regionId = \Sib\Core\Catalog::getDefRegion($_SESSION['VREGIONS_REGION']['ID']);
        ${$arParams['FILTER_NAME']}['PROPERTY_SIB_AVAIL_' . $regionId] = \Sib\Core\Helper::getPropValue('SIB_AVAIL_' . $regionId, 'AVAILABLE');
    }

    //не отображаем секции в которых стоит галочка UF_DIS_CATALOG (скрываем для юзеров оставляем для поисковиков)
    $filterSubSections = [];
    $filterSubSections = \Sib\Core\Helper::getSubSectionFromParents(['IBLOCK_ID' => $arParams['IBLOCK_ID'], 'ACTIVE' => 'Y'], ['UF_DIS_CATALOG' => 1]);
    if(!in_array($rz_current_sectionID, $filterSubSections)){
        ${$arParams['FILTER_NAME']}['!IBLOCK_SECTION_ID'] = $filterSubSections;
    }
}
?>
<section class="main-block main-block_search main-block_catalog2 main-block_catalog2-2">
    <h1 class="main-title"><?=$h1_before;?> <?$APPLICATION->ShowTitle(false)?> <?=$h1_after;?> <?=$h1_city?></h1>
    <?if (!(array_key_exists('rz_all_elements', $_REQUEST) && $_REQUEST['rz_all_elements'] === 'y')):?>
        <?include 'include/section_list.php';?>
    <?endif?>
    <div class="search-box">
        <div class="category">
            <button class="search-form__close button-close rbs-close-filter-btn"></button>
            <?include 'include/filter.php';?>
        </div>
        <div class="result">
            <div class="tab-wrap">
                <div class="tab-top">
                    <?
                        include 'include/sort.php';
                        include 'include/view.php';
                    ?>
                </div>
                <hr class="m-sort">
                <div class="box-tab-cont">
                    <?
                    global ${$arParams["FILTER_NAME"]};
                    if (!empty(${$arParams["FILTER_NAME"]}))
                    {
                        $arSkipFilters = array('FACET_OPTIONS');

                        $arDiff = array_diff(array_keys(${$arParams["FILTER_NAME"]}) , $arSkipFilters);
                        $bFilterSet = 0 < count($arDiff);

                        $arParams["CACHE_FILTER"] = $bFilterSet ? $arParams['CACHE_FILTER'] : 'Y';

                        unset($arSkipFilters, $arDiff);
                        if (array_key_exists('rz_all_elements', $_REQUEST) && $_REQUEST['rz_all_elements'] === 'y') {
                            unset(${$arParams["FILTER_NAME"]}['FACET_OPTIONS']);
                        }
                    }

                    include 'include/prepare_params_section.php';

                    ?>
                   
                    <div id="catalog_section">
                        <?
                            $APPLICATION->IncludeComponent(
                                "bitrix:catalog.section",
                                $view,
                                $arSectionParams,
                                $component
                            );
                        ?>
                        <div class="box-paging">
                            <?include 'include/pagination.php';?>
                        </div>
                        <?
                            if(\Bitrix\Main\Loader::includeModule('aristov.vregions')){
                                $region_text = \Aristov\Vregions\Texts::getSectionText($arCurSection['ID']);
                                if (strlen(trim($region_text)) > 0){
                                    $arCurSection['DESCRIPTION'] = $region_text;
                                }
                            }
                            if($bSibCore && strlen(trim($arCurSection['DESCRIPTION']))){
                                $arCurSection['DESCRIPTION'] = \Sib\Core\Helper::initLazyLoadImgFromText($arCurSection['DESCRIPTION'], 'section_' . $arCurSection['ID'] . '_' . md5($arCurSection['DESCRIPTION']));
                            }

                        $frame = new \Bitrix\Main\Page\FrameHelper("section_description");
                        $frame->begin();
                        ?>
                        <?if(!empty($arCurSection['DESCRIPTION'])):?>
                            <article class="seo rbs-section-description-block">
                                <div class="seo__main-wrap">
                                    <div class="seo__main js-seo">
                                        <div class="seo__content js-seo-content">
                                            <?=$arCurSection['DESCRIPTION'];?>
                                        </div>
                                        <!-- <a class="seo__more js-seo-more-rbs" href="javascript:void(0);"></a> -->
                                    </div>
                                </div>
                            </article>
                        <?endif;
                        $frame->end();
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?if($rz_current_sectionID > 0):?>
    <script data-skip-moving="true">
        window.vkAsyncInitCallbacks.push(function(){
            VK.Retargeting.ProductEvent(PRICE_LIST_ID, "view_category", {
                category_ids: '<?=$rz_current_sectionID?>'
            });
        });
    </script>
    <?
    //global $USER;
    $isFilter = strpos($APPLICATION->GetCurPage(false), '/filter/') !== false;
    $isClearFilter = strpos($APPLICATION->GetCurPage(false), '/filter/clear/') !== false;
    if($bSibCore && (!$isFilter || $isClearFilter)):
        $banners = \Sib\Core\Helper::getSectionBanner($rz_current_sectionID);
        ?>
        <?if(count($banners) > 0):?>
            <script>
                var checkBannerSection = function(){
                    
                    if(
                        window.innerWidth <= 768 || 
                        !!$('.sib-banner-section').length ||
                        (window.location.pathname.indexOf('/filter/') > -1 && window.location.pathname.indexOf('/filter/clear/') === -1)
                    ){
                        return;
                    }

                    var liList = $('ul.catalog__list li');
                    if(liList.length <= 0) return;

                    var banners = <?=CUtil::PhpToJsObject($banners);?>,
                        sectionViewTmpl = '<?=$view?>';

                    for(var i in banners){
                        var banner = banners[i],
                            name = banner.NAME,
                            link = banner.LINK,
                            posBlock = parseInt(banner.POSITION_BLOCKS),
                            posList = parseInt(banner.POSITION_LIST) - 1,
                            pic = $('html').hasClass('webp') ? banner.PIC.WEBP : banner.PIC.JPG;

                        if(posList < 0){
                            posList = 0;
                        }
                        if(posBlock < 0){
                            posBlock = 1;
                        }

                        var posElements = posList,
                            stylesBanner = {
                                maxWidth: liList.first().width() + 'px',
                                padding: 0,
                                marginBottom: '5px'
                            };
                        if(sectionViewTmpl === 'blocks'){
                            var cellsRowCount = Math.trunc($('ul.catalog__list').width() / $('ul.catalog__list li').first().width());
                            if(cellsRowCount > 0){
                                posElements = (cellsRowCount * posBlock) - 1;
                                var maxWidthBanner = liList.first().width() * cellsRowCount + (6 * cellsRowCount);
                                stylesBanner.maxWidth = maxWidthBanner + 'px';
                            }
                        }
                        
                        var liListItem = liList.last();
                        if(liList.length > posElements + 1){
                            liListItem = liList.eq(posElements);
                        }

                        if(!!liListItem.length){
                            if(link !== ''){
                                liListItem.after($('<li class="catalog-item sib-banner-section"><a href="'+link+'" target="_blank"><img title="'+name+'" src="'+pic+'"></a></li>'));
                            } else {
                                liListItem.after($('<li class="catalog-item sib-banner-section"><img title="'+name+'" src="'+pic+'"></li>'));
                            }
                            $('.sib-banner-section').css(stylesBanner);
                        }
                    }
                };

                BX.ready(checkBannerSection);
            </script>
        <?endif?>
    <?endif ?>
<?endif?>

<?
if($bSibCore){
    \Sib\Core\Seo::setParentSection($arCurSection);
    \Sib\Core\Seo::setMinPriceOfSection($arCurSection);
}
?>
