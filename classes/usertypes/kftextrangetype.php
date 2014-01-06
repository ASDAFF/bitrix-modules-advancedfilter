<?php
 
class KFTextRangeType extends kfiltertype {
 
    function addVariants(&$field) {
        if($_REQUEST[$field['NAME'] . '_FROM'] > $_REQUEST[$field['NAME'] . '_TO'])
            $_REQUEST[$field['NAME'] . '_FROM'] = $_REQUEST[$field['NAME'] . '_TO'];
        foreach (array('FROM' => '>=', 'TO' => '<=') as $key => $znak) { 
            if($_REQUEST[$field['NAME'] . '_' . $key]){
                $field['VALUE'][$key] = $_REQUEST[$field['NAME'] . '_' . $key]; 
                $this->filter[ $znak . 'PROPERTY_' . $field['NAME'] ] = $_REQUEST[$field['NAME'] . '_' . $key]; 
            }
        }
    }
 
} 