<?php

require_once 'Zenity.php';

$zenity = new Zenity();

$data = array(
    array('key' => 1, 'column 1' => 'Item 1', 'column 2' => 'Another Value 1'),
    array('key' => 2, 'column 1' => 'Item 2', 'column 2' => 'Another Value 2'),
    array('key' => 3, 'column 3' => 'Item 3', 'column 2' => 'Another Value 3')
);

//Get a date using a calendar:
$selected =  $zenity->showList('Select from the list', 'Example List', $data);
print $selected;

