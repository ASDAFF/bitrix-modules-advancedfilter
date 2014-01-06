<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();  
 
$arParams['IBLOCK_ID'] = intval($arParams['IBLOCK_ID']);
if(!$arParams['IBLOCK_ID'])
    return;

if(!CModule::IncludeModule('advancedfilter'))
    return;

$filterData = new KFilter($arParams['IBLOCK_ID']);

$arResult['FILTERS'] = $filterData->GetResult();
 
$this->IncludeComponentTemplate(); 