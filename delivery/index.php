<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
global $isNewTemplate;
$APPLICATION->SetPageProperty("description", "Наш магазин предлагает самые лучшие условия доставки Ваших заказов по всей России!");
$APPLICATION->SetPageProperty("keywords", "доставка, стоимость, сроки");
$APPLICATION->SetPageProperty("title", "Сроки, стоимость и условия доставки в Sibdroid.ru");
$APPLICATION->SetTitle("Доставка");

if($isNewTemplate):
$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/new_css/style_1.css");
?>
<section class="main-block main-block_tabs">
	<div class="tab-wrap tab-wrap_delivery">
		<ul class="nav-tab-list tabs">
			<li class="nav-tab-list__item active">
				<a href="#tab_1" class="nav-tab-list__link">Способы доставки</a>
			</li>
			<li class="nav-tab-list__item">
				<a href="#tab_2" class="nav-tab-list__link">Способы оплаты</a>
			</li>
		</ul>
		<div class="box-tab-cont">
			#VREGION_TEXT_BY_URL_NEW#
		</div>
	</div>
	
</section>
<?else:?>
<main class="container about-page">
<h1><?$APPLICATION->ShowTitle()?></h1>
#VREGION_TEXT_BY_URL#
 </main>
<?endif?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>