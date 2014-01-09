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
            case 'HIBLOCK': ?>




            <?
            break;
        } // switch ($filter['SOURCE']) { 
     } // foreach ($arResult['FILTERS'] as $filter) { 
 ?>  
</form>