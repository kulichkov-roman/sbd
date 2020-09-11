<?
/*********************************************************************************
���������� ������� �������� ������������ eDost.ru
������ 2.0.7, 09.01.2017
�����: ��� "������"

�������� �������� � ��������� ������� �������� � ������ �������� eDost.ru (��������� �����������: http://edost.ru/reg.php)


������ ������� �������:
--------------------------------------------------------------------------------
$order = array(
	// ����������� ���������
	// ���� �������� ������ 'WEIGHT' � 'PRICE' (��� 'ITEMS'), ����� �������� �������������� �� ������� ������� �������� (���� ������� �� ������)!!!
	// �������� �� ��������������, ���� � 'ITEMS' (��� �������) ���� ���� ���� ����� � ������� �����!!!
	'SITE_ID' => SITE_ID,
	'CURRENCY' => 'RUB',
	'WEIGHT' => 1000, // ��� � �������
	'PRICE' => 2000, // ��������� ������ � ������ (��� ������� ���������)
	'LOCATION_FROM' => COption::GetOptionString('sale', 'location', false, SITE_ID), // ��� ������ edost ��������� �� ���������
	'LOCATION_TO' => 234, // ID ��������������, ���� ���������� ���������� ��������
	'LOCATION_ZIP' => 620004, // �������� ������ (������ ��� ������� ����� �������� ������� - ���������� � ������� �������� � ���������� ������ edost)
	'ITEMS' => array( // ������ � ������� (��������� � bitrix 14, ������� � bitrix 15.5 ��������� �����������!!!)
		'0' => array(
			'ID' => 10,
			'PRODUCT_ID' => 20,
			'CAN_BUY' => 'Y',
			'PRICE' => 2000,
			'CURRENCY' => 'RUB',
			'QUANTITY' => 1,
			'WEIGHT' => 1000,
			'CALLBACK_FUNC' => 'CatalogBasketCallback',
			'MODULE' => 'catalog',
			'DIMENSIONS' => Array(
				'WIDTH' => 10,
				'HEIGHT' => 20,
				'LENGTH' => 30
			)
		),
	),

	// �������������� ��������� ��� ������ edost (��������� �� �����������, ������� � bitrix 15.5 ��� ������ ����� ����������� ������� ������� � ������ �� ����������!!!)
	'NO_LOCAL_CACHE' => 'Y', // 'Y' - �� ������������ ��������� ��� ������ CDeliveryEDOST (�������� ����� ��������������� ��� ������ �������)
	'CART' => 'Y', // 'Y' - ������ �� 'ITEMS' (�� ���������),  'N' - ������ �� ���������� 'WEIGHT', 'PRICE', 'LENGTH', 'WIDTH', 'HEIGHT', 'QUANTITY',  'DOUBLE' - ��� ��������
	'QUANTITY' => 1, // ����������
	'LENGTH' => 0, 'WIDTH' => 0, 'HEIGHT' => 0, // �������� (����������� ��������� ������ ��������� � ������������ � ������ �������� edost)
	'ORDER_ID' => '1', // ID ��� ������������ ������, ��� �������� ���������� ���������� ��������
);


// ��������� ������ ������� � �������� �������� (bitrix 16)
$delivery = array();
$shipment = CSaleDelivery::convertOrderOldToNew($order);
$services = \Bitrix\Sale\Delivery\Services\Manager::getRestrictedObjectsList($shipment);
foreach ($services as $k => $v) {
	$code = $v->getCode();
	$s = explode(':', $code);
	$automatic = (isset($s[1]) ? $s[0] : '');
	$profile = ($v->isProfile() ? true : false);

	$id = $v->getId();
	$name = ($profile && $automatic !== 'edost' ? $v->getNameWithParent() : $v->getName());

	$tariff = array(
		'ID' => $id,
		'NAME' => $name,
		'DESCRIPTION' => $v->getDescription(),
		'SORT' => $v->getSort(),
		'CODE' => $code,
		'CURRENCY' => $v->getCurrency(),
	);

	$s = $v->getLogotip();
	if (!empty($s)) $tariff['LOGOTIP'] = array('ID' => $s, 'SRC' => CFile::GetPath($s));

	// ������ ��������
	$s = $v->calculate($shipment);
	if ($s->isSuccess()) $tariff += array('VALUE' => $s->getPrice(), 'TRANSIT' => $s->getPeriodDescription());
	else {
		$error = $s->getErrorMessages();
		if (empty($error)) $error = array($sing['delivery_error']);
		$tariff += array('ERROR' => implode('<br>', $error));
	}

	$delivery[] = $tariff;
}
echo '<pre>'.print_r($delivery, true).'</pre>';


// ��������� ������ ��������� ������������������ ����� �������� (bitrix 15 � ������)
$ar = CSaleDeliveryHandler::GetList(array('SORT' => 'ASC'), array('COMPABILITY' => $order));
while ($v = $ar->Fetch())
	foreach ($v['PROFILES'] as $profile_id => $profile)
		echo '<b>'.$v['SID'].':'.$profile_id.'</b> - '.$profile['TITLE'].($profile['DESCRIPTION'] !== '' ? ' ('.$profile['DESCRIPTION'].')' : '').'<br>';

// ��������� ��������� �������� ��� ������ "edost" � ����� "5" EMS ����� ������ (bitrix 15 � ������)
$v = CSaleDeliveryHandler::CalculateFull('edost', 5, $order, 'RUB');
echo '<pre>'.print_r($v, true).'</pre>';


// �������� ������ �� ������ ������ ��� ������ � ����� "5" EMS ����� ������ (����� ������� ������ ���� ������� ������ ��������� �������!!!)
if (class_exists('CDeliveryEDOST')) {
	$edost_tariff = CDeliveryEDOST::GetEdostTariff(5);
	echo '<pre>'.print_r($edost_tariff, true).'</pre>';
}
--------------------------------------------------------------------------------


��������� �������������� ������ � ��������� �������� �� ���� ������:
--------------------------------------------------------------------------------
$order = CSaleOrder::GetByID(10);
echo '<br>delivery id: '.$order['DELIVERY_ID'].'<br>delivery price: '.$order['PRICE_DELIVERY'];
--------------------------------------------------------------------------------


��������� �������� ������ �������� �� ��� ��������������:
--------------------------------------------------------------------------------
$delivery_id = 'edost:5'; // ������������� ������, ��� �������� ���������� �������� ��������
if (intval($delivery_id) > 0) {
	// ������������� ������ ��������
	$ar = CSaleDelivery::GetByID($delivery_id);
	$name = $ar['NAME'];
}
else {
	// ������������������ ������ ��������
	$id = explode(":", $delivery_id);
	if (isset($id[1])) {
		$db = CSaleDeliveryHandler::GetBySID($id[0]);
		if ($ar = $db->GetNext()) {
			$company = (isset($ar['NAME']) ? $ar['NAME'] : '');
			$name = (isset($ar['PROFILES'][$id[1]]['TITLE']) ? $ar['PROFILES'][$id[1]]['TITLE'] : '');
			$name = $company.($company != '' ? ' (' : '').$name.($company != '' ? ')' : '');
		}
	}
}
echo '<br>tariff name: '.$name;
--------------------------------------------------------------------------------


���� �������:
--------------------------------------------------------------------------------
��� �������� = ��� edost * 2 - 1
� +1, ���� ����� ����� �� ����������

���� edost: http://www.edost.ru/kln/help.html#DeliveryCode

������:
edost:1 - ����� ������ (����������� 1-�� ������)
edost:2 - ����� ������ (����������� 1-�� ������) �� ����������
edost:3 - ����� ������ (�������� �������)
edost:4 - ����� ������ (�������� �������) �� ����������
edost:5 - EMS ����� ������
--------------------------------------------------------------------------------


*********************************************************************************/


CModule::IncludeModule('sale');

IncludeModuleLangFile(__FILE__);

include_once 'edost_const.php';
if (defined('DELIVERY_EDOST_FUNCTION') && DELIVERY_EDOST_FUNCTION == 'Y') include_once 'edost_function.php';

define('DELIVERY_EDOST_TARIFF_COUNT', 68); // ���������� ������� �������� ��������� � ������ (��� �������� ������ - �� ������!)
define('DELIVERY_EDOST_SERVER', 'edost.ru'); // ������ ������� ��������
define('DELIVERY_EDOST_SERVER_ZIP', 'edostzip.ru'); // ���������� ������
define('DELIVERY_EDOST_SERVER_RESERVE', 'xn--d1ab2amf.xn--p1ai'); // �������������� ������ (�����.��)
define('DELIVERY_EDOST_SERVER_RESERVE2', 'edost.net'); // �������������� ������

class CDeliveryEDOST {
	public static $result = null;
	public static $automatic = false;
	public static $setting_key = array(
		'id' => '', 'ps' => '', 'host' => '', 'hide_error' => 'N', 'show_zero_tariff' => 'N',
		'map' => 'N', 'cod_status' => '', 'send_zip' => 'Y', 'hide_payment' => 'Y', 'sort_ascending' => 'N',
		'template' => 'N3', 'template_format' => 'odt', 'template_block' => 'off', 'template_block_type' => 'none', 'template_cod' => 'td', 'template_autoselect_office' => 'N', 'autoselect' => 'Y',
		'admin' => 'Y', 'template_map_inside' => 'N',
		'control' => 'Y', 'control_auto' => 'Y', 'control_status_arrived' => '', 'control_status_completed' => 'F', 'control_status_completed_cod' => 'F',
	);

	public static $tariff_shop = array(35,56,57,58, 31,32,33,34);
	public static $zip_required = array(1,2,3,24,61,62,68);
	public static $post_office = array(1,2,61,68);

	public static $country_code = array(0 => "������", 1 => "���������", 2 => "�������", 3 => "�����������", 4 => "�������", 5 => "�����", 6 => "������������ �����", 7 => "�������", 8 => "������", 9 => "������", 10 => "�������", 11 => "������� � �������", 12 => "���������� �������", 13 => "���������", 14 => "�������", 15 => "�����", 16 => "����������", 17 => "��������� �������", 18 => "���������", 19 => "��������", 20 => "�������", 21 => "��������", 22 => "�����", 23 => "�������", 24 => "�����", 25 => "���������� �������", 26 => "��������", 27 => "�������", 28 => "�������", 29 => "������ � �����������", 30 => "��������", 31 => "��������", 32 => "������", 33 => "������� ����", 34 => "�������", 35 => "�����", 36 => "������ � ������ �������", 37 => "�������", 38 => "��������������", 39 => "�������", 40 => "���������", 41 => "���������� ������� (����������)", 42 => "���������� ������� (���)", 43 => "��������� �����", 44 => "�������", 45 => "�����", 46 => "�����", 47 => "������", 48 => "������", 49 => "����", 50 => "���������", 51 => "���������", 52 => "������", 53 => "������ ��������������", 54 => "������-�����", 55 => "��������", 56 => "������ (����������� �������)", 57 => "���������", 58 => "��������", 59 => "�������", 60 => "�������", 61 => "����������", 62 => "������", 63 => "������", 64 => "����", 65 => "�����", 66 => "������ (����������� �������)", 67 => "�������", 68 => "��������", 69 => "������������� ����.", 70 => "������", 71 => "������", 72 => "�������� ���� ������� (����-�����)", 73 => "��������", 74 => "�������", 75 => "�����", 76 => "���������", 77 => "��������", 78 => "����", 79 => "����", 80 => "��������", 81 => "��������", 82 => "�������", 83 => "������", 84 => "�����", 85 => "���������", 86 => "��������� �������", 87 => "��������", 88 => "�������", 89 => "������", 90 => "��������� �������", 91 => "�����", 92 => "�����", 93 => "����", 94 => "��������", 95 => "��������� �������� ����������", 96 => "��������", 97 => "��������� �������", 98 => "�����", 99 => "�����, ��������������� ����.", 100 => "�����, ��������", 101 => "�����, �����", 102 => "������", 103 => "�����-����", 104 => "���-�'�����", 105 => "����", 106 => "������", 107 => "���� �������", 108 => "����������", 109 => "�������", 110 => "����", 111 => "������", 112 => "������", 113 => "�������", 114 => "�����", 115 => "�����", 116 => "�����", 117 => "�����������", 118 => "����������", 119 => "��������", 120 => "����������", 121 => "����������", 122 => "�������", 123 => "�����", 124 => "���������", 125 => "������", 126 => "��������", 127 => "����", 128 => "����������� �������", 129 => "������", 130 => "�������", 131 => "���������", 132 => "���������� �������", 133 => "�������", 134 => "����������", 135 => "��������", 136 => "�������", 137 => "������", 138 => "��������", 139 => "����������", 140 => "������", 141 => "�������", 142 => "�����", 143 => "�����", 144 => "�����", 145 => "�����", 146 => "�������", 147 => "���������� (���������)", 148 => "���������", 149 => "����", 150 => "����� ��������", 151 => "����� ���������", 152 => "��������", 153 => "������������ �������� �������", 154 => "����", 155 => "��������", 156 => "�����", 157 => "������", 158 => "�����-����� ������", 159 => "��������", 160 => "����", 161 => "������", 162 => "����������", 163 => "������-����", 164 => "�������", 165 => "������", 166 => "�������", 167 => "������", 168 => "���������", 169 => "�����", 170 => "���-������", 171 => "���-���� � ��������", 172 => "���������� ������", 173 => "���������", 174 => "�������� ��������", 175 => "����������� �������", 176 => "���-���������", 177 => "�������", 178 => "����-�������", 179 => "����-����", 180 => "����-���������", 181 => "����-�����", 182 => "����-�������", 183 => "����-������", 184 => "����-�����", 185 => "������", 186 => "��������", 187 => "�����", 188 => "��������", 189 => "��������", 190 => "���������� �������", 191 => "������", 192 => "����������", 193 => "�����", 194 => "�������", 195 => "���", 196 => "������-�����", 197 => "�����������", 198 => "�������", 199 => "�����", 200 => "�������", 201 => "��������", 202 => "����", 203 => "�����", 204 => "�������� � ������", 205 => "������", 206 => "�����", 207 => "������������", 208 => "����� � ������", 209 => "������", 210 => "������", 211 => "����������", 212 => "�������", 213 => "�������", 214 => "�����", 215 => "��������� �������", 216 => "�����", 217 => "���������", 218 => "���������", 219 => "������������ (�����������) �������", 220 => "�������", 221 => "����������� ������", 222 => "����������� ���������", 223 => "��������", 224 => "����������� ����������� ����.", 225 => "���", 226 => "����������", 227 => "�����", 228 => "����", 229 => "���������", 230 => "������", 231 => "���������", 232 => "���-�����", 233 => "�������", 234 => "�������", 235 => "�������", 236 => "�������", 237 => "���", 238 => "������", 239 => "������");
	public static $region_code = array(
		0 => array(22 => '��������� ����', 28 => '�������� �������', 29 => '������������� �������', 30 => '������������ �������', 31 => '������������ �������', 32 => '�������� �������', 33 => '������������ �������', 34 => '������������� �������', 35 => '����������� �������', 36 => '����������� �������', 79 => '��������� ��', 75 => '������������� ����', 37 => '���������� �������', 38 => '��������� �������', 7 => '���������-���������� ����������', 39 => '��������������� �������', 40 => '��������� �������', 41 => '���������� ����', 9 => '���������-���������� ����������', 42 => '����������� �������', 43 => '��������� �������', 44 => '����������� �������', 23 => '������������� ����', 24 => '������������ ����', 45 => '���������� �������', 46 => '������� �������', 47 => '������������� �������', 48 => '�������� �������', 49 => '����������� �������', 50 => '���������� �������', 51 => '���������� �������', 83 => '�������� ��', 52 => '������������� �������', 53 => '������������ �������', 54 => '������������� �������', 55 => '������ �������', 56 => '������������ �������', 57 => '��������� �������', 58 => '���������� �������', 59 => '�������� ����', 25 => '���������� ����', 60 => '��������� �������', 1 => '���������� ������', 4 => '���������� �����', 2 => '���������� ������������', 3 => '���������� �������', 5 => '���������� ��������', 6 => '���������� ���������', 8 => '���������� ��������', 10 => '���������� �������', 11 => '���������� ����', 12 => '���������� ����� ��', 13 => '���������� ��������', 14 => '���������� ���� (������)', 15 => '���������� �������� ������ - ������', 16 => '���������� ���������', 17 => '���������� ����', 19 => '���������� �������', 61 => '���������� �������', 62 => '��������� �������', 63 => '��������� �������', 64 => '����������� �������', 65 => '����������� �������', 66 => '������������ �������', 67 => '���������� �������', 26 => '�������������� ����', 68 => '���������� �������', 69 => '�������� �������', 70 => '������� �������', 71 => '�������� �������', 72 => '��������� �������', 18 => '���������� ����������', 73 => '����������� �������', 27 => '����������� ����', 86 => '�����-���������� ��', 74 => '����������� �������', 20 => '��������� ����������', 21 => '��������� ����������', 87 => '��������� ��', 89 => '�����-�������� ��', 76 => '����������� �������', 90 => '��������', 91 => '���������� ����', 77 => '������', 78 => '�����-���������', 92 => '�����������'),
	);
	public static $region_code2 = array(
		0 => array(22 => '��������� ����', 28 => '�������� �������', 29 => '������������� �������', 30 => '������������ �������', 31 => '������������ �������', 32 => '�������� �������', 33 => '������������ �������', 34 => '������������� �������', 35 => '����������� �������', 36 => '����������� �������', 79 => '��������� ��', 75 => '������������� ����', 37 => '���������� �������', 38 => '��������� �������', 7 => '���������-���������� ����������', 39 => '��������������� �������', 40 => '��������� �������', 41 => '���������� ����', 9 => '���������-���������� ����������', 42 => '����������� �������', 43 => '��������� �������', 44 => '����������� �������', 23 => '������������� ����', 24 => '������������ ����', 45 => '���������� �������', 46 => '������� �������', 47 => '������������� �������', 48 => '�������� �������', 49 => '����������� �������', 50 => '���������� �������', 51 => '���������� �������', 83 => '�������� ��', 52 => '������������� �������', 53 => '������������ �������', 54 => '������������� �������', 55 => '������ �������', 56 => '������������ �������', 57 => '��������� �������', 58 => '���������� �������', 59 => '�������� ����', 25 => '���������� ����', 60 => '��������� �������', 1 => '���������� ������', 4 => '���������� �����', 2 => '���������� ������������', 3 => '���������� �������', 5 => '���������� ��������', 6 => '���������� ���������', 8 => '���������� ��������', 10 => '���������� �������', 11 => '���������� ����', 12 => '���������� ����� ��', 13 => '���������� ��������', 14 => '���������� ���� (������)', 15 => '���������� �������� ������ - ������', 16 => '���������� ���������', 17 => '���������� ����', 19 => '���������� �������', 61 => '���������� �������', 62 => '��������� �������', 63 => '��������� �������', 64 => '����������� �������', 65 => '����������� �������', 66 => '������������ �������', 67 => '���������� �������', 26 => '�������������� ����', 68 => '���������� �������', 69 => '�������� �������', 70 => '������� �������', 71 => '�������� �������', 72 => '��������� �������', 18 => '���������� ����������', 73 => '����������� �������', 27 => '����������� ����', 86 => '�����-���������� ��', 74 => '����������� �������', 20 => '��������� ����������', 21 => '��������� ����������', 87 => '��������� ��', 89 => '�����-�������� ��', 76 => '����������� �������', 90 => '��������', 91 => '���������� ����', 77 => '������', 78 => '�����-���������', 92 => '�����������'),
		85 => array(1 => '����������� �������', 2 => '����������� �������', 3 => '����������� �������', 4 => '���������� �������', 5 => '��������-������������� �������', 6 => '���������� �������', 7 => '�������-������������� �������', 8 => '�������������� �������', 9 => '������������ �������', 10 => '�������������� �������', 11 => '������������� �������', 12 => '������������ �������', 13 => '������-������������� �������', 14 => '����-������������� �������', 15 => '������', 16 => '������'),
		21 => array(1 => '��������� �������', 2 => '��������� �������', 3 => '���������� �������', 4 => '����������� �������', 5 => '������� �������', 6 => '����������� �������', 7 => '�����'),
		14 => array(1 => '������������� �������', 2 => '���������� �������', 3 => '����������� �������', 4 => '������������� �������', 5 => '�������������� �������', 6 => '���������� �������', 7 => '��������� �������', 8 => '��������� �������', 9 => '��������� �������', 10 => '��������� �������', 11 => '������'),
		108 => array(1 => '���������� �������', 2 => '������-�������� �������', 3 => '�����-�������� �������', 4 => '��������� �������', 5 => 'O����� �������', 6 => '��������� �������', 7 => '������� �������', 8 => '������', 9 => '��'),
	);

	public static $fed_city = array(
		'id' => array(77, 78, 92), //, 15, 16, 11),
		'name' => array('������', '�����-���������', '�����������'), //, '������', '������', '������'),
		'region' => array(50, 47, 91), //, 1, 3, 11),
	);

	public static $country_flag = array(0, 21, 85, 212, 14, 108);

	public static $country_edost = array('�����, ��������������� ����.', '�����, ��������', '�����, �����', '��������', '������', '������', '������', '������');
	public static $country_bitrix = array('����� ��������������� ����.', '����� ��������', '����� �����', '����������', '������', '���������� ���������', '���������� ���������', 'Russia');

	public static $region_edost = array(
		0 => array('�������� �������', '������������� �������', '������������ �������', '������������ �������', '�������� �������', '������������ �������', '������������� �������', '����������� �������', '����������� �������', '��������� ��', '���������� �������', '��������� �������', '���������-���������� ����������', '��������������� �������', '��������� �������', '���������-���������� ����������', '����������� �������', '��������� �������', '����������� �������', '���������� �������', '������� �������', '������������� �������', '�������� �������', '����������� �������', '���������� �������', '���������� �������', '������������� �������', '������������ �������', '������������� �������', '������ �������', '������������ �������', '��������� �������', '���������� �������', '��������� �������', '���������� ������', '���������� �����', '���������� ������������', '���������� �������', '���������� ��������', '���������� ���������', '���������� ��������', '���������� �������', '���������� ����', '���������� ����� ��', '���������� ��������', '���������� ���� (������)', '���������� �������� ������ - ������', '���������� ���������', '���������� ����', '���������� �������', '���������� �������', '��������� �������', '��������� �������', '����������� �������', '����������� �������', '������������ �������', '���������� �������', '���������� �������', '�������� �������', '������� �������', '�������� �������', '��������� �������', '���������� ����������', '����������� �������', '�����-���������� ��', '����������� �������', '��������� ����������', '��������� ����������', '����������� �������', '���������� ����', '���������� ����', '�����-�������� ��', '��������� ��', '��������� ��', '���������� �������� ������ - ������', '�������� ��', '�����-���������� ��', '������', '������', '�����-���������', '�����-���������', '�����������', '�����������'),
		85 => array('���������� �������'),
	);
	public static $region_bitrix = array(
		0 => array('�������� ���', '������������� ���', '������������ ���', '������������ ���', '�������� ���', '������������ ���', '������������� ���', '����������� ���', '����������� ���', '��������� ����', '���������� ���', '��������� ���', '���������-���������� ����', '��������������� ���', '��������� ���', '���������-���������� ����', '����������� ���', '��������� ���', '����������� ���', '���������� ���', '������� ���', '������������� ���', '�������� ���', '����������� ���', '���������� ���', '���������� ���', '������������� ���', '������������ ���', '������������� ���', '������ ���', '������������ ���', '��������� ���', '���������� ���', '��������� ���', '������ ����', '����� ����', '������������ ����', '������� ����', '�������� ����', '��������� ����', '�������� ����', '������� ����', '���� ����', '����� �� ����', '�������� ����', '���� /������/ ����', '�������� ������ - ������ ����', '��������� ����', '���� ����', '������� ����', '���������� ���', '��������� ���', '��������� ���', '����������� ���', '����������� ���', '������������ ���', '���������� ���', '���������� ���', '�������� ���', '������� ���', '�������� ���', '��������� ���', '���������� ����', '����������� ���', '�����-���������� ���������� ����� - ���� ��', '����������� ���', '��������� ����', '��������� ����', '����������� ���', '���� ����', '����', '�����-�������� ���������� �����', '��������� ���������� �����', '��������� ���������� �������', '���������� �������� ������-������', '�������� ���������� �����', '�����-���������� ���������� �����', '������ - ������', '������ (������)', '�����-��������� - ������', '�����-��������� (������)', '����������� - ������', '����������� (������)'),
		85 => array('����������� �������'),
	);

