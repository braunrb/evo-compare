<?php

class compare
{
    public $modx;
    public $ids;
    public $data;
    public $tvs;
    public $categories;
    public $defaultValue;
    public $layoutType;
    public $vocab = [];
    public $config = [
        'showUniqueValues'=>0,//показывать только уникальние значения параметров
        'group'=>0, //групировать по категориям
        'requiredTV'=>'', // Обезательние тв поля для выборки DocLister (картинка товара и т.д.)
        'tvList'=>'',//список тв для сравнения
    ];

    public $prepareTemplate = ['paramBlockOuter','blockOuter','ownerTpl','firstRowTpl','paramsFirstBlockTpl','itemTpl','rowTpl','paramTpl','paramNameTpl','groupOuterTpl','groupRowTpl'];
    //шаблон горизонтальной верстки
    public $horizontal_config = [
        'ownerTpl'=>'@CODE:<table border="1px" style="border-collapse: collapse;padding: 10px;">[+wrapper+]</table>',
        'firstRowTpl'=>'@CODE:<tr class="first">[+wrapper+]</tr>',
        'paramsFirstBlockTpl'=>'@CODE:<td>[+paramCaption+]</td>',
        'itemTpl'=> '@CODE:<td>[+pagetitle+]<br> <img src="[[phpthumb? &input=`[+image+]` &options=`w=100,h=100,far=C,bg=ffff`]]"><br><a href="[~[*id*]~]?delete=[+id+]"><Удалить></Удалить></a> </td>',
        'rowTpl'=>'@CODE:<tr class="[+class+]">[+wrapper+]</tr>',
        'paramNameTpl'=>'@CODE:<td>[+name+]</td>',
        'paramTpl'=>'@CODE:<td>[+value+]</td>',
        'groupOuterTpl'=>'@CODE:<tr class="[+class+]">[+wrapper+]</tr>',  //шаблон обертка строки з названием групы
        'groupRowTpl'=>'@CODE:<td colspan="[+count+]"><b>[+name+]</b></td>',  //шаблон ячейки з названием групы
    ];


    public $vertical_config = [
        'ownerTpl'=>'@CODE:<div class="compare-wrap">[+wrapper+]</div>',  //главная обертка
        'blockOuter'=>'@CODE:<div class="compare-item">[+item+][+tvs+]</div>', //обертка блока с товаром и эго свойствами
        'itemTpl'=> '@CODE:<div class="compare-item-info">[+pagetitle+]<br> <img src="[[phpthumb? &input=`[+image+]` &options=`w=100,h=100,far=C,bg=ffff`]]"><br><a href="[~[*id*]~]?delete=[+id+]">Удалить</a> </div>', //блок сверху с информацией об товаре
        'paramBlockOuter'=>'@CODE:<ul class="compare-values-outer">[+wrapper+]</ul>',// обертка блока с списком ив полей
        'paramTpl'=>'@CODE:<li><p>[+name+]</p><p>[+value+]</p></li>',// Блок из значением тв поля


    ];

    static function delete($id){
        $flag = true;
        $id = intval($id);
        $resp = $_COOKIE['compare_ids'];
        $resp = json_decode($resp,true);

        foreach ($resp as $parent => $arr) {
            foreach ($arr as $_id => $val) {
                if (!empty($resp[$parent][$_id]) && $_id == $id) {
                    unset($resp[$parent][$id]);
                    $flag = false;
                }
            }
        }
        if ($flag) {return false;}
        $json = str_replace('}}','} }',json_encode($resp,JSON_FORCE_OBJECT)); // MODx fix
        //$_COOKIE['compare_ids'] = $json;
        setcookie ("compare_ids", $json, time() + 3600*24*30,'/');
        header('Location: '.$_SERVER['REDIRECT_URL']);
        exit;
    }
    public function __construct($modx,$params,$lang,$layoutType,$items)
    {
        $this->modx = $modx;
        $this->layoutType = $layoutType;
        $this->loadLexicon($lang);
        $this->getItems($items);

        $this->config = array_merge($this->config,$params);
        if($this->layoutType=='horizontal'){
            $this->config = array_merge($this->horizontal_config,$this->config);
        }
        elseif ($this->layoutType=='vertical'){
            $this->config = array_merge($this->vertical_config,$this->config);
        }

        $this->prepareTemplate();
        $this->getConfig();
        $this->getItemsData();
        $this->getDefaultTVValues();
        $this->getCategories();

    }

