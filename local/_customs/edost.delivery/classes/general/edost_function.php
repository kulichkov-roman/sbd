<?
/*********************************************************************************
Пользовательские функции модуля eDost (при обновлении данный файл не переписывается)

Для подключения в файле 'edost_const.php' должна быть установлена константа:
define('DELIVERY_EDOST_FUNCTION', 'Y');
*********************************************************************************/

class edost_function {

	// вызывается перед расчетом доставки
	public static function BeforeCalculate(&$order, &$config) {
/*
		$order - оригинальный массив битрикса с параметрами расчета
		$config - настройки модуля

		return false; // продолжить выполнение расчета
		return array('hide' => true); // отключить модуль (не производится запрос на сервер, не выводится ошибка)
		return array('data' => array( тарифы доставки )); // сбросить расчет и заменить результат массивом 'data' (формат должен соответствовать стандарту eDost)
*/

		// echo '<br><b>BeforeCalculate - arOrder:</b> <pre style="font-size: 12px">'.print_r($order, true).'</pre>';


//		echo '<br>SERVER[REQUEST_URI]:'.$_SERVER['REQUEST_URI'];
//		$_SESSION['EDOST']['REQUEST_URI'] = $_SERVER['REQUEST_URI'];
//		unset($_SESSION['EDOST']['office_default']); // сбросить выбранные на карте офисы
//		$order['LOCATION_TO'] = 10000000000;

/*
		// вывести собственный тариф для указанных местоположений (вместо реального расчета)
		$ar = array(5979, 5980); // ID местоположений
		if (in_array($order['LOCATION_TO'], $ar)) {
			$order['location'] = CDeliveryEDOST::GetEdostLocation($order['LOCATION_TO']);
			if ($order['location'] === false) return false;

			return array(
				'sizetocm' => '1', // коэффициент пересчета габаритов магазина в сантиметры
				'data' => array(
					9 => array( // тариф "СПСР Экспресс"
						'id' => 5,
						'price' => 400,
						'priceinfo' => 0,
						'pricecash' => 500,
						'transfer' => 0,
						'day' => '3-4 дня',
						'insurance' => 0,
						'company' => 'СПСР Экспресс',
						'name' => 'пеликан-стандарт',
						'format' => 'door',
						'company_id' => 1,
						'city' => '',
						'profile' => 9,
						'sort' => 4,
					)
				)
			);
		}
*/
		// меняем конфиг едоста для москвы
		if (in_array($_SESSION['VREGIONS_REGION']['ID'], array(
			14668,
14715,
14740,
14745,
14760,
14774,
14781,
14784,
14786,
14790,
14802,
14806,
14809,
14813,
14819,
14827,
14847,
14863,
14864,
14868,
14876,
14884,
14888,
14925,
14926,
14928,
14931,
15033,
15039,
15042,
15046,
15082,
15084,
15097,
15104,
15109,
15135,
15160,
15174,
15178,
15185,
15194,
15196,
15197,
15198,
14646,
14685,
14729,
14778,
14783,
14886,
14932,
14952,
14959,
14965,
15003,
15009,
15016,
15022,
15041,
15048,
15164,
15195
			))) {

			$config['id'] = '10524';
			$config['ps'] = 'mh53cnO2DZF41DTSl0ZdmsElr1BPfCb6';

			  // запрет на расчет едосту из москвы в москву
			  // if($order['LOCATION_TO']=='0000073738')
            		// return array('hide' => true);
		}
/*
		// изменить ид и пароль от сервера eDost (например, когда у магазина несколько филиалов в разных городах, и требуется изменять город отправки в зависимости от местонахождения покупателя)
		$config['id'] = '12345';
		$config['ps'] = 'aaaaa';
*/

		// отключить модуль на странице оформления заказа
//		if (strpos($_SERVER['REQUEST_URI'], '/personal/order/make') === 0) return array('hide' => true);

		// отключить модуль в карточке товара
//		if (strpos($_SERVER['REQUEST_URI'], '/catalog') === 0 || strpos($_SERVER['REQUEST_URI'], '/bitrix/components/edost/catalogdelivery') === 0) return array('hide' => true);

/*
		// отключить модуль для указанных местоположений
		$ar = array(5979, 5980); // ID местоположений
		if (in_array($order['LOCATION_TO'], $ar)) return array('hide' => true);
*/

		return false;

	}

