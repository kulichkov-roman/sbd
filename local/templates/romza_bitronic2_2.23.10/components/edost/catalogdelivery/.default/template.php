
<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();?>
<?

$mode = $arResult['mode'];
$catalogdelivery_sign = GetMessage('EDOST_CATALOGDELIVERY_SIGN');
$error = GetMessage('EDOST_CATALOGDELIVERY_ERROR');
$param = (isset($arResult['param']) ? $arResult['param'] : false);
//echo '<br><b>template param</b> <pre style="font-size: 12px">'.print_r($param, true).'</pre>';

$edost_delivery = (!empty($param['edost_delivery']) ? true : false);
$edost_locations = (!empty($param['edost_locations']) ? true : false);

$location_id = (!empty($param['location']['id']) ? $param['location']['id'] : '');
$location_zip = (!empty($param['location']['zip']) ? $param['location']['zip'] : '');
$city = (!empty($param['location']['city']) ? $param['location']['city'] : '');
$region = (!empty($param['location']['region']) ? $param['location']['region'] : '');
$country = (!empty($param['location']['show_country']) ? $param['location']['country'] : '');

$s = $region;
if ((function_exists('mb_strlen') ? mb_strlen($city, LANG_CHARSET) : strlen($city)) > 35) {
	if ($country != '') $s .= ($s != '' ? ', ' : '').$country;
	$s = '<br><span>'.$s.'</span>';
}
else {
	if ($s != '') $s = ' <span>('.$s.')</span>';
	if ($country != '') $s .= ($s != '' || $city != '' ? ', ' : '').$country;
}

if (empty($location_id)) {
	$city = $catalogdelivery_sign['get'];
	$location_data = 'GETCITY';
}
else {
	$flag = (isset($param['location']['flag']) ? '<img class="edost_flag" src="/bitrix/images/delivery_edost_img/flag/'.$param['location']['flag'].'.gif" border="0">' : '');
	$location_data = $flag.'<span class="edost_city_name">'.$city.$s.'</span><span class="edost_city_link" onclick="searchCityChange()">'.$catalogdelivery_sign['change'].'</span>';
	$location_data .= '<input id="edost_shop_LOCATION" value="'.$location_id.'" type="hidden">';
	if ($edost_locations) {
		$location_data .= '<input id="edost_shop_ZIP" value="'.$location_zip.'" type="hidden">';
		$location_data .= '<input id="edost_city2" value="'.(!empty($param['location']['city2']) ? $param['location']['city2'] : '').'" type="hidden">';
	}
}

if (isset($arResult['window_param'])) echo '<input id="edost_catalogdelivery_window_param" value="'.$arResult['window_param'].'" type="hidden">';