    public function getCategories(){
        //categories
        $table = $this->modx->getFullTableName('categories');
        $resp = $this->modx->db->makeArray($this->modx->db->query("select * from ".$table." order by rank"));
        if(is_array($resp)){
            foreach ($resp as $item) {
                $name = $item['category'];
                if(!empty($this->modx->getConfig('__cp_cat_'.$item['id']))){
                    $name = $this->modx->getConfig('__cp_cat_'.$item['id']);
                }
                $this->categories[$item['id']] = [
                    'name'=>$name
                ];
            }
        }
    }
    public function prepareTemplate(){
        foreach ($this->prepareTemplate as $item) {
            $this->config[$item]=$this->modx->getTpl($this->config[$item]);
        }
    }
    public function getItemsData(){
        if(empty($this->tvs)){
            return ;
        }
        $tvs = implode(',',$this->tvs);
        if(!empty($this->config['requiredTV'])){
            $tvs .= ','.$this->config['requiredTV'];
        }

        //получаем товары из базы
        $data = $this->modx->runSnippet('DocLister', [
            'api' => '1',
            'documents' => implode(',', $this->ids),
            'tvList'=>$tvs,
            'tvPrefix'=>''
        ]);
        $this->data = json_decode($data, true);


    }
    public function render(){
        if($this->layoutType=='horizontal'){
            return $this->renderHorizontal();
        }
        elseif ($this->layoutType=='vertical'){
            return $this->renderVertical();
        }
    }
    //полумаем список товаров из
    public function getItems($items){

        $resp = $items;

        $ids = [];
        if(is_array($resp)){
            foreach ($resp as $key=> $re){
                $ids[$re]=intval($re);
                if(empty($firstItem)){
                    $firstItem = $re;
                }
            }
        }
        $this->ids = $ids;


 ;


    }
    //загрузка мульязичного словаря
    public function loadLexicon($lang){

        $vocab = [];

        if(file_exists(MODX_BASE_PATH.'assets/snippets/compare/lang/'.$lang.'.php')){
            require MODX_BASE_PATH.'assets/snippets/compare/lang/'.$lang.'.php';

        }
        else{

        }
        $this->vocab = $vocab;



    }
    //поиск списка тв для сравнение в родительских категориях по дереве вверх
    public function getConfig(){
        $items = [];
        if(!empty($this->config['tvList'])) {
            $resp  =  $this->config['tvList'];
            $tvNames = [];
            foreach (explode(',',$resp) as $item) {
                $item = $this->modx->db->escape($item);
                $tvNames[] = "'".$item."'";
            }
            $tvNames = implode(',',$tvNames);


            $sql = "select id from ".$this->modx->getFullTableName('site_tmplvars')." where name in (".$tvNames.")";
            $q = $this->modx->db->query($sql);
            $resp = $this->modx->db->makeArray($q);

            foreach ($resp as $re){
                $items []=$re['id'];
            }
        }
        elseif(!empty($this->config['tvCategory'])){

            $categories = explode(',',$this->config['tvCategory']);
            $categoryIds = [];
            foreach ($categories as $category) {
                $categoryIds[] = intval($category);
            }
            $categoryIds = implode(',',$categoryIds);

            $sql = "select id from ".$this->modx->getFullTableName('site_tmplvars')." where category in (".$categoryIds.")";
            $q = $this->modx->db->query($sql);
            $resp = $this->modx->db->makeArray($q);

            foreach ($resp as $re){
                $items []=$re['id'];
            }
        }
        else{



            //----
            $firstItem = $this->ids[key($this->ids)];
            $config = '';
            while (1) {
                $parent = $this->modx->runSnippet('DocInfo', ['field' => 'parent', 'docid' => $firstItem]);
                $value = $this->modx->runSnippet('DocInfo', ['field' => 'compare', 'docid' => $parent]);
                $value = json_decode($value, true);
                if (!empty($value)) {
                    $config = $value['fieldValue'];
                    break;
                }
                if ($parent == 0) {
                    break;
                }
                $firstItem = $parent;
            }

            $items = [];
            if (is_array($config)) {
                foreach ($config as $el) {
                    $items[] = $el['dropdown'];
                }
            } else {
                return '';
            }

        }
        $sortType = 'none';
        $orderBy = 'category';
        if($this->config['group'] == '1'){
            $orderBy = 'category';
        }
        else{
            $sortType = 'doclist';
            $orderBy = '';
        }
        $resp = $this->modx->runSnippet('DocLister',[
            'controller'=>'onetable',
            'table'=>'site_tmplvars',
            'idType'=>'documents',
            'documents'=>implode(',',$items),
            'api'=>1,
            'orderBy'=>$orderBy,
            'sortType'=>$sortType,
        ]);
        $tvs = [];
        $resp = json_decode($resp,true);

        foreach ($resp as $el) {
            $tvCaption = $el['caption'];

            if(!empty($this->modx->getConfig('__cp_'.$el['name']))){
                $tvCaption = $this->modx->getConfig('__cp_'.$el['name']);
            }
            $tvs[$el['category']][$el['id']]  = [
                'id'=>$el['id'],
                'type'=>$el['type'],
                'name'=>$el['name'],
                'caption'=>$tvCaption,
                'elements'=>$el['elements'],
            ];
            $this->tvs[]=$el['name'];
        }

        $this->config['tvs']=$tvs;
    }
    public function getDefaultTVValues($array = array())
    {
        if(empty($this->tvs)){
            return ;
        }
        $out = [];
        foreach ($this->config['tvs'] as $tv) {
            foreach ($tv as $tv_id => $element) {
                $element = $element['elements'];
                if (stristr($element, "@EVAL")) {
                    $element = trim(substr($element, 6));
                    $element = str_replace("\$modx->", "\$this->modx->", $element);

                    $element = eval($element);

                }
                if ($element != '') {
                    $tmp = explode("||", $element);
                    foreach ($tmp as $v) {
                        $tmp2 = explode("==", $v);
                        $key = isset($tmp2[1]) && $tmp2[1] != '' ? $tmp2[1] : $tmp2[0];
                        $value = $tmp2[0];
                        if ($key != '') {
                            $out[$tv_id][$key] = $value;
                        }
                    }
                }
            }
        }

        $this->defaultValue = $out;
    }
    public function prepareValue($tv,$el){
        $value =  $el[$tv['name']];
        $out = [];
        switch ($tv['type']){
            case 'listbox-multiple':
            case 'checkbox':
                $out = explode('||',$value);
                foreach ($out as $key=>$el){
                    if(!empty($this->defaultValue[$tv['id']][$el])){
                        $out[$key] = $this->defaultValue[$tv['id']][$el];
                    }
                }
                break;
            case 'option':
            case 'listbox':
            case 'dropdown':
                $out[]=$value;
                if(!empty($this->defaultValue[$tv['id']][$value])){
                    $out[0] = $this->defaultValue[$tv['id']][$value];
                }
                break;

            default:
                $out[]=$value;
                break;
        }
        return implode(', ',$out);

    }

