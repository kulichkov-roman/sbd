<?
class CEdostModifySaleOrderAjax {

	// подключение класса edost + языкового файла
	public static function EdostDelivery() {
		if (class_exists('edost_class')) return;
		$s = 'modules/edost.delivery/classes/general/delivery_edost.php';
		$s = $_SERVER['DOCUMENT_ROOT'].(version_compare(SM_VERSION, '15.0.0') >= 0 ? getLocalPath($s) : '/bitrix/'.$s);
		IncludeModuleLangFile($s);
		require_once($s);
	}

	// загрузка настроек модуля edost
	public static function GetEdostConfig($site_id) {
		if (isset($arResult['edost']['config'])) return $arResult['edost']['config'];
		self::EdostDelivery();
		return CDeliveryEDOST::GetEdostConfig($site_id);
	}

	// проверка наличия в заказе доставки и наложенного платежа edost
	public static function CheckOrderDevileryEdostAndEdostPayCod($arOrder) {
		self::EdostDelivery();
		if (isset($arOrder['PAY_SYSTEM_ID']) && isset($arOrder['PERSON_TYPE_ID']) && !empty($arOrder['DELIVERY_ID']) && CDeliveryEDOST::GetEdostProfile($arOrder['DELIVERY_ID']) !== false) {
			$dbPaySystem = CSalePaySystem::GetList(array('SORT' => 'ASC', 'PSA_NAME' => 'ASC'), array('ACTIVE' => 'Y', 'PERSON_TYPE_ID' => $arOrder['PERSON_TYPE_ID'], 'PSA_HAVE_PAYMENT' => 'Y'));
			while ($arPaySystem = $dbPaySystem->Fetch()) if ($arPaySystem['ID'] == $arOrder['PAY_SYSTEM_ID']) {
				if (substr($arPaySystem['PSA_ACTION_FILE'], -11) == 'edostpaycod') return true;
				break;
			}
		}
		return false;
	}


	// отмена отправки письма с напоминанием об оплате заказа, если выбран наложенный платеж edost
	function OnSCOrderRemindSendEmail($OrderID, &$eventName, &$arFields) {
		if ($eventName == 'SALE_ORDER_REMIND_PAYMENT') {
			$arOrder = CSaleOrder::GetByID($OrderID);
			if (self::CheckOrderDevileryEdostAndEdostPayCod($arOrder)) return false;
		}
		return true;
	}


	// установка статуса нового заказа, если выбран наложенный платеж edost
	function OnSCBeforeOrderAdd(&$arOrder) {
		if (self::CheckOrderDevileryEdostAndEdostPayCod($arOrder)) {
			$config = self::GetEdostConfig(isset($arOrder['SITE_ID']) ? $arOrder['SITE_ID'] : '');
			if ($config['cod_status'] != '') $arOrder['STATUS_ID'] = $config['cod_status'];
		}
	}


	// вызывается при сохранении заказа
	function OnSaleOrderBeforeSaved(Bitrix\Sale\Order $order, $old_values = false) {
//		echo '<br><b>old_values:</b> <pre style="font-size: 12px">'.print_r($old_values, true).'</pre>';

		if (version_compare(SM_VERSION, '16.0.12') <= 0) return;

		$mode = '';
		$s = $GLOBALS['APPLICATION']->GetCurPage();
		if ($s == '/bitrix/admin/sale_order_edit.php') $mode = 'order_edit'; // редактирование заказа
		if ($s == '/bitrix/admin/sale_order_shipment_edit.php') $mode = 'shipment_edit'; // редактирование отгрузки
		if ($s == '/bitrix/admin/sale_order_ajax.php') $mode = 'order_ajax'; // изменение разрешения доставки, отгрузки и идентификатора отправления
//		if ($s == '/bitrix/admin/sale_order_create.php') $mode = 'order_create'; // новый заказ
		$order_paid = (!empty($old_values['PAYED']) ? true : false); // изменение флага "заказ оплачен"


		// оформление нового заказа
		if ($order->isNew() && strpos($s, '/admin/') === false) {
			$config = self::GetEdostConfig(SITE_ID);
			if (empty($config['template']) || $config['template'] != 'N3') return;
			if (!class_exists('SaleOrderAjax') || !method_exists('SaleOrderAjax', 'getCurrentShipment')) return;

			$shipment = SaleOrderAjax::getCurrentShipment($order);
			if ($shipment === false) return;

			$delivery_id = $shipment->getDeliveryId();

			$tariff = CDeliveryEDOST::GetEdostProfile($delivery_id, true);
			if ($tariff === false) return;

			$props = edost_class::GetProps($order, array('order', 'no_location'));
//			echo '<br><b>props:</b> <pre style="font-size: 12px">'.print_r($props, true).'</pre>';
			$tariff = CDeliveryEDOST::GetEdostTariff($tariff['profile'], !empty($props['office']['type']) ? $props['office']['type'] : 0);
//			echo '<br><b>tariff:</b> <pre style="font-size: 12px">'.print_r($tariff, true).'</pre>';

			// установка стоимости доставки для эксклюзивных офисов и наложенного платежа
			$price = (!empty($tariff['priceoffice_active']) ? $tariff['price'] : -1);
			if (!empty($props['cod'])) $price = $tariff['pricecash'];
			if ($price >= 0) {
				$ar = $order->getPaymentCollection();
				if (count($ar) != 1) return;

				$base_currency = CDeliveryEDOST::GetRUB();
				$r = $shipment->setFields(array('CUSTOM_PRICE_DELIVERY' => 'Y', 'PRICE_DELIVERY' => edost_class::GetPrice('value', $price, $base_currency, $order->getCurrency())));
				if (!$r->isSuccess()) return new \Bitrix\Main\EventResult(\Bitrix\Main\EventResult::ERROR, null, null, 'sale');

				$payment = $ar->rewind();
				$payment->setField('SUM', $order->getPrice());
			}

			return;
		}


		if ($mode == '' && !$order_paid) return;

		$site_id = $order->getSiteId();
		$config = self::GetEdostConfig($site_id);
		if ($config['control'] != 'Y') return;

		$props = false;
		$props_new = array();
		$shipment_id = array();

		// редактирование заказа + изменение флага "заказ оплачен"
		if ($mode == 'order_edit' || $order_paid) {
			foreach ($order->getShipmentCollection() as $v) if (!$v->isSystem()) $shipment_id[] = $v->getId();
			$props = edost_class::GetProps($order->getId(), array('no_payment'));

			if ($mode == 'order_edit') {
				$p = edost_class::GetProps($order, array('order', 'no_payment'));
				$props_new['location_code'] = (!empty($p['location_code']) ? $p['location_code'] : '');
			}
			if ($order_paid) {
				$props_new['order_paid'] = $order->isPaid();
			}
		}

		// изменение разрешения доставки и идентификатора отправления через ajax
		if ($mode == 'order_ajax' && !empty($_REQUEST['shipmentId']) && isset($_REQUEST['action']) && in_array($_REQUEST['action'], array('updateShipmentStatus', 'saveTrackingNumber'))) {
			$shipment_id = array(intval($_REQUEST['shipmentId']));
			$props = edost_class::GetProps($shipment_id[0], array('shipment'));

			if (isset($_REQUEST['field']) && $_REQUEST['field'] == 'ALLOW_DELIVERY' && isset($_REQUEST['status']) && $_REQUEST['status'] == 'Y') $props_new['allow_delivery'] = true;
			if (isset($_REQUEST['trackingNumber'])) $props_new['tracking_code'] = $_REQUEST['trackingNumber'];
		}

		// редактирование отгрузки
		if ($mode == 'shipment_edit' && !empty($_REQUEST['shipment_id'])) {
			$shipment_id = array(intval($_REQUEST['shipment_id']));
			$props = edost_class::GetProps($shipment_id[0], array('shipment'));

			// сохранение пункта выдачи в поле 'ADDRESS'
			if (isset($_REQUEST['edost_address'])) {
				$address = $_REQUEST['edost_address'];
				$office = edost_class::ParseOfficeAddress($address);
				if (!empty($office)) {
					$ar = $order->getPropertyCollection();
					foreach ($ar->getGroups() as $v) foreach ($ar->getGroupProperties($v['ID']) as $v2) {
						$code = $v2->getField('CODE');
						if ($code == 'ADDRESS') $v2->setValue($address);
					}
				}
			}

			// сохранение платежной системы
			if (!empty($_REQUEST['edost_payment'])) {
				$payment_id = intval($_REQUEST['edost_payment']);
				$ar = $order->getPaymentCollection();
				$payment = $ar->rewind();

				$ar = \Bitrix\Sale\PaySystem\Manager::getObjectById($payment_id);
				if (!empty($ar)) {
					$s = $ar->getField('NAME');
					if (!empty($s)) {
						$payment->setField('PAY_SYSTEM_NAME', $s);
						$payment->setField('PAY_SYSTEM_ID', $payment_id);
					}
				}
			}

			$ar = array('delivery_id' => 'PROFILE', 'tracking_code' => 'TRACKING_NUMBER', 'allow_delivery' => 'ALLOW_DELIVERY');
			foreach ($ar as $k => $v) if (isset($_REQUEST['SHIPMENT']['1'][$v])) $props_new[$k] = $_REQUEST['SHIPMENT']['1'][$v];
			if (isset($props_new['allow_delivery'])) $props_new['allow_delivery'] = ($props_new['allow_delivery'] == 'Y' ? true : false);

			$props['shipment_flag'] = (isset($_REQUEST['edost_shipment_flag_start']) ? $_REQUEST['edost_shipment_flag_start'] : '0');
			$props_new['shipment_flag'] = (isset($_REQUEST['edost_shipment_flag']) ? $_REQUEST['edost_shipment_flag'] : '0');
		}

//		echo '<br><b>props:</b> <pre style="font-size: 12px">'.print_r($props, true).'</pre>';
//		echo '<br><b>props_new:</b> <pre style="font-size: 12px">'.print_r($props_new, true).'</pre>';
//		echo '<br><b>shipment_id:</b> <pre style="font-size: 12px">'.print_r($shipment_id, true).'</pre>';

		// автоматическая постановка на контроль и обновление данных при изменении параметров + снятие с контроля (если изменился тариф или удалили идентификатор отправления)
		if (!empty($props_new) && !empty($shipment_id)) {
			$a = false;
			foreach ($props_new as $k => $v) if (!isset($props[$k]) || $v != $props[$k]) { $a = true; break; }
			if ($a) {
				$data = edost_class::Control();
				$c = false;
				foreach ($shipment_id as $k2 => $v2) {
					if (isset($data['data'][$v2])) $c = true;
					else if ($mode == 'order_edit' || $order_paid) unset($shipment_id[$k2]);
				}
				if (empty($shipment_id)) return;

				$flag = (isset($props['shipment_flag']) && $props_new['shipment_flag'] != $props['shipment_flag'] ? $props_new['shipment_flag'] : false);

				if (!isset($props_new['allow_delivery'])) $props_new['allow_delivery'] = $props['allow_delivery'];
				if (!isset($props_new['tracking_code'])) $props_new['tracking_code'] = $props['tracking_code'];

				$a = false;
				if ($c) {
					if (empty($props_new['tracking_code'])) $flag = 0;
				}
				else {
					if ($flag !== false) $a = true;
					else if ($config['control_auto'] == 'Y' && !empty($props_new['allow_delivery']) && ($props_new['allow_delivery'] != $props['allow_delivery'] || $props_new['tracking_code'] != $props['tracking_code'])) $a = true;
				}

				if (!$c && !$a) return;

				if ($mode == 'order_edit' || $order_paid) {
					$ar = array();
					foreach ($shipment_id as $v2) $ar[] = $order->getShipmentCollection()->getItemById($v2);
					$data = edost_class::Control($ar);
				}
				else {
					$shipment = $order->getShipmentCollection()->getItemById($shipment_id[0]);
					$delivery_id = $shipment->getDeliveryId();
					$tariff = CDeliveryEDOST::GetEdostProfile($delivery_id, true);

					$a = true;
					if ($tariff === false)
						if ($c) $flag = 0;
						else $a = false;

					if ($a) $data = edost_class::Control($shipment, $flag);
				}
			}
		}

	}


