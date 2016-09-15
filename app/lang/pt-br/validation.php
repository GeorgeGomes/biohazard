<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Validation Language Lines
	|--------------------------------------------------------------------------
	|
	| The following language lines contain the default error messages used by
	| the validator class. Some of these rules have multiple versions such
	| as the size rules. Feel free to tweak each of these messages here.
	|
	*/

	"accepted"             => "O :attribute deve ser aceito.",
	"active_url"           => "O :attribute não é uma URL válida.",
	"after"                => "O :attribute deve ser uma data posterior a :date.",
	"alpha"                => "O :attribute só pode possuir letras.",
	"alpha_dash"           => "O :attribute só pode possuir letras, números e traços.",
	"alpha_num"            => "O :attribute só pode possuir letras e números.",
	"array"                => "O :attribute deve ser uma matriz.",
	"before"               => "O :attribute deve ser uma data anterior a :date.",
	"between"              => array(
		"numeric" => "O :attribute deve ser entre :min e :max.",
		"file"    => "O :attribute deve conter entre :min e :max kilobytes.",
		"string"  => "A :attribute deve possuir entre :min e :max caracteres.",
		"array"   => "O :attribute deve conter entre :min e :max itens.",
	),
	"boolean"              => "O campo :attribute deve ser verdadeiro ou falso",
	"confirmed"            => "A confirmação de :attribute não corresponde.",
	"date"                 => "A :attribute não é válida.",
	"date_format"          => "A :attribute não corresponde ao formato :format.",
	"different"            => "O :attribute e :other devem ser diferentes.",
	"digits"               => "O :attribute deve ser :digits dígitos.",
	"digits_between"       => "O :attribute deve conter entre :min e :max dígitos.",
	"email"                => "O :attribute deve ser um e-mail válido.",
	"exists"               => "O :attribute selecionado é inválido.",
	"image"                => "O :attribute deve ser uma imagem.",
	"in"                   => "O :attribute selecionado é inválido.",
	"integer"              => "O :attribute deve ser do tipo inteiro.",
	"ip"                   => "O :attribute deve ser um endereço IP válido.",
	"max"                  => array(
		"numeric" => "O :attribute não pode ser maior que :max.",
		"file"    => "O :attribute não pode ser maior que :max kilobytes.",
		"string"  => "A :attribute não pode possuir mais que :max caracteres.",
		"array"   => "O :attribute não pode possuir mais que :max itens.",
	),
	"mimes"                => "O :attribute deve ser um arquivo do tipo: :values.",
	"min"                  => array(
		"numeric" => "O :attribute deve ser no mínimo :min.",
		"file"    => "O :attribute deve ter no mínimo :min kilobytes.",
		"string"  => "A :attribute deve possuir ao menos :min caracteres.",
		"array"   => "O :attribute deve possuir no mínimo :min itens.",
	),
	"not_in"               => "O :attribute selecionado é inválido.",
	"numeric"              => "O :attribute deve ser um número.",
	"regex"                => "O :attribute possui formato inválido.",
	"required"             => "O campo :attribute é necessário.",
	"required_if"          => "O campo :attribute é necessário quando :other é :value.",
	"required_with"        => "O campo :attribute é necessário quando :values está selecionado.",
	"required_with_all"    => "O campo :attribute é necessário quando :values está selecionado.",
	"required_without"     => "O campo :attribute é necessário quando :values não está selecionado.",
	"required_without_all" => "O campo :attribute é necessário quando nenhum dos :values estão selecionados.",
	"same"                 => "O :attribute e :other devem combinar.",
	"size"                 => array(
		"numeric" => "O :attribute deve ser :size.",
		"file"    => "O :attribute deve ser de :size kilobytes.",
		"string"  => "A :attribute deve possuir :size caracteres.",
		"array"   => "O :attribute deve conter :size itens.",
	),
	"unique"               => "O :attribute já está sendo utilizado.",
	"url"                  => "O formato da :attribute é inválido",
	"timezone"             => "O :attribute deve possuir uma zona válida.",

	/*
	|--------------------------------------------------------------------------
	| Custom Validation Language Lines
	|--------------------------------------------------------------------------
	|
	| Here you may specify custom validation messages for attributes using the
	| convention "attribute.rule" to name the lines. This makes it quick to
	| specify a specific custom language line for a given attribute rule.
	|
	*/

	'custom' => array(
		'attribute-name' => array(
			'rule-name' => 'custom-message',
		),
	),

	/*
	|--------------------------------------------------------------------------
	| Custom Validation Attributes
	|--------------------------------------------------------------------------
	|
	| The following language lines are used to swap attribute place-holders
	| with something more reader friendly such as E-Mail Address instead
	| of "email". This simply helps us make messages a little cleaner.
	|
	*/

	'attributes' => array(),

);
