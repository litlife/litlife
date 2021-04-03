<?php

return [

	'api_url' => env('UNITPAY_API_URL', ''),

	'url' => env('UNITPAY_URL', ''),

	/*
	 * Login
	 */
	'login' => env('UNITPAY_LOGIN', ''),

	/*
	 * projectID
	 */
	'project_id' => env('UNITPAY_PROJECT_ID', ''),

	/*
	 * secretKey
	 */
	'secret_key' => env('UNITPAY_SECRET_KEY', ''),

	'api_secret_key' => env('UNITPAY_API_SECRET_KEY', ''),

	'public_key' => env('UNITPAY_PUBLIC_KEY', ''),
	/*
	 * locale for payment form
	 */
	'locale' => 'ru',  // ru || en

	// Allowed ip's http://help.unitpay.ru/article/67-ip-addresses
	'allowed_ips' => [
		'31.186.100.49',
		'178.132.203.105',
		'52.29.152.23',
		'52.19.56.234',
		'192.168.10.1',
		'127.0.0.1'
	],

	'allowed_currencies' => ['RUB', 'UAH', 'BYN', 'EUR', 'USD'],
	'allowed_locales' => ['ru', 'en'],
	'allowed_payment_types' => ['card', 'webmoney', 'yandex', 'qiwi', 'mc'],
	'allowed_methods' => ['initPayment', 'getPayment', 'refundPayment', 'getCommissions', 'getPartner', 'massPayment', 'massPaymentStatus', 'getBinInfo'],
	'allowed_outgoing_payment_types' => ['card', 'webmoney', 'yandex', 'qiwi'],
	'log_enable' => true,
	'log_chanel' => env('LOG_CHANNEL', 'stack'),
	'deposit_comissions' => [
		'card' => '4',
		'webmoney' => '1',
		'yandex' => '7',
        'yoomoney' => '7',
		'qiwi' => '7',
		'beeline' => '25',
		'mts' => '12',
		'mf' => '20',
		'tele2' => '25',
        'applepay' => '4',
        'googlepay' => '4',
        'samsungpay' => '4'
	],
	'allowed_mobile_payment_types' => [
		'beeline',
		'mts',
		'mf',
		'tele2'
	],
	'withdrawal_restrictions' => [
		'qiwi' => [
			'comission' => '2',
			'min' => '10',
			'max' => '15000',
		],
		'yandex' => [
			'comission' => '2',
			'min' => '10',
			'max' => '15000',
		],
        'yoomoney' => [
            'comission' => '2',
            'min' => '10',
            'max' => '15000',
            'max_in_day' => '600000',
        ],
		'webmoney' => [
			'comission' => '3',
			'min' => '10',
			'max' => '14700'
		],
        'wmr' => [
            'comission' => '3',
            'min' => '10',
            'max' => '14700'
        ],
		'card_rf' => [
            'comission' => '2',
			'min_comission' => '30',
			'min' => '50',
			'max' => '50000',
			'max_in_month' => '600000',
		],
		'card_not_rf' => [
            'comission' => '3',
			'min_comission' => '180',
			'min' => '120',
			'max' => '50000',
			'max_in_month' => '600000',
		]
	]
];
