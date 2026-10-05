<?php
// Require the helper class and import its namespace
require_once __DIR__ . '/../helpers/ApiClient.php';
use helpers\ApiClient;

// Start session to store temporary data across pages
session_start();

$searchQuery = isset($_GET['seachQuery']) ? trim($_GET['seachQuery']) : '';

// Fallback to index if query is empty
if (empty($searchQuery)) {
    header("Location: ../index.html");
    exit();
}
//////////////////////////////////////////////
// Your Serper API Key                      //
//////////////////////////////////////////////
// Secret file location fallbacks for local Docker and Render environments
$secretPaths = [
    '/secrets/api-key',
    '/etc/secrets/api-key',
    __DIR__ . '/../../secrets/api-key'
];

$apiKey = getenv('SERPER_API_KEY') ?: '';

// Attempt reading from secret file paths if environment variable is not set
if (empty($apiKey)) {
    foreach ($secretPaths as $singlePath) {
        if (file_exists($singlePath) && is_readable($singlePath)) {
            $apiKey = trim(file_get_contents($singlePath));
            break;
        }
    }
}
// Handle missing API key to prevent API failure
if (empty($apiKey)) {
    $_SESSION['searchQuery'] = $searchQuery;
    $_SESSION['searchResults'] = [];
    $_SESSION['searchError'] = 'API key error: Secret file is missing or unreadable.';
    header("Location: ../result.php");
    exit();
}

// Initialize API client and fetch results
$apiClient = new ApiClient($apiKey);
$apiResponse = $apiClient->search($searchQuery);

// Store variables into session storage before redirecting
$_SESSION['searchQuery'] = $searchQuery;
$_SESSION['searchResults'] = $apiResponse['results'];
$_SESSION['searchError'] = $apiResponse['error'];

// Redirect browser to the view page
header("Location: ../result.php");
exit();