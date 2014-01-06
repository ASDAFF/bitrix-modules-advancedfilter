<?php

class KFilter {

    private $fields;
    private $iblock_id;
    private $props = array(); 

    private $filterTypes = array('RANGE'      => 'KFRangeType',         // выбор диапазона селектами
                                 'TEXT_RANGE' => 'KFTextRangeType');    // ввод диапазона цифрами
                               
    private $objectsArr = array(); 
    private $filters = array();

    private $obCache;
    private static $cache_time = 3600000; 
    private static $cache_dir = "/kfilter";
            
    function __construct($iblock_id) {
        if(!$iblock_id)
            return false;
        $this->iblock_id = $iblock_id;
            
        $this->obCache = new CPHPCache;
        if($this->obCache->InitCache(self::$cache_time, 
                                     md5($this->iblock_id . __METHOD__),
                                     self::$cache_dir)) {
            $this->props = $this->obCache->GetVars(); 
        } elseif($this->obCache->StartDataCache()) { 
            CModule::IncludeModule('iblock'); 
            global $CACHE_MANAGER;
            $CACHE_MANAGER->StartTagCache(self::$cache_dir);
            $CACHE_MANAGER->RegisterTag("iblock_id_" . $this->iblock_id);
            $CACHE_MANAGER->EndTagCache();
            $properties = CIBlockProperty::GetList(array("sort" => "asc", 
                                                         "name" => "asc"),
                                                   array("ACTIVE" => "Y",
                                                         "IBLOCK_ID" => $this->iblock_id));
            while ($prop_fields = $properties->GetNext()) {
                $this->props[$prop_fields["CODE"]] = array('ID' => $prop_fields['ID'],
                                                           'NAME' => $prop_fields['NAME'],
                                                           'PROPERTY_TYPE' => $prop_fields['PROPERTY_TYPE'],
                                                           'CODE' => $prop_fields['CODE']);
            }
            $this->obCache->EndDataCache($this->props);
        }
    }

    private function validateSourceField(&$field) {
        $field['SOURCE'] = trim(strtoupper($field['SOURCE']));
        if (!$field['SOURCE']) {
            $field['SOURCE'] = 'PROPERTY';
            $field['PROPERTY'] = $field['NAME'];
            return;
        }
        if (strpos($field['SOURCE'], 'PROPERTY_') === 0) {
            $field['PROPERTY'] = substr($field['SOURCE'], strlen('PROPERTY_'), strlen($field['SOURCE']));
            $field['SOURCE'] = 'PROPERTY';
            return;
        } 
        for($i = 1; $i <= 4; $i++) { 
            if($field['SOURCE'] == $i . '_LEVEL_SECTIONS'){
                $field['SOURCE'] = 'SECTIONS';
                $field['DEPTH_LEVEL'] = $i;
                return;
            }
        }
        if(is_object($this->objectsArr[$field['NAME']])){ 
            $this->objectsArr[$field['NAME']]->validate($field);
        }
    }

    function Add($name, $arr) {
        $name = trim($name); 
        if (!$name)
            return $this; 
        
        $arr['NAME'] = $name;

        if (!$arr['VIEW'])
            $arr['VIEW'] = 'VARIANTS';

        if($this->filterTypes[$arr['SOURCE']])
            $this->objectsArr[$arr['NAME']] = new $this->filterTypes[$arr['SOURCE']]();
 
        $this->validateSourceField($arr);
        $this->addVariants($arr);
        $this->fields[$arr['NAME']] = $arr;
        return $this;
    }
            