	function Init() {

		$profile = array();
		$base_currency = self::GetRUB();

		$error = GetMessage('EDOST_DELIVERY_ERROR');
		$tariff = GetMessage('EDOST_DELIVERY_TARIFF');

		// ������� ����� "��������� �������� ����� ������������� �����"
		$profile[0] = array(
			'TITLE' => $error['tariff_zero'],
			'DESCRIPTION' => '',
			'RESTRICTIONS_WEIGHT' => array(0),
			'RESTRICTIONS_SUM' => array(0),
		);

		$insurance = array('', '_insurance');
		for ($i = 1; $i <= DELIVERY_EDOST_TARIFF_COUNT; $i++) foreach ($insurance as $k => $v) $profile[$i*2-1+$k] = array(
			'TITLE' => (isset($tariff['title'.$v][$i]) ? $tariff['title'.$v][$i] : ''),
			'DESCRIPTION' => (isset($tariff['description'.$v][$i]) ? $tariff['description'.$v][$i] : ''),
			'RESTRICTIONS_WEIGHT' => array(0),
			'RESTRICTIONS_SUM' => array(0),
		);

		return array(
			'SID' => 'edost',
			'NAME' => $tariff['module_name'],
			'DESCRIPTION' => '',
			'DESCRIPTION_INNER' => $tariff['module_description_inner'],
			'BASE_CURRENCY' => $base_currency,
			'HANDLER' => __FILE__,
			'DBGETSETTINGS' => array('CDeliveryEDOST', 'GetSettings'),
			'DBSETSETTINGS' => array('CDeliveryEDOST', 'SetSettings'),
			'GETCONFIG' => array('CDeliveryEDOST', 'GetConfig'),
			'COMPABILITY' => array('CDeliveryEDOST', 'Compability'),
			'CALCULATOR' => array('CDeliveryEDOST', 'Calculate'),
			'PROFILES' => $profile
		);

	}

	function GetConfig() {

		$data = GetMessage('EDOST_DELIVERY_CONFIG');

		// ��� ���� � ������� ����������
		$field = array(
			'id' => 'TEXT',
			'ps' => 'TEXT',
			'host' => 'TEXT',
			'hide_error' => 'CHECKBOX',
			'show_zero_tariff' => 'CHECKBOX',
			'map' => 'CHECKBOX',
			'send_zip' => 'CHECKBOX',
			'autoselect' => 'CHECKBOX',
			'hide_payment' => 'CHECKBOX',
			'sort_ascending' => 'CHECKBOX',
			'admin' => 'CHECKBOX',
			'cod_status' => 'DROPDOWN',

			'control' => 'CHECKBOX',
			'control_auto' => 'CHECKBOX',
			'control_status_arrived' => 'DROPDOWN',
			'control_status_completed' => 'DROPDOWN',
			'control_status_completed_cod' => 'DROPDOWN',

			'template' => 'DROPDOWN',
			'template_format' => 'DROPDOWN',
			'template_block' => 'DROPDOWN',
			'template_block_type' => 'DROPDOWN',
			'template_cod' => 'DROPDOWN',
			'template_autoselect_office' => 'CHECKBOX',
			'template_map_inside' => 'CHECKBOX',
		);

		foreach ($field as $k => $v) {
			$v = array('TYPE' => $v, 'GROUP' => 'all');
			$s = (isset($data['field'][$k]) ? $data['field'][$k] : false);
			$v['TITLE'] = (isset($s['TITLE']) ? $s['TITLE'] : '');
			$v['DEFAULT'] = (isset(self::$setting_key[$k]) ? self::$setting_key[$k] : '');
			if ($v['TYPE'] == 'DROPDOWN') $v['VALUES'] = (isset($s['VALUES']) ? $s['VALUES'] : array());
			$field[$k] = $v;
		}

		// ������ �������� ������
		$status = array('' => $data['no_change']);
		$ar = CSaleStatus::GetList(array('SORT' => 'ASC'), array('LID' => LANGUAGE_ID), false, false, array('ID', 'NAME'));
		while ($v = $ar->Fetch()) $status[$v['ID']] = '['.$v['ID'].'] '.str_replace(array('<', '>'), array('&lt;', '&gt;'), $v['NAME']);
		$field['cod_status']['VALUES'] = $field['control_status_arrived']['VALUES'] = $field['control_status_completed']['VALUES'] = $field['control_status_completed_cod']['VALUES'] = $status;

		return array(
			'CONFIG_GROUPS' => array('all' => $data['head']),
			'CONFIG' => $field,
		);

	}

	function GetSettings($strSettings) {

		$r = array();
		$ar = explode(';', $strSettings);
		$i = 0;
		foreach (self::$setting_key as $k => $v) {
			$r[$k] = (isset($ar[$i]) ? $ar[$i] : $v);
			$i++;
		}
		return $r;

	}

	function SetSettings($arSettings) {

		$r = array();
		foreach (self::$setting_key as $k => $v) $r[] = (isset($arSettings[$k]) ? $arSettings[$k] : $v);
		$r = implode(';', $r);

		// ���������� �� ����������� �������� �������������� ������ (�������������� ��������� ��� ��������� ������ �� ��������������!!!)
		if (strpos($_SERVER['REQUEST_URI'], 'sale_delivery_handler_edit.php') !== false) {
			$ar = array('all' => $r);
			COption::SetOptionString('edost.delivery', 'module_setting', serialize($ar));
		}

		return $r;

	}

	function Calculate($profile, $arConfig, $arOrder, $STEP) {
//		echo '<br><b>Calculate:</b> <pre style="font-size: 12px">'.print_r($arOrder, true).'</pre>';
//		echo '<br><b>Calculate:</b> <pre style="font-size: 12px">'.print_r($arConfig, true).'</pre>';
//		$_SESSION['EDOST']['run'] .= '<br>=====Calculate: '.$arOrder['LOCATION_TO'];

		if ($STEP >= 3) {
			$error = GetMessage('EDOST_DELIVERY_ERROR');
			return array('RESULT' => 'ERROR', 'TEXT' => $error['connect']);
		}

		$mode = '';
		$s = $GLOBALS['APPLICATION']->GetCurPage();
		if ($s == '/bitrix/admin/sale_order_view.php') $mode = 'order_view'; // �������� ������
		if ($s == '/bitrix/admin/sale_order_shipment_edit.php') $mode = 'shipment_edit'; // �������������� ��������
		if ($s == '/bitrix/admin/sale_order_ajax.php') $mode = 'order_ajax'; // ��������� ���������� ��������, �������� � �������������� �����������

		// ���������� ������� �� �������� ��������� ������ � ������� (������� �������� ������������, �� ��������� �� �� ��� �� ������!)
		if ($mode == 'order_ajax' && !empty($_REQUEST['action']) && $_REQUEST['action'] == 'getOrderTails') return array('RESULT' => 'OK', 'VALUE' => 0, 'TRANSIT' => '');

		$admin = (($mode == 'shipment_edit' || $mode == 'order_ajax') && !empty($arConfig['admin']['VALUE']) && $arConfig['admin']['VALUE'] == 'Y' ? true : false);
		if ($admin) {
			$arConfig['hide_error']['VALUE'] = 'N';
			$arConfig['show_zero_tariff']['VALUE'] = 'N';
		}

		// ������ ��������
		$data = self::EdostCalculate($arOrder, $arConfig);

		// �������������� ������� ��� �������������� �������� � �������
		if ($admin) {
			$module = self::GetModule($arOrder['SITE_ID']);
//			echo '<br><b>_REQUEST:</b> <pre style="font-size: 12px">'.print_r($_REQUEST, true).'</pre>';

			if (!empty($_REQUEST['shipment_id'])) $id = $_REQUEST['shipment_id'];
			else if (!empty($_REQUEST['formData']['SHIPMENT']['1']['SHIPMENT_ID'])) $id = $_REQUEST['formData']['SHIPMENT']['1']['SHIPMENT_ID'];
			else $id = 0;

			$ar = array();
			if (!empty($data['data'])) foreach ($data['data'] as $k => $v) foreach (CDeliveryEDOST::$automatic as $k2 => $v2) if ($v2['automatic'] == 'edost' && $v2['profile'] == $k) {
				$ar[$k2] = array('ID' => $k2, 'NAME' => $v2['name'], 'DESCRIPTION' => '');
				break;
			}

			// �������� ��������� ����� �� ������ ��� ������ ��������
			if (!empty($_GET['order_id'])) {
				$props = edost_class::GetProps($_GET['order_id'], array('no_payment'));
				if (!empty($props['office'])) {
					$s = $props['office'];
					$_SESSION['EDOST']['admin_order_edit_office'][$id] = array('id' => 'edost:'.$s['profile'], 'profile' => $s['profile'], 'office_id' => $s['id']);
	            }
			}

			if (!isset($_SESSION['EDOST']['admin_order_edit_office'][$id]) && !isset($_SESSION['EDOST']['admin_order_edit_office'][0])) $active = false;
			else {
				$i = (isset($_SESSION['EDOST']['admin_order_edit_office'][$id]) ? $id : 0);
				$active = $_SESSION['EDOST']['admin_order_edit_office'][$i];
			}

			$format = edost_class::FormatTariff($ar, self::GetRUB(), $arOrder, $active, array('hide_error' => 'N', 'show_zero_tariff' => 'N', 'template' => 'Y', 'map' => 'Y', 'template_block' => 'all', 'template_block_type' => 'none', 'template_cod' => 'off', 'template_map_inside' => 'N', 'NAME_NO_CHANGE' => true, 'ADD_ZERO_TARIFF' => true));
//			echo '<br><b>data:</b> <pre style="font-size: 12px">'.print_r($format, true).'</pre>';
//			echo '<br><b>data:</b> <pre style="font-size: 12px">'.print_r(self::$result['error'], true).'</pre>';

			$tracking = edost_class::GetTracking($arOrder['SITE_ID']);

			$ar = array();
			if (!empty($format['data'])) foreach ($format['data'] as $f_key => $f) if (!empty($f['tariff'])) {
				if ($f['head'] != '') $ar[] = array('head' => $f['head']);
				foreach ($f['tariff'] as $k => $v) if (isset($v['id'])) {
					$v['id'] = edost_class::GetBitrixID($v);
					$v['title'] = edost_class::GetTitle($v, true);
					if (isset($v['head'])) unset($v['head']);
					if (isset($v['office_mode']) && empty($v['office_map'])) unset($v['office_mode']);
					if (!empty($tracking['data'][$v['company_id']])) {
						$v['tracking_example'] = $tracking['data'][$v['company_id']]['example'];
						$v['tracking_format'] = $tracking['data'][$v['company_id']]['format'];
					}
					$ar[] = $v;
				}
			}

			$json = '{"ico_path": "/bitrix/images/delivery_edost_img", '.
				'"format": '.edost_class::GetJson($ar, array('head', 'id', 'profile', 'title', 'price', 'pricetotal', 'pricetotal_formatted', 'pricecash', 'pricecash_formatted', 'priceinfo_formatted', 'transfer_formatted', 'checked', 'office_id', 'office_mode', 'office_address_full', 'error', 'tracking_example', 'tracking_format'), true, false).
				', "module_id": '.$module['ID'].
				(isset($format['map_json']) ? ', '.$format['map_json'] : '').
				(!empty($format['warning']) ? ', "warning": "'.$format['warning'].'"' : '').'}';

			$_SESSION['EDOST']['admin_order_edit'][$id] = $json;
		}

		// ����� ����������
		if (isset($data['data'][$profile])) {
			$v = $data['data'][$profile];
			if ($v['id'] <= DELIVERY_EDOST_TARIFF_COUNT) return array('RESULT' => 'OK', 'VALUE' => $v['price'], 'TRANSIT' => $v['day']);
		}

		return array('RESULT' => 'OK', 'VALUE' => 0, 'TRANSIT' => '');

	}

	function Compability($arOrder, $arConfig) {
//		echo '<br><b>Compability:</b> <pre style="font-size: 12px">'.print_r($arOrder, true).'</pre>';
//		echo '<br><b>Compability:</b> <pre style="font-size: 12px">'.print_r($arConfig, true).'</pre>';
//		$_SESSION['EDOST']['run'] .= '<br>=============Compability: '.$arOrder['LOCATION_TO'];

		$r = array();
		$data = self::EdostCalculate($arOrder, $arConfig);
		if (!empty($data['data'])) foreach ($data['data'] as $k => $v) $r[] = $k;

		// ������� ����� "��������� �������� ����� ������������� �����"
		if (count($r) == 0 && empty($data['hide']) && ($arConfig['hide_error']['VALUE'] != 'Y' || $arConfig['show_zero_tariff']['VALUE'] == 'Y')) $r = array(0);

		// �������������� ������� ��� �������������� ������� ������ � �������
		if (!empty($arConfig['admin']['VALUE']) && $arConfig['admin']['VALUE'] == 'Y' && $GLOBALS['APPLICATION']->GetCurPage() == '/bitrix/admin/sale_order_new.php') {
			if (!empty($_REQUEST['ID'])) $id = $_REQUEST['ID'];
			else if (!empty($_REQUEST['id'])) $id = $_REQUEST['id'];
			else $id = 0;

			$ar = array();
			if (!empty($data['data'])) foreach ($data['data'] as $k => $v) $ar[$k] = array('SID' => $k, 'TITLE' => $v['company'].(!empty($v['name']) ? ' ('.$v['name'].')' : ''), 'DESCRIPTION' => '');
			$ar = array('edost' => array('SID' => 'edost', 'SORT' => '100', 'TITLE' => '', 'DESCRIPTION' => '', 'PROFILES' => $ar));
			if (!isset($_SESSION['EDOST']['admin_order_edit_office'][$id]) && !isset($_SESSION['EDOST']['admin_order_edit_office'][0])) $active = false;
			else {
				$i = (isset($_SESSION['EDOST']['admin_order_edit_office'][$id]) ? $id : 0);
				$active = $_SESSION['EDOST']['admin_order_edit_office'][$i];
				unset($_SESSION['EDOST']['admin_order_edit_office'][$i]);
			}
			$format = edost_class::FormatTariff($ar, self::GetRUB(), $arOrder, $active, array('template' => 'Y', 'map' => 'Y', 'template_block_type' => 'none', 'template_cod' => 'off', 'template_map_inside' => 'N', 'NAME_NO_CHANGE' => true));

			$ar = array();
			if (!empty($format['data'])) foreach ($format['data'] as $f_key => $f) if (!empty($f['tariff'])) {
				if ($f['head'] != '') $ar[] = array('head' => $f['head']);
				foreach ($f['tariff'] as $k => $v) if (isset($v['id'])) {
					$v['id'] = edost_class::GetBitrixID($v);
					$v['title'] = edost_class::GetTitle($v, true);
					if (isset($v['head'])) unset($v['head']);
					if (isset($v['office_mode']) && empty($v['office_map'])) unset($v['office_mode']);
					$ar[] = $v;
				}
			}
			$json = '{"ico_path": "/bitrix/images/delivery_edost_img", '.
				'"format": '.edost_class::GetJson($ar, array('head', 'id', 'title', 'pricetotal', 'pricetotal_formatted', 'pricecash', 'pricecash_formatted', 'priceinfo_formatted', 'transfer_formatted', 'checked', 'office_id', 'office_mode', 'office_address_full', 'error'), true, false).
				(isset($format['map_json']) ? ', '.$format['map_json'] : '').
				(!empty($format['warning']) ? ', "warning": "'.$format['warning'].'"' : '').'}';

			$_SESSION['EDOST']['admin_order_edit'][$id] = $json;
		}

		return $r;

	}


	// �������� ������� � ������ �������� � ������
	public static function GetModule($site_id = false) {

		// �������� ������� � ������
		$service_site = array();
		$ar = \Bitrix\Sale\Internals\ServiceRestrictionTable::getList(array('filter' => array('=CLASS_NAME' => '\Bitrix\Sale\Delivery\Restrictions\BySite')));
		while ($v = $ar->fetch()) {
			$service_site[$v['SERVICE_ID']] = $v;
			if (!empty($v['PARAMS']['SITE_ID'][0])) $service_site[$v['SERVICE_ID']]['site'] = $v['PARAMS']['SITE_ID'][0];
			else if (!empty($v['PARAMS']['SITE_ID'])) $service_site[$v['SERVICE_ID']]['site'] = $v['PARAMS']['SITE_ID'];
		}
//		echo '<br><b>service_site:</b> <pre style="font-size: 12px">'.print_r($service_site, true).'</pre>';

		// ������ ��������
		$module_setting = array();
		$module_setting_id = false;
		$ar = \Bitrix\Sale\Delivery\Services\Table::GetList(array('filter' => array('=CODE' => 'edost')));
		while ($v = $ar->fetch()) {
			$v['INSTALLED'] = 'Y';
			$v['LID'] = (!empty($service_site[$v['ID']]['site']) ? $service_site[$v['ID']]['site'] : 'all');
			if ($module_setting_id === false || ($module_setting[$module_setting_id]['ACTIVE'] != 'Y' && $v['ACTIVE'] == 'Y')) $module_setting_id = $v['LID'];
			$module_setting[$v['LID']] = $v;
		}
//		echo '<br><b>module_setting:</b> <pre style="font-size: 12px">'.print_r($module_setting, true).'</pre>';

		if ($site_id !== false) {
			if (!empty($module_setting[$site_id])) $r = $module_setting[$site_id];
			else if (!empty($module_setting['all'])) $r = $module_setting['all'];
			else $r = false;

			CDeliveryEDOST::GetAutomatic();
			if (!empty($r['ID'])) foreach (CDeliveryEDOST::$automatic as $k => $v) if ($v['parent_id'] != $r['ID']) unset(CDeliveryEDOST::$automatic[$k]);

			return $r;
		}

		return array(
			'service_site' => $service_site,
			'module_setting' => $module_setting,
			'module_setting_id' => $module_setting_id,
		);

	}


	// ��������� ���� �������������� ��������� eDost �� �������� ������ (��� ���� ������ eDost � �������� �������)
	public static function GetEdostLocationID($country, $region = '', $convert_charset = true) {

		if ($country === '') return false;
		if ($convert_charset) {
			if ($region === '') $country = $GLOBALS['APPLICATION']->ConvertCharset($country, LANG_CHARSET, 'windows-1251');
			else {
				$region = $GLOBALS['APPLICATION']->ConvertCharset($region, LANG_CHARSET, 'windows-1251');
				$region = str_replace('�', '�', $region);
			}
		}
		if ($region === '') {
			$i = array_search($country, self::$country_bitrix);
			if ($i !== false) $country = self::$country_edost[$i];
			return array_search($country, self::$country_code);
		}
		if (!empty(self::$region_code2[$country])) {
			if (isset(self::$region_bitrix[$country])) {
				$i = array_search($region, self::$region_bitrix[$country]);
				if ($i !== false) $region = self::$region_edost[$country][$i];
			}
			return array_search($region, self::$region_code2[$country]);
		}
		return false;

	}

