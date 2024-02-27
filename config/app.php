<?php
    $appPath = "../app";
    $resourcesPath = "../resources";

    $controllerPath = "$appPath/controllers";
    $vuesPath = "$resourcesPath/vues";

    $page = filter_input(INPUT_GET, "page") !== null ? htmlspecialchars(filter_input(INPUT_GET, "page")) : 'home';

    $controller = file_exists("$controllerPath/$page.php") ? "$controllerPath/$page.php" : null;
    $vue = file_exists("$vuesPath/$page.php") ? "$vuesPath/$page.php" : "$vuesPath/home.php";

    $lang = require_once("$resourcesPath/lang/fr.php");
