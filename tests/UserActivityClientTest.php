<?php

namespace TpBloomland\UserActivity\Tests;

use PHPUnit\Framework\TestCase;
use TpBloomland\UserActivity\UserActivityClient;
use TpBloomland\UserActivity\Exceptions\ApiException;

class UserActivityClientTest extends TestCase
{
    private UserActivityClient $client;
    private const BASE_URL = 'https://ce7jzbocq1.execute-api.ca-central-1.amazonaws.com/dev';
    private const TEST_USER_ID = 125;
    private const EMPTY_USER_ID = 1;

    protected function setUp(): void
    {
        $this->client = new UserActivityClient(self::BASE_URL);
    }

    /**
     * Test basic activity retrieval for user with data
     */
    public function testGetActivityBasic(): void
    {
        $response = $this->client->getUserActivity(self::TEST_USER_ID);

        $this->assertIsArray($response);
        $this->assertArrayHasKey('message', $response);
        $this->assertArrayHasKey('success', $response);
        $this->assertArrayHasKey('source', $response);
        $this->assertTrue($response['success']);
        $this->assertEquals('Activity summary retrieved', $response['message']);
        $this->assertIsArray($response['source']);
    }

    /**
     * Test activity data structure contains required fields
     */
    public function testGetActivityDataStructure(): void
    {
        $response = $this->client->getUserActivity(self::TEST_USER_ID);

        $this->assertNotEmpty($response['source'], 'User 125 should have activity data');

        $firstRecord = $response['source'][0];
        $this->assertArrayHasKey('date', $firstRecord);
        $this->assertArrayHasKey('totalHits', $firstRecord);
        $this->assertArrayHasKey('hitCost', $firstRecord);
        $this->assertArrayHasKey('balance', $firstRecord);

        // Validate data types
        $this->assertIsString($firstRecord['date']);
        $this->assertIsInt($firstRecord['totalHits']);
        $this->assertIsNumeric($firstRecord['hitCost']);
        $this->assertIsNumeric($firstRecord['balance']);
    }

    /**
     * Test activity retrieval with date range
     */
    public function testGetActivityWithDateRange(): void
    {
        $startDate = '2025-08-01';
        $endDate = '2025-08-31';

        $response = $this->client->getUserActivity(self::TEST_USER_ID, $startDate, $endDate);

        $this->assertTrue($response['success']);
        $this->assertIsArray($response['source']);

        // Verify all dates are within the specified range
        foreach ($response['source'] as $record) {
            $this->assertGreaterThanOrEqual($startDate, $record['date']);
            $this->assertLessThanOrEqual($endDate, $record['date']);
        }
    }

    /**
     * Test activity retrieval with start date only
     */
    public function testGetActivityWithStartDateOnly(): void
    {
        $startDate = '2025-08-01';

        $response = $this->client->getUserActivity(self::TEST_USER_ID, $startDate);

        $this->assertTrue($response['success']);
        $this->assertIsArray($response['source']);

        // Verify all dates are on or after start date
        foreach ($response['source'] as $record) {
            $this->assertGreaterThanOrEqual($startDate, $record['date']);
        }
    }

    /**
     * Test activity retrieval with end date only
     */
    public function testGetActivityWithEndDateOnly(): void
    {
        $endDate = '2025-07-31';

        $response = $this->client->getUserActivity(self::TEST_USER_ID, null, $endDate);

        $this->assertTrue($response['success']);
        $this->assertIsArray($response['source']);

        // Verify all dates are on or before end date
        foreach ($response['source'] as $record) {
            $this->assertLessThanOrEqual($endDate, $record['date']);
        }
    }

    /**
     * Test user with no activity data returns empty array
     */
    public function testGetActivityEmptyUser(): void
    {
        $response = $this->client->getUserActivity(self::EMPTY_USER_ID);

        $this->assertTrue($response['success']);
        $this->assertIsArray($response['source']);
        $this->assertEmpty($response['source'], 'User 1 should have no activity data');
    }

    /**
     * Test missing user ID throws exception
     */
    public function testGetActivityMissingUserId(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->client->getUserActivity(0);
    }

    /**
     * Test schema inspector - describe table
     */
    public function testDescribeTable(): void
    {
        $response = $this->client->describeTable('payment_records');

        $this->assertIsArray($response);
        $this->assertArrayHasKey('success', $response);
        $this->assertTrue($response['success']);
        $this->assertArrayHasKey('source', $response);
        $this->assertIsArray($response['source']);
    }

    /**
     * Test schema inspector - get sample data
     */
    public function testGetSampleData(): void
    {
        $response = $this->client->getSampleData('tp_log', 5);

        $this->assertIsArray($response);
        $this->assertArrayHasKey('success', $response);
        $this->assertTrue($response['success']);
        $this->assertArrayHasKey('source', $response);
        $this->assertIsArray($response['source']);
        $this->assertLessThanOrEqual(5, count($response['source']));
    }

    /**
     * Test schema inspector - sample data default limit
     */
    public function testGetSampleDataDefaultLimit(): void
    {
        $response = $this->client->getSampleData('tp_log');

        $this->assertTrue($response['success']);
        $this->assertIsArray($response['source']);
    }

    /**
     * Test client can be instantiated with custom base URL
     */
    public function testClientCustomBaseUrl(): void
    {
        $customUrl = 'https://custom-api.example.com/v1';
        $client = new UserActivityClient($customUrl);

        $this->assertInstanceOf(UserActivityClient::class, $client);
    }

    /**
     * Test client uses default base URL if none provided
     */
    public function testClientDefaultBaseUrl(): void
    {
        $client = new UserActivityClient();

        $this->assertInstanceOf(UserActivityClient::class, $client);
    }

    /**
     * Test date format validation
     */
    public function testDateFormatValidation(): void
    {
        // This should work fine with valid date format
        $response = $this->client->getUserActivity(self::TEST_USER_ID, '2025-08-01', '2025-08-31');
        $this->assertTrue($response['success']);
    }

    /**
     * Test negative user ID throws exception
     */
    public function testNegativeUserIdThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->client->getUserActivity(-1);
    }

    /**
     * Test invalid table name for describe
     */
    public function testDescribeInvalidTable(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->client->describeTable('');
    }

    /**
     * Test invalid table name for sample data
     */
    public function testSampleDataInvalidTable(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->client->getSampleData('');
    }

    /**
     * Test invalid limit for sample data
     */
    public function testSampleDataInvalidLimit(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->client->getSampleData('tp_log', -1);
    }
}
