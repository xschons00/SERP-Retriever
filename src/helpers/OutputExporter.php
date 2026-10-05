<?php
namespace helpers;

class OutputExporter {
    
    /**
     * Formats data into a pretty-printed JSON string.
     */
    public function toJson(string $searchQuery, array $searchResults): string {
        $payload = [
            'query' => $searchQuery,
            'results' => $searchResults
        ];
        
        return json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    /**
     * Formats data into a valid XML structure.
     */
    public function toXml(string $searchQuery, array $searchResults): string {
        $xmlObject = new \SimpleXMLElement('<SearchResponse/>');
        $xmlObject->addChild('Query', htmlspecialchars($searchQuery, ENT_XML1, 'UTF-8'));
        $itemsNode = $xmlObject->addChild('Results');
        
        foreach ($searchResults as $item) {
            $resultNode = $itemsNode->addChild('Result');
            // SimpleXMLElement automatically escapes values passed as the second argument
            $resultNode->addChild('Title', $item['title']);
            $resultNode->addChild('Link', $item['link']);
            $resultNode->addChild('Snippet', $item['snippet']);
        }
        
        return $xmlObject->asXML();
    }

    /**
     * Formats data into standard Markdown text.
     */
    public function toMarkdown(string $searchQuery, array $searchResults): string {
        $markdownData = "# Search Results for: " . $searchQuery . "\n\n";
        
        foreach ($searchResults as $index => $item) {
            $currentIndex = $index + 1;
            $markdownData .= "### " . $currentIndex . ". [" . $item['title'] . "](" . $item['link'] . ")\n";
            $markdownData .= "> " . $item['snippet'] . "\n\n";
        }
        
        return $markdownData;
    }
}