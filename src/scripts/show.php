<?php

// vendor's dependencies
require '../../vendor/autoload.php';
// app dependencies
require '../Manager.php';

if (isset($_GET['id'])) {
    $manager = new \App\Classes\Manager();
    $image = $manager->getPhotoById($_GET['id']);
}

// Load twig
$loader = new \Twig\Loader\FilesystemLoader('../../templates');
$twig = new \Twig\Environment($loader, []);

// display a template
echo $twig->render('show.html.twig', ['image' => $image[0]]);
