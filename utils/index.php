<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
global $USER; if(!$USER->IsAdmin()){LocalRedirect('/');}
$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/new_css/style_1.css");
?>
<div class="main-block">
    <ul>
        <li><a href="/utils/region_auto.php">Заполнение региона</a></li>
        <li><a href="/utils/regions_linked_menu.php">Привязка верхнее меню</a></li>
        <li><a href="/utils/regions_linked_sprod_intergation.php">Фильтр регионов Б24</a></li>
        <li><a href="/utils/price_types.php">Типы цен</a></li>
        <li><a href="/utils/edost_copy.php">Копирование едост тарифа в регионах</a></li>
    </ul>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>