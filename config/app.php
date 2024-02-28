<?php

$resourcesPath = "../resources";
$storagePath = "./storage";
$viewsPath = "$resourcesPath/views";
$controllersPath = "../app/Controllers";

$configs = [
	'routes',
	'languages',
	'auth',
	'logging',
];

foreach ($configs as $config)
{
	require_once ("../config/$config.php");
}

$languages = getLangKeys("fr");

$page = getPage();
$controller = getController();
