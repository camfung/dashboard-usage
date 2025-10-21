# TP API Reference

Complete API documentation for the Bloomland Traffic Portal API.

**Last Updated:** October 20, 2025
**Version:** 1.2.0

---

## What's New

### October 20, 2025 - v1.2.0

**New Endpoints:**
- ✨ `GET /user-activity-summary/{uid}/by-link` - Get usage aggregated per link/keyword per day

**Updates:**
- ✅ Fixed `usage_records` INSERT bug (line 76)
- ✅ Implemented dual-write to `tp_log` AND `usage_records` for all redirects
- ✅ Updated by-link endpoint to show per-day breakdown (not just totals)
- ✅ Comprehensive testing completed - all tests passing (100%)
- ✅ Consolidated documentation into `/docs` folder
- ✅ Removed temporary deployment and test documentation

**Testing Results:**
- ✅ 8/8 tests passed for by-link endpoint
- ✅ Performance: 57-84ms response times (after cold start)
- ✅ Verified cost calculations with actual product pricing
- ✅ Confirmed per-day aggregation working correctly

**Migration Status:**
- Phase 1: Dual-write implementation ✓ DEPLOYED
- Phase 2: Monitoring consistency (30 days) - In Progress
- Phase 3: Migrate billing to usage_records - Planned

See [`MIGRATION_GUIDE.md`](MIGRATION_GUIDE.md) for full migration details.

---

## Base URL

**Development:** `https://ce7jzbocq1.execute-api.ca-central-1.amazonaws.com/dev`

---

## Quick Links