	// вызывается после обработки платежной системы при расчете заказа в DoCalculateOrder (old)
	function OnSCCalculateOrderPaySystem(&$arOrder) {

		if (!class_exists('CDeliveryEDOST')) return;

		$tariff = CDeliveryEDOST::GetEdostProfile($arOrder['DELIVERY_ID']);
		if ($tariff === false) return;

		$tariff = CDeliveryEDOST::GetEdostTariff($tariff['profile']);
		$priceoffice = false;
		if (!empty($tariff['priceoffice']) && !empty($arOrder['ORDER_PROP'])) {
			$props = array();
			$ar = CSaleOrderProps::GetList(array(), array(), false, false, array('ID', 'CODE'));
			while ($v = $ar->GetNext()) if ($v['CODE'] == 'ADDRESS') $props[] = $v['ID'];
			if (!empty($props)) foreach ($arOrder['ORDER_PROP'] as $k => $v) if (in_array($k, $props)) {
				$priceoffice = edost_class::GetOfficePrice($tariff['priceoffice'], '', $v);
				break;
			}
		}

		$price = ($priceoffice !== false ? $priceoffice['price'] : -1);
		if (self::CheckOrderDevileryEdostAndEdostPayCod($arOrder)) {
			$price = ($priceoffice !== false ? $priceoffice['pricecash'] : $tariff['pricecash']);
			if ($price < 0) $price = 0; // для выбранного тарифа наложенный платеж недоступен
		}
		if ($price >= 0) {
			$base_currency = CDeliveryEDOST::GetRUB();
			$arOrder['DELIVERY_PRICE'] = edost_class::GetPrice('value', $price, $base_currency, $arOrder['CURRENCY']);
			$arOrder['PRICE_DELIVERY'] = $arOrder['DELIVERY_PRICE'];
		}

	}

	// вызывается после подтверждения заказа (old)
	function OnSCOrderOneStepComplete($ID, $arOrder) {
	}


	// вызывается в начале каждой страницы
	function OnBeforeProlog() {

		if ($GLOBALS['APPLICATION']->GetCurPage() != '/bitrix/admin/sale_order_new.php') return; // старое редактирование заказа
		if (version_compare(SM_VERSION, '12.5.0') < 0) return; // редактирование заказа с присвоением через 'DELIVERY_SELECT' (начиная с bitrix 12.5)
		if (empty($_GET['ID'])) return;

		self::EdostDelivery();

		// данные по выбранному офису в поле c адресом оформленного заказа
		$ar = CSaleOrderPropsValue::GetOrderProps($_GET['ID']);
		while ($v = $ar->Fetch()) if ($v['CODE'] == 'ADDRESS') {
			$office = edost_class::ParseOfficeAddress($v['VALUE']);
			if (is_array($office)) $_SESSION['EDOST']['admin_order_edit_office'][$_REQUEST['ID']] = array('id' => 'edost:'.$office['profile'], 'profile' => $office['profile'], 'office_id' => $office['id']);
			break;
		}

	}


	// вызывается в админке перед выводом формы редактирования
	function OnAdminTabControlBegin(&$form) {

		$mode = '';
		$s = $GLOBALS['APPLICATION']->GetCurPage();
		if ($s == '/bitrix/admin/sale_order_view.php') $mode = 'order_view'; // просмотр заказа
		if ($s == '/bitrix/admin/sale_order_shipment_edit.php') $mode = 'shipment_edit'; // редактирование отгрузки
		if ($s == '/bitrix/admin/sale_order_new.php') $mode = 'order_new'; // старое редактирование заказа
		if ($mode == '') return;

		$config = self::GetEdostConfig('');
//		echo '<br><b>_REQUEST:</b> <pre style="font-size: 12px">'.print_r($_REQUEST, true).'</pre>';

		// просмотр заказа + редактирование отгрузки
		if ($mode == 'shipment_edit' && $config['admin'] == 'Y' || $mode == 'order_view' && $config['control'] == 'Y') {
			if (version_compare(SM_VERSION, '16.0.12') <= 0) return;

			$ar = array();
			if ($mode == 'shipment_edit') $ar = array(
				'ORDER_ID' => (!empty($_REQUEST['order_id']) ? $_REQUEST['order_id'] : ''),
				'SHIPMENT_ID' => (!empty($_REQUEST['shipment_id']) ? $_REQUEST['shipment_id'] : ''),
			);

			$GLOBALS['APPLICATION']->IncludeComponent('edost:delivery', '', array('MODE' => 'order_edit', 'ADMIN' => 'Y') + $ar, null, array('HIDE_ICONS' => 'Y'));
		}

		// редактирование заказа (old)
		if ($mode == 'order_new' && $config['admin'] == 'Y') {
			if (version_compare(SM_VERSION, '12.5.0') < 0) return; // редактирование заказа с присвоением через 'DELIVERY_SELECT' (начиная с bitrix 12.5)

			$date = date('dmY');
			$protocol = CDeliveryEDOST::GetProtocol();
			$map_link = $protocol.'edostimg.ru/shop/';
			$GLOBALS['APPLICATION']->SetAdditionalCSS($map_link.'office.css?a='.$date);
			$GLOBALS['APPLICATION']->AddHeadString('<script type="text/javascript" src="'.$map_link.'office.js?a='.$date.'" charset="utf-8"></script>');

			$sign = GetMessage('EDOST_DELIVERY_SIGN');

			// поля с адресом (может быть несколько - для физ и юр лиц)
			$address = array();
			$ar = CSaleOrderProps::GetList(array(), array(), false, false, array('ID', 'CODE')); //'TYPE', 'IS_LOCATION'
			while ($v = $ar->GetNext()) if ($v['CODE'] == 'ADDRESS') $address[] = $v['ID'];

			// модуль наложенного платежа edost
			$edostpaycod = false;
			$ar = CSalePaySystem::GetList(array('SORT' => 'ASC', 'PSA_NAME' => 'ASC'), array('ACTIVE' => 'Y'), false, false, array('ID', 'NAME', 'PSA_ACTION_FILE'));
			while ($v = $ar->Fetch()) if (substr($v['PSA_ACTION_FILE'], -11) == 'edostpaycod') { $edostpaycod = $v['ID']; break; }

?>
<input autocomplete="off" id="edost_office_data" value="" type="hidden">

<style>
	#DELIVERY_ID optgroup option { padding: 1px 3px 1px 15px; }
</style>

<script type="text/javascript">

	var edost_address = '';
	var edost_address_id = false;

	function edost_InsertData(select) {

		if (typeof select != 'object') {
			var E = document.createElement('DIV');
			E.innerHTML = select;
			select = E.firstChild;
		}
		if (!select) return;

		var selected = select.value;

		// получение отформатированных тарифов edost
		BX.ajax.post('/bitrix/components/edost/delivery/edost_delivery.php', 'mode=order_edit&id=' + orderID, function(data) {
			var E = BX('DELIVERY_ID');
			if (!E) return;

			E.onchange = edost_ChangeDelivery;

			var E2 = BX('edost_office_data');
			if (E2) E2.value = data;

			data = (window.JSON && window.JSON.parse ? JSON.parse(data) : eval('(' + data + ')'));

			// извлечение тарифов других модулей, чтобы потом добавить их в конец списка
			var bitrix_delivery = '';
			var edost_delivery = false;
			var no_delivery = '';
			var bitrix_active = false;
			var ar = BX.findChildren(select, {'tag': 'option'}, true);
			for (var i = 0; i < ar.length; i++) {
				var v = ar[i];
				if (v.value.indexOf('edost:') == 0) {
					edost_delivery = true;
					continue;
				}
				if (v.value == selected) bitrix_active = true;
				var s = '<option value="' + v.value + '"' + (v.value == selected ? ' selected="selected"' : '') + '>' + v.innerHTML + '</option>';
				if (v.value == '') no_delivery = s; else bitrix_delivery += s;
			}

			// поиск поля с адресом
			edost_address_id = false;
			var E_address = false;
			var ar = [<?=implode(',', $address)?>];
			for (var i = 0; i < ar.length; i++) {
				E_address = BX('ORDER_PROP_' + ar[i]);
				if (E_address) { edost_address_id = ar[i]; break; }
			}

			// проверка адреса на наличие данных по офису и сохранение оригинального адреса покупателя
			var office_in_address = false;
			if (E_address && E_address.value != '')
				if (E_address.value.indexOf(', <?=$sign['code']?>:') > 0) office_in_address = true;
				else edost_address = E_address.value;

			// формирование общего списка с тарифами
			var active = false;
			var s = '';
			var optgroup = false;
			if (edost_delivery && data.format != undefined) for (var i = 0; i < data.format.length; i++) {
				var v = data.format[i];
				if (v.head !== undefined) {
					if (optgroup) s += '</optgroup>';
					optgroup = true;
					s += '<optgroup label="' + v.head + '">';
					continue;
				}
				var a = (!bitrix_active && (v.id == selected || v.checked) ? true : false);
				if (a) active = v;
				s += '<option value="' + v.id + '" ' + (a ? ' selected="selected"' : '');
				if (v.office_address_full != undefined) s += ' data-edost_address="' + v.office_address_full + '"';
				if (v.office_id != undefined) s += ' data-edost_office_id="' + v.office_id + '"';
				if (v.pricetotal_formatted === '0') v.pricetotal_formatted = '<?=$sign['free']?>';
				if (v.pricecash_formatted === '0') v.pricecash_formatted = '<?=$sign['free']?>';
				s += '>' + v.title + (v.pricetotal_formatted != undefined ? ' - ' + v.pricetotal_formatted : '') + (v.pricecash_formatted != undefined ? ' (' + v.pricecash_formatted + ')' : '');
				s += '</option>';
			}
			s += bitrix_delivery;
			if (optgroup) s += '</optgroup>';
			E.innerHTML = no_delivery + s;

			// вывод адреса выбранного офиса в отдельном поле
			if (E_address) {
				var E_office = BX('edost_office_address');
				if (!E_office) {
					var E2 = BX.findParent(E_address);
					E2.appendChild( BX.create('div', {'props': {'id': 'edost_office_address'}}) );
					E_office = BX('edost_office_address');
				}
				var a = true;
				if (active !== false && active.office_address_full != undefined) {
					a = false;
					var ar = active.office_address_full.split(', <?=$sign['code']?>: ');
					if (ar[1] == undefined)	s = '<b style="color: #F00;"><?=$sign['office_unchecked']?></b>';
					else {
						s = (active.office_id ? ' (<a class="edost_link" href="<?=$protocol?>edost.ru/office.php?c=' + active.office_id + '" target="_blank"><?=$sign['map']?></a>)' : '');
						s = '<b style="color: #00A;">' + ar[0].replace(': ', '</b>' + s + '<br>').replace(', <?=$sign['tel']?>:', '<br>').replace(', <?=$sign['schedule']?>:', '<br>');
						var code = ar[1].split('/');
						if (code[0] != '' && code[0] != 'S' && code[0] != 'T') s += '<br><b><?=$sign['code']?>: ' + code[0] + '</b>';
					}
					if (active.office_mode) s += (s != '' ? '<br>' : '') + '<span style="cursor: pointer; color: #A00; font-size: 14px; font-weight: bold;" onclick="edost_office.window(\'' + active.office_mode + '\');"><?=$sign['change']?></span>';
					E_office.innerHTML = s;
					E_address.value = active.office_address_full;
				}
				else if (office_in_address) E_address.value = '';
				E_address.style.display = (a ? 'block' : 'none');
				E_office.style.display = (!a ? 'block' : 'none');
			}

			// вывод error, warning, pricecash и priceinfo
			var s = '';
			var error = '';
			var E = BX('edost_delivery_info');
			if (!E) {
				var E = BX('DELIVERY_SELECT');
				if (E) E.appendChild( BX.create('div', {'props': {'id': 'edost_delivery_info'}}) );
				E = BX('edost_delivery_info');
			}
			if (active !== false) {
				<? if ($edostpaycod !== false) { ?>
				if (BX('PAY_SYSTEM_ID').value == '<?=$edostpaycod?>') {
					if (active.pricecash_formatted != undefined) {
						if (active.transfer_formatted != undefined && active.transfer_formatted != 0) s += '<div style="padding-top: 5px; color: #F00;"><?=str_replace('%transfer%', "' + active.transfer_formatted + '", $sign['transfer'])?></div>';
					}
					else if (active.id != 'edost:0') error += '<span style="padding: 2px 8px; background: #F00; color: #FFF;"><?=$sign['admin_no_cod']?></span>';
				}
				<? } ?>
				if (active.priceinfo_formatted) s += '<div style="padding-top: 5px;"><?=str_replace('%price_info%', "' + active.priceinfo_formatted + '", $sign['priceinfo_warning_bitrix'])?></div>';
				if (active.error) error += '<div style="padding-top: 5px;">' + active.error + '</div>';
			}
			if (data.warning) error += '<div style="padding-top: 5px;">' + data.warning + '</div>';
			E.innerHTML = (error != '' ? '<div style="padding-top: 5px; color: #F00; font-weight: bold; font-size: 12px;">' + error + '</div>' : '') + s;
		});

	}

	function edost_ChangeDelivery(address, office_id) {

		var profile = '';
		if (edost_address_id != false) {
			var E = BX('DELIVERY_ID');
			E = BX.findChild(E, {'tag': 'option', 'attribute': {'value': E.value}}, true);
			var E_address = BX('ORDER_PROP_' + edost_address_id);
			if (E && E_address) {
				var s = E.value.split(':');
				if (s[0] == 'edost') profile = (s[1] != undefined ? s[1] : '');

				if (office_id == undefined) {
					address = E.getAttribute('data-edost_address');
					office_id = E.getAttribute('data-edost_office_id');
				}

				var office_in_address = (E_address.value.indexOf(', <?=$sign['code']?>:') > 0 ? true : false);
				if (E_address.value != '' && !office_in_address) edost_address = E_address.value;

				if (address != undefined) E_address.value = address;
				else if (E_address.value == '' || office_in_address) E_address.value = edost_address;

				if (office_id && profile) {
					BX.ajax.post('/bitrix/components/edost/delivery/edost_delivery.php', 'mode=order_edit&id=' + orderID + '&office_id=' + office_id + '&profile=' + profile, function(res) { fChangeDelivery(); });
					return;
				}
			}
		}

		if (profile == 'shop' || profile == 'office' || profile == 'terminal') edost_office.window(profile, true);
		else fChangeDelivery();

	}

	function edost_SetOffice(profile, id, cod, mode) {

		if (edost_office.map) {
			edost_office.map.balloon.close();
			edost_office.window('hide');
		}

		if (id == undefined) {
			fChangeDelivery();
			return;
		}

		var address = '';
		for (var i = 0; i < edost_office.data.length; i++) {
			for (var i2 = 0; i2 < edost_office.data[i].point.length; i2++) if (edost_office.data[i].point[i2].id == id) {
				address = edost_office.data[i].point[i2].address + ', <?=$sign['code']?>: /' + id + '/' + edost_office.data[i].to_office + '/' + profile;
				break;
			}
			if (address != '') break;
		}

		var E = BX('DELIVERY_ID');
		if (E) {
			var E2 = BX.findChild(E, {'tag': 'option', 'attribute': {'value': E.value}}, true);
			if (E2) E.value = E2.value = 'edost:' + profile;
		}

		edost_ChangeDelivery(address, id);

	}

	BX.ready(function() {
		var s = fRecalProductResult.toString();
		if (s.indexOf("/* edost */") > 0) return false;

		s = s.replace("BX('DELIVERY_SELECT').innerHTML = res[0][\"DELIVERY\"];", "edost_InsertData(res[0]['DELIVERY']);    /* edost */");
		fRecalProductResult = eval("(" + s + ")");

		var E = BX('DELIVERY_ID');
		if (E) edost_InsertData(E);
	});

</script>
<?
		}
	}


// =========================== sale.order.ajax ===========================


