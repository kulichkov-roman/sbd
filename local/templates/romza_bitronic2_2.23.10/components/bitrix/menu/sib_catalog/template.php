<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main\Page\Asset;
use Bitronic2\Mobile;

if(method_exists($this, 'setFrameMode')) $this->setFrameMode(true);

$curPage = $APPLICATION->GetCurPage();
$bMainPage = $APPLICATION->GetCurPage(false) == SITE_DIR;

$cnt = count($arResult["ALL_ITEMS_ID"]);
$height = ceil(400/$cnt);
$heightFirstLast = $cnt ==7 ? ceil(400/$cnt) + 1 : $height;
?>
<script>
	var menuItemCountCol = <?=$arParams['COL_COUNT']?:17?>;
</script>
<style>
.main-nav__text{height: <?=$height?>px;}
.main-nav__text:first-child,.main-nav__text:last-child{height: <?=$heightFirstLast?>px;}
</style>
<nav class="rbs-main-nav-desktop main-nav js-nav main-nav_desktop <?=!$bMainPage?'main-nav_inner':'main-nav_index';?>" itemscope itemtype="http://schema.org/SiteNavigationElement" id="js-nav-desktop">
	<ul class="rbs-main-nav-desktop-ul main-nav__list">
		<!-- BEGIN ITEM -->
		<?foreach($arResult["ALL_ITEMS_ID"] as $idItem_1=>$arItem_1):?>
			<?$hasChildItem_1 = is_array($arItem_1) && count($arItem_1) > 0;?>
			<?$img = &$arResult['ALL_ITEMS'][$idItem_1]['PARAMS']['PICTURE'];?>
			<?$backImg = &$arResult['ALL_ITEMS'][$idItem_1]['PARAMS']['BACKGROUND'];?>
			<?// global $USER; if($USER->IsAdmin()){echo '<pre>'; print_r(); echo '</pre>';}; ?>
			<li class="main-nav__item js-nav-item <?=$backImg?'rbs-background-li-menu-item':''?>" <?=$backImg?'style="background-image:url('.$backImg.')"':''?>>
				<a class="main-nav__link <?=$arResult['ALL_ITEMS'][$idItem_1]['PARAMS']['UF_FIELDS']['UF_CLASS_MENU']?> js-nav-link" href="<?=$arResult["ALL_ITEMS"][$idItem_1]['LINK']?>">
					<span class="main-nav__fix <?=$hasChildItem_1?'':'rbs-none-arrow'?>">
						<span class="main-nav__text"><?=$arResult["ALL_ITEMS"][$idItem_1]['TEXT']?></span>
					</span>
					<?/*if($img):?>
						<img class="rbs-menu-img" src="<?=$img?>" alt="<?=$arResult["ALL_ITEMS"][$idItem_1]['TEXT']?>">
					<?endif*/?>
				</a>
				<?if($hasChildItem_1):?>
					<?$frame = $this->createFrame()->begin('')?>
						<ul class="inner-nav js-nav-hide rbs-ul-lvl-2">
							<?foreach($arItem_1 as $idItem_2=>$arItem_2):?>
							<?$hasChildItem_2 = is_array($arItem_2) && count($arItem_2) > 0;?>
							<?$img = &$arResult['ALL_ITEMS'][$idItem_2]['PARAMS']['PICTURE'];?>
							<!-- BEGIN ITEM -->
								<li class="inner-nav__item js-nav-item rbs-li-lvl-2">
									<a class="inner-nav__link <?=$hasChildItem_2?'':'rbs-none-arrow'?> js-nav-link" href="<?=$arResult["ALL_ITEMS"][$idItem_2]['LINK']?>">
										<span class="inner-nav__fix">
											<?/*?> <span class="inner-nav__image">
												<img alt="<?=$arResult["ALL_ITEMS"][$idItem_2]['TEXT']?>" src="<?=$img?>">
											</span>
											<?*/?>
											<span class="inner-nav__text"><span><?=$arResult["ALL_ITEMS"][$idItem_2]['TEXT']?></span></span>
										</span>                                        	
									</a>
									<?if($hasChildItem_2):?>
									<?
										$arItemChunk = array_chunk($arItem_2, (int)$arParams['COL_COUNT']);
										foreach($arItemChunk as $arItem_2):
									?>
											<ul class="inner-nav rbs-ul-lvl-3 js-nav-hide">
												<?foreach($arItem_2 as $idItem_3):?>
												<?$img = &$arResult['ALL_ITEMS'][$idItem_3]['PARAMS']['PICTURE'];?>
													<li class="inner-nav__item">
														<a class="inner-nav__link" href="<?=$arResult["ALL_ITEMS"][$idItem_3]['LINK']?>">
															<span class="inner-nav__fix">
															<?/*?> <span class="inner-nav__image">
																	<img alt="<?//=$arResult["ALL_ITEMS"][$idItem_3]['TEXT']?>" src="<?=$img?>">
																</span> <?*/?>
																<span class="inner-nav__text"><span><?=$arResult["ALL_ITEMS"][$idItem_3]['TEXT']?></span></span>
															</span>                                                 
														</a>                                            
													</li> 
												<?endforeach;?>								
											</ul>
										<?endforeach;?>
									<?endif;?>
								</li>
							<?endforeach;?>
							<!-- ITEM EOF -->                                                                                                                 
						</ul>
					<?$frame->end()?>
				<?endif;?>
			</li>
		<?endforeach;?>
		<!-- ITEM EOF -->                                                                                                                                                                  
	</ul>
</nav>