$bSibCore = \Bitrix\Main\Loader::includeModule('sib.core');
//$arrSetOut = [348];
// 'inside' блок (заполняется через js)
$inside_data = false;
if ($mode != 'manual' && $mode != '') {
	$format = $arResult['inside'];
	$minimize = (!empty($format['minimize']) ? true : false);
	$light = ($minimize && empty($format['show_day']) ? ' style="color: #555;"' : '');

	$s = '';
	if (!empty($format['data']))
		global $USER; if($USER->IsAdmin()){echo '<pre>';print_r($arResult);echo '</pre>';}
		foreach ($format['data'] as $f_key => $f)
			if (!empty($f['tariff']))
			{
				/*RBS_CUSTOM_START*/
				$arrSet = ['dpd' => 370, 'sdek' => 352/* , 296, 340, 348, 346 */];
				$sdek = false;
				$dpd = false;
				foreach ($f['tariff'] as $k => $v){
					if (isset($v['id']) && $v['id'] == 352){
						$sdek = true;
					}
					if (isset($v['id']) && $v['id'] == 370){
						$dpd = true;
					}
				}
				if(!$sdek){
					$arrSet['sdek'] = 354;
				}
				if(!$dpd){
					$arrSet['dpd'] = 372;
				}

				if($arrSet['sdek'] == 352){
					$arrSetOut[] = 354;
				}
				if($arrSet['sdek'] == 354){
					$arrSetOut[] = 352;
				}
				if($arrSet['dpd'] == 370){
					$arrSetOut[] = 372;
				}
				if($arrSet['dpd'] == 372){
					$arrSetOut[] = 370;
				}

				$hasSdekSelf = false;
				$hasSdekCourier = false;
				$arAfter = [];

				$hasDpdSelf = false;
				$hasDpdCourier = false;
				
				//$f['tariff']
				

				foreach ($f['tariff'] as $k => $v){
					if($v['tariff_id'] == 37){
						$hasSdekSelf = true;
					}
					if($v['tariff_id'] == 38){
						$hasSdekCourier = true;
					}

					if($v['tariff_id'] == 46){
						$hasDpdSelf = true;
					}

					if($v['tariff_id'] == 47){
						$hasDpdCourier = true;
					}

					if($v['id'] == 348){
						$arrSetOut[] = 422;
					}
					if(in_array((int)$v['id'], [342, 344])){
						unset($f['tariff'][$k]);
						$arAfter = $v;
					}
				}
				if($arAfter){
					$f['tariff'][] = $arAfter;
				}
				/*RBS_CUSTOM_END*/	
				foreach ($f['tariff'] as $k => $v) if (isset($v['id'])) {
					/*RBS_CUSTOM_START*/
                     if($hasSdekSelf && $hasSdekCourier){
						if(!in_array($v['tariff_id'], [37, 38])){
							continue;
						}

						switch((int)$v['tariff_id']){
							case 37: 
								$v['company'] = 'Самовывоз'; $v['ico'] = SITE_TEMPLATE_PATH . '/new_img/new_delivery_self.png';
							break;
							case 38: 
								$v['company'] = 'Доставка курьером'; $v['ico'] = SITE_TEMPLATE_PATH . '/new_img/new_delivery_courier.png';
							break;
						}

						\Sib\Core\Edost::modifyDetailTemplate($v, $arParams);
						
						$s .= '<li data-tarrif-id="'.$v['tariff_id'].'" data-delivery-id="'.$v['id'].'" class="card-main-delivery-list__item rbs-dblock-detail">';

							/* if (!empty($format['show_ico'])) {
								if (isset($v['ico']) && $v['ico'] !== '') $ico = (strlen($v['ico']) <= 3 ? '/bitrix/images/delivery_edost_img/'.($minimize ? 'small/' : '').$v['ico'].'.gif' : $v['ico']);
								else if (!empty($param['ico_default'])) $ico = $param['ico_default'];
								else $ico = $arResult['component_path'].'/images/logo-default-d.gif';

								if($v['ico'] != 'unset'){
									if ($ico !== false) $s .= '<div class="card-main-delivery-list__logo rbs-delivery-img"><img src="'.$ico.'" border="0"></div>';
									else $s .= '<div class="card-main-delivery-list__logo"><div class="edost_ico'.($minimize ? '_small' : '').'"></div></div>';
								}							
							} */

							$s .= '<div class="card-main-delivery-list__name">';

								$s .= '<span class="edost_format_tariff"'.$light.'>'.(empty($v['company']) && !empty($v['head']) ? $v['head'] : $v['company']).'</span>';

								$s2 = '';
								if (!empty($v['name_original'])) $s2 .= $v['name_original'];
								else if (!empty($v['name'])) $s2 .= $v['name'];
								if (!empty($format['show_day']) && !empty($v['day'])) $s2 .= ($s2 != '' ? ', ' : '').$v['day'];
								$s .= '<div>';
																
								$s .= '</div>';
							$s .= '</div>';

							$s .= '<div class="card-main-delivery-list__name">';

								$s .= '<div class="rbs-edost-self-date">';
									if ($s2 != ''){
										$s .= ' <span class="edost_format_name">через '.$s2;
										$s .= '</span>';
									}		
								$s .= '</div>';

								$s .= '<div class="rbs-edost-det-price">';
									if(isset($v['free_custom'])) $s .= $v['free_custom'];
									else if (isset($v['free'])) $s .= '<span class="delivery-options__free">'.$v['free'].'</span>';
									else $s .= (isset($v['pricetotal_formatted']) ? $v['pricetotal_formatted'] : $v['price_formatted']);
								$s .= '</div>';

							$s .= '</div>';

						$s .= '</li>';

					}
					else if ($hasDpdSelf && $hasDpdCourier) {
						if(!in_array($v['tariff_id'], [46, 47])){
							continue;
						}

						switch((int)$v['tariff_id']){
							case 46: 
								$v['company'] = 'Самовывоз'; $v['ico'] = SITE_TEMPLATE_PATH . '/new_img/new_delivery_self.png';
							break;
							case 47:
								$v['company'] = 'Доставка курьером'; $v['ico'] = SITE_TEMPLATE_PATH . '/new_img/new_delivery_courier.png';
							break;
						}

						\Sib\Core\Edost::modifyDetailTemplate($v, $arParams);
						
						$s .= '<li data-tarrif-id="'.$v['tariff_id'].'" data-delivery-id="'.$v['id'].'" class="card-main-delivery-list__item rbs-dblock-detail">';

							/* if (!empty($format['show_ico'])) {
								if (isset($v['ico']) && $v['ico'] !== '') $ico = (strlen($v['ico']) <= 3 ? '/bitrix/images/delivery_edost_img/'.($minimize ? 'small/' : '').$v['ico'].'.gif' : $v['ico']);
								else if (!empty($param['ico_default'])) $ico = $param['ico_default'];
								else $ico = $arResult['component_path'].'/images/logo-default-d.gif';

								if($v['ico'] != 'unset'){
									if ($ico !== false) $s .= '<div class="card-main-delivery-list__logo rbs-delivery-img"><img src="'.$ico.'" border="0"></div>';
									else $s .= '<div class="card-main-delivery-list__logo"><div class="edost_ico'.($minimize ? '_small' : '').'"></div></div>';
								}							
							} */

							$s .= '<div class="card-main-delivery-list__name">';

								$s .= '<span class="edost_format_tariff"'.$light.'>'.(empty($v['company']) && !empty($v['head']) ? $v['head'] : $v['company']).'</span>';

								$s2 = '';
								if (!empty($v['name_original'])) $s2 .= $v['name_original'];
								else if (!empty($v['name'])) $s2 .= $v['name'];
								if (!empty($format['show_day']) && !empty($v['day'])) $s2 .= ($s2 != '' ? ', ' : '').$v['day'];
								$s .= '<div>';
																
								$s .= '</div>';
							$s .= '</div>';

							$s .= '<div class="card-main-delivery-list__name">';

								$s .= '<div class="rbs-edost-self-date">';
									if ($s2 != ''){
										$s .= ' <span class="edost_format_name">через '.$s2;
										$s .= '</span>';
									}		
								$s .= '</div>';

								$s .= '<div class="rbs-edost-det-price">';
									if(isset($v['free_custom'])) $s .= $v['free_custom'];
									else if (isset($v['free'])) $s .= '<span class="delivery-options__free">'.$v['free'].'</span>';
									else $s .= (isset($v['pricetotal_formatted']) ? $v['pricetotal_formatted'] : $v['price_formatted']);
								$s .= '</div>';

							$s .= '</div>';

						$s .= '</li>';

                    }
					else {
						if(in_array($v['id'], $arrSetOut)){
							continue;
						}

						switch((int)$v['id']){
							case $arrSet['dpd']: $v['company'] = 'DPD'; $v['ico'] = SITE_TEMPLATE_PATH . '/new_img/delivery-comp-1.png'; break;
							case $arrSet['sdek']: $v['company'] = 'СДЭК'; $v['ico'] = SITE_TEMPLATE_PATH . '/new_img/delivery-comp-2.png'; break;
							case 297:
							case 296: $v['company'] = 'EMS'; $v['ico'] = SITE_TEMPLATE_PATH . '/new_img/delivery-comp-3.png'; break;
							case 340:
							case 342:
							case 344:
							case 423:
								$customDate = '<span class="rbs-today-delivery js-card-question">сегодня <span class="card-tooltipe">При заказе до 19:00</span></span>';
								if($bSibCore){
									$customDate = \Sib\Core\Edost::getCustomDate();
								}	
								$v['custom_date'] = $customDate;
								if($v['id'] == 423){
									$v['custom_date'] = "через " . $v['day'];
								}
								$v['ico'] = SITE_TEMPLATE_PATH . '/new_img/new_delivery_courier.png';
								$v['company'] = 'Доставка курьером';
							break;
							case 348:	
							case 422:					
								$v['ico'] = SITE_TEMPLATE_PATH . '/new_img/new_delivery_self.png';
								$v['company'] = 'Самовывоз';
								$v['free_custom'] = '<a href="/contacts/" target="_blank" class="rbs-card-contact-link"><span>из магазина</span></a>';
								if($bSibCore){
									$v['free_custom'] = \Sib\Core\Edost::getCustomFree();
								}	
								$v['free_custom_date'] = 'через 5 минут';
								if($v['free']){
									$v['free'] = str_replace('!', '', $v['free']);
								}
							break;
						}

						

						\Sib\Core\Edost::modifyDetailTemplate($v, $arParams);
						global $USER; if($USER->IsAdmin()){echo '<pre>';print_r($v);echo '</pre>';}
						$s .= '<li data-tarrif-id="'.$v['tariff_id'].'" data-delivery-id="'.$v['id'].'" class="card-main-delivery-list__item rbs-dblock-detail">';

							/* if (!empty($format['show_ico'])) {
								if (isset($v['ico']) && $v['ico'] !== '') $ico = (strlen($v['ico']) <= 3 ? '/bitrix/images/delivery_edost_img/'.($minimize ? 'small/' : '').$v['ico'].'.gif' : $v['ico']);
								else if (!empty($param['ico_default'])) $ico = $param['ico_default'];
								else $ico = $arResult['component_path'].'/images/logo-default-d.gif';

								if($v['ico'] != 'unset'){
									if ($ico !== false) $s .= '<div class="card-main-delivery-list__logo rbs-delivery-img"><img src="'.$ico.'" border="0"></div>';
									else $s .= '<div class="card-main-delivery-list__logo"><div class="edost_ico'.($minimize ? '_small' : '').'"></div></div>';
								}							
							} */
							if(!empty($v['custom_date'])) $s2 = $v['custom_date'];
							$s .= '<div class="card-main-delivery-list__name">';

								$s .= '<span class="edost_format_tariff"'.$light.'>'.(empty($v['company']) && !empty($v['head']) ? $v['head'] : $v['company']).'</span>';

							

								$s .= '<div>';
									if(isset($v['free_custom'])) $s .= $v['free_custom'];
								$s .= '</div>';	

							$s .= '</div>';

							$s .= '<div class="card-main-delivery-list__name">';

								$s .= '<div class="rbs-edost-self-date">';
									if ($s2 != ''){
										$s .= ' <span class="edost_format_name">' . $s2;
										$s .= '</span>';
									}	
									unset($s2);
									if(isset($v['free_custom_date'])) $s .= $v['free_custom_date'];
								$s .= '</div>';

								$s .= '<div class="rbs-edost-det-price">';
									
									if (isset($v['free'])) $s .= '<span class="delivery-options__free">'.$v['free'].'</span>';
									else $s .= (isset($v['pricetotal_formatted']) ? $v['pricetotal_formatted'] : $v['price_formatted']);
								$s .= '</div>';

							$s .= '</div>';

						$s .= '</li>';
					}
				}
			}
		

	if (empty($location_id)) $s = '';
	else if ($s == '') $s = 'FALSE';
	else $s = '<ul class="edost edost_catalogdelivery_inside card-main-delivery-list">'.$s.'</ul>';

	$inside_data = $s;
}