	// вызывается перед расчетом доставки
	function OnSaleComponentOrderProperties(&$arUserResult, Bitrix\Main\HttpRequest $http_request, &$arParams, &$arResult) {
//		$_SESSION['EDOST']['run'] .= '<br>OnSaleComponentOrderProperties';
//		echo '<br><b>arResult:</b> <pre style="font-size: 12px">'.print_r($arResult, true).'</pre>';
//		echo '<br><b>arUserResult:</b> <pre style="font-size: 12px">'.print_r($arUserResult, true).'</pre>';

		$arResult['edost']['config'] = $config = self::GetEdostConfig(SITE_ID);
		if ($config['template'] == 'off' || empty($config['template'])) return;
//		echo '<br><b>config (sale.order.ajax):</b> <pre style="font-size: 12px">'.print_r($config, true).'</pre>';

//		if (empty($arResult['edost']['error'])) $arResult['edost']['error'] = (isset($arResult['ERROR']) ? $arResult['ERROR'] : array());

		if (!empty($arResult['edost']['order_recreated_delivery_id'])) $arUserResult['DELIVERY_ID'] = $arResult['edost']['order_recreated_delivery_id'];

		$sign = GetMessage('EDOST_DELIVERY_SIGN');
		$locations_installed = (!empty($arResult['edost']['locations_installed']) ? true : false);
		if ($config['template_map_inside'] == 'Y' && $config['map'] == 'Y' && $config['template_format'] !== 'off' && $config['template_block'] !== 'off') $arResult['edost']['map_inside'] = true;
		if ($config['map'] == 'Y' && !in_array($config['template'], array('Y', 'N3')) && (!defined('DELIVERY_EDOST_PICKPOINT_WIDGET') || DELIVERY_EDOST_PICKPOINT_WIDGET == 'Y')) $arResult['edost']['pickpoint_widget'] = true;

		// загрузка дополнительных параметров из id доставки: edost:'profile'_id(id - начиная с магазина 16):'office_id':'cod_tariff'
		$id = (isset($arUserResult['DELIVERY_ID']) ? $arUserResult['DELIVERY_ID'] : '');
		$v = explode(':', $id);
		$ar = array();
		if ($v[0] === 'edost') {
			$profile = $v[1];
			$s = explode('_', $profile);
			if (isset($s[1])) {
				$id = $s[1];
				$profile = $s[0];
			}
			else $id = 'edost:'.$profile;

			$ar['profile'] = $profile;
			if (isset($v[2])) {
				if (!empty($v[2])) $ar['office_id'] = $v[2];
				if (!empty($v[3])) $ar['cod_tariff'] = ($v[3] === 'Y' ? true : false);
			}

			$arUserResult['DELIVERY_ID'] = $id;
		}
		$ar['id'] = $id;
		if (!empty($_REQUEST['edost_bookmark'])) $ar['bookmark'] = substr($_REQUEST['edost_bookmark'], 0, 10);
		$arResult['edost']['active'] = $ar;


		// поле ADDRESS (для сохранения данных по выбранному пункту выдачи)
		$address_id = (isset($arResult['edost']['address_id']) ? $arResult['edost']['address_id'] : -1);
		$address = (isset($arResult['edost']['address_value']) ? $arResult['edost']['address_value'] : '');;
		if (!$locations_installed) {
			$props = edost_class::SetPropsCode($arUserResult['ORDER_PROP']);
			if (isset($props['ADDRESS'])) {
				$arResult['edost']['address_id'] = $address_id = $props['ADDRESS']['id'];
				$arResult['edost']['address_value'] = $address = $props['ADDRESS']['value'];
			}
		}


		// подключение скрипта выбора пунктов выдачи и стилей
		if (empty($arResult['edost']['order_recreated']) && (!defined('DELIVERY_EDOST_JS_SALE_ORDER_AJAX') || DELIVERY_EDOST_JS_SALE_ORDER_AJAX != 'N')) {
			$protocol = CDeliveryEDOST::GetProtocol();
			$date = date('dmY');
			$map_link = $protocol.'edostimg.ru/shop/';
//			$map_link = '/bitrix/js/edost.delivery/'; // !!!!!
			if ($config['template'] == 'Y' || $config['map'] == 'Y') $GLOBALS['APPLICATION']->SetAdditionalCSS($map_link.'office.css?a='.$date);
			if ($config['map'] == 'Y') {
				$GLOBALS['APPLICATION']->AddHeadString('<script type="text/javascript" src="'.$map_link.'office.js?a='.$date.'" charset="utf-8"></script>');
				if (!empty($arResult['edost']['pickpoint_widget'])) $GLOBALS['APPLICATION']->AddHeadString('<script type="text/javascript" src="'.$protocol.'pickpoint.ru/select/postamat.js" charset="utf-8"></script>');
			}
			if ($config['template'] != 'Y') {
				if ($config['map'] == 'Y') $s = '
					function edost_SetOffice(profile, id, cod, mode) {
						var E = document.getElementById("edost_office");
						if (E) E.value = id;

						if (edost_office.map) {
							edost_office.map.balloon.close();
							edost_office.window("hide");
						}

	                    '.($config['template'] == 'N3' ? 'BX.Sale.OrderAjaxComponent.sendRequest();' : 'submitForm();').'
					}';
				else $s = '
					function edost_OpenMap(n) {
						var E = document.getElementById("edost_office_" + n);
						if (E) window.open((document.location.protocol == "https:" ? "https://" : "http://") + "edost.ru/office.php?c=" + E.value, "_blank");
					}

					function edost_SetOffice(n, id) {
						var E = document.getElementById("edost_office_" + n);
						if (E) {
							var E2 = document.getElementById("edost_office");
							if (E2) E2.value = E.value;

							'.($config['template'] == 'N3' ? 'BX.Sale.OrderAjaxComponent.sendRequest();' : '
							id = "ID_DELIVERY_" + (id != undefined ? "ID_" + id : "edost_" + n);
							if (document.getElementById(id).checked) submitForm();
							').'
						}
					}';

				if ($config['template'] == 'N3') $s .= '
					function edost_ShowOfficeAddress() {
						var E = document.getElementById("edost_address_input");
						if (!E || E.value == "") return;
						var E = document.getElementById("soa-property-" + E.value);

						if (E && E.style.display != "none") {
							if (E.value.indexOf("'.$sign['code'].'" + ": ") == -1) E.value = "";
							E.style.display = "none";

							var s = E.value;
							if (E.value == "") {
								s = "<span style=\"color: #F00;\">'.$sign['office_unchecked'].'!<span/>";
								s += " <span class=\"edost_link\" onclick=\"var E = BX(\'edost_get_office_span\'); if (E) E.click();\">'.$sign['change2'].'</span>";
							}

							var E2 = BX.findParent(E);
							E2.appendChild( BX.create("div", {"props": {"style": "font-weight: bold;", "innerHTML": s}}) );
						}
					}

					window.setInterval("edost_ShowOfficeAddress()", 500);';

				$s = '<script type="text/javascript">'.$s.'</script>';
				$GLOBALS['APPLICATION']->AddHeadString($s);
			}
		}


		// сброс старого (из профиля покупателя) адреса пункта выдачи при первой загрузке + перенос в дефолтные для нового выбора
		if ($address_id != -1 && ($_SERVER['REQUEST_METHOD'] != 'POST' || !$locations_installed && !isset($_SESSION['EDOST']['readonly']))) {
			if (!$locations_installed) $_SESSION['EDOST']['readonly'] = false;
			$office = edost_class::ParseOfficeAddress($address);
			if ($office !== false) {
				if (is_array($office)) $_SESSION['EDOST']['office_default']['profile'] = array('id' => $office['id'], 'profile' => $office['profile'], 'cod_tariff' => $office['cod_tariff']);
				if (!$locations_installed) $arUserResult['ORDER_PROP'][$address_id] = '';
			}
		}

	}


