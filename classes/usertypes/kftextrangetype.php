<?php
  
/*  Фильтрация по диапазону значений
 *  2 текстовых поля ОТ и ДО
 *  kudinsasha@gmail.com    */
 
class KFTextRangeType extends kfiltertype {
 
    function addVariants(&$field) { 
        $_REQUEST[$field['NAME'] . '_FROM'] = intval($_REQUEST[$field['NAME'] . '_FROM']);
        $_REQUEST[$field['NAME'] . '_TO'] = intval($_REQUEST[$field['NAME'] . '_TO']); 
        if(!$_REQUEST[$field['NAME'] . '_FROM'] && !$_REQUEST[$field['NAME'] . '_TO']){
            return;
        }
        if( $_REQUEST[$field['NAME'] . '_FROM'] > $_REQUEST[$field['NAME'] . '_TO'] && $_REQUEST[$field['NAME'] . '_TO'] ){
            $_REQUEST[$field['NAME'] . '_FROM'] = $_REQUEST[$field['NAME'] . '_TO'];
        }
        if($_REQUEST[$field['NAME'] . '_FROM'] == $_REQUEST[$field['NAME'] . '_TO']){ 
            $field['VALUE']['FROM'] = $field['VALUE']['TO'] = $_REQUEST[$field['NAME'] . '_FROM']; 
            $this->filter['PROPERTY_' . $field['NAME']] = $_REQUEST[$field['NAME'] . '_FROM']; 
        } else {   
            foreach (array('FROM' => '>=', 'TO' => '<=') as $key => $znak) { 
                if($_REQUEST[$field['NAME'] . '_' . $key]){
                    $field['VALUE'][$key] = $_REQUEST[$field['NAME'] . '_' . $key]; 
                    $this->filter[$znak . 'PROPERTY_' . $field['PROPERTY']] = $_REQUEST[$field['NAME'] . '_' . $key]; 
                } 
            } 
        }  
    }
    
}