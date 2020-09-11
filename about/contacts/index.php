<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
LocalRedirect('/contacts/', '301 Moved Permanently');
$APPLICATION->SetTitle("Контакты");
?><main class="container about-page">
<h3 class="col-xs-12"> </h3>
<h1><?$APPLICATION->ShowTitle()?></h1>
<p>
 <b>Адрес:</b> г. Новосибирск, ул. Новогодняя&nbsp;17,&nbsp;вход со стороны дороги
</p>
<p>
 <b>Режим работы:</b> пн-вс с 10:00 по 19-00 <br>
 <b>Телефон отдела продаж : </b>8(383) 383-00-55
<b>Телефон курьерской службы:</b>8(999) 465-59-58</p>

 <b>ИП Зуев П.C.</b> <b>ИНН: 540863231129 &nbsp;</b> <b>ОГРНИП: 316547600073112</b> <br>
 <b>ООО "Топ Гаджет"&nbsp;ИНН: 5408004674 КПП: 540801001&nbsp;ОКПО 53880691&nbsp;ОГРН 1155476083058</b><br>
 <b>Расчетный счет: 40702810404270000736&nbsp;Банк: Ф ОНЛАЙН ПАО "ХАНТЫ-МАНСИЙСКИЙ БАНК ОТКРЫТИЕ" &nbsp;БИК 044583999&nbsp;Корреспондентский счет: 30101810600000000999<br>
 </b>
<p>
</p>
<p>
     <?$APPLICATION->IncludeComponent(
    "bitrix:map.yandex.view",
    ".default",
    Array(
        "COMPONENT_TEMPLATE" => ".default",
        "CONTROLS" => array(0=>"ZOOM",1=>"SMALLZOOM",2=>"MINIMAP",3=>"TYPECONTROL",4=>"SCALELINE",),
        "INIT_MAP_TYPE" => "MAP",
        "MAP_DATA" => "a:4:{s:10:\"yandex_lat\";d:54.982984898741016;s:10:\"yandex_lon\";d:82.90147354730514;s:12:\"yandex_scale\";i:16;s:10:\"PLACEMARKS\";a:1:{i:0;a:3:{s:3:\"LON\";d:82.903854012736;s:3:\"LAT\";d:54.983338661376;s:4:\"TEXT\";s:11:\"Sibdroid.ru\";}}}",
        "MAP_HEIGHT" => "500",
        "MAP_ID" => "",
        "MAP_WIDTH" => "AUTO",
        "OPTIONS" => array(0=>"ENABLE_SCROLL_ZOOM",1=>"ENABLE_DBLCLICK_ZOOM",2=>"ENABLE_RIGHT_MAGNIFIER",3=>"ENABLE_DRAGGING",)
    )
);?>
</p>
 <?$APPLICATION->IncludeComponent(
    "bitrix:map.google.view",
    ".default",
    Array(
        "COMPONENT_TEMPLATE" => ".default",
        "CONTROLS" => array(0=>"SMALL_ZOOM_CONTROL",1=>"TYPECONTROL",2=>"SCALELINE",),
        "INIT_MAP_TYPE" => "ROADMAP",
        "MAP_DATA" => "a:4:{s:10:\"google_lat\";d:54.982976493626644;s:10:\"google_lon\";d:82.90148319244383;s:12:\"google_scale\";i:16;s:10:\"PLACEMARKS\";a:1:{i:0;a:3:{s:4:\"TEXT\";s:11:\"Sibdroid.ru\";s:3:\"LON\";d:82.9039478302002;s:3:\"LAT\";d:54.98335180915187;}}}",
        "MAP_HEIGHT" => "500",
        "MAP_ID" => "",
        "MAP_WIDTH" => "AUTO",
        "OPTIONS" => array(0=>"ENABLE_SCROLL_ZOOM",1=>"ENABLE_DBLCLICK_ZOOM",2=>"ENABLE_DRAGGING",3=>"ENABLE_KEYBOARD",)
    )
);?><br>
 <br>
 &nbsp;<?if (CModule::IncludeModule('simai.maps2gis')): ?><br>
<p>
     <?$APPLICATION->IncludeComponent(
    "simai:maps.2gis.simple",
    "",
    Array(
        "MAP_WIDTH" => "AUTO"
    )
);?>
</p>
 <?endif?> </main><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php")?>