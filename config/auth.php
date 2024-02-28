<?php
// TODO: Make functions
$isLoggedIn = isset($_SESSION['token']);

if ($isLoggedIn)
{
	$username = $_SESSION['token'];
}

