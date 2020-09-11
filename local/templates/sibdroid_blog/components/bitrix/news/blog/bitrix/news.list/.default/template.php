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
$count = 1;
//global $USER; if($USER->IsAdmin()){echo '<pre>'; print_r($arResult); echo '</pre>';};
?>
<div class="blog js-blog-list blog-list">
	<?if(count($arResult["ITEMS"]) === 0):?>
	<div class="blog__item">
		<div class="blog__item_headline">
			К сожалению, нет новостей по вашему запросу
		</div>
	</div>
	<?endif?>
	<?foreach($arResult["ITEMS"] as $arItem):	
		$isLast = (int)$arParams['NEWS_COUNT'] === $count++;
		if($isLast){
			$nextPage = $arResult['NAV_RESULT']->NavPageNomer + 1;
		}
		?>
		<div class="blog__item <?= $isLast ? 'blog__item_last' : ''?>" <?= $isLast && $nextPage <= $arResult['NAV_RESULT']->NavPageCount ? ' data-next-page="' . $nextPage . '"' : ''?>>
			<div class="blog__item_headline">
				<?if($arItem['TAGS']):
					$tag = trim(explode(',', $arItem['TAGS'])[0]);
					?>
					<a href="<?=$_SERVER['SCRIPT_URL'] . '?tag=' . $tag?>" class="bg blog__item_headline__brand"><?=$tag?></a>
				<?endif?>
				<?if($arParams["DISPLAY_DATE"] === "Y" && $arItem["DISPLAY_ACTIVE_FROM"]):?>
					<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="bg blog__item_headline__date"><?=$arItem["DISPLAY_ACTIVE_FROM"]?></a>
				<?endif?>
			</div>
			<div class="blog__item_head">
				<h2 onclick="window.location.href='<?=$arItem['DETAIL_PAGE_URL']?>'"><?=$arItem["NAME"]?></h2>
			</div>
			<div class="blog__item_prevdescr">
				<a href="<?=$arItem["DETAIL_PAGE_URL"]?>">
					<?=($arItem["PREVIEW_TEXT"])?>
				</a>
			</div>
			<?if($arItem["PREVIEW_PICTURE"]["SRC"]):?>
				<div class="blog__item_prevpic">
					<a href="<?=$arItem["DETAIL_PAGE_URL"]?>">
						<img 
							src="<?=SITE_TEMPLATE_PATH?>/img/placetransparent.png"
							alt="<?=$arItem["PREVIEW_PICTURE"]["ALT"]?>"
							title="<?=$arItem["PREVIEW_PICTURE"]["TITLE"]?>"
							class="rbs-find-img-detail-descr placeholder rbs-lazy-img-text"
							data-original="<?=CResizer2Resize::ResizeGD2($arItem["PREVIEW_PICTURE"]["SRC"], 50)?>"
							data-original-jpg="<?=CResizer2Resize::ResizeGD2($arItem["PREVIEW_PICTURE"]["SRC"], 51)?>"
						>
					</a>
				</div>
			<?endif?>

			<div class="blog__item_footerline">
				<div class="blog__item_fleft">
					<div class="blog__item_comfav">
						<div class="blog__item_footerline__comments js-check-comment" data-entity="item" data-id="<?=$arItem['ID']?>">
							<a href='<?=$arItem["DETAIL_PAGE_URL"]?>#comments_item_block' class="grid">
								<svg xmlns="http://www.w3.org/2000/svg"><use xlink:href="#svg_comments"></use></svg>
								<span><?=(int)$arItem['PROPERTIES']['BLOG_COMMENTS']['VALUE']?></span>
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
					<div data-entity="item" data-id="<?=$arItem['ID']?>" data-current="<?=(int)$arItem['PROPERTIES']['BLOG_LIKES']['VALUE']?>" class="js-like-check blog__item_fright__likes neitral">
						<span class="likes__rate likes__down">						
							<svg xmlns="http://www.w3.org/2000/svg"><use xlink:href="#svg_arrow_down"></use></svg>
						</span>
						<span class="likes__count"><?=(int)$arItem['PROPERTIES']['BLOG_LIKES']['VALUE']?></span>
						<span class="likes__rate likes__up">
							<svg xmlns="http://www.w3.org/2000/svg"><use xlink:href="#svg_arrow_up"></use></svg>
						</span>
					</div>
				</div>		
			</div>   

			

		</div>
	<?endforeach?>
</div>
<div class="load-list-block">
	<img src="<?=SITE_TEMPLATE_PATH?>/img/ajax-loader.gif" alt="loader">
</div>