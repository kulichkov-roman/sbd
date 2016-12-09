<?if($arParams['USE_OWN_REVIEW'] != 'N'):?>
	<?
	$bAjax = (isset($_REQUEST['rz_ajax']) && $_REQUEST['rz_ajax'] === 'y');
	?>
	<?if($arParams['REVIEWS_MODE'] == 'blog'):?>
		<?$APPLICATION->IncludeComponent(
			"bitrix:blog.post.comment",
			"reviews",
			array(
				"SEO_USER" => "N",
				"ID" => $arResult['PROPERTIES'][CIBlockPropertyTools::CODE_BLOG_POST]['VALUE'],
				// for create new blog post
				"ELEMENT" => array(
					"DETAIL_PAGE_URL" => $arResult["DETAIL_PAGE_URL"],
					"NAME" => $arResult["NAME"],
					"PREVIEW_TEXT" => $arResult["PREVIEW_TEXT"],
				),

				"BLOG_URL" => $arParams['BLOG_URL'],
				"PATH_TO_SMILE" => $arParams["PATH_TO_SMILE"],
				"COMMENTS_COUNT" => $arParams["MESSAGES_PER_PAGE"],
				"DATE_TIME_FORMAT" => $DB->DateFormatToPhp(FORMAT_DATETIME),
				"CACHE_TYPE" => $arParams["CACHE_TYPE"],
				"CACHE_TIME" => $arParams["CACHE_TIME"],
				"AJAX_POST" => "N",
				"AJAX_MODE" => "N",
				"AJAX_OPTION_HISTORY" => "N",
				"RZ_AJAX" => $bAjax,
				"SIMPLE_COMMENT" => "Y",
				"SHOW_SPAM" => "Y",
				"NOT_USE_COMMENT_TITLE" => "Y",
				"SHOW_RATING" => "N",
				"RATING_TYPE" => $arParams["RATING_TYPE"],
				"PATH_TO_POST" => $arResult["URL_TO_COMMENT"],
				"IBLOCK_ID" => $arParams['IBLOCK_ID'],
				"ELEMENT_ID" => $arResult['ID'],
				"NO_URL_IN_COMMENTS" => "L",
				"RESIZER_COMMENT_AVATAR" => $arParams['RESIZER_SETS']['RESIZER_COMMENT_AVATAR']
			),
			$component,
			array("HIDE_ICONS" => "Y")
		);?>
	<?else:?>
		<?$APPLICATION->IncludeComponent(
			"bitrix:forum.topic.reviews",
			"reviews",
			Array(
				"CACHE_TYPE" => $arParams["CACHE_TYPE"],
				"CACHE_TIME" => $arParams["CACHE_TIME"],
				"MESSAGES_PER_PAGE" => $arParams["MESSAGES_PER_PAGE"],
				"USE_CAPTCHA" => $arParams["USE_CAPTCHA"],
				"PATH_TO_SMILE" => $arParams["PATH_TO_SMILE"],
				"FORUM_ID" => $arParams["FORUM_ID"],
				"URL_TEMPLATES_READ" => $arParams["URL_TEMPLATES_READ"],
				"SHOW_LINK_TO_FORUM" => $arParams["SHOW_LINK_TO_FORUM"],
				"ELEMENT_ID" => $arResult['ID'],
				"IBLOCK_ID" => $arParams["IBLOCK_ID"],
				"AJAX_POST" => $arParams["REVIEW_AJAX_POST"],
				"RZ_AJAX" => $bAjax,
				"POST_FIRST_MESSAGE" => $arParams["POST_FIRST_MESSAGE"],
				"URL_TEMPLATES_DETAIL" => "",
				"AUTOSAVE" => false,
				"PAGE_NAVIGATION_TEMPLATE" => ".default",
				"PREORDER" => (!empty($arParams["PREORDER"])) ? $arParams["PREORDER"] : "N",
			),
			$component,
			array("HIDE_ICONS" => "Y")
		);?>
	<?endif?>
<?endif?>