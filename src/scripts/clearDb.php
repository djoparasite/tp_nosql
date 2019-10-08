<?php

require '../Manager.php';
require '../../vendor/autoload.php';

$manager = new \App\Classes\Manager();
$results = $manager->clearDb();

header('Content-Type: application/json');
echo json_encode($results);

