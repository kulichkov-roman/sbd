<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>


<?
// предзагрузка скрипта для быстрого открытия карты с пунктами выдачи (но при этом сама страница оформления заказа будет загружаться дольше!!!)
//$APPLICATION->AddHeadString('<script type="text/javascript" src="https://api-maps.yandex.ru/2.0-stable/?load=package.standard,package.clusters&lang=ru-RU"></script>');
?>


<? /* ==================== edost.locations (выбор местоположений) */ ?>
<? if (!empty($arResult['edost']['locations_installed'])) { ?>
    <style>
        /* перебивание глобальных стилей шаблона Visual (больших полей) !!! */
        #edost_location_city_div input.edost_city { width: 100% !important; max-width: 600px !important; }
        #edost_location_city_div input.edost_city, #edost_location_city_zip_div input.edost_input, #edost_location_city_div input.edost_input, #edost_location_address_div input.edost_input { height: 25px !important; padding: 2px 4px !important; margin-bottom: 0 !important; vertical-align: baseline; }
    </style>

    <div class="edost_main edost_template_div">
        <h4 class="edost_location_head">Местоположение доставки</h4>
        <?
        $delivery_id = $arResult['USER_VALS']['DELIVERY_ID'];
        $warning = (!empty($arResult['edost']['format']['warning']) || isset($arResult['edost']['warning']) ? true : false);
        $address_shop = 'N'; // если указано любое значение отличное от 'N', тогда поле для ввода адреса отключается и в заказе сохраняется указанный адрес
        $address_hide = (!empty($arResult['edost']['address_hide']) || $delivery_id == '' ? true : false); // генерируется при активном тарифе с доставкой до пункта выдачи модуля edost.delivery (НЕ менять!!!)
        /*
            // собственные тарифы "Самовывоз" магазина, для которых требуется отключение ввода адреса и замена адреса на заданное значение
            $address_tariff = array(
                'edost:61' => 'магазин: ул. Кутузовская, д. 20, телефон: +7-123-456-78-90',
            );
            if (isset($address_tariff[$delivery_id])) {
                $address_hide = true;
                $address_shop = $address_tariff[$delivery_id];
            }
        */

                    // собственные тарифы "Самовывоз" магазина, для которых требуется отключение ввода адреса и замена адреса на заданное значение

                    if ($_SESSION['VREGIONS_REGION']['ID'] == 14646){
                        $address_tariff = array(
                            '348' => 'магазин: г. Москва, Багратионовский проезд 7 корпус 3 ТК Горбушкин Двор пав. h2-003; Режим работы: пн-вс с 10:00 до 21:00; +7(495) 120-95-00',
                        );
                    } else {
                        $address_tariff = array(
                            '348' => 'магазин: г. Новосибирск, ул. Новогодняя, д. 17, телефон: +7(383)383-00-55',
                        );
                    }
        
        
        $edost_locations_param = array(
            'ID' => $arResult['USER_VALS']['DELIVERY_LOCATION'],
            'DELIVERY_ID' => $delivery_id,
            'PROP' => $arResult['edost']['order_prop'],
            'PROP2' => $arResult['edost']['order_prop2'],

            'PARAM' => array(
//          'edost_delivery' => false, // false - отключить использование модуля edost.delivery (перейти на выбор местоположений через выпадающие списки), только для быстрой проверки - для постоянного использования требуется включение блокировки в константах !!!
                'input' => (!empty($arResult['edost']['location_input']) ? $arResult['edost']['location_input'] : 0), // ID местоположения с которого включится режим выбора (если не задано или равно "0", тогда модуль работает в автоматическом режиме)
                'zip_in_city' => (isset($arResult['edost']['order_prop']['ZIP']) && ($arResult['edost']['order_prop']['ZIP']['value'] === '' || $warning && $address_hide) ? true : false), // true - выводить индекс в блоке с местоположением
                'address' => $address_shop, // присвоить собственный адрес самовывоза ('N' - стандартная работа)
//          'loading' => 'loading_small_f2.gif', // иконка загрузки при расчете доставки и проверке индекса - лежит в папке bitrix/components/edost/locations/images/
                /*
                            // предупреждения (если указаны, тогда заменяют значения по умолчанию)
                            'zip_warning' => array(
                                1 => 'Такого индекса НЕ существует!2222',
                                2 => 'Индекс НЕ соответствует региону!',
                                'digit' => 'Должны быть только цифры!',
                            ),
                */
                // модификация полей адреса (если указаны, тогда заменяют значения по умолчанию)
                'address_field' => array(
                    /*
                                    'city2' => array(
                                        'name' => 'Населенный пункт', // название поля
                                        'width' => 200, // длина поля в пикселях
                                        'max' => 50, // допустимое количество символов
                                        'enter' => true, // true - добавить перед полем 'ввод'
                                    ),
                                    'street' => array(
                                        'delimiter' => true, // установить после поля разделитель
                                        'delimiter_style' => 'width: 20px', // собственный стиль для разделителя
                                    ),
                    //              'house_1', 'house_2', 'house_3', 'house_4', 'door_1', 'door_2'
                    */
                    // ограничения по выводу полей
                    'city2_required' => array(
//                  'value' => 'Y', // '' - не обязательно, 'Y' - обязательно (по умолчанию)
                    ),
                    'street_required' => array(
//                  'value' => '', // '' - не обязательно, 'Y' - обязательно, 'A' - работа с модулем edost.delivery с обязательным выбором улицы из списка подсказок для городов с отдаленными районами (по умолчанию)
                    ),
                    'zip_required' => array(
//                  'value' => (in_array($delivery_id, array(10, 20)) ? 'Y' : ''), // '' - поле не выводится, 'S' - поле выводится, 'Y' - должно быть обязательно заполнено, 'A' - работа с модулем edost.delivery (по умолчанию)
                    ),
                    'metro_required' => array(
//                  'value' => (in_array($delivery_id, array(10, 20)) ? 'S' : ''), // '' - поле не выводится, 'S' - поле выводится, 'A' - работа с модулем edost.delivery (по умолчанию)
                    ),
                ),
            ),
        );
        ?>
	    <?
	    // подстановка местоположения из шапки
        $edost_locations_param['ID'] = $_SESSION['VREGIONS_IM_LOCATION']['ID'];
	    ?>
        <? $GLOBALS['APPLICATION']->IncludeComponent('edost:locations', '', array('MODE' => 'city') + $edost_locations_param, null, array('HIDE_ICONS' => 'Y')); ?>
    </div>
<? } ?>
<? /* ==================== edost.locations */ ?>