	// вызывается после расчета заказа в sale.order.ajax
	function OnSaleComponentOrderDeliveriesCalculated(Bitrix\Sale\Order $order, &$arUserResult, Bitrix\Main\HttpRequest $http_request, &$arParams, &$arResult, &$arDeliveryServiceAll, &$arPaySystemServiceAll) {
//		$_SESSION['EDOST']['run'] .= '<br>OnSaleComponentOrderDeliveriesCalculated';
//		$_SESSION['EDOST']['arUserResult_Calculated'] = $arUserResult;
//		$_SESSION['EDOST']['arResult_Calculated'] = $arResult;
//		echo '<br><b>arUserResult:</b> <pre style="font-size: 12px">'.print_r($arUserResult, true).'</pre>';
//		echo '<br><b>arResult:</b> <pre style="font-size: 12px">'.print_r($arResult, true).'</pre>';
//		echo '<br><b>arDeliveryServiceAll:</b> <pre style="font-size: 12px">'.print_r($arDeliveryServiceAll, true).'</pre>';
//		echo '<br><b>arPaySystemServiceAll:</b> <pre style="font-size: 12px">'.print_r($arPaySystemServiceAll, true).'</pre>';

		$config = self::GetEdostConfig(SITE_ID);

		$shipment = SaleOrderAjax::getCurrentShipment($order);
		if ($shipment === false) return;

		$bitrix_data = $arResult['DELIVERY'];
		$bitrix_delivery_id = $delivery_id = $shipment->getDeliveryId();

		// подготовка данных по доставке для форматирования + расчет стоимости для тарифов битрикса (для шаблона eDost и расширенного Visual)
		if (!empty($config) && $config['template'] != 'off' && !empty($bitrix_data) && !empty($arDeliveryServiceAll) && empty($arResult['edost']['order_recreated'])) {
			$cloned_order = $order->createClone();
			$cloned_shipment = SaleOrderAjax::getCurrentShipment($cloned_order);
			$cloned_shipment->setField('CUSTOM_PRICE_DELIVERY', 'N');

			foreach ($bitrix_data as $k => $v) if (isset($arDeliveryServiceAll[$k])) {
				if (isset($v['CHECKED'])) unset($arResult['DELIVERY'][$k]['CHECKED']);

				$o = $arDeliveryServiceAll[$k];
				$code = $o->getCode();
				$s = explode(':', $code);
				$automatic = (isset($s[1]) ? $s[0] : '');
				$profile = ($o->isProfile() ? true : false);
				$id = $o->getId();

				$v = array(
					'ID' => $id,
					'OWN_NAME' => ($profile && $automatic !== 'edost' ? $o->getNameWithParent() : $o->getName()),
					'DESCRIPTION' => $o->getDescription(),
					'SORT' => $o->getSort(),
					'CODE' => $code, // в стандартном массиве битрикса кода нет
					'CURRENCY' => $order->getCurrency(),
//					'FIELD_NAME' => 'DELIVERY_ID',
//					'EXTRA_SERVICES' => $o->getExtraServices()->getItems(),
//					'STORE' => \Bitrix\Sale\Delivery\ExtraServices\Manager::getStoresList($id),
				) + $v;

				if ($automatic != 'edost') {
					$s = $o->getLogotip();
					if (!empty($s)) $v['LOGOTIP'] = array('ID' => $s, 'SRC' => CFile::GetPath($s));
					if (!in_array($config['template'], array('N', 'N3')) && !isset($v['PRICE'])) {
						$cloned_shipment->setField('DELIVERY_ID', $id);
						$cloned_order->getShipmentCollection()->calculateDelivery();
						$s = $o->calculate($cloned_shipment);
						if ($s->isSuccess()) {
							$p1 = $v['PRICE'] = $s->getPrice() + 10000;
							$p2 = $cloned_order->getDeliveryPrice();
							if ($p2 >= 0 && $p1 != $p2) $v['DELIVERY_DISCOUNT_PRICE'] = $p2;
							if (strlen($s->getPeriodDescription()) > 0) $v['PERIOD_TEXT'] = $s->getPeriodDescription();
						}
						else {
							$error = $s->getErrorMessages();
							if (empty($error)) $error = array(Bitrix\Main\Localization\Loc::getMessage('SOA_DELIVERY_CALCULATE_ERROR'));
							$v['CALCULATE_ERRORS'] = implode('<br>', $error);
						}
					}
				}

				$bitrix_data[$k] = $v;
			}
//			echo '<br><b>bitrix_data:</b> <pre style="font-size: 12px">'.print_r($bitrix_data, true).'</pre>';

			$c = array();

			$a = false;
			if (!empty($arPaySystemServiceAll)) foreach ($arPaySystemServiceAll as $k => $v) if (substr($v['ACTION_FILE'], -11) == 'edostpaycod') $a = true;
            if (!$a) $c['template_cod'] = 'off'; // отключение вывода стоимости с наложенным платежом, если нет модуля с обработчиком 'edostpaycod'

			$format = edost_class::FormatTariff($bitrix_data, $arResult['BASE_LANG_CURRENCY'], false, isset($arResult['edost']['active']) ? $arResult['edost']['active'] : false, $c);
			if ($format === false) return;
//			echo '<br><b>format:</b> <pre style="font-size: 12px">'.print_r($format, true).'</pre>';

			$arResult['edost']['format'] = $format;

	        $delivery_id = $arUserResult['DELIVERY_ID'] = $format['active']['id'];
			if (isset($arResult['DELIVERY'][$delivery_id])) $arResult['DELIVERY'][$delivery_id]['CHECKED'] = 'Y';

			if (empty($delivery_id)) {
				$shipment->setFields(array('CUSTOM_PRICE_DELIVERY' => 'Y', 'PRICE_DELIVERY' => 0));
				$arUserResult['CALCULATE_PAYMENT'] = true;
				return;
			}

			// пересоздание заказа при изменении выбранной доставки
			if ($delivery_id != $bitrix_delivery_id) {
				$arUserResult['RECREATE_ORDER'] = true;
				$arResult['edost']['order_recreated'] = true;
				$arResult['edost']['order_recreated_delivery_id'] = $delivery_id;
				return;
			}
		}


		$format_active = (!empty($arResult['edost']['format']['active']) ? $arResult['edost']['format']['active'] : false);
		$cod_tariff = (!empty($arResult['edost']['format']['cod_tariff']) ? true : false);
		$cod_tariff_active = (!empty($format_active['cod_tariff']) ? true : false);
		if ($cod_tariff && $cod_tariff_active) $arUserResult['PAY_CURRENT_ACCOUNT'] = false;

		$paysystem_id = intval($arUserResult['PAY_SYSTEM_ID']);
		$tariff = CDeliveryEDOST::GetEdostProfile($delivery_id);
		if ($tariff !== false) {
			$props = edost_class::GetProps($order, array('order', 'no_location', 'no_payment'));
//			echo '<br><b>props[office]:</b> <pre style="font-size: 12px">'.print_r($props['office'], true).'</pre>';
			$tariff = CDeliveryEDOST::GetEdostTariff($tariff['profile'], !empty($props['office']['type']) ? $props['office']['type'] : 0);
//			echo '<br><b>tariff:</b> <pre style="font-size: 12px">'.print_r($tariff, true).'</pre>';
		}

		// удаление наложенного платежа для тарифов без наложки + выбор наложки для тарифов с 'cod_tariff' + выделение первого способа оплаты, если нет активных
		$cod = $i = $update = false;
		if (!empty($arPaySystemServiceAll)) foreach ($arPaySystemServiceAll as $k => $v) {
			$a = ($v['ID'] == $paysystem_id ? true : false);
			if (substr($v['ACTION_FILE'], -11) == 'edostpaycod') {
				if ($cod_tariff && $cod_tariff_active && !$a) {
					$update = $cod = true;
					$i = $v['ID'];
					break;
				}
				else if ($tariff === false || $tariff['pricecash'] < 0 || $cod_tariff && !$cod_tariff_active) {
					if ($a) $update = true;
					unset($arPaySystemServiceAll[$k]);
				}
				else if ($a) $cod = true;
			}
			else if ($i === false && $v['ACTION_FILE'] != 'inner') $i = $v['ID'];
		}
		if ($update) $arUserResult['PAY_SYSTEM_ID'] = ($i !== false ? $i : '');

		if ($tariff === false) return;

		// установка стоимости доставки для эксклюзивных офисов и наложенного платежа
		$price = (!empty($tariff['priceoffice_active']) ? $tariff['price'] : -1);
		if ($cod) $price = $tariff['pricecash'];
		if ($price >= 0) {
			$base_currency = CDeliveryEDOST::GetRUB();
			$r = $shipment->setFields(array('CUSTOM_PRICE_DELIVERY' => 'Y', 'PRICE_DELIVERY' => edost_class::GetPrice('value', $price, $base_currency, $order->getCurrency())));
			if (!$r->isSuccess()) return new \Bitrix\Main\EventResult(\Bitrix\Main\EventResult::ERROR, null, null, 'sale');
			$arUserResult['CALCULATE_PAYMENT'] = true;
		}

	}


