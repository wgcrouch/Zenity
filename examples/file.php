<?php

require_once 'Zenity.php';

$zenity = new Zenity();

//Get a file path using a file dialogue
$path =  $zenity->getFile('Please Select a file');
print "You selected " . implode(', ', $path) . "\n";

//Getting Multiple File Paths:
$paths =  $zenity->getFile('Please Select files', true);
print "You selected " . implode(', ', $paths) . "\n";