// параметры расчета
if (!empty($arResult['param_string'])) { ?>
<input id="edost_catalogdelivery_param" value="<?=$arResult['param_string']?>" type="hidden">
<? }


// подключение библиотек (если в параметрах прописано подключение через JS)
if (!empty($param['script'])) { ?>
<script type="text/javascript">
	var E = document.getElementById('edost_catalogdelivery_script');
	if (!window.edost_catalogdelivery && !E) {
		var E = document.head;
		var protocol = (document.location.protocol == 'https:' ? 'https://' : 'http://');

		var E2 = document.createElement('LINK');
		E2.id = 'edost_office_css';
		E2.href = protocol + 'edostimg.ru/shop/office.css?a=<?=$param['script']['date']?>';
		E2.type = 'text/css';
		E2.rel = 'stylesheet';
		E.appendChild(E2);

		var E2 = document.createElement('SCRIPT');
		E2.id = 'edost_catalogdelivery_script';
		E2.type = 'text/javascript';
		E2.charset = 'utf-8';
		E2.src = protocol + 'edostimg.ru/shop/catalogdelivery.js?a=<?=$param['script']['date']?>';
		E.appendChild(E2);

<?		if ($edost_delivery) { ?>
		var E2 = document.createElement('SCRIPT');
		E2.id = 'edost_office_script';
		E2.type = 'text/javascript';
		E2.charset = 'utf-8';
		E2.src = protocol + 'edostimg.ru/shop/office.js?a=<?=$param['script']['date']?>';
		E.appendChild(E2);
<?		} ?>
	}
</script>
<? }


