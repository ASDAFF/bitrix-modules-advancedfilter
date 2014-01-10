<?php
 
/*  Фильтр для выбора элементов с установленым (не пустым) свойством  */
 
class KFSettedProperty extends kfiltertype {
    
    function addVariants(&$field) {
        if($_REQUEST[$field['NAME']] == 'Y') {
            $field['VALUE'] = true;
            $this->filter['!PROPERTY_' . $field['PROPERTY']['CODE']] = false;
        } 
    }
 
} 