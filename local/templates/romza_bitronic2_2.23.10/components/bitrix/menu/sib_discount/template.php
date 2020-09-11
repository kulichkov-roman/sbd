<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?
if (method_exists($this, 'setFrameMode')) $this->setFrameMode(false);
include $_SERVER["DOCUMENT_ROOT"].SITE_TEMPLATE_PATH.'/include/debug_info_dynamic.php';
$cntEls = 0;
?>
<ul class="category-list">
    <?foreach($arResult as $key => $arItem):?>
        <li class="category-list__item">
            <a href="<?=$arItem['LINK']?>" class="category-list__link"><?=$arItem['TEXT']?> <span data-count-id="<?=$arItem['PARAMS']['ID']?>"><?=$cntEls?></span></a>
        </li>
    <?endforeach?>
</ul>
<script>
    $.ajax({
        url: '/ajax/sib/menu_spec_discount_count.php',
        method: 'post',
        dataType: 'json',
        success: function(data){
            for(i in data){
                $('[data-count-id="'+i+'"]').text(data[i]);
            }
        }
    });
</script>