	// вызывается перед расчетом доставки (old)
	function OnSCOrderOneStepPersonType(&$arResult, &$arUserResult, &$arParams, $isNew = false) {
//		$_SESSION['EDOST']['run'] .= '<br>EVENT_PERSON: '.$arUserResult['DELIVERY_ID'].' - '.$arUserResult['DELIVERY_LOCATION'];
//		echo '<br><b>arUserResult:</b> <pre style="font-size: 12px">'.print_r($arUserResult, true).'</pre>';

		if ($isNew) return;

		if (empty($arResult['edost']['error'])) $arResult['edost']['error'] = (isset($arResult['ERROR']) ? $arResult['ERROR'] : array());

	}

	// вызывается перед расчетом доставки (old)
	function OnSCOrderOneStepOrderPropsHandler(&$arResult, &$arUserResult, &$arParams, $isNew = false) {
//		$_SESSION['EDOST']['run'] .= '<br>EVENT_PROPS: '.$arUserResult['DELIVERY_ID'].' - '.$arUserResult['DELIVERY_LOCATION'];
//		echo '<br><b>arResult:</b> <pre style="font-size: 12px">'.print_r($arResult, true).'</pre>';
//		echo '<br><b>arUserResult:</b> <pre style="font-size: 12px">'.print_r($arUserResult, true).'</pre>';

		if ($isNew) return;

		$arResult['edost']['config'] = $config = self::GetEdostConfig(SITE_ID);
		if (empty($config) || $config['template'] == 'off') return;
//		echo '<br><b>config ajax:</b> <pre style="font-size: 12px">'.print_r($config, true).'</pre>';

		if (empty($arResult['edost']['error'])) $arResult['edost']['error'] = (isset($arResult['ERROR']) ? $arResult['ERROR'] : array());

		$locations_installed = (!empty($arResult['edost']['locations_installed']) ? true : false);
		if ($config['template_map_inside'] == 'Y' && $config['map'] == 'Y' && $config['template_format'] !== 'off' && $config['template_block'] !== 'off') $arResult['edost']['map_inside'] = true;

		// загрузка дополнительных параметров из id доставки: edost:'profile'_id(id - начиная с магазина 16):'office_id':'cod_tariff'
		$id = (isset($arUserResult['DELIVERY_ID']) ? $arUserResult['DELIVERY_ID'] : '');
		$v = explode(':', $id);
		$ar = array();
		if ($v[0] === 'edost') {
			$profile = $v[1];
			$s = explode('_', $profile);
			if (isset($s[1])) {
				$id = $s[1];
				$profile = $s[0];
			}
			else $id = 'edost:'.$profile;

			$ar['profile'] = $profile;
			if (isset($v[2])) {
				if (!empty($v[2])) $ar['office_id'] = $v[2];
				if (!empty($v[3])) $ar['cod_tariff'] = ($v[3] === 'Y' ? true : false);
			}

			$arUserResult['DELIVERY_ID'] = $id;
		}
		$ar['id'] = $id;
		if (!empty($_REQUEST['edost_bookmark'])) $ar['bookmark'] = substr($_REQUEST['edost_bookmark'], 0, 10);
		$arResult['edost']['active'] = $ar;

		// подключение скрипта выбора пунктов выдачи и стилей
		if (!defined('DELIVERY_EDOST_JS_SALE_ORDER_AJAX') || DELIVERY_EDOST_JS_SALE_ORDER_AJAX != 'N') {
			$protocol = CDeliveryEDOST::GetProtocol();
			$date = date('dmY');
			$map_link = $protocol.'edostimg.ru/shop/';
//			$map_link = '/bitrix/js/edost.delivery/'; // !!!!!
			if ($config['template'] == 'Y') $GLOBALS['APPLICATION']->SetAdditionalCSS($map_link.'office.css?a='.$date);
			if ($config['map'] == 'Y')
				if ($config['template'] == 'Y') $GLOBALS['APPLICATION']->AddHeadString('<script type="text/javascript" src="'.$map_link.'office.js?a='.$date.'" charset="utf-8"></script>');
				else $GLOBALS['APPLICATION']->AddHeadString('<script type="text/javascript" src="'.$protocol.'pickpoint.ru/select/postamat.js" charset="utf-8"></script>');
		}

		// поле ADDRESS (для сохранения данных по выбранному пункту выдачи)
		$address_id = -1;
		$address = '';
		if ($locations_installed) {
			if (isset($arResult['edost']['order_prop']['ADDRESS'])) {
				$address_id = $arResult['edost']['order_prop']['ADDRESS']['id'];
				$address = $arResult['edost']['order_prop']['ADDRESS']['value'];
			}
		}
		else foreach ($arResult['ORDER_PROP']['USER_PROPS_Y'] as $k => $v) if ($v['CODE'] == 'ADDRESS' && in_array($v['TYPE'], array('TEXT', 'TEXTAREA'))) {
			$address_id = $k;
			$arResult['edost']['address_id'] = $address_id;
			$arResult['edost']['address_value'] = $address = $v['VALUE'];
		}

		// сброс старого (из профиля покупателя) адреса пункта выдачи при первой загрузке + перенос в дефолтные для нового выбора
		if ($address_id != -1 && ($_SERVER['REQUEST_METHOD'] != 'POST' || !$locations_installed && !isset($_SESSION['EDOST']['readonly']))) {
			if (!$locations_installed) $_SESSION['EDOST']['readonly'] = false;
			$office = edost_class::ParseOfficeAddress($address);
			if ($office !== false) {
				if (is_array($office)) $_SESSION['EDOST']['office_default']['profile'] = array('id' => $office['id'], 'profile' => $office['profile'], 'cod_tariff' => $office['cod_tariff']);
				if (!$locations_installed) $arResult['ORDER_PROP']['USER_PROPS_Y'][$address_id]['VALUE'] = '';
			}
		}

	}


