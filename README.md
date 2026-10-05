# Search Result Retriever

A lightweight, containerized PHP application that performs web searches via the [Serper.dev](https://serper.dev/) Google Search API and allows users to view and export search results into **JSON**, **XML**, or **Markdown** formats.

---

## Features

- **Google Search Integration**: Connects to the Serper API to retrieve organic search results (titles, links, and snippets).
- **Multiple Export Formats**:
  - **JSON**: Formatted with pretty print and unescaped slashes (`searchResults.json`).
  - **XML**: Structured XML document with `<SearchResponse>`, `<Query>`, and `<Results>` nodes (`searchResults.xml`).
  - **Markdown**: Formatted with headings, numbered lists, links, and blockquote snippets (`searchResults.md`).
- **Clean UI**: Simple and responsive interface for searching, viewing results, and triggering downloads.
- **Dockerized**: Pre-configured with PHP 8.2 CLI and required extensions (`curl`, `dom`), ready to run with Docker Compose.
- **Automated Unit Tests**: Built-in test suite covering data exporters and API client handling.

---

## Project Structure

```text
.
├── Dockerfile                  # Container definition (PHP 8.2 CLI + curl & dom extensions)
├── docker-compose.yml          # Docker Compose service definition exposing port 8080
├── README.md                   # Project documentation
├── src/
│   ├── index.html              # Search input form (landing page)
│   ├── result.php              # Search results view and download triggers
│   ├── style.css               # Application styling
│   ├── helpers/
│   │   ├── ApiClient.php       # Serper API integration & response parsing
│   │   └── OutputExporter.php  # JSON, XML, and Markdown format generators
│   └── scripts/
│       ├── search.php          # Processes query, calls API, stores data in session
│       └── download-content.php# Streams exported search results with download headers
└── test/
    └── tests.php               # Unit test suite using PHP assertions
```

---

## Getting Started

### Option 1: Running with Docker

1. **Clone or navigate to the project directory:**
   ```bash
   cd SERP-Retriever
   ```

2. **Start the application using Docker Compose:**
   ```bash
   docker compose up --build
   ```
   To run in the background (detached mode):
   ```bash
   docker compose up -d --build
   ```

3. **Access the application:**
   Open your browser and navigate to:
   ```
   http://localhost:8080
   ```

4. **Stop the container:**
   ```bash
   docker compose down
   ```

---

### Option 2: Running Locally (Without Docker)

#### Requirements
- **PHP 8.2+**
- PHP Extensions: `curl`, `dom` / `simplexml`

1. **Start the PHP built-in web server:**
   ```bash
   php -S 0.0.0.0:8080 -t src
   ```

2. **Open in browser:**
   ```
   http://localhost:8080
   ```

---

## Configuration

The application uses the Serper.dev API to query Google search results. The API key is configured in `src/scripts/search.php`:

```php
$apiKey = 'your_serper_api_key_here';
```

> **Note:** To obtain an API key, register for a free account at [Serper.dev](https://serper.dev/).

---

## Running Unit Tests

The test suite validates data transformation in `OutputExporter` and error handling in `ApiClient`.

### Run tests inside Docker:
```bash
docker compose exec search-app php test/tests.php
```

### Run tests locally:
```bash
php test/tests.php
```

**Expected output:**
```text
Starting Unit Test Suite...

[1] Testing OutputExporter...
  ✔ toJson() passed
  ✔ toXml() passed
  ✔ toMarkdown() passed

[2] Testing ApiClient...
  ✔ search() return signature passed
  ✔ search() HTTP error handling passed

...All tests completed successfully!
```

---

## Export Formats

When viewing search results on the results page, you can download the data in three formats:

### 1. JSON (`searchResults.json`)
```json
{
    "query": "docker tutorial",
    "results": [
        {
            "title": "Docker Guide",
            "link": "https://docker.com",
            "snippet": "Learn containers."
        }
    ]
}
```

### 2. XML (`searchResults.xml`)
```xml
<?xml version="1.0"?>
<SearchResponse>
  <Query>docker tutorial</Query>
  <Results>
    <Result>
      <Title>Docker Guide</Title>
      <Link>https://docker.com</Link>
      <Snippet>Learn containers.</Snippet>
    </Result>
  </Results>
</SearchResponse>
```

### 3. Markdown (`searchResults.md`)
```markdown
# Search Results for: docker tutorial

### 1. [Docker Guide](https://docker.com)
> Learn containers.
```

---

## Architecture & Code Overview

- **`helpers\ApiClient`** (`src/helpers/ApiClient.php`):
  - Sends HTTP POST requests to `https://google.serper.dev/search`.
  - Parses JSON response payloads and extracts the `organic` results array.
  - Returns a structured array `['results' => [...], 'error' => ...]` with defensive error handling for non-200 HTTP statuses.

- **`helpers\OutputExporter`** (`src/helpers/OutputExporter.php`):
  - `toJson(string $query, array $results)`: Generates pretty-printed JSON.
  - `toXml(string $query, array $results)`: Builds a DOM XML structure using `SimpleXMLElement`.
  - `toMarkdown(string $query, array $results)`: Formats search results as readable Markdown documentation with links and blockquotes.

- **`scripts/search.php`** (`src/scripts/search.php`):
  - Receives the search query via GET parameter `seachQuery`.
  - Stores query and results in `$_SESSION` to persist across page redirects.
  - Redirects to `result.php`.

- **`scripts/download-content.php`** (`src/scripts/download-content.php`):
  - Reads data from session and requested format via GET parameter `downloadFormat` (`json`, `xml`, or `md`).
  - Sets appropriate `Content-Type` and `Content-Disposition: attachment` headers to trigger a file download in the user's browser.
