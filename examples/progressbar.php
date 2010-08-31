<?php

require_once 'Zenity.php';

$zenity = new Zenity();

$zenity->showProgress();
for ($i = 1; $i <= 10; $i++) {
    sleep(1);
    $zenity->updateProgress($i*10,'Testing Progress - ' . $i*10 . '%');

}
$zenity->closeProgress();