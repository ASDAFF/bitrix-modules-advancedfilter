<?php

abstract class kfiltertype {
 
    protected $filter = array();
    
    /* первая проверка при добавлении поля */
    function validate(&$field){ }    
    
    /* Добавляет перечисляемые варианты в $field['VARIANTS'], выделяет выбраные значения, формирует $this->filter */
    function addVariants(&$field){ }   
 
    function getFilter(){
        return $this->filter;
    }

}

abstract class kfiltertyperange extends kfiltertype {
    
    protected function validateToInt(&$field) {
        $_REQUEST[$field['NAME'] . '_FROM'] = intval($_REQUEST[$field['NAME'] . '_FROM']);
        $_REQUEST[$field['NAME'] . '_TO'] = intval($_REQUEST[$field['NAME'] . '_TO']); 
    }
 
    protected function checkToFromWasLess(&$field) {
        if( $_REQUEST[$field['NAME'] . '_FROM'] > $_REQUEST[$field['NAME'] . '_TO'] && $_REQUEST[$field['NAME'] . '_TO'] ){
            $_REQUEST[$field['NAME'] . '_FROM'] = $_REQUEST[$field['NAME'] . '_TO'];
        }
    }

}