// нестандартные стили окна
if ($inside_data === false && isset($arResult['COLOR'])) { ?>
<style>
	div.edost_catalogdelivery_window {
		border: 5px solid <?=$arResult['COLOR']?> !important;
		box-shadow: 0px 0px 10px 0px <?=($arResult['CLEAR_WHITE'] ? '#888' : $arResult['COLOR_SHADOW'])?> !important;
		border-radius: <?=$arResult['RADIUS']?>px !important;
	}
	td.edost_catalogdelivery_window_head {
		color: <?=($arResult['CLEAR_WHITE'] ? '#AAA' : $arResult['COLOR_FONT'])?> !important;
		background: <?=$arResult['COLOR']?> !important;
	}
	div.edost_catalogdelivery_window_close {
		background: url(<?=$arResult['component_path']?>/images/close<?=($arResult['CLEAR_WHITE'] ? '_black' : '')?>.png) no-repeat 0px 2px !important;
	}
	div.edost_catalogdelivery_window_close:hover {
		background: url(<?=$arResult['component_path']?>/images/close<?=($arResult["CLEAR_WHITE"] ? '_black' : '')?>_hover.png) no-repeat 0px 2px !important;
	}
	div.edost_catalogdelivery_button {
		color: <?=$arResult['COLOR_FONT']?> !important;
		border: 1px solid <?=($arResult['CLEAR_WHITE'] ? '#DDD' : $arResult['COLOR'])?> !important;
		background: <?=$arResult['COLOR']?> !important;
	}
	div.edost_catalogdelivery_button:hover {
		background: <?=$arResult['COLOR_UP']?> !important;
	}
	div.edost_catalogdelivery_button:active {
		color: <?=$arResult['COLOR_FONT_UP']?> !important;
	}
</style>
<? }


