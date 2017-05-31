<?php

$api = isset($api)?$api:0;

$lang = isset($lang)?$lang:'ru';

$layoutType = isset($layoutType)?$layoutType:'horizontal';
//подключаем языковий конфиг
$vocab = [];

require_once MODX_BASE_PATH.'assets/snippets/compare/compare.php';

if(!empty($_GET['delete'])){
    compare::delete($_GET['delete']);
}
$data = $_COOKIE['compare_ids'];
$data = json_decode($data,true);

$group = [];
if(is_array($data)){
    foreach ($data as $parent=> $el) {
        if(empty($el)){
            continue;
        }
        foreach ($el as $id=> $e) {
            if(empty($e)){
                continue;
            }
            $group[$parent][]=$id;


        }
    }
}

var_dump($group);
if(empty($group)){
    $compare = new compare($modx,$params,$lang,$layoutType,[]);
    echo $compare->vocab['emptyConfig'];
    return '';
}

//обертка блока из сранениям
$compareOwner = isset($compareOwner)?$compareOwner:'@CODE:<div><h3>[+title+]</h3><p>[+messages+]</p>[+wrapper+]</div>';
$compareOwner = $modx->getTpl($compareOwner);

if(is_array($group)){
    foreach ($group as $key=> $elements){
        $title = $modx->runSnippet('DocInfo',['docid'=>$key]);

        $compare = new compare($modx,$params,$lang,$layoutType,$elements);
        if(count($compare->ids)<=1){
            echo $modx->parseText($compareOwner,['title'=>$title,'messages'=>$compare->vocab['empty']]);
            continue;
        }


        if(empty($compare->tvs)){

            echo $modx->parseText($compareOwner,['title'=>$title,'messages'=>$compare->vocab['emptyConfig']]);
            continue;
        }

        $wrapper = $compare->render();
        echo $modx->parseText($compareOwner,['title'=>$title,'wrapper'=>$wrapper]);

    }
}

