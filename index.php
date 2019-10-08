<?php

// vendor's dependencies
require 'vendor/autoload.php';
// app dependencies
require 'src/Manager.php';

// Load twig
$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader, []);

$manager = new \App\Classes\Manager();

//get information from db
$infos = $manager->getInfosDb();

// display a template
echo $twig->render('index.html.twig', ['infos' => $infos]);

