<?php

class advancedfilter extends CModule {

    var $MODULE_ID = "advancedfilter";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_GROUP_RIGHTS;
    var $errors = array();

    function __construct() {
        $arModuleVersion = array();

        $path = str_replace("\\", "/", __FILE__);
        $path = substr($path, 0, strlen($path) - strlen("/index.php"));
        include($path . "/version.php");

        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        }

        $this->MODULE_NAME = 'Настраиваемый фильтр';
        $this->MODULE_DESCRIPTION = 'Модуль предназначен для расширеной настройки филтьрации элементов на сайте';
    }

    function DoInstall() {
        RegisterModule($this->MODULE_ID);
    }

    function DoUninstall() {  
        UnRegisterModule($this->MODULE_ID);
    } 

} 