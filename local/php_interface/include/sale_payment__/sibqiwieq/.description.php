<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

$data = [
    'NAME' => 'SIBDROID.RU Эквайринг',
    'SORT' => 400,
    'IS_AVAILABLE' => \Bitrix\Sale\PaySystem\Manager::HANDLER_AVAILABLE_TRUE,
	'CODES' =>[
        'SIBQIWIEQ_MID' => [
            'NAME' => "mId",
			'SORT' => 100
        ],
        'SIBQIWIEQ_MAID' => [
            'NAME' => "maId",
			'SORT' => 110
		],
		'SIBQIWIEQ_USERNAME' => [
            'NAME' => "userName",
			'SORT' => 115
		],
		'SIBQIWIEQ_PASSWORD' => [
            'NAME' => "password",
			'SORT' => 125
		],
		'SIBQIWIEQ_KEY' => [
            'NAME' => "Key",
			'SORT' => 135
		],
		'SIBQIWIEQ_IS_PAY' => [
            'NAME' => "Признак оплаты заказа",
			'SORT' => 200
		],
		'SIBQIWIEQ_PAY_ID' => [
            'NAME' => "Код оплаты",
			'SORT' => 200
        ],
        'SIBQIWIEQ_PS_IS_TEST' => array(
			'NAME' => 'Тестовый режим',
			'SORT' => 900,
			'GROUP' => 'GENERAL_SETTINGS',
			"INPUT" => array(
				'TYPE' => 'Y/N'
			)
        ),
        'SIBQIWIEQ_ORDER_NUM' => array(
			'NAME' => 'Номер заказа',
			'SORT' => 1900,
			'GROUP' => 'GENERAL_SETTINGS'
        ),
        'SIBQIWIEQ_ORDER_ID' => array(
			'NAME' => 'ID заказа',
			'SORT' => 1920,
			'GROUP' => 'GENERAL_SETTINGS'
        ),
        'SIBQIWIEQ_AMOUNT' => array(
			'NAME' => 'Сумма оплаты',
			'SORT' => 1930,
			'GROUP' => 'GENERAL_SETTINGS'
		),
    ]
];