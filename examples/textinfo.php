<?php

require_once 'Zenity.php';

$zenity = new Zenity();

//Show this file in a text box
$zenity->showTextInfo(__FILE__, __FILE__);


//Show this file in an editable text box and print the result
$result = $zenity->showTextInfo(__FILE__, 'Edit Me', true);

print "RESULT\n";
print "------\n";
print $result;


