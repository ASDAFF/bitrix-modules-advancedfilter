<?php

/* Фильтрация по разделам привязанного элемента
 * например если инфоблок автомобилей привязан к инфоблоку автосалонов
 * и эти автосалоны лежат в разделах с названиями городов то 
 * эта фильтрация автомобилей по городам их автосалонов */

class KFSectByLinkElement extends kfiltertype {
     
    static $cacheSubdir = '/sectbylinkedelement';

    public function isExcluded() {
        return true;
    }
    
    public function validate(&$field) {
        $_REQUEST[$field['NAME']] = intval($_REQUEST[$field['NAME']]);
    }

    public function addVariants(&$field, $params) {
        if(!$params["LINK_IBLOCK_ID"])
            return;
        $cacheDir = KFilter::$config['CACHE_DIR'] . self::$cacheSubdir;
        $obCache = new CPHPCache;
        if($obCache->InitCache(KFilter::$config['CACHE_TIME'],
                               md5($params["LINK_IBLOCK_ID"] . __METHOD__),
                               $cacheDir)) {
            $field['VARIANTS'] = $obCache->GetVars(); 
        } elseif($obCache->StartDataCache()) {
            CModule::IncludeModule('iblock'); 
            global $CACHE_MANAGER;
            $CACHE_MANAGER->StartTagCache($cacheDir);
            $CACHE_MANAGER->RegisterTag("iblock_id_" . $params["LINK_IBLOCK_ID"]); 
            $CACHE_MANAGER->EndTagCache();
            $rsSect = CIBlockSection::GetList(array('sort' => 'asc', 'name' => 'asc'),
                                              array('IBLOCK_ID' => $params["LINK_IBLOCK_ID"], 'ACTIVE' => 'Y'),
                                              false,
                                              array('ID', 'NAME'));
            while ($arSect = $rsSect->GetNext()) {
                $field['VARIANTS'][] = array('ID'   => $arSect['ID'], 
                                             'NAME' => $arSect['NAME']);
            }
            $obCache->EndDataCache($field['VARIANTS']); 
        }
        foreach($field['VARIANTS'] as &$section) {
            if($_REQUEST[$field['NAME']] == $section['ID']) {
                $section['SELECTED'] = 'Y';
                $this->filter['PROPERTY_' . $field['PROPERTY'] . '.IBLOCK_SECTION_ID'] = $section['ID'];
                break;
            }
        } 
    }
 
} 