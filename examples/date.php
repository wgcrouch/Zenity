<?php

require_once 'Zenity.php';

$zenity = new Zenity();

//Get a date using a calendar:
$date =  $zenity->getDate('Please Select Date', 'Select Date');
print "You selected $date\n";