	// вызывается после расчета доставки
	function OnSCOrderOneStepDeliveryHandler(&$arResult, &$arUserResult, &$arParams, $isNew = false) {
//		$_SESSION['EDOST']['run'] .= '<br>EVENT_DELIVERY: '.$arUserResult['DELIVERY_ID'].' - '.$arUserResult['DELIVERY_LOCATION'];
//		$_SESSION['EDOST']['arUserResult_Delivery'] = $arUserResult;
//		echo '<br><b>arUserResult_Delivery:</b> <pre style="font-size: 12px">'.print_r($arUserResult, true).'</pre>';
//		echo '<br><b>arResult:</b> <pre style="font-size: 12px">'.print_r($arResult, true).'</pre>';
//		echo '<br><b>arResult[DELIVERY]:</b> <pre style="font-size: 12px">'.print_r($arResult['DELIVERY'], true).'</pre>';
//		echo '<br><b>arUserResult:</b> <pre style="font-size: 12px">'.print_r($arUserResult, true).'</pre>';

		if (empty($arResult['DELIVERY'])) return;

		$config = (isset($arResult['edost']['config']) ? $arResult['edost']['config'] : self::GetEdostConfig(SITE_ID));
		if (empty($config) || $config['template'] == 'off') return;

		$converted = (\Bitrix\Main\Config\Option::get('main', '~sale_converted_15', 'N') == 'Y' ? true : false); // проверка на магазин 16
		$bitrix_delivery_id = $arUserResult['DELIVERY_ID'];

		$address_id = (isset($arResult['edost']['address_id']) && !$locations_installed ? $arResult['edost']['address_id'] : -1);

		if ($isNew) {
			if (!isset($arResult['edost']['format'])) return;
			$format = $arResult['edost']['format'];

			$ar = $format;
			if (!empty($ar['data'])) foreach ($ar['data'] as $f_key => $f) foreach ($f['tariff'] as $k => $v) if (!empty($v['automatic'])) $ar['data'][$f_key]['tariff'][$k]['id'] = $v['automatic']; // поддержка старого шаблона eDost (для вывода иконок)
			$arResult['edost']['format'] = $ar;
		}
		else {
			$order = array(
				'SITE_ID' => SITE_ID, // битрикс 16
				'PRICE' => $arResult['ORDER_PRICE'],
				'WEIGHT' => $arResult['ORDER_WEIGHT'],
				'LOCATION_FROM' => COption::GetOptionInt('sale', 'location'),
				'LOCATION_TO' => ($converted && isset($arUserResult['DELIVERY_LOCATION_BCODE']) ? $arUserResult['DELIVERY_LOCATION_BCODE'] : $arUserResult['DELIVERY_LOCATION']),
				'LOCATION_ZIP' => $arUserResult['DELIVERY_LOCATION_ZIP'],
			);

			// новые параметры битрикс 14 и 16
			$ar = array('MAX_DIMENSIONS' => 'MAX_DIMENSIONS', 'DIMENSIONS' => 'ORDER_DIMENSIONS', 'ITEMS_DIMENSIONS' => 'ITEMS_DIMENSIONS', 'ITEMS' => 'BASKET_ITEMS', 'EXTRA_PARAMS' => 'DELIVERY_EXTRA', 'CURRENCY' => 'BASE_LANG_CURRENCY');
			foreach ($ar as $k => $v) if (isset($arResult[$v])) $order[$k] = $arResult[$v];

			$format = edost_class::FormatTariff($arResult['DELIVERY'], $arResult['BASE_LANG_CURRENCY'], $order, isset($arResult['edost']['active']) ? $arResult['edost']['active'] : false);
			if ($format === false) return;

			$ar = $format;
			if (!empty($ar['data'])) foreach ($ar['data'] as $f_key => $f) foreach ($f['tariff'] as $k => $v) if (!empty($v['automatic'])) $ar['data'][$f_key]['tariff'][$k]['id'] = $v['automatic']; // поддержка старого шаблона eDost (для вывода иконок)
			$arResult['edost']['format'] = $ar;

			$arUserResult['DELIVERY_ID'] = $format['active']['id'];
		}

		$sign = GetMessage('EDOST_DELIVERY_SIGN');
		$format_data = GetMessage('EDOST_DELIVERY_FORMAT');
		$base_currency = CDeliveryEDOST::GetRUB();

		// перевод форматированных тарифов обратно в формат битрикса (для стандартного шаблона)
		$ar = array();
		if (!empty($format['data'])) foreach ($format['data'] as $f_key => $f) foreach ($f['tariff'] as $k => $v) if (!empty($v['id'])) {
			$id = ($converted || $v['automatic'] == '' ? $v['id'] : $v['automatic']);
			$bitrix_tariff = (isset($arResult['DELIVERY'][$id]) ? $arResult['DELIVERY'][$id] : false);

			$name = (isset($v['name']) ? $v['name'] : '');
			if (!empty($v['insurance'])) $name .= (!empty($name) ? ' ' : '').$v['insurance'];
			if (!empty($v['company'])) $name = $v['company'].(!empty($name) ? ' ('.$name.')' : '');

			if ($v['automatic'] == 'edost') {
				if ($config['template'] == 'Y') $v['description'] = $sign['template_warning'].(!empty($v['description']) ? '<br>'.$v['description'] : '');
				else if ($v['tariff_id'] == 29 && isset($format['pickpointmap'])) $v['pickpointmap'] = $format['pickpointmap'];
				else if (in_array($v['format'], array('shop', 'office', 'terminal')) && !empty($format['office'][$v['company_id']])) $v['office_data'] = $format['office'][$v['company_id']];

				if (!empty($v['error']))
					if ($config['template'] == 'N3') $name .= ' ('.$v['error'].')';
					else $name .= '<br><font color="#FF0000">'.$v['error'].'</font>';
			}

			if ($converted) {
				if ($bitrix_tariff !== false) $s = $bitrix_tariff;
				else $s = array('ID' => $id, 'FIELD_NAME' => 'DELIVERY_ID', 'EXTRA_SERVICES' => array(), 'STORE' => array(), 'SORT' => 100, 'CURRENCY' => $arResult['BASE_LANG_CURRENCY']);

				if ($v['automatic'] == 'edost' || $config['template'] == 'N2' || $bitrix_tariff === false) $s = array_merge($s, array(
					'NAME' => $name,
					'DESCRIPTION' => (isset($v['description']) ? $v['description'] : ''),
					'PRICE' => (isset($v['price']) ? $v['price'] : ''),
					'PRICE_FORMATED' => (isset($v['price_formatted']) ? $v['price_formatted'] : ''),
					'PERIOD_TEXT' => (isset($v['day']) ? $v['day'] : ''),
				));

				if ($config['template'] == 'N3') {
					if (isset($v['sort'])) $s['SORT'] = $v['sort'];
					if (isset($v['priceinfo'])) $s['pricetotal_formatted'] = $v['pricetotal_formatted'];
				}

				if (!empty($v['error']) && $v['automatic'] != 'edost') $s['CALCULATE_ERRORS'] = $v['error'];
				if (!empty($v['pickpointmap'])) $s['pickpointmap'] = $v['pickpointmap'];
				if (!empty($v['office_data'])) $s['office_data'] = $v['office_data'];
				if ($v['automatic'] == 'edost') $s['profile'] = $v['profile'];

				if ($id == $format['active']['id']) $s['CHECKED'] = 'Y';
				else if (isset($s['CHECKED'])) unset($s['CHECKED']);

				$ar[$id] = $s;

			}
			else if ($v['automatic'] != '') {
				$profile = $v['profile'];

				if (!isset($ar[$id])) {
					if ($bitrix_tariff !== false) $ar[$id] = $bitrix_tariff;
					else $ar[$id] = array('SID' => $id, 'SORT' => 0, 'TITLE' => '', 'DESCRIPTION' => '');
					$ar[$id]['PROFILES'] = array();
				}

				$s = (isset($bitrix_tariff['PROFILES'][$profile]) ? $bitrix_tariff['PROFILES'][$profile] : array());
				if (!isset($s['SID'])) {
					$s['SID'] = $v['profile'];
					$s['TITLE'] = $name;
					$s['DESCRIPTION'] = $v['description'];
					$s['FIELD_NAME'] = 'DELIVERY_ID';
				}

				if ($id.':'.$profile === $format['active']['id']) $s['CHECKED'] = 'Y';
				else if (isset($s['CHECKED'])) unset($s['CHECKED']);

				if (!empty($v['pickpointmap'])) $s['pickpointmap'] = $v['pickpointmap'];
				if (!empty($v['office_data'])) $s['office_data'] = $v['office_data'];

				$ar[$id]['PROFILES'][$profile] = $s;
			}
			else {
				if ($bitrix_tariff === false) continue;
				$s = $bitrix_tariff;
				if ($id === $format['active']['id']) $s['CHECKED'] = 'Y';
				else if (isset($s['CHECKED'])) unset($s['CHECKED']);
				$ar[$id] = $s;
			}
		}
		$arResult['DELIVERY'] = $ar;
//		echo '<br><b>arResult[DELIVERY]2:</b> <pre style="font-size: 12px">'.print_r($arResult['DELIVERY'], true).'</pre>';


		// данные для стандартного шаблона (стоимость доставки, дни и офисы)
		if ($config['template'] != 'Y') {
			$office_data = false;
			$div = ($config['template'] == 'N3' ? true : false);

			if ($config['template'] == 'N3') $office_set = (!empty($_REQUEST['order']['edost_office']) ? substr($_REQUEST['order']['edost_office'], 0, 15) : 0); // выбранный офис из POST
			else $office_set = (!empty($_REQUEST['edost_office']) ? substr($_REQUEST['edost_office'], 0, 10) : 0); // выбранный офис из POST

			if (!$converted) $ar = (!empty($arResult['DELIVERY']['edost']['PROFILES']) ? $arResult['DELIVERY']['edost']['PROFILES'] : array());
			foreach ($ar as $k => $v) {
				if ($converted) {
					if (empty($v['profile'])) continue;
					$profile = $v['profile'];
				}
				else $profile = $k;

				$tariff = CDeliveryEDOST::GetEdostTariff($profile);

				// офисы
				if (isset($v['office_data'])) {
					$company_id = $tariff['company_id'];

					$office_number = count($v['office_data']);
                    $office_id = 0;

					$i = (isset($_SESSION['EDOST']['address'][$tariff['company_id']]) ? $_SESSION['EDOST']['address'][$tariff['company_id']] : '');
					if (isset($v['office_data'][$office_set])) $office_id = $_SESSION['EDOST']['address'][$tariff['company_id']] = $office_set;
					else if (isset($v['office_data'][$i])) $office_id = $i;
					else if ($config['map'] != 'Y' || $office_number == 1) foreach ($v['office_data'] as $o) { $office_id = $o['id']; break; }

					if ($office_id != 0) {
						$o = $v['office_data'][$office_id];
						$tariff = CDeliveryEDOST::GetEdostTariff($profile, $o['type']);

						if ($v['CHECKED'] == 'Y') {
							$arResult['edost']['format']['active']['address'] = edost_class::GetOfficeAddress($o, $tariff);
							$arResult['edost']['format']['active']['office_id'] = $office_id;
							if (isset($o['codmax']) && $tariff['pricecash'] > $o['codmax']) $arResult['edost']['format']['active']['cod'] = false;
							if (!empty($tariff['office_type'])) $arResult['edost']['format']['active']['office_type'] = $tariff['office_type'];
						}

						$s = $sign['delivery'];
						if (in_array($o['type'], array(5, 6))) $s = ($tariff['company_id'] == 72 ? $s['pochtomat'] : $s['postamat']);
						else $s = $s[$tariff['format']];
						$head = $s;
					}

					if ($config['map'] == 'Y') {
	                    $s = '';

						$office_link = '';
						if ($office_id != 0) {
							if ($office_number != 1) $office_link = '<br>'.$sign['change'];

							$s .= '<span class="edost_address_head_n" style="font-size: 14px; color: #888;'.($div ? ' display: block;' : '').'">'.$head.':</span> <b style="font-size: 14px;">'.edost_class::GetOfficeAddress($o).'</b>';

							if ($office_number == 1) $s .= ' <a class="edost_link" class="edost_address_map_n" style="'.($div ? ' display: block;' : '').'" href="http://www.edost.ru/office.php?c='.$office_id.'" target="_blank">'.$sign['map'].'</a>';
						}
						else {
							if ($company_id == 26) $office_link = $sign['postamat']['format_get'];
							else if ($company_id == 72) $office_link = $sign['pochtomat']['format_get'];
							else $office_link = $format_data[$tariff['format']]['get'];
						}
						if ($office_link != '') $s .= '<span id="edost_get_office_span" class="edost_format_link'.($office_id == 0 ? '_big' : '').'" onclick="edost_office.window(\'profile_'.$profile.'_'.$v['ID'].'\');">'.$office_link.'</span>';

						$s = '<div class="edost">'.$s.'</div>';
					}
					else {
						$s = $head;
						$set_office = 'edost_SetOffice('.$profile.($converted ? ', '.$v['ID'] : '').')';

						if ($div) $s = '<div class="edost"><span style="color: #888;">'.$s.':</span><br>';
						else $s = '<td>'.$s.':</td><td style="padding-left: 5px;">';

						if ($office_number != 1) $s .= '<select id="edost_office_'.$profile.'" style="width: 100%; max-width: 250px;" onchange="'.$set_office.'">';
						foreach ($v['office_data'] as $o)
							if ($office_number == 1) $s .= '<b>'.$o['address'].'</b>'.'<input type="hidden" id="edost_office_'.$profile.'" value="'.$o['id'].'">';
							else $s .= '<option '.($o['id'] == $office_id ? 'selected="selected"' : '').' value="'.$o['id'].'">'.$o['address'].(in_array($o['type'], array(5, 6)) ? ' ('.$sign['postamat']['name_address'].')' : '').'</option>';
						if ($office_number != 1) $s .= '</select>';

						if ($div) $s .= '<br>';
						else $s .= '</td><td style="padding-left: 10px;">';
						$s .= '<a href="#" style="cursor: pointer; text-decoration: none; font-size: 11px;" onclick="edost_OpenMap('.$profile.'); return false;" >'.$sign[($div ? 'map' : 'map2')].'</a>';
						if ($div) $s .= '</div>';
						else {
							$s .= '</td>';
							$s = '<table class="edost_office_table" style="display: inline; margin: 0px;" border="0" cellspacing="0" cellpadding="0"><tr style="padding: 0px; margin: 0px;">'.$s.'</tr></table>';
						}

						$v['onclick'] = $set_office;
					}

					if ($config['map'] == 'Y' && !empty($arResult['edost']['format']['map_json']) && ($config['template'] == 'N3' && $v['CHECKED'] == 'Y' || $config['template'] != 'N3' && !$office_data)) {
						$office_data = true;
						$s .= '<input id="edost_office_data" autocomplete="off" value=\'{"ico_path": "/bitrix/images/delivery_edost_img", '.$arResult['edost']['format']['map_json'].'}\' type="hidden">';
					}
					if ($v['CHECKED'] == 'Y' && $config['template'] == 'N3' && $address_id != -1) {
						$s .= '<input type="hidden" value="" id="edost_office" name="edost_office">';
						$s .= '<input type="hidden" value="'.$address_id.'" id="edost_address_input">';
					}

					$v['office'] = $s;
				}

				$tariff['price_formatted'] = edost_class::GetPrice('formatted', $tariff['price'], $base_currency, $arResult['BASE_LANG_CURRENCY']);

				if ($profile == 0 || !empty($tariff['priceinfo'])) $p = '';
				else if ($tariff['price'] == 0) $p = $sign['free_bitrix'];
				else $p = $tariff['price_formatted'];
				$v['price'] = $p;

				if (!empty($tariff['day'])) $v['day'] = $tariff['day'];

				if (!empty($tariff['priceinfo'])) {
					$v['price_backup'] = $tariff['price_formatted'];
					$v['priceinfo'] = edost_class::GetPrice('formatted', $tariff['priceinfo'], $base_currency, $arResult['BASE_LANG_CURRENCY']);

					$s0 = $v['DESCRIPTION'];
					$s1 = str_replace('%price_info%', $v['priceinfo'], $sign['priceinfo_warning_bitrix']);
					$s2 = ($tariff['price'] > 0 ? str_replace('%price%', $tariff['price_formatted'], $sign['priceinfo_description']) : '');
					$v['DESCRIPTION'] = $s1 . ($s1 != '' && $s2 != '' ? '<br>' : '') . $s2 . (($s1 != '' || $s2 != '') && $s0 != '' ? '<br>' : '') . $s0;
				}

				if (!empty($tariff['format'])) {
					if ($tariff['format'] == 'house') $v['DESCRIPTION'] = $sign['house_warning_bitrix'].($v['DESCRIPTION'] != '' ? '<br>' : '').$v['DESCRIPTION'];
					if ($tariff['format'] == 'terminal' && $office_number > 1) $v['DESCRIPTION'] = $sign['terminal_warning_bitrix'].($v['DESCRIPTION'] != '' ? '<br>' : '').$v['DESCRIPTION'];
				}

				// PickPoint
				if ($profile == 57 && !empty($v['pickpointmap'])) {
					if ($converted) $arResult['edost']['pickpoint_id'] = $v['ID'];

					if (isset($_SESSION['EDOST']['location_pickpoint']) && $_SESSION['EDOST']['location_pickpoint'] != $arUserResult['DELIVERY_LOCATION']) $_SESSION['EDOST']['address'][$tariff['company_id']] = '';
					else if ($office_set === 'pickpoint') {
						if (isset($arResult['edost']['order_prop']['ADDRESS'])) $address = $arResult['edost']['order_prop']['ADDRESS']['value'];
						else $address = (isset($arResult['edost']['address_value']) ? $arResult['edost']['address_value'] : '');
						$_SESSION['EDOST']['address'][$tariff['company_id']] = $address;
					}

					$s = (isset($_SESSION['EDOST']['address'][$tariff['company_id']]) ? $_SESSION['EDOST']['address'][$tariff['company_id']] : '');
					if ($v['CHECKED'] == 'Y') {
						$arResult['edost']['format']['active']['address'] = $s;
						if (strpos($s, $sign['postamat']['name'].' PickPoint') === 0) $arResult['edost']['cod_description2'] = true;
					}
					if ($s != '') {
						$s1 = explode(': ', $s);
						$s2 = explode(', '.$sign['code'].': ', $s);
						$s = ($s1[0] == $sign['postamat']['name'].' PickPoint' ? $sign['delivery']['postamat'] : $sign['delivery']['office']).': <b>'.str_replace($s1[0].': ', '', $s2[0]).'</b><br>';
					}
					else {
						$s = $sign['postamat']['get'];
						$v['onclick'] = "PickPoint.open(EdostPickPoint,{city:'".$v['pickpointmap']."', ids:null}); edost_SubmitActive('set'); submitForm();";
					}
					$v['office'] = '<a style="color: #A00; text-decoration: none;" href="#" id="EdostPickPointRef" onclick="PickPoint.open(EdostPickPoint,{city:\''.$v['pickpointmap'].'\', ids:null}); return false;">'.$s.'</a>';
				}

				if ($config['template'] == 'N3') {
					if (!empty($v['office'])) $v['DESCRIPTION'] = $v['office'].(!empty($v['DESCRIPTION']) ? '<br>' : '').$v['DESCRIPTION'];

					if (isset($v['pricetotal_formatted'])) $v['PRICE_FORMATED'] = $v['pricetotal_formatted'];
					else if ($v['PRICE_FORMATED'] == '0') $v['PRICE_FORMATED'] = $sign['free'];
				}

				if ($converted) $arResult['DELIVERY'][$k] = $v;
				else $arResult['DELIVERY']['edost']['PROFILES'][$profile] = $v;
			}
		}
//		echo '<br><b>arResult[DELIVERY] NEW:</b> <pre style="font-size: 12px">'.print_r($arResult['DELIVERY'], true).'</pre>';


		// пересчет стоимости доставки (для тарифов eDost или после изменения активной доставки)
		if (!$isNew) {
			$format_active = (!empty($arResult['edost']['format']['active']) ? $arResult['edost']['format']['active'] : false);
			$id = (isset($format_active['id']) ? $format_active['id'] : '');
			$automatic = (isset($format_active['automatic']) ? $format_active['automatic'] : '');
			$profile = (isset($format_active['$profile']) ? $format_active['$profile'] : '');
			if (empty($id) || $automatic == 'edost' || $id !== $bitrix_delivery_id) {
				if (isset($arResult['edost']['error'])) $arResult['ERROR'] = $arResult['edost']['error'];

				$price = false;
				if (!empty($id)) if ($automatic == 'edost') {
					$office_type = (!empty($format_active['office_type']) ? $format_active['office_type'] : 0);
					$tariff = CDeliveryEDOST::GetEdostTariff($profile, $office_type);
					$price = edost_class::GetPrice('price', $tariff['price'], $base_currency, $arResult['BASE_LANG_CURRENCY']);
				}
				else if ($automatic != '') {
					if ($converted) {
						$shipment = CSaleDelivery::convertOrderOldToNew($order);
						$service = \Bitrix\Sale\Delivery\Services\Manager::getObjectById($id);
						$ar = $service->calculate($shipment);
						if ($ar->isSuccess()) $price = edost_class::GetPrice('price', $ar->getPrice(), '', $arResult['BASE_LANG_CURRENCY']);
						else {
							$s = $ar->getErrorMessages();
							if (empty($s)) $s = array();
							$arResult['ERROR'][] = implode('<br>', $s);
						}
					}
					else {
						$ar = CSaleDeliveryHandler::CalculateFull($automatic, $profile, $order, $arResult['BASE_LANG_CURRENCY']);
						if ($ar['RESULT'] == 'ERROR') $arResult['ERROR'][] = (!empty($ar['TEXT']) ? $ar['TEXT'] : 'delivery error');
						else {
							$price = edost_class::GetPrice('price', $ar['VALUE'], '', $arResult['BASE_LANG_CURRENCY']);
							if (isset($ar['PACKS_COUNT'])) $arResult['PACKS_COUNT'] = $ar['PACKS_COUNT'];
						}
					}
				}
				else foreach ($arResult['DELIVERY'] as $v) if (isset($v['ID']) && $v['ID'] == $id) {
					$price = edost_class::GetPrice('price', $v['PRICE'], $v['CURRENCY'], $arResult['BASE_LANG_CURRENCY']);
					break;
				}

				$arResult['DELIVERY_PRICE'] = (!empty($price['price']) ? $price['price'] : 0);
				$arResult['DELIVERY_PRICE_FORMATED'] = (!empty($price['price']) ? $price['price_formatted'] : '');
			}
		}

		// привязка пункта выдачи eDost к складу битрикса
		if (defined('DELIVERY_EDOST_BUYER_STORE')) {
			$store = array();
			$ar = explode(',', DELIVERY_EDOST_BUYER_STORE);
			foreach ($ar as $v) {
				$v = explode('=', $v);
				if (!empty($v[0]) && !empty($v[1])) $store[$v[0]] = $v[1];
			}
			$office_id = (!empty($format_active['office_id']) ? $format_active['office_id'] : 0);
			if (isset($store[$office_id])) {
				$arUserResult['DELIVERY_STORE'] = $arUserResult['DELIVERY_ID']; // id выбранной доставки, которой принадлежит привязанный офис
				$arResult['BUYER_STORE'] = $store[$office_id];
			}
		}

	}


