<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/urlrewrite.php';
require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';
CHTTP::SetStatus('404 Not Found');
if (!defined('ERROR_404')) {
	define('ERROR_404', 'Y');
}

$APPLICATION->SetPageProperty('title', 'Страница не найдена');
// $APPLICATION->AddChainItem('404');

global $rz_b2_options;

$asset = Bitrix\Main\Page\Asset::getInstance();
$asset->addJs(SITE_TEMPLATE_PATH."/js/custom-scripts/inits/pages/initNotFoundPage.js");

$bCore = Bitrix\Main\Loader::includeModule('yenisite.core');
global $isNewTemplate;
?>
<?if($isNewTemplate):
  $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/new_css/style_1.css");
  ?>
<section class="main-block">
    <div class="box-404">
        <div class="box-404__inner">
            <div class="box-404__img">
                <img src="<?=SITE_TEMPLATE_PATH?>/new_img/404.png" alt="404">
            </div>
            <div class="box-404__heading">
                <h3>Страница не найдена</h3>
            </div>
            <div class="box-404__text">
                Страница, которую вы ищете, была перемещена, удалена, переименована или никогда не существовала.
            </div>
            <div class="box-404__btn">
                <a href="<?=SITE_DIR?>" class=" button">На главную</a>
            </div>
        </div>
    </div>              
</section>

<style type="text/css">
	/*404*/
.main_404 {
  background-image: url('<?=SITE_TEMPLATE_PATH?>/new_img/404-bg.jpg');
  -webkit-background-size: cover;
          background-size: cover;
  /*padding-top: 148px;*/
  padding-bottom: 182px;
  background-position: 0px -170px;
  background-repeat: no-repeat;
}
.main_404 .main-block {
  background-color: transparent;
  padding-top: 172px;
}
.box-404 {
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-pack: center;
  -webkit-justify-content: center;
      -ms-flex-pack: center;
          justify-content: center;
  -webkit-box-align: center;
  -webkit-align-items: center;
      -ms-flex-align: center;
          align-items: center;
}
.box-404__inner {
  text-align: center;
}
.box-404__img {
  text-align: center;
  margin-bottom: 49px;
}
.box-404__img img {
  display: inline-block;
}
.box-404__heading h3 {
  text-transform: uppercase;
  font-size: 24px;
  line-height: 24px;
  color: #999999;
  padding: 0 0 14px;
}
.box-404__text {
  font-size: 15px;
  line-height: 20px;
  color: #666666;
  max-width: 405px;
  letter-spacing: -0.01px;
}
.box-404__btn {
  margin-top: 32px;
}
.box-404__btn .button {
  padding: 0 33px;
}
@media screen and (max-width: 1720px) {
  .main_404 {
    background-position: 0 0;
  }
}
/*sitemap*/
.sitemap-list {
  -webkit-column-count: 4;
     -moz-column-count: 4;
          column-count: 4;
  -webkit-column-gap: 50px;
     -moz-column-gap: 50px;
          column-gap: 50px;
  padding-bottom: 0;
  position: relative;
  left: -7px;
}
.sitemap-list__item {
  font-size: 13px;
  line-height: 28px;
  color: #006699;
  font-weight: 700;
  padding-left: 7px;
}
.sitemap-sublist {
  padding: 0;
}
.sitemap-sublist__item {
  position: relative;
}
.sitemap-sublist__link {
  font-weight: 400;
  font-size: 13px;
  line-height: 28px;
  color: #006699;
  border-bottom: 1px solid transparent;
  letter-spacing: 0.1px;
}
.sitemap-sublist__link:hover {
  color: #006699!important;
  border-color: #006699;
}
.sitemap-sublist__item:before {
  content: '';
  display: block;
  position: absolute;
  width: 3px;
  height: 3px;
  left: -7px;
  top: 12px;
  background-color: #006699;
  -webkit-border-radius: 50%;
          border-radius: 50%;
  /*display: none;*/
}
.header-sublist {
  padding: 13px 0px 9px 25px;
}
.main-block_sitemap .main-title {
  margin-left: -7px;
}
.main-block_sitemap {
  padding: 15px 32px 23px;
  margin: 0 0 9px;
}
@media screen and (max-width: 1125px) {
  .sitemap-list__item {
    line-height: 18px;
  }
}
</style>
<?else:?>
<main class="container not-found-page" data-page="not-found-page">
	<? if ($bCore) Yenisite\Core\Tools::includeArea('404', 'banner', false, true, $rz_b2_options['block_show_ad_banners']) ?>
	<div class="row">
		<div class="col-xs-12">
			<h1><?$APPLICATION->ShowTitle()?></h1>
			<p>Так сложились звезды, что этой страницы либо не существует, либо ее похитили инопланетяне для опытов. Но это не беда! Мы уверены, что вы обязательно найдете что-нибудь полезное для себя в нашем интернет-магазине.</p>
			<div class="sad-robot"></div>
			<div class="big404">404</div>
			<p>Перейти к <a href="<?=SITE_DIR?>site-map/" class="link"><span class="text">карте сайта</span></a>.</p>
		</div><!-- /.col-xs-12 -->
	</div>
	<?
	if ('N' !== $rz_b2_options['block_404-viewed']
	||  'N' !== $rz_b2_options['block_404-bestseller']
	||  'N' !== $rz_b2_options['block_404-recommend']
	) {
		CJSCore::Init(array('rz_b2_bx_catalog_item'));
		$asset->addJs(SITE_TEMPLATE_PATH . '/js/custom-scripts/inits/sliders/initHorizontalCarousels.js');

		$arParams = array();
		if ($bCore) {
			$arParams = Yenisite\Core\Ajax::getParams('bitrix:catalog', false, CRZBitronic2CatalogUtils::getCatalogPathForUpdate());
		}
		// @var array $arPrepareParams
		include $_SERVER['DOCUMENT_ROOT'] . SITE_TEMPLATE_PATH . '/components/bitrix/catalog/.default/include/prepare_params_element.php';
	}
	if ($rz_b2_options['block_404-viewed'] !== 'N') {
		include $_SERVER['DOCUMENT_ROOT'] . SITE_TEMPLATE_PATH . '/components/bitrix/catalog/.default/include/viewed_products.php';
	}
	if ($rz_b2_options['block_404-bestseller'] !== 'N') {
		$arPrepareParams['HEADER_TEXT'] = $arParams['BIGDATA_BESTSELL_TITLE'] ?: 'Лидеры продаж';
		$arPrepareParams['RCM_TYPE'] = 'bestsell';
		include $_SERVER['DOCUMENT_ROOT'] . SITE_TEMPLATE_PATH . '/components/bitrix/catalog/.default/include/bigdata.php';
	}
	if ($rz_b2_options['block_404-recommend'] !== 'N') {
		$arPrepareParams['HEADER_TEXT'] = $arParams['BIGDATA_PERSONAL_TITLE'] ?: 'Рекомендуем';
		$arPrepareParams['RCM_TYPE'] = 'personal';
		include $_SERVER['DOCUMENT_ROOT'] . SITE_TEMPLATE_PATH . '/components/bitrix/catalog/.default/include/bigdata.php';
	}
	?>

</main>
<?endif?>
<? require $_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php' ?>