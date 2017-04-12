### Сравнение


#### Преимущеста

1. Быстрая установка и настройка
2. Поддержка blang
3. Поддержка горизонтальное верстки (таблица) и вертикальное (блоки)
4. Удобная настройка списка тв полей для сравнения з возможностю для разных категорий задавать свои поля
5. Групировка тв по категориям
6. Подстановка значений из девера документов
7. Возможность  скрыть одинаковие параметры

##### Параметри плагина

* max - Максимальное количество файлов для сравнения* 
* js - Покдлючать js
* css=Покдлючать css
* lang=Язык;string;ru
* active=Клас который вказует что елемент в сравнении
* compareSelector=селектор елементов для сравнения
* compareCount=Блок с количеством елементов с равнении;


 

Чтобы сравнение работало в фронтенде досточно для кнопки или ссылки доабавить клас to-compare и
атрибут data-id с ид товара

Пример: <a class="to-compare" data-id="5">Добавить в сравнение</a>

Для елемента с количеством товаров в сравнении необходимо задать id="compare-count"

Пример: <div>Количество товаров в сравнении <span> id="compare-count"></span></div> 

После загрузки страницы скрипт пропишет активный клас для елементов которые уже есть в списке для сравнениея

### Конфигурация
Задать список тв полей для сравнение можна несколькими способами:
1. Перечислив их в параметре tvList
2. Задать в родителском документе в multiTV compare
3. Задать в параметру tvCategory id категорий тв параметров через запятую



### Вывод списка товаров
Для вывода списка товаров необходимо на странице вызвать сниппет compare.

* lang - язык (ru/en/ua)
* group - выводить название груп  (1/0)
* showUniqueValues - Показывать только тв из разнымы значениями (0/1). По умолчанию: 0 
* requiredTV - Тв для обезательной выборки DocLister (Картинка, и т.д.)
* layoutType - Тип верстки (vertical/horizontal). По умолчанию horizontal.
* api - Если поставить один в фомате json вернет список товаров, тв полей, и значей по умолчанию

#### Шаблоны горизонтальной верстки

    ownerTpl - Шаблон в который оборачивается...  
    Значение по умолчанию: @CODE:<table border="1px" style="border-collapse: collapse;padding: 10px;">[+wrapper+]</table>

    firstRowTpl  - Шаблон в который оборачивается строка с список товаров  
    Значение по умолчанию: @CODE:<tr class="first">[+wrapper+]</tr>

    paramsFirstBlockTpl - Первая яцейка таблицы после которой идет товары
    Значение по умолчанию: @CODE:<td>[+paramCaption+]</td>
    
    itemTpl - Шаблон ячейки с информацией о товаре  
    Значение по умолчанию: @CODE:<td>[+pagetitle+]<br> <img src="[[phpthumb? &input=`[+image+]` &options=`w=100,h=100,far=C,bg=ffff`]]"><br><a href="[~[*id*]~]?delete=[+id+]"><Удалить></Удалить></a> </td>
    
    rowTpl - Строка с списком значений тв параметров  
    Значение по умолчанию: @CODE:<tr class="[+class+]">[+wrapper+]</tr>
    
    paramNameTpl - Ячейка из названием тв параметра  
    Значение по умолчанию: @CODE:<td>[+name+]</td>
    
    paramTpl - Ячейка из значением тв параметра  
    Значение по умолчанию: @CODE:<td>[+value+]</td>

    groupOuterTpl - Обертка стрки из названием групы тв параметров  
    Значение по умолчанию: @CODE:<tr class="[+class+]">[+wrapper+]</tr>
    
    groupRowTpl - Ячейка из названием групы  
    Значение по умолчанию: @CODE:<td colspan="[+count+]"><b>[+name+]</b></td>

#### Шаблоны верикальной верстки

    ownerTp - Шаблон в который оборачивается список товаров и их значения
    Значение по умолчанию: @CODE:<div class="compare-wrap">[+wrapper+]</div>
    
    blockOuter - Обертка одного товара и значений тв параметров
    Значение по умолчанию: @CODE:<div class="compare-item">[+item+][+tvs+]</div>
    
    itemTpl - Шаблон вывода информации о товаре
    Значение по умолчанию: @CODE:<div class="compare-item-info">[+pagetitle+]<br> <img src="[[phpthumb? &input=`[+image+]` &options=`w=100,h=100,far=C,bg=ffff`]]"><br><a href="[~[*id*]~]?delete=[+id+]">Удалить</a> </div>
    
    paramBlockOuter - Обертка списка тв параметров
    Значение по умолчанию: @CODE:<ul class="compare-values-outer">[+wrapper+]</ul>
    
    paramTpl - Шаблон значение тв параметра
    Значение по умолчанию: @CODE:<li><p>[+name+]</p><p>[+value+]</p></li>



### bLang
мультияз для названий тв параметров префиес cp_ , синтаксис __cp_название параметра__  
Пример: cp_ram = 'Оперативная память';
    
мультияз для названий категорий параметров префиес cp_ , синтаксис __cp_cat_Ид категории__
Пример: cp_cat_12 = 'Блок';



### JS api
Функция добавления в сравнения: deleteFromCompare(id)  
Функция удаления из сравнения: addInCompare(id)  
Функция для проставноки активного класа: setActive()  //можна использовать если часть контента грузиться аяксом


### Примеры

    [!compare?
        &showUniqueValues=`0` //выводем все свойста
        &layoutType=`vertical` // вертикальная верстка
        &ownerTpl=`@CODE:<ul class="compare-list js-compare-slider">[+wrapper+]</ul>`
        &blockOuter=`@CODE:<li class="compare-list__item">[+item+][+tvs+]</li>`
        &itemTpl=`tpl.compareItem`
        &paramBlockOuter=`@CODE:<ul class="compare-list__descr">[+wrapper+]</ul>`
        &paramTpl=`@CODE:<li class="compare-list__descr-item">
        <span class="compare-list__descr-title">[+name+]</span>
        <span class="compare-list__descr-info">[+value+]</span>
    </li>`
    !]