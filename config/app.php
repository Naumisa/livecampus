<?php

/**
 * @return string
 */
function getResourcesPath (): string
{ return "../resources"; }

/**
 * @return string
 */
function getStoragePath (): string
{ return "./storage"; }

/**
 * @return string
 */
function getViewsPath (): string
{ return getResourcesPath() . "/views"; }

/**
 * @return string
 */
function getControllersPath (): string
{ return "../app/Controllers"; }

/**
 * @return string
 */
function getLogFile (): string
{ return "../storage/logs/latest.log"; }

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
