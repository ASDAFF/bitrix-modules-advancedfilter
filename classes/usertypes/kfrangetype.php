<?php
 
/*  Фильтр для выбора среди указанного диапазона чисел
 *  Визуально представляет из себя 2 выпадающих селекта ОТ и ДО */
 
class KFRangeType extends kfiltertyperange {
    
    private static $config = array('RANGE_SEPARATOR' => '-');

    function addVariants(&$field) {  
        if($_REQUEST[$field['NAME'] . '_FROM'] || $_REQUEST[$field['NAME'] . '_TO']) {
            $this->checkToFromWasLess($field); 
            if($_REQUEST[$field['NAME'] . '_FROM'] == $_REQUEST[$field['NAME'] . '_TO']) { 
                $field['VALUE']['FROM'] = $field['VALUE']['TO'] = $_REQUEST[$field['NAME'] . '_FROM']; 
                $this->filter['PROPERTY_' . $field['NAME']] = $_REQUEST[$field['NAME'] . '_FROM']; 
            } 
        }
        foreach (array('FROM', 'TO') as $key) {
            if(!is_array($field[$key])) {
                $tmp = explode(self::$config['RANGE_SEPARATOR'], $field[$key]);
                if(count($tmp) != 2)
                    return;
                $field[$key] = array();
                $field[$key]['START'] = $tmp[0];
                $field[$key]['END'] = $tmp[1];  
            }
            for ($i = $field[$key]['START']; $i <= $field[$key]['END']; $i++) {                
                $tmparr = array('ID' => $i, 
                                'NAME' => $i );
                if($_REQUEST[$field['NAME'] . '_' . $key] == $i){
                    $tmparr['SELECTED'] = 'Y';
                    if(!$this->filter['PROPERTY_' . $field['NAME']]) {
                        $this->filter[ (($key == 'FROM') ? '>=' : '<=') .'PROPERTY_' . $field['NAME'] ] = $i;
                    }
                }
                $field['VARIANTS'][$key][] = $tmparr;
            } 
        }
    }

} 