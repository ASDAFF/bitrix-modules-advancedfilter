<?php

class KFilterList {
     
    private static $kfilters_table = 'kfilters';
            
    function GetAll(){
        global $DB;
        $strSql = 'SELECT * FROM `' . self::$kfilters_table;
        $rs = $DB->Query($strSql);
        return $rs;
    }
    
    
}