    private function addSectionVariants(&$field){ 
        if($this->obCache->InitCache(self::$cache_time, 
                                     md5($this->iblock_id . __METHOD__ . $field['DEPTH_LEVEL']),
                                     self::$cache_dir)) {
            $field['VARIANTS'] = $this->obCache->GetVars(); 
        } elseif($this->obCache->StartDataCache()) {
            $arSelect = array('ID', 'NAME');
            if($field['DEPTH_LEVEL'] > 1){
                $arSelect[] = 'IBLOCK_SECTION_ID';
            }
            $db_list = CIBlockSection::GetList(array("SORT" => "ASC", 
                                                    "NAME" => "ASC"), 
                                              array('IBLOCK_ID' => $this->iblock_id,
                                                    'ACTIVE' => 'Y', 
                                                    'DEPTH_LEVEL' => $field['DEPTH_LEVEL']),
                                              false,
                                              $arSelect);  
           while($section = $db_list->Fetch()) {
               $field['VARIANTS'][] = $section;
           }
           $this->obCache->EndDataCache($field['VARIANTS']);
        }
        foreach($field['VARIANTS'] as &$section){
           if($_REQUEST[$field['NAME']] == $section['ID']){
               $section['SELECTED'] = 'Y';
               $this->filters[$field['NAME']]['SECTION_ID'] = $section['ID'];
               break;
           }
        }
    } 

    private function addPropertyEnumVariants(&$field){
        if($this->obCache->InitCache(self::$cache_time, 
                                     md5($this->iblock_id . __METHOD__ . $field['PROPERTY']['ID']),
                                     self::$cache_dir)) {
            $field['VARIANTS'] = $this->obCache->GetVars(); 
        } elseif($this->obCache->StartDataCache()) { 
            $db_enums = CIBlockProperty::GetPropertyEnum($field['PROPERTY']['ID'],
                                                         array("SORT" => "asc",
                                                               "VALUE" => "asc"),
                                                         array("IBLOCK_ID" => $this->iblock_id));
            while ($ar_enum_list = $db_enums->GetNext()) {
                $enum_variant = array('ID' => $ar_enum_list['ID'],
                                      'NAME' => $ar_enum_list['VALUE']); 
                $field['VARIANTS'][] = $enum_variant;
            } 
            $this->obCache->EndDataCache($field['VARIANTS']);
        }
        foreach ($field['VARIANTS'] as &$enum_variant) {
            if($_REQUEST[$field['PROPERTY']['CODE']] == $enum_variant['ID']) {
                $enum_variant['SELECTED'] = 'Y';
                $this->filters[$field['NAME']]['PROPERTY_' . $field['PROPERTY']['CODE']] = $enum_variant['ID'];
                break;
            }
        }
    }

    private function attachPropertyArrToField(&$field){
        if (is_numeric($field['PROPERTY'])) {
            foreach ($this->props as $prop) {
                if ($prop['ID'] == $field['PROPERTY']) {
                    $field['PROPERTY'] = $prop;
                    break;
                }
            }
        } else {
            if (isset($this->props[$field['PROPERTY']]))
                $field['PROPERTY'] = $this->props[$field['PROPERTY']];
        }  
    }
    
    private function addVariants(&$field){ 
        switch ($field['SOURCE']) {
            case 'SECTIONS': 
                $this->addSectionVariants($field);
                break; 
            case 'PROPERTY':
                $this->attachPropertyArrToField($field); 
                switch ($field['PROPERTY']["PROPERTY_TYPE"]) {
                    case 'L':
                        $this->addPropertyEnumVariants($field);
                        break;
                }
                break; 
            default:
                if(is_object($this->objectsArr[$field['NAME']])){
                    $this->objectsArr[$field['NAME']]->addVariants($field);
                } 
                break;
        }
    }

    function registerType($type, $class){
        $this->filterTypes[$type] = $class;
    }    
         
    function GetResult() { 
        return $this->fields; 
    }

    function GetFilter(){
        $filter = array('IBLOCK_ID' => $this->iblock_id);
        foreach ($this->fields as $name => $field) {
            switch ($field['SOURCE']) {
                case 'SECTIONS':
                case 'PROPERTY':
                    $filters = $this->filters[$name];
                    break; 
                default:
                    $filters = $this->objectsArr[$name]->getFilter(); 
                    break;
            } 
            if($filters){
                $filter = array_merge($filters, $filter);
            }
        }
        return $filter;
    }

}