<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
use Yenisite\Core\Tools;
use Bitronic2\Mobile;

CModule::IncludeModule('yenisite.core');
global $arPagination;
$bPaginationSecond = true;	//Vlad custom
if ($bPaginationSecond) {
	
	switch ($rz_b2_options['pagination_type']) {
		case 'inf-button':
			?>
			<script type="text/javascript">
				RZB2.ajax.params.inf_button = true;
			</script>
		<?
		case 'inf':
			?>
			<!-- Vlad custom start -->
			<script>
				//$("div.box-paging:not(:last)").hide();
				//var NotFixCatalog = $('.catalog').filter(function() { return $(this).parents('.clearfix').length == 0; });
				//NotFixCatalog.wrapAll('<div class="clearfix">');
				$(".js-seo-more-rbs").on("click", function () {
					if ($(this).hasClass("active")) {
						$(this).removeClass("active").closest(".js-seo").find(".js-seo-content").removeClass("opened");
					} else {
						$(this).addClass("active").closest(".js-seo").find(".js-seo-content").addClass("opened");
					}
				});
			</script>
			<!-- Vlad  custom  end -->
			<div class="more-catalog-wrap">
				<? $isDisabled = $arPagination['PAGEN'] >= $arPagination['END_PAGE'];?>
				<a <?= ($isDisabled) ? '' : '' ?>
					<?// href="' . $APPLICATION->GetCurPageParam('PAGEN_' . $arPagination['NUM'] . '=' . ($arPagination['PAGEN'] + 1), array('PAGEN_' . $arPagination['NUM'])) . '"?>
				class="more-catalog<?= ($isDisabled) ? ' disabled' : '' ?>"
				data-pagen-key="<?= 'PAGEN_' . $arPagination['NUM'] ?>" data-page="<?=  $arPagination['PAGEN'] + 1 ?>">
				<span class="btn-plus">+</span>
				<? if (!$isDisabled): ?>
					<?$page_diff = $arPagination['COUNT'] - $arPagination['SELECT'];?>
					<span class="text"><?= GetMessage("BITRONIC2_CATALOG_POKAZAT_ESHE") ?> <?=$page_diff < $page_count ? $page_diff : $page_count ?></span>
				<? endif ?>
				</a>
				<span class="text"><?= GetMessage("BITRONIC2_CATALOG_POKAZANO") ?> <?= $arPagination['SELECT'] ?>
					<?= Tools::rusQuantity($arPagination['SELECT'], GetMessage('BITRONIC2_CATALOG_TOVAR')), GetMessage("BITRONIC2_CATALOG_IZ") ?>
					<?= $arPagination['COUNT'] ?>
				</span>
			</div>
			<script type="text/javascript">
				if (!('inf_button' in RZB2.ajax.params)) {
					jQuery(window).load(function(){
						require(['back-end/inf_scroll'], function(){
							$('.more-catalog-wrap').RMZinfScroll(
								function () {
									var $this = $('.more-catalog');
									if (!$this.hasClass('disabled')) {
										var params = {};
										params[$this.attr('data-pagen-key')] = $this.attr('data-page');
										params['MORE_CLICK'] = 1;
										return RZB2.ajax.CatalogSection.Start($this.find('.btn-plus'), params);
									}
								},
								{ paddingTop: $('.more-catalog-wrap').outerHeight(true) + 50 }
							);
						});
					});
				}
			</script>
			<?
			break;
		case 'default':
		default:
            //include 'page_count.php';
            $APPLICATION->ShowViewContent('catalog_paginator');
	}
}
else
{
    //include 'page_count.php';
    $APPLICATION->ShowViewContent('catalog_paginator');
}
$bPaginationSecond = true;