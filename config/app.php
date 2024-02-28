<?php

$resourcesPath = "../resources";
$storagePath = "./storage";
$viewsPath = "$resourcesPath/views";
$controllersPath = "../app/Controllers";

    $controllerPath = "$appPath/controllers";
    $vuesPath = "$resourcesPath/vues";

    $page = filter_input(INPUT_GET, "page") !== null ? htmlspecialchars(filter_input(INPUT_GET, "page")) : 'home';

$languages = getLangKeys("fr");

    $lang = require_once("$resourcesPath/lang/fr.php");