	// ��������� �������������� ��������� eDost �� id �������������� ��������
	public static function GetEdostLocation($id) {

		if (empty($id)) return false;

		if (substr($id, 0, 1) === '0') $id = CSaleLocation::getLocationIDbyCODE($id);

		if (!empty($_SESSION['EDOST']['location']['id'])) {
			$v = $_SESSION['EDOST']['location'];
			if ($id == $v['id']) return $v;
		}

		$location = CSaleLocation::GetByID($id, 'ru');
		if (empty($location['ID']) || empty($location['COUNTRY_NAME_LANG'])) return false;

		$r = array('id' => $id);

		$country = self::GetEdostLocationID($location['COUNTRY_NAME_LANG']);
		if ($country === false) return false;

		$r['country'] = $country;
		$r['country_name'] = $GLOBALS['APPLICATION']->ConvertCharset(self::$country_code[$country], 'windows-1251', LANG_CHARSET);
		$r['bitrix']['country'] = $location['COUNTRY_ID'];

		if (!empty(self::$region_code2[$country])) {
			$r['city'] = (isset($location['CITY_NAME_LANG']) ? $location['CITY_NAME_LANG'] : '');
			$r['bitrix']['city'] = $r['city'];
			$r['city'] = $GLOBALS['APPLICATION']->ConvertCharset($r['city'], LANG_CHARSET, 'windows-1251');
			$r['city'] = str_replace('�', '�', $r['city']);

			$region = (isset($location['REGION_NAME_LANG']) ? $location['REGION_NAME_LANG'] : '');
			if ($region != '') $region = self::GetEdostLocationID($country, $region);
			else if ($r['city'] == '') $region = false;
			else {
				$region = self::GetEdostLocationID($country, $r['city'], false); // ������ ������������ �������� (��� ��������)
				if ($region === false) {
					// ����������� ������� �� ������ (��������� ������� ������� bitrix ��� ��������)
					$s = explode('(', $r['city']);
					$p = strpos($r['city'], '���������� ���� (������)');
					if ($p > 0) {
						$r['city'] = trim($s[0]);
						$s = '���������� ���� (������)';
					}
					else if (isset($s[1]) && $p === false) {
						$r['city'] = trim($s[0]);
						$s = explode(')', $s[1]);
						$s = trim($s[0]);
						if ($s == '�������� ���������� �����') $s = '�������� ��';
					}
					else {
						$ar = array(31 => array('��������', '����������', '�������', '������', '������ �����', '��������', '��������', '���������', '����� �����', '��������'), 32 => array('������', '��������', '������', '����������', '������', '�������', '�������', '�������', '�����', '�����', '��������', '���������', '�����'), 33 => array('��������', '�����������', '�������', '����-�����������', '������', '����������', '�����', '�������', '�������', '����������', '��������', '���������', '���������', '������', '�������', '�������', '������', '�����-��������'), 36 => array('�������', '����������', '������������', '�����', '�����������', '����������', '��������', '�������', '����', '������', '������������', '������������', '�����', '����� ������', '��������'), 37 => array('�������', '������', '�������', '�������', '��������', '���', '�����', '���������', '�������', '���'), 40 => array('������', '��������', '�������', '����������', '��������', '���������', '��������', '�������������', '��������'), 44 => array('��������', '���', '������������', '�����', '���������', '�������', '�����'), 46 => array('�����', '��������', '�����', '�����', '������', '������'), 48 => array('������', '����', '�����', '������', '��������', '������'), 50 => array('��������', '�����������', '������������', '�����', '���������������', '���������', '����������', '��������', '�������', '�������', '��������������', '�����', '���������', '�������-�����', '��������', '��������', '������', '������', '������', '��������', '�������', '��������', '������������', '���������', '��������', '���������-�������������', '�����������', '�����������', '������������', '�����������', '�������', '����������', '����������', '���������', '�������', '�����', '�������', '������', '����', '�����������', '��������', '������', '����������', '��������', '�������', '����������', '���������', '��������', '�������', '������', '����-�������', '���������', '���������', '�������', '������������', '�����������', '������ �������', '��������', '��������', '�������', '�����', '���������', '������-������', '���������� �����', '������������', '�������', '�������', '���������', '�������', '������� �����', '��������', '��������', '�������������', '�������', '�����', '������', '�����', '������', '�������', '������-����������', '������', '����������', '�������', '�������', '������'), 57 => array('����', '�����', '������'), 62 => array('������', '�������', '������', '������', '���������', '�������������', '������', '�����', '������'), 67 => array('��������', '����������', '������', '�������', '�����������������', '��������', '��������', '������'), 68 => array('������', '��������', '�������', '���������', '��������', '����������', '�������', '��������'), 69 => array('�����', '������', '�������', '������ �������', '�����', '�����', '��������', '��������', '��������', '����', '������', '������', '�������', '�������'), 71 => array('����', '����� ����', '�������', '����������', '�������', '������-�������', '�������', '�������', '������������', '�������', '������', '�����', '�����', '��������', '������', '�������', '���������'), 76 => array('���������', '����������-���������', '������', '�������', '������', '�����', '��������-��', '�������'), 10 => array('������������', '����', '���������', '����������', '������', '���������', '�������������'), 11 => array('���������', '�������', '��������', '������', '����', '������', '����������', '������', '����', '����'), 29 => array('�����������', '�������', '������', '����������', '�����', '������������', '������', '�������'), 83 => array('������-���'), 35 => array('�������', '������� �����', '�����', '���������', '��������', '������'), 39 => array('�����������', '��������', '�������', '���������', '�����', '����������'), 47 => array('������������', '������', '����������', '������', '�������', '���������', '������', '�������� ����', '����', '��������', '����������', '���������', '���������', '������', '�������� ���', '������', '�����', '����������', '��������', '��������', '����������', '�������', '������������', '������� ����', '����������', '��������', '������', '������', '�����-���������'), 51 => array('��������', '�������', '����������', '����������', '����������', '�������� ����', '��������', '�����������', '������', '�������', '����������', '������'), 53 => array('������� ��������', '��������', '������ �����', '������', '����� ������', '��������', '�������', '������'), 60 => array('�����', '������� ����', '������', '������'), 1 => array('������', '����������', '����', '�����������'), 5 => array('���������', '��������', '������������ ����', '�������', '��������', '��������', '��������', '������', '��������'), 6 => array('��������', '�������', '���������', '���������', '�������', '�������', '�����������������', '������������', '��������', '�����'), 7 => array('�������', '����������', '������', '����������', '�������', '�����', '��������', '�����', '��������'), 8 => array('������', '������'), 9 => array('��������', '����������', '������������', '�������', '����-�������'), 15 => array('�����������', '������', '�����', '������', '������'), 20 => array('�������', '�����', '��������', '����-������', '����', '�����-������', '��������', '����-���', '�����-���', '������'), 23 => array('���������', '��������', '����������', '��������������', '�����', '�������', '�����������', '���������', '������� ����', '����', '���������', '������', '�������', '������������', '��������-��-������', '����', '��������', '������', '������', '���������', '��������', '���������', '���������', '����� �����', '�����������', '�������', '����������', '�������', '���������������', '���������', '���������', '����������', '����������', '�������������', '����������', '���������', '�������������', '����������', '�����������', '��������������', '��������', '����������', '���������-�������', '��������', '�������', '���������', '������������', '����������', '������', '���������', '������������', '����-�������', '���������', '�����������������'), 26 => array('����������', '����������', '����������', '���������', '������������', '����������', '����������', '���������', '����������� ����', '������������', '���������', '�������������', '�������', '���������������', '������', '������', '�����������', '���������', '����������', '�������', '������������', '������������', '�����������������', '����������', '�����������������', '����������', '������������', '�����������', '�����������', '�������', '����������'), 30 => array('���������', '���������', '��������', '�������', '��������'), 34 => array('���������', '�����������', '��������', '�������', '����������', '��������', '�������', '��������', '�������', '�����', '��������', '�����-��-����', '������ ���', '������������', '������', '�������', '����������', '�������������', '����������', '��������������', '����������'), 61 => array('������-��-����', '����', '�������', '����� �������', '����������', '������', '������', '�������', '�������-����������', '������� �����', '���������', '������������', '������������', '������', '��������', '�����', '���������', '�����', '����������', '�����������', '���������', '���������', '��������������', '������� ������', '���������', '�������', '�������������', '���������', '����������', '�������������', '��������'), 2 => array('���', '�������', '������', '�������', '��������', '��������', '�����', '�����������', '�������', '�������', '��������', '��������', '������', '����������', '�����������', '�������', '�����', '�����������', '�������', '�����', '�����', '��������', '�����'), 12 => array('������-���', '������', '��������������', '���������'), 13 => array('�������', '���������', '��������', '�������������'), 16 => array('������', '���������', '�����������', '�����', '��������', '������', '�������', '������', '������������', '�����������', '���������� �����', '����������', '������', '���������', '�����', '����', '���������', '������', '�����������', '����������', '������� ������', '�������'), 18 => array('������', '��������', '������', '�����', '�������', '��������', '����', '���'), 21 => array('���������', '�������', '�����', '��������������', '�������'), 43 => array('������� ������', '������-������', '���������', '����������', '���������', '������'), 52 => array('������ ��������', '�������', '�������', '���������', '���', '�����', '�������', '���������', '������', '��������', '�������', '�����', '��������', '�������', '��������', '����������', '�������', '������', '�������'), 56 => array('��������', '��������', '����������', '�������', '���', '��������', '����������', '����������', '����', '����-�����', '���������', '�����', '�������', '��������', '������ ������'), 58 => array('�����', '�������', '�������', '��������', '������ �����', '��������'), 59 => array('�����', '�������������', '���������', '������', '��������', '�����', '�����������', '������', '������', '���������', '����������', '�������', '����������', '�������������', '�����', '���', '����', '��������', '��������'), 63 => array('������', '���������', '������', '��������������', '���������', '��������', '�����������', '�������', '��������', '��������', '��������', '������-��������', '����������', '�������'), 64 => array('�������', '�������', '��������', '�������', '������', '�����', '��������', '�������', '�������', '�������', '�����������', '�������', '�����', '���������', '������� ���', '����������', '�������'), 73 => array('���������', '�����', '������������', '����', '�������������'), 45 => array('������', '��������', '���������', '�������', '��������', '������'), 66 => array('������������', '��������', '���������', '�����������', '������', '����������', '����������', '������� �����', '�������������', '������� �����', '������', '�����', '�������-���������', '��������', '��������', '��������', '���������', '��������������', '�������������', '������������', '�����', '������', '��������', '������ �����', '������ �����', '������ ����', '�����������', '������������', '���������', '�����', '��������', '���', '�������������', '�����', '����� ���', '�����', '����� ����', '�������', '�������', '������', '�������'), 72 => array('������', '������������', '����', '��������', '����������', '���������'), 86 => array('�����-��������', '����������', '�������', '��������', '������', '�����������', '�������������', '������', '������', '����-��', '������', '����', '������', '����������', '���������', '���������', '������', '����� ��', '�����������'), 89 => array('��������', '����������', '����������', '����������', '�����', '����� �������', '��������', '�����-����'), 74 => array('���������', '������������������', '���', '������� ������', '�����������', '��������', '�������', '�������', '�����', '�����-��������', '�������', '�������', '����', '������', '������������', '�����', '�����', '�����', '����-�����', '���������', '�����������', '����������', '������', '��������', '���', '�������', '����', '�����'), 4 => array('�����-�������', '�����'), 3 => array('����-���', '���������������', '����������', '�����', '������������'), 17 => array('�����'), 19 => array('������', '����������', '����������', '�����', '����-������'), 22 => array('�������', '��������������', '�����', '������', '����������', '�����', '�������', '������-��-���', '�����������', '��������', '���������', '������', '�������', '���������'), 24 => array('����������', '������', '�������', '��������', '����������', '��������', '�����������', '�����', '�����������', '���������', '��������', '��������', '��������', '������', '������������', '��������', '���������', '��������', '�������', '��������', '����', '���������', '�������'), 38 => array('�������', '�������', '�������', '������', '����', '�����������', '������', '������', '�����', '������-���������', '����-������', '����-���', '���������', '������', '�������', '���������', '������������-��������', '��������', '���������', '�������', '����-���������'), 42 => array('��������', '��������', '������-��������', '������', '���������', '����������', '����� �������', '��������', '������', '���������', '�������-���������', '���������', '��������', '������������', '�����', '�����������', '��������', '�����������', '�����', '��������', '�����', '����', '������������', '���������', '������'), 54 => array('�����������', '���������', '������', '�������', '��������', '���', '�������', '��������', '������', '�������', '��������', '������', '����������', '�����', '�������', '����������'), 55 => array('����', '���������', '���������', '����'), 70 => array('�����', '�����', '���������', '�������', '���������'), 75 => array('����', '�����', '�����', '�������������', '��������-�������������', '�������� ����', '��������', '�����'), 14 => array('������', '��������', '�����', '�����', '�������', '�����'), 25 => array('�����������', '��������', '��������', '�����', '������� ������', '�����������', '�������������', '�����������', '�������', '��������', '����������', '������-�������', '���������', '����������', '���������', '��������', '����������'), 27 => array('���������', '������', '�����', '�����������-��-�����', '����������-��-�����', '��������� ������', '������', '��������', '���������', '���������'), 28 => array('���������', '���', '����������', '���������', '�����', '���������', '���������'), 41 => array('�������������-����������', '���������', '�������'), 49 => array('�������'), 65 => array('����-���������', '��������', '��������', '���', '���������', '������'), 79 => array('����������'), 87 => array('�������'), 91 => array('������', '�������', '����������', '�������', '���������', '�����', '���������������', '����', '�����������', '�����������', '�����', '��������', '����'));
						foreach ($ar as $k => $v) if (in_array($r['city'], $v)) { $region = $k; break; }
						if ($region === false) {
							$s = $r['city'];
							$r['city'] = '';
						}
					}
					if ($region === false) $region = self::GetEdostLocationID($country, $s, false);
				}
            }

			if ($region === false) return false;

			$r['region'] = $region;
			$r['region_name'] = $GLOBALS['APPLICATION']->ConvertCharset(self::$region_code2[$country][$region], 'windows-1251', LANG_CHARSET);
			$r['bitrix']['region'] = $location['REGION_ID'];
		}
		else {
			$r['region'] = '';
			$r['region_name'] = (isset($location['REGION_NAME_LANG']) ? $location['REGION_NAME_LANG'] : '');
			$r['city'] = '';
			$r['bitrix']['city'] = (isset($location['CITY_NAME_LANG']) ? $location['CITY_NAME_LANG'] : '');
		}

		$_SESSION['EDOST']['location'] = $r;

		return $r;

	}


	// �������� �������� ������ edost �� 'option' ��� ������ $data
	public static function GetEdostConfig($site_id, $data = false) {

		$r = array();
		$first = false;

		if ($data !== false) $r['all'] = $data;
		else {
			$s = COption::GetOptionString('edost.delivery', 'module_setting', '');
			if ($s != '') $r = unserialize($s);
//			$r = COption::GetOptionString('edost.delivery', 'module_setting', '');
//			$r = ($r != '' ? unserialize($r) : array('all' => '')); // ����
		}

		foreach ($r as $k => $v) {
			$v = explode(';', $v);

			$i = 0;
            $ar = array();
			foreach (self::$setting_key as $k2 => $v2) {
				$ar[$k2] = (isset($v[$i]) ? $v[$i] : $v2);
				$i++;
			}
			$r[$k] = $ar;

			if ($first === false) $first = $ar;
		}

		if ($site_id !== 'all')
			if (isset($r['all'])) $r = $r['all'];
			else if (isset($r[$site_id])) $r = $r[$site_id];
			else $r = $first;

		return $r;

	}


	// ��������� ������ �� ���� ��������
	public static function GetEdostTariff($profile, $office_type = 0) {

		$data = self::$result;
		if (isset($data['data'][$profile])) {
			$v = $data['data'][$profile];

			if ($v['id'] <= DELIVERY_EDOST_TARIFF_COUNT) {
				if ($office_type > 0 && !empty($v['priceoffice'])) {
					$priceoffice = edost_class::GetOfficePrice($v['priceoffice'], $office_type);
					if ($priceoffice !== false) {
						$v = array_replace($v, $priceoffice);
						$v['priceoffice_active'] = true;
					}
				}
				return $v;
			}
		}

		// ����� �� ������ - ����� ������
		return array(
			'error' => self::GetEdostError(isset($data['error']) ? $data['error'] : 0),
			'price' => 0
		);

	}


	// ��������� ���� ������ ���� (��� ������ edost.locations)
	public static function GetPropRequired($id, $prop) {

		$converted = (\Bitrix\Main\Config\Option::get('main', '~sale_converted_15', 'N') == 'Y' ? true : false); // �������� �� ������� 16

		$tariff = false;
		if ($converted) {
			CDeliveryEDOST::GetAutomatic();
			if (isset(CDeliveryEDOST::$automatic[$id])) $tariff = CDeliveryEDOST::$automatic[$id];
		}
		else {
			$s = explode(':', $id);
			if (isset($s[1])) $tariff = array('automatic' => $s[0], 'profile' => $s[1]);
		}

		if ($tariff === false || $tariff['automatic'] !== 'edost') return '';

		$profile = $tariff['profile'];
		$tariff = ceil(intval($profile) / 2);

		if ($prop == 'zip') return (in_array($tariff, array(1, 2, 3, 24, 61, 62, 68)) ? 'Y' : '');
		if ($prop == 'metro') return (in_array($tariff, array(31, 32, 33, 34)) ? 'S' : '');

		return '';

	}


	// ��������� ������ ������������ �� ����
	public static function GetEdostError($id, $type = 'delivery') {

		$error = GetMessage('EDOST_DELIVERY_ERROR');
		$r = $error['head'].($type == 'office' ? $error['office'] : '');
		$r .= (isset($error[$id]) ? $error[$id] : $error['no_delivery']).'!';
		return $r;

	}

	// ��������� �������������� ������������
	public static function GetEdostWarning($id = false) {

		$r = '';
		if ($id === false) $data = self::$result;
		if ($id !== false || !empty($data['warning'])) {
			$warning = GetMessage('EDOST_DELIVERY_WARNING');
			if ($id !== false) {
				if (!empty($warning[$id])) $r .= $warning[$id];
			}
			else {
				foreach ($data['warning'] as $v) if (!empty($warning[$v])) $r .= $warning[$v].'<br>';
				if ($r != '') $r = $warning[0].'<br>'.$r;
			}
		}
		return $r;

	}

	// ������ ��������
	public static function EdostCalculate($order, $bitrix_config) {
//		echo '<br><b>order:</b> <pre style="font-size: 12px">'.print_r($order, true).'</pre>';

		$o = $order;
		$ar = array('ITEMS', 'MAX_DIMENSIONS');
		foreach ($ar as $v) if (isset($o[$v])) unset($o[$v]);
		$order['original'] = $o;

		$a = false;
		if (self::$result != null && !empty(self::$result['order']['original']) && (!isset($order['NO_LOCAL_CACHE']) || $order['NO_LOCAL_CACHE'] != 'Y')) {
			$a = true;
			foreach (self::$result['order']['original'] as $k => $v) if (isset($o[$k]) && $o[$k] != $v) { $a = false; break; };
		}
		if ($a) return self::$result;

		$config = array();
		foreach ($bitrix_config as $k => $v) $config[$k] = $v['VALUE'];

		if (class_exists('edost_function') && method_exists('edost_function', 'BeforeCalculate')) {
			$v = edost_function::BeforeCalculate($order, $config);
			if ($v !== false && is_array($v)) return self::SetResult($v, $order, $config);
		}

		$weight_zero = false;
		$total_weight = 0;
		$total_price = 0;
		$package = array();

		if (!isset($order['ITEMS']) && isset($order['BASKET_ITEMS'])) $order['ITEMS'] = $order['BASKET_ITEMS']; // ��������� ������ ���������� (�� ������� 14)

		$cart = (!isset($order['CART']) ? 'Y' : $order['CART']);
		if (isset($order['NO_CART']) && $order['NO_CART'] == 'Y') $cart = (isset($order['ADD_CART']) && $order['ADD_CART'] == 'Y' ? 'DOUBLE' : 'N'); // ��������� ������ ���������� (�� ������ 1.2.0)

		$currency = CSaleLang::GetLangCurrency(isset($order['SITE_ID']) ? $order['SITE_ID'] : SITE_ID);
		$base_currency = self::GetRUB();

		$prop = array();
		$prop_size = array();
		$prop_get = array('ID', 'NAME');
		$ar = array('WEIGHT', 'VOLUME', 'LENGTH', 'WIDTH', 'HEIGHT');
		foreach ($ar as $v) if (defined('DELIVERY_EDOST_'.$v.'_PROPERTY_NAME')) {
			$s = constant('DELIVERY_EDOST_'.$v.'_PROPERTY_NAME');
			$prop[$v] = 'PROPERTY_'.$s.'_VALUE';
			if (in_array($v, array('LENGTH', 'WIDTH', 'HEIGHT'))) $prop_size[] = $prop[$v];
			$prop_get[] = 'PROPERTY_'.$s;
		}
		if (count($prop_size) <= 1) unset($prop_size);

		$prop['MEASURE'] = (defined('DELIVERY_EDOST_WEIGHT_PROPERTY_MEASURE') ? DELIVERY_EDOST_WEIGHT_PROPERTY_MEASURE : 'G');
		$prop['RATIO'] = (defined('DELIVERY_EDOST_VOLUME_PROPERTY_RATIO') ? DELIVERY_EDOST_VOLUME_PROPERTY_RATIO : 1);

		$weight_default = (defined('DELIVERY_EDOST_WEIGHT_DEFAULT') ? DELIVERY_EDOST_WEIGHT_DEFAULT : 0);
		$weight_from_main_product = (defined('DELIVERY_EDOST_WEIGHT_FROM_MAIN_PRODUCT') && DELIVERY_EDOST_WEIGHT_FROM_MAIN_PRODUCT == 'Y' ? true : false);
		$property_from_main_product = (defined('DELIVERY_EDOST_PROPERTY_FROM_MAIN_PRODUCT') && DELIVERY_EDOST_PROPERTY_FROM_MAIN_PRODUCT == 'Y' ? true : false);
		$write_log = (defined('DELIVERY_EDOST_WRITE_LOG') && DELIVERY_EDOST_WRITE_LOG == 1 ? true : false);

		// ��������� ������ �� ������� � $order['ITEMS'] ��� � ������� ��� �� ���� ������
		if ($cart != 'N' && CModule::IncludeModule('iblock')) {
			$items = array();
			if (isset($order['ITEMS'])) {
				// ������ �� ������
				if (is_array($order['ITEMS']) && count($order['ITEMS']) > 0) foreach ($order['ITEMS'] as $v)
					if ((!isset($v['CAN_BUY']) || $v['CAN_BUY'] == 'Y') && (!isset($v['DELAY']) || $v['DELAY'] == 'N') && !empty($v['QUANTITY'])) $items[] = $v;
			}
			else {
				// ������ �� ������� ��� ������
				if (!empty($order['ORDER_ID']) && !empty($order['SITE_ID'])) $filter = array('ORDER_ID' => $order['ORDER_ID'], 'LID' => $order['SITE_ID']);
				else $filter = array('FUSER_ID' => CSaleBasket::GetBasketUserID(), 'LID' => SITE_ID, 'ORDER_ID' => 'NULL');

				$ar = CSaleBasket::GetList(array('NAME' => 'ASC', 'ID' => 'ASC'), $filter, false, false, array('ID', 'CALLBACK_FUNC', 'MODULE', 'PRODUCT_ID', 'QUANTITY', 'DELAY', 'CAN_BUY', 'PRICE', 'WEIGHT'));
				while ($v = $ar->Fetch()) if ($v['CAN_BUY'] == 'Y' && $v['DELAY'] == 'N' && !empty($v['QUANTITY'])) $items[] = $v;
			}
			foreach ($items as $item) {
//				echo '<br><b>edost module - item:</b> <pre style="font-size: 12px">'.print_r($item, true).'</pre>';

				if (empty($item['TYPE']) && !empty($item['SET_PARENT_ID'])) continue; // ������ �� ���������

				$weight = (isset($item['WEIGHT']) && $item['WEIGHT'] > 0 ? $item['WEIGHT'] : 0);
				$s = (isset($item['DIMENSIONS']) ? $item['DIMENSIONS'] : '');
				$s = array((isset($s['LENGTH']) ? $s['LENGTH'] : 0), (isset($s['WIDTH']) ? $s['WIDTH'] : 0), (isset($s['HEIGHT']) ? $s['HEIGHT'] : 0));

				// ������������ ����� ��������� IBXSaleProductProvider !!!!!
				if (isset($item['MODULE']) && isset($item['CALLBACK_FUNC']) && strlen($item['CALLBACK_FUNC']) > 0) {
					CSaleBasket::UpdatePrice($item['ID'], $item['CALLBACK_FUNC'], $item['MODULE'], $item['PRODUCT_ID'], $item['QUANTITY']);
					$item = CSaleBasket::GetByID($item['ID']);
				}

				// ��������� ������ �� �������� ������ �� id ��������� ����������� (���������� � ����������)
				if (isset($item['PRODUCT_ID']) && ($weight_from_main_product || $property_from_main_product)) {
					$main_product = CCatalogSku::GetProductInfo($item['PRODUCT_ID']);
                    if (isset($main_product['ID']) && $main_product['ID'] > 0) {
	                    if ($weight_from_main_product && $weight == 0) {
							$v = CCatalogProduct::GetByID($main_product['ID']);
							if (isset($v['WEIGHT']) && $v['WEIGHT'] > 0) $weight = $v['WEIGHT'];
						}

						if ($property_from_main_product) $item['PRODUCT_ID'] = $main_product['ID'];
					}
				}

				// �������� ������� ������, ���� �� ����� ��� ��� �������� (���������� � ����������)
				$get_weight = (isset($prop['WEIGHT']) && $weight == 0 ? true : false);
				$get_size = ((!empty($prop_size) || isset($prop['VOLUME'])) && ($s[0] == 0 || $s[1] == 0 || $s[2] == 0) ? true : false);
				if ($get_weight || $get_size) {
					$ar = CIBlockElement::GetById($item['PRODUCT_ID']);
					$v = $ar->Fetch();
					$ar = CIBlockElement::GetList(array(), array('ID' => $item['PRODUCT_ID'], 'IBLOCK_ID' => $v['IBLOCK_ID']), false, array('nPageSize' => 5), $prop_get);
					if ($v = $ar->GetNext()) {
						if ($get_weight && isset($v[$prop['WEIGHT']])) {
						    self::CommaToPoint($v[$prop['WEIGHT']]);
						    if ($v[$prop['WEIGHT']] > 0) {
							    $weight = $v[$prop['WEIGHT']];
							    if ($prop['MEASURE'] == 'KG') $weight = $weight*1000;
							}
						}
						if ($get_size) {
							$s = array(0, 0, 0);
							if (!empty($prop_size)) foreach ($prop_size as $k2 => $v2) if (isset($v[$v2])) {
								self::CommaToPoint($v[$v2]);
								if ($v[$v2] > 0) $s[$k2] = $v[$v2];
							}

							// ���� ��������� ���, �� ����� �����, ����� �������� ����������� �� ������
							if (isset($prop['VOLUME']) && isset($v[$prop['VOLUME']]) && $s[0] == 0 && $s[1] == 0 && $s[2] == 0) {
								self::CommaToPoint($v[$prop['VOLUME']]);
								$volume = ($v[$prop['VOLUME']] > 0 ? $v[$prop['VOLUME']] : 0);
								$s[0] = $s[1] = $s[2] = pow($volume, 1/3) * $prop['RATIO'];
							}
						}
					}
				}

				// ���� ������ ������ ��� �������, ����� ���������, ��� ��� ����� (����� � �������)
				if ($s[0] > 0 && $s[1] > 0 && $s[2] == 0) $s[2] = $s[1];
				if ($s[0] > 0 && $s[2] > 0 && $s[1] == 0) $s[1] = $s[2];

				edost_class::PackItem($package, $s, $item['QUANTITY']);

				if ($weight == 0) $weight = $weight_default;
				if ($weight == 0) $weight_zero = true;
				$weight = $weight * $item['QUANTITY'];

				$total_weight += $weight;
				$total_price += CCurrencyRates::ConvertCurrency($item['PRICE'], isset($item['CURRENCY']) ? $item['CURRENCY'] : $currency, $base_currency) * $item['QUANTITY'];
//				echo '<br>weight: <b>'.$weight. '</b>, total_weight: <b>'.$total_weight.'</b> | price: <b>'.$item['PRICE'].'</b>, total_price: <b>'.$total_price.'</b> | quantity: <b>'.$item['QUANTITY'].'</b><pre style="font-size: 12px">'.print_r($s, true).'</pre>';
			}
		}

		if (defined('DELIVERY_EDOST_IGNORE_ZERO_WEIGHT') && DELIVERY_EDOST_IGNORE_ZERO_WEIGHT == 'Y') $weight_zero = false;

		if ($cart == 'Y') {
			if ($weight_zero) $order['WEIGHT'] = 0;
			else if ($total_weight > 0) $order['WEIGHT'] = $total_weight;

			if ($total_price > 0) $order['PRICE'] = $total_price;
		}
		else {
			$s = array(
				isset($order['LENGTH']) && $order['LENGTH'] > 0 ? $order['LENGTH'] : 0,
				isset($order['WIDTH']) && $order['WIDTH'] > 0 ? $order['WIDTH'] : 0,
				isset($order['HEIGHT']) && $order['HEIGHT'] > 0 ? $order['HEIGHT'] : 0
			);
			$quantity = (isset($order['QUANTITY']) && intval($order['QUANTITY']) > 0 ? intval($order['QUANTITY']) : 1);

			$order['WEIGHT'] = $order['WEIGHT'] * $quantity;
			$order['PRICE'] = $order['PRICE'] * $quantity;

			if ($cart != 'DOUBLE') $package = array();
			else {
				if ($weight_zero) $order['WEIGHT'] = 0;
				else {
					$order['WEIGHT'] += $total_weight;
					$order['PRICE'] += $total_price;
				}
			}

			edost_class::PackItem($package, $s, $quantity);
		}

		$order['size'] = edost_class::PackItems($order['WEIGHT'] > 0 ? $package : '');

		$s = '';
		if (!(isset($config['send_zip']) && $config['send_zip'] == 'N') && isset($order['LOCATION_ZIP'])) {
			$s = substr($order['LOCATION_ZIP'], 0, 8);
			if ($s == '0') $s = ''; // ����� ������ �������� (�� �������� �������������� ������ ������������ ������� intval, ������� ������ ��� ���������� ������ ���������� �����)
			if ($s == '.') $s = ''; // ����� ������ ������� - ����� ���������� �������� ������������� ����� �������
			else if (strlen($s) == 7 && strlen(preg_replace("/[^0-9]/i", "", $s)) == 6 && substr($s, -1) == '.') $s = substr($s, 0, 6); // ����� � ����� ������� - ������ ��������� ��������
		}
		$order['LOCATION_ZIP'] = $s;

		if (class_exists('edost_function') && method_exists('edost_function', 'BeforeCalculateRequest')) {
			$v = edost_function::BeforeCalculateRequest($order, $config);
			if ($v !== false && is_array($v)) return self::SetResult($v, $order, $config);
		}

		$weight = round($order['WEIGHT']*0.001, 3);
		if (!($weight > 0)) return self::SetResult(array('error' => 11), $order, $config); // � ������� �� ����� ���

		if (!isset($order['location'])) {
			if (empty($order['LOCATION_TO'])) return self::SetResult(array('error' => 'no_location'), $order, $config); // �� ������� ��������������
			$order['location'] = self::GetEdostLocation($order['LOCATION_TO']);
		}
		if ($order['location'] === false) return self::SetResult(array('error' => 5), $order, $config); // � ��������� �������������� ������ �������� �� ������������
//define("LOG_FILENAME", __DIR__."/log.txt"); 
//AddMessage2Log($config);
//AddMessage2Log($_REQUEST['order']['DELIVERY_ID']);
		// �������� ������� ������� �� ����
		$cache_id = $_REQUEST['order']['DELIVERY_ID'] . '|sale|16.0.0|edost|delivery|'.$config['id'].'|'.$order['LOCATION_FROM'].'|'.$order['LOCATION_TO'].'|'.$order['WEIGHT'].'|'.ceil($order['PRICE']).'|'.implode('|', $order['size']).'|'.$order['LOCATION_ZIP'].'|'.$_SESSION['VREGIONS_REGION']['ID'];
		//AddMessage2Log($cache_id);
		$cache = new CPHPCache();
		if ($cache->InitCache(DELIVERY_EDOST_CACHE_LIFETIME, $cache_id, '/')) {
//			echo '<br>OLD data from cache';
			$r = $cache->GetVars();
			$r['cache'] = true;
			if (defined('DELIVERY_EDOST_FUNCTION_RUN_AFTER_CACHE') && DELIVERY_EDOST_FUNCTION_RUN_AFTER_CACHE == 'Y') if (class_exists('edost_function') && method_exists('edost_function', 'AfterCalculate')) edost_function::AfterCalculate($order, $config, $r);
			return self::SetResult($r, $order, $config);
		}

		// ������ �� ������ �������
		$ar = array();
		$ar[] = 'country='.$order['location']['country'];
		$ar[] = 'region='.$order['location']['region'];
		$ar[] = 'city='.urlencode($order['location']['city']);
		$ar[] = 'weight='.urlencode($weight);
		$ar[] = 'insurance='.urlencode($order['PRICE']);
		$ar[] = 'size='.urlencode(implode('|', $order['size']));
		if ($order['LOCATION_ZIP'] !== '') $ar[] = 'zip='.urlencode($order['LOCATION_ZIP']);
		$r = edost_class::RequestData($config['host'], $config['id'], $config['ps'], implode('&', $ar), 'delivery');
		
		if (class_exists('edost_function') && method_exists('edost_function', 'AfterCalculate')) edost_function::AfterCalculate($order, $config, $r);

		// ���������� ������� � ��� ����
		if ($write_log) {
			$s = '';
			if (isset($r['error'])) $s = self::GetEdostError($r['error']);
			else if (!empty($r['data'])) $s = edost_class::implode2(array("\r\n", ' | ', ' : ', ' , '), $r['data']);
			self::WriteLog($order['location']['country'].', '.$order['location']['region'].', '.$GLOBALS['APPLICATION']->ConvertCharset($order['location']['city'], 'windows-1251', LANG_CHARSET).', '.$order['LOCATION_ZIP'].', '.$weight.' kg, '.$order['PRICE'].' rub, '.implode(' x ', $order['size']).' - '.date("Y.m.d H:i:s")."\r\n\r\n".$s);
		}

		if (!isset($r['error'])) {
			$cache->StartDataCache();
			$cache->EndDataCache($r);
		}

		return self::SetResult($r, $order, $config);

	}