	// вызывается после обработки платежных систем
	function OnSCOrderOneStepPaySystemHandler(&$arResult, &$arUserResult, &$arParams, $isNew = false) {
//		$_SESSION['EDOST']['run'] .= '<br>EVENT_PAYMENT: '.$arUserResult['DELIVERY_ID'].' - '.$arUserResult['DELIVERY_LOCATION'];
//		$_SESSION['EDOST']['arResult_PaySystem'] = $arResult;
//		echo '<br><b>arResult[DELIVERY]:</b> <pre style="font-size: 12px">'.print_r($arResult['DELIVERY'], true).'</pre>';
//		echo '<br><b>arResult[PAY_SYSTEM]:</b> <pre style="font-size: 12px">'.print_r($arResult['PAY_SYSTEM'], true).'</pre>';
//		echo '<br><b>ORDER_PROP:</b> <pre style="font-size: 12px">'.print_r($arResult['ORDER_PROP']['USER_PROPS_Y'], true).'</pre>';

		$config = (isset($arResult['edost']['config']) ? $arResult['edost']['config'] : self::GetEdostConfig(SITE_ID));
		if (empty($config) || $config['template'] == 'off') return;

		$sign = GetMessage('EDOST_DELIVERY_SIGN');
		$protocol = CDeliveryEDOST::GetProtocol();
		$arResult['edost']['javascript'] = '';
		$locations_installed = (!empty($arResult['edost']['locations_installed']) ? true : false);
		$format_active = (!empty($arResult['edost']['format']['active']) ? $arResult['edost']['format']['active'] : false);

		$address_id = (isset($arResult['edost']['address_id']) && !$locations_installed ? $arResult['edost']['address_id'] : -1);
		$address = (isset($arResult['edost']['address_value']) ? $arResult['edost']['address_value'] : '');
		if ($address_id != -1 && !isset($arResult['ORDER_PROP']['USER_PROPS_Y'][$address_id])) $address_id = -1;
		if ($address_id != -1) $arResult['edost']['javascript'] .= '<input type="hidden" value="ORDER_PROP_'.$address_id.'" id="edost_address_input">';


		// предупреждения модуля edost (warning)
		$warning = CDeliveryEDOST::GetEdostWarning();
		if ($warning != '') {
			// вывод ошибки при подтверждении заказа, если перед оформлением была выбрана почта (наземная, посылка онлайн, курьер онлайн) и есть предупреждение по индексу
			if ($arUserResult['CONFIRM_ORDER'] == 'Y' && isset($arResult['edost']['active']['profile']) && in_array(ceil(intval($arResult['edost']['active']['profile']) / 2), CDeliveryEDOST::$zip_required))
				foreach (CDeliveryEDOST::$result['warning'] as $v) if (in_array($v, array(1, 2))) {
					$s = GetMessage('EDOST_DELIVERY_WARNING');
					$arResult['ERROR'][] = $s[$v];
				}

			// для стандартного шаблона
			if ($config['template'] != 'Y') $arResult['edost']['warning'] = '<span id="edost_warning" style="color: #F00; font-weight: bold;">'.$warning.'</span>';
			if ($config['template'] == 'N3') $arResult['WARNING']['DELIVERY'][] = $warning . $sign['post_zip'];
		}


		// сохранение нового адреса в поле ADDRESS
		if ($address_id != -1) {
			$office_set = (!empty($_REQUEST['edost_office']) ? substr($_REQUEST['edost_office'], 0, 10) : 0); // выбранный офис из POST
			$address_readonly = (isset($format_active['address']) ? true : false);
			$address_new = ($address_readonly ? $format_active['address'] : false);

			if (empty($_SESSION['EDOST']['readonly']) && $office_set !== 'pickpoint') {
				$office = edost_class::ParseOfficeAddress($address);
				if (empty($office)) $_SESSION['EDOST']['address'][0] = $address;
			}
			else if (!$address_readonly) $address_new = (isset($_SESSION['EDOST']['address'][0]) ? $_SESSION['EDOST']['address'][0] : '');

			if ($address_new !== false) {
				$address = $address_new;
				$arResult['ORDER_PROP']['USER_PROPS_Y'][$address_id]['VALUE'] = $address;
				$arUserResult['ORDER_PROP'][$address_id] = $address;
			}

			$_SESSION['EDOST']['readonly'] = $address_readonly;
			if ($config['template'] != 'Y') $_SESSION['EDOST']['location_pickpoint'] = $arUserResult['DELIVERY_LOCATION'];
		}


		// удаление способов оплаты, если нет способов доставки или доставка не выбрана
		if ($config['hide_payment'] == 'Y' && (empty($arResult['edost']['format']['count']) || empty($arUserResult['DELIVERY_ID']))) $arResult['PAY_SYSTEM'] = array();

		// ошибка "не выбран способ доставки"
		if ($config['autoselect'] != 'Y' && !empty($arResult['edost']['format']['count']) && empty($arUserResult['DELIVERY_ID'])) $arResult['ERROR'][] = $sign['delivery_unchecked'];

		// ошибка "не выбрана точка самовывоза"
		if (!empty($address_readonly) && $arResult['ORDER_PROP']['USER_PROPS_Y'][$address_id]['VALUE'] == '') $arResult['ERROR'] = array($sign['office_unchecked']);


		$cod_tariff = (!empty($arResult['edost']['format']['cod_tariff']) ? true : false);
		$cod_tariff_active = (!empty($format_active['cod_tariff']) ? true : false);
		if ($cod_tariff && $cod_tariff_active) $arUserResult['PAY_CURRENT_ACCOUNT'] = false;


		// удаление наложенного платежа для тарифов без наложки
		$tariff = false;
		$office_type = (!empty($format_active['office_type']) ? $format_active['office_type'] : 0);
		if (!empty($format_active['automatic']) && $format_active['automatic'] == 'edost' && $format_active['profile'] !== '') {
			$tariff = CDeliveryEDOST::GetEdostTariff($format_active['profile'], $office_type);
			if (!isset($tariff['pricecash']) || $tariff['pricecash'] < 0) $tariff = false;
			if (empty($format_active['cod'])) $tariff = false;
			if ($cod_tariff && !$cod_tariff_active) $tariff = false;
		}
		$acitve = $set = $edost = false;
		foreach ($arResult['PAY_SYSTEM'] as $k => $v) {
			if (substr($v['PSA_ACTION_FILE'], -11) == 'edostpaycod')
				if ($tariff !== false) $edost = $k;
				else {
					unset($arResult['PAY_SYSTEM'][$k]);
					continue;
				}
			if ($set === false) $set = $k;
			if ($v['CHECKED'] == 'Y') $acitve = $k;
		}
		if ($cod_tariff && $cod_tariff_active && $acitve !== false && $edost !== false && $acitve != $edost) {
			unset($arResult['PAY_SYSTEM'][$acitve]);
			$acitve = false;
			$set = $edost;
		}

		// выделение первого способа оплаты, если нет активных
		if ($acitve === false && $set !== false && (empty($arUserResult['PAY_CURRENT_ACCOUNT']) || $arUserResult['PAY_CURRENT_ACCOUNT'] !== 'Y' || $cod_tariff && $cod_tariff_active)) {
			$arResult['PAY_SYSTEM'][$set]['CHECKED'] = 'Y';
			$acitve = $set;
		}

		if (!$isNew) {
			$arUserResult['PAY_SYSTEM_ID'] = ($acitve !== false ? $arResult['PAY_SYSTEM'][$acitve]['ID'] : '');
		}

		// учет наценок наложенного платежа
		if ($edost !== false) {
			$v = $arResult['PAY_SYSTEM'][$edost];
			$base_currency = CDeliveryEDOST::GetRUB();

			// нестандартное название и описание
			$ar = GetMessage('EDOST_DELIVERY_COD');
			if (is_array($ar)) foreach ($ar as $s) if (in_array($tariff['id'], $s['tariff'])) {
				if (isset($s['name'])) $v['PSA_NAME'] = $s['name'];
				if (isset($s['description'])) $v['DESCRIPTION'] = $s['description'];
				if (isset($s['description2']) && (!empty($arResult['edost']['cod_description2']) || !empty($office_type) && in_array($office_type, array(5, 6)))) $v['DESCRIPTION'] = $s['description2'];
			}

			$p = array();
			$p += edost_class::GetPrice('codplus', $tariff['pricecash'] - $tariff['price'], $base_currency, $arResult['BASE_LANG_CURRENCY']);
			$p += edost_class::GetPrice('transfer', $tariff['transfer'], $base_currency, $arResult['BASE_LANG_CURRENCY']);
			$p += edost_class::GetPrice('codtotal', $tariff['pricecash'] - $tariff['price'] + $tariff['transfer'], $base_currency, $arResult['BASE_LANG_CURRENCY']);

			if (!empty($p['codplus'])) $v['codplus'] = str_replace('%codplus%', $p['codplus_formatted'], $sign['codplus']);
			if (!empty($p['transfer'])) $v['transfer'] = str_replace('%transfer%', $p['transfer_formatted'], $sign['transfer']);
			if (!empty($p['codplus']) && !empty($p['transfer'])) $v['codtotal'] = str_replace('%codtotal%', $p['codtotal_formatted'], $sign['codtotal']);

			// в стандартном шаблоне информация по наценке добавляется в описание
			if ($config['template'] != 'Y') {
				$ar = array('codplus', 'transfer', 'codtotal');
				foreach ($ar as $v2) if (!empty($v[$v2])) $v['DESCRIPTION'] .= ($v['DESCRIPTION'] != '' ? '<br>' : '').($v2 == 'transfer' ? '<font color="#FF0000">'.$v[$v2].'</font>' : $v[$v2]);
			}

			if (!$isNew && isset($v['CHECKED']) && $v['CHECKED'] == 'Y') {
				$p += edost_class::GetPrice('pricecash', $tariff['pricecash'], $base_currency, $arResult['BASE_LANG_CURRENCY']);
				$arResult['DELIVERY_PRICE'] = $p['pricecash'];
				$arResult['DELIVERY_PRICE_FORMATED'] = (!empty($p['pricecash']) ? $p['pricecash_formatted'] : '');
			}

			$arResult['PAY_SYSTEM'][$edost] = $v;
		}
//		echo '<br><b>arResult[PAY_SYSTEM] NEW:</b> <pre style="font-size: 12px">'.print_r($arResult['PAY_SYSTEM'], true).'</pre>';


		if ($config['template'] == 'Y') {
			if (!isset($arResult['edost']['format'])) $arResult['edost']['format'] = false;
		}
		else if (isset($arResult['edost']['format'])) unset($arResult['edost']['format']);


		// javascript - офисы (стандартный шаблон)
		if (!in_array($config['template'], array('Y', 'N3')) && ($address_id != -1 || $locations_installed)) $arResult['edost']['javascript'] .= '
		<input type="hidden" value="" id="edost_office" name="edost_office">';

		// javascript - PickPoint (стандартный шаблон)
		if (!in_array($config['template'], array('Y', 'N3')) && ($address_id != -1 || $locations_installed) && $config['map'] == 'Y' && !empty($arResult['edost']['pickpoint_widget'])) $arResult['edost']['javascript'] .= '
		<input type="hidden" value="" id="edost_submit_active">
		<script type="text/javascript">
			function edost_SubmitActive(n) {
				var E = document.getElementById("edost_submit_active");
				if (E) {
					if (n == "set") E.value = "Y";
					else return (E.value == "Y" ? true : false);
				}
			}

			function EdostPickPoint(rz) {
				if (edost_SubmitActive("get")) return false;

				var s = (rz[\'name\'].substr(0, 3) == "'.$sign['postamat']['pvz'].'" ? "'.$sign['office'].'" : "'.$sign['postamat']['name'].'") + " PickPoint: ";

				if (rz[\'shortaddress\'] != undefined) rz[\'address\'] = rz[\'shortaddress\'];
				var i = rz[\'address\'].indexOf("'.$sign['postamat']['rf'].'");
				if (i > 0) rz[\'address\'] = rz[\'address\'].substr(i + 22);
				var s2 = rz[\'name\'];
				var i = s2.indexOf(":");
				if (i > 0) s2 = s2.substr(i + 1).replace(/^\s+/g, "");
				s2 = s2.trim();
				if (s2 != "") rz[\'address\'] += " (" + s2 + ")";

				rz[\'id\'] = ", '.$sign['code'].': " + rz[\'id\'];

				s += rz[\'address\'] + rz[\'id\'];

				var E = document.getElementById("edost_shop_ADDRESS");
				if (E) E.value = s;
				else {
					E = document.getElementById("edost_address_input");
					if (E) E = document.getElementById(E.value);
					if (E) E.value = s;
				}
				if (!E) return;

				var E = document.getElementById("EdostPickPointRef");
				if (E) E.innerHTML = "'.$sign['loading'].'";

				var E = document.getElementById("edost_office");
				if (E) E.value = "pickpoint";

				var E = document.getElementById("ID_DELIVERY_'.(!empty($arResult['edost']['pickpoint_id']) ? 'ID_'.$arResult['edost']['pickpoint_id'] : 'edost_57').'");
				if (E && !E.checked) E.checked = true;

		        submitForm();
			}
		</script>';

		// javascript - блокировка поля ADDRESS, если выбран тариф с офисом
		if ($config['template'] != 'N3' && $address_id != -1) $arResult['edost']['javascript'] .= '
		<script type="text/javascript">
			var E = document.getElementById(document.getElementById("edost_address_input").value);
			if (E) {'.
				(!empty($address_readonly) ? '
				E.readOnly = true; E.style.color = "#707070"; E.style.backgroundColor = "#E0E0E0";' : '
				E.readOnly = false; E.style.color = "#000000"; E.style.backgroundColor = "#FFFFFF";').'
			}
		</script>';

	}

}
?>