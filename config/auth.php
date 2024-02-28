<?php
// TODO: Make functions
$isLoggedIn = isset($_SESSION['username']);

if ($isLoggedIn)
{
	$username = $_SESSION['username'];
}

