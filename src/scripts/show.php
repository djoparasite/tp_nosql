<?php

require '../Manager.php';
require '../../vendor/autoload.php';

if (isset($_GET['id'])) {
    $manager = new \App\Classes\Manager();
    $image = $manager->getPhotoById($_GET['id']);
}


$loader = new \Twig\Loader\FilesystemLoader('../../templates');
$twig = new \Twig\Environment($loader, []);

echo $twig->render('show.html.twig', ['image' => $image[0]]);