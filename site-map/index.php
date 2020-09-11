<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Карта сайта");
global $isNewTemplate;

if($isNewTemplate):
    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/new_css/style_1.css");
?>
<style>
.site-map-actions{display:none;}
</style>
<section class="main-block">
	<h1><?$APPLICATION->ShowTitle()?></h1>
	<?
	$APPLICATION->IncludeComponent("bitrix:menu", "sitemap", array(
        "CHILD_MENU_TYPE" => "top_sub",
        "MAX_LEVEL" => "2",
		"ROOT_MENU_TYPE" => "top",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "86400"
		),
		false
	);

	$APPLICATION->IncludeComponent("bitrix:menu", "sitemap", array(
        "CHILD_MENU_TYPE" => "top_sub",
        "MAX_LEVEL" => "2",
		"ROOT_MENU_TYPE" => "catalog",
		"USE_EXT" => "Y",
		"SECOND" => "Y",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "86400"
		),
		false
	);
	?>
</section>
<?else:?>

<main class="container site-map-page" data-page="site-map-page">
	<div class="row">
		<div class="col-xs-12">
		<h1><?$APPLICATION->ShowTitle()?></h1>
	<?
	$APPLICATION->IncludeComponent("bitrix:menu", "sitemap", array(
        "CHILD_MENU_TYPE" => "top_sub",
        "MAX_LEVEL" => "2",
		"ROOT_MENU_TYPE" => "top",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "86400"
		),
		false
	);

	$APPLICATION->IncludeComponent("bitrix:menu", "sitemap", array(
        "CHILD_MENU_TYPE" => "top_sub",
        "MAX_LEVEL" => "2",
		"ROOT_MENU_TYPE" => "catalog",
		"USE_EXT" => "Y",
		"SECOND" => "Y",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "86400"
		),
		false
	);
	?>
		</div>
	</div>
</main>
<?endif?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");