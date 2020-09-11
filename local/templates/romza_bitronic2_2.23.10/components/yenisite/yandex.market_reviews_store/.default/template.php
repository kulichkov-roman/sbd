<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
if (method_exists($this, 'setFrameMode')) $this->setFrameMode(true);
?>
<div class="feedback wow fadeIn hidden-xs">
    <div class="container carousel slide" id="comments-carousel">
        <div class="quote-start"></div>
        <div class="quote-end"></div>
        <header><?=GetMessage("RZ_OTZIVI_POKUPATELEJ")?></header>
        <div class="slider-controls carousel-indicators"></div>
        <div class="comments carousel-inner" id="main-shop-reviews"></div><!-- /.comments -->
    </div><!-- /.container -->
</div><!-- /.feedback -->
<script type="text/javascript">
    $(document).ready(function () {
        updateYRMS(1, '<?=$templateFolder?>', '<?=$arParams["COUNT"]?>', '<?=$APPLICATION->GetCurPage(true)?>');
    })
</script>