	// ��������� ���������� � ���������� ������
	public static function SetResult($data, $order, $config) {

		$k = (isset($data['sizetocm']) ? $data['sizetocm'] : 0); // ����������� ��������� ��������� �������� � ���������� (�������� ����������� � ������ �������� edost)
		$size = (isset($order['size']) ? $order['size'] : array(0, 0, 0));

		$data['order'] = array(
			'location' => (isset($order['location']) ? $order['location'] : false),
			'zip' => $order['LOCATION_ZIP'],
			'weight' => round($order['WEIGHT']*0.001, 3),
			'price' => $order['PRICE'],
			'size1' => ceil($size[0] * $k),
			'size2' => ceil($size[1] * $k),
			'size3' => ceil($size[2] * $k),
			'sizesum' => ceil(($size[0] + $size[1] + $size[2]) * $k),
			'config' => $config,
			'original' => $order['original'],
		);

		self::$result = $data;

		return $data;

	}


	// ��������� ������ ������������������ �������
	public static function GetAutomatic() {

		if (CDeliveryEDOST::$automatic !== false) return;

		$services = \Bitrix\Sale\Delivery\Services\Table::getList(array('filter' => array('=ACTIVE' => 'Y', '=CLASS_NAME' => '\Bitrix\Sale\Delivery\Services\AutomaticProfile')));
		$r = array();
		while ($v = $services->fetch()) {
			$s = explode(':', $v['CODE']);
			if (!isset($s[1])) continue;
			$r[$v['ID']] = array('id' => $v['ID'], 'parent_id' => $v['PARENT_ID'], 'code' => $v['CODE'], 'automatic' => $s[0], 'profile' => $s[1], 'name' => $v['NAME'], 'description' => $v['DESCRIPTION']);
		}

		CDeliveryEDOST::$automatic = $r;

	}

	// ��������� ������� edost �� ���� ��� ID ��������
	public static function GetEdostProfile($id, $control = false) {

		if (empty($id)) return false;

		$r = false;

		$s = explode(':', $id);
		if ($s[0] === 'edost' && isset($s[1])) $r = $s[1];

		if ($r === false) {
			$converted = (\Bitrix\Main\Config\Option::get('main', '~sale_converted_15', 'N') == 'Y' ? true : false); // �������� �� ������� 16
			if (!$converted) return false;

			self::GetAutomatic();
//			echo '<br>'.$id.'<b>automatic:</b> <pre style="font-size: 12px">'.print_r(self::$automatic[$id], true).'</pre>';
			if (isset(self::$automatic[$id]) && self::$automatic[$id]['automatic'] == 'edost') $r = self::$automatic[$id]['profile'];
		}

		if ($r === false) return false;

		$tariff = ceil(intval($r) / 2);
		if ($control && $r !== false && in_array($tariff, CDeliveryEDOST::$tariff_shop)) return false;

		$r = array(
			'tariff' => $tariff,
			'profile' => $r,
			'title' => self::$automatic[$id]['name'],
		);
		$r['insurance'] = ($r['tariff']*2 - $r['profile'] == 0 ? true : false);
		if ($control) {
			$sign = GetMessage('EDOST_DELIVERY_SIGN');
			$s = edost_class::ParseName(self::$automatic[$id]['name'], '', '', $sign['insurance']);
			$r['company'] = $s['company'];
			$r['name'] = $s['name'];
		}

		return $r;

	}

	public static function GetRUB() {
		$currency = 'RUB';
		if (CCurrency::GetByID('RUR')) $currency = 'RUR';
		if (CCurrency::GetByID('RUB')) $currency = 'RUB';
		return $currency;
	}

	public static function GetProtocol() {
		return (\Bitrix\Main\Application::getInstance()->getContext()->getRequest()->isHttps() ? 'https://' : 'http://');
//		return (!empty($_SERVER['HTTPS']) || !empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' ? 'https://' : 'http://');
	}

	public static function CommaToPoint(&$n) {
		if (!empty($n)) $n = str_replace(',', '.', preg_replace("/[^0-9,.]/i", "", $n));
	}

	public static function WriteLog($data) {
		$fp = fopen(dirname(__FILE__)."/edost.log", "a");
		fwrite($fp, "\r\n==========================================\r\n");
		fwrite($fp, $data);
		fclose($fp);
	}

}


AddEventHandler('sale', 'onSaleDeliveryHandlersBuildList', array('CDeliveryEDOST', 'Init'));


class edost_class {
	public static $error = false;

	public static function RequestError($code, $msg, $file, $line) {
		self::$error = true;
		return true;
	}

	// ������ �� ������ edost
	public static function RequestData($url, $id, $ps, $post, $type) {

		if ($id === '' || $ps === '') return array('error' => 12);
		if (intval($id) == 0) return array('error' => 3);
		if ($post === '') return array('error' => 4);

		$api2 = ($type == 'delivery' || $type == 'control' || $type == 'detail' ? true : false);
		$auto = ($url == '' ? true : false);
		$server_default = ($api2 ? DELIVERY_EDOST_SERVER : DELIVERY_EDOST_SERVER_ZIP);
		$server = ($auto ? COption::GetOptionString('edost.delivery', $api2 ? 'server' : 'server_zip', $server_default) : $url);
		if ($server == '') $server = $server_default;
		$url = 'http://'.$server.'/'.($api2 ? 'api2.php' : 'api.php');

		$post = 'id='.$id.'&p='.$ps.'&'.$post;
		$parse_url = parse_url($url);
		$path = $parse_url['path'];
		$host = $parse_url['host'];

		self::$error = false;
		set_error_handler(array('edost_class', 'RequestError'));

		$fp = fsockopen($host, 80, $errno, $errstr, 4); // 4 - ������������ ����� �������
		restore_error_handler();
//		echo '<br>error: '.($fp ? 'fsockopen TRUE' : 'fsockopen FALSE').' | '.(self::$error ? 'self::error TRUE' : 'self::error FALSE').' | '.$errno.' - '.$errstr;

		if ($errno == 13 || self::$error || !$fp) $r = array('error' => 14); // ��������� ������� �� ��������� ��������� ������ �� ������
		else {
			$out =	"POST ".$path." HTTP/1.0\n".
					"Host: ".$host."\n".
					"Referer: ".$url."\n".
					"Content-Type: application/x-www-form-urlencoded\n".
					"Content-Length: ".strlen($post)."\n\n".
					$post."\n\n";

			fputs($fp, $out);
			$r = '';
			while ($gets = fgets($fp, 512)) $r .= $gets;
			fclose($fp);

//			echo '<br>----------------<br>'.$out.'<br>----------------'; // !!!!!
//			echo '<br><br>response from server (original): ----------------<br>'.$GLOBALS['APPLICATION']->ConvertCharset($r, 'windows-1251', LANG_CHARSET).'<br>----------------';
//			if (!is_array($_SESSION['EDOST']['request'])) $_SESSION['EDOST']['request'] = array();
//			$_SESSION['EDOST']['request'][] = array('out' => $out, 'response' => $GLOBALS['APPLICATION']->ConvertCharset($r, 'windows-1251', LANG_CHARSET));

			$r = stristr($r, 'api_data:', false);
			if ($r === false) $r = array('error' => 8); // ������ ������� �� ��������
			else {
				$r = substr($r, 9);
				if ($type != 'develop') $r = self::ParseData($r, $type);
			}
		}
//		echo '<br><b>request result:</b> <pre style="font-size: 12px">'.print_r($r, true).'</pre>'; // !!!!!

		// ������������ �� ������ ����������� ������, ���� ������ �� ��������
		if (isset($r['error']) && in_array($r['error'], array(8, 14)) && $auto) {
			$server_new = '';
			$ar = array($server_default, DELIVERY_EDOST_SERVER_RESERVE, DELIVERY_EDOST_SERVER_RESERVE2);
			for ($i = 0; $i < count($ar)-1; $i++) if ($ar[$i] == $server) { $server_new = $ar[$i+1]; break; }
			if ($server_new == '') $server_new = $server_default;
			COption::SetOptionString('edost.delivery', ($api2 ? 'server' : 'server_zip'), $server_new);
		}

		return $r;

	}



	public static function SetPropsCode($props) {

//		echo '<br><b>props:</b> <pre style="font-size: 12px">'.print_r($props, true).'</pre>';
		$r = array();
		$ar = CSaleOrderProps::GetList(array(), array(), false, false, array('ID', 'CODE'));
		while ($v = $ar->GetNext()) if (array_key_exists($v['ID'], $props)) $r[$v['CODE']] = array('id' => $v['ID'], 'value' => isset($props[$v['ID']]) ? $props[$v['ID']] : '');
		return $r;

	}


	// �������� ������� ������ + ������ ($param: 'order' - ������� ������ ������,  'no_payment' - �� ��������� ������ � �� ���������� �������,  'no_location' - �� ��������� ��������������,  'shipment' - ����� �� �� ��������,  'office_link' - �������� ������ ������ �������,  'field' - ��������� ���� ��� ��������� ���������� �� ��������)
	public static function GetProps($id, $param = array()) {

		if (empty($id)) return;

		$r = array();
		$order = false;
		$field = array();

		if (in_array('shipment', $param)) {
			// ����� ������ �� id �������� + �������� �������������� �����
			$ar = edost_class::GetShipmentData($id);
			foreach ($ar as $v) {
				$order = \Bitrix\Sale\Order::load($v['order_id']);
				$r += array(
					'delivery_id' => $v['delivery_id'],
					'tracking_code' => $v['tracking_code'],
					'allow_delivery' => $v['allow_delivery'],
				);
				break;
			}
		}
		else if (in_array('order', $param)) $order = $id;
		else $order = Bitrix\Sale\Order::load($id);

		if (empty($order)) return false;

		$r['order_paid'] = $order->isPaid();

		// �������� ������
		$props = array();
		$ar = $order->getPropertyCollection();
		foreach ($ar->getGroups() as $v) foreach ($ar->getGroupProperties($v['ID']) as $v2) $props[$v2->getField('CODE')] = $v2->getValue();
//		echo htmlspecialcharsbx($v2->getField('CODE').' - '.$v2->getName()).' - '. $v2->getValue().'<br>';  echo $v2->getViewHtml();  echo $v2->getEditHtml();
//		echo '<br><b>props:</b> <pre style="font-size: 12px">'.print_r($props, true).'</pre>';

		$s = '';
		if (!empty($props['COMPANY'])) $s = $props['COMPANY'];
		else if (!empty($props['FIO'])) $s = $props['FIO'];
		else if (!empty($props['NAME'])) $s = $props['NAME'];
		$r['name'] = $s;

		$r['phone'] = (!empty($props['PHONE']) ? $props['PHONE'] : '');

		$address = (!empty($props['ADDRESS']) ? $props['ADDRESS'] : '');
		$office = self::ParseOfficeAddress($address);

		if (!in_array('no_location', $param)) {
			$location_name = '';
			$location_code = (!empty($props['LOCATION']) ? $props['LOCATION'] : 0);
			$location_data = false;
			$location_edost = CDeliveryEDOST::GetEdostLocation($location_code);
			if (!empty($location_edost)) {
				$city = (!empty($props['CITY']) ? $props['CITY'] : '');
				$location_data = array('country' => $location_edost['country'], 'region' => $location_edost['region'], 'city' => $location_edost['city']);
				if (CModule::IncludeModule('edost.locations') && method_exists('CLocationsEDOST', 'ParseAddress')) {
					if ($address != '') {
						$s = CLocationsEDOST::ParseAddress($address);
						if (!empty($s['city2'])) {
							$city = $s['city2'];
							$s = explode('; ', $address);
							$address = $s[0];
						}
					}
					$location = CLocationsEDOST::GetData(CSaleLocation::getLocationIDbyCODE($location_code), $city, true);
					if (!empty($location) && $location_data['country'] == 0) $location['show_country'] = false;
				}
				else {
					$location = array(
						'country' => (!empty($location_edost['country_name']) ? $location_edost['country_name'] : ''),
						'region' => (!empty($location_edost['region_name']) ? $location_edost['region_name'] : ''),
						'city' => (!empty($location_edost['bitrix']['city']) ? $location_edost['bitrix']['city'] : $city),
					);
				}
				$s = '';
				if (!empty($location)) {
					$s = $location['city'];
					if (!empty($location['region']))
						if ($s == '') $s = $location['region'];
						else $s .= ' ('.$location['region'].')';
					if ($s == '' || !empty($location['country']) && !empty($location['show_country'])) $s .= ($s != '' ? ', ' : '').$location['country'];
				}
				$location_name = $s;
			}
			$r += array(
				'location_name' => $location_name, // ������ ��������
				'location_code' => $location_code, // ���
				'location_data' => $location_data, // ������ ��� ������ eDost
			);
		}
		$r['address'] = $address; // ����� � ��������� ������� (���� ��� 'no_location') ��� ������������ ������ �� �����

		// ���������� ��������� ������� + ����������� ������� + �������� ������ �����
		if (!in_array('no_payment', $param)) {
			$ar = $order->getPaymentCollection();
			if (count($ar) == 1) {
				$payment = $ar->rewind();
				if (!empty($payment)) {
					$ar = \Bitrix\Sale\PaySystem\Manager::getListWithRestrictions($payment, \Bitrix\Sale\Services\PaySystem\Restrictions\Manager::MODE_MANAGER);
					if (!empty($ar)) {
						$paysystem_list = array();
						$paysystem_id = $payment->getPaymentSystemId();
						$cod = false;
						$a = false;
						foreach ($ar as $v) {
							$s = array('ID' => $v['ID'], 'NAME' => str_replace(array('<', '>'), array('&lt;', '&gt;'), $v['NAME']));
							if ($v['ID'] == $paysystem_id) $s['checked'] = true;
							if (substr($v['ACTION_FILE'], -11) == 'edostpaycod') {
						    	$a = true;
						    	$s['cod'] = true;
						    	if (!empty($s['checked'])) $cod = true;
						    }
						    $paysystem_list[] = $s;
						}
	                    if ($a) {
							if ($cod) {
								$r['cod'] = true;
								if ($payment->isPaid()) $r['paid'] = true;
							}
							$r['paysystem_list'] = $paysystem_list;
							$r['payment_id'] = $payment->getId();
//							$r['paysystem_name'] = $payment->getPaymentSystemName();
	                    }
					}
				}
			}
		}

		// �������������� ������ ��� ����
		if (!empty($office)) {
			$r['office'] = $office;

			$sign = GetMessage('EDOST_DELIVERY_SIGN');
			$ico_path = '/bitrix/images/delivery_edost_img';
			$link = (in_array('office_link', $param) ? true : false);

			$s = $office['head'];
			$s2 = ' text-decoration: none;" href="http://edost.ru/office.php?c='.$office['id'].'" target="_blank"';

			$code_head = '';
			foreach ($sign['code_head'] as $v) if (isset($v[1]) && strpos($s, $v[0]) === 0) { $code_head = $v[1]; break; }

			if ($link) {
				$s = '<a style="font-weight: bold; '.$s2.'>'.$s.'</a>';
				$s .= ' <img class="edost_control_button_new_active" style="vertical-align: middle;" src="'.$ico_path.'/control_show.png" border="0" onclick="edost_ShowDetail(this, \'address_show\')"><div style="display: none;">';
			}
			else $s = '<b>'.$s.'</b> (<a style="'.$s2.'>'.$sign['map'].'</a>)<br>';

			$s .= $office['address'];
			if (!empty($office['tel'])) $s .= '<br>'.$office['tel'];
			if (!empty($office['schedule'])) $s .= '<br>'.str_replace(', ', '<br>', $office['schedule']);
			if (!empty($code_head) && !empty($office['code']) && !in_array($office['code'], array('S', 'T'))) {
				if ($link) $field = array(array('name' => $sign['code_head']['code'].$code_head, 'value' => $office['code'], 'admin' => true, 'bold' => true));
				else $s .= '<br>'.$sign['code_head']['code2'].$code_head.': '.$office['code'];
			}
			if ($link) $s .= '</div>';

			$address = $s;
		}
		$r['address_formatted'] = $address;

		// ���� ��� ��������� ���������� �� ��������
		if (in_array('field', $param)) {
			$ar = array();
			$control = GetMessage('EDOST_DELIVERY_CONTROL');
			if (!empty($r['tracking_code'])) $ar[] = array('name' => $control['tracking_head'], 'value' => $r['tracking_code'], 'admin' => true, 'bold' => true);
			if (!empty($r['phone'])) $ar[] = array('name' => $control['phone_head'], 'value' => $r['phone'], 'admin' => true, 'bold' => true);
			if (!empty($r['address_formatted'])) $ar[] = array('name' => $control['address_head'], 'value' => $r['address_formatted'], 'admin' => true, 'bold' => empty($office) ? true : false);
			$r['field'] = array_merge($ar, $field);
		}

//		echo '<br><b>props</b> <pre style="font-size: 12px">'.print_r($r, true).'</pre>';

		return $r;

	}


	// ��������� ������ �������� � ������������ �������� ($shipment - �������/������ �������� 'shipment' ��� id ��������,  $user_id - ���� �������, �������� ����������� �� ������������, ������ ��� id ��������)
	public static function GetShipmentData($shipment, $user_id = false) {

		if (empty($shipment)) return false;

		$r = array();
		$id = array();
		if (!is_array($shipment)) $shipment = array($shipment);

		foreach ($shipment as $k => $v) if (!is_object($v)) {
			$v = intval($v);
			if ($v == 0) continue;
			$id[] = $v;
			unset($shipment[$k]);
		}

		// �������� �������� �� id
		if (!empty($id)) {
			$filter = array('=ID' => $id);
			if (!empty($user_id)) $filter['=ORDER.USER_ID'] = $user_id;
			$ar = \Bitrix\Sale\Internals\ShipmentTable::getList(array(
				'select' => array('ID', 'ORDER.LID', 'ORDER.ACCOUNT_NUMBER', 'ORDER.STATUS_ID', 'ORDER.PAYED', 'ORDER_ID', 'DELIVERY_ID', 'ACCOUNT_NUMBER', 'STATUS_ID', 'ALLOW_DELIVERY', 'DEDUCTED', 'CANCELED', 'TRACKING_NUMBER', 'TRACKING_STATUS', 'TRACKING_DESCRIPTION', 'DELIVERY_NAME', 'COMMENTS', 'ORDER.COMMENTS'),
				'filter' => $filter,
				'limit' => 1000
			));
			while ($v = $ar->fetch()) {
				$s = array('ALLOW_DELIVERY', 'DEDUCTED', 'CANCELED', 'SALE_INTERNALS_SHIPMENT_ORDER_PAYED');
				foreach ($s as $k) $v[$k] = ($v[$k] == 'Y' ? true : false);
				$shipment[] = $v;
			}
//			echo '<br><b>GetShipmentData:</b> <pre style="font-size: 12px">'.print_r($shipment, true).'</pre>';
		}

		foreach ($shipment as $k => $v) {
			$o = (is_object($v) ? true : false);
			$delivery_id = ($o ? $v->getDeliveryId() : $v['DELIVERY_ID']);

			$tariff = CDeliveryEDOST::GetEdostProfile($delivery_id, true);
			if ($tariff === false) continue;

			if ($o) $order = $v->getCollection()->getOrder();

			$id = ($o ? $v->getId() : $v['ID']);
			$r[$id] = $tariff + array(
				'id' => $id,
				'tracking_code' => ($o ? $v->getField('TRACKING_NUMBER') : $v['TRACKING_NUMBER']),
				'delivery_id' => $delivery_id,

				'order_id' => ($o ? $order->getId() : $v['ORDER_ID']),
				'order_number' => ($o ? $order->getField('ACCOUNT_NUMBER') : $v['SALE_INTERNALS_SHIPMENT_ORDER_ACCOUNT_NUMBER']),
				'order_paid' => ($o ? $order->isPaid() : $v['SALE_INTERNALS_SHIPMENT_ORDER_PAYED']),
				'site_id' => ($o ? $order->getSiteId() : $v['SALE_INTERNALS_SHIPMENT_ORDER_LID']),
				'order_status' => ($o ? $order->getField('STATUS_ID') : $v['SALE_INTERNALS_SHIPMENT_ORDER_STATUS_ID']),
//				'order_comments' => ($o ? '' : $v['SALE_INTERNALS_SHIPMENT_ORDER_COMMENTS']),
//				'location_id' => ($o ? $order->getDeliveryLocation() : ''),

				'allow_delivery' => ($o ? $v->isAllowDelivery() : $v['ALLOW_DELIVERY']),
				'deducted' => ($o ? $v->isShipped() : $v['DEDUCTED']),
				'canceled' => ($o ? $v->isCanceled() : $v['CANCELED']),
//				'comments' => ($o ? '' : $v['COMMENTS']),
			);
			if ($o) $r[$id]['order'] = $order;
		}

//		echo '<br><b>GetShipmentData:</b> <pre style="font-size: 12px">'.print_r($r, true).'</pre>';

		return $r;

	}


	// ��������� �������������� �������� � ������� ��� ������ ������ ��� �������� � ������������ �������� � 'allow_delivery' ($id - �������/������ id ��������  ���  ���������� �� id ������������ ��� $mode == 'user')
	public static function GetControlShipment($id, $mode = '') {

		if (empty($id)) return false;

		$r = self::Control();

		if ($mode == 'user') {
			if (empty($r['data'])) return false;
			$ar = self::GetShipmentData(array_keys($r['data']), $id);
			foreach ($r['data'] as $k => $v) if (!empty($ar[$k])) $r['data'][$k] += $ar[$k]; else unset($r['data'][$k]);
		}
		else {
			if (!is_array($id)) $id = array($id);
			$ar = self::GetShipmentData($id);
			if (!empty($r['data'])) foreach ($r['data'] as $k => $v)
				if (empty($ar[$k])) unset($r['data'][$k]);
				else {
					$r['data'][$k] += $ar[$k];
					$r['data'][$k]['control'] = true;
					$i = array_search($k, $id);
					if ($i !== false) unset($id[$i]);
				}

			if (!empty($id)) {
				if (empty($r['data'])) $r['data'] = array();
				foreach ($id as $k) if (!empty($ar[$k]) && $ar[$k]['allow_delivery'] && !empty($ar[$k]['tracking_code'])) $r['data'][$k] = $ar[$k];
			}
		}

		// ����������� �� �������� � ������� eDost � ����������� ��������� ���������
		if (!empty($r['data'])) {
			$config = CDeliveryEDOST::GetEdostConfig('all');
			foreach ($r['data'] as $k => $v) {
				$c = false;
				if (isset($config[$v['site_id']])) $c = $config[$v['site_id']];
				else if (isset($config['all'])) $c = $config['all'];

				$shop_id = (isset($c['id']) ? $c['id'] : '');
				$r['data'][$k]['shop_id'] = $shop_id;
				$r['data'][$k]['control_count'] = (isset($r['control'][$shop_id]['count']) ? $r['control'][$shop_id]['count'] : 0);
			}
		}

//		echo '<br><b>GetControlShipment:</b> <pre style="font-size: 12px">'.print_r($r, true).'</pre>';

		return $r;

	}


