<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Third Party Services
	|--------------------------------------------------------------------------
	|
	| This file is for storing the credentials for third party services such
	| as Stripe, Mailgun, Mandrill, and others. This file provides a sane
	| default location for this type of information, allowing packages
	| to have a conventional place to find your various credentials.
	|
	*/

	'mailgun' => array(
		'domain' => '',
		'secret' => '',
	),

	'mandrill' => array(
		'secret' => '20IJXE4a9MZIAUDco8M2Rw',
		'username' => 'informativo@educacaocoletiva.com.br',
	),

	'sendgrid' => array(
		'secret' => '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',
		'username' => 'mobile@codificar.com.br',
	),

	'stripe' => array(
		'model'  => 'User',
		'secret' => '',
	),

);
