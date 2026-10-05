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
$apiKey = '91b06491c1e3ef8da8dfe0e53287ea4cb78b7c32';

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