	// ��������� ���������� �� ��������������� ������
	public static function GetControlDetail($id) {

		$id = intval($id);
		$v = self::GetShipmentData($id);
  		if (empty($v[$id])) return false;

  		$v = $v[$id];

		$cache = new CPHPCache();
		if ($cache->InitCache(300, 'sale|16.0.0|edost|detail|'.$id.'|'.$_SESSION['VREGIONS_REGION']['ID'], '/')) $r = $cache->GetVars();
		else {
			$config = CDeliveryEDOST::GetEdostConfig($v['site_id']);
			$r = self::RequestData('', $config['id'], $config['ps'], 'type=control&mode=detail&data='.$id.'|end|', 'detail');
			if (!isset($r['error'])) {
				$cache->StartDataCache();
				$cache->EndDataCache($r);
			}
		}

//		echo '<br><b>'.$id.'</b> <pre style="font-size: 12px">'.print_r($r, true).'</pre>';

		return $r;

	}


	// ��������� html ����� �� �������� ��������: ������������, ����������� �� ������� "���������...", ����� ������ ($string_length - ����� ������ ��� ����������� ������)
	public static function GetControlString($data, $string_length = 0) {

		if (empty($data)) return '';

		$r = '';
		$detail = true;
		$control = GetMessage('EDOST_DELIVERY_CONTROL');

		if (isset($data['id'])) {
			$detail = false;
			$data = array($data);
		}

		$count = count($data);
		$max = 16; // ���������� ���������� ����� ��� ������ "�������� ���..."

		$i = 0;
		foreach ($data as $k => $v) {
			$i++;
			if (!isset($v['status'])) return false;

			$s = '';
			$status = $v['status_string'];
			if (empty($v['status_string']) && !empty($control['status'][$v['status']])) $status = $control['status'][$v['status']];

			if (!empty($v['status_date'])) $s .= '<span class="edost_control_date">'.$v['status_date'].'</span>';
			if (!$string_length && $detail && !empty($v['status_time'])) $s .= ' <span class="edost_control_time">'.$v['status_time'].'</span>';

			if ($string_length) {
				$n = (function_exists('mb_strlen') ? mb_strlen($status, LANG_CHARSET) : strlen($status));
				if ($n > $string_length) $status = (function_exists('mb_substr') ? mb_substr($status, 0, $string_length, LANG_CHARSET) : substr($status, 0, $string_length)).'...';
				return (!empty($v['status_date']) ? $v['status_date'].' - ' : '').$status;
			}

			$color = '';
			if (in_array($v['status'], array(4, 5, 7))) $color = 'green';
			if (!empty($v['status_warning']))
				if ($v['status_warning'] == 1) $color = 'pink';
				else if ($v['status_warning'] == 2) $color = 'red';
				else if ($v['status_warning'] == 3) $color = 'orange';
			if ($color != '') $color = ' edost_control_color_'.$color;

			if (!empty($v['status_info']))
				if (!$detail) $status .= ' ('.str_replace(array(' (', ') ', '(', ')'), array(', ', ', ', ', ', ''), $v['status_info']).')';
				else $status .= '<br><span class="edost_control_color_light" style="font-size: 12px;">'.$v['status_info'].'</span>';

			$v['status'] = ''; // ���������������� ������ ��� ������ ����� �������� �������� !!!!!

			$status = ($v['status'] !== '' ? $v['status'].' - ' : '').'<span class="edost_control_status'.$color.'">'.$status.'</span>';
			if (!$detail) $s .= '&nbsp;&nbsp;'.$status;
			else $s = '<div class="edost_control_td1">'.$s.'</div>'.'<div class="edost_control_td2">'.$status.'</div>';

			if (!$detail) $c = '';
			else if ($k == 0) $c = 'first';
			else if ($k % 2 == 1) $c = 'odd';
			else $c = 'even';

			if ($count > $max && $i == $max-5) $r .= '<span class="edost_link edost_control_detail" style="float: left;" onclick="edost_ShowDetail(this, \'all\')">'.$control['detail_all'].'</span><div style="display: none;">';
			$r .= '<div class="edost_control_string'.($c != '' ? '_'.$c : '').($k == 0 ? ' edost_control_string_bold' : '').'">'.$s.'</div>';
		}
		if ($count > $max) $r .= '</div>';

		if (!$detail && !$string_length && !empty($data[0]['id'])) {
			if (!empty($data[0]['status']) && !in_array($data[0]['status'], array(13, 14))) $r = '<div id="edost_control_'.$data[0]['id'].'_string">'.$r.'<span class="edost_link edost_control_detail" onclick="edost_ShowDetail('.$data[0]['id'].')">'.$control['detail'].'</span></div><div id="edost_control_'.$data[0]['id'].'_detail" style="padding: 10px 0 0 0; display: none;"></div>';
			$r = '<div class="edost_control" id="edost_control_'.$data[0]['id'].'">'.$r.'</div>';
		}

		return $r;

	}


	// �������� ����������������� ���� ��������
	public static function GetCache() {

		$date = date('dmY');
		$v = COption::GetOptionString('edost.delivery', 'cache', '');

		if ($v == $date.'|Y') return true;
		if ($v == $date.'|N') return false;
		if (!empty($_REQUEST['clear_cache']) && $_REQUEST['clear_cache'] == 'Y') return true;

		$v = explode('|', $v);
		$v = (isset($v[1]) ? intval($v[1]) : 0);

		$cache = new CPHPCache();
		if ($cache->InitCache(86400, 'sale|16.0.0|edost|cache|'.$_SESSION['VREGIONS_REGION']['ID'], '/')) $v = 'Y';
		else {
			$v++;
			if ($v > 3) $v = 'N';
			$cache->StartDataCache();
			$cache->EndDataCache('data');
		}

		COption::SetOptionString('edost.delivery', 'cache', $date.'|'.$v);

		return ($v !== 'N' ? true : false);

	}


	// �������� ��������� �������� �� cookie (��� �������)
	public static function GetCookie() {

		$ar = (isset($_COOKIE['edost_admin']) && $_COOKIE['edost_admin'] != '' ? explode('|', preg_replace("/[^0-9a-z_|-]/i", "", $_COOKIE['edost_admin'])) : array());

		$r = array(
			'filter_days' => '5', // ������ ����������� �� ��������� 'filter_days' ����
			'docs_active' => '', // �������� ��������� ��� ������ ������
			'setting_active' => 'module', // �������� ��������� (module, paysystem, document)
			'setting_tariff_show' => 'N', // ������������� �������� ������� (Y, N)
			'admin_type' => '', // ��������� ������������� ��������
			'control_day_delay' => '5', // �������� ���� ��������
			'control_day_office' => '2', // ����� � ������ ������
			'control_day_complete' => '15', // ������� ���������� �������
			'control_show_total' => 'N', // ������ �� ��������� ��������
			'control_setting' => 'N', // �������� ���� � ����������� ��������
			'control_delete' => 'Y', // �������� ������ "����� � ��������" ��� �������� �������
			'control_paid' => 'Y', // �������� ������ "��������� ������" ��� �������� ������� � ���������� ��������
			'control_changed' => 'Y', // �������� ������ � ��������, � ������� ������� ��������� ������
			'control_complete_delay' => 'N', // �������� �� ������� �������� ���� �������� � ����������� �������
		);

		$i = 0;
		foreach ($r as $k => $v) {
			$r[$k] = (isset($ar[$i]) && $ar[$i] !== '' ? $ar[$i] : $v);
			$i++;
		}

		$r['docs_active'] = ($r['docs_active'] != '' ? explode('-', $r['docs_active']) : array());

		return $r;

	}


	// ��������� �������� � ������� ����� �����������
	public static function GetTracking($site_id = false) {

		if (!self::GetCache()) return false;

		$cache = new CPHPCache();
		if ($cache->InitCache(86400*5, 'sale|16.0.0|edost|tracking|'.$_SESSION['VREGIONS_REGION']['ID'], '/')) return $cache->GetVars();

		$config = CDeliveryEDOST::GetEdostConfig($site_id !== false ? $site_id : SITE_ID);
		$r = edost_class::RequestData('', $config['id'], $config['ps'], 'type=tracking', 'tracking');
//		echo '<br><b>GetTracking:</b> <pre style="font-size: 12px">'.print_r($r, true).'</pre>';

		if (!isset($r['error'])) {
			$cache->StartDataCache();
			$cache->EndDataCache($r);
		}

		return $r;

	}


	// ������ � ���������� �������� �� ��������� 28 �����: ���������� + �������� ����� ������ + �������� ����� ������ ($id - �� ����������� ��������)
	public static function ControlChanged($id = false) {

		if ($id === 'delete') {
			COption::SetOptionString('edost.delivery', 'control_changed', '');
			return false;
		}

		$time = time();
		$r = COption::GetOptionString('edost.delivery', 'control_changed', '');
		$r = ($r != '' ? unserialize($r) : array());

		// �������� ������ �������
		if (!empty($r)) foreach ($r as $k => $v) if ($time - $v > 100800) unset($r[$k]);

		if (!empty($id)) {
			$r[$id] = $time;
			COption::SetOptionString('edost.delivery', 'control_changed', serialize($r));
		}

		return $r;

	}


	// �������� �������: ���������� ��� ��������� ������ + �������� ����� ������ ($shipment - �������/������ �������� 'shipment' ��� id ��������,  $flag - ��� ��� �������: add - ��������, delete - �������, new - ������� '�����', old - ������� '������', special - �� ������ ��������,  $data - ������ ��������, ������ �������� ������������ ������ � �������)
	public static function Control($shipment = false, $flag = false, $data = false) {

		if ($shipment !== false) $shipment = self::GetShipmentData($shipment);

		$mode = 'add';
		if ($flag !== false) {
			if (empty($flag)) {
				$mode = 'delete';
				$flag = 0;
			}
			else if (intval($flag) == 0) {
				$mode = $flag;
				$flag = false;
			}
		}

		$r = array();
		$add = array();
		$reload = false;
		$save = false;

		if (!empty($shipment)) {
			$c = ($data !== false ? $data : self::Control());

			$key = array('id', 'flag', 'tariff', 'tracking_code', 'country', 'region', 'city', 'order_paid');
			if (in_array($mode, array('delete', 'new', 'old', 'special', 'normal'))) $key = array_slice($key, 0, 2);

			// ������ �������� �� ������
			$ar = array();
			foreach ($shipment as $k => $v) {
				if (isset($c['data'][$k])) $v = $v + $c['data'][$k];
				else if ($mode == 'delete') continue;

				if ($mode == 'add') {
					if (empty($v['order'])) $p = edost_class::GetProps($v['order_id'], array('no_payment'));
					else {
						$p = edost_class::GetProps($v['order'], array('order', 'no_payment'));
						unset($v['order']);
					}
//					echo '<br><b>GetProps</b> <pre style="font-size: 12px">'.print_r($p, true).'</pre>';
					if (!empty($p['location_data'])) $v += $p['location_data'];
//					echo '<br><b>add location</b> <pre style="font-size: 12px">'.print_r($v, true).'</pre>';
				}

				$ar[$v['site_id']][] = $v;
			}

			// �������� ������ �� ������ ��������
			foreach ($ar as $site_id => $site_shipment) {
				$config = CDeliveryEDOST::GetEdostConfig($site_id);

				foreach ($site_shipment as $k => $v) {
					if ($flag !== false) $f = $flag;
					else {
						$f = (!empty($v['flag']) ? $v['flag'] : 1);

						if ($mode == 'delete') $f = 0;
						else if ($mode == 'new') $f = ($f == 3 ? 4 : 2);
						else if ($mode == 'old') $f = ($f == 4 ? 3 : 1);
						else if ($mode == 'special') $f = ($f == 2 ? 4 : 3);
						else if ($mode == 'normal') $f = ($f == 4 ? 2 : 1);
					}

					$site_shipment[$k]['flag'] = $f;
					$site_shipment[$k]['shop_id'] = $config['id'];
				}
//				echo '<br><b>mode: '.$mode.':</b> <pre style="font-size: 12px">'.print_r($site_shipment, true).'</pre>';

				$s = 'type=control&mode=add&data='.edost_class::PackData($site_shipment, $key);
//				echo '<br><b>shipment</b> <pre style="font-size: 12px">'.print_r($s, true).'</pre>';
				$data = edost_class::RequestData($config['host'], $config['id'], $config['ps'], $s, 'control');
//				echo '<br><b>post|'.$s.'===== mode: '.$mode.' ('.$site_id.'):</b> <pre style="font-size: 12px">'.print_r($data, true).'</pre>';

				if (isset($data['error']) || isset($data['warning'])) $reload = true;
				else foreach ($site_shipment as $v) $add[$v['id']] = $v;
			}

			// ���������� ������ ��� ��������� ���� (����� �� ������� ����� �� �������� ������ � �������)
			foreach ($add as $k => $v) {
				$ar = array(
					'flag' => 1,
					'status' => 0,
					'status_warning' => '',
					'status_string' => '',
					'status_info' => '',
				);
				if ($mode == 'add') {
					$v['status_date'] = date('d.m.Y');
					$v['status_time'] = '';
				}
				$add[$k] = $v + $ar;
			}
//			echo '<br><b>'.$mode.':</b> <pre style="font-size: 12px">'.print_r($add, true).'</pre>';
		}

		$cache = new CPHPCache();
		$cache_id = 'sale|16.0.0|edost|control|'.$_SESSION['VREGIONS_REGION']['ID'];
		if ($cache->InitCache(3600, $cache_id, '/')) $r = $cache->GetVars();
		else if (empty($add)) $reload = true;

		// ���������� ������ � ��������� ����
		if (!empty($add) && !$reload) {
			$r = $c;
			foreach ($add as $k => $v) {
				if ($mode == 'delete') {
					if ($v['status'] == 0) $r['control'][$v['shop_id']]['count']++;
					if (isset($r['data'][$k])) unset($r['data'][$k]);
				}
				else {
					if ($mode == 'add') $r['control'][$v['shop_id']]['count']--;
					$r['data'][$k] = $v;
				}
			}
			$save = true;
		}

		if ($reload) {
			// �������� ���� ������� � ������� ��������
			$config = CDeliveryEDOST::GetEdostConfig('all');
			$s = 'type=control&mode=get&data='.edost_class::PackData($config, array('id', 'ps'));
			foreach ($config as $v) { $config = $v; break; }
			$r = edost_class::RequestData($config['host'], $config['id'], $config['ps'], $s, 'control');
			$save = (!isset($r['error']) ? true : false);

			// ���������� �������� ������� (����� �������� ��� �������� � ����� ������)
			if (!empty($r['data'])) {
				$ar = array();
				foreach ($r['data'] as $k => $v) if ($v['day_office'] >= 1 || $v['status'] == 5) $ar[] = $k;
				$ar = self::GetShipmentData($ar);
				if (!empty($ar)) foreach ($ar as $k => $v) {
					$v += $r['data'][$k];

					$config = CDeliveryEDOST::GetEdostConfig($v['site_id']);
					$status = array(
						'arrived' => $config['control_status_arrived'],
						'completed' => $config['control_status_completed'],
						'completed_cod' => $config['control_status_completed_cod'],
					);

					$s = false;
					$complete = false;
					if ($v['status'] == 5) {
						if (empty($status['completed']) && empty($status['completed_cod'])) continue;
						if (!empty($status['completed']) && $status['completed'] == $v['order_status'] || !empty($status['completed_cod']) && $status['completed_cod'] == $v['order_status']) continue;
						$props = edost_class::GetProps($v['order_id']);
						if (!empty($props['cod'])) {
							if (!empty($status['completed_cod'])) $s = $status['completed_cod'];
						}
						else if (!empty($status['completed'])) {
							$s = $status['completed'];
							$complete = true;
						}
					}
					else {
						if (empty($status['arrived']) || $status['arrived'] == $v['order_status']) continue;
						$s = $status['arrived'];
					}
					if ($s !== false) {
						self::ControlChanged($v['id']);
						$order = \Bitrix\Sale\Order::load($v['order_id']);
						$order->setField('STATUS_ID', $s);
						$order->save();
					}
				}
			}
		}

		if ($save) {
			$cache->Clean($cache_id, '/');
			$cache->StartDataCache();
			$cache->EndDataCache($r);
		}

		// ������ ���������� ������� �� ������ ������ + ����������� 'new' � 'special'
		$control = GetMessage('EDOST_DELIVERY_CONTROL');
		$count = array_fill_keys(array_keys($control['count_head']), 0);
		if (!empty($r['data'])) foreach ($r['data'] as $k => $v) {
			$v['new'] = ($v['flag'] == 2 || $v['flag'] == 4 ? true : false);
			$v['special'] = ($v['flag'] == 3 || $v['flag'] == 4 ? true : false);
			$r['data'][$k] = $v;

			$count['total']++;
			if (!empty($v['new'])) $count['new']++;
			if (!empty($v['special'])) $count['special']++;
			if ($v['status_warning'] == 1) $count['warning_pink']++;
			if ($v['status_warning'] == 2) $count['warning_red']++;
			if ($v['status_warning'] == 3) $count['warning_orange']++;
			if ($v['status'] != 5 && $v['day_delay'] >= 1) $count['delay']++;
			if ($v['status'] != 5 && $v['day_office'] >= 1) $count['office']++;
			if ($v['status'] == 0) $count['add']++;
		}
		$r['count'] = $count;

//		echo '<br><b>control:</b> <pre style="font-size: 12px">'.print_r($r, true).'</pre>';

		return $r;

	}


	// �������� ������
	public static function GetOffice($order, $company) {

		if (!isset($order['location']['country']) || empty($company)) return false;

		if (class_exists('edost_function') && method_exists('edost_function', 'BeforeGetOffice')) edost_function::BeforeGetOffice($order, $company);

		$data = array();
		$location = $order['location'];
		$config = $order['config'];
		$company = implode(',', $company);

		$cache_id = 'sale|16.0.0|edost|office|'.$location['id'].'|'.$company.'|'.$_SESSION['VREGIONS_REGION']['ID'];
		$cache = new CPHPCache();
		if ($cache->InitCache(86400, $cache_id, '/')) {
			$data = $cache->GetVars();
			$data['cache'] = true;
			if (defined('DELIVERY_EDOST_FUNCTION_RUN_AFTER_CACHE') && DELIVERY_EDOST_FUNCTION_RUN_AFTER_CACHE == 'Y') if (class_exists('edost_function') && method_exists('edost_function', 'AfterGetOffice')) edost_function::AfterGetOffice($order, $data);
		}
		else {
			$ar = array();
			$ar[] = 'type=office';
			$ar[] = 'country='.$location['country'];
			$ar[] = 'region='.$location['region'];
			$ar[] = 'city='.urlencode($location['city']);
			$ar[] = 'company='.urlencode($company);
			if (!empty($order['pickpoint_widget'])) $ar[] = 'pickpoint=1'; // �������� ������ ������ ��� ������ ��� ������� PickPoint (������ Visual)
			$data = self::RequestData('', $config['id'], $config['ps'], implode('&', $ar), 'office');

			if (class_exists('edost_function') && method_exists('edost_function', 'AfterGetOffice')) edost_function::AfterGetOffice($order, $data);

			if (!isset($data['error'])) {
				foreach ($data['data'] as $k => $v) foreach ($v as $k2 => $v2)
					$data['data'][$k][$k2]['address_full'] = $v2['address'].($v2['address2'] != '' ? ', ' : '').$v2['address2'];

				$cache->StartDataCache();
				$cache->EndDataCache($data);
			}
		}

//		echo '<br><b>get office:</b> <pre style="font-size: 12px">'.print_r($data, true).'</pre>';
//		$_SESSION['EDOST']['office'] = $data;

		// ����������� �� ���������� ������
		if (!empty($data['data']) && !empty($data['limit']))
			foreach ($data['limit'] as $v) if (isset($data['data'][$v['company_id']]))
				foreach ($data['data'][$v['company_id']] as $k2 => $v2) if ($v2['type'] == $v['type']) {
					$a = false;
					if ($order['weight'] < $v['weight_from'] || $v['weight_to'] != 0 && $order['weight'] > $v['weight_to']) $a = true;

					$ar = array('size1', 'size2', 'size3', 'sizesum');
					foreach ($ar as $s) if ($v[$s] != 0 && $order[$s] > $v[$s]) $a = true;

					if ($a) unset($data['data'][$v['company_id']][$k2]);
					else if ($v['price'] != 0) $data['data'][$v['company_id']][$k2]['codmax'] = intval($v['price'] - $order['price'] - 1);
				}

		return $data;

	}


