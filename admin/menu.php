<?php
 
return array(
    "parent_menu" => "global_menu_services",
    "sort" => "10", 
    "text" => 'Фильтры',
    "title" => 'Фильтры',
    "icon" => "advancedfilter-main",
    "page_icon" => "advancedfilter-main",
    "items_id" => "advancedfilter", 
    "items" => array(
        array( 
            "url"         => "kfilter_list.php?&lang=".LANGUAGE_ID, 
            "text"        => 'Список фильтров', 
            "title"       => 'Список фильтров',
            "icon"        => "advancedfilter-list", 
            "page_icon"   => "advancedfilter-list", 
            "items_id"    => "advancedfilterlist"   
            ), 
        array( 
            "url"         => "kfilter_help.php?&lang=".LANGUAGE_ID, 
            "text"        => 'Помощь', 
            "title"       => 'Помощь',
            "icon"        => "advancedfilter-help", 
            "page_icon"   => "advancedfilter-help", 
            "items_id"    => "advancedfilterhelp"   
            ), 
       )
);
 