	// вызывается после обработки параметров заказа и перед запросом на сервер eDost
	public static function BeforeCalculateRequest(&$order, &$config) {
/*
		$order - модифицированный массив битрикса с параметрами расчета
		$config - настройки модуля

		return false; // продолжить выполнение расчета
		return array('hide' => true); // отключить модуль (не производится запрос на сервер, не выводится ошибка)
		return array('data' => array( тарифы доставки )); // сбросить расчет и заменить результат массивом 'data' (формат должен соответствовать стандарту eDost)

		расчет производится по параметрам:
			$order['LOCATION_TO'] - ид местоположения битрикса
			$order['LOCATION_ZIP'] - почтовый индекс (если пустой, тогда не передается на сервер расчета)
			$order['WEIGHT'] - вес заказа в граммах
			$order['PRICE'] - цена заказа в рублях
			$order['size'] - массив с габаритами заказа (единица измерения должна совпадать с размерностью в личном кабинете eDost)
				Предупреждение: на выходе габариты должны быть отсортированы по возрастанию - пример: $order['size'] = array(30, 10, 20);  sort($order['size']);
*/
		// global $USER;
// if ($USER->IsAdmin()): 
//		echo '<br><b>BeforeCalculateRequest - arOrder:</b> <pre style="font-size: 12px">'.print_r($order, true).'</pre>';
// endif;
//		$order['size'] = array(10, 20, 30);
//		$order['LOCATION_TO'] = 1;
//		$order['WEIGHT'] = 500;
//		$order['WEIGHT'] += 32000;
//		$order['PRICE'] = 1000;

/*
		// установить местоположение расчета по стандарту eDost (вместо стандартного расчета по коду битрикса $order['LOCATION_TO'])
		$order['location'] = array(
		    'country' => 0, // код страны стандарта eDost (0 - Россия)
		    'region' => 59, // код региона стандарта eDost
		    'city' => 'Пермь', // название города в кодировке win
		);
//		$order['LOCATION_TO'] = 100; // если 'LOCATION_TO' не передан, тогда для корректной работы кэширования, обязательно должен быть присвоен уникальный код местоположения (можно свой)
*/

/*
		// добавить вес на упаковку для указанных местоположений
		$ar = array(5979, 5980); // ID местоположений
		if (in_array($order['LOCATION_TO'], $ar)) $order['WEIGHT'] += 1000;
*/

		return false;

	}