	// �������������� �������
	public static function FormatTariff($bitrix_data, $currency, $order, $active, $config = array()) {
//		echo '<br><b>FormatTariff order:</b> <pre style="font-size: 12px">'.print_r($order, true).'</pre>';
//		echo '<br><b>FormatTariff bitrix_data:</b> <pre style="font-size: 12px">'.print_r($bitrix_data, true).'</pre>';

		$r = array();
		$data = array();
		$sign = GetMessage('EDOST_DELIVERY_SIGN');
		$rename = GetMessage('EDOST_DELIVERY_RENAME');
		$base_currency = CDeliveryEDOST::GetRUB();
		$edost_order = (isset(CDeliveryEDOST::$result['order']) ? CDeliveryEDOST::$result['order'] : array());
		$converted = (\Bitrix\Main\Config\Option::get('main', '~sale_converted_15', 'N') == 'Y' ? true : false); // �������� �� ������� 16

		if (!empty($edost_order['config'])) $config = $config + $edost_order['config'];
		foreach (CDeliveryEDOST::$setting_key as $k => $v) if (empty($config[$k])) $config[$k] = $v;
		if (!empty($config['CATALOGDELIVERY'])) {
			if ($config['template_block_type'] == 'bookmark2') $config['template_block_type'] = 'bookmark1';
			if (!empty($config['SHOW_ERROR'])) {
				$config['hide_error'] = 'N';
				$config['show_zero_tariff'] = 'Y';
			}
			else {
				$config['hide_error'] = 'Y';
				$config['show_zero_tariff'] = 'N';
			}
		}
		if ($config['template'] != 'Y') {
			$config['template_autoselect_office'] = 'N';
			$config['template_format'] = 'off';
			$config['template_cod'] = 'off';
		}
		if ($config['template_autoselect_office'] == 'Y') $config['template_map_inside'] = 'N';
		if ($config['template_format'] == 'off') $config['template_block'] = 'off';
		if ($config['template_block'] == 'off') {
			$config['template_block_type'] = 'none';
			$config['template_map_inside'] = 'N';
		}
		else if ($config['template_block'] != 'all' && ($config['template_block_type'] == 'bookmark1' || $config['template_block_type'] == 'bookmark2')) $config['template_block'] = 'auto2';
		if (empty($config['template_map_inside'])) $config['template_map_inside'] = 'N';
		$edost_order['config'] = $config;
//		echo '<br><b>config format:</b> <pre style="font-size: 12px">'.print_r($config, true).'</pre>';
//		echo '<br><b>edost_order:</b> <pre style="font-size: 12px">'.print_r($edost_order, true).'</pre>';

		$office_get = array();
		$office_key = array('shop', 'office', 'terminal');
		$edost_enabled = false;
		$edost_error = false;
		$edost_bitrix_sort = -1;
		$bookmark = in_array($config['template_block_type'], array('bookmark1', 'bookmark2'));
		$show_error = (!isset($config['SHOW_ERROR']) || $config['SHOW_ERROR'] ? true : false);
		$shipment = false;

		// ���������� � �������������� ������ ��� ������� ��� ����������
		if (!empty($active['bookmark'])) {
			$s = explode('_', $active['bookmark']);
			if ($config['template_block_type'] == 'bookmark2' && $s[0] != 'show') $active = array('id' => '', 'bookmark' => $s[0]);
			else if (isset($s[1]) && $s[1] == 's') $active = (isset($_SESSION['EDOST']['delivery_default'][$s[0]]) ? $_SESSION['EDOST']['delivery_default'][$s[0]] : array('id' => '', 'bookmark' => $s[0]));
			else $_SESSION['EDOST']['delivery_default'][$s[0]] = $active;
		}

		// ��������� ������� ������� � 'PROFILES'
		$ar = array();
		if (!empty($bitrix_data) && is_array($bitrix_data)) foreach ($bitrix_data as $k => $v)
			if (empty($v['PROFILES'])) $ar[] = $v;
			else foreach ($v['PROFILES'] as $k2 => $v2) {
				$v2['NAME'] = $v2['TITLE'];
				$v2['CODE'] = $k.':'.$k2;
				if (!empty($v['LOGOTIP'])) $v2['LOGOTIP'] = $v['LOGOTIP'];
				if ($k !== 'edost') $v2['company'] = $v['TITLE'];
				$ar[] = $v2;
			}
		$bitrix_data = $ar;
//		echo '<br><b>bitrix_data code:</b> <pre style="font-size: 12px">'.print_r($bitrix_data, true).'</pre>';

		// ������� ������� ������� �������� � ����������� ������
		foreach ($bitrix_data as $delivery_key => $delivery) {

			// ��������� ������ ������� !!!!!!!
			if(in_array($delivery["ID"],array(412,417,413,415,414,416,421))) continue;

			if ($_SESSION['VREGIONS_REGION']['ID'] == 14646){
	          if($delivery["ID"] == '348') $delivery["DESCRIPTION"] = iconv("windows-1251","UTF-8","�. ������, ��������������� ������ 7 ������ 3 �� ��������� ���� ���. h2-003<br>����� ������: ��-�� � 10:00 �� 21:00");
	          if($delivery["ID"] == '340') $delivery["DESCRIPTION"] = iconv("windows-1251","UTF-8","�������� � �������� ���� - 390�<br>�������� �������������� ���� � ���� ��� ������� ������ �� 14:00, ��������� ������ ����� ���������� �� ��������� ���� � ������� ���������: 11:00 - 15:00, 16:00 - 19:00, 19:00 - 22:00");
	        }

			$v = array('name' => '', 'automatic' => '');
			$sort = (isset($delivery['SORT']) ? $delivery['SORT'] : $delivery_key);

			$code = (isset($delivery['CODE']) ? $delivery['CODE'] : '');
			if ($converted && $code == '') {
				CDeliveryEDOST::GetAutomatic();
				if (isset(CDeliveryEDOST::$automatic[$delivery['ID']])) $code = CDeliveryEDOST::$automatic[$delivery['ID']]['code'];
			}
			if ($code != '') {
				$s = explode(':', $code);
				if (isset($s[1])) {
					$v['automatic'] = $s[0];
					$v['profile'] = $s[1];
				}
			}

			$v['id'] = ($converted || $v['automatic'] == '' ? $delivery['ID'] : $v['automatic']);

			if (!empty($delivery['OWN_NAME'])) $delivery['NAME'] = $delivery['OWN_NAME'];
			else if ($v['automatic'] == 'edost' && $converted) {
				CDeliveryEDOST::GetAutomatic();
				$delivery['NAME'] = CDeliveryEDOST::$automatic[$delivery['ID']]['name'];
			}
			$v['name_save'] = $delivery['NAME'];

			if ($v['automatic'] == 'edost') {
				$edost_enabled = true;
				$edost_bitrix_sort = $sort;

				$tariff = CDeliveryEDOST::GetEdostTariff($v['profile']);
				if (isset($active['profile']) && $active['profile'] == $v['profile'] && isset($tariff['format'])) $active['format'] = $tariff['format'];

				$v = array_merge($v, self::ParseName($delivery['NAME'], '', $delivery['DESCRIPTION'], $sign['insurance']));

				if (!isset($tariff['error'])) {
					$v['tariff_id'] = $v['ico'] = $tariff['id'];

					$ar = array('day', 'insurance', 'company_id', 'format', 'sort', 'city');
					foreach ($ar as $k) $v[$k] = $tariff[$k];

					if (in_array($v['format'], $office_key)) $office_get[$v['company_id']] = $v['company_id'];

					if ($config['template'] == 'Y' && $config['map'] == 'Y' && !empty($tariff['priceoffice'])) foreach ($tariff['priceoffice'] as $v2) {
						$ar = $v;
						$ar['to_office'] = $v2['type'];
						$ar += self::GetPrice('price', $v2['price'], $base_currency, $currency);
						$ar += self::GetPrice('pricetotal', $v2['price'] + $v2['priceinfo'], $base_currency, $currency);
						if ($v2['priceinfo'] > 0) $ar += self::GetPrice('priceinfo', $v2['priceinfo'], $base_currency, $currency);
						if ($v2['pricecash'] >= 0) {
							$ar += self::GetPrice('pricecod', $v2['pricecash'], $base_currency, $currency);
							$ar += self::GetPrice('pricecash', $v2['pricecash'], $base_currency, $currency);
						}
						$data[] = $ar;
					}

					$v += self::GetPrice('price', $tariff['price'], $base_currency, $currency);
					$v += self::GetPrice('pricetotal', $tariff['price'] + $tariff['priceinfo'], $base_currency, $currency);
					if ($tariff['priceinfo'] > 0) $v += self::GetPrice('priceinfo', $tariff['priceinfo'], $base_currency, $currency);
					if ($tariff['pricecash'] >= 0) {
						$v += self::GetPrice('pricecod', $tariff['pricecash'] + $tariff['transfer'], $base_currency, $currency);
						$v += self::GetPrice('pricecash', $tariff['pricecash'], $base_currency, $currency);
						$v += self::GetPrice('transfer', $tariff['transfer'], $base_currency, $currency);
					}
				}
				else {
					if (!$show_error) continue;

					$edost_error = true;
					$v['error'] = ($config['hide_error'] != 'Y' ? $tariff['error'] : '');
					$v['price'] = 0;
					$v['ico'] = 0;
				}
			}
			else {
				$v = array_merge($v, self::ParseName($delivery['NAME'], isset($delivery['company']) ? $delivery['company'] : '', $delivery['DESCRIPTION']));
				$v['bitrix_sort'] = $sort;
				if (!empty($delivery['LOGOTIP']['SRC'])) $v['ico'] = $delivery['LOGOTIP']['SRC'];

				if ($config['template'] == 'N') $v['price'] = (isset($delivery['PRICE']) ? floatval($delivery['PRICE']) : 0);
				else {
					$tariff = array();
					if ($converted) {
						$service = \Bitrix\Sale\Delivery\Services\Manager::getObjectById($delivery['ID']);

						$p = false;
						if (!$service->isProfile()) {
							$p = $service->getConfig();
							if (isset($p['MAIN']['ITEMS']['PERIOD']['ITEMS'])) {
								$p = $p['MAIN']['ITEMS']['PERIOD']['ITEMS'];
								$p = array('PERIOD_FROM' => $p['FROM']['VALUE'], 'PERIOD_TO' => $p['TO']['VALUE'], 'PERIOD_TYPE' => $p['TYPE']['VALUE']);
							}
						}

						if (isset($delivery['CALCULATE_ERRORS'])) $tariff = array('ERROR' => $delivery['CALCULATE_ERRORS']);
						else if (isset($delivery['PRICE'])) {
							$tariff += array('VALUE' => $delivery['PRICE']);
							if (isset($delivery['PERIOD_TEXT'])) $tariff += array('TRANSIT' => $delivery['PERIOD_TEXT']);
							if ($p !== false) $tariff += $p;
						}
						else {
							if ($shipment === false) $shipment = CSaleDelivery::convertOrderOldToNew($order);
							$ar = $service->calculate($shipment);

							if ($ar->isSuccess()) {
								$tariff += array('VALUE' => $ar->getPrice(), 'TRANSIT' => $ar->getPeriodDescription());
								if ($p !== false) $tariff += $p;
							}
							else {
								$s = $ar->getErrorMessages();
								if (empty($s)) $s = array($sing['delivery_error']);
								$tariff = array('ERROR' => implode('<br>', $s));
							}
						}
					}
					else if ($v['automatic'] != '') {
						$ar = CSaleDeliveryHandler::CalculateFull($v['automatic'], $v['profile'], $order, $currency);
						if ($ar['RESULT'] == 'OK') $tariff += array('VALUE' => $ar['VALUE'], 'TRANSIT' => $ar['TRANSIT']);
						else $tariff += array('ERROR' => isset($ar['TEXT']) ? $ar['TEXT'] : '');
					}
					else $tariff = array('VALUE' => $delivery['PRICE'], 'PERIOD_FROM' => $delivery['PERIOD_FROM'], 'PERIOD_TO' => $delivery['PERIOD_TO'], 'PERIOD_TYPE' => $delivery['PERIOD_TYPE'], 'CURRENCY' => $delivery['CURRENCY']);

					if (isset($tariff['VALUE'])) {
						$v += self::GetPrice('price', $tariff['VALUE'], isset($tariff['CURRENCY']) ? $tariff['CURRENCY'] : '', $currency);

						if (isset($tariff['PERIOD_TYPE'])) $v['day'] = self::GetDay($tariff['PERIOD_FROM'], $tariff['PERIOD_TO'], $tariff['PERIOD_TYPE']);
						else if (!empty($tariff['TRANSIT'])) {
							$s = $tariff['TRANSIT'];
							$s = explode('<a ', $s); // ������ boxberry ����������� ������ �� ����� ������� ������!
							$s = $s[0];
							$s = str_replace(array('�', $sign['to']), '-', $s); // ������ �������� ���� � ' �� '
							$s = preg_replace("/[^0-9-]/i", "", $s);
							$s = explode('-', $s);
							$v['day'] = self::GetDay($s[0], isset($s[1]) ? $s[1] : 0);
						}
					}
					else {
						if (!$show_error) continue;

						$v['error'] = (isset($tariff['ERROR']) ? $tariff['ERROR'] : '');
						$v['price'] = 0;
					}
				}
			}

			$data[] = $v;
		}
//		echo '<br><b>DELIVERY start:</b> <pre style="font-size: 12px">'.print_r($data, true).'</pre>';
//		echo '<br><b>automatic:</b> <pre style="font-size: 12px">'.print_r(CDeliveryEDOST::$automatic, true).'</pre>';

		// �������� ��� ����������� ������� ��������� �������
		foreach ($data as $k => $v) $data[$k]['cod_tariff'] = false;
		if ($config['template_cod'] == 'tr') {
			$ar = array();
			foreach ($data as $k => $v) if ($v['automatic'] == 'edost' && isset($v['pricecash'])) {
				$a = true;
				foreach ($ar as $k2 => $v2) if ($v2['tariff_id'] == $v['tariff_id'] && (!isset($v2['to_office']) && !isset($v['to_office']) || isset($v2['to_office']) && isset($v['to_office']) && $v2['to_office'] == $v['to_office'])) {
					$a = false;
					if ($v2['sort'] <= $v['sort']) $ar[$k2]['sort'] = $v['sort'] + 1;
				}
				if (!$a) continue;

				$v['cod_tariff'] = true;
				$v['sort']++;
				$v['price'] = $v['pricetotal'] = $v['pricecash'];
				$v['price_formatted'] = $v['pricetotal_formatted'] = $v['pricecash_formatted'];
				if ($v['insurance'] == 0 && !in_array($v['tariff_id'], CDeliveryEDOST::$tariff_shop)) $v['insurance'] = 1; // ������������ ���������
				if (!empty($v['transfer'])) $v['warning'] = str_replace('%transfer%', $v['transfer_formatted'], $sign['transfer']);

				$ar[] = $v;
			}
			if (!empty($ar)) $data = array_merge($data, $ar);
		}
// 				global $USER;
// if ($USER->IsAdmin()): 
// 		echo '<br><b>DELIVERY start + cod:</b> <pre style="font-size: 12px">'.print_r($data, true).'</pre>';
// endif;
		// �������� �������� ������, ���� ���� ������ ������� ��������
		if ($edost_error && $config['hide_error'] == 'Y' && count($data) > 1)
			foreach ($data as $k => $v) if ($v['automatic'] == 'edost') unset($data[$k]);

		// �������������� ����� �� ������� ����������
		if (isset($_SESSION['EDOST']['office_default']['profile'])) {
			$ar = $_SESSION['EDOST']['office_default']['profile'];
			foreach ($data as $k => $v)
				if ($v['automatic'] == 'edost' && $v['profile'] == $ar['profile'] && $v['cod_tariff'] == $ar['cod_tariff'] && !isset($_SESSION['EDOST']['office_default'][$v['format']]))
					$_SESSION['EDOST']['office_default'][$v['format']] = $ar;
			unset($_SESSION['EDOST']['office_default']['profile']);
		}


		// ��������� ���������� ����� ���������� + ������ ��� ������� ��������
		foreach ($data as $k => $v)
			if ($v['automatic'] == 'edost') $data[$k]['sort'] += $edost_bitrix_sort*1000;
			else if (isset($v['bitrix_sort'])) {
				$data[$k]['sort'] = ($v['bitrix_sort'] + ($v['bitrix_sort'] < $edost_bitrix_sort ? 0 : 1))*1000 + $k;
				$data[$k]['format'] = 'bitrix_'.($v['bitrix_sort'] < $edost_bitrix_sort ? 1 : 2);
			}

		// ����������
		if ($config['template'] != 'Y' || $config['template_format'] == 'off') $sorted = false;
		else {
			self::SortTariff($data, $config);
			$sorted = true;
		}


		// ������ �������
		$ar = array(
			'odt' => array('shop', 'office', 'terminal', 'door', 'house', 'post', 'general'),
			'dot' => array('door', 'house', 'shop', 'office', 'terminal', 'post', 'general'),
			'tod' => array('post', 'shop', 'office', 'terminal', 'door', 'house', 'general'),
		);
		$ar = (isset($ar[$config['template_format']]) ? $ar[$config['template_format']] : $ar['odt']);
		$ar = array_merge(array('bitrix_1'), $ar, array('bitrix_2'));
		$format = array_fill_keys($ar, '');
		$format_data = GetMessage('EDOST_DELIVERY_FORMAT');
		foreach ($format as $f_key => $f) {
			$f = (isset($format_data[$f_key]) ?  $format_data[$f_key] : array());
			if (!isset($f['name'])) $f['name'] = '';
			$f['data'] = array();
			$format[$f_key] = $f;
		};

		// ������������� ������� �� �������
		foreach ($data as $k => $v) {
			$f_key = ($config['template'] == 'Y' && !empty($v['format']) && isset($format[$v['format']]) ? $v['format'] : 'general');
			$format[$f_key]['data'][] = $v;
		}
//		echo '<br><b>FORMAT start:</b> <pre style="font-size: 12px">'.print_r($format, true).'</pre>';


		// ����������� �������� �������
		$hide = array();
		foreach ($sign['hide'] as $v) $hide[] = '- '.$v;
		$hide = array_merge($hide, $sign['hide']);
		foreach ($format as $f_key => $f) if (!empty($f['data'])) {
			// �������� �������� ������, ���� � ���� ������� �������� ���������� �������� (��� ����� ������ ����)
			if ($config['template'] == 'Y' && empty($config['NAME_NO_CHANGE'])) {
				$n = count($f['data']);
				for ($i = 0; $i < $n; $i++) if (!isset($f['data'][$i]['deleted'])) {
					$p = $p2 = 0;
					for ($i2 = $i+1; $i2 < $n; $i2++) if ($f['data'][$i]['company'] == $f['data'][$i2]['company']) {
						$p++;
						if ($f['data'][$i]['name'] == $f['data'][$i2]['name']) $p2++;
						$f['data'][$i2]['deleted'] = true;
					}
					if ($p == $p2) for ($i2 = $i; $i2 < $n; $i2++) if ($f['data'][$i]['company'] == $f['data'][$i2]['company']) $format[$f_key]['data'][$i2]['name'] = '';
				}
			}

			// �������� �� �������� ������ ������ '�������� �� �����', '�� ������ ������', ...
			if (empty($config['NAME_NO_CHANGE']) || in_array($f_key, array('office', 'terminal'))) {
//				if (in_array($f_key, array('door', 'office', 'terminal', 'house')))
				foreach ($format[$f_key]['data'] as $k => $v) if ($v['name'] != '' && in_array($v['format'], array('door', 'office', 'terminal', 'house'))) {
					$s = trim(str_replace($hide, '', $v['name']));
					if ($config['template'] == 'Y') $format[$f_key]['data'][$k]['name'] = $s;
				}
			}
		}
//		echo '<br><b>FORMAT name:</b> <pre style="font-size: 12px">'.print_r($format, true).'</pre>';

		// ��������� ������ PickPoint
		if ($config['map'] == 'Y' && !in_array($config['template'], array('Y', 'N3')) && (!defined('DELIVERY_EDOST_PICKPOINT_WIDGET') || DELIVERY_EDOST_PICKPOINT_WIDGET == 'Y')) $r['pickpoint_widget'] = $edost_order['pickpoint_widget'] = true;

		// �������� ������ � ������� edost
		$office = array();
		$office_error = false;
		if ($config['map'] == 'Y' || $config['template'] != 'Y') {
			$office = self::GetOffice($edost_order, $office_get);
			if (isset($office['error'])) $office_error = $office['error'];
			if (!empty($r['pickpoint_widget']) && !empty($office['pickpointmap'])) $r['pickpointmap'] = $office['pickpointmap'];
			$office = (!empty($office['data']) ? $office['data'] : array());
			if ($config['template'] != 'Y') $r['office'] = $office;
		}
//		echo '<br><b>office_get:</b> <pre style="font-size: 12px">'.print_r($office_get, true).'</pre>';
//		echo '<br><b>office:</b> <pre style="font-size: 12px">'.print_r($office, true).'</pre>';


		$active_id = (isset($active['id']) ? $active['id'] : '');
		$active_profile = (isset($active['profile']) ? $active['profile'] : '');
		$active_cod = (!empty($active['cod_tariff']) ? true : false);
		$active_bookmark = (!empty($active['bookmark']) ? $active['bookmark'] : '');

//		$_SESSION['EDOST']['office_default'] = $_SESSION['EDOST']['delivery_default'] = '';

		$ar = (isset($_SESSION['EDOST']['office_default']) ? $_SESSION['EDOST']['office_default'] : array());
		if (isset($active['format']) && !empty($active['office_id'])) {
			$ar[$active['format']] = $ar['all'] = array('id' => $active['office_id'], 'profile' => $active['profile'], 'cod_tariff' => $active_cod);
			if ($config['template_block_type'] != 'bookmark2' || $active_bookmark == 'show') $_SESSION['EDOST']['office_default'] = $ar;
		}
		$active_office = $ar;

		$active = false; // �������� �����

		// �������� �� ������������� ��������� ������ + ����������� 'type'
		foreach ($active_office as $k => $v) foreach ($office as $o) if (isset($o[$v['id']])) {
			$active_office[$k]['type'] = $o[$v['id']]['type'];
			break;
		}


		// �������� ������� ��� ������ ��� ������������ �������
		if ($config['template'] != 'Y') foreach ($format['general']['data'] as $k => $v) if (isset($v['format']) && in_array($v['format'], $office_key))
			if (($v['company_id'] != 26 || empty($r['pickpoint_widget'])) && empty($office[$v['company_id']]) || $v['company_id'] == 26 && !empty($r['pickpoint_widget']) && empty($r['pickpointmap'])) unset($format['general']['data'][$k]);

		// �������� ������� ��� ������ ��� ������� eDost + ��������� ��������� ������ (�������������)
		foreach ($format as $f_key => $f) if (in_array($f_key, $office_key) && !empty($f['data'])) {
			// ���������� ������ � ������� ������ (������� � ������������ �����, ����� ���������)
			$office_count = array();
			$office_count_total = 0;
			for ($i = 0; $i <= 1; $i++) foreach ($f['data'] as $k => $v) {
				$id = $v['company_id'];
				if (!isset($office_count[$id])) {
					$office_count[$id]['total'] = (isset($office[$id]) ? count($office[$id]) : 0);
					$office_count_total += $office_count[$id]['total'];
				}

				if ($i == 0 && isset($v['to_office'])) {
					$n = 0;
					if (isset($office[$id])) foreach ($office[$id] as $o) if ($o['type'] == $v['to_office']) $n++;
					$f['data'][$k]['office_count'] = $n;
					$office_count[$id][$v['to_office']] = $n;

					// ��������� ��������� ������ (�������������)
					if ($n > 0 && isset($active_office[$f_key]['type']) && $v['profile'] == $active_office[$f_key]['profile'] && $v['cod_tariff'] == $active_office[$f_key]['cod_tariff'] && $v['to_office'] == $active_office[$f_key]['type']) {
						if (self::GetBitrixID($v) == $active_id) {
							$f['data'][$k]['checked'] = true;
							$active = $v;
						}
						$active_office[$f_key]['tariff_key'] = $k;
					}
				}
				else if ($i == 1 && !isset($v['to_office'])) {
					$n = $office_count[$id]['total'];
					foreach ($office_count[$id] as $k2 => $v2) if ($k2 !== 'total') $n -= $v2;
					$f['data'][$k]['office_count'] = $n;
				}
			}

			foreach ($f['data'] as $k => $v) if ($v['office_count'] == 0) unset($f['data'][$k]);
			if ($office_count_total > 0) $f['office_count'] = $office_count_total;

			$format[$f_key] = $f;
		}
//		echo '<br><b>format:</b> <pre style="font-size: 12px">'.print_r($format, true).'</pre>';

		// ��������� ��������� ������ (�� �������������)
		foreach ($format as $f_key => $f) foreach ($f['data'] as $k => $v) if (!isset($v['to_office'])) {
			if ($active === false && self::GetBitrixID($v) == $active_id && ($v['automatic'] !== 'edost' || $v['cod_tariff'] == $active_cod)) {
				$format[$f_key]['data'][$k]['checked'] = true;
				$active = $v;
			}
			if (isset($active_office[$f_key]['type']) && !isset($active_office[$f_key]['tariff_key']) && $v['profile'] == $active_office[$f_key]['profile'] && $v['cod_tariff'] == $active_office[$f_key]['cod_tariff']) $active_office[$f_key]['tariff_key'] = $k;
		}
//		echo '<br><b>active:</b> <pre style="font-size: 12px">'.print_r($active, true).'</pre>';
//		echo '<br><b>active_office:</b> <pre style="font-size: 12px">'.print_r($active_office, true).'</pre>';


		// �������� �� ������� 'priceinfo'
		$priceinfo = false;
		foreach ($format as $f_key => $f) if (!empty($f['data']))
			foreach ($f['data'] as $k => $v) if (isset($v['priceinfo'])) $priceinfo = true;


		// ������ ��� �����
		if ($config['map'] == 'Y' && !empty($office)) {
			$point = array();
			foreach ($office as $k => $v) $point[] = '{"company_id": "'.$k.'", "data": '.self::GetJson($v, array('id', 'name', 'address', 'schedule', 'gps', 'type', 'metro', 'codmax')).'}';
			$tariff = array();
			foreach ($format as $f_key => $f) if (!empty($f['data']) && (isset($f['office_count']) || $f_key == 'general')) {
				self::FormatInsurance($f); // �������� '�� ����������', ���� � ������ ��� ������ �� ����������
				if (!$sorted) self::SortTariff($f['data'], $config);
				foreach ($f['data'] as $k => $v) if (isset($v['format']) && in_array($v['format'], $office_key)) {
					if ($f_key == 'general') {
						$v['name'] = '';
						$v['insurance'] = '';
					}
					if ($config['template_cod'] == 'tr') $v['cod_tariff'] = ($v['cod_tariff'] ? 'Y' : 'N'); else $v['cod_tariff'] = '';
					if ($converted) $v['profile'] = $v['profile'].'_'.$v['id'];
					$v['price'] = $v['pricetotal'];
					$v['price_formatted'] = $v['pricetotal_formatted'];
					if (isset($v['pricecod'])) $v += self::GetPrice('codplus', $v['pricecod'] - $v['price'], '', $currency); // �� ����� ��������� ������ ������� �� ������� 'codplus'
					$v['company'] = self::RenameTariff($v['company'], $rename['company']);
					$tariff[] = $v;
				}
			}
			$r['map_json'] =
				'"point": ['.implode(', ', $point).'], '.
				'"tariff": '.self::GetJson($tariff, array('profile', 'company', 'name', 'tariff_id', 'price', 'price_formatted', 'pricecash', 'codplus', 'codplus_formatted', 'day', 'insurance', 'to_office', 'company_id', 'format', 'cod_tariff'));
		}


		// �������� ������ � ������� � ���� ����� (������������� ��� � ������� �� �����)
		$tariff_count = $office_count = 0;
		$ico = false;
		$f2 = $format['office'];
		$f2['data'] = array();
		$f2['office_count'] = 0;
		$f2['head'] = $sign['bookmark']['office'];
		foreach ($format as $f_key => $f) if (isset($f['office_count'])) {
			$n = count($f['data']);
			$tariff_count += $n;
			$office_count += $f['office_count'];

			// ������� ��������� ������
			$checked = false;
			foreach ($f['data'] as $v) if (isset($v['checked'])) { $checked = true; break; }

			self::FormatRange($f, $currency, $config['template_cod'] != 'off' ? true : false);
			if ($ico === false && !empty($f['ico'])) $ico = $f['ico'];

            // ��������� ������ ����� ��������������� ����� �� ��� ���������� �� ������
			if ($config['template_map_inside'] == 'Y' && empty($active_office['all']) && isset($active_office[$f_key]['tariff_key'])) $active_office['all'] = $active_office[$f_key];

			// ��������� ������������� ����� (��� ������ �������, ���� �������� � ���������� ������ 'template_autoselect_office' ��� 'bookmark2')
			if (!isset($active_office[$f_key]['tariff_key']) && ($n == 1 && $f['office_count'] == 1 || $config['template_autoselect_office'] == 'Y') || $config['template_block_type'] == 'bookmark2' && $active_bookmark != 'show') {
				$k = $f['min']['key'];
				$v = $f['data'][$k];
				$id = false;
				if (isset($v['to_office'])) {
					foreach ($office[$v['company_id']] as $o) if ($o['type'] == $v['to_office']) { $id = $o['id']; break; }
				}
				else foreach ($office[$v['company_id']] as $o) {
					$a = true;
					foreach ($f['data'] as $k2 => $v2) if ($k2 !== $k && $v2['company_id'] == $v['company_id'] && isset($v2['to_office']) && $v2['to_office'] == $o['type']) $a = false;
					if ($a) { $id = $o['id']; break; }
				}
				$active_office[$f_key] = array('id' => $id, 'profile' => $v['profile'], 'cod_tariff' => $v['cod_tariff'], 'type' => $office[$v['company_id']][$id]['type'], 'tariff_key' => $k);
			}

			if (isset($active_office[$f_key]['tariff_key'])) {
				$p = $active_office[$f_key];
				$v = $f['data'][$p['tariff_key']];
				$o = $office[$v['company_id']][$p['id']];

				if ($f['office_count'] != 1 || $n != 1) {
					$v['office_map'] = 'change';
					$v['office_link'] = $sign['change'];
					foreach ($f['data'] as $v2) { $v['sort'] = $v2['sort']; break; }
				}
				else $v['office_link'] = $sign['map'];

				$v['office_mode'] = $f_key;
				$v['office_id'] = $o['id'];
				$v['office_type'] = $o['type'];
				$v['office_address'] = self::GetOfficeAddress($o);
				$v['office_address_full'] = self::GetOfficeAddress($o, $v);

				// ���������� ����������� �������, ���� ��������� ����������� ���������� ����� �������� ��� ���������� �����
				if (isset($v['pricecash']) && isset($o['codmax']) && $v['pricecash'] > $o['codmax']) {
					$ar = array('pricecash', 'pricecash_formatted', 'pricecod', 'pricecod_formatted');
					foreach ($ar as $v2) unset($v[$v2]);
				}

				// ��������� ������, ���������� ����������� ��� 'template_map_inside' + ���������� ���������� �����
				if ($config['template_map_inside'] == 'Y' && !empty($active_office['all']['id']) && $active_office['all']['id'] == $p['id']) {
					$v['checked_inside'] = true;
					$config['template_map_inside'] = 'tariff';
				}

				if (in_array($config['template_map_inside'], array('Y', 'tariff')) && isset($v['checked']) && empty($v['checked_inside'])) unset($v['checked']);

				if (isset($v['checked'])) $active = $v;
				else if ($checked) {
					$active_id = '';
					$active = false;
				}
			}
			else {
				$sort = 0;
				$company_id = false;
				foreach ($f['data'] as $k => $v) {
					if ($sort == 0) $sort = $v['sort'];

					if ($company_id === false) {
						$company_id = $v['company_id'];
						$tariff = $v;
					}
					else if ($v['company_id'] != $company_id) {
						$company_id = false;
						break;
					}
				}

				if ($company_id == 26) $office_link = $sign['postamat']['format_get'];
				else if ($company_id == 72) $office_link = $sign['pochtomat']['format_get'];
				else $office_link = $f['get'];

				$v = array(
					'id' => (!$converted ? 'edost' : ''),
					'automatic' => 'edost',
					'profile' => $f_key,
					'company' => (!empty($company_id) ? $tariff['company'] : ''),
					'name' => '',
					'description' => '',
					'ico' => (!empty($company_id) ? $tariff['ico'] : 35),
					'company_id' => (!empty($company_id) ? $company_id : ''),
					'format' => $f_key,
					'sort' => $sort,
					'price' => $f['price']['max']['value'],
					'price_formatted' => self::GetRange($f['price']),
					'price_long' => ($f['price']['min']['value'] == $f['price']['max']['value'] ? 'normal' : 'light'),
					'day' => '',
					'office_map' => 'get',
					'office_mode' => $f_key,
					'office_link' => $office_link,
					'office_count' => 0,
					'office_address_full' => '',
					'cod_tariff' => false,
				);
				if ($f['pricecod']['max']['value'] >= 0) {
					$v['pricecod'] = $f['pricecod']['max']['value'];
					$v['pricecod_formatted'] = self::GetRange($f['pricecod']);
				}

				if ($checked) {
					$active_id = '';
					$active = false;
				}

				if ($active_profile === $f_key) {
					$v['checked'] = true;
					$active = $v;
				}
			}

			self::FormatHead($v, $f['name']);
			$v['pricehead'] = $f['pricehead'];
			$v['dayhead'] = $f['day'];

			if (!isset($f2['min']) || $f['min']['price'] < $f2['min']['price']) {
				$f2['min'] = $f['min'];
				$f2['min']['key'] = count($f2['data']);
			}

			$f2['data'][] = $v;
			$f2['office_count'] += $f['office_count'];
			$format[$f_key]['data'] = array();
		}

		if ($config['template_map_inside'] == 'tariff' && !empty($f2['data'])) {
			if ($tariff_count > 1 || $office_count > 1) foreach ($f2['data'] as $k => $v) {
				// ���������� '������� ������...' ��� ���� �������
				$v['office_map'] = 'change';
				$v['office_link'] = $sign['change'];
				$f2['data'][$k]	= $v;
			}
		}
		if ($config['template_map_inside'] == 'Y') {
			if ($tariff_count == 1 && $office_count == 1) {
				// ��������� ������, ����� ��� ������ + ���������� ���������� �����
				foreach ($f2['data'] as $k => $v) $f2['data'][$k]['checked_inside'] = true;
				$config['template_map_inside'] = 'tariff';
			}
			else {
				// ����� ���������� �����, ���� ������� ��������������� �����
				foreach ($f2['data'] as $k => $v) if (isset($v['office_id'])) {
					unset($v['office_id']);
					$v['profile'] = $v['office_mode'];
					$v['id'] = $v['office_address_full'] = '';
					$f2['data'][$k] = $v;
				}
			}
		}

		// ������������ ��������� ��� ��� ��������� ������
		$pricehead = $day = false;
		foreach ($f2['data'] as $k => $v) {
			$pricehead = self::AddRange($pricehead, $v['pricehead']);
			$day = self::AddRange($day, $v['dayhead']);
			unset($f2['data'][$k]['pricehead']);
			unset($f2['data'][$k]['dayhead']);
		}
		$f2['pricehead'] = $pricehead;
		$f2['day'] = $day;
		if ($ico !== false) $f2['ico'] = $ico;

		$format['office'] = $f2;


		// ����������� ����� � ����� ������ 'general'
		$count_format = 0;
		$count_tariff = 0;
		$count_edost = 0;
		$count_bitrix = 0;
		$auto = false;
		if ($config['template_block'] == 'auto2') {
			$n = ($bookmark ? 1 : 2);
			foreach ($format as $f_key => $f) if (!in_array($f_key, array('general', 'bitrix_1', 'bitrix_2')) && count($f['data']) > $n) $auto = true;
		}
		foreach ($format as $f_key => $f) if (!empty($f['data'])) {
			$count_format++;
			$count_tariff += count($f['data']);
		}
		foreach ($format as $f_key => $f) if (!empty($f['data'])) {
			if ($f_key == 'general') {
				$format[$f_key]['pack'] = 'normal';
				$count_edost++;
			}
			else if (in_array($f_key, array('bitrix_1', 'bitrix_2'))) {
				$format[$f_key]['pack'] = 'normal';
				$count_bitrix++;
			}
			else if ($count_format == 1 && $count_tariff <= 2 || $config['template_format'] == 'off' || $config['template_block'] == 'off' || ($config['template_block'] == 'auto1' && count($f['data']) <= 2) || ($config['template_block'] == 'auto2' && !$auto)) {
				$format[$f_key]['pack'] = 'head';
				$count_edost++;
			}
		}
		$bitrix = false;
		if ($config['template_block'] == 'off' || $count_format == 1 && !($config['template_map_inside'] == 'Y' && !empty($format['office']['data'])) || $count_edost > 1 || $count_bitrix > 0) {
			$f2 = $format['general'];
			$f2['data'] = array();
			foreach ($format as $f_key => $f) if (isset($f['pack'])) {
				if ($f['pack'] == 'normal') $data = $f['data'];
				else if ($f['pack'] == 'head') {
					$data = array();
					foreach ($f['data'] as $k => $v) {
						self::FormatHead($v, $f['name']);
						$data[] = $v;
					}
				}

				if (count($f2['data']) != 0 && $config['template_format'] != 'off' && !($f_key == 'bitrix_2' && $bitrix)) $f2['data'][] = array('delimiter' => true);
				$f2['data'] = array_merge($f2['data'], $data);
				$format[$f_key]['data'] = array();

				$bitrix = ($f_key == 'bitrix_1' ? true : false);
			}
			$format['general'] = $f2;
		}
//		echo '<br><b>format:</b> <pre style="font-size: 12px">'.print_r($format, true).'</pre>';


		// ������� ����������� ������� � ������
		$cod = false;
		if ($config['template_cod'] != 'off') foreach ($format as $f_key => $f) foreach ($f['data'] as $v) if (isset($v['pricecod'])) {
			$format[$f_key]['cod'] = true;
			$cod = true;
			break;
		}


		// ������� �������������� ��� ����� "�� ��������"
		if (!empty($format['house']['data'])) {
			$f = $format['house'];

			$count = count($f['data']);
			$count_priceinfo = 0;
			foreach ($f['data'] as $v) if (isset($v['priceinfo'])) $count_priceinfo++;

			$p = -1;
			foreach ($f['data'] as $v) {
				if (!isset($v['priceinfo'])) $p = -1;
				else if ($p < 0) {
					$p = $v['price'];
					$p_formatted = $v['price_formatted'];
				}
				else if ($p != $v['price']) $p = -1;
				if ($p < 0) break;
			}

			// ����� �������������� � ���������
			$f['warning'] = $sign['house_warning'];
			if ($count == $count_priceinfo) {
				$f['warning'] .= ($f['warning'] != '' ? '<br>' : '').$sign['priceinfo_warning'];
				if ($p > 0) $f['description'] = str_replace('%price%', $p_formatted, $sign['priceinfo_description']);
			}

			// �������������� � �������
			foreach ($f['data'] as $k => $v) if (isset($v['priceinfo'])) {
				if ($count != $count_priceinfo) $v['warning'] = $sign['priceinfo_warning'];
				if ($p < 0 && $v['price'] > 0) $v['description'] = str_replace('%price%', $v['price_formatted'], $sign['priceinfo_description']).($v['description'] != '' ? '<br>' : '').$v['description'];
				$f['data'][$k] = $v;
			}

			$format['house'] = $f;
		}


		// ����������
		if (!$sorted) self::SortTariff($format['general']['data'], $config);


		// ���������� �������� ������ (���� ��� ������ ������� ��� ���� ������ �������� ������)
		if ($config['hide_error'] != 'Y' || $config['show_zero_tariff'] == 'Y') {
			$count = 0;
			$count_edost = 0;
			foreach ($format as $f_key => $f) foreach ($f['data'] as $v) if (isset($v['id'])) {
				$count++;
				if ($v['automatic'] == 'edost') $count_edost++;
			}
			if ($count == 0 && (!empty($bitrix_data) || !empty($config['ADD_ZERO_TARIFF'])) || $config['hide_error'] != 'Y' && ($office_error !== false || $edost_enabled && $count_edost == 0)) {
				$error = '';
				if ($config['hide_error'] != 'Y')
					if ($office_error !== false) $error = CDeliveryEDOST::GetEdostError($office_error, 'office');
					else if (!empty($config['ADD_ZERO_TARIFF'])) $error = CDeliveryEDOST::GetEdostError(!empty(CDeliveryEDOST::$result['error']) ? CDeliveryEDOST::$result['error'] : 0);
					else if ($edost_enabled && $count_edost == 0) $error = CDeliveryEDOST::GetEdostError(0);

				$tariff = false;
				if ($converted) {
					CDeliveryEDOST::GetAutomatic();
					foreach (CDeliveryEDOST::$automatic as $v) if ($v['code'] == 'edost:0') { $tariff = $v; break; }
				}
				else {
					$ar = CSaleDeliveryHandler::GetBySID('edost');
					$v = $ar->GetNext();
					$tariff = array('id' => 'edost:0', 'name' => isset($v['PROFILES'][0]['TITLE']) ? $v['PROFILES'][0]['TITLE'] : '', 'description' => isset($v['PROFILES'][0]['DESCRIPTION']) ? $v['PROFILES'][0]['DESCRIPTION'] : '');
				}
				if ($tariff !== false) {
					$v = array(
						'id' => $tariff['id'],
						'automatic' => 'edost',
						'profile' => 0,
						'name' => '',
						'company' => $tariff['name'],
						'description' => $tariff['description'],
						'error' => $error,
						'price' => 0,
						'ico' => 0,
						'cod_tariff' => false,
					);
					if ($tariff['id'] == $active_id) {
						$active = $v;
						$v['checked'] = true;
					}
					$format['general']['data'][] = $v;
				}
			}
		}


		// �������������� ��������� ��� ��������� + ����� ������ �������� ������ � ������
		foreach ($format as $f_key => $f) if (!empty($f['data']) && !isset($f['pricehead'])) {
			self::FormatRange($f, $currency, $config['template_cod'] != 'off' ? true : false);
			$format[$f_key] = $f;
		}
//		echo '<br><b>format:</b> <pre style="font-size: 12px">'.print_r($format, true).'</pre>';


		// ����� ��������� ��������, ���� ������ ���������� + ����� ��������� ������, ���� ������� ������ ������
		if ($config['template_block_type'] == 'bookmark1' && $active_bookmark != '') {
			if (empty($format[$active_bookmark]['data'])) $active_bookmark = '';
			else if ($active !== false) foreach ($format as $f_key => $f) if (!empty($f['data']) && $f_key != $active_bookmark) {
				foreach ($f['data'] as $k => $v) if (isset($v['checked'])) {
					unset($format[$f_key]['data'][$k]['checked']);
					$active_id = '';
					$active = false;
					break;
				}
				if ($active === false) break;
			}
		}

		// ����� ��������� ��������, ���� ������ ���������� + ��������� �������� "������..."
		if ($config['template_block_type'] == 'bookmark2') {
			$bookmark_show = false;
			foreach ($format as $f_key => $f) if (!empty($f['data']) && (count($f['data']) > 1 || isset($f['office_count']) && $f['office_count'] > 1 || $f_key == 'general')) { $bookmark_show = true; break; }
			if ($active_bookmark != '' && $active_bookmark != 'show' && empty($format[$active_bookmark]['data']) || $active_bookmark == 'show' && !$bookmark_show) $active_bookmark = '';
		}


		// ��������� ����������, ���� �������� ������ ���� �����
		if ($active === false && $config['autoselect'] != 'Y') {
			$count_all = 0;
			foreach ($format as $f_key => $f) if (!empty($f['data'])) {
				$count = 0;
				foreach ($f['data'] as $k => $v) if (isset($v['id'])) $count++;
				$count_all += $count;
				if ($config['template_block_type'] == 'bookmark1' && $f_key == $active_bookmark && $count == 1) $config['autoselect'] = 'Y';
			}
			if ($count_all == 1) $config['autoselect'] = 'Y';
		}


		// ����� ������ ��������, ���� ������ �� �������
		$key = false;
		if ($active === false && $config['template_block_type'] == 'bookmark2') {
			if ($active_bookmark == '' && $config['autoselect'] == 'Y') foreach ($format as $f_key => $f) if (!empty($f['data']) && $f_key != 'general') { $active_bookmark = $f_key; break; }
			if (!empty($format[$active_bookmark]['data'])) $key = array($active_bookmark, $format[$active_bookmark]['min']['key']);
		}
		if ($active === false && $key === false && $config['autoselect'] == 'Y') {
			$i = false;
			if ($config['template_block_type'] == 'bookmark1' && !empty($format[$active_bookmark]['data'])) $i = $active_bookmark;
			else foreach ($format as $f_key => $f) if (!empty($f['data'])) { $i = $f_key; break; }
			if ($i !== false) foreach ($format[$i]['data'] as $k => $v) if (isset($v['id']) && ($config['template_map_inside'] != 'tariff' || !isset($v['office_mode']) || !empty($v['checked_inside']))) { $key = array($i, $k); break; }
		}
		if ($key !== false) {
			$active = $format[$key[0]]['data'][$key[1]];
			$active_id = self::GetBitrixID($active);
			$format[$key[0]]['data'][$key[1]]['checked'] = true;
		}


		// �������� ����� ������� � ���� ����� ������
		$data = array();
		$day = false;
		$count_tariff = 0;
		$count_bookmark = 0;
		$count_bookmark_cod = 0;
		if ($bookmark) foreach ($format as $f_key => $f) if (!empty($f['data']) && $f_key != 'general') {
			$count_bookmark++;
			if ($config['template_block_type'] == 'bookmark1' && isset($f['cod']) || isset($f['min']['pricecash'])) $count_bookmark_cod++;
		}
		foreach ($format as $f_key => $f) if (!empty($f['data'])) {
			if ($f_key == 'general' && count($data) == 0) $head = '';
			else if ($count_bookmark > 1 && ($config['template_block_type'] != 'bookmark2' || $f_key != 'general')) $head = (isset($sign['bookmark'][$f_key]) ? $sign['bookmark'][$f_key] : '');
			else $head = (isset($f['head']) ? $f['head'] : $f['name']);

			$insurance = (!in_array($f_key, array('office', 'general')) && self::FormatInsurance($f) ? $sign['insurance_head'] : ''); // ����� ������� "��������� �������� �� ��� ������"

			$ar = array();
			foreach ($f['data'] as $k => $v) {
				if (isset($v['id'])) {
					$count_tariff++;
					if (!empty($v['checked']) && $active_bookmark == '') $active_bookmark = $f_key;
					$v['html_id'] = self::GetHtmlID($v);
					$v['html_value'] = self::GetHtmlValue($v);
					if ($config['template'] == 'Y') $v['name'] = self::RenameTariff($v['name'], $rename['name']);
					$v['insurance'] = (isset($v['insurance']) && $v['insurance'] == 1 ? $sign['insurance'] : '');
					if (!isset($v['priceinfo']) && $v['price'] == 0 && !isset($v['error'])) $v['free'] = $sign['free'];
					if (isset($v['pricecod']) && $v['pricecod'] == 0) $v['cod_free'] = $sign['free'];
					if (!empty($v['day'])) $day = true;
				}
				$ar[] = $v;
			}

			$data[$f_key] = array(
				'head' => $head,
				'cod' => (isset($f['cod']) ? true : false),
				'description' => (isset($f['description']) ? $f['description'] : ''),
				'warning' => (isset($f['warning']) ? $f['warning'] : ''),
				'insurance' => $insurance,
				'tariff' => $ar,
			);
			if ($config['template_block_type'] == 'bookmark1') {
				if ($f['pricehead']['min']['value'] == -1) $data[$f_key]['price_formatted'] = '';
				else {
					$data[$f_key]['price_formatted'] = self::GetRange($f['pricehead']);
					if (empty($data[$f_key]['price_formatted'])) $data[$f_key]['free'] = $sign['free'];

					// ����������� ������� ��� �������� �������
					if (empty($f['pricehead']['min']['value'])) $data[$f_key]['short']['free'] = $sign['free'];
					else  $data[$f_key]['short']['price_formatted'] = ($f['pricehead']['min']['value'] != $f['pricehead']['max']['value'] ? $sign['from'] : '') . $f['pricehead']['min']['formatted'];
				}

				if (!empty($f['day'])) $data[$f_key]['short']['day'] = self::GetDay(round(($f['day']['min']['value'] + $f['day']['max']['value'])/2));
				if (!empty($f['ico'])) $data[$f_key]['short']['ico'] = $f['ico'];
			}
			if ($config['template_block_type'] == 'bookmark2') {
				if ($f['min']['price'] == 0) $f['min']['free'] = $sign['free'];
				$data[$f_key]['min'] = $f['min'];
			}
		}
		if ($config['template_block_type'] == 'bookmark2' && $count_bookmark > 1 && $bookmark_show) $data['show'] = array('head' => $sign['bookmark']['show']); // ���������� ������ 'show' (�������� ��� ������)

		$r['data'] = $data;
		$r['count'] = $count_tariff;
		$r['cod'] = ($count_tariff == 1 || $config['template_cod'] != 'td' ? false : $cod); // ���� ������ � ���������� �������� � ������� ����� � ��������� �������
		$r['cod_bookmark'] = ($config['template_cod'] != 'off' && $count_bookmark > 1 && $count_bookmark != $count_bookmark_cod ? true : false); // ����������� � ��������� "+ �������� ������ ��� ���������"
		$r['cod_tariff'] = ($config['template_cod'] == 'tr' ? true : false); // ������� ����� ����������� ������� ��������� �������
		$r['priceinfo'] = $priceinfo; // ���� ������ � ���������������
		$r['day'] = $day; // ���� ������ �� ������ ��������
		$r['border'] = ($config['template_block_type'] == 'border' && count($data) > 1 ? true : false); // ���� � ��������
		$r['warning'] = CDeliveryEDOST::GetEdostWarning();
		if ($count_bookmark > 1) $r['bookmark'] = ($config['template_block_type'] == 'bookmark1' ? 1 : 2); // �������� �������� ��� ������� ������
		if (in_array($config['template_map_inside'], array('Y', 'tariff'))) $r['map_inside'] = $config['template_map_inside'];

		$r['active'] = array(
			'id' => (!empty($active) ? $active_id : ''),
			'automatic' => (isset($active['automatic']) ? $active['automatic'] : ''),
			'profile' => (isset($active['profile']) ? $active['profile'] : ''),
			'cod' => (isset($active['pricecash']) ? true : false),
			'cod_tariff' => (!empty($active['cod_tariff']) ? true : false),
			'bookmark' => $active_bookmark,
			'name' => (isset($active['name_save']) ? $active['name_save'] : ''),
		);
		if (isset($active['office_type'])) $r['active']['office_type'] = $active['office_type'];
		if (isset($active['office_id'])) $r['active']['office_id'] = $active['office_id'];
		if (isset($active['office_address_full'])) $r['active']['address'] = $active['office_address_full'];

//		echo '<br><b>FORMAT RESULT:</b> <pre style="font-size: 12px">'.print_r($r, true).'</pre>';

		return $r;

	}


