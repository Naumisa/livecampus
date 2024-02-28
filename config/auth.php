<?php
// TODO: Make functions
session_start();
$isLoggedIn = isset($_SESSION['token']) && $_SESSION['token'] != null;