// js функции
if ($inside_data === false) { ?>
<script type="text/javascript">
	function edost_RunScript(name) {

		if (!window.edost_catalogdelivery<?=($edost_delivery ? ' || !window.edost_office' : '')?>) {
			var s = "edost_RunScript('" + name + "'";
			for (var i = 1; i < arguments.length; i++) s += ",'" + arguments[i].replace(/'/g, '\\\'') + "'";
			s += ")";
			window.setTimeout(s, 500);
			return;
		}

		if (name == 'param') edost_catalogdelivery.set_window_param(arguments[1] != undefined ? arguments[1] : false);
		if (name == 'data') edost_catalogdelivery.set_data(arguments[1], arguments[2], arguments[3], arguments[4]);
		if (name == 'window') edost_catalogdelivery.window('', arguments[1], arguments[2]);
		if (name == 'inside') edost_catalogdelivery.window('inside', arguments[1], arguments[2]);
		if (name == 'preview') edost_catalogdelivery.calculate('preview', arguments[1], arguments[2]);
		if (name == 'map_inside') {
<?			if ($mode == 'manual') { ?>
			edost_catalogdelivery.set_window_param('manual');
<?			} ?>
			var E = document.getElementById('edost_office_inside_head');
			if (!E) edost_office2.window('inside');
		}

	}

	function searchCityChange()
	{
		$('#edost_catalogdelivery_window .edost_catalogdelivery_window_close').trigger('click');
		$('#edost_catalogdelivery_inside_city .edost_link').trigger('click');
		/*
		$onClick = "OpenVregionsPopUp('open', 'vregions-popup', 'vregions-sepia'); return false;"
		if($('.vr-template').data('rand') > 0)
		{
			$onClick = $onClick.replace('vregions-popup', 'vregions-popup' + $('.vr-template').data('rand'));
			$onClick = $onClick.replace('vregions-sepia', 'vregions-sepia' + $('.vr-template').data('rand'));
			$('#edost_catalogdelivery_inside_city .edost_link').attr('onclick', $onClick);
		}*/
	}

	function edost_LoadLocation(param, id) {

		var post = true;

		if (id == undefined) {
			var E = document.getElementById('edost_shop_LOCATION');
			if (E) id = E.value;
		}

<?		if ($edost_locations) { ?>
		if (param == 'start') {
<?			$GLOBALS['APPLICATION']->IncludeComponent('edost:locations', '', array('MODE' => 'add_script'), null, array('HIDE_ICONS' => 'Y')); ?>

			if (!window.edost_location) {
				window.setTimeout("edost_LoadLocation('start', '" + id + "')", 500);
				post = false;
			}
		}
<?		} ?>

		var id_default = 0;
		var s = edost_catalogdelivery.param_string;
		var p = s.indexOf('location_id_default(');
		if (p >= 0) id_default = s.substr(p + 20).split(')')[0];

		var E = document.getElementById('edost_catalogdelivery_window_city');
		if (!E) return;

		if (param == 'start') {
			var s = edost_catalogdelivery.loading_small;
<?			if ($edost_locations) { ?>
			s = '<div class="edost_catalogdelivery_window_city">' + s + '</div>';
<?			} ?>
			E.innerHTML = s;
		}
		if (!post) return;

		BX.ajax.post('<?=$arResult['component_path']?>/edost_catalogdelivery.php', 'location=Y&id=' + id + '&default=' + id_default + '<?=($edost_locations ? '&edost_locations=Y' : '')?>', function(r) {
			E.innerHTML = r;
			<?if ($edost_locations) { ?>
				E = BX('edost_city');
				if (E && E.type != 'hidden') E.focus();
			<?}?>
			
		});
	}

	function edost_GetTariff(param) {

		var E = document.getElementById('edost_catalogdelivery_quantity');
		var quantity = (E ? E.value : 1);

		if (param == 'quantity') {
			if (quantity != edost_catalogdelivery.quantity && quantity > 0) edost_catalogdelivery.quantity = quantity;
			else return;
		}

		var post = '';
		var param = edost_catalogdelivery.param_string;

		var E = document.getElementById('edost_shop_LOCATION');
		var id = (E ? E.value : 0);

		var E = document.getElementById('edost_shop_ZIP');
		var zip = (E ? E.value : '');

		var E = document.getElementById('edost_city2');
		var city2 = encodeURIComponent(E ? E.value : '');

		var E = document.getElementById('edost_bookmark');
		var bookmark = (E ? E.value : edost_catalogdelivery.bookmark);

<?		if ($mode == 'manual') { ?>
		if (!(id > 0)) {
			edost_catalogdelivery.calculate('error', '<?=$error['head'].$error['location']?>');
			return;
		}
<?		} ?>

		var c = edost_catalogdelivery.get_cookie('edost_catalogdelivery');
		c = c.split('|manual=');
		var c1 = c[0];
		var c2 = (c[1] ? c[1] : '');

<?		if ($mode == 'manual') { ?>
		c2 = [];
		var ar = ['weight', 'price', 'size1', 'size2', 'size3'];
		for (var i = 0; i < ar.length; i++) {
			var E = document.getElementById('edost_catalogdelivery_' + ar[i]);
			var v = (E ? encodeURIComponent(E.value.replace(/[^0-9,.]/g, '').replace(/,/g, '.')) : '');
			if (E) post += '&' + ar[i] + '=' + v;
			if (ar[i] == 'weight' && v == 0) {
				edost_catalogdelivery.calculate('error', '<?=$error['head'].$error['weight']?>');
				return;
			}
			c2.push(v);
		}
		c2 = c2.join('|');
<?		} else { ?>
		var E = document.getElementById('edost_catalogdelivery_cart');
		var add_cart = c1 = (E && E.checked ? 1 : 0);
		post = '&product=' + edost_catalogdelivery.product_id + '&quantity=' + quantity + '&addcart=' + add_cart;

		var E = document.getElementById('edost_catalogdelivery_product_price_' + edost_catalogdelivery.product_id);
		if (E) post += '&price=' + E.value;
<?		} ?>

		edost_catalogdelivery.set_cookie('edost_catalogdelivery=' + c1 + '|manual=' + c2);

<?		if ($edost_locations) { ?>
		post += '&edost_locations=Y';
<?		} else { ?>
		if (id) edost_catalogdelivery.set_cookie('edost_location=' + id + '|' + zip + '|');
<?		} ?>

		edost_catalogdelivery.calculate('loading');
		BX.ajax.post('<?=$arResult['component_path']?>/edost_catalogdelivery.php', 'mode=window&param=' + param + '&id=' + id + '&zip=' + zip + '&city2=' + city2 + '&bookmark=' + bookmark + post, function(r) {
			var E = document.getElementById('edost_catalogdelivery_data');
			if (E) E.innerHTML = r;
			edost_catalogdelivery.position('update');
			
			setTimeout(function() {
				$onClick = "OpenVregionsPopUp('open', 'vregions-popup', 'vregions-sepia'); return false;"
				if($('.vr-template').data('rand') > 0)
				{
					$onClick = $onClick.replace('vregions-popup', 'vregions-popup' + $('.vr-template').data('rand'));
					$onClick = $onClick.replace('vregions-sepia', 'vregions-sepia' + $('.vr-template').data('rand'));
					$('#edost_catalogdelivery_inside_city .edost_link').attr('onclick', $onClick);
				}
			}, 100);
			
		});

	}
</script>
<? }