    public function checkUnique($tv){ //проверка онукальноси параметров
        $tvName = $tv['name'];
        $old = 0;
        foreach ($this->data as $key =>$el){

           if(empty($old)){

               $old = $el[$tvName];
           }
           else{
               if($old!=$el[$tvName]){
                   return true;
               }
               else{
                   $old = $el[$tvName];
               }
           }


        }

        return false;
    }
    public function renderHorizontal(){
        $ind = 0;
        $trStr = '';
        $tdStr = '';


        //шапка
        $paramArray = ['paramCaption'=>$this->vocab['paramCaption']];
        $tdStr .= $this->modx->parseText($this->config['paramsFirstBlockTpl'],$paramArray);
        foreach ($this->data as $el) {
            $tdStr .= $this->modx->parseText($this->config['itemTpl'],array_merge($el));

        }
        $trStr .= $this->modx->parseText($this->config['firstRowTpl'],['wrapper'=>$tdStr]);



        /// тело таблицы
        foreach ($this->config['tvs'] as $categoryId => $tvs) {
            $groupTr = '';
            $groupTitleTr = '';


            //Вывод Заголовка категории
            if($this->config['group']==1 && !empty($categoryId)){
                if(!empty($this->categories[$categoryId]['name'])){
                    $array = [
                        'name'=>$this->categories[$categoryId]['name'],
                        'count'=>count($this->ids)+1,

                    ];
                    $groupTitleTd = $this->modx->parseText($this->config['groupRowTpl'], $array);
                    $array = [
                        'wrapper'=>$groupTitleTd,
                        'class'=>$ind % 2==1?'even':'odd'
                    ];
                    $ind ++;
                    $groupTitleTr = $this->modx->parseText($this->config['groupOuterTpl'], $array);
                }
            }

            //Вывод списка свойств
            foreach ($tvs as $tv) {
                if($this->config['showUniqueValues']==1 && $this->checkUnique($tv)==false){
                    continue;
                }
                $paramArray = ['name' => $tv['caption']];
                $tdStr = $this->modx->parseText($this->config['paramNameTpl'], $paramArray);;

                //Переборсвойств товаров
                foreach ($this->data as $el) {
                    $value = $this->prepareValue($tv, $el);
                    $array = [
                        'value' => $value,
                    ];
                    $tdStr .= $this->modx->parseText($this->config['paramTpl'], $array);;
                }
                $array = [
                    'wrapper' => $tdStr,
                    'class'=>$ind % 2==1?'even':'odd'
                ];
                $ind++;
                $groupTr .= $this->modx->parseText($this->config['rowTpl'], $array);

            }
            if(empty($groupTr)){
                continue;
            }
            $trStr .= $groupTitleTr;
            $trStr .= $groupTr;
        }
        $output =  $this->modx->parseText($this->config['ownerTpl'],['wrapper'=>$trStr]);
        return $output;
    }

