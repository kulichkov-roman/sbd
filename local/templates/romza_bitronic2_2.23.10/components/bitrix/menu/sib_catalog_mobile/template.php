<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main\Page\Asset;
use Bitronic2\Mobile;

if(method_exists($this, 'setFrameMode')) $this->setFrameMode(true);

$curPage = $APPLICATION->GetCurPage();
?>
<a class="main-nav__link main-nav__link_ctg js-nav-link" href="javascript:;">
	<span class="main-nav__fix">
		<span class="main-nav__text">Каталог</span>
	</span>
</a>

<ul class="inner-nav js-nav-hide rbs-main-catalog-menu">
	<li>
		<div class="inner-nav__head">
			<button class="button-back js-click-back-catalog"></button>                                            
			<div class="inner-nav__head-txt">
				<p class="inner-nav__title">Каталог</p>                                                
			</div>
			<button class="inner-nav__button button-close js-click-close"></button>
		</div>
	</li>
	<!-- BEGIN ITEM -->
	<?foreach($arResult["ALL_ITEMS_ID"] as $idItem_1=>$arItem_1):?>
		<?$img = &$arResult['ALL_ITEMS'][$idItem_1]['PARAMS']['PICTURE'];?>
		<?$hasChildItem_1 = is_array($arItem_1) && count($arItem_1) > 0;?>
		<?$backImg = &$arResult['ALL_ITEMS'][$idItem_1]['PARAMS']['BACKGROUND'];?>
		<li class="main-nav__item js-nav-item <?=$backImg?'rbs-background-li-menu-item':''?>" <?=$backImg?'style="background-image:url('.$backImg.')"':''?>>
			<a class="main-nav__link <?=$arResult['ALL_ITEMS'][$idItem_1]['PARAMS']['UF_FIELDS']['UF_CLASS_MENU']?> js-nav-link" href="<?=$hasChildItem_1 ? 'javascript:;' : $arResult["ALL_ITEMS"][$idItem_1]['LINK']?>">
				<span class="main-nav__fix <?=$hasChildItem_1?'':'rbs-none-arrow'?>">
					<span class="main-nav__text"><?=$arResult["ALL_ITEMS"][$idItem_1]['TEXT']?></span>
				</span>
				<?/*if($img):?>
					<img class="rbs-menu-img" src="<?=$img?>" alt="<?=$arResult["ALL_ITEMS"][$idItem_1]['TEXT']?>">
				<?endif*/?>
			</a>
			<?if($hasChildItem_1):?>
				<?$frame = $this->createFrame()->begin('')?>
					<ul class="inner-nav js-nav-hide inner-nav-inside">
						<?foreach($arItem_1 as $idItem_2=>$arItem_2):?>
						<?$img = &$arResult['ALL_ITEMS'][$idItem_2]['PARAMS']['PICTURE'];?>
						<?$hasChildItem_2 = is_array($arItem_2) && count($arItem_2) > 0;?>
						<!-- BEGIN ITEM -->
							<li class="inner-nav__item js-nav-item">
								<a class="inner-nav__link js-nav-link" href="<?=$hasChildItem_2 ? 'javascript:;' : $arResult["ALL_ITEMS"][$idItem_2]['LINK']?>">
									<span class="inner-nav__fix <?=$hasChildItem_2?'':'rbs-none-arrow'?>">
										<span class="inner-nav__image">
											<?/*?><img alt="<?=$arResult["ALL_ITEMS"][$idItem_2]['TEXT']?>" src="<?=$img?>"><?*/?>
										</span>
										<span class="inner-nav__text"><span><?=$arResult["ALL_ITEMS"][$idItem_2]['TEXT']?></span></span>
									</span>                                        	
								</a>
								<?if($hasChildItem_2):?>
									<ul class="inner-nav js-nav-hide inner-nav-inside">
										<?foreach($arItem_2 as $idItem_3):?>
										<?$img = &$arResult['ALL_ITEMS'][$idItem_3]['PARAMS']['PICTURE'];?>
											<li class="inner-nav__item">
												<a class="inner-nav__link" href="<?=$arResult["ALL_ITEMS"][$idItem_3]['LINK']?>">
													<span class="inner-nav__fix rbs-none-arrow">
														<span class="inner-nav__image">
															<?/*?><img alt="<?=$arResult["ALL_ITEMS"][$idItem_3]['TEXT']?>" src="<?=$img?>"><?*/?>
														</span>
														<span class="inner-nav__text"><span><?=$arResult["ALL_ITEMS"][$idItem_3]['TEXT']?></span></span>
													</span>                                                 
												</a>                                            
											</li> 
										<?endforeach;?>                                                                                                                                                           
									</ul>
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