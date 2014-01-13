<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
$APPLICATION->SetTitle('Справка');
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
 
if(file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/advancedfilter/README.md"))
    include $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/advancedfilter/README.md";
 
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php"); 