// поля для ввода параметров ручного калькулятора
if ($mode == 'manual') {
	$field = $param['field']; ?>
	<div id="edost_catalogdelivery_window" class="edost_catalogdelivery_manual">
		<table class="edost_catalogdelivery_param" cellpadding="0" cellspacing="0" border="0">
			<tr<?=(!empty($field['location']['hide']) ? ' style="display: none;"' : '')?>>
				<td id="edost_catalogdelivery_window_city_head"><?=$field['location']['name']?>:</td>
				<td>
					<div id="edost_catalogdelivery_window_city">
<?
						if (!empty($location_id)) echo $location_data;
						else CCatalogDeliveryEDOST::DrawLocation(0, $edost_locations);
?>
					</div>
				</td>
			</tr>
<?
			$ar = array('zip', 'weight', 'price', 'size');
			foreach ($ar as $v) if (empty($field[$v]['disable'])) {
				if ($v == 'zip') {
					if ($edost_locations) continue;
					$field[$v]['default'] = $location_zip;
				}
				echo '<tr'.(!empty($field[$v]['hide']) ? ' style="display: none;"' : '').'><td>'.$field[$v]['name'].':</td><td>';
				if ($v != 'size') echo '<input class="edost_catalogdelivery_param" id="'.($v == 'zip' ? 'edost_shop_ZIP' : 'edost_catalogdelivery_'.$v).'" value="'.(!empty($field[$v]['default']) ? $field[$v]['default'] : '').'"'.($v == 'zip' ? ' style="width: 70px;"' : '').'>';
				else for ($i = 0; $i < 3; $i++) echo ($i != 0 ? ' x ' : '').'<input class="edost_catalogdelivery_param" id="edost_catalogdelivery_size'.($i+1).'" value="'.(!empty($field[$v]['default'][$i]) ? $field[$v]['default'][$i] : '').'">';
				echo (isset($field[$v]['measure_name']) ? ' '.$field[$v]['measure_name'] : '').'</td></tr>';
			}
?>
		</table>

		<div class="edost_catalogdelivery_button" style="width: 150px; margin: 15px 0 20px 80px;" onclick="edost_GetTariff('');"><?=$catalogdelivery_sign['calculate']?></div>
		<div id="edost_catalogdelivery_data" style2="padding-bottom: 4px; height: 555px; width: 700px; overflow-y: visible;">
<? } ?>




<? /* шаблон edost - НАЧАЛО */ ?>
<? if (isset($arResult['edost']['format'])) { ?>
<?
	$edost_catalogdelivery = true;
	$data = (isset($arResult['edost']) ? $arResult['edost'] : false);
	$sign = GetMessage('EDOST_DELIVERY_SIGN');
	$table_width = 645;
	$ico_path = '/bitrix/images/delivery_edost_img';
	$ico_loading = '<img style="vertical-align: middle;" src="'.$ico_path.'/loading_small.gif" width="20" height="20" border="0">'; // иконка загрузки
//	$ico_loading_map_inside = '<div class="edost_map_loading"><img src="'.$ico_path.'/loading_small.gif" border="0" width="64" height="64"></div>'; // иконка загрузки для интегрированной карты

	if ($edost_catalogdelivery) $ico_default = (!empty($param['ico_default']) ? $param['ico_default'] : $arResult['component_path'].'/images/logo-default-d.gif');
	else $ico_default = $templateFolder.'/images/logo-default-d.gif';

	if (!empty($data['javascript'])) echo $data['javascript'];
	if (!empty($data['format']['warning']) && empty($arResult['edost']['locations_installed'])) echo '<div class="edost_warning edost_warning_big">'.$data['format']['warning'].'</div>'.'<br>';
	$map_inside = (!$edost_catalogdelivery && empty($data['map_inside']) || empty($data['format']['map_inside']) || $data['format']['map_inside'] == 'N' ? '' : $data['format']['map_inside']);

	// собственное описание для групп пунктов выдачи
//	$sign['office_description']['shop'] = ''; // адреса магазинов
//	$sign['office_description']['office'] = ''; // пункты выдачи
//	$sign['office_description']['terminal'] = ''; // терминалы
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
<?			if (!$edost_catalogdelivery) { ?>
			var E = document.getElementById('edost_delivery_id');
			if (E) if (E.value != 'edost:' + profile) submitForm();
<?			} ?>
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

<?		if (!$edost_catalogdelivery) { ?>
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
<?		} else { ?>
		edost_catalogdelivery.calculate('loading');
		BX.ajax.post('<?=$arResult['component_path']?>/edost_catalogdelivery.php', 'set_office=Y&id=' + id + '&profile=' + profile + '&cod=' + cod + '&mode=' + mode, function(r) {
			edost_catalogdelivery.calculate();
		});
<?		} ?>

	}

	function edost_MapInside() {

<?		if ($edost_catalogdelivery) { ?>
		edost_RunScript('map_inside');
<?		} else { ?>
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
<?		} ?>

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
<?			if (!$edost_catalogdelivery) { ?>
			if (E)
				if (!start) E.style.display = 'none';
				else if (bookmark == 1) E.style.display = (show ? 'block' : 'none');
<?			} else { ?>
			if (E) E.style.display = (show ? 'block' : 'none');
<?			} ?>
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

<?			if (!$edost_catalogdelivery) { ?>
			submitForm();
<?			} ?>
		}

<?		if ($edost_catalogdelivery && $map_inside == 'Y') { ?>
		if (id == 'office') edost_MapInside();
<?		} ?>

<?		if ($edost_catalogdelivery && $mode != 'manual') { ?>
		edost_catalogdelivery.position('update');
<?		} ?>
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


