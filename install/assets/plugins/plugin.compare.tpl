//<?php
/**
 * compare
 *
 * compare plugin
 *
 * @category    plugin
 * @version     0.1
 * @license     http://www.gnu.org/copyleft/gpl.html GNU Public License (GPL)
 * @internal    @properties &max=Максимальное количество файлов для сравнения;string;3 &js=Покдлючать js;string;1 &css=Покдлючать css;string;1 &lang=Язык;string;ru &active=Клас который вказует что елемент в сравнении;string;active &compareSelector=селектор елементов для сравнения;string;.to-compare &compareCount=Блок с количеством елементов с равнении;string;#compare-count
 * @internal    @events OnWebPageInit
 * @internal    @modx_category shop
 * @internal    @legacy_names compare
 * @internal    @installset base
 *
 * @author Dzhuryn Volodymyr / updated: 2017-04-09


 */


/*
&max=Максимальное количество файлов для сравнения;string;3
&js=Покдлючать js;string;1
&css=Покдлючать css;string;1
&lang=Язык;string;ru
&active=Клас который вказует что елемент в сравнении;string;active
&compareSelector=селектор елементов для сравнения;string;.to-compare //
&compareCount=Блок с количеством елементов с равнении;string;#compare-count

*/


require MODX_BASE_PATH.'assets/snippets/compare/plugin.compare.php';