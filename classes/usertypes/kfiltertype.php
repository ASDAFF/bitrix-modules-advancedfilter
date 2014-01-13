<?php

abstract class kfiltertype {
     
    protected $filter = array();
     
    /* первая проверка при добавлении поля */
    public function validate(&$field){ }    
    
    /* Добавляет перечисляемые варианты в $field['VARIANTS'], выделяет выбраные значения, формирует $this->filter */
    public function addVariants(&$field){ }   
 
    public function getFilter(){
        return $this->filter;
    }

}

abstract class kfiltertyperange extends kfiltertype {
    
    public function validate(&$field) {
        $_REQUEST[$field['NAME'] . '_FROM'] = intval($_REQUEST[$field['NAME'] . '_FROM']);
        $_REQUEST[$field['NAME'] . '_TO'] = intval($_REQUEST[$field['NAME'] . '_TO']); 
    }
 
    protected function checkToFromWasLess(&$field) {
        if( $_REQUEST[$field['NAME'] . '_FROM'] > $_REQUEST[$field['NAME'] . '_TO'] && $_REQUEST[$field['NAME'] . '_TO'] ){
            $_REQUEST[$field['NAME'] . '_FROM'] = $_REQUEST[$field['NAME'] . '_TO'];
        }
    }

}