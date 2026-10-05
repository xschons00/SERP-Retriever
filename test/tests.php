<?php
// Note the added /../ to go up from the 'test' directory into the root, then into 'src'
require_once __DIR__ . '/../src/helpers/OutputExporter.php';
require_once __DIR__ . '/../src/helpers/ApiClient.php';

use helpers\OutputExporter;
use helpers\ApiClient;

// Configure strict assertion behaviors
assert_options(ASSERT_ACTIVE, 1);
assert_options(ASSERT_WARNING, 1);
assert_options(ASSERT_BAIL, 1);

echo "Starting Unit Test Suite...\n\n";

// ==========================================
// TEST pt1: OutputExporter
// ==========================================
echo "[1] Testing OutputExporter...\n";

$outputExporter = new OutputExporter();
$testQuery = "docker tutorial";
$testResults = [
    [
        'title'   => 'Docker Guide',
        'link'    => 'https://docker.com',
        'snippet' => 'Learn containers.'
    ]
];

// Verify JSON encoding
$jsonOutput = $outputExporter->toJson($testQuery, $testResults);
$decodedJson = json_decode($jsonOutput, true);
assert($decodedJson !== null, 'JSON export is invalid');
assert($decodedJson['query'] === $testQuery, 'JSON query parameter mismatch');
assert($decodedJson['results'][0]['title'] === 'Docker Guide', 'JSON title parameter mismatch');
echo "  ✔ toJson() passed\n";

// Verify XML parsing and structure
$xmlOutput = $outputExporter->toXml($testQuery, $testResults);
$xmlObject = simplexml_load_string($xmlOutput);
assert($xmlObject !== false, 'XML export is invalid');
assert((string)$xmlObject->Query === $testQuery, 'XML Query node mismatch');
assert((string)$xmlObject->Results->Result->Title === 'Docker Guide', 'XML Title node mismatch');
echo "  ✔ toXml() passed\n";

// Verify Markdown formatting
$markdownOutput = $outputExporter->toMarkdown($testQuery, $testResults);
assert(strpos($markdownOutput, '# Search Results for: docker tutorial') !== false, 'Markdown header missing');
assert(strpos($markdownOutput, '[Docker Guide](https://docker.com)') !== false, 'Markdown link format invalid');
echo "  ✔ toMarkdown() passed\n";


// ==========================================
// TEST pt2: ApiClient
// ==========================================
echo "\n[2] Testing ApiClient...\n";

// Use a fake API key to safely test network execution and error parsing logic
$fakeApiKey = 'fake_invalid_api_key_123';
$apiClient = new ApiClient($fakeApiKey);

$apiResponse = $apiClient->search('test query');

// Verify return structure signature
assert(is_array($apiResponse), 'ApiClient must return an array');
assert(array_key_exists('results', $apiResponse), 'ApiClient return missing results key');
assert(array_key_exists('error', $apiResponse), 'ApiClient return missing error key');
echo "  ✔ search() return signature passed\n";

// Verify error handling logic
assert(empty($apiResponse['results']), 'Results must be empty when API key is invalid');
assert($apiResponse['error'] !== null, 'Error message must be populated on HTTP failure');
assert(is_string($apiResponse['error']), 'Error message must be a string');
echo "  ✔ search() HTTP error handling passed\n";

echo "\n...All tests completed successfully!\n";