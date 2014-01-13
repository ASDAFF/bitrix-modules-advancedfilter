<?php

class KFilter {

    private $fields;
    private $iblock_id;
    private $props = array(); 
    
    private static $config = array('CACHE_TIME'                 => 3600000,
                                   'CACHE_DIR'                  => '/kfilter',
                                   'CACHE_TAG'                  => 'kfilter',
                                   'MAX_SECTIONS_DEPTH_LEVEL'   => 5, 
                                   'HIBLOCK_DEFAULT_NAME_FIELD' => 'UF_NAME', 
                                   'HIBLOCK_DEFAULT_ID_FIELD'   => 'UF_XML_ID'); 

    private $filterTypes = array(  'RANGE'           => 'KFRangeType',    
                                   'TEXT_RANGE'      => 'KFTextRangeType',
                                   'SETTED_PROPERTY' => 'KFSettedProperty'); 
        
    private $objectsArr = array(); 
    private $filters = array();

    private $obCache;

    function __construct($iblock_id) {
        if(!$iblock_id)
            return false;
        $this->iblock_id = $iblock_id;
            
        $this->obCache = new CPHPCache;
        if($this->obCache->InitCache(self::$config['CACHE_TIME'], 
                                     md5($this->iblock_id . __METHOD__),
                                     self::$config['CACHE_DIR'])) {
            $this->props = $this->obCache->GetVars(); 
        } elseif($this->obCache->StartDataCache()) { 
            CModule::IncludeModule('iblock'); 
            global $CACHE_MANAGER;
            $CACHE_MANAGER->StartTagCache(self::$config['CACHE_DIR']);
            $CACHE_MANAGER->RegisterTag("iblock_id_" . $this->iblock_id);
            $CACHE_MANAGER->RegisterTag(self::$config['CACHE_TAG']);
            $CACHE_MANAGER->EndTagCache();
            $properties = CIBlockProperty::GetList(array("sort" => "asc"),
                                                   array("ACTIVE" => "Y",
                                                         "IBLOCK_ID" => $this->iblock_id));
            while ($prop_fields = $properties->GetNext()) { 
                $propArr = array('ID' => $prop_fields['ID'],
                                 'PROPERTY_NAME' => $prop_fields['NAME'],
                                 'PROPERTY_TYPE' => $prop_fields['PROPERTY_TYPE'],
                                 'CODE' => $prop_fields['CODE']);
                if($prop_fields["USER_TYPE"])
                    $propArr['USER_TYPE'] = $prop_fields["USER_TYPE"];
                if($prop_fields["USER_TYPE_SETTINGS"]["TABLE_NAME"])
                    $propArr['TABLE_NAME'] = $prop_fields["USER_TYPE_SETTINGS"]["TABLE_NAME"];
                $this->props[$prop_fields["CODE"]] = $propArr;
            } 
            $this->obCache->EndDataCache($this->props);  
        }
    }

    static function clearCache() {
        global $CACHE_MANAGER;
        $CACHE_MANAGER->ClearByTag(self::$config['CACHE_TAG']);
    }
  
    private function validateSourceField(&$field) {
        $field['SOURCE'] = trim(strtoupper($field['SOURCE']));
        $field['PROPERTY'] = $field['PROPERTY'] ? $field['PROPERTY'] : $field['NAME']; 
        if (!$field['SOURCE']) {
            if(isset($this->props[$field['PROPERTY']])){
                $field['SOURCE'] = 'PROPERTY';
                return true;                
            } 
        } elseif($field['SOURCE'] == 'PROPERTY' && !isset($this->props[$field['PROPERTY']])){
            return false;
        }
        for($i = 1; $i <= self::$config['MAX_SECTIONS_DEPTH_LEVEL']; $i++) { 
            if($field['SOURCE'] == $i . '_LEVEL_SECTIONS') {
                $field['SOURCE'] = 'SECTIONS'; 
                $field['DEPTH_LEVEL'] = $i; 
                return true; 
            }
        }
        if($this->filterTypes[$field['SOURCE']]) {
            $this->objectsArr[$field['NAME']] = new $this->filterTypes[$field['SOURCE']]($this->iblock_id);
            $this->objectsArr[$field['NAME']]->validate($field);
            return true;
        } 
        return false;
    }

    function Add($name, $arr) { 
        if ($name = trim($name)) { 
            $arr['NAME'] = $name; 
            if($this->validateSourceField($arr)){
                $this->addVariants($arr);
                $this->fields[$arr['NAME']] = $arr;
            }
        }
        return $this;
    }
            
