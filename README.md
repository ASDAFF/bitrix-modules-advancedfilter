advancedfilter
====

<p>Модуль для расширеной фильтрации элементов</p>
<p>Установка:</p>
<pre>  cd site/bitrix/modules/ 
  git clone https://github.com/kudin/bitrix-modules-advancedfilter.git advancedfilter 
</pre>
 
==== 

<p>Класс KFilter предназначен для формирования фильтра.</p>
<p>доступные методы:</p>
<ul>

  <li><p><b>Add(string $filter_name,[ array $config ]) - добавляет поле для фильтрации</b></p> 
     <ul><li>
        <p>$filter_name - произвольный код фильтра</p>
    </li>
     <li>
     <p>$config - массив настроек содержит ключи:</p>
     
       <p>'SOURCE' - тип источника для значений фильтра, от значения этого ключа пляшут остальные ключи.
                     Значение этих других ключей описаны после описания каждого конкретного значения ключа SOURCE  </p>
                     <p>По умолчанию равен PROPERTY_$filter_name</p>
                    <p>может принимать следующие значения:</p> 
                    <ul>
                    <li><p>'1_LEVEL_SECTIONS' : разделы первого уровня</p></li>
                    <li><p>'N_LEVEL_SECTIONS' : разделы N -нного уровня (2_LEVEL_SECTIONS, 3_LEVEL_SECTIONS и т.д...) </p></li> 
                     <li><p>'PROPERTY_КОД_СВОЙСТВА' : свойство с кодом КОД_СВОЙСТВА, вместо КОД_СВОЙСТВА может быть ИД свойства</p> 
                                            
                        </li>
                     <li><p>'RANGE' : 2 селекта для выбора диапазона чисел</p>
                             <p> при этом обрабатываются следующие ключи:</p>
                              <ul>
                                <li><p>'FROM' - диапазон от через знак минус; например 'FROM'=>'1995-2014'</p></li>
                                <li><p>'TO' - диапазон до через знак минус; например 'TO'=>'1990-2014'</p></li>
                              </ul>

                              </li>

                     <li>'TEXT_RANGE' : 2 инпута для ввода диапазона чисел  </li>
                    </ul>
       </li></ul>
   </li>
   
  <li><b>GetResult() - возвращает результат для передачи в шаблон</b></li>
  <li><b>GetFilter() - возвращает фильтр для CIblockElement::FetList-a для фильтрации элементов</b></li>
    <li>
      <b>registerType($type, $class) - регистрирует пользовательский тип фильтра</b>
       <ul>
              <li><p> $type - код фильтра</p></li>
         <li><p>$class - имя класса фильтра</p>
         <p> класс должен наследоваться от абстрактного класса <a href='https://github.com/kudin/bitrix-modules-advancedfilter/blob/master/classes/usertypes/kfiltertype.php'>kfiltertype</a>:</p>
          <pre>abstract class kfiltertype { 
              private $filter = false; 
              function validate(&$field){
                     // первая проверка при добавлении поля  
              }     
              function addVariants(&$field){
                     // метод должен добавить перечисляемые варианты в $field['VARIANTS']
                     // выделить выбраные значения по массиву $_REQUEST
                     // сформировать $this->filter
              }    
              function getFilter(){
                  return $this->filter;
                  // возвращает фильтр 
              }
          }</pre></li></ul>
    </li>
</ul>


<p>Класс поддерживает внутреннее CPHPCache тегированое кеширование</p>

<p>Пример использования:</p>
<pre>
    CModule::IncludeModule('advancedfilter'); 
    $filterData = new KFilter(CARS_IBLOCK_ID);
    $filterData->Add('MARKA',   array('SOURCE' => '1_LEVEL_SECTIONS') )
               ->Add('YEAR',    array('SOURCE' => 'RANGE', 'FROM'=>'1980-2014', 'TO'=>'1990-2014' )  )
               ->Add('PRICE',   array('VIEW'   => 'INPUT'))
               ->Add('KPP'  )
               ->Add('MODEL',   array('SOURCE' => '2_LEVEL_SECTIONS', 'LINKTO' => 'MARKA'))
               ->Add('KUZOV' )
               ->Add('PROBEG',  array('VIEW'   => 'INPUT'))
               ->Add('DVIGATEL')
               ->Add('GOROD')
               ->Add('PRIVOD'); 
              
   $arResult['FILTERS'] = $filterData->GetResult();
</pre>   
  
   