	// ���� ��� ������ � ������ �� ����������, ����� �������� 'insurance' ��������� � ������������ true
	public static function FormatInsurance(&$f) {

		$n = count($f['data']);
		if ($n <= 1) return false;

		$i = 0;
		foreach ($f['data'] as $v) if (!empty($v['insurance'])) $i++;

		if ($i != $n) return false;
		else {
			foreach ($f['data'] as $k => $v) unset($f['data'][$k]['insurance']);
			return true;
		}

	}


	// �������� � json �� �������� ������
	public static function GetJson($data, $key, $array = true, $pack = true) {

		if (!$array) $data = array($data);
		else if (!is_array($data) || count($data) == 0) return '[]';

		$s = array();
		foreach ($data as $v) {
			$s2 = array();
			if ($pack) {
				foreach ($key as $v2) $s2[] = (isset($v[$v2]) ? str_replace(array('"', "'"), array('', ''), $v[$v2]) : '');
				$s[] = '"'.implode('|', $s2).'"';
			}
            else {
				foreach ($v as $k2 => $v2) if (in_array($k2, $key)) $s2[] = '"'.$k2.'": "'.str_replace(array('"', "'", "\t"), array('\"', "\'", ' '), $v2).'"';
				$s[] = '{'.implode(', ', $s2).'}';
			}
		}

		if (!$array) return $s[0];
		else return '['.implode(', ', $s).']';

	}


	// ������ ������������ ������� (1,2,... : 3,4,... : ...)
	public static function ParseArray($array, $id, &$data) {

		if (in_array($id, array('field', 'control'))) $array = $GLOBALS['APPLICATION']->ConvertCharset(substr($array, 0, 10000), 'windows-1251', LANG_CHARSET);
		else $array = preg_replace("/[^0-9.:,-]/i", "", substr($array, 0, 1000));
		if ($array == '') return;

		if ($id == 'priceoffice') $key = array('type', 'price', 'priceinfo', 'pricecash');
		else if ($id == 'limit') $key = array('company_id', 'type', 'weight_from', 'weight_to', 'price', 'size1', 'size2', 'size3', 'sizesum');
		else if ($id == 'field') $key = array('name', 'value');
		else if ($id == 'control') $key = array('id', 'count', 'site');
		else return;

		$key_count = count($key);
		$default = array_fill_keys($key, 0);
		if ($id == 'priceoffice') $default['pricecash'] = -1;

		$r = array();
		$array = explode(':', $array);
		foreach ($array as $v) {
			$v = explode(',', $v);
			if ($v[0] == '' || !isset($v[1])) continue;

			$ar = $default;
			foreach ($v as $k2 => $v2) if ($k2 < $key_count && $v2 !== '') $ar[$key[$k2]] = str_replace(array('%c', '%t'), array(',', ':'), $v2);
			if (in_array($id, array('priceoffice', 'control'))) $r[$v[0]] = $ar; else $r[] = $ar;
		}
		if (!empty($r)) $data[$id] = $r;

	}

