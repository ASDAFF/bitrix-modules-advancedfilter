<?php 
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");  
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/advancedfilter/include.php"); 
    
$sTableID = "kfilters"; 
$oSort = new CAdminSorting($sTableID, "ID", "desc");  
$lAdmin = new CAdminList($sTableID, $oSort); 


    
$cData = new KFilterList;
$rsData = $cData->GetAll(array($by=>$order), $arFilter);
    
$rsData = new CAdminResult($rsData, $sTableID);
    
$rsData->NavStart();
    
$lAdmin->NavText($rsData->GetNavPrint('Фильтры'));
    
$lAdmin->AddHeaders(array(
  array(  "id"    =>"ID",
    "content"  =>"ID",
    "sort"    =>"id",
    "align"    =>"right",
    "default"  =>true,
  ),
  array(  "id"    =>"NAME",
    "content"  => 'Название',
    "sort"    =>"name",
    "default"  =>true,
  ),
  array(  "id"    =>"IBLOCK_ID",
    "content"  => 'Инфоблок',
    "sort"    =>"lid",
    "default"  =>true,
  ),
    
));

while($arRes = $rsData->NavNext(true, "f_")):
   
  $row =& $lAdmin->AddRow($f_ID, $arRes); 
 
  $row->AddInputField("NAME", array("size"=>40));
  $row->AddViewField("NAME", '<a href="kfilter_edit.php?ID='.$f_ID.'&lang='.LANG.'">'.$f_NAME.'</a>');
  
 
  $row->AddInputField("SORT", array("size"=>20)); 
    
  $arActions = Array();
    
  $arActions[] = array(
    "ICON"=>"edit",
    "DEFAULT"=>true,
    "TEXT"=>GetMessage("rub_edit"),
    "ACTION"=>$lAdmin->ActionRedirect("rubric_edit.php?ID=".$f_ID)
  );
    
    $arActions[] = array(
      "ICON"=>"delete",
      "TEXT"=>GetMessage("rub_del"),
      "ACTION"=>"if(confirm('".GetMessage('rub_del_conf')."')) ".$lAdmin->ActionDoGroup($f_ID, "delete")
    );
    
  $row->AddActions($arActions);

endwhile;
 
$lAdmin->AddFooter(
  array(
    array("title"=>GetMessage("MAIN_ADMIN_LIST_SELECTED"), "value"=>$rsData->SelectedRowsCount()), // кол-во элементов
    array("counter"=>true, "title"=>GetMessage("MAIN_ADMIN_LIST_CHECKED"), "value"=>"0"), // счетчик выбранных элементов
  )
);

 
$lAdmin->AddGroupActionTable(Array(
  "delete"=>GetMessage("MAIN_ADMIN_LIST_DELETE"),  
  ));

  
$aContext = array(
  array(
    "TEXT"=>'Создать фильтр',
    "LINK"=>"rubric_edit.php?lang=".LANG,
    "TITLE"=>'Создать фильтр',
    "ICON"=>"btn_new",
  ),
);
    
$lAdmin->AddAdminContextMenu($aContext);
    
$lAdmin->CheckListMode();
    
$APPLICATION->SetTitle('Список фильтров');
    
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
    
$lAdmin->DisplayList();
    
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
    