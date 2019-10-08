<?php

require 'vendor/autoload.php';
require 'src/Manager.php';


$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader, []);

$manager = new \App\Classes\Manager();
$infos = $manager->getInfosDb();

echo $twig->render('index.html.twig', ['infos' => $infos]);