<? /* шаблон edost - НАЧАЛО */ ?>
<? if (isset($arResult['edost']['format'])) { ?>
    <style>
        /* модификация для bitronic */
        div.edost input.edost_format_radio { display: inline; }
        #edost_office_div { max-width: 800px; }
    </style>
    <?
    $edost_catalogdelivery = false;
    $data = (isset($arResult['edost']) ? $arResult['edost'] : false);
    $sign = GetMessage('EDOST_DELIVERY_SIGN');
    $table_width = 645;
    $ico_path = '/bitrix/images/delivery_edost_img';
    $ico_loading = '<img style="vertical-align: middle;" src="'.$ico_path.'/loading_small.gif" width="20" height="20" border="0">'; // иконка загрузки
//  $ico_loading_map_inside = '<div class="edost_map_loading"><img src="'.$ico_path.'/loading_small.gif" border="0" width="64" height="64"></div>'; // иконка загрузки для интегрированной карты

    if ($edost_catalogdelivery) $ico_default = (!empty($param['ico_default']) ? $param['ico_default'] : $arResult['component_path'].'/images/logo-default-d.gif');
    else $ico_default = $templateFolder.'/images/logo-default-d.gif';

    if (!empty($data['javascript'])) echo $data['javascript'];
    if (!empty($data['format']['warning']) && empty($arResult['edost']['locations_installed'])) echo '<div class="edost_warning edost_warning_big">'.$data['format']['warning'].'</div>'.'<br>';
    $map_inside = (!$edost_catalogdelivery && empty($data['map_inside']) || empty($data['format']['map_inside']) || $data['format']['map_inside'] == 'N' ? '' : $data['format']['map_inside']);

    // собственное описание для групп пунктов выдачи
//  $sign['office_description']['shop'] = ''; // адреса магазинов
//  $sign['office_description']['office'] = ''; // пункты выдачи
//  $sign['office_description']['terminal'] = ''; // терминалы
    ?>

    <? /*
<style>
    div.edost_office_window_fon { z-index: 1500 !important; }
    div.edost_office_window { z-index: 1501 !important; }
    div.edost_catalogdelivery_window_fon { z-index: 1490 !important; }
    div.edost_catalogdelivery_window { z-index: 1491 !important; }
</style>
*/ ?>

    <script type="text/javascript">
        function edost_SetOffice(profile, id, cod, mode) {

            if (id == undefined) {
                <?          if (!$edost_catalogdelivery) { ?>
                var E = document.getElementById('edost_delivery_id');
                if (E) if (E.value != 'edost:' + profile) submitForm();
                <?          } ?>
                return;
            }

            if (window.edost_office && edost_office.map && edost_office.map.balloon) {
                edost_office.map.balloon.close();
                edost_office.map = false;
                edost_office.window('close');
            }
            if (window.edost_office2 && edost_office2.map && edost_office2.map.balloon) {
                edost_office2.map.balloon.close();
                edost_office2.map = false;
            }

            <?      if (!$edost_catalogdelivery) { ?>
            var E = document.getElementById('edost_address_' + mode);
            if (E) E.style.display = 'none';

            var E = document.getElementById('edost_office_inside');
            if (E) E.style.display = 'none';
            var E = document.getElementById('edost_office_detailed');
            if (E) E.innerHTML = '<br>';

            var E = document.getElementById('edost_address_' + mode + '_loading');
            if (E) E.innerHTML = '<?=$ico_loading?> <span class="edost_description"><?=$sign['loading']?></span>';

            var ar = document.getElementsByName('DELIVERY_ID');
            for (var i = 0; i < ar.length; i++) if (ar[i].id == 'ID_DELIVERY_edost_' + mode) {
                ar[i].value = 'edost' + ':' + profile + ':' + id + (cod != '' ? ':' + cod : '');
                ar[i].checked = true;
                break;
            }

            submitForm();
            <?      } else { ?>
            edost_catalogdelivery.calculate('loading');
            BX.ajax.post('<?=$arResult['component_path']?>/edost_catalogdelivery.php', 'set_office=Y&id=' + id + '&profile=' + profile + '&cod=' + cod + '&mode=' + mode, function(r) {
                edost_catalogdelivery.calculate();
            });
            <?      } ?>

        }

        function edost_MapInside() {

            <?      if ($edost_catalogdelivery) { ?>
            edost_RunScript('map_inside');
            <?      } else { ?>
            if (!window.edost_office2) return;
            var E = document.getElementById('edost_office_inside');
            if (!E) return;
            var E = document.getElementById('edost_office_inside_head');
            if (E) return;
            var E = document.getElementById('edost_office_div');
            if (E && E.style.display != 'none') {
                if (window.edost_office) edost_office.map = false;
                edost_office2.map = false;
                edost_office2.window('inside');
            }
            <?      } ?>

        }

        function edost_SetBookmark(id, bookmark) {

            var start = false;
            if (bookmark == undefined) bookmark = '';
            if (id == 'start') {
                start = true;
                E2 = document.getElementById('edost_bookmark');
                if (E2) id = E2.value;
                if (id == '') return;
            }

            var ar = ['office', 'door', 'house', 'post', 'general', 'show'];
            for (var i = 0; i < ar.length; i++) {
                var E = document.getElementById('edost_' + ar[i] + '_div');
                var E2 = document.getElementById('edost_' + ar[i] + '_td');
                if (!E && !E2) continue;

                var E3 = document.getElementById('edost_' + ar[i] + '_td_bottom');

                var show = (ar[i] == id ? true : false);

                if (E2) E2.className = 'edost_active_' + (show ? 'on' : 'off');
                if (E3) E3.className = 'edost_active_fon_' + (show ? 'on' : 'off');
                <?          if (!$edost_catalogdelivery) { ?>
                if (E)
                    if (!start) E.style.display = 'none';
                    else if (bookmark == 1) E.style.display = (show ? 'block' : 'none');
                <?          } else { ?>
                if (E) E.style.display = (show ? 'block' : 'none');
                <?          } ?>
            }

            var E = document.getElementById('edost_bookmark_delimiter');
            if (E) E.className = 'edost_active_fon_on';

            if (!start) {
                var E = document.getElementById('edost_bookmark_loading');
                if (E) {
                    E.innerHTML = '<?=$ico_loading?> <span class="edost_description"><?=$sign['loading2']?></span>';
                    E.style.display = 'block';
                }

                var E = document.getElementById('edost_bookmark_info');
                if (E) E.style.display = 'none';

                E = document.getElementById('edost_bookmark');
                if (E) E.value = id + '_s';

                <?          if (!$edost_catalogdelivery) { ?>
                submitForm();
                <?          } ?>
            }

            <?      if ($edost_catalogdelivery && $map_inside == 'Y') { ?>
            if (id == 'office') edost_MapInside();
            <?      } ?>

            <?      if ($edost_catalogdelivery && $mode != 'manual') { ?>
            edost_catalogdelivery.position('update');
            <?      } ?>
        }

        <? if (!$edost_catalogdelivery && !empty($data['map_inside'])) { ?>
        if (window.edost_office2 && edost_office2.timer_inside == false) {
            edost_office2.timer_inside = window.setInterval('edost_MapInside()', 500);
        }
        <? } ?>
    </script>

    <? if (!empty($data['format']['data'])) { ?>
        <div class="edost edost_main<?=(!$edost_catalogdelivery ? ' edost_template_div' : '')?>">

            <?
            $border = (!empty($data['format']['border']) ? true : false);
            $cod = (!empty($data['format']['cod']) ? true : false);
            $cod_bookmark = (!empty($data['format']['cod_bookmark']) ? true : false);
            $top = ($border ? 15 : 40);
            $hide_radio = ($data['format']['count'] == 1 ? true : false);
            $cod_tariff = (!empty($data['format']['cod_tariff']) ? true : false);

            if ($data['format']['priceinfo']) $table_width = 645;
            else $table_width = ($data['format']['day'] ? 620 : 570);

            $day_width = ($data['format']['day'] ? 80 : 10);
            $price_width = 85;
            $cod_width = 90;

            $bookmark = (!empty($data['format']['bookmark']) ? $data['format']['bookmark'] : '');
            $bookmark_id = (!empty($data['format']['active']['bookmark']) ? $data['format']['active']['bookmark'] : '');

            if ($cod_tariff) {
                $sign['price_head'] = '<span class="edost_payment_normal">'.str_replace('<br>', ' ', $sign['price_head']).'</span>';
                $sign['cod_head'] = '<span class="edost_payment_cod">'.str_replace('<br>', ' ', $sign['cod_head']).'</span>';
            }
            ?>


            <? if (!$edost_catalogdelivery) { ?>
                <h4><?=($bookmark != '' ? 'Способ доставки' : GetMessage('SOA_TEMPL_DELIVERY'))?></h4>
            <? } ?>


            <?  if ($bookmark != '') { ?>
                <div id="edost_bookmark_div">
                    <input id="edost_bookmark" name="edost_bookmark" value="<?=$bookmark_id?>" type="hidden">
                    <table class="edost_bookmark" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <?      foreach ($data['format']['data'] as $f_key => $f) if ($bookmark !== 2 || $f_key !== 'general') { $id = $f_key; ?>
                                <td id="edost_<?=$id?>_td" class="edost_active" width="110" style="padding-bottom: 5px;" onclick="edost_SetBookmark('<?=$id?>')">
                                    <img src="<?=$ico_path.'/'.$f_key.'.gif'?>" border="0">
                                    <br>
                                    <span class="edost_bookmark"><?=$f['head']?></span>
                                    <br>
                                    <?          if ($f_key != 'show') { ?>
                                        <div>
                                            <?                  if (isset($f['free']) || isset($f['min']['free'])) { ?>
                                                <span class="edost_format_price edost_price_free" style=""><?=(isset($f['free']) ? $f['free'] : $f['min']['free'])?></span>
                                            <?                  } else if (isset($f['price_formatted']) || isset($f['min']['price_formatted'])) { ?>
                                                <span class="edost_format_price edost_price"<?=(isset($f['price_formatted']) ? ' style="color: #888;"' : '')?>><?=(isset($f['price_formatted']) ? $f['price_formatted'] : $f['min']['price_formatted'])?></span>
                                            <?                  } ?>

                                            <?                  if (!empty($f['min']['day'])) { ?>
                                                <br><span class="edost_format_price edost_day"><?=(!empty($f['min']['day']) ? $f['min']['day'] : '')?></span>
                                            <?                  } ?>

                                            <?                  if ($cod_bookmark && ($bookmark == 1 && $f['cod'] || $bookmark == 2 && (!$cod_tariff && isset($f['min']['pricecash']) || $f['min']['cod_tariff']))) { ?>
                                                <br><span class="edost_price_head edost_payment"><?=$sign['cod_head_bookmark']?></span>
                                            <?                  } ?>
                                        </div>
                                    <?          } ?>
                                </td>
                                <td width="25"></td>
                            <?      } ?>
                        </tr>
                        <?      if ($bookmark == 1) { ?>
                            <tr>
                                <?          foreach ($data['format']['data'] as $f_key => $f) { $id = $f_key; ?>
                                    <td id="edost_<?=$id?>_td_bottom" style="height: 10px;"></td>
                                    <td></td>
                                <?          } ?>
                            </tr>
                            <tr>
                                <td id="edost_bookmark_delimiter" colspan="10" style="height: 5px;"></td>
                            </tr>
                        <?      } ?>
                    </table>
                    <?  if (!$edost_catalogdelivery) { ?>
                        <div id="edost_bookmark_loading" style="padding-top: 20px; display: none;"></div>
                    <?  } ?>
                    <?  if ($bookmark_id == 'show') echo '<div style="height: 20px;"></div>'; ?>
                </div>
            <?  } ?>


            <?
            if ($bookmark == 2 && $bookmark_id != '' && $bookmark_id != 'show') foreach ($data['format']['data'] as $f_key => $f) if (!empty($f['tariff'])) foreach ($f['tariff'] as $v) if (!empty($v['checked'])) {
                $description = array();
                if (!empty($f['description'])) $description[] = $f['description'];
                if (!empty($v['description'])) $description[] = $v['description'];

                $warning = array();
                if (!empty($f['warning'])) $warning[] = $f['warning'];
                if (!empty($v['error'])) $warning[] = $v['error'];
                if (!empty($v['warning'])) $warning[] = $v['warning'];

                if (!empty($description) || !empty($warning) || !empty($v['office_address'])) {
                    echo '<div id="edost_bookmark_info" style="margin-top: 15px; padding: 12px 12px 0 12px; border-color: #DD8; border-style: solid; border-width: 1px 0; background: #FFD;">';
                    ?>
                    <?          if (!empty($v['office_address'])) { ?>
                        <div style="padding-bottom: 12px;">
                            <span class="edost_format_address_head"><?=$sign['address2']?>: </span>
                            <span class="edost_format_address"><?=$v['office_address']?></span>
                            <a class="edost_link" href="http://www.edost.ru/office.php?c=<?=$v['office_id']?>" target="_blank"><?=$sign['map']?></a>
                        </div>
                    <?          } ?>
                    <?
                    if (!empty($warning)) echo '<div class="edost_warning edost_format_info">'.implode('<br>', $warning).'</div>';
                    if (!empty($description)) echo '<div class="edost_format_info">'.implode('<br>', $description).'</div>';
                    echo '</div>';
                }
            }
            ?>


            <div id="edost_tariff_div">
                <?
                $i = 0;
                foreach ($data['format']['data'] as $f_key => $f) if (!empty($f['tariff'])) {
                    $display = ($bookmark == 1 && $bookmark_id != $f_key || $bookmark == 2 && $bookmark_id != 'show' ? ' display: none;' : '');
                    $map = ($map_inside == 'Y' && $f_key == 'office' ? true : false);
                    $cod_td = ($cod && ($f['cod'] || $border) ? true : false);

                    if ($map) $w = '100%';
                    else $w = ($table_width - ($cod_td ? 0 : $cod_width)).'px';
                    ?>
                    <div id="edost_<?=$f_key?>_div" class="<?=(!$border || $f['head'] == '' ? 'edost_format' : 'edost_format_border')?>" style="width: <?=$w?>; margin: <?=($i != 0 && $bookmark != 1 ? $top.'px' : '0')?> 0 0 0;<?=$display?>">
                        <?
                        $i++;

                        if ($bookmark == 1) $head = '';
                        else $head = ($f['head'] != '' ? '<div class="edost_format_head">'.$f['head'].':'.'</div>' : '');

                        if ($bookmark == 1 && !$map) echo '<div style="height: 8px;"></div>';

                        if ($cod && $f['cod'] && !$map) {
                            echo '<table class="edost_format_head" width="100%" cellpadding="0" cellspacing="0" border="0"><tr>';
                            echo '<td>'.($head != '' ? $head : '&nbsp;').'</td>';
                            echo '<td class="edost_format_head" width="'.$price_width.'"><span class="edost_price_head edost_price_head_color">'.$sign['price_head'].'</span></td>';
                            echo '<td class="edost_format_head" width="'.$cod_width.'"><span class="edost_price_head edost_payment">'.$sign['cod_head'].'</span></td>';
                            echo '</tr></table>';
                            echo '<div style="padding: 8px 0 0 0;"></div>';
                        }
                        else if ($head != '') {
                            echo $head.'<div style="padding: 8px 0 0 0;"></div>';
                            echo '<div style="padding: 3px 0 0 0;"></div>';
                        }

                        if ($map) {
                            echo '<div id="edost_office_inside" class="edost_office_inside" style="height: 450px;"></div>';
                            echo '<div id="edost_office_detailed" class="edost_office_detailed"><span class="edost_format_link_big" onclick="edost_office.window(\'all\');">'.$sign['detailed_office'].'</span></div>';
                        }

                        if ($f['warning'] != '') echo '<div class="edost_warning edost_format_info">'.$f['warning'].'</div>';
                        if ($f['description'] != '') echo '<div class="edost_format_info">'.$f['description'].'</div>';
                        if ($f['insurance'] != '') echo '<div class="edost_format_info"><span class="edost_insurance">'.$f['insurance'].'</span></div>';

                        $i2 = 0;
                        foreach ($f['tariff'] as $v) {
                            if (isset($v['delimiter'])) {
                                echo '<div class="edost_delimiter edost_delimiter_mb'.($edost_catalogdelivery ? '2' : '').'"></div>';
                                $i2 = 0;
                                continue;
                            }

                            if ($i2 != 0 && ($map_inside == '' || $f_key != 'office')) echo '<div class="edost_delimiter edost_delimiter_ms'.($edost_catalogdelivery ? '2' : '').'"></div>';
                            $i2++;

                            $id = 'ID_DELIVERY_'.$v['html_id'];
                            $value = $v['html_value'];
                            $office_map = (isset($v['office_map']) ? $v['office_map'] : '');
                            $onclick = ($office_map == 'get' ? "edost_office.window('".$v['office_mode']."', true);" : 'submitForm();');
                            $price_long = (isset($v['price_long']) ? $v['price_long'] : '');
                            $display = ($f_key == 'office' && ($map_inside == 'Y' || $map_inside == 'tariff' && empty($v['checked_inside'])) ? ' style="display: none;"' : '');

                            if (isset($v['ico']) && $v['ico'] !== '') $ico = (strlen($v['ico']) <= 3 ? $ico_path.'/'.$v['ico'].'.gif' : $v['ico']);
                            else $ico = (!empty($ico_default) ? $ico_default : false);

                            if (isset($v['office_mode']) && $office_map == 'get' && !empty($sign['office_description'][$v['office_mode']])) $v['description'] = $sign['office_description'][$v['office_mode']];
                            if (isset($v['office_mode'])) echo '<div id="edost_address_'.$v['office_mode'].'_loading"></div>';
                            ?>
                            <table class="edost_format_tariff" <?=($office_map != '' && isset($v['office_mode']) ? 'id="edost_address_'.$v['office_mode'].'"' : '')?> width="100%" cellpadding="0" cellspacing="0" border="0"<?=$display?>>
                                <tr>
                                    <td class="edost_format_ico" width="<?=($hide_radio || $edost_catalogdelivery ? '70' : '95')?>" rowspan="3">
                                        <?                  if (!$edost_catalogdelivery) { ?>
                                            <input class="edost_format_radio" <?=($hide_radio ? 'style="display: none;"' : '')?> type="radio" id="<?=$id?>" name="DELIVERY_ID" value="<?=$value?>" <?=(!empty($v['checked']) ? 'checked="checked"' : '')?> onclick="<?=$onclick?>">
                                        <?                  } ?>

                                        <?                  if ($ico !== false) { ?>
                                            <label class="edost_format_radio" for="<?=$id?>"><img class="edost_ico edost_ico_normal" src="<?=$ico?>" border="0"></label>
                                        <?                  } else { ?>
                                            <div class="edost_ico"></div>
                                        <?                  } ?>
                                    </td>

                                    <td class="edost_format_tariff">
                                        <label for="<?=$id?>">
                                            <span class="edost_format_tariff"><?=(isset($v['head']) ? $v['head'] : $v['company'])?></span>
                                            <?                  if ($v['name'] != '' && !isset($v['company_head'])) { ?>
                                                <span class="edost_format_name"> (<?=$v['name']?>)</span>
                                            <?                  } ?>

                                            <?                  if ($v['insurance'] != '' && (!$cod_tariff || empty($v['cod_tariff']))) { ?>
                                                <br><span class="edost_insurance"><?=$v['insurance']?></span>
                                            <?                  } ?>

                                            <?                  if ($cod_tariff && $office_map == 'get' && isset($v['pricecod']) && $v['pricecod'] >= 0) { ?>
                                                <br><span class="edost_price_head edost_payment"><?=str_replace('<br>', ' ', $sign['cod_head_bookmark'])?></span>
                                            <?                  } ?>

                                            <?                  if ($cod_tariff && $v['automatic'] == 'edost' && $v['profile'] != 0 && ($office_map == '' || !empty($v['office_address']))) { ?>
                                                <br><?=(empty($v['cod_tariff']) ? $sign['price_head'] : $sign['cod_head'])?>
                                            <?                  } ?>
                                        </label>

                                        <?                  if ($office_map == 'get') { ?>
                                            <br><span class="edost_format_link_big" onclick="edost_office.window('<?=($map_inside ? 'all' : $v['office_mode'])?>');"><?=$v['office_link']?></span>
                                        <?                  } ?>
                                    </td>

                                    <?              if (!isset($v['error'])) { ?>

                                        <?              if ($price_long === '') { ?>
                                            <td class="edost_format_price" width="<?=$day_width?>" align="center">
                                                <label for="<?=$id?>"><span class="edost_format_price edost_day"><?=(!empty($v['day']) ? $v['day'] : '')?></span></label>
                                            </td>
                                        <?              } ?>

                                        <td class="edost_format_price" width="<?=(($price_long != '' ? $day_width : 0) + $price_width)?>" align="right">
                                            <label for="<?=$id?>">
                                                <?                  if (isset($v['free'])) { ?>
                                                    <span class="edost_format_price edost_price_free" style="<?=($price_long == 'light' ? 'opacity: 0.5;' : '')?>"><?=$v['free']?></span>
                                                <?                  } else { ?>
                                                    <span class="edost_format_price edost_price" style="<?=($price_long == 'light' ? 'opacity: 0.5;' : '')?>"><?=(isset($v['priceinfo_formatted']) ? $v['priceinfo_formatted'] : $v['price_formatted'])?></span>
                                                <?                  } ?>
                                            </label>
                                        </td>

                                        <?              if ($cod_td) { ?>
                                            <td class="edost_format_price" width="<?=$cod_width?>" align="right">
                                                <?                  if (isset($v['pricecod']) && $v['pricecod'] >= 0) { ?>
                                                    <label for="<?=$id?>"><span class="edost_price_head edost_payment"><?=(isset($v['cod_free']) ? $v['cod_free'] : $v['pricecod_formatted'])?></span></label>
                                                <?                  } ?>
                                            </td>
                                        <?              } ?>

                                    <?              } ?>
                                </tr>

                                <?          if (isset($v['company_head'])) { ?>
                                    <tr>
                                        <td colspan="5"<?=($cod_tariff ? ' style="padding-top: 2px;"' : '')?>>
                                            <span class="edost_format_company_head"><?=$v['company_head']?>: </span>
                                            <span class="edost_format_company"><?=$v['company']?></span>
                                            <?=($v['name'] != '' ? '<span class="edost_format_company_name"> ('.$v['name'].')</span>' : '')?>
                                        </td>
                                    </tr>
                                <?          } ?>

                                <?          if (!empty($v['office_address'])) { ?>
                                    <tr>
                                        <td colspan="5"<?=($cod_tariff && $office_map != 'get' ? ' style="padding-top: 2px;"' : '')?>>
                                            <span class="edost_format_address_head"><?=$sign['address']?>: </span>
                                            <span class="edost_format_address"><?=$v['office_address']?></span>

                                            <?                  if ($office_map == 'change') { ?>
                                                <br><span class="edost_format_link" onclick="edost_office.window('<?=($map_inside ? 'all' : $v['office_mode'])?>');"><?=$v['office_link']?></span>
                                            <?                  } else { ?>
                                                <a class="edost_link" href="http://www.edost.ru/office.php?c=<?=$v['office_id']?>" target="_blank"><?=$v['office_link']?></a>
                                            <?                  } ?>
                                        </td>
                                    </tr>
                                <?          } ?>

                                <?          if (!empty($v['description']) || !empty($v['warning']) || !empty($v['error'])) { ?>
                                    <tr>
                                        <td colspan="5">
                                            <?                  if (!empty($v['error'])) { ?>
                                                <div class="edost_format_description edost_warning"><b><?=$v['error']?></b></div>
                                            <?                  } ?>

                                            <?                  if (!empty($v['warning'])) { ?>
                                                <div class="edost_format_description edost_warning"><?=$v['warning']?></div>
                                            <?                  } ?>

                                            <?                  if (!empty($v['description'])) { ?>
                                                <div class="edost_format_description edost_description"><?=nl2br($v['description'])?></div>
                                            <?                  } ?>
                                        </td>
                                    </tr>
                                <?          } ?>
                            </table>
                        <?      } ?>
                    </div>
                <?  } ?>
            </div>


            <?  if (!$edost_catalogdelivery) { ?>
                <input name="BUYER_STORE" id="BUYER_STORE" value="<?=$arResult["BUYER_STORE"]?>" type="hidden">
            <?  } ?>

            <?  if (!empty($data['format']['active']['id'])) { ?>
                <input id="edost_delivery_id" value="<?=$data['format']['active']['id']?>" type="hidden">
            <?  } ?>

            <?  if (!empty($data['format']['map_json'])) { ?>
                <input id="edost_office_data" value='{"ico_path": "<?=$ico_path?>", <?=$data['format']['map_json']?>}' type="hidden">
            <?  } ?>

            <?  if ($edost_catalogdelivery && $map_inside != '') { ?>
                <script type="text/javascript">
                    if (window.edost_office) edost_office.map = false;
                    if (window.edost_office2) edost_office2.map = false;
                    <?      if ($map_inside == 'Y' && $bookmark == '') { ?>
                    edost_MapInside();
                    <?      } ?>
                </script>
            <?  } ?>

            <? if (isset($ico_loading_map_inside)) { ?>
                <script type="text/javascript">
                    if (window.edost_office2) edost_office2.loading_inside = '<?=$ico_loading_map_inside?>';
                </script>
            <? } ?>

            <? if ($bookmark != '') { ?>
                <script type="text/javascript">
                    edost_SetBookmark('start', '<?=$bookmark?>');
                </script>
            <? } ?>

        </div>
    <? } ?>

<? } ?>
<? /* шаблон edost - КОНЕЦ */ ?>




