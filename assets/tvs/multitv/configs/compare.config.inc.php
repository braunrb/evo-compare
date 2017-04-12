<?php
$settings['display'] = 'vertical';
$settings['fields'] = array(

    'dropdown' => array(
        'caption' => 'ТВ',
        'type' => 'dropdown',
        'elements' => '@SELECT caption,id FROM [+PREFIX+]site_tmplvars ORDER BY caption ASC'
    ),

);
$settings['templates'] = array(
    'outerTpl' => '<ul>[+wrapper+]</ul>',
    'rowTpl' => '<li>[+text+], [+image+], [+thumb+], [+textarea+], [+date+], [+dropdown+], [+listbox+], [+listbox-multiple+], [+checkbox+], [+option+]</li>'
);
$settings['configuration'] = array(
    'enablePaste' => true,
    'enableClear' => true,
    'csvseparator' => ','
);