**User Activity:**
- [User Activity Summary](#user-activity-summary-api) - Daily aggregated data
- [User Activity By Link](#user-activity-by-link-api) - Usage per link/keyword 🆕

**Product Management:**
- [Products API](#products-api) - Manage products and pricing
- [User Products API](#user-products-api) - Assign products to users

**Billing:**
- [Usage Records API](#usage-records-api) - Billable events
- [Payment Records API](#payment-records-api) - Payment transactions

**Debug Tools:**
- [Schema Inspector API](#schema-inspector-api) - Query database schema
- [Testing](#testing) - Test scripts and examples
- [Monitoring](#monitoring) - CloudWatch logs

---

## User Activity Summary API

Retrieves daily aggregated activity data including hit counts, costs, and running balance.

### Endpoint

```
GET /user-activity-summary/{uid}
```

### Path Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `uid` | integer | Yes | User ID to retrieve activity for |

### Query Parameters

| Parameter | Type | Required | Description | Example |
|-----------|------|----------|-------------|---------|
| `start_date` | string | No | Filter from this date (YYYY-MM-DD) | `2025-07-01` |
| `end_date` | string | No | Filter to this date (YYYY-MM-DD) | `2025-07-31` |

### Response Format

```json
{
  "message": "Activity summary retrieved",
  "success": true,
  "source": [
    {
      "date": "2025-07-30",
      "totalHits": 5,
      "hitCost": -0.5,
      "balance": -0.5
    }
  ]
}
```

### Response Fields

| Field | Type | Description |
|-------|------|-------------|
| `date` | string | Date in YYYY-MM-DD format |
| `totalHits` | integer | Number of redirects/hits for this date |
| `hitCost` | float | Cost of hits (negative value) |
| `balance` | float | Running balance (cumulative sum of costs and payments) |

### Example Usage

```bash
# Basic request
curl "https://ce7jzbocq1.execute-api.ca-central-1.amazonaws.com/dev/user-activity-summary/125"

# With date range
curl "https://ce7jzbocq1.execute-api.ca-central-1.amazonaws.com/dev/user-activity-summary/125?start_date=2025-08-01&end_date=2025-08-31"
```

```javascript
// JavaScript/React example
const response = await fetch(
  `https://ce7jzbocq1.execute-api.ca-central-1.amazonaws.com/dev/user-activity-summary/${userId}`
);
const data = await response.json();
if (data.success) {
  console.log('Activity records:', data.source);
}
```

---

## User Activity By Link API

Get usage data aggregated per link/keyword **per day** for a specific user. Shows daily breakdown of which links were used and their costs.

### Endpoint

```
GET /user-activity-summary/{uid}/by-link
```

### Path Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `uid` | integer | Yes | User ID to retrieve link usage for |

### Query Parameters

| Parameter | Type | Required | Description | Example |
|-----------|------|----------|-------------|---------|
| `start_date` | string | No | Filter from this date (YYYY-MM-DD) | `2025-07-01` |
| `end_date` | string | No | Filter to this date (YYYY-MM-DD) | `2025-07-31` |

### Response Format

```json
{
  "message": "Usage by link per day retrieved",
  "success": true,
  "source": [
    {
      "date": "2025-08-19",
      "mid": 20,
      "keyword": null,
      "destination": null,
      "hits": 8791,
      "cost": -87.91
    },
    {
      "date": "2025-08-19",
      "mid": 14143,
      "keyword": "PERF_bab870b3-59f9-42b1-964a-a66cdad26ca8",
      "destination": "https://httpbin.org/status/200",
      "hits": 4,
      "cost": -0.04
    },
    {
      "date": "2025-08-11",
      "mid": 20,
      "keyword": null,
      "destination": null,
      "hits": 8,
      "cost": -0.08
    }
  ]
}
```

**Notes:**
- Results are sorted by `date` DESC (most recent first), then by `hits` DESC (most popular first)
- Each record represents usage for a specific link on a specific day
- Same `mid` can appear multiple times (once per day it was used)
- Links with `null` keyword/destination were deleted but still have hit history
- Costs are calculated using user's active product pricing or $0.10/hit default

### Response Fields

| Field | Type | Description |
|-------|------|-------------|
| `date` | string | Date in YYYY-MM-DD format |
| `mid` | integer | Map ID (link identifier) |
| `keyword` | string \| null | Short URL keyword/key (null if link deleted from tp_map) |
| `destination` | string \| null | Destination URL (null if link deleted from tp_map) |
| `hits` | integer | Number of clicks for this link on this date |
| `cost` | float | Cost for this link on this date (negative value) |

**Note:** `keyword` and `destination` may be `null` if the link (mid) no longer exists in the `tp_map` table. Hit counts and costs are still accurate.

### Example Usage

```bash
# Get all link usage for user 125
curl "https://ce7jzbocq1.execute-api.ca-central-1.amazonaws.com/dev/user-activity-summary/125/by-link"

# Get link usage for specific date range
curl "https://ce7jzbocq1.execute-api.ca-central-1.amazonaws.com/dev/user-activity-summary/125/by-link?start_date=2025-08-01&end_date=2025-08-31"
```

```javascript
// JavaScript/React example - Get usage per link per day
const response = await fetch(
  `https://ce7jzbocq1.execute-api.ca-central-1.amazonaws.com/dev/user-activity-summary/${userId}/by-link`
);
const data = await response.json();
if (data.success) {
  console.log('Usage by link per day:', data.source);

  // Group by date
  const byDate = {};
  data.source.forEach(record => {
    if (!byDate[record.date]) byDate[record.date] = [];
    byDate[record.date].push(record);
  });

  // Display
  Object.keys(byDate).forEach(date => {
    console.log(`\n${date}:`);
    byDate[date].forEach(link => {
      console.log(`  ${link.keyword || `mid:${link.mid}`}: ${link.hits} hits, $${Math.abs(link.cost).toFixed(2)}`);
    });
  });
}
```

**Use Cases:**
- Show which links are most popular
- Display usage breakdown in a table or chart
- Calculate cost per link for billing
- Identify high-traffic links for optimization
- Show keyword usage breakdown in dashboard

**Real-World Example:**

```javascript
// Fetch link usage per day and aggregate by link
const response = await fetch(
  `https://ce7jzbocq1.execute-api.ca-central-1.amazonaws.com/dev/user-activity-summary/125/by-link?start_date=2025-08-01&end_date=2025-08-31`
);
const data = await response.json();

// Aggregate totals per link (across all days)
const linkTotals = {};
data.source.forEach(record => {
  const key = record.mid;
  if (!linkTotals[key]) {
    linkTotals[key] = {
      mid: record.mid,
      keyword: record.keyword,
      destination: record.destination,
      totalHits: 0,
      totalCost: 0,
      days: []
    };
  }
  linkTotals[key].totalHits += record.hits;
  linkTotals[key].totalCost += record.cost;
  linkTotals[key].days.push({ date: record.date, hits: record.hits });
});

// Get top 5 links by hits
const topLinks = Object.values(linkTotals)
  .filter(link => link.keyword !== null)
  .sort((a, b) => b.totalHits - a.totalHits)
  .slice(0, 5);

// Display
topLinks.forEach((link, index) => {
  console.log(`${index + 1}. ${link.keyword}: ${link.totalHits} clicks → ${link.destination}`);
  console.log(`   Cost: $${Math.abs(link.totalCost).toFixed(2)}`);
  console.log(`   Active on ${link.days.length} days`);
});
```

**Output:**
```
1. PERF_bab870b3-59f9-42b1-964a-a66cdad26ca8: 4 clicks → https://httpbin.org/status/200
   Cost: $0.04
   Active on 1 days
2. PERF_3_1755604230: 2 clicks → https://example.com/test-803
   Cost: $0.02
   Active on 1 days
```

---

## Products API

Manage product definitions and pricing.

### List Products

```
GET /products
```

Returns all products in the database.

### Get Product

```
GET /products/{pid}
```

Returns details for a specific product.

### Create Product

```
POST /products
```

**Body:**
```json
{
  "name": "Link Clicks",
  "description": "Pay per link click/redirect",
  "type": "usage-based",
  "price": 0.10,
  "active": true
}
```

**Product Types:**
- `usage-based`: Pay per use (per click)
- `subscription`: Monthly/yearly recurring fee
- `one-time`: One-time purchase
- `tiered`: Volume-based pricing

### Update Product

```
PUT /products/{pid}
```

**Body:**
```json
{
  "price": 0.15,
  "active": true
}
```

---

## User Products API

Manage product assignments to users.

### List User's Products

```
GET /user-products/user/{uid}
```

Returns all products assigned to a user.

### Assign Product to User

```
POST /user-products
```

**Body:**
```json
{
  "uid": 125,
  "pid": 5,
  "status": "active"
}
```

**Status values:** `active`, `inactive`, `expired`, `cancelled`

### Update User Product

```
PUT /user-products/{upid}
```

**Body:**
```json
{
  "status": "inactive"
}
```

---

## Usage Records API

Manual API for creating/reading billable usage events.

### Create Usage Record

```
POST /usage-records
```

**Body:**
```json
{
  "upid": 3,
  "datetime": "2025-10-19 10:00:00",
  "event": "redirect:mid=123"
}
```

### Get Usage Records

```
GET /usage-records/{urid}
```

Returns usage records for a specific record ID.

---

## Payment Records API

Track payment transactions.

### Create Payment Record

```
POST /payment-records
```

**Body:**
```json
{
  "upid": 3,
  "amount": 100.00,
  "datetime": "2025-10-19 10:00:00"
}
```

### List Payment Records

```
GET /payment-records/user/{uid}
```

Returns all payment records for a user.

---

## Schema Inspector API

Debug tool for querying database schema and sample data.

### Describe Table Schema

```
GET /schema-inspector/describe/{table_name}
```

Returns column definitions for a table.

**Example:**
```bash
curl "https://ce7jzbocq1.execute-api.ca-central-1.amazonaws.com/dev/schema-inspector/describe/tp_log"
```

### Get Sample Data

```
GET /schema-inspector/sample/{table_name}?limit={n}
```

Returns sample records from a table.

**Example:**
```bash
curl "https://ce7jzbocq1.execute-api.ca-central-1.amazonaws.com/dev/schema-inspector/sample/tp_log?limit=5"
```

**Supported Tables:**
- `tp_log`, `tp_user`, `tp_map`, `tp_set`
- `payment_records`, `usage_records`, `user_products`, `products`

---

## Error Responses

All endpoints return errors in this format:

```json
{
  "message": "Error description",
  "success": false,
  "source": null
}
```

**Common HTTP Status Codes:**
- `200`: Success
- `400`: Bad request (missing required fields)
- `404`: Resource not found
- `502`: Server error (database connection, query failure)

---

## Rate Limiting

- **Timeout**: 15 seconds per request (Lambda default)
- **No explicit rate limiting**: API Gateway standard limits apply
- **Recommended**: Cache results on frontend for 5-10 minutes

---

## Monitoring

### CloudWatch Logs

**View logs for specific functions:**
```bash
# User Activity Summary
aws logs tail /aws/lambda/dev-GetUserActivitySummaryFunction --follow

# User Activity By Link (NEW)
aws logs tail /aws/lambda/dev-GetUserActivityByLinkFunction --follow

# Redirect (to check dual-write)
aws logs tail /aws/lambda/dev-RedirectToURLFunction --follow
```

**Common log patterns:**

*User Activity Endpoints:*
- `Activity summary retrieved` - Success
- `Usage by link retrieved` - Success (new endpoint)
- `Using product pricing: {name} at ${price}/hit` - Product pricing used
- `No usage-based product found` - Using default pricing
- `Error Message:` - Error occurred

*Redirect Functions (Dual-Write):*
- `Logged usage record for uid=X, mid=Y, upid=Z` - Dual-write successful ✓
- `No active usage-based product found for uid=X, skipping usage_records write` - User has no product
- `Exception logging usage record:` - Dual-write failed (redirect still succeeds)

---

## Testing

### Test Script

```bash
#!/bin/bash
API_BASE="https://ce7jzbocq1.execute-api.ca-central-1.amazonaws.com/dev"

echo "=== Test 1: User Activity Summary ==="
curl -s "$API_BASE/user-activity-summary/125" | python3 -m json.tool
echo ""

echo "=== Test 2: Usage By Link ==="
curl -s "$API_BASE/user-activity-summary/125/by-link" | python3 -m json.tool
echo ""

echo "=== Test 3: Usage By Link (Date Filtered) ==="
curl -s "$API_BASE/user-activity-summary/125/by-link?start_date=2025-08-01&end_date=2025-08-31" | python3 -m json.tool
echo ""

echo "=== Test 4: List Products ==="
curl -s "$API_BASE/products" | python3 -m json.tool
echo ""

echo "=== Test 5: Schema Inspector ==="
curl -s "$API_BASE/schema-inspector/describe/tp_log" | python3 -m json.tool
echo ""
```

### Test Users

- **UID 125**: Has activity data (8,833+ records)
- **UID 1**: No activity data (returns empty array)

---

## Implementation Details

### How Activity Summary Works

```
1. Query user's active usage-based product → get price
2. Count daily hits from tp_log table
3. Calculate hit costs = totalHits × price
4. Get payment records from payment_records table
5. Calculate running balance = Σ(payments + costs)
6. Return daily aggregated records ordered by date
```

### Database Tables

| Table | Purpose |
|-------|---------|
| `tp_log` | Hit/redirect counts and analytics data |
| `user_products` | Links users to products (PK: upid) |
| `products` | Product pricing (type, price, active) |
| `payment_records` | Payment transactions (FK: upid) |
| `usage_records` | Billable usage events (FK: upid) |

### Cost Calculation

- **Hit Cost** = Number of Hits × Product Price
- If no product found: defaults to $0.10 per hit
- Hit costs are negative (reduce balance)
- Payments are positive (increase balance)

### Balance Calculation

Running total calculated chronologically:
```
Day 1: Balance = Payment1 + HitCost1
Day 2: Balance = Day1Balance + Payment2 + HitCost2
Day 3: Balance = Day2Balance + Payment3 + HitCost3
```

---

## Future Enhancements

**Planned Features:**
- [ ] Add top 2 keywords per day to daily activity summary
- [ ] Reverse balance calculation (backward from today)
- [ ] Add pagination for large result sets
- [ ] Add authentication/authorization
- [ ] Support for tiered pricing
- [ ] CSV/Excel export functionality
- [ ] Caching layer (Redis/ElastiCache)

**In Progress:**
- ✓ Dual-write to usage_records (deployed, monitoring)
- ⏳ Migration to usage_records for billing (Phase 2)

---

## Deployment Information

**Current Version:** v1.2.0 (October 20, 2025)

**Stack:**
- Name: `dev-linksmarty`
- Region: `ca-central-1`
- API Base: `https://ce7jzbocq1.execute-api.ca-central-1.amazonaws.com/dev`

**Related Documentation:**
- [Admin Guide](ADMIN_GUIDE.md) - Product setup and troubleshooting
- [Migration Guide](MIGRATION_GUIDE.md) - Complete migration plan and tp_log → usage_records migration details
