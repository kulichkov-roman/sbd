<?if(\Bitrix\Main\Loader::includeModule("advertising")):?>
	<?$APPLICATION->IncludeComponent(
		"bitrix:advertising.banner",
		"simple",
		Array(
			"TYPE" => "b2_index_bot_left",
			"NOINDEX" => "Y",
			"CACHE_TYPE" => "A",
			"CACHE_TIME" => "3600"
		)
	);?>
<?endif?>