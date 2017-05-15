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

$compare = new compare($modx,$params,$lang,$layoutType);

if($api==0){

    if(count($compare->ids)<=1){
        echo $compare->vocab['empty'];
        return '';
    }


    if(empty($compare->tvs)){
        echo $compare->vocab['emptyConfig'];
        return '';
    }

    echo $compare->render();
}
else{

    echo $compare->renderJSON();
}
