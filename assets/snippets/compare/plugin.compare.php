<?php
$active = isset($active)?$active:'active';   //клас елемента который находиться в сравнении
$compareSelector = isset($compareSelector)?$compareSelector:'.to-compare'; // селектор елементов для сравнения
$compareCount = isset($compareCount)?$compareCount:'#compare-count'; // блок с количеством елементов с равнении
$categoryTemplate = isset($categoryTemplate)?$categoryTemplate:''; // блок с количеством елементов с равнении




$max = isset($max)?$max:3;
$js = isset($js)?$js:1;
$css = isset($css)?$css:1;
$lang = isset($lang)?$lang:'ru';

if(!empty($_REQUEST['lang']) && in_array($_REQUEST['lang'],['ua','ru','en'])){
    $lang = $_REQUEST['lang'];
}

global $resp;
$e = $modx->event;

if(!function_exists('compare_parent')){
    function compare_parent($el){

        global $resp;
        $id = $el;
       // echo $id;
        $parent = $resp[$el];
   //     echo $parent['id'];
        if($parent['parent'] == 0 || $parent['compare_top'] == 'yes'){
            return $parent['id'];
        }
        else{
            return compare_parent($parent['parent']);
        }


    }
}
switch ($e->name) {
    case 'OnPageNotFound':
        $start = microtime(true);

        switch ($_GET['q']){
            case 'ajax-compare-add':
                $parent = intval($_GET['parent']);
                $id = intval($_GET['id']);
                $resp = $_COOKIE['compare_ids'];
                $resp = json_decode($resp,true);
                if(!is_array($resp)){
                    $resp = [];
                };
                $resp[$parent][$id] = true;
                $json = str_replace('}}','} }',json_encode($resp,JSON_FORCE_OBJECT)); // MODx fix
                setcookie ("compare_ids",$json , time() + 3600*24*30);
               // var_dump($resp);

                die();
                break;
            case 'ajax-compare-delete':
                $parent = $_GET['parent'];
                $id = $_GET['id'];
                $data = $_COOKIE['compare_ids'];
                $data = json_decode($data,true);
                unset($data[$parent][$id]);
                $json = str_replace('}}','} }',json_encode($data,JSON_FORCE_OBJECT)); // MODx fix
                setcookie ("compare_ids", $json, time() + 3600*24*30);
                die();
                break;
            // полностью очистить список сравнения
            case 'ajax-compare-clear':
                setcookie ("compare_ids", "", time() - 3600);
                die();
                break;
            case 'compare_parent':
                $data = $_GET['data'];

                //получаем все перенты
                $resp = $modx->runSnippet('DocLister',[
                   'api'=>1,
                   'depth'=>4,
                    'selectFields'=>'id,parent',
                    'parents'=>'0',
                    'addWhereList'=>'template = '.$categoryTemplate,
                    'showParent'=>1,
                    'tvList'=>'compare_top,compare_group_name',
                    'tvPrefix'=>''
                ]);
                $resp = json_decode($resp,true);
                $new = [];
                foreach ($data as $el) {
                    $elParent = $modx->runSnippet('DocInfo',['field'=>'parent','docid'=>$el]);
                    $parent = compare_parent($elParent);
                    $new[$el]= ['parent'=>$parent,'title'=>$resp[$parent]['compare_group_name']];
                }

                echo json_encode($new);

                die();
                break;
        }

        break;
    case 'OnWebPageInit':

        $ru = '

            var c_message = {
                maxMessage:"Максимальное количество (group) для сравнения: (current) / (total)"
            }
        ';
        $ua = '
            var c_message = {
                maxMessage:"Максимальна кількість (group) для порівняння: (current) / (total)"
            }
        ';
        $en = '
            var c_message = {
                maxMessage:"Maximum number of (group) to compare: (current) / (total)"
            }
        ';

        switch ($lang){

            case 'en':
                $message = $en;
                break;
            case 'ua':
                $message = $ua;
                break;
            default:
                $message = $ru;

        }

        $modx->regClientScript('<script>
            var c_config = {
                max:'.$max.',
                active:"'.$active.'",
                compareSelector:"'.$compareSelector.'",
                compareCount:"'.$compareCount.'",

            };
            '.$message.'

</script>');

        if($css == 1){
            $modx->regClientCSS('/assets/snippets/compare/html/compare.css');
        }
        if($js == 1){
            $modx->regClientScript('/assets/snippets/compare/html/compare.js');
        }
        break;
}
