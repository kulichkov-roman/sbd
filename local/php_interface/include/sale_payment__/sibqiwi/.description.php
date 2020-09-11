<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

$data = [
    'NAME' => 'QIWI SIBDROID.RU',
    'SORT' => 400,
    'IS_AVAILABLE' => \Bitrix\Sale\PaySystem\Manager::HANDLER_AVAILABLE_TRUE,
	'CODES' =>[
        'SIBQIWI_CLIENT_ID' => [
            'NAME' => "Client ID",
			'SORT' => 100
        ],
        'SIBQIWI_CLIENT_SECRET' => [
            'NAME' => "Client Secret",
			'SORT' => 110
		],
		'SIBQIWI_CLIENT_PIN' => [
            'NAME' => "Client Pin",
			'SORT' => 115
		],
		'SIBQIWI_SHOP_ID' => [
            'NAME' => "Shop ID",
			'SORT' => 125
        ],
        'SIBQIWI_PS_IS_TEST' => array(
			'NAME' => 'Тестовый режим',
			'SORT' => 900,
			'GROUP' => 'GENERAL_SETTINGS',
			"INPUT" => array(
				'TYPE' => 'Y/N'
			)
        ),
        'SIBQIWI_ORDER_NUM' => array(
			'NAME' => 'Номер заказа',
			'SORT' => 1900,
			'GROUP' => 'GENERAL_SETTINGS'
        ),
        'SIBQIWI_ORDER_ID' => array(
			'NAME' => 'ID заказа',
			'SORT' => 1920,
			'GROUP' => 'GENERAL_SETTINGS'
        ),
        'SIBQIWI_AMOUNT' => array(
			'NAME' => 'Сумма оплаты',
			'SORT' => 1930,
			'GROUP' => 'GENERAL_SETTINGS'
		),
    ]
];