	// ������ ������ �������
	public static function ParseData($data, $type = 'delivery') {

		if ($type == 'delivery') $key = array('id', 'price', 'priceinfo', 'pricecash', 'priceoffice', 'transfer', 'day', 'insurance', 'company', 'name', 'format', 'company_id', 'city');
		else if ($type == 'document') $key = array('id', 'data', 'data2', 'name', 'size', 'quantity', 'mode', 'cod', 'delivery', 'length', 'space');
		else if ($type == 'office') $key = array('id', 'code', 'name', 'address', 'address2', 'tel', 'schedule', 'gps', 'type', 'metro');
		else if ($type == 'location') $key = array('city', 'region', 'country');
		else if ($type == 'location_street') $key = array('street', 'zip', 'city');
		else if ($type == 'location_zip') $key = array('zip');
		else if ($type == 'location_robot') $key = array('ip_from', 'ip_to');
		else if ($type == 'control') $key = array('id', 'flag', 'tariff', 'tracking_code', 'status', 'status_warning', 'status_string', 'status_info', 'status_date', 'status_time', 'day_arrival', 'day_delay', 'day_office');
		else if ($type == 'detail') $key = array('status', 'status_warning', 'status_string', 'status_info', 'status_date', 'status_time');
		else if ($type == 'tracking') $key = array('id', 'tariff', 'example', 'format');
		else if ($type == 'param') $key = array();
		else return array('error' => 4);

		$r = array();
		$key_count = count($key);
		$data = explode('|', $data);

		// ����� ���������: error=2;warning=1;sizetocm=1;...
		$p = explode(';', $data[0]);
		foreach ($p as $v) {
			$s = explode('=', $v);
			$s[0] = preg_replace("/[^0-9_a-z]/i", "", substr($s[0], 0, 20));
			if (isset($s[1]) && $s[0] != '')
				if ($s[0] == 'limit') self::ParseArray($s[1], 'limit', $r);
				else if ($s[0] == 'field') self::ParseArray($s[1], 'field', $r);
				else if ($s[0] == 'control') self::ParseArray($s[1], 'control', $r);
				else if ($s[0] == 'warning') $r[$s[0]] = explode(':', $s[1]);
				else $r[$s[0]] = $s[1];
		}

		if (isset($r['error']) || $key_count == 0) return $r;

		$r['data'] = array();
		$array_id = '';
		$sort = 0;
		foreach ($data as $k => $v) if ($k == 0 || $v == 'end') {
			if ($k != 0 && isset($parse[$key[0]]) && ($key_count == 1 || isset($parse[$key[1]]))) {
				$sort++;
				if ($type == 'delivery') {
					$profile = $parse['id']*2 + ($parse['insurance'] == 1 ? 0 : -1);
					$parse['profile'] = $profile;
					$parse['sort'] = $sort*2;
					if ($profile > 0) $r['data'][$profile] = $parse;
				}
				else if ($array_id !== '') $r['data'][$array_id][$parse['id']] = $parse;
				else if (isset($parse['id'])) $r['data'][$parse['id']] = $parse;
				else $r['data'][] = $parse;
			}
			$i = 0;
			$parse = array();
		}
		else if ($v === 'key') $array_id = 'get';
		else if ($array_id === 'get') $array_id = $v;
		else if ($i < $key_count) {
			$p = $key[$i];
			$i++;

			if ($type == 'delivery') {
				if (in_array($p, array('day', 'company', 'name'))) $v = $GLOBALS['APPLICATION']->ConvertCharset(substr($v, 0, 80), 'windows-1251', LANG_CHARSET);
				else if (in_array($p, array('price', 'priceinfo', 'pricecash', 'transfer'))) {
					$v = preg_replace("/[^0-9.-]/i", "", substr($v, 0, 11));
					if ($v === '') $v = ($p == 'pricecash' ? -1 : 0);
				}
				else if (in_array($p, array('id', 'insurance'))) $v = intval($v);
				else if ($p == 'company_id') $v = preg_replace("/[^a-z0-9]/i", "", substr($v, 0, 3));
				else if ($p == 'format') $v = preg_replace("/[^a-z]/i", "", substr($v, 0, 10));
				else if ($p == 'priceoffice') {
					self::ParseArray($v, $p, $parse);
					continue;
				}
			}

			if ($type == 'document') {
				if ($p == 'insurance' || $p == 'cod') $v = ($v == 1 ? true : false);
				else if ($p == 'delivery') $v = ($v != '' ? explode(',', $v) : false);
				else if ($p == 'size') $v = explode('x', $v);
				else if ($p == 'length' || $p == 'space') {
					$v = explode(',', $v);
					$o = array();
					foreach ($v as $s) if ($s != '') {
						$s = explode('=', $s);
						if ($s[0] != '') $o[$s[0]] = (isset($s[1]) ? intval($s[1]) : 0);
					}
					$v = $o;
				}
			}

			if ($type == 'office') {
				if ($p == 'type') $v = intval($v);
				else if (in_array($p, array('id', 'gps'))) $v = preg_replace("/[^a-z0-9.,]/i", "", substr($v, 0, 30));
				else $v = $GLOBALS['APPLICATION']->ConvertCharset(substr($v, 0, 160), 'windows-1251', LANG_CHARSET);
			}

			if ($type == 'location') {
				if ($p == 'country' || $p == 'region') $v = intval($v);
				else $v = $GLOBALS['APPLICATION']->ConvertCharset(substr($v, 0, 160), 'windows-1251', LANG_CHARSET);
			}

			if ($type == 'location_street') {
				if (in_array($p, array('street', 'city'))) $v = $GLOBALS['APPLICATION']->ConvertCharset(substr($v, 0, 160), 'windows-1251', LANG_CHARSET);
			}

			if ($type == 'location_zip') {
				$v = preg_replace("/[^0-9]/i", "", substr($v, 0, 6));
			}

			if ($type == 'location_robot') {
				$v = preg_replace("/[^0-9.]/i", "", substr($v, 0, 15));
			}

			if ($type == 'control' || $type == 'detail') {
				if (in_array($p, array('id', 'flag', 'status', 'tariff', 'status_warning', 'day_arrival', 'day_delay', 'day_office'))) $v = intval($v);
				else $v = $GLOBALS['APPLICATION']->ConvertCharset(substr($v, 0, 500), 'windows-1251', LANG_CHARSET);
			}

			if ($type == 'tracking') {
				if (in_array($p, array('company_id'))) $v = intval($v);
				else if ($p == 'tariff') $v = explode(',', $v);
				else $v = $GLOBALS['APPLICATION']->ConvertCharset(substr($v, 0, 500), 'windows-1251', LANG_CHARSET);
			}

			$parse[$p] = $v;
		}

		return $r;

	}


	// ��������� id ������ ��������� �������� (��� �������������� ���������� eDost)
	public static function GetBitrixID($v) {
		if ($v['automatic'] !== $v['id']) return $v['id'];
		return $v['automatic'].':'.$v['profile'];
	}
	// ��������� id ������ ��� html
	public static function GetHtmlID($v) {
		if ($v['automatic'] == 'edost') {
			if (isset($v['office_mode'])) $s = $v['office_mode'];
			else $s = $v['profile'].($v['cod_tariff'] ? '_Y' : '');
			return 'edost_'.$s;
		}
		if ($v['automatic'] !== $v['id']) return $v['id'];
		return $v['automatic'].'_'.$v['profile'];
	}
	// ��������� value ������ ��� html
	public static function GetHtmlValue($v) {
		if ($v['automatic'] == 'edost') {
			$value = 'edost:'.$v['profile'].($v['id'] != '' && $v['automatic'] !== $v['id'] ? '_'.$v['id'] : '');
			if (isset($v['office_id']) || $v['cod_tariff']) $value .=  ':'.(isset($v['office_id']) ? $v['office_id'] : '').':'.($v['cod_tariff'] ? 'Y' : '');
		}
		else $value = self::GetBitrixID($v);
		return $value;
	}
	// ��������� title ������
	public static function GetTitle($v, $full = false) {
		$r = ($full && isset($v['head']) && !isset($v['company_head']) ? $v['head'] : $v['company']);
		$s = $v['name'];
		if ($full) $s .= ($s != '' && $v['insurance'] != '' ? ' ' : '').$v['insurance'];
		return $r.($s != '' ? ' ('.$s.')' : '');
	}

	// ������ �������� �� �������� �������� � ����� + �������� ������ '<br>' � �������� + �������� '�� ����������'
	public static function ParseName($s, $company = '', $description = '', $insurance = '') {

		$r = array('name' => '');

		$o = $s;
		if ($insurance != '') $s = str_replace($insurance, '', $s);
		if ($company != '' && strpos($s, $company) !== false) $company = '';
		if ($company != '') {
			$r['company'] = trim($company);
			$r['name'] = trim($s);
		}
		else {
			$s = explode('(', $s);
			$r['company'] = trim($s[0]);
			if (isset($s[1])) {
				$s = explode(')', $s[1]);
				$r['name'] = trim($s[0]);
			}

			// ������������ �������� ������
			$o = explode('(', $o);
			if (isset($o[1])) {
				$o = explode(')', $o[1]);
				$r['name_original'] = trim($o[0]);
			}
		}

		$s = trim($description);
		if ($s === '<br>' || $s === '<br />') $s = '';
		$r['description'] = $s;

		return $r;

	}

	// ��������� ��������� � �������� ������ - ������ � ������� � ����������������� ���� ($key == 'value' - ������������ ������ ��������,  $key == 'formatted' - ������������ ������ ����������������� ������)
	public static function GetPrice($key, $price, $currency, $currency_result = '') {

		$r = array();
		if ($price == '') $price = 0;

		if ($currency_result == '') $currency_result = $currency;
		$r[$key] = ($currency !== '' ? CCurrencyRates::ConvertCurrency($price, $currency, $currency_result) : $price);
		$r[$key] = roundEx($r[$key], SALE_VALUE_PRECISION);
		if ($key != 'value') $r[$key.'_formatted'] = ($price == '0' ? '0' : SaleFormatCurrency($r[$key], $currency_result));

		if ($key == 'value') return $r[$key];
		if ($key == 'formatted') return $r[$key.'_formatted'];
		return $r;

	}


	// ��������� ����� �������� ���� '5-8 ����'
	public static function GetDay($from = '', $to = '', $name = 'D') {

		$sign = GetMessage('EDOST_DELIVERY_SIGN');
		$from = intval($from);
		$to = intval($to);
		if (!in_array($name, array('D', 'H', 'M', 'MIN'))) $name = 'D';

		$r = '';
		$n = 0;

		if ($from > 0) {
			$n = $from;
			$r .= $from;
		}
		if ($to > 0 && $to != $from) {
			$n = $to;
			$r .= ($r != '' ? '-' : '').$to;
		}

		if ($n == 0) return '';

		$s = '';
		$ar = $sign['day'];
		if ($n >= 11 && $n <= 19) $s = $ar[$name][2];
		else {
			$n = $n % 10;
			if ($n == 1) $s = $ar[$name][0];
			else if ($n >= 2 && $n <= 4) $s = $ar[$name][1];
			else $s = $ar[$name][2];
		}

		return $r.' '.$s;

	}


	// ���������� �������
	public static function SortTariff(&$data, $config) {

		if (count($data) <= 1) return;

		$sort_max = 0;
		foreach ($data as $k => $v) {
			if (empty($v['sort'])) $data[$k]['sort'] = $v['sort'] = 0;
			if ($v['sort'] > $sort_max) $sort_max = $v['sort'];
		}

		$ar = array();
		foreach ($data as $k => $v) {
			if ($config['sort_ascending'] == 'Y') {
				// �� ��������� ��������
				$i = ((isset($v['price']) ? floatval($v['price']) : 0) + (isset($v['priceinfo']) ? floatval($v['priceinfo']) : 0))*1000 + (!empty($sort_max) ? 5*$v['sort']/$sort_max : 0);
				$ar[] = $i;
				if ($config['template'] == 'N3') $data[$k]['sort'] = round($i*1000);
			}
			else {
				// �� ���� ����������
				$ar[] = $v['sort'];
			}
		}
		array_multisort($ar, SORT_ASC, SORT_NUMERIC, $data);

	}


	// ��������� ������ ����� (���� ������� $tariff, ����� ����������� ������ ����� � ����������, ����������� ������ � �.�.)
	public static function GetOfficeAddress($office, $tariff = false) {

		$r = '';
		$sign = GetMessage('EDOST_DELIVERY_SIGN');
		$metro = ($office['metro'] != '' ? $sign['metro'].$office['metro'] : '');
		$r = $office['name'];
		$r .= ($r != '' && $metro != '' ? ', ' : '').$metro;
		$r = ($r != '' ? ' ('.$r.')' : '');

		if ($tariff === false) return $office['address'].$r;

		$shop = (in_array($tariff['company_id'], array('s1', 's2', 's3', 's4')) ? true : false);
		$shop_company_default = (in_array($tariff['company'], $sign['shop_company_default']) ? true : false);

		$c = $office['code'];
		if ($c == '') $c = ($shop ? 'S' : 'T');

		if (in_array($office['type'], array(5, 6))) $head = ($tariff['company_id'] == 72 ? $sign['pochtomat']['name'] : $sign['postamat']['name']);
		else $head = $sign[$tariff['format']];

		$s = array();
		$s[] = $head.(!$shop_company_default && $tariff['format'] != 'shop' ? ' '.$tariff['company'] : '').': '.$office['address_full'] . $r;
		if ($office['tel'] != '') $s[] = $sign['tel'].': '.$office['tel'];
		if ($office['schedule'] != '') $s[] = $sign['schedule'].': '.$office['schedule'];
		$s[] = $sign['code'].': '.$c.'/'.$office['id'].'/'.$office['type'].'/'.$tariff['profile'].(!empty($tariff['cod_tariff']) ? '-Y' : '');
		$r = implode(', ', $s);

		return $r;

	}

	// ��������� ������ ����� �� ������ (���������: false - ����� ���,  true - ���� ����, �� ��� ������,  array - ������ �����)
	public static function ParseOfficeAddress($address) {

		$sign = GetMessage('EDOST_DELIVERY_SIGN');

		$s = explode(', '.$sign['code'].': ', $address);
		if (empty($s[1])) return false;

		$s1 = explode(':', $s[0]);
		$head = $s1[0];

		$s1 = explode($head.': ', $s[0]);
		$s1 = $s1[1];

		$address = $tel = $schedule = '';
		$ar = array(', '.$sign['schedule'].': ', ', '.$sign['tel'].': ');
		foreach ($ar as $k => $v) {
			$s2 = explode($v, $s1);
			if (!empty($s2[1])) {
				if ($k == 0) $schedule = $s2[1];
				else $tel = $s2[1];
				$s1 = $s2[0];
			}
		}
		$address = $s1;

		$s = explode('/', $s[1]);
		if (empty($s[3])) return true;
		$profile = explode('-', $s[3]);

		return array(
			'code' => $s[0],
			'id' => preg_replace("/[^0-9A]/i", "", substr($s[1], 0, 20)),
			'type' => intval($s[2]),
			'profile' => intval($profile[0]),
			'cod_tariff' => (!empty($profile[1]) && $profile[1] == 'Y' ? true : false),
			'head' => $head,
			'address' => $address,
			'tel' => $tel,
			'schedule' => $schedule,
		);

	}


	// ������ �������� �� ������� ������������ $data
	public static function RenameTariff($s, $data) {
		if ($s != '' && isset($data[1])) {
			$i = array_search($s, $data[0]);
			if ($i !== false) $s = $data[1][$i];
		}
		return $s;
	}


	// �������������� ������ ��� ������ � ����� 'general'
	public static function FormatHead(&$v, $head) {

		if (isset($v['head']) || $v['format'] == 'post') return;

		$sign = GetMessage('EDOST_DELIVERY_SIGN');

		$v['head'] = $head;
		if (isset($v['office_type']) && in_array($v['office_type'], array(5, 6))) $v['head'] = ($v['company_id'] == 72 ? $sign['pochtomat']['head'] : $sign['postamat']['head']);

		$shop_company_default = (in_array($v['company'], $sign['shop_company_default']) ? true : false);
		$a = false;
		if (isset($v['office_count'])) $a = true;
		else if ($v['company_id'] != 27 || !$shop_company_default) $v['company_head'] = $sign['delivery_company']; // ����� �������� ������ �������� ��������� ������� (����� ������� ������)
		if ($a && $v['format'] != 'shop' && !$shop_company_default) {
			$rename = GetMessage('EDOST_DELIVERY_RENAME');
			$v['head'] .=  ' '.self::RenameTariff($v['company'], $rename['company']); // ���������� �������� �������� � ��������� (��� ������� � �������)
		}

		// ������� �������������� ��� ��
		if (in_array($v['format'], array('terminal', 'house'))) {
			$v['warning'] = '';
			if ($v['format'] == 'terminal' && isset($v['office_count']) && $v['office_count'] > 1) $v['warning'] = $sign['terminal_warning'];
			else if ($v['format'] == 'house') $v['warning'] = $sign['house_warning'];

			if (isset($v['priceinfo'])) {
				$v['warning'] .= ($v['warning'] != '' ? '<br>' : '').$sign['priceinfo_warning'];
				if ($v['price'] > 0) $v['description'] = str_replace('%price%', $v['price_formatted'], $sign['priceinfo_description']).(!empty($v['description']) ? '<br>'.$v['description'] : '');
			}
		}

	}


	// ��������� ������������ ���� �� ���� ����� (��� �� ���� �����, ���������� � ������, ���� $type == '')
	public static function GetOfficePrice($priceoffice, $type, $address = '') {

		if ($type == '') {
			if ($address === '') return false;

			$sign = GetMessage('EDOST_DELIVERY_SIGN');
			$s = explode(', '.$sign['code'].': ', $address);
			if (!empty($s[1])) {
				$s = explode('/', $s[1]);
				if (!empty($s[2])) $type = intval($s[2]);
			}
		}

		foreach ($priceoffice as $v) if ($v['type'] == $type) return array(
			'price' => $v['price'],
			'pricecash' => $v['pricecash'],
			'priceinfo' => $v['priceinfo'],
			'office_type' => $v['type'],
		);

		return false;

	}


	// ��������� ��������� ����: �� '�����������' �� '������������' (�� 100 ���. �� 200 ���.) + ����� ������ �������� ������
	public static function FormatRange(&$format, $currency, $cod) {
		$price = $pricecod = $day = $day2 = self::SetRange();
		$ico = '';
		$min = false;
		foreach ($format['data'] as $k => $v) if (isset($v['id']) && !isset($v['error'])) {
			if ($ico == '' && !empty($v['ico'])) $ico = $v['ico'];

			$p = $v['price'] + (isset($v['priceinfo']) ? $v['priceinfo'] : 0);
			if ($min === false || $p < $min['price']) $min = array('price' => $p, 'key' => $k);
			$price = self::SetRange($price, $p);

			if (!empty($v['day'])) {
				$s = preg_replace("/[^0-9-]/i", "", $v['day']);
				$s = explode('-', $s);

				$day = self::SetRange($day, $s[0]);
				if (!empty($s[1])) $day = self::SetRange($day, $s[1]);
			}

			if ($cod && isset($v['pricecod'])) $pricecod = self::SetRange($pricecod, $v['pricecod'], $v['pricecod_formatted']);
		}
		if ($min !== false) {
			$v = $min + $format['data'][$min['key']];
			$v['price_formatted'] = self::GetPrice('formatted', $v['price'], '', $currency);
			$format['min'] = $v;
		}
		$price['min']['formatted'] = self::GetPrice('formatted', $price['min']['value'], '', $currency);
		$price['max']['formatted'] = self::GetPrice('formatted', $price['max']['value'], '', $currency);
		$format['price'] = $price;
		$format['pricecod'] = $pricecod;
		$format['pricehead'] = self::AddRange($price, $pricecod);
		$format['day'] = $day; //self::GetDay($day['min']['value'], $day['max']['value']);
		$format['ico'] = $ico;
	}
	public static function SetRange($range = false, $value = 0, $formatted = '') {
		if ($range === false) return array('min' => array('value' => -1, 'formatted' => ''), 'max' => array('value' => -1, 'formatted' => ''));
		if ($range['min']['value'] == -1 || $value < $range['min']['value']) $range['min'] = array('value' => $value, 'formatted' => $formatted);
		if ($range['max']['value'] == -1 || $value > $range['max']['value']) $range['max'] = array('value' => $value, 'formatted' => $formatted);
		return $range;
	}
	public static function AddRange($range = false, $range2) {
		if ($range === false) return $range2;
		if ($range2['min']['value'] >= 0) $range = self::SetRange($range, $range2['min']['value'], $range2['min']['formatted']);
		if ($range2['max']['value'] >= 0) $range = self::SetRange($range, $range2['max']['value'], $range2['max']['formatted']);
		return $range;
	}
	public static function GetRange($range) {
		$sign = GetMessage('EDOST_DELIVERY_SIGN');
		$r = ($range['min']['value'] != $range['max']['value'] && $range['min']['value'] !== '0' ? '<br>' : '');
		$r = ($range['min']['value'] != $range['max']['value'] ? $sign['from'] . $range['min']['formatted'] . $r . $sign['to'] : '') . $range['max']['formatted'];
		return $r;
	}


	// �������� ������ � ���� ������
	public static function PackData($data, $key) {

		$r = '';
		$key_count = count($key) - 1;

		foreach ($data as $v) {
			$s = 'end|';
			$start = false;
			for ($i = $key_count; $i >= 0; $i--) {
				if (!$start && isset($v[$key[$i]])) $start = true;
				if ($start) {
					$p = (isset($v[$key[$i]]) ? $v[$key[$i]] : '');
					if ($p != '') {
						if (in_array($key[$i], array('tracking_code'))) $p = $GLOBALS['APPLICATION']->ConvertCharset($p, LANG_CHARSET, 'windows-1251');
						$p = urlencode(str_replace('|', '', $p));
					}
					$s = ($p !== 'end' ? $p : '').'|'.$s;
				}
			}
			$r .= $s;
		}

		return $r;

	}


	// �������� ������ ������
	public static function PackItem(&$package, $s, $quantity) {

		if (!($s[0] > 0 && $s[1] > 0 && $s[2] > 0 && $quantity > 0)) return false;

		sort($s); // ���������� ��������� �� �����������

		if ($quantity == 1) {
			$package[] = array('X' => $s[0], 'Y' => $s[1], 'Z' => $s[2]);
			return true;
		}

		$x1 = $y1 = $z1 = $l = 0;
		$max1 = floor(sqrt($quantity));
		for ($y = 1; $y <= $max1; $y++) {
			$i = ceil($quantity / $y);
			$max2 = floor(sqrt($i));

			for ($z = 1; $z <= $max2; $z++) {
				$x = ceil($i/$z);

				$l2 = $x*$s[0] + $y*$s[1] + $z*$s[2];
				if ($l == 0 || $l2 < $l) {
					$l = $l2;
					$x1 = $x;
					$y1 = $y;
					$z1 = $z;
				}
			}
		}

		$package[] = array('X' => $x1*$s[0], 'Y' => $y1*$s[1], 'Z' => $z1*$s[2]);
		return true;

	}

	// �������� ������ �������
	public static function PackItems($a) {

		if (empty($a)) return array(0, 0, 0);

		$n = count($a);
		for ($i3 = 1; $i3 < $n; $i3++) {
			// ���������� �������� �� ��������
			for ($i2 = $i3-1; $i2 < $n; $i2++) {
				for ($i = 0; $i <= 1; $i++) {
					if ($a[$i2]['X'] < $a[$i2]['Y']) {
						$a1 = $a[$i2]['X'];
						$a[$i2]['X'] = $a[$i2]['Y'];
						$a[$i2]['Y'] = $a1;
					};
					if ($i == 0 && $a[$i2]['Y'] < $a[$i2]['Z']) {
						$a1 = $a[$i2]['Y'];
						$a[$i2]['Y'] = $a[$i2]['Z'];
						$a[$i2]['Z'] = $a1;
					}
				}
				$a[$i2]['sum'] = $a[$i2]['X'] + $a[$i2]['Y'] + $a[$i2]['Z']; // ����� ������
			}

			// ���������� ������� �� �����������
			for ($i2 = $i3; $i2 < $n; $i2++)
				for ($i = $i3; $i < $n; $i++)
					if ($a[$i-1]['sum'] > $a[$i]['sum']) {
						$a2 = $a[$i];
						$a[$i] = $a[$i-1];
						$a[$i-1] = $a2;
					}

			// �������� ���� ����� ��������� �������
			if ($a[$i3-1]['X'] > $a[$i3]['X']) $a[$i3]['X'] = $a[$i3-1]['X'];
			if ($a[$i3-1]['Y'] > $a[$i3]['Y']) $a[$i3]['Y'] = $a[$i3-1]['Y'];
			$a[$i3]['Z'] = $a[$i3]['Z'] + $a[$i3-1]['Z'];
			$a[$i3]['sum'] = $a[$i3]['X'] + $a[$i3]['Y'] + $a[$i3]['Z']; // ����� ������
		}

		$r = array(round($a[$n-1]['X'], 3), round($a[$n-1]['Y'], 3), round($a[$n-1]['Z'], 3));
		sort($r); // ���������� ��������� �� �����������

		return $r;

	}

	public static function implode2($delimiter, $data, $n = 0) {

		if (empty($data)) return '';
		if (!is_array($data)) return $data;

		$s = '';
		$n++;

		$a = $delimiter;
		if (is_array($a)) {
			if (isset($a[$n-1])) $a = $a[$n-1];
			else if (isset($a[count($a)-1])) $a = $a[count($a)-1];
			else $a = '';
		}

		$s = array();
		foreach ($data as $v) $s[] = (is_array($v) ? self::implode2($delimiter, $v, $n) : $v);
		$s = implode($a, $s);

		return $s;

	}
}

?>