	// вызывается после расчета доставки
	public static function AfterCalculate($order, $config, &$result) {

			if (strpos($_SERVER['REQUEST_URI'], '/catalog') === 0 || strpos($_SERVER['REQUEST_URI'], '/bitrix/components/edost/catalogdelivery') === 0)
			if (!empty($result['data'])) foreach ($result['data'] as $k => $v) $result['data'][$k]['format'] = '';

			//define("LOG_FILENAME", __DIR__."/log.txt"); 
			//global $USER;
			//if($USER->IsAdmin()){
				//AddMessage2Log($_SERVER);
				//$order['ID']
			//}
			
			/*RBS_CUSTOM_START*/
			$regionId = false;
			$bSibCore = \Bitrix\Main\Loader::includeModule('sib.core');	

			if((defined("ADMIN_SECTION") && ADMIN_SECTION === true) && (int)$order['ID'] > 0 && $bSibCore){
				$orderObj = \Bitrix\Sale\Order::load((int)$order['ID']);
				$propertyCollection = $orderObj->getPropertyCollection();
				if($locPropValue = $propertyCollection->getDeliveryLocation()){
					$regionId = \Sib\Core\Regions::getRegionIdByLoc($locPropValue->getValue());
				} else {
					$regionId = (int)$propertyCollection->getItemByOrderPropertyId(20)->getValue();
				}
				
				if($regionId){
					$arProps = \Sib\Core\Regions::getRegionProps($regionId);
				}	
			}

					
			foreach ($result['data'] as $k => $v){
				if($bSibCore && in_array((int)$v['id'], [37, 38])){
					$interval = explode('-', explode(' ', $v['day'])[0]);
					if(count($interval) === 2){
						if((int)$interval[0] > 0 && (int)$interval[1] > 0){
							$result['data'][$k]['day'] = \Sib\Core\Edost::getDayIntervalText((int)$interval[0], (int)$interval[1]);
						}                    
					}
				}
				if((int)$order['PRICE'] <= 6000){
					switch((int)$v['id']){
						case 37:
							$result['data'][$k]['price'] = 290;
						break;
						case 38:
							$result['data'][$k]['price'] = 390;
						break;
					}
				}

				$arTarrifs = false;
				if($bSibCore){
					if(isset($arProps) && is_array($arProps)){
						if(isset($arProps['EDOST_CUSTOM_PRICE_' . $v['id']])){
							$arTarrifs = $arProps['EDOST_CUSTOM_PRICE_' . $v['id']]['VALUE'];
						}
					}					
					$customPrice = \Sib\Core\Edost::getCustomPrice($v['id'], (int)$order['original']['WEIGHT'], (int)$order['PRICE'], $arTarrifs, $regionId);
					if($customPrice > 0){
						$result['data'][$k]['price'] = $customPrice;
					}
				}				
			}
			
			/*RBS_CUSTOM_END*/
			
/*
		$order - модифицированный массив битрикса с параметрами расчета
		$config - настройки модуля
		$result - результат расчета
*/
// 		global $USER;
// if ($USER->IsAdmin()): 
// 		echo '<br><b>AfterCalculate - order:</b> <pre style="font-size: 12px">'.print_r($order, true).'</pre>';
// 		echo '<br><b>AfterCalculate - result:</b> <pre style="font-size: 12px">'.print_r($result, true).'</pre>';
// endif;
/*
		// исключение стоимости доставки из итого (для почты и EMS - id: 1, 2, 3)
		if (!empty($result['data'])) foreach ($result['data'] as $k => $v)
			if (in_array($v['id'], array(1, 2, 3)) && $v['price'] > 0) {
				$result['data'][$k]['priceinfo'] = $v['price'];
				$result['data'][$k]['price'] = 0;
			}
*/

		// удалить из расчета тариф "DPD (parcel до пункта выдачи)" (код 91)
		// if (isset($result['data']['413'])) unset($result['data']['413']);

/*
		// изменение стоимости доставки тарифа "PickPoint" (код 57)
		if (isset($result['data']['57'])) {
			// установка фиксированной стоимости доставки для указанных местоположений
			$ar = array(5979, 5980); // ID местоположений
			if (in_array($order['LOCATION_TO'], $ar)) {
				$result['data']['57']['price'] = 250; // стоимость доставки
				$result['data']['57']['pricecash'] = 250; // стоимость доставки при наложенном платеже (-1 - отключить наложенный платеж)
			}

			// установить эксклюзивную стоимость для пунктов выдачи с типом 5
			$result['data']['57']['priceoffice'] = array(
				5 => array(
					'type' => 5,
					'price' => $result['data']['57']['price'] + 100, // стандартная цена доставки + 100 руб.
					'priceinfo' => 0,
					'pricecash' => 800, // наложка
				),
			);
		}
*/
	}


	// вызывается после загрузки данных по пунктам выдачи
	public static function AfterGetOffice($order, &$result) {
/*
		$order - параметры заказа
		$result - пункты выдачи
*/
//		echo '<br><b>AfterGetOffice - order:</b> <pre style="font-size: 12px">'.print_r($order, true).'</pre>';
//		echo '<br><b>AfterGetOffice - result:</b> <pre style="font-size: 12px">'.print_r($result, true).'</pre>';


		// удалить пункты выдачи тарифа 'Самовывоз 1' (код 's1')
//		if (isset($result['data']['s1'])) unset($result['data']['s1']);

/*
		// вывести пункт выдачи для тарифа 'Самовывоз 1' (код 's1')
		$result['data']['s1'] = array(
			'12345A12345' => array(
				'id' => '12345A12345',
				'code' => '',
				'name' => 'ТЦ Калач',
				'address' => 'Москва, ул. Академика Янгеля, д. 6, корп. 1',
				'address2' => 'оф. 5',
				'tel' => '+7-123-123-45-67',
				'schedule' => 'с 10 до 20, без выходных2222',
				'gps' => '37.592311,55.596037',
				'type' => 3,
				'metro' => '',
			),
		);
*/
	}


	// вызывается после загрузки документов (почтовые бланки, шаблоны для печати и т.д.)
	public static function AfterGetDocument($setting, &$result) {
/*
		$setting - настройки печати
		$result - документы
*/
//		echo '<br><b>AfterGetDocument - setting:</b> <pre style="font-size: 12px">'.print_r($setting, true).'</pre>';
//		echo '<br><b>AfterGetDocument - result:</b> <pre style="font-size: 12px">'.print_r($result, true).'</pre>';

/*
		// заполнение полей отправителя в ф.116 (по умолчанию поля заполняются значениями из настроек печатных форм битрикса)
		$result['data']['116']['data'] = str_replace(
			array('%company_name%', '%company_address%', '%company_zip%'),
			array('ООО "Ромашка"', 'ул. Академика Янгеля, д. 6, г. Москва', '101000'),
			$result['data']['116']['data']
		);
*/
	}

}
?>