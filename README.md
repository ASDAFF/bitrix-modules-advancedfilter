advancedfilter
====

<p>Модуль для расширеной фильтрации элементов</p>
<p>Установка:</p>
<pre>cd site/bitrix/modules/ 
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
                     <p>По умолчанию устанавливается в зависимости от текущих настроек свойств инфоблока</p>
                    <p>может принимать следующие значения:</p> 
                    <ul>
                    <li><p>'1_LEVEL_SECTIONS' ... '5_LEVEL_SECTIONS' : разделы выбранного уровня</p>
                    <p>Пример:</p>
<pre>$filterData = new KFilter($iblock_id);
$filterData->Add('MARKA',   array('SOURCE' => '1_LEVEL_SECTIONS'));</pre>
                    </li>
                     
                    <li><p>'PROPERTY' : свойство, при этом допустимы следующие поля:</p>
                          <ul>
                              <li><p>PROPERTY - код свойства для привязки, если он отличим от $filter_name</p></li>
                           </ul>
                    <p>Добавляем свойство KUZOV</p>
<pre>$filterData = new KFilter($iblock_id);
$filterData->Add('KUZOV');</pre>
                    <p>Добавляем свойство KUZOV, но у нас оно будет называться TIP_KUZOVA</p>
<pre>$filterData = new KFilter($iblock_id);
$filterData->Add('TIP_KUZOVA',   array('PROPERTY' => 'KUZOV'));</pre>
                <p>Ещё пример:</p>
<pre>$filterData = new KFilter($iblock_id);
$filterData->Add('KUZOV')
           ->Add('MARKA') 
           ->Add('YEAR' , array('PROPERTY' => 'GOD'))
           ->Add('MODEL');
</pre>
                    </li>
                    <li><p>'RANGE' : 2 селекта для выбора диапазона чисел</p>
                             <p> при этом обрабатываются следующие ключи:</p>
                              <ul>
                                <li><p>'FROM' - диапазон от через знак минус; например 'FROM'=>'1995-2014'</p></li>
                                <li><p>'TO' - диапазон до через знак минус; например 'TO'=>'1990-2014'</p></li>
                                <li><p>'DONT_SELECT_MAX_TO' - если равен 'Y' то по умолчанию поле TO не будет устанавливаться в максимальное</p></li>
                                
                              </ul>
<p>Пример:</p>
<pre>$filterData = new KFilter($iblock_id);
$filterData->Add('YEAR',   array('SOURCE' => 'RANGE', 'FROM'=>'1980-2000' , 'TO'=>'1980-' . date('Y')) );
</pre>
                    </li> 
                     <li><P>'TEXT_RANGE' : 2 инпута для ввода диапазона чисел </P> 
<p>Пример:</p>
<pre>$filterData = new KFilter($iblock_id);
$filterData->Add('PROBEG',  array('SOURCE' => 'TEXT_RANGE'));</pre>
</li>
                    </ul> 
 <P>Когда свойство является справочником Highloadblock допустимы следующие поля:</P>
                           <ul> 
                              <li><p>NAME_FIELD - код поля таблицы для значений вариантов, по умолчанию UF_NAME</p></li>
                              <li><p>ID_FIELD - ид на случай если нужно использовать поле отличное от UF_XML_ID, по умолчанию UF_XML_ID</p></li> 
                          </ul>
                     </li>
 
       </li></ul>
<p>Пример использования:</p>
<pre>CModule::IncludeModule('advancedfilter'); 
$filterData = new KFilter(CARS_IBLOCK_ID);
$filterData->Add('MARKA',   array('SOURCE' => '1_LEVEL_SECTIONS'))
           ->Add('YEAR',    array('SOURCE' => 'RANGE', 'FROM'=>'1980-' . date('Y'), 'TO'=>'1980-' . date('Y')) )
           ->Add('PRICE',   array('SOURCE' => 'TEXT_RANGE'))
           ->Add('KPP')
           ->Add('MODEL',   array('SOURCE' => '2_LEVEL_SECTIONS', 'LINKTO' => 'MARKA'))
           ->Add('TIP_KUZOVA',   array('PROPERTY' => 'KUZOV'))
           ->Add('PROBEG',  array('SOURCE' => 'TEXT_RANGE'))
           ->Add('DVIGATEL')
           ->Add('GOROD')
           ->Add('PRIVOD',  array('PROPERTY'=> 'TIP_PRIVODA')); 

$arResult['FILTERS'] = $filterData->GetResult();
</pre>   
   </li>
   
  <li><p><b>GetResult() - возвращает результат для передачи в шаблон</b></p></li>
        <li><p><b>clearCache() - чистит весь внутренний кеш компонента</b></p>
         <p>статический метод</p>
         <p>Можно использовать когда тегированый кеш инфоблоков не валит кеш (например при обновлении значений свойств через api)</p>
         <p>Пример использования: <pre>KFilter::clearCache()</pre></p>

     </li>
  <li><p><b>GetFilter() - возвращает фильтр для CIblockElement::FetList-a для фильтрации элементов</b></p></li>
    <li>
      <p><b>registerType($type, $class) - регистрирует пользовательский тип фильтра</b></p>
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
}</pre>
</li></ul>
    </li>
</ul>


<p>Класс поддерживает внутреннее CPHPCache тегированое кеширование</p>
 
==== 