<?	if ($bookmark != '') { ?>
	<div id="edost_bookmark_div">
	<input id="edost_bookmark" name="edost_bookmark" value="<?=$bookmark_id?>" type="hidden">
	<table class="edost_bookmark" cellpadding="0" cellspacing="0" border="0">
		<tr>
<?		foreach ($data['format']['data'] as $f_key => $f) if ($bookmark !== 2 || $f_key !== 'general') { $id = $f_key; ?>
			<td id="edost_<?=$id?>_td" class="edost_active" width="110" style="padding-bottom: 5px;" onclick="edost_SetBookmark('<?=$id?>')">
				<img src="<?=$ico_path.'/'.$f_key.'.gif'?>" border="0">
				<br>
				<span class="edost_bookmark"><?=$f['head']?></span>
				<br>
<?			if ($f_key != 'show') { ?>
				<div>
<?					if (isset($f['free']) || isset($f['min']['free'])) { ?>
					<span class="edost_format_price edost_price_free" style=""><?=(isset($f['free']) ? $f['free'] : $f['min']['free'])?></span>
<?					} else if (isset($f['price_formatted']) || isset($f['min']['price_formatted'])) { ?>
					<span class="edost_format_price edost_price"<?=(isset($f['price_formatted']) ? ' style="color: #888;"' : '')?>><?=(isset($f['price_formatted']) ? $f['price_formatted'] : $f['min']['price_formatted'])?></span>
<?					} ?>

<?					if (!empty($f['min']['day'])) { ?>
					<br><span class="edost_format_price edost_day"><?=(!empty($f['min']['day']) ? $f['min']['day'] : '')?></span>
<?					} ?>

<?					if ($cod_bookmark && ($bookmark == 1 && $f['cod'] || $bookmark == 2 && (!$cod_tariff && isset($f['min']['pricecash']) || $f['min']['cod_tariff']))) { ?>
						<br><span class="edost_price_head edost_payment"><?=$sign['cod_head_bookmark']?></span>
<?					} ?>
				</div>
<?			} ?>
			</td>
			<td width="25"></td>
<?		} ?>
		</tr>
<?		if ($bookmark == 1) { ?>
		<tr>
<?			foreach ($data['format']['data'] as $f_key => $f) { $id = $f_key; ?>
			<td id="edost_<?=$id?>_td_bottom" style="height: 10px;"></td>
			<td></td>
<?			} ?>
		</tr>
		<tr>
			<td id="edost_bookmark_delimiter" colspan="10" style="height: 5px;"></td>
		</tr>
<?		} ?>
	</table>
<?	if (!$edost_catalogdelivery) { ?>
	<div id="edost_bookmark_loading" style="padding-top: 20px; display: none;"></div>
<?	} ?>
<?	if ($bookmark_id == 'show') echo '<div style="height: 20px;"></div>'; ?>
	</div>
<?	} ?>


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
<?			if (!empty($v['office_address'])) { ?>
				<div style="padding-bottom: 12px;">
					<span class="edost_format_address_head"><?=$sign['address2']?>: </span>
					<span class="edost_format_address"><?=$v['office_address']?></span>
					<a class="edost_link" href="http://www.edost.ru/office.php?c=<?=$v['office_id']?>" target="_blank"><?=$sign['map']?></a>
				</div>
<?			} ?>
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
<?					if (!$edost_catalogdelivery) { ?>
					<input class="edost_format_radio" <?=($hide_radio ? 'style="display: none;"' : '')?> type="radio" id="<?=$id?>" name="DELIVERY_ID" value="<?=$value?>" <?=(!empty($v['checked']) ? 'checked="checked"' : '')?> onclick="<?=$onclick?>">
<?					} ?>

<?					if ($ico !== false) { ?>
					<label class="edost_format_radio" for="<?=$id?>"><img class="edost_ico edost_ico_normal" src="<?=$ico?>" border="0"></label>
<?					} else { ?>
					<div class="edost_ico"></div>
<?					} ?>
				</td>

				<td class="edost_format_tariff">
					<label for="<?=$id?>">
					<span class="edost_format_tariff"><?=(isset($v['head']) ? $v['head'] : $v['company'])?></span>
<?					if ($v['name'] != '' && !isset($v['company_head'])) { ?>
					<span class="edost_format_name"> (<?=$v['name']?>)</span>
<?					} ?>

<?					if ($v['insurance'] != '' && (!$cod_tariff || empty($v['cod_tariff']))) { ?>
					<br><span class="edost_insurance"><?=$v['insurance']?></span>
<?					} ?>

<?					if ($cod_tariff && $office_map == 'get' && isset($v['pricecod']) && $v['pricecod'] >= 0) { ?>
					<br><span class="edost_price_head edost_payment"><?=str_replace('<br>', ' ', $sign['cod_head_bookmark'])?></span>
<?					} ?>

<?					if ($cod_tariff && $v['automatic'] == 'edost' && $v['profile'] != 0 && ($office_map == '' || !empty($v['office_address']))) { ?>
						<br><?=(empty($v['cod_tariff']) ? $sign['price_head'] : $sign['cod_head'])?>
<?					} ?>
					</label>

<?					if ($office_map == 'get') { ?>
					<br><span class="edost_format_link_big" onclick="edost_office.window('<?=($map_inside ? 'all' : $v['office_mode'])?>');"><?=$v['office_link']?></span>
<?					} ?>
				</td>

<?				if (!isset($v['error'])) { ?>

<?				if ($price_long === '') { ?>
				<td class="edost_format_price" width="<?=$day_width?>" align="center">
					<label for="<?=$id?>"><span class="edost_format_price edost_day"><?=(!empty($v['day']) ? $v['day'] : '')?></span></label>
				</td>
<?				} ?>

				<td class="edost_format_price" width="<?=(($price_long != '' ? $day_width : 0) + $price_width)?>" align="right">
					<label for="<?=$id?>">
<?					if (isset($v['free'])) { ?>
					<span class="edost_format_price edost_price_free" style="<?=($price_long == 'light' ? 'opacity: 0.5;' : '')?>"><?=$v['free']?></span>
<?					} else { ?>
					<span class="edost_format_price edost_price" style="<?=($price_long == 'light' ? 'opacity: 0.5;' : '')?>"><?=(isset($v['priceinfo_formatted']) ? $v['priceinfo_formatted'] : $v['price_formatted'])?></span>
<?					} ?>
					</label>
				</td>

<?				if ($cod_td) { ?>
				<td class="edost_format_price" width="<?=$cod_width?>" align="right">
<?					if (isset($v['pricecod']) && $v['pricecod'] >= 0) { ?>
					<label for="<?=$id?>"><span class="edost_price_head edost_payment"><?=(isset($v['cod_free']) ? $v['cod_free'] : $v['pricecod_formatted'])?></span></label>
<?					} ?>
				</td>
<?				} ?>

<?				} ?>
			</tr>

