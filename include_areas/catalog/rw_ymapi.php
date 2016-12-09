<?if(CModule::IncludeModule('yenisite.yandexreviewsmodel')):?>
<? $APPLICATION->IncludeComponent(
	"yenisite:yandex.market_reviews_model", 
	".default", 
	array(
		"MODEL" => $arParams["ELEMENT_ID"],
		"ACCESSTOKEN" => "5WoL7mGzhVT3VKc8zA4je3vPxiia0c",
		"HEAD" => "Отзывы о товар:",
		"HEAD_SIZE" => "h2",
		"SORT" => "date",
		"HOW" => "desc",
		"GRADE" => "0",
		"COUNT" => "5",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "86400",
		"INCLUDE_JQUERY" => "Y",
		"COMPONENT_TEMPLATE" => ".default"
	),
	false,
	array(
		"ACTIVE_COMPONENT" => "Y"
	)
); ?>
<?endif;?>