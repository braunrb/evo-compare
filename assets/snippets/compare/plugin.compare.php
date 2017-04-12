<?php
$active = isset($active)?$active:'active';   //клас елемента который находиться в сравнении
$compareSelector = isset($compareSelector)?$compareSelector:'.to-compare'; // селектор елементов для сравнения
$compareCount = isset($compareCount)?$compareCount:'#compare-count'; // блок с количеством елементов с равнении

$max = isset($max)?$max:3;
$js = isset($js)?$js:1;
$css = isset($css)?$css:1;
$lang = isset($lang)?$lang:'ru';

if(!empty($_REQUEST['lang']) && in_array($_REQUEST['lang'],['ua','ru','en'])){
    $lang = $_REQUEST['lang'];
}


$e = $modx->event;


switch ($e->name) {
    case 'OnWebPageInit':

        $ru = '
            var c_message = {
                maxMessage:"Максимальное количество товаров для сравнения: (current) / (total)"
            }
        ';
        $ua = '
            var c_message = {
                maxMessage:"Максимальна кількість товарів для порівняння: (current) / (total)"
            }
        ';
        $en = '
            var c_message = {
                maxMessage:"Maximum number of products to compare: (current) / (total)"
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


        if($css){
            $modx->regClientCSS('/assets/snippets/compare/html/compare.css');
        }
        if($js){
            $modx->regClientScript('/assets/snippets/compare/html/compare.js');
        }
        break;
}