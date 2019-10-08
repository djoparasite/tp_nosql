<?php

// vendor's dependencies
require '../../vendor/autoload.php';
// app dependencies
require '../Manager.php';

$manager = new \App\Classes\Manager();
$image = $manager->getPhotoById($_GET['id']);

// Load twig
$loader = new \Twig\Loader\FilesystemLoader('../../templates');
$twig = new \Twig\Environment($loader, [
    'debug' => true,
]);

$twig->addExtension(new \Twig\Extension\DebugExtension());

$result = $manager->getOtherInformations($image[0]);

echo $twig->render('show.html.twig', ['image' => $result[0]]);
