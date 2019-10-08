<?php

require '../Manager.php';
require '../../vendor/autoload.php';

$query = '';

if (isset($_GET['search'])) {
    $query = $_GET['search'];
}

$manager = new \App\Classes\Manager();
$results = $manager->handle($query);

header('Content-Type: application/json');
echo json_encode($results);

