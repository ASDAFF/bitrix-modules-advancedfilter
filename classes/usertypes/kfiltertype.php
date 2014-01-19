<?php

abstract class kfiltertype {

    protected $filter = array();
    protected $arrElements = array();
    protected $propertyName = '';
    
    /* первая проверка при добавлении поля, тут же 
     * закидываем $field['PROPERTY'] в $this->propertyName если 
     * хотим использовать отсекание лишних значений свойств... */
    
    public function validate(&$field) {
        $this->propertyName = $field['PROPERTY'];
    }    

    public function getFilter() {
        return $this->filter;
    }
    
    /* Должна вернуть true если для значений фильтров нужно делать выборку
     * элементов чтобы отсечь заведомо пустые варианты */
    
    public function isExcluded() {
        return false;
    }

    /* Добавляет перечисляемые варианты в $field['VARIANTS'], 
     * выделяет выбраные значения, формирует $this->filter */
    
    public function addVariants(&$field) { }   

    /* закидываем в $this->arrElements только то что понадобится
     * для отсекания в последствии лишних значений свойств */
    
    public function addExcludedResult($arr) {
        $this->arrElements[] = $arr;
    }

    /* Удалит значения которые не встречаются в $this->arrElements */
    
    public function Exclude($field) { }

}

abstract class kfiltertyperange extends kfiltertype {
    
    public function validate(&$field) {
        parent::validate($field);
        $_REQUEST[$field['NAME'] . '_FROM'] = intval($_REQUEST[$field['NAME'] . '_FROM']);
        $_REQUEST[$field['NAME'] . '_TO'] = intval($_REQUEST[$field['NAME'] . '_TO']); 
    }
 
    protected function checkToFromWasLess(&$field) {
        if( $_REQUEST[$field['NAME'] . '_FROM'] > $_REQUEST[$field['NAME'] . '_TO'] && $_REQUEST[$field['NAME'] . '_TO'] ){
            $_REQUEST[$field['NAME'] . '_FROM'] = $_REQUEST[$field['NAME'] . '_TO'];
        }
    }

}