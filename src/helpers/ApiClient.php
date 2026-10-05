<?php
namespace helpers;

class ApiClient {
    private string $apiKey;

    public function __construct(string $apiKey) {
        $this->apiKey = $apiKey;
    }

    /**
     * Executes a search query via the Serper.dev API and returns processed results.
     */
    public function search(string $query): array {
        // Initialize cURL session for the API endpoint
        $curlSession = curl_init('https://google.serper.dev/search');
        curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlSession, CURLOPT_POST, true);
        curl_setopt($curlSession, CURLOPT_HTTPHEADER, [
            'X-API-KEY: ' . $this->apiKey,
            'Content-Type: application/json'
        ]);

        // Send the payload requesting 10 results
        $payload = json_encode(['q' => $query, 'num' => 10]);
        curl_setopt($curlSession, CURLOPT_POSTFIELDS, $payload);

        $rawResponse = curl_exec($curlSession);
        $httpCode = curl_getinfo($curlSession, CURLINFO_HTTP_CODE);
        curl_close($curlSession);

        $results = [];
        $errorMessage = null;

        // Parse successful API responses
        if ($httpCode === 200 && !empty($rawResponse)) {
            $data = json_decode($rawResponse, true);
            
            // Extract the organic search results
            if (isset($data['organic']) && is_array($data['organic'])) {
                foreach ($data['organic'] as $item) {
                    $results[] = [
                        'title'   => $item['title'] ?? '',
                        'link'    => $item['link'] ?? '',
                        'snippet' => $item['snippet'] ?? ''
                    ];
                }
            }
        } else {
            // Handle HTTP errors or invalid API keys
            $data = json_decode($rawResponse, true);
            $errorMessage = $data['message'] ?? "API Request failed with HTTP status {$httpCode}.";
        }

        return [
            'results' => $results,
            'error'   => $errorMessage
        ];
    }
}