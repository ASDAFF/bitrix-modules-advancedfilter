<?php
CModule::AddAutoloadClasses("advancedfilter",  
                            array( "KFilter"         => "classes/kfilter.php" ,  
                                   "kfiltertype"     => "classes/usertypes/kfiltertype.php" ,
                                   "kfiltertyperange"=> "classes/usertypes/kfiltertype.php" ,
                                   "KFRangeType"     => "classes/usertypes/kfrangetype.php" ,
                                   "KFTextRangeType" => "classes/usertypes/kftextrangetype.php", 
                                   "KFSettedProperty"=> "classes/usertypes/kfsettedproperty.php")     
                           ); 