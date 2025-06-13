<?php
/**
 * WordPress Admin Router
 * Routes admin requests to appropriate pages
 */

// Load WordPress functions fallback
require_once '../includes/wordpress-functions.php';

// Start session
session_start();

// Get the requested page
$page = $_GET['page'] ?? 'dashboard';

// Route to appropriate admin page
switch ($page) {
    case 'wdm-header-settings':
        include 'wdm-header-settings.php';
        break;
    default:
        include 'index.php';
        break;
}
?>