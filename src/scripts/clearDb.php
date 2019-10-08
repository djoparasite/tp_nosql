<?php

// vendor's dependencies
require '../../vendor/autoload.php';
// app dependencies
require '../Manager.php';

$manager = new \App\Classes\Manager();
$results = $manager->clearDb();

header('Content-Type: application/json');
echo json_encode($results);

