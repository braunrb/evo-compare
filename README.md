### Сравнение


#### Преимущеста

1. Быстрая установка и настройка
2. Поддержка blang
3. Поддержка горизонтальной верстки (таблица) и вертикальной (блоки)
4. Удобная настройка списка тв полей для сравнения с возможностью задавать свои поля для разных категорий
5. Группировка тв по категориям
6. Подстановка значений из дерева документов
7. Возможность скрыть одинаковые параметры

##### Параметри плагина

* max - Максимальное количество файлов для сравнения
* js - Подключать js
* css - Подключать css
* lang - Язык (ru/en/ua)
* active - Клас, который указывает, что элемент находится в списке сравнения (не реализовано в compare.js).
* compareSelector - Селектор элементов для сравнения
* compareCount - Блок с количеством элементов в сравнении




Чтобы сравнение работало во фронтенде, досточно для кнопки или ссылки добавить class **to-compare** и
атрибут **data-id=_id_**, где **_id_** это идентификатор товара.

Пример:
```<a class="to-compare" data-id="5">Добавить в сравнение</a>```

Для элемента с количеством товаров в сравнении необходимо задать **class="compare-count"**.

Пример:
```<div>Количество товаров в сравнении <span class="compare-count"> </span></div>```

После загрузки страницы скрипт пропишет активный клас для элементов, которые уже есть в списке сравнения, а также добавит дополнительные атрибуты принадлежности к родительским категориям (группам). Список сравнения хранится в cookie.

### Конфигурация
Задать список тв полей для сравнение можно несколькими способами:
1. Перечислив их в параметре **tvList**
2. Задать в родительском документе в multiTV **compare**
3. Задать в параметре *tvCategory* id категорий тв параметров через запятую



### Вывод списка товаров
Для вывода списка товаров необходимо на странице вызвать сниппет **compare**:

* lang - язык (ru/en/ua)
* group - выводить название групп  (1/0)
* showUniqueValues - Показывать только тв из разнымы значениями (0/1). По умолчанию: 0.
* requiredTV - TV-параметры выборки DocLister (image, desc и т.д.) для подстановки в шаблон вывода.
* layoutType - Тип верстки (vertical/horizontal). По умолчанию horizontal.
* api - Если равно 1, то вернет список товаров, тв полей, и значей по умолчанию в json-формате.

#### Шаблоны горизонтальной верстки

    ownerTpl - Шаблон, в который оборачивается весь список товаров
    Значение по умолчанию: @CODE:<table border="1px" style="border-collapse: collapse;padding: 10px;">[+wrapper+]</table>

    firstRowTpl  - Шаблон, в который оборачивается строка списка товаров  
    Значение по умолчанию: @CODE:<tr class="first">[+wrapper+]</tr>

    paramsFirstBlockTpl - Первая ячейка таблицы после которой идет товары
    Значение по умолчанию: @CODE:<td>[+paramCaption+]</td>

    itemTpl - Шаблон ячейки с информацией о товаре  
    Значение по умолчанию: @CODE:<td>[+pagetitle+]<br> <img src="[[phpthumb? &input=`[+image+]` &options=`w=100,h=100,far=C,bg=ffff`]]"><br><a href="[~[*id*]~]?delete=[+id+]"><Удалить></Удалить></a> </td>

    rowTpl - Строка с списком значений тв параметров  
    Значение по умолчанию: @CODE:<tr class="[+class+]">[+wrapper+]</tr>

    paramNameTpl - Ячейка из названием тв параметра  
    Значение по умолчанию: @CODE:<td>[+name+]</td>

    paramTpl - Ячейка из значением тв параметра  
    Значение по умолчанию: @CODE:<td>[+value+]</td>

    groupOuterTpl - Обертка стрки из названием группы тв параметров  
    Значение по умолчанию: @CODE:<tr class="[+class+]">[+wrapper+]</tr>

    groupRowTpl - Ячейка из названием группы  
    Значение по умолчанию: @CODE:<td colspan="[+count+]"><b>[+name+]</b></td>

#### Шаблоны верикальной верстки

    ownerTp - Шаблон, в который оборачивается весь список товаров
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
мультияз для названий тв параметров префикс cp_ , синтаксис __cp_название параметра__  
Пример: `cp_ram = 'Оперативная память'`

мультияз для названий категорий параметров префикс cp_ , синтаксис __cp_cat_Ид категории__

Пример: `cp_cat_12 = 'Блок'`



### JS api
*   **clearCompare()** - функция полной очистки сравнения  
*   **deleteFromCompare(id)** - функция добавления в сравнения  
*   **addInCompare(id,parent)** - функция удаления из сравнения (parent по умолчанию равен 1, но чтобы работало корректно, он должен совпадать с id-родительской категории).  
*   **setActive()** - функция проставновки активного класа для элементов в сравнении (можно использовать, если часть контента грузиться через ajax)  

**Callback-функции:**  
*    **afterAddToCompare(id,elem)** - вызывается после добавление в сравнение  
*    **afterDeleteFormCompare(id,elem)** - вызывается после удаления из сравнения  
*    **afterSetDefault(id,elem,status)** - вызывается после того, как скрипт помечает на странице все элементы с класом *.to-compare* как активные. Если статус *true* - элемент в сравнении, иначе нет.  
*    **afterSetCount(count)** - вызывается после того, как в блоке *.compare-count* устанавливается количество элементов в сравнении (первым параметром передает количество элементов в сравнении).  


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
