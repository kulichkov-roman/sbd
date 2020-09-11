<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
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
$this->setFrameMode(true);
//global $USER; if($USER->IsAdmin()){echo '<pre>'; print_r($arResult['PROPERTIES']); echo '</pre>';};
?>
<div class="blog-detail">
	<div class="blog__item">
		<div class="blog__item_headline">
			<?if($arResult['TAGS']): 
				$tag = trim(explode(',', $arResult['TAGS'])[0]);
				?>
				<a href="/blog/?tag=<?=$tag?>" class="blog__item_headline__brand"><?=$tag?></a>
			<?endif?>
			<?if($arParams["DISPLAY_DATE"]!="N" && $arResult["DISPLAY_ACTIVE_FROM"]):?>
				<span class="bg blog__item_headline__date"><?=$arResult["DISPLAY_ACTIVE_FROM"]?></span>
			<?endif;?>
			<?//if($arResult['SHOW_COUNTER']):?>
				<span class="bg blog__item_headline__views"><?=$arResult['SHOW_COUNTER']?></span>
			<?//endif;?>			
		</div>
		<h1 class="blog__item_head">
			<?=$arResult["NAME"]?>
		</h1>  
		<div class="blog__item_prevpic">
			<?
				$picType = 'DETAIL';
				if(empty($arResult["DETAIL_PICTURE"]["SRC"])){
					$picType = 'PREVIEW';
				}
			?>
			<span>
				<img 
					src="<?=SITE_TEMPLATE_PATH?>/img/placetransparent.png"
					alt="<?=$arResult["{$picType}_PICTURE"]["ALT"]?>"
					title="<?=$arResult["{$picType}_PICTURE"]["TITLE"]?>"
					class="rbs-find-img-detail-descr placeholder rbs-lazy-img-text"
					data-original="<?=CResizer2Resize::ResizeGD2($arResult["{$picType}_PICTURE"]["SRC"], 50)?>"
					data-original-jpg="<?=CResizer2Resize::ResizeGD2($arResult["{$picType}_PICTURE"]["SRC"], 51)?>"
				>
			</span>
		</div>
		<div class="blog__item_prevdescr">
			<?if(strlen($arResult["DETAIL_TEXT"]) > 0):?>
				<?=$arResult["DETAIL_TEXT"];?>
			<?else:?>
				<?=$arResult["PREVIEW_TEXT"];?>
			<?endif?>
		</div>
		<!--
		<a href="#" class="blog__item_zen">
			<span class="zen-text">Подписывайтесь на наш канал в <span class="red-text">Яндекс.Дзен</span></span>
		</a>
		-->
		<!--
		<div class="blog__item_rate">
			<span class="blog__item_rate__text">Оцените статью:</span>
			<span class="emoji">
			<span class="bg e1"></span>
			<span class="bg e2"></span>
			<span class="bg e3"></span>
			<span class="bg e4"></span>
			<span class="bg e5"></span>
			<span class="bg e6"></span>
			<span class="bg e7"></span>
			</span>
		</div>
		-->
		<?if($arResult['TAGS']):?>
			<div class="blog__item__tags">
				<?foreach(explode(',', $arResult['TAGS']) as $tag):?>
					<a href="/blog/?tag=<?=$tag?>">#<?=trim($tag)?></a>
				<?endforeach;?>
			</div>     
		<?endif?>
			
		<div class="blog__item_footerline">
			<div class="blog__item_fleft">
				<div class="blog__item_share">
					<a href="https://www.facebook.com/sharer/sharer.php?u=#URL#" target="_blank" class="fb"></a>
					<a href="https://twitter.com/intent/tweet?url=#URL#&via=sibdroid" target="_blank"  class="tw"></a>
					<a href="https://vk.com/share.php?url=#URL#" target="_blank" class="vk"></a>
					<a href="https://connect.ok.ru/offer?url=#URL#" target="_blank"  class="add"></a>
				</div>
				<div class="blog__item_comfav">
					<div class="blog__item_footerline__comments">
						<a href="#comments_item_block" class="grid js-to-elem">
							<svg xmlns="http://www.w3.org/2000/svg"><use xlink:href="#svg_comments"></use></svg>
							<span><?=(int)$arResult['PROPERTIES']['BLOG_COMMENTS']['VALUE']?></span>
						</a>
					</div>
					<!-- <div class="blog__item_footerline__favorites">
						<div class="grid">
							<svg xmlns="http://www.w3.org/2000/svg"><use xlink:href="#svg_favorite"></use></svg>
							<span>15</span>
						</div>
					</div> -->
				</div>
			</div>
	
			<div class="blog__item_fright">                    
				<div data-entity="item" data-id="<?=$arResult['ID']?>" data-current="<?=(int)$arResult['PROPERTIES']['BLOG_LIKES']['VALUE']?>" class="js-like-check blog__item_fright__likes neitral">
					<span class="likes__rate likes__down">						
						<svg xmlns="http://www.w3.org/2000/svg"><use xlink:href="#svg_arrow_down"></use></svg>
					</span>
					<span class="likes__count"><?=(int)$arResult['PROPERTIES']['BLOG_LIKES']['VALUE']?></span>
					<span class="likes__rate likes__up">
						<svg xmlns="http://www.w3.org/2000/svg"><use xlink:href="#svg_arrow_up"></use></svg>
					</span>
				</div>
			</div>		
		</div>   
		             
	</div><!-- blog item -->

	<?//global $USER;?>
	<?if(!empty($arResult['PROPERTIES']['RELATED_ITEMS']['VALUE'])):?>
	<?
		global $arFilterDetail;
		$arFilterDetail['ID'] = $arResult['PROPERTIES']['RELATED_ITEMS']['VALUE'];
		$APPLICATION->IncludeComponent(
			'bitrix:catalog.section',
			'detail_items',
			array(
				"FILTER_NAME" => 'arFilterDetail',
				'USE_FILTER' => 'Y',
				'CACHE_FILTER' => 'Y',
				"IBLOCK_TYPE" => "catalog",
				"IBLOCK_ID" => "6",
				"SECTION_ID" => "",
				"SECTION_CODE" => "",
				"INCLUDE_SUBSECTIONS" => "Y",
				"SHOW_ALL_WO_SECTION" => "Y",
				"HIDE_NOTAVAILABLE" => "N",
				"HIDE_WITHOUTPICTURE" => "Y",
				"RESIZER_SET_BIG" => "3",
				"ELEMENT_SORT_FIELD" => "shows",
				"ELEMENT_SORT_ORDER" => "desc",
				"LIST_PRICE_SORT" => "CATALOG_PRICE_1",
				"SHOW_ELEMENT" => "N",
				"OFFERS_FIELD_CODE" => ",",
				"OFFERS_PROPERTY_CODE" => ",",
				"OFFERS_SORT_FIELD" => "sort",
				"OFFERS_SORT_ORDER" => "asc",
				"PAGE_ELEMENT_COUNT" => "36",
				"LINE_ELEMENT_COUNT" => "4",
				"SECTION_URL" => "",
				"DETAIL_URL" => "",
				"BASKET_URL" => "/personal/cart/",
				"ACTION_VARIABLE" => "action",
				"PRODUCT_ID_VARIABLE" => "id",
				"PRODUCT_QUANTITY_VARIABLE" => "quantity",
				"PRODUCT_PROPS_VARIABLE" => "prop",
				"SECTION_ID_VARIABLE" => "SECTION_ID",
				"PRICE_CODE" => $_SESSION["VREGIONS_REGION"]["PRICE_CODE"],
				"STORES" => $_SESSION["VREGIONS_REGION"]["ID_SKLADA"],
				"USE_PRICE_COUNT" => "N",
				"SHOW_PRICE_COUNT" => "1",
				"PRICE_VAT_INCLUDE" => "Y",
				"USE_PRODUCT_QUANTITY" => "Y",
				"HIDE_BUY_IF_PROPS" => "N",
				"CONVERT_CURRENCY" => "Y",
				"OFFERS_CART_PROPERTIES" => "",
				"CACHE_TYPE" => "Y",
				"CACHE_TIME" => "3600",
				"CACHE_GROUPS" => "N",
				"META_KEYWORDS" => "-",
				"META_DESCRIPTION" => "-",
				"BROWSER_TITLE" => "-",
				"ADD_SECTIONS_CHAIN" => "N",
				"DISPLAY_COMPARE" => "Y",
				"COMPARE_PATH" => SITE_DIR."ajax/sib/compare_sib.php",
				"SET_TITLE" => "Y",
				"SET_STATUS_404" => "N",
				"PAGER_TEMPLATE" => "indicators",
				"DISPLAY_TOP_PAGER" => "N",
				"DISPLAY_BOTTOM_PAGER" => "Y",
				"PAGER_SHOW_ALWAYS" => "Y",
				"PAGER_DESC_NUMBERING" => "N",
				"PAGER_DESC_NUMBERING_CACHE_TIME" => "3600",
				"PAGER_SHOW_ALL" => "N",
				"IBLOCK_MAX_VOTE" => "5",
				"IBLOCK_VOTE_NAMES" => array(
					0 => "1",
					1 => "2",
					2 => "3",
					3 => "4",
					4 => "5",
				),
				"DISPLAY_AS_RATING" => "rating",
				"OFFER_TREE_PROPS" => array(
				),
				"PRODUCT_PROPERTIES" => "",
				"INCLUDE_JQUERY" => "Y",
				"SHOW_AMOUNT_STORE" => "Y",
				"COMPONENT_TEMPLATE" => "sib_bitronic2",
				"ARTICUL_PROP" => "MORE_PHOTO1",
				"PARTIAL_PRODUCT_PROPERTIES" => "Y",
				"IMAGE_SET" => "35",
				"DISPLAY_FAVORITE" => "N",
				"DISPLAY_ONECLICK" => "N",
				"HIDE_ICON_SLIDER" => "N",
				"RESIZER_SECTION_ICON" => "5",
				"CURRENCY_ID" => "RUB",
				"BLOCK_VIEW_MODE" => "",
				"COLOR_SCHEME" => "",
				"IMAGE_SET_BIG" => "",
				"PRODUCT_DISPLAY_MODE" => "",
				"USE_MOUSEWHEEL" => "",
				"MAIN_SP_ON_AUTO_NEW" => "N",
				"PROPERTY_CODE" => array(
					0 => "",
					1 => "",
				),
				"COMPOSITE_FRAME_MODE" => "A",
				"COMPOSITE_FRAME_TYPE" => "AUTO"
			),
			$component
		);?>	
	<?endif?>			
	<?
		$APPLICATION->IncludeComponent(
			'sibdroid:blog.comments',
			'',
			[
				'CACHE_TYPE' => 'A',
				'CACHE_TIME' => 3600,
				'ELEMENT_ID' => $arResult['ID']
			],
			false
		);
	?>
</div>