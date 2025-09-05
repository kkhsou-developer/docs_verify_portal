<?php
if(session_status() == PHP_SESSION_NONE): session_start(); endif;

$route = $_GET['route'] ?? '/';

switch ($route) {
    case '/':
    case 'dashboard':
        require 'pages/dashboard.php';
        break;

    case 'documents':
        require 'pages/documents.php';
        break;

    case 'login':
        require 'pages/login.php';
        break;
    
    case 'logout':
        require 'handlers/logoutHandler.php';
        break;
    
    default:
        require 'pages/404_not_found.php';
        break;
}