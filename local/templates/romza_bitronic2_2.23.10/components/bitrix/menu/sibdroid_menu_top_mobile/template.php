<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<?
if (method_exists($this, 'setFrameMode')) $this->setFrameMode(true);
// echo "<pre style='text-align:left;'>"; print_r($arResult); echo "</pre>";
//The template is never cached
//include $_SERVER["DOCUMENT_ROOT"].SITE_TEMPLATE_PATH.'/include/debug_info_dynamic.php';
$curPage = $APPLICATION->GetCurPage();
?>
<ul itemscope itemtype="http://www.schema.org/SiteNavigationElement" class="main-nav__pages">
    <?
    foreach ($arResult as $key => $arItem):?>
        <?
        if ($arItem['DEPTH_LEVEL'] > 1) continue;
        $class = '';
        $bParent = false;
        $arItem['ADDITIONAL_LINKS'] = array();
        if ($arItem['IS_PARENT']) {
            $k = $key + 1;
            while (($arSubItem = $arResult[$k]) && is_array($arSubItem) && ($arSubItem['DEPTH_LEVEL'] > 1)) {
                $arItem['ADDITIONAL_LINKS'][$arSubItem['TEXT']] = $arSubItem['LINK'];
                $k++;
            }
        }
        if ($arItem['SELECTED']) $class .= ' active';

        ?>
        <li class="main-nav__item js-nav-item">
            <a href="<?= $arItem['LINK'] ?>" <?if($arItem['PARAMS']['TARGET'] == 'BLANK'):?>target="_blank"<?endif?> class="main-nav__link js-nav-link">
                <span class="main-nav__fix">
                    <span class="main-nav__text"><?= $arItem['TEXT'] ?></span>
                </span>
            </a>
        </li>
    <? endforeach ?>
</ul>