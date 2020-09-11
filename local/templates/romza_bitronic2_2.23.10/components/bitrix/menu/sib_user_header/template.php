<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
if(method_exists($this, 'setFrameMode')) $this->setFrameMode(true);
// echo "<pre style='text-align:left;'>"; print_r($arResult); echo "</pre>";

$curPage = $APPLICATION->GetCurPage();
?>
<ul class="login-nav">
	<? foreach ($arResult as $key => $arItem):
		$liClass = $arItem['PARAMS']['ITEM_CLASS'];
		?>
		<li class="login-nav__item">
			<a class="login-nav__link" href="<?=$arItem['LINK']?>">
				<span><?=$arItem['TEXT']?></span>
			</a>
		</li>
	<? endforeach ?>                                                                  
</ul>