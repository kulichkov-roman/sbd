<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

if ($arParams['INCLUDE_SESSION_ARRAY_IN_CACHE'] == 'N' && $_SESSION['VREGIONS_REGION']['NAME']){
	?>
	<script>
		var regionNameEl = $('.js-vr-template__link-region-name');
		regionNameEl.text('<?=$_SESSION['VREGIONS_REGION']['NAME'];?>');
		regionNameEl.fadeIn(1000);
	</script>
<? }
