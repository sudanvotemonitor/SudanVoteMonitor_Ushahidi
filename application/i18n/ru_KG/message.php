<?php

$lang = array
(
	'name' => array
	(
		'required'=> 'Введите название сообщения.',
		'length'		=> 'Длинна названия сообщения не может быть менее 3х символов.',
	),

	'email' => array
	(
		'required'		=> 'Нужно ввести адрес Email, или уберите галочку.',
		'email'		  => 'Ваш Email адрес введен не правильно, введите правильно.',
		'length'	  => 'Длинна Email не может быть меньше 4х и  более 64 символов.'
	),	

	'phone' => array
	(
		'length'		=> 'Номер телефона введен неверно.',
	),

	'message' => array
	(
		'required'		=> 'Введите сообщение'
	),

	'captcha' => array
	(
		'required' => 'Введите защитный код', 
		'default' => 'Введите правильный защитный код'
	)

);