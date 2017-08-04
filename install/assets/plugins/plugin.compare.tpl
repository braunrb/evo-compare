//<?php
/**
 * compare
 *
 * compare plugin
 *
 * @category    plugin
 * @version     0.2.1
 * @license     http://www.gnu.org/copyleft/gpl.html GNU Public License (GPL)
 * @internal    @properties &max=Максимальное количество файлов для сравнения;string;3 &js=Покдлючать js;string;1 &css=Покдлючать css;string;1 &lang=Язык;string;ru &active=Клас, который указывает, что элемент в сравнении;string;active &compareSelector=селектор элементов для сравнения;string;.to-compare &compareCount=Блок с количеством элементов в сравнении;string;.compare-count &categoryTemplate=Id шаблонов категорий;string;
 * @internal    @events OnWebPageInit,OnPageNotFound
 * @internal    @modx_category shop
 * @internal    @legacy_names compare
 * @internal    @installset base
 *
 * @author Dzhuryn Volodymyr / updated: 2017-04-09
 * @contibutor EGO7000 / updated: 2017-07-24


 */


/*
&max=Максимальное количество файлов для сравнения;string;3
&js=Покдлючать js;string;1
&css=Покдлючать css;string;1
&lang=Язык;string;ru
&active=Клас, который указывает, что элемент в сравнении;string;active
&compareSelector=селектор элементов для сравнения;string;.to-compare //
&compareCount=Блок с количеством элементов в сравнении;string;.compare-count
&categoryTemplate=Id шаблонов категорий;string;

*/


require MODX_BASE_PATH.'assets/snippets/compare/plugin.compare.php';
