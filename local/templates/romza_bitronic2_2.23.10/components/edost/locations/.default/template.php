<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();?>
<?

$sign = GetMessage('EDOST_LOCATIONS_SIGN');

if ($arResult['mode'] == 'city') {
	CUtil::InitJSCore(array('ajax'));
?>
<style>
	div.edost_loading { padding-top: 4px; }
	div.edost_loading span, div.edost_loading img { vertical-align: middle; font-weight: bold; color: #888; font-size: 12px; }
	span.edost_loading_small img, span.edost_loading_small span { vertical-align: middle; padding: 0 2px 0 0; font-size: 14px; font-weight: bold; color: #888; }

	div.edost_suggest_div { display: none; z-index: 1000; background: #FFF; position: absolute; text-align: left; margin: -1px 0 0 0px; box-shadow: 0px 2px 10px 0 #888; }
	div.edost_suggest_head { padding: 4px; margin: 0px; background: #AAA; color: #FFF; font-weight: bold; size: 12px; }
	div.edost_suggest_data { border: 1px solid #888; }
	div.edost_suggest_active { background: #C8E5FF; }
	div.edost_suggest_value { font-size: 16px; color: #000; padding: 3px; margin: 0px; cursor: pointer; }
	div.edost_suggest_value span { color: #888; }
	div.edost_suggest_value:hover { color: #00F; }
	div.edost_suggest_value:hover span { color: #88F; }

	div.edost_suggest_hint { padding: 6px; margin: 0px; background: #FFD; size: 12px; border-width: 0 1px 1px 1px; border-color: #AAA; border-style: solid; }
	div.edost_suggest_warning { padding: 6px; margin: 0px; background: #FFE0E0; size: 12px; border: 1px solid #F00; }
	div.edost_suggest_bold { font-weight: bold; }

	img.edost_flag { vertical-align: middle; padding-right: 5px; width: 18px; height: 12px; }
	input.edost_city { width: 100%; max-width: 610px; }
	div.edost_city_hint { font-size: 13px; color: #F00; }
	div.edost_address { font-size: 12px; display: inline-block; padding-right: 4px; }
	div.edost_country_list { padding-bottom: 4px; }
	span.edost_city_name { vertical-align: middle; font-weight: bold; font-size: 14px; }
	span.edost_city_name span { color: #888; }
	span.edost_city_link { cursor: pointer; color: #A00; font-size: 14px; font-weight: bold; display: block; }
	div.edost_address_delimiter2 { padding-top: 10px; }
	input.edost_input { margin-top: 2px; }
	div.edost_location_warning { color: #F00; font-size: 12px; font-weight: bold; }

	select.edost_select { width: 300px; margin: 0 0 4px 0; }
	option.edost_select_head { color: #F00; }
	option.edost_select_no { color: #888; }

	div.edost_location_delimiter { display: inline-block; cursor: default; padding: 2px 0; margin: 0; border-width: 0 1px 0 0; border-color: #AAA; border-style: solid; }
	div.edost_country_active { display: inline-block; cursor: default; font-weight: bold; padding: 2px 8px 2px 12px; margin: 0; }
	div.edost_country_active span, div.edost_country_active img { vertical-align: middle; }
	div.edost_country_active_off { vertical-align: top; color: #BBB; }
	div.edost_country_active_off span { color: #BBB; }
	div.edost_country_active_off img { opacity: 0.5; }
	div.edost_country_active_off:hover img { opacity: 0.8; }
	div.edost_country_active_off:hover span { color: #888; }
</style>

<script type="text/javascript">
	function edost_SetLoading(id, s) {
		<? if ($arResult['edost_catalogdelivery'] && $arResult['edost_delivery']) { ?>
		edost_catalogdelivery.calculate('loading');
		<? } else { ?>
		var E = document.getElementById(id);
		if (!E) return;
		E.innerHTML = '<div class="edost_loading"><img src="<?=$arResult['component_path']?>/images/<?=$arResult['loading']?>" width="20" height="20" border="0"> <span>' + s + '</span></div>';
		if (id != 'edost_location_zip_hint') E.style.display = (s == '' ? 'none' : 'block');
		<? } ?>
	}

	function edost_SetLocationID(id) {
		var E = document.getElementById('edost_shop_LOCATION');
		if (!E) return;
		E.value = id;
		edost_SetLoading('edost_location_city_loading', '<?=$sign['submit']?>');
		edost_location.disable();
		edost_location.set_cookie(id);

		<?=($arResult['edost_catalogdelivery'] ? 'edost_catalogdelivery.calculate();' : 'submitForm();')?>
	}

<?	if (!empty($arResult['edost_delivery'])) { ?>
	function edost_SetCountry(id, list) {

		id = id.split('_');
		var id_edost = id[0];
		var set = (id[2] != undefined && id[2] == 'set' ? id[1] : false);
		id = id[0] + '_' + id[1];

		edost_location.location(['', '', id, '', '']);

		if (set) {
			var E = document.getElementById('edost_city_div');
			if (E) E.style.display = 'none';
			edost_SetLocationID(set);
			return;
		}

		if (list) {
			var ar = BX.findChildren(BX('edost_country_list_div'), {'tag': 'div'}, true);
			for (var i = 0; i < ar.length; i++) if (ar[i].id) {
				var code = ar[i].id.split('_');
				code = code[3] + '_' + code[4];
				ar[i].className = 'edost_country_active' + (code == id ? '' : ' edost_country_active_off');
			}
		}

		var E = document.getElementById('edost_city_hint_data');
		if (E) {
			var s = '';
			var ar = E.value.split(';');
			for (var i = 0; i < ar.length; i++) {
				var v = ar[i].split('=');
				if (v[1] == undefined) continue;
				if (i == 0 || v[0] == id_edost) s = v[1];
			}
			var E = document.getElementById('edost_city_hint');
			if (E) E.innerHTML = s;
		}

		var E = document.getElementById('edost_city');
		if (E && E.type != 'hidden') E.focus();

	}
<?	} ?>

	function edost_SetZip(value, full, submit) {

		edost_SetZipWarning('reset');

		var reset = false;
		var original = false;
		if (full == 'original') {
			full = false;
			original = true;
		}
		else if (full == 'reset') {
			full = false;
			reset = true;
		}
		else if (full == undefined) {
			full = true;
			submit = true;
		}

		var E = document.getElementById('edost_zip_full');
		if (E) E.value = (value != '' && full ? 'Y' : '');

		var E_zip = document.getElementById('edost_zip');
		if (E_zip && !original && (full || reset)) E_zip.value = value;

		var E = document.getElementById('edost_shop_ZIP');
		if (!E) return;
		if (!reset && (E.value == value || E.value != '' && E.value != '.' && value == '')) return;
		E.value = value;

		if (!submit) return;

		if (E_zip && E_zip.type != 'hidden') edost_SetZipWarning('submit');
		else edost_SetLoading('edost_location_address_loading', '<?=$sign['submit']?>');

		edost_location.disable();
		edost_location.set_cookie();

		submitForm();
		return true;

	}

	function edost_SetZipWarning(s) {

		var id = 'edost_location_zip_hint';
		if (s == 'checking') edost_SetLoading(id, '<?=$sign['zip']['checking']?>');
		else if (s == 'submit') edost_SetLoading(id, '<?=$sign['submit']?>');
		else {
			if (s != '') {
				var E = document.getElementById('edost_zip_warning');
				if (E) {
					if (s == 'reset') E.value = '';
					else E.value = (s == '2' ? 2 : 1);
				}
			}

			if (s == '1') s = '<?=$arResult['zip_warning'][1]?>';
			else if (s == '2') s = '<?=$arResult['zip_warning'][2]?>';
			else if (s == 'digit') s = '<?=$arResult['zip_warning']['digit']?>';
			else if (s == 'format') s = '<?=$arResult['zip_warning']['format']?>';
			else if (s == 'reset') s = '';

			var E = document.getElementById(id);
			if (!E) return;
			if (s != '') s = '<div class="edost_location_warning">' + s + '</div>';
			E.innerHTML = s;
		}

	}

	function edost_SetCity2(name) {
		var E = document.getElementById('edost_city2');
		if (E) E.value = name;
	}

	function edost_SetLocation(id, edost_delivery, city, region_id, country_id, get_zip) {
//		alert(id + ' | ' + edost_delivery + ' | ' + country_id + ' | ' + region_id + ' | ' + city + ' | ' + get_zip);

		<? if (!$arResult['get_zip']) echo 'get_zip = false;'; ?>

		id = id.split('|');
		var select = (id[1] != undefined ? id[1] : false);
		id = id[0].split('_');
		var set = (id[1] != undefined && id[1] == 'set' ? true : false);
		id = id[0];

		if (select !== false) for (var i = select*1+1; i <= 5; i++) {
			var E = document.getElementById('edost_location_' + i + '_select');
			if (E) E.style.display = 'none';
		}

		if (set || edost_delivery && country_id != undefined) {
			edost_location.disable();
			var E = document.getElementById('edost_city_hint');
			if (E) E.innerHTML = '';
			var E = document.getElementById('edost_city');
			if (E) E.disabled = true;
		}

		if (set || edost_delivery && id != '' && country_id != undefined) {
			edost_SetZip('', 'reset');
			edost_SetCity2('');
			edost_SetLocationID(id);
			return;
		}

		var change = (!edost_delivery && select === false || edost_delivery && country_id == undefined ? true : false);
		if (change) {
			var E = document.getElementById('edost_location_city_zip_div');
			if (E) E.style.display = 'none';

			var E = document.getElementById('edost_location_address_div');
			if (E) E.style.display = 'none';

			var E = document.getElementById('edost_location_address_head');
			if (E) E.style.display = 'none';

			edost_SetZip('', 'reset');
		}
		edost_SetLoading('edost_location_city_' + (change ? 'div' : 'loading'), country_id == undefined ? '<?=$sign['loading2']?>' : '<?=$sign['submit']?>');

		var param = 'type=html&id=' + id + '&edost_delivery=' + (edost_delivery ? 'Y' : 'N');
		if (country_id != undefined) param += '&country=' + encodeURIComponent(country_id) + '&' + 'region=' + encodeURIComponent(region_id) + '&city=' + encodeURIComponent(city) + (get_zip === false ? '&get_zip=N' : '');
		BX.ajax.post('<?=$arResult['component_path']?>/edost_location.php', param, function(r) {
			edost_SetLoading('edost_location_city_loading', '');

			if (r.indexOf('{') == 0) {
				var v = (window.JSON && window.JSON.parse ? JSON.parse(r) : eval('(' + r + ')'));
				if (v.error_string != undefined) r = '<div class="edost_location_warning">' + v.error_string + '</div>';
				else {
					edost_SetCity2(v.city2 != undefined ? city : '');
					if (edost_delivery && v.zip) edost_SetZip(v.zip, v.zip_full != undefined ? true : false);

					edost_SetLocationID(v.id);
					return;
				}
			}

			var E = document.getElementById('edost_location_city_div');
			if (E) E.innerHTML = r;

			var E = document.getElementById('edost_city');
			if (E && E.type != 'hidden') E.focus();
		});
	}

	function edost_LocationAjax(name, value, param) {
		BX.ajax.post('<?=$arResult['component_path']?>/edost_location.php', param, function(result) { edost_location.ajax(name, value, result) });
	}
</script>
<?
}

if (!empty($arResult['hidden'])) echo $arResult['hidden'];
?>

<div id="edost_location_<?=$arResult['mode']?>_div">

<?
if ($arResult['mode'] == 'city') {
	if (isset($arResult['location']['city'])) {
		$region = (!empty($arResult['location']['region']) ? ' <span>('.$arResult['location']['region'].')</span>' : '');
		$country = (!empty($arResult['location']['show_country']) ? ', '.$arResult['location']['country'] : '');
		if (isset($arResult['location']['flag'])) echo '<img class="edost_flag" src="/bitrix/images/delivery_edost_img/flag/'.$arResult['location']['flag'].'.gif" border="0">';
?>
		<span class="edost_city_name"><?=$arResult['location']['city']?><?=$region?><?=$country?></span>
		<span class="edost_city_link rbs-edost-onclick" 
			<?//onclick="edost_SetLocation('//=(!empty($arResult['location']['main_id']) ? $arResult['location']['main_id'] : '')."'".(!empty($arResult['edost_delivery']) ? ', true' : ''))"?>
			onclick=""
		><?=$sign['change']?></span>
		<script>
			$onClick = "OpenVregionsPopUp('open', 'vregions-popup', 'vregions-sepia'); return false;"
			if($('.vr-template').data('rand') > 0)
			{
				$onClick = $onClick.replace('vregions-popup', 'vregions-popup' + $('.vr-template').data('rand'));
				$onClick = $onClick.replace('vregions-sepia', 'vregions-sepia' + $('.vr-template').data('rand'));
				$('.rbs-edost-onclick').attr('onclick', $onClick);
			}	
		</script>
<?
	}
	if (isset($arResult['city_data'])) echo $arResult['city_data'];
}

if (!empty($arResult['address_field'])) {
	$a = ($arResult['mode'] == 'city' ? true : false);
	if ($arResult['mode'] == 'city') {
		echo '</div><div id="edost_location_'.$arResult['mode'].'_zip_div">';
	}
	foreach ($arResult['address_field'] as $k => $v) if (!empty($v['hidden'])) {
		echo '<input type="hidden" id="edost_'.$k.'" name="edost_'.$k.'" value="'.(!empty($v['value']) ? $v['value'] : '').'">';
	}
	else if (isset($v['name'])) {
		if (!empty($v['enter']) && $a) echo '<div class="edost_address_delimiter2"></div>';
		$a = true;
?>
		<div class="edost_address">
			<?=$v['name']?><br>
			<input type="text" style="<?=(empty($v['style']) ? 'width: '.$v['width'].'px;' : $v['style'])?>" class="edost_input" maxlength="<?=(!empty($v['max']) ? $v['max'] : 6)?>" value="<?=(isset($v['value']) ? $v['value'] : '')?>" id="edost_<?=$k?>" name="edost_<?=$k?>"<?=(!empty($v['suggest']) ? ' autocomplete="off" onfocus="edost_location.suggest(this.id, \'start\');" onblur="edost_location.suggest(this.id, \'hide\')" onkeydown="edost_location.keydown(this.id, event);"><br><div class="edost_suggest_div" id="edost_'.$k.'_suggest_div"></div>' : '>')?>
		</div>
<?
		if (isset($v['hint']) && $v['hint'] !== false) {
?>
		<div class="edost_address" id="<?='edost_location_'.$k.'_hint'?>">
            <div class="edost_location_warning"><?=$v['hint']?></div>
		</div>
<?
		}

		if (!empty($v['delimiter'])) echo '<div class="edost_address edost_address_delimiter"'.(!empty($v['delimiter_style']) ? ' style="'.$v['delimiter_style'].'"' : '').'></div>';
	}
}
?>

</div>

<div id="edost_location_<?=$arResult['mode']?>_loading" style="display: none;"></div>