<?			if (isset($v['company_head'])) { ?>
			<tr>
				<td colspan="5"<?=($cod_tariff ? ' style="padding-top: 2px;"' : '')?>>
					<span class="edost_format_company_head"><?=$v['company_head']?>: </span>
					<span class="edost_format_company"><?=$v['company']?></span>
					<?=($v['name'] != '' ? '<span class="edost_format_company_name"> ('.$v['name'].')</span>' : '')?>
				</td>
			</tr>
<?			} ?>

<?			if (!empty($v['office_address'])) { ?>
			<tr>
				<td colspan="5"<?=($cod_tariff && $office_map != 'get' ? ' style="padding-top: 2px;"' : '')?>>
					<span class="edost_format_address_head"><?=$sign['address']?>: </span>
					<span class="edost_format_address"><?=$v['office_address']?></span>

<?					if ($office_map == 'change') { ?>
					<br><span class="edost_format_link" onclick="edost_office.window('<?=($map_inside ? 'all' : $v['office_mode'])?>');"><?=$v['office_link']?></span>
<?					} else { ?>
					<a class="edost_link" href="http://www.edost.ru/office.php?c=<?=$v['office_id']?>" target="_blank"><?=$v['office_link']?></a>
<?					} ?>
				</td>
			</tr>
<?			} ?>

<?			if (!empty($v['description']) || !empty($v['warning']) || !empty($v['error'])) { ?>
			<tr>
				<td colspan="5">
<?					if (!empty($v['error'])) { ?>
					<div class="edost_format_description edost_warning"><b><?=$v['error']?></b></div>
<?					} ?>

<?					if (!empty($v['warning'])) { ?>
					<div class="edost_format_description edost_warning"><?=$v['warning']?></div>
<?					} ?>

<?					if (!empty($v['description'])) { ?>
					<div class="edost_format_description edost_description"><?=nl2br($v['description'])?></div>
<?					} ?>
				</td>
			</tr>
<?			} ?>
		</table>
<?		} ?>
	</div>
<?	} ?>
	</div>


<?	if (!$edost_catalogdelivery) { ?>
	<input name="BUYER_STORE" id="BUYER_STORE" value="<?=$arResult["BUYER_STORE"]?>" type="hidden">
<?	} ?>

<?	if (!empty($data['format']['active']['id'])) { ?>
	<input id="edost_delivery_id" value="<?=$data['format']['active']['id']?>" type="hidden">
<?	} ?>

<?	if (!empty($data['format']['map_json'])) { ?>
	<input id="edost_office_data" value='{"ico_path": "<?=$ico_path?>", <?=$data['format']['map_json']?>}' type="hidden">
<?	} ?>

<?	if ($edost_catalogdelivery && $map_inside != '') { ?>
	<script type="text/javascript">
		if (window.edost_office) edost_office.map = false;
		if (window.edost_office2) edost_office2.map = false;
<?		if ($map_inside == 'Y' && $bookmark == '') { ?>
		edost_MapInside();
<?		} ?>
	</script>
<?	} ?>

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




<?
// блок для вывода ошибки "Расчет недоступен"
if (!empty($location_id) && empty($arResult['edost']['format']['data']) && ($mode == 'manual' && empty($param['disable']) || $mode == 'window')) { ?>
<div id="edost_catalogdelivery_window_error" class="edost_warning2"></div>
<? }


if ($mode == 'manual') { ?>
		</div>
	</div>
<script type="text/javascript">
	edost_RunScript('param', 'inside');
</script>
<? }


// заполнение блока 'inside' и города
if ($inside_data !== false) { ?>
<script type="text/javascript">
	edost_RunScript('data', '<?=str_replace("'", "\'", $inside_data)?>', '<?=str_replace("'", "\'", $location_data)?>', '<?=$city?>', '<?=(!empty($arResult['detailed']) ? 'Y' : 'N')?>');
	$("a[href='#popup-contacts']").fancybox({
		afterShow: function(){
			$("body").addClass("popup-open");
			var params = <?=CUtil::PhpToJsObject(\Sib\Core\Edost::getPopupMapParams());?>;
			if(!$('#popup-map').hasClass('loaded')){
				var script = document.createElement('script');
				script.src = params.SRC;
				script.async = true;
				$('#popup-map').append(script);
				$('#popup-contacts .popup__title').text(params.HEAD);
				$('#popup-map').addClass('loaded');
				setTimeout(() => {
					if(Modernizr.mq('(max-width: 731px)')){
						var calcHeight = $(window).height() - 76 - $('#popup-contacts .popup__title').height();
						$('#popup-map').css({height: calcHeight + 'px'});
					}
				}, 300);
			}
		},
		beforeClose: function () {
			$("body").removeClass("popup-open");
		},
		touch: false
	});
</script>
<? } ?>
