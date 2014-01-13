<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<form method="GET">  
   <? foreach ($arResult['FILTERS'] as $filter) { 
        switch ($filter['SOURCE']) {
            case 'PROPERTY': ?>

 
            <?  
            break;
            case 'SECTIONS': ?>
    
 
            <?       
            break;
            default : 
                $APPLICATION->IncludeComponent("kudin:filter.field",
                                               $filter['SOURCE'], 
                                               array("FILTER" => $filter),
                                               null,
                                               array("HIDE_ICONS" => "Y")); 
            break;
        }
     }
 ?>  
</form>