    private function addSectionVariants(&$field) {
        if($this->obCache->InitCache(self::$config['CACHE_TIME'], 
                                     md5($this->iblock_id . __METHOD__ . $field['DEPTH_LEVEL']),
                                     self::$config['CACHE_DIR'])) {
            $field['VARIANTS'] = $this->obCache->GetVars(); 
        } elseif($this->obCache->StartDataCache()) {
            $arSelect = array('ID', 'NAME');
            if($field['DEPTH_LEVEL'] > 1){
                $arSelect[] = 'IBLOCK_SECTION_ID';
            }
            $db_list = CIBlockSection::GetList(array('SECTION'=> "ASC", "SORT" => "ASC", "NAME" => "ASC"), 
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
        foreach($field['VARIANTS'] as &$section) {
           if($_REQUEST[$field['NAME']] == $section['ID']){
               $section['SELECTED'] = 'Y';
               $this->filters[$field['NAME']]['SECTION_ID'] = $section['ID'];
               break;
           }
        }
    } 

    private function addPropertyEnumVariants(&$field) {
        if($this->obCache->InitCache(self::$config['CACHE_TIME'], 
                                     md5($this->iblock_id . __METHOD__ . $field['PROPERTY']['ID']),
                                     self::$config['CACHE_DIR'])) {
            $field['VARIANTS'] = $this->obCache->GetVars(); 
        } elseif($this->obCache->StartDataCache()) { 
            $db_enums = CIBlockProperty::GetPropertyEnum($field['PROPERTY']['ID'],
                                                         array("SORT" => "asc", "VALUE" => "asc"),
                                                         array("IBLOCK_ID" => $this->iblock_id));
            while ($ar_enum_list = $db_enums->GetNext()) {
                $enum_variant = array('ID' => $ar_enum_list['ID'],
                                      'NAME' => $ar_enum_list['VALUE']); 
                $field['VARIANTS'][] = $enum_variant;
            } 
            $this->obCache->EndDataCache($field['VARIANTS']);
        }
        foreach ($field['VARIANTS'] as &$enum_variant) {
            if($_REQUEST[$field['NAME']] == $enum_variant['ID']) {
                $enum_variant['SELECTED'] = 'Y';
                $this->filters[$field['NAME']]['PROPERTY_' . $field['PROPERTY']['CODE']] = $enum_variant['ID'];
                break;
            } 
        } 
    }

    private function attachPropertyArrToField(&$field) {
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
    
    private function addHIblockVariants(&$field) { 
        if(!$field['NAME_FIELD']) 
            $field['NAME_FIELD'] = self::$config['HIBLOCK_DEFAULT_NAME_FIELD']; 
        if(!$field['ID_FIELD']) 
            $field['ID_FIELD'] = self::$config['HIBLOCK_DEFAULT_ID_FIELD'];
        if($this->obCache->InitCache(self::$config['CACHE_TIME'], 
                                     md5($this->iblock_id . __METHOD__ . $field['ID']),
                                     self::$config['CACHE_DIR'])) {
            $field['VARIANTS'] = $this->obCache->GetVars(); 
        } elseif($this->obCache->StartDataCache()) {
            CModule::IncludeModule('highloadblock'); 
            $filter = array( "filter" => array('TABLE_NAME' => $field['PROPERTY']['TABLE_NAME']) );
            $hlblock = Bitrix\Highloadblock\HighloadBlockTable::getList($filter)->fetch();
            if (isset($hlblock['ID'])) {
                $result = array(); 
                $entity = Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock);
                $entity_data_class = $entity->getDataClass();
                $rsData = $entity_data_class::getList(array());
                while ($arData = $rsData->fetch()) { 
                    $result[] = array('ID' => $arData[$field['ID_FIELD']], 
                                      'NAME' => $arData[$field['NAME_FIELD']]);
                }
                $field['VARIANTS'] = $result; 
            }   
            $this->obCache->EndDataCache($field['VARIANTS']);
        }
        foreach($field['VARIANTS'] as &$variant){
           if($_REQUEST[$field['NAME']] == $variant['ID']){
               $variant['SELECTED'] = 'Y';
               $this->filters[$field['NAME']]['PROPERTY_' . $field['PROPERTY']['CODE']] = $variant['ID'];
               break;
           }
        }
    }
    
    private function addVariants(&$field) {
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
                    case 'S':
                        switch ($field['PROPERTY']["USER_TYPE"]) {
                            case 'directory':
                                $this->addHIblockVariants($field);
                                break;
                            default: 
                            
                                break;
                        }
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

    function registerType($type, $class) {
        $this->filterTypes[$type] = $class;
    }    
         
    function GetResult() { 
        return $this->fields; 
    }

    function GetFilter() {
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
