<?php

abstract class kfiltertype {
 
    private $filter = false;
 
    function validate(&$field){
           // первая проверка при добавлении поля  
    }    
 
    function addVariants(&$field){
         // добавить перечисляемые варианты в $field['VARIANTS']
         // выделить выбраные значения 
         // сформировать $this->filter
    }   
 
    function getFilter(){
        return $this->filter;
    }

}