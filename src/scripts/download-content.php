<?php
// Require the helper class and import its namespace
require_once __DIR__ . '/../helpers/OutputExporter.php';
use helpers\OutputExporter;

// Start session to retrieve stored data
session_start();

$searchQuery = $_SESSION['searchQuery'] ?? '';
$searchResults = $_SESSION['searchResults'] ?? [];
$downloadFormat = $_GET['downloadFormat'] ?? '';

// Validate that search data exists before attempting an export
if (empty($searchResults)) {
    http_response_code(400);
    exit('No search results available to download.');
}

// Initialize the formatting helper
$outputExporter = new OutputExporter();

// Export data as JSON format
if ($downloadFormat === 'json') {
    header('Content-Type: application/json; charset=utf-8');
    header('Content-Disposition: attachment; filename="searchResults.json"');
    echo $outputExporter->toJson($searchQuery, $searchResults);
    exit();
}

// Export data as XML format
if ($downloadFormat === 'xml') {
    header('Content-Type: application/xml; charset=utf-8');
    header('Content-Disposition: attachment; filename="searchResults.xml"');
    echo $outputExporter->toXml($searchQuery, $searchResults);
    exit();
}

// Export data as Markdown format
if ($downloadFormat === 'md') {
    header('Content-Type: text/markdown; charset=utf-8');
    header('Content-Disposition: attachment; filename="searchResults.md"');
    echo $outputExporter->toMarkdown($searchQuery, $searchResults);
    exit();
}

// Handle unsupported download formats
http_response_code(400);
exit('Invalid download format specified.');