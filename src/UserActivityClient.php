<?php

namespace TpBloomland\UserActivity;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use TpBloomland\UserActivity\Exceptions\ApiException;

/**
 * PHP Client for User Activity Summary API
 *
 * Provides methods to interact with the User Activity Summary API endpoints
 * including activity retrieval and schema inspection.
 */
class UserActivityClient
{
    private const DEFAULT_BASE_URL = 'https://ce7jzbocq1.execute-api.ca-central-1.amazonaws.com/dev';

    private Client $httpClient;
    private string $baseUrl;

    /**
     * Create a new UserActivityClient instance
     *
     * @param string|null $baseUrl Optional custom base URL, uses default if not provided
     * @param array $options Optional Guzzle client options
     */
    public function __construct(?string $baseUrl = null, array $options = [])
    {
        $this->baseUrl = $baseUrl ?? self::DEFAULT_BASE_URL;

        // Ensure base URL ends with trailing slash for proper path resolution
        $baseUri = rtrim($this->baseUrl, '/') . '/';

        $defaultOptions = [
            'base_uri' => $baseUri,
            'timeout' => 15.0,
            'headers' => [
                'Accept' => 'application/json',
                'User-Agent' => 'TpBloomland-UserActivity-PHP-Client/1.0',
            ],
        ];

        $this->httpClient = new Client(array_merge($defaultOptions, $options));
    }

    /**
     * Get user activity summary
     *
     * Retrieves daily aggregated activity data for a specific user.
     * Returns date, totalHits, hitCost, and running balance for each day.
     *
     * @param int $userId User ID to retrieve activity for
     * @param string|null $startDate Optional start date filter (YYYY-MM-DD)
     * @param string|null $endDate Optional end date filter (YYYY-MM-DD)
     * @return array Response containing message, success, and source array
     * @throws \InvalidArgumentException If user ID is invalid
     * @throws ApiException If the API request fails
     */
    public function getUserActivity(int $userId, ?string $startDate = null, ?string $endDate = null): array
    {
        if ($userId <= 0) {
            throw new \InvalidArgumentException('User ID must be a positive integer');
        }

        $queryParams = [];

        if ($startDate !== null) {
            $queryParams['start_date'] = $startDate;
        }

        if ($endDate !== null) {
            $queryParams['end_date'] = $endDate;
        }

        $path = "user-activity-summary/{$userId}";

        if (!empty($queryParams)) {
            $path .= '?' . http_build_query($queryParams);
        }

        return $this->makeRequest('GET', $path);
    }

    /**
     * Describe database table schema
     *
     * Returns the schema structure for a specified table including
     * column names, types, and constraints.
     *
     * @param string $tableName Name of the table to describe
     * @return array Response containing table schema information
     * @throws \InvalidArgumentException If table name is empty
     * @throws ApiException If the API request fails
     */
    public function describeTable(string $tableName): array
    {
        if (empty($tableName)) {
            throw new \InvalidArgumentException('Table name cannot be empty');
        }

        return $this->makeRequest('GET', "schema-inspector/describe/{$tableName}");
    }

    /**
     * Get sample data from a table
     *
     * Returns sample rows from the specified table for debugging
     * and understanding data structure.
     *
     * @param string $tableName Name of the table to sample
     * @param int $limit Number of rows to return (default: 5)
     * @return array Response containing sample data
     * @throws \InvalidArgumentException If table name is empty or limit is invalid
     * @throws ApiException If the API request fails
     */
    public function getSampleData(string $tableName, int $limit = 5): array
    {
        if (empty($tableName)) {
            throw new \InvalidArgumentException('Table name cannot be empty');
        }

        if ($limit < 1) {
            throw new \InvalidArgumentException('Limit must be a positive integer');
        }

        $path = "schema-inspector/sample/{$tableName}?limit={$limit}";

        return $this->makeRequest('GET', $path);
    }

    /**
     * Make an HTTP request to the API
     *
     * @param string $method HTTP method (GET, POST, etc.)
     * @param string $path Request path
     * @param array $options Additional Guzzle request options
     * @return array Decoded JSON response
     * @throws ApiException If the request fails
     */
    private function makeRequest(string $method, string $path, array $options = []): array
    {
        try {
            $response = $this->httpClient->request($method, $path, $options);
            $body = (string) $response->getBody();

            return json_decode($body, true, 512, JSON_THROW_ON_ERROR);

        } catch (GuzzleException $e) {
            throw new ApiException(
                "API request failed: {$e->getMessage()}",
                $e->getCode(),
                $e
            );
        } catch (\JsonException $e) {
            throw new ApiException(
                "Failed to decode JSON response: {$e->getMessage()}",
                0,
                $e
            );
        }
    }

    /**
     * Get the base URL being used by this client
     *
     * @return string
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * Get the HTTP client instance
     *
     * @return Client
     */
    public function getHttpClient(): Client
    {
        return $this->httpClient;
    }
}
