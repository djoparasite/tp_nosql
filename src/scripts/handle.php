<?php

// vendor's dependencies
require '../../vendor/autoload.php';
// app dependencies
require '../Manager.php';

$query = '';

if (isset($_GET['search'])) {
    $query = $_GET['search'];
}

$manager = new \App\Classes\Manager();
$results = $manager->handle($query);

header('Content-Type: application/json');
echo json_encode($results);

