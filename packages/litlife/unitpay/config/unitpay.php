<?php

return [

	/*
	 * Login
	 */
	'UNITPAY_LOGIN' => env('UNITPAY_LOGIN', ''),

	/*
	 * projectID
	 */
	'UNITPAY_PROJECT_ID' => env('UNITPAY_PROJECT_ID', ''),

	/*
	 * secretKey
	 */
	'UNITPAY_SECRET_KEY' => env('UNITPAY_SECRET_KEY', ''),

	/*
	 * api secretKey
	 */
	'UNITPAY_API_SECRET_KEY' => env('UNITPAY_API_SECRET_KEY', ''),

	/*
	 * locale for payment form
	 */
	'locale' => 'ru',  // ru || en

	// Allowed ip's http://help.unitpay.ru/article/67-ip-addresses
	'allowed_ips' => [
		'31.186.100.49',
		'178.132.203.105',
		'52.29.152.23',
		'52.19.56.234'
	],

	'allowed_currencies' => ['RUB', 'UAH', 'BYN', 'EUR', 'USD'],
	'allowed_locales' => ['ru', 'en'],
	'allowed_payment_types' => ['mc', 'sms', 'card', 'webmoney', 'yandex', 'qiwi', 'paypal', 'liqpay', 'alfaClick', 'cash', 'applepay'],
	'allowed_methods' => ['initPayment', 'getPayment', 'refundPayment', 'getCommissions', 'getPartner', 'massPayment', 'massPaymentStatus', 'getBinInfo'],
];