<? /* шаблон bitronic (на базе 2.2) - НАЧАЛО */ ?>
<? if (!isset($arResult['edost']['format'])) { ?>

    <script type="text/javascript">
        function fShowStore(id, showImages, formWidth, siteId, del_id)
        {
            del_id = del_id || 0;
            var strUrl = '<?=$templateFolder?>' + '/map.php';
            var strUrlPost = 'delivery=' + id + '&showImages=' + showImages + '&siteId=' + siteId;

            var storeForm = new BX.CDialog({
                'title': '<?=GetMessage('BITRONIC2_SOA_ORDER_GIVE')?>',
                head: '',
                'content_url': strUrl,
                'content_post': strUrlPost,
                'width': formWidth,
                'height':450,
                'resizable':false,
                'draggable':false
            });

            var button = [
                {
                    title: '<?=GetMessage('BITRONIC2_SOA_POPUP_SAVE')?>',
                    id: 'crmOk',
                    'action': function ()
                    {
                        GetBuyerStore(del_id);
                        BX.WindowManager.Get().Close();
                    }
                },
                BX.CDialog.btnCancel
            ];
            storeForm.ClearButtons();
            storeForm.SetButtons(button);
            storeForm.Show();
        }

        function GetBuyerStore(del_id)
        {
            BX('BUYER_STORE').value = BX('POPUP_STORE_ID').value;
            BX('ORDER_DESCRIPTION').value = '<?=GetMessage("BITRONIC2_SOA_ORDER_GIVE_TITLE")?>: '+BX('POPUP_STORE_NAME').value;
            BX('store_desc_' + del_id).innerHTML = BX('POPUP_STORE_NAME').value;
            BX.show(BX('select_store_' + del_id));
        }

        function showExtraParamsDialog(deliveryId)
        {
            var strUrl = '<?=$templateFolder?>' + '/delivery_extra_params.php';
            var formName = 'extra_params_form';
            var strUrlPost = 'deliveryId=' + deliveryId + '&formName=' + formName;

            if(window.BX.SaleDeliveryExtraParams)
            {
                for(var i in window.BX.SaleDeliveryExtraParams)
                {
                    strUrlPost += '&'+encodeURI(i)+'='+encodeURI(window.BX.SaleDeliveryExtraParams[i]);
                }
            }

            var paramsDialog = new BX.CDialog({
                'title': '<?=GetMessage('BITRONIC2_SOA_ORDER_DELIVERY_EXTRA_PARAMS')?>',
                head: '',
                'content_url': strUrl,
                'content_post': strUrlPost,
                'width': 500,
                'height':200,
                'resizable':true,
                'draggable':false
            });

            var button = [
                {
                    title: '<?=GetMessage('BITRONIC2_SOA_POPUP_SAVE')?>',
                    id: 'saleDeliveryExtraParamsOk',
                    'action': function ()
                    {
                        insertParamsToForm(deliveryId, formName);
                        BX.WindowManager.Get().Close();
                    }
                },
                BX.CDialog.btnCancel
            ];

            paramsDialog.ClearButtons();
            paramsDialog.SetButtons(button);
            //paramsDialog.adjustSizeEx();
            paramsDialog.Show();
        }

        function insertParamsToForm(deliveryId, paramsFormName)
        {
            var orderForm = BX("ORDER_FORM"),
                paramsForm = BX(paramsFormName);
            wrapDivId = deliveryId + "_extra_params";

            var wrapDiv = BX(wrapDivId);
            window.BX.SaleDeliveryExtraParams = {};

            if(wrapDiv)
                wrapDiv.parentNode.removeChild(wrapDiv);

            wrapDiv = BX.create('div', {props: { id: wrapDivId}});

            for(var i = paramsForm.elements.length-1; i >= 0; i--)
            {
                var input = BX.create('input', {
                        props: {
                            type: 'hidden',
                            name: 'DELIVERY_EXTRA['+deliveryId+']['+paramsForm.elements[i].name+']',
                            value: paramsForm.elements[i].value
                        }
                    }
                );

                window.BX.SaleDeliveryExtraParams[paramsForm.elements[i].name] = paramsForm.elements[i].value;

                wrapDiv.appendChild(input);
            }

            orderForm.appendChild(wrapDiv);

            BX.onCustomEvent('onSaleDeliveryGetExtraParams',[window.BX.SaleDeliveryExtraParams]);
        }
    </script>

    <input type="hidden" name="BUYER_STORE" id="BUYER_STORE" value="<?=$arResult["BUYER_STORE"]?>" />
    <?
    if(!empty($arResult["DELIVERY"]))
    {
        $width = ($arParams["SHOW_STORES_IMAGES"] == "Y") ? 850 : 700;
        ?>
        <h3><?=GetMessage("BITRONIC2_SOA_TEMPL_DELIVERY")?></h3>
        <div class="delivery-type row">
            <?

            foreach ($arResult["DELIVERY"] as $delivery_id => $arDelivery)
            {
                if(in_array($delivery_id,array(414))) continue;
                if ($delivery_id !== 0 && intval($delivery_id) <= 0)
                {
                    foreach ($arDelivery["PROFILES"] as $profile_id => $arProfile)
                    {
                        if ($delivery_id == 'edost') $deliveryImgURL = '/bitrix/images/delivery_edost_img/big/'.ceil($profile_id / 2).'.gif';
                        else if (count($arDelivery["LOGOTIP"]) > 0) {
                            $arFileTmp = CFile::ResizeImageGet(
                                $arDelivery["LOGOTIP"]["ID"],
                                array("width" => "160", "height" =>"106"),
                                BX_RESIZE_IMAGE_PROPORTIONAL,
                                true
                            );

                            $deliveryImgURL = $arFileTmp["src"];
                        } else {
                            $deliveryImgURL = $templateFolder."/images/logo-default-d.gif";
                        }

                        if($arDelivery["ISNEEDEXTRAINFO"] == "Y")
                            $extraParams = "showExtraParamsDialog('".$delivery_id.":".$profile_id."');";
                        else
                            $extraParams = "";

                        $onclick="BX('ID_DELIVERY_{$delivery_id}_{$profile_id}').checked=true;{$extraParams}submitForm();";
                        ?>
                        <label class="col-md-2 col-sm-3 col-xs-6" for="ID_DELIVERY_<?=$delivery_id?>_<?=$profile_id?>">
                            <input
                                    type="radio"
                                    id="ID_DELIVERY_<?=$delivery_id?>_<?=$profile_id?>"
                                    name="<?=htmlspecialcharsbx($arProfile["FIELD_NAME"])?>"
                                    value="<?=$delivery_id.":".$profile_id;?>"
                                <?=$arProfile["CHECKED"] == "Y" ? "checked=\"checked\"" : "";?>
                                    onclick="submitForm();"
                            />
                            <span class="radio-item">
                            <span class="radio-img" onclick="<?=$onclick?>">
                                <img src="<?=$deliveryImgURL?>" alt="<?=$arDelivery["TITLE"].'('.$arProfile["TITLE"].')'?>">
                            </span>
                            <span class="radio-item-header" onclick="<?=$onclick?>">
                                <?=(($delivery_id == 'edost' ? '' : htmlspecialcharsbx($arDelivery["TITLE"]).' - ') . $arProfile["TITLE"] . ($arProfile['day'] != '' ? ', '.$arProfile['day'] : ''))?>
                            </span>
                            <span class="bx_result_price"><!-- click on this should not cause form submit -->
                                <?
                                if($arProfile["CHECKED"] == "Y" && doubleval($arResult["DELIVERY_PRICE"]) > 0):
                                    ?>
                                    <div><?=GetMessage("BITRONIC2_SALE_DELIV_PRICE")?>:&nbsp;<b><?=$arResult["DELIVERY_PRICE_FORMATED"]?></b></div>
                                    <?
                                    if ((isset($arResult["PACKS_COUNT"]) && $arResult["PACKS_COUNT"]) > 1):
                                        echo GetMessage('SALE_PACKS_COUNT').': <b>'.$arResult["PACKS_COUNT"].'</b>';
                                    endif;
                                else:
                                    $APPLICATION->IncludeComponent('bitrix:sale.ajax.delivery.calculator', '', array(
                                        "NO_AJAX" => $arParams["DELIVERY_NO_AJAX"],
                                        "DELIVERY" => $delivery_id,
                                        "PROFILE" => $profile_id,
                                        "ORDER_WEIGHT" => $arResult["ORDER_WEIGHT"],
                                        "ORDER_PRICE" => $arResult["ORDER_PRICE"],
                                        "LOCATION_TO" => $arResult["USER_VALS"]["DELIVERY_LOCATION"],
                                        "LOCATION_ZIP" => $arResult["USER_VALS"]["DELIVERY_LOCATION_ZIP"],
                                        "CURRENCY" => $arResult["BASE_LANG_CURRENCY"],
                                        "ITEMS" => $arResult["BASKET_ITEMS"],
                                        "EXTRA_PARAMS_CALLBACK" => $extraParams
                                    ), null, array('HIDE_ICONS' => 'Y'));
                                endif;
                                ?>
                            </span>
                            <span class="radio-item-description">
                                <p onclick="<?=$onclick?>">
                                <?if (strlen($arProfile["DESCRIPTION"]) > 0):?>
                                    <?=nl2br($arProfile["DESCRIPTION"])?>
                                <?else:?>
                                    <?=nl2br($arDelivery["DESCRIPTION"])?>
                                <?endif;?>
                                </p>
                            </span>
                        </span>
                        </label>
                        <?
                    } // endforeach
                }
                else // stores and courier
                {
                    if (count($arDelivery["STORE"]) > 0)
                        $clickHandler = "onClick = \"fShowStore('".$arDelivery["ID"]."','".$arParams["SHOW_STORES_IMAGES"]."','".$width."','".SITE_ID."'," . $arDelivery["ID"] . ")\";";
                    else
                        $clickHandler = "onClick = \"BX('ID_DELIVERY_ID_".$arDelivery["ID"]."').checked=true;submitForm();\"";

                    if (count($arDelivery["LOGOTIP"]) > 0):

                        $arFileTmp = CFile::ResizeImageGet(
                            $arDelivery["LOGOTIP"]["ID"],
                            array("width" => "161", "height" =>"107"),
                            BX_RESIZE_IMAGE_PROPORTIONAL,
                            true
                        );

                        $deliveryImgURL = $arFileTmp["src"];
                    else:
                        $deliveryImgURL = $templateFolder."/images/logo-default-d.gif";
                    endif;
                    ?>
                    <label class="col-md-2 col-sm-3 col-xs-6" for="ID_DELIVERY_ID_<?=$arDelivery["ID"]?>">
                        <input type="radio"
                               id="ID_DELIVERY_ID_<?= $arDelivery["ID"] ?>"
                               name="<?=htmlspecialcharsbx($arDelivery["FIELD_NAME"])?>"
                               value="<?= $arDelivery["ID"] ?>"<?if ($arDelivery["CHECKED"]=="Y") echo " checked";?>
                               onclick="submitForm();"
                        />
                        <span class="radio-item">
                            <span class="radio-img">
                                <img src="<?=$deliveryImgURL?>" alt="<?=$arDelivery["NAME"]?>">
                            </span>
                            <span class="radio-item-header"><?=htmlspecialcharsbx($arDelivery["NAME"])?></span>
                            <span class="bx_result_price">
                                <?
                                if (strlen($arDelivery["PERIOD_TEXT"])>0)
                                {
                                    echo $arDelivery["PERIOD_TEXT"];
                                    ?><br /><?
                                }
                                ?>
                                <?=GetMessage("BITRONIC2_SALE_DELIV_PRICE");?>: <b><?=$arDelivery["PRICE_FORMATED"]?></b><br />
                            </span>
                            <span class="radio-item-description">
                                <?
                                if (strlen($arDelivery["DESCRIPTION"])>0)
                                    echo $arDelivery["DESCRIPTION"]."<br />";

                                if (count($arDelivery["STORE"]) > 0):
                                    ?>
                                    <span id="select_store_<?=$arDelivery["ID"]?>"<?if(strlen($arResult["STORE_LIST"][$arResult["BUYER_STORE"]]["TITLE"]) <= 0) echo " style=\"display:none;\"";?>>
                                        <span class="select_store"><?=GetMessage('BITRONIC2_SOA_ORDER_GIVE_TITLE');?>: </span>
                                        <span class="ora-store" id="store_desc_<?=$arDelivery["ID"]?>"><?=htmlspecialcharsbx($arResult["STORE_LIST"][$arResult["BUYER_STORE"]]["TITLE"])?></span>
                                    </span>
                                    <?
                                endif;
                                ?>
                            </span>
                            <?if(count($arDelivery['STORE']) > 0):?>
                                <a class="pseudolink select-store" href="javascript:;" id="select_store_link_<?=$arDelivery["ID"]?>" <?=$clickHandler?>><span class="link-text"><?=GetMessage("RZ_VIBRAT_SKLAD")?> </span></a>
                            <?endif?>
                        </span>
                    </label>
                    <?
                }
            }
            ?>
        </div><!-- /delivery-type row -->
        <?
    }
    ?>

<? } ?>
<? /* шаблон bitronic - КОНЕЦ */ ?>




<?
/* ==================== edost.locations (ввод адреса) */
if (!empty($arResult['edost']['locations_installed']) && isset($arResult['edost']['order_prop']['ADDRESS'])) {
    ?>
    <div class="edost_main edost_template_div"<?=($address_hide ? ' style="display: none;"' : '')?>>
        <h4 id="edost_location_address_head" class="edost_location_head"<?=($address_hide ? ' style="display: none;"' : '')?>>Адрес доставки</h4>
        <? $GLOBALS['APPLICATION']->IncludeComponent('edost:locations', '', array('MODE' => 'address') + $edost_locations_param, null, array('HIDE_ICONS' => 'Y')); ?>
    </div>
    <?
}
/* ==================== edost.locations */
?>
