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

$asset->addCss(SITE_TEMPLATE_PATH."/new_css/discounts.css");
$asset->addJs(SITE_TEMPLATE_PATH."/js/custom-scripts/inits/toggles/initGenInfoToggle.js");
CJSCore::Init(array('rz_b2_um_countdown', 'rz_b2_bx_catalog_item'));
$asset->addJs(SITE_TEMPLATE_PATH."/js/custom-scripts/inits/pages/initCatalogPage.js");
$asset->addString('<script>RZB2.ajax.CatalogSection.ID = '. (int)$rz_current_sectionID .';</script>', false, AssetLocation::AFTER_JS);

$arParams['SEF_FOLDER'] = $arResult["FOLDER"] = '/catalog/';

$bSibCore = \Bitrix\Main\Loader::includeModule('sib.core');
if($bSibCore){
    global ${$arParams['FILTER_NAME']};
    $defaultFilter = ['IBLOCK_ID' => $arParams['IBLOCK_ID'], 'ACTIVE' => 'Y'];

    $regionId = \Sib\Core\Catalog::getDefRegion($_SESSION['VREGIONS_REGION']['ID']);
    ${$arParams['FILTER_NAME']}['PROPERTY_SIB_AVAIL_' . $regionId] = \Sib\Core\Helper::getPropValue('SIB_AVAIL_' . $regionId, 'AVAILABLE');
    ${$arParams['FILTER_NAME']}['PROPERTY_' . \Sib\Core\Catalog::getTabPropertySale() . '_VALUE'] = 'Y';
    
    ${$arParams['FILTER_NAME']}['!IBLOCK_SECTION_ID'] = \Sib\Core\Helper::getSubSectionFromParents($defaultFilter, ['ID' => [608, 56]]);

    $arTreeFirst = \Sib\Core\Helper::getSectionTreeForDepthFirst();
    
    $sections = [];
    $rsEls = \CIblockElement::GetList([], array_merge($defaultFilter, ${$arParams['FILTER_NAME']}), false, false, ['ID', 'IBLOCK_SECTION_ID']);
    $cntEls = $rsEls->SelectedRowsCount();
    while($obEl = $rsEls->fetch()){
        $sectionFirstId = isset($arTreeFirst[$obEl['IBLOCK_SECTION_ID']]) ? $arTreeFirst[$obEl['IBLOCK_SECTION_ID']] : $obEl['IBLOCK_SECTION_ID'];
        $sections[$sectionFirstId][] = $obEl['ID'];
    }
    $rsSec = \CIblockSection::GetList([], ['IBLOCK_ID' => $arParams['IBLOCK_ID'], 'ID' => array_keys($sections)], false, ['ID', 'NAME', 'DETAIL_PAGE_URL']);
} 
?>
<section class="main-block main-block_search main-block_catalog2 main-block_catalog2-2">
    <h1 class="main-title"><?=$h1?$h1.' ':'';?><?$APPLICATION->ShowTitle(false)?> <?/*=$_SESSION['VREGIONS_REGION']['WHERE'] ? 'в ' . $_SESSION['VREGIONS_REGION']['WHERE'] : ''*/?></h1>
    <?/*if (!(array_key_exists('rz_all_elements', $_REQUEST) && $_REQUEST['rz_all_elements'] === 'y')):?>
        <?include 'include/section_list.php';?>
    <?endif*/?>
    <div class="search-box">
        <div class="filters-toggle js-filters">Фильтры</div>
        <div class="category">
            <?//include 'include/filter.php';?>
            <div class="rbs-category-discount">
                <h4>Спецпредложения:</h4>
                <div class="accordion">
                    <div class="accordion__item active category-list">
                        <div class="accordion__heading">
                            <div class="category-list__item active"><a class="category-list__link rbs-accordion-item" href="javascript:void(0);" data-section-id="0">Товары со скидкой <span><?=$cntEls?></span></a></div>
                            <div class="accordion__arrow"></div>
                        </div>
                        <div class="accordion__text">                                      
                            <div class="tab-cont">
                                <div class="tab-inner">
                                    <div class="tab-text tab-text_desc">
                                        <ul class="category-list filter-section">
                                            <?while($obSec = $rsSec->GetNext()):?>
                                                <li class="category-list__item">
                                                    <a href="#" class="category-list__link" data-section-id="<?=$obSec['ID']?>"><?=$obSec['NAME']?> <span><?=count($sections[$obSec['ID']])?></span></a>
                                                </li>
                                            <?endwhile?>
                                           <!--  <li class="category-list__item">
                                                <a href="#" class="category-list__link" data-section-id="0">Все товары <span><?=$cntEls?></span></a>
                                            </li> -->
                                        </ul>
                                    </div>
                                </div>
                            </div>                                        
                        </div>
                    </div>
                </div> 

                <? 
                    $menuType = 'discount';
                    if(\Sib\Core\Catalog::isMskRegion($_SESSION['VREGIONS_REGION']['ID'])){
                        $menuType = 'discount_msk';
                    }

                    $APPLICATION->IncludeComponent(
                        "bitrix:menu", 
                        "sib_discount", 
                        array(
                            "ROOT_MENU_TYPE" => $menuType,
                            "MAX_LEVEL" => "3",
                            "CHILD_MENU_TYPE" => $menuType,
                            "USE_EXT" => "N",
                            "MENU_CACHE_TYPE" => "A",
                            "MENU_CACHE_TIME" => "604800",
                            "MENU_CACHE_USE_GROUPS" => "Y",
                            "MENU_CACHE_GET_VARS" => array(),
                            "DELAY" => "N",
                            "CACHE_SELECTED_ITEMS" => false,
                            "ALLOW_MULTI_SELECT" => "N",
                            "COMPONENT_TEMPLATE" => "static",
                            "COMPOSITE_FRAME_MODE" => "A",
                            "COMPOSITE_FRAME_TYPE" => "AUTO",
                        ),
                        false
                    );
                ?>               
            </div>

            <!-- <div class="rbs-category-discount">
                <h4>Спецпредложения:</h4>
                
            </div> -->
           
        </div>
        <div class="result">
            <div class="tab-wrap">
                <div class="rbs-discount-banner">
                <?
                global $discountBanners;
                $discountBanners['PROPERTY_RBS_DISCOUNT_VALUE'] = 'Y';
                $APPLICATION->IncludeComponent(
                    "bitrix:news.list", 
                    "sib_discount_slider",
                    array(
                        "IBLOCK_TYPE" => "services",
                        "IBLOCK_ID" => "4",
                        "CACHE_TYPE" => "A",
                        "CACHE_TIME" => "36000000",
                        "CACHE_GROUPS" => "Y",
                        "NEWS_COUNT" => "30",
                        "FILTER_NAME" => "discountBanners",
                        "USE_FILTER" => 'Y',
                        "FIELD_CODE" => array(
                            
                        ),
                        "PROPERTY_CODE" => array(
                            0 => "RBS_IMG",
                            1 => "RBS_LINK",
                            2 => "RBS_TITLE",
                            3 => "RBS_DESC"
                        ),
                        "CHECK_DATES" => "Y",
                        "CACHE_FILTER" => "N",
                        "SET_TITLE" => "N",
                        "SET_BROWSER_TITLE" => "N",
                        "SET_META_KEYWORDS" => "N",
                        "SET_META_DESCRIPTION" => "N",
                        "SET_STATUS_404" => "N",
                        "SORT_BY1" => "sort",
                        "SORT_ORDER1" => "asc",
                        "SORT_BY2" => "id",
                        "SORT_ORDER2" => "desc",
                        "DETAIL_URL" => "",
                        "AJAX_MODE" => "N",
                        "AJAX_OPTION_JUMP" => "N",
                        "AJAX_OPTION_STYLE" => "Y",
                        "AJAX_OPTION_HISTORY" => "N",
                        "AJAX_OPTION_ADDITIONAL" => "",
                        "PREVIEW_TRUNCATE_LEN" => "",
                        "ACTIVE_DATE_FORMAT" => "d.m.Y",
                        "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
                        "ADD_SECTIONS_CHAIN" => "N",
                        "HIDE_LINK_WHEN_NO_DETAIL" => "N",
                        "PARENT_SECTION" => "",
                        "PARENT_SECTION_CODE" => "",
                        "INCLUDE_SUBSECTIONS" => "Y",
                        "PAGER_TEMPLATE" => ".default",
                        "DISPLAY_TOP_PAGER" => "N",
                        "DISPLAY_BOTTOM_PAGER" => "Y",
                        "PAGER_TITLE" => "Новости",
                        "PAGER_SHOW_ALWAYS" => "N",
                        "PAGER_DESC_NUMBERING" => "N",
                        "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
                        "PAGER_SHOW_ALL" => "N",
                        "COMPONENT_TEMPLATE" => "sib_big_slider",
                        "SET_LAST_MODIFIED" => "N",
                        "STRICT_SECTION_CHECK" => "N",
                        "YOUTUBE_PARAMETERS" => "",
                        "RESIZER_SET" => "41",
                        "PAGER_BASE_LINK_ENABLE" => "N",
                        "SHOW_404" => "N",
                        "MESSAGE_404" => "",
                        "REGION_ID" => $_SESSION["VREGIONS_REGION"]["ID"]
                    ),
                    false
                );
                ?>
                </div>
                <div class="tab-top">
                    <?
                        include 'include/sort.php';
                        include 'include/view.php';
                    ?>
                </div>
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
                    if((int)$_GET['section'] > 0){
                        $arSectionParams['SECTION_ID'] = (int)$_GET['section'];
                    }
                    //global $USER; if($USER->IsAdmin()){echo '<pre>'; print_r($arSectionParams); echo '</pre>';};
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
                                $arCurSection['DESCRIPTION'] = \Sib\Core\Helper::initLazyLoadImgFromText($arCurSection['DESCRIPTION'], 'section_' . $arCurSection['ID']);
                            }
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
                        <?endif?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    RZB2.ajax.params['section'] = '';
    $('.accordion .category-list__item').on('click', 'a', function(e){
        
        if(!$(this).hasClass('rbs-accordion-item')){
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
        } else {
            //console.log($(this).closest('.accordion__item').find('.accordion__text').css('display') == 'block');
            if(
                $(this).closest('.accordion__item').find('.accordion__text').css('display') == 'block' &&
                $('.category-list.filter-section li.active').length > 0
            ){
                RZB2.ajax.CatalogSection.Start(this, {'PAGEN_2': 1, 'section': $(this).data('section-id')});
                $('.category-list.filter-section li').removeClass('active');
            }
        }
       
    });
</script>