    public function renderVertical(){

        $items = '';



        //перебор товаров
        foreach ($this->data as $el) {

            $itemInfo  = $this->modx->parseText($this->config['itemTpl'],$el);

            $tvRowStr = '';

            foreach ($this->config['tvs'] as $categoryId => $tvs) {

                foreach ($tvs as $tv) {
                    if($this->config['showUniqueValues']==1 && $this->checkUnique($tv)==false){
                        continue;
                    }
                    $value = $this->prepareValue($tv, $el);

                    $paramArray = [
                        'name'=>$tv['caption'],
                        'value'=>$value,
                    ];
                    $tvRowStr .= $this->modx->parseText($this->config['paramTpl'],$paramArray);
                }
            }


            $paramArray = [
                'wrapper'=>$tvRowStr,
            ];
            $tvBlockStr = $this->modx->parseText($this->config['paramBlockOuter'],$paramArray);

            //обертка блока с товаром и свойствами
            $paramArray = [
                'item'=>$itemInfo,
                'tvs'=>$tvBlockStr,
            ];
            $items.= $this->modx->parseText($this->config['blockOuter'],$paramArray);
        }


        //главная обертка
        $paramArray = [
            'wrapper'=>$items
        ];
        $output = $this->modx->parseText($this->config['ownerTpl'],$paramArray);

        return $output;
    }
    public function renderJSON(){

        $array = [
            'items'=>$this->data,
            'tvs'=>$this->config['tvs'],
            'default'=>$this->defaultValue,
            'categoryName'=>$this->categories,
        ];

        return json_encode($array);
    }


}
