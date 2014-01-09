<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => 'Фильтр',
	"DESCRIPTION" => 'Расширеная фильтрация для элементов',
	"ICON" => "/images/kfilter.gif",
	"PATH" => array(
		"ID" => "service",
		"CHILD" => array(
			"ID" => "kfilter",
			"NAME" => 'Фильтрация'
		)
	),
);
 