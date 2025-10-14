# User Activity Summary API Documentation

## Overview

The User Activity Summary API provides aggregated daily user activity data including hit counts, costs, and running balance calculations. This endpoint is designed to populate activity tables in the frontend with comprehensive usage metrics.

---

## Endpoints

### 1. Get User Activity Summary

Retrieves daily aggregated activity data for a specific user.

**Endpoint**: `GET /user-activity-summary/{uid}`

**Base URL**: `https://ce7jzbocq1.execute-api.ca-central-1.amazonaws.com/dev`

**Full URL**: `https://ce7jzbocq1.execute-api.ca-central-1.amazonaws.com/dev/user-activity-summary/{uid}`

---

## Request Parameters

### Path Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `uid` | integer | Yes | User ID to retrieve activity for |

### Query Parameters (Optional)

| Parameter | Type | Required | Description | Example |
|-----------|------|----------|-------------|---------|
| `start_date` | string | No | Filter from this date (YYYY-MM-DD) | `2025-07-01` |
| `end_date` | string | No | Filter to this date (YYYY-MM-DD) | `2025-07-31` |

---

## Response Format

### Success Response (200)

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
    },
    {
      "date": "2025-08-19",
      "totalHits": 8794,
      "hitCost": -879.4,
      "balance": -882.2
    }
  ]
}
```

### Response Fields

| Field | Type | Description |
|-------|------|-------------|
| `message` | string | Human-readable status message |
| `success` | boolean | Indicates if the request was successful |
| `source` | array | Array of daily activity records |

### Activity Record Fields

| Field | Type | Description |
|-------|------|-------------|
| `date` | string | Date in YYYY-MM-DD format |
| `totalHits` | integer | Number of redirects/hits for this date |
| `hitCost` | float | Cost of hits (negative value, calculated from product price) |
| `balance` | float | Running balance (cumulative sum of costs and payments) |

### Error Response (400)

```json
{
  "message": "Missing user ID",
  "success": false,
  "source": null
}
```

### Error Response (502)

```json
{
  "message": "Error Message: [detailed error]",
  "success": false,
  "source": null
}
```

---

## Usage Examples

### 1. Basic Request (No Date Filter)

**cURL:**
```bash
curl -X GET "https://ce7jzbocq1.execute-api.ca-central-1.amazonaws.com/dev/user-activity-summary/125"
```

**JavaScript (Fetch):**
```javascript
fetch('https://ce7jzbocq1.execute-api.ca-central-1.amazonaws.com/dev/user-activity-summary/125')
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      console.log('Activity records:', data.source);
      data.source.forEach(record => {
        console.log(`${record.date}: ${record.totalHits} hits, Balance: $${record.balance}`);
      });
    }
  });
```

**Python:**
```python
import requests

url = "https://ce7jzbocq1.execute-api.ca-central-1.amazonaws.com/dev/user-activity-summary/125"
response = requests.get(url)
data = response.json()

if data['success']:
    for record in data['source']:
        print(f"{record['date']}: {record['totalHits']} hits, Balance: ${record['balance']}")
```

---

### 2. Request with Date Range Filter

**cURL:**
```bash
curl -X GET "https://ce7jzbocq1.execute-api.ca-central-1.amazonaws.com/dev/user-activity-summary/125?start_date=2025-08-01&end_date=2025-08-31"
```

**JavaScript:**
```javascript
const params = new URLSearchParams({
  start_date: '2025-08-01',
  end_date: '2025-08-31'
});

fetch(`https://ce7jzbocq1.execute-api.ca-central-1.amazonaws.com/dev/user-activity-summary/125?${params}`)
  .then(response => response.json())
  .then(data => console.log(data));
```

**Python:**
```python
import requests

url = "https://ce7jzbocq1.execute-api.ca-central-1.amazonaws.com/dev/user-activity-summary/125"
params = {
    'start_date': '2025-08-01',
    'end_date': '2025-08-31'
}
response = requests.get(url, params=params)
print(response.json())
```

---

### 3. Filter by Start Date Only

**cURL:**
```bash
curl -X GET "https://ce7jzbocq1.execute-api.ca-central-1.amazonaws.com/dev/user-activity-summary/125?start_date=2025-08-01"
```

Returns all activity from August 1st, 2025 onwards.

---

### 4. Filter by End Date Only

**cURL:**
```bash
curl -X GET "https://ce7jzbocq1.execute-api.ca-central-1.amazonaws.com/dev/user-activity-summary/125?end_date=2025-07-31"
```

Returns all activity up to and including July 31st, 2025.

---

## How It Works

### Data Flow

```
1. Get user's active product and price from products table
2. Count daily hits from tp_log table
3. Calculate hit costs = totalHits × product.price
4. Get payment records from payment_records (via user_products.upid)
5. Calculate running balance = Σ(payments + costs)
6. Return daily aggregated records ordered by date
```

### Database Tables Used

| Table | Purpose |
|-------|---------|
| `tp_log` | Source of hit/redirect counts (uses `dt` timestamp column) |
| `user_products` | Links users to products (uses `upid` as PK, `uid` and `pid` as FKs) |
| `products` | Product pricing information (uses `price` field for cost calculation) |
| `payment_records` | Payment transactions (links via `upid` to user_products) |

### Cost Calculation

- **Hit Cost** = Number of Hits × User's Active Product Price
- If no active product found, defaults to $0.10 per hit
- Hit costs are negative (they reduce balance)
- Payment amounts are positive (they increase balance)

### Balance Calculation

The balance is a **running total** calculated chronologically:

```
Day 1: Balance = Payment1 + HitCost1
Day 2: Balance = Day1Balance + Payment2 + HitCost2
Day 3: Balance = Day2Balance + Payment3 + HitCost3
...
```

Example:
```
2025-07-30: 5 hits × $0.10 = -$0.50 cost, Balance = -$0.50
2025-08-05: 1 hit  × $0.10 = -$0.10 cost, Balance = -$0.60
2025-08-11: 8 hits × $0.10 = -$0.80 cost, Balance = -$1.40
```

---

## Frontend Integration

### React Example

```javascript
import React, { useState, useEffect } from 'react';

function ActivityTable({ userId }) {
  const [activity, setActivity] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    const fetchActivity = async () => {
      try {
        const response = await fetch(
          `https://ce7jzbocq1.execute-api.ca-central-1.amazonaws.com/dev/user-activity-summary/${userId}`
        );
        const data = await response.json();

        if (data.success) {
          setActivity(data.source);
        } else {
          setError(data.message);
        }
      } catch (err) {
        setError(err.message);
      } finally {
        setLoading(false);
      }
    };

    fetchActivity();
  }, [userId]);

  if (loading) return <div>Loading...</div>;
  if (error) return <div>Error: {error}</div>;

  return (
    <table>
      <thead>
        <tr>
          <th>Date</th>
          <th>Total Hits</th>
          <th>Hit Cost</th>
          <th>Balance</th>
        </tr>
      </thead>
      <tbody>
        {activity.map((record, index) => (
          <tr key={index}>
            <td>{record.date}</td>
            <td>{record.totalHits}</td>
            <td>${record.hitCost.toFixed(2)}</td>
            <td>${record.balance.toFixed(2)}</td>
          </tr>
        ))}
      </tbody>
    </table>
  );
}

export default ActivityTable;
```

### Vue.js Example

```vue
<template>
  <div>
    <table v-if="!loading && !error">
      <thead>
        <tr>
          <th>Date</th>
          <th>Total Hits</th>
          <th>Hit Cost</th>
          <th>Balance</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="record in activity" :key="record.date">
          <td>{{ record.date }}</td>
          <td>{{ record.totalHits }}</td>
          <td>${{ record.hitCost.toFixed(2) }}</td>
          <td>${{ record.balance.toFixed(2) }}</td>
        </tr>
      </tbody>
    </table>
    <div v-if="loading">Loading...</div>
    <div v-if="error">Error: {{ error }}</div>
  </div>
</template>

<script>
export default {
  props: ['userId'],
  data() {
    return {
      activity: [],
      loading: true,
      error: null
    };
  },
  mounted() {
    this.fetchActivity();
  },
  methods: {
    async fetchActivity() {
      try {
        const response = await fetch(
          `https://ce7jzbocq1.execute-api.ca-central-1.amazonaws.com/dev/user-activity-summary/${this.userId}`
        );
        const data = await response.json();

        if (data.success) {
          this.activity = data.source;
        } else {
          this.error = data.message;
        }
      } catch (err) {
        this.error = err.message;
      } finally {
        this.loading = false;
      }
    }
  }
};
</script>
```

---

## Rate Limiting & Performance

- **Timeout**: 15 seconds (Lambda default)
- **No explicit rate limiting**: API Gateway standard limits apply
- **Query Optimization**: Uses indexed columns (uid, dt, upid) for fast lookups
- **Recommended**: Cache results on frontend for 5-10 minutes to reduce API calls

---

## Error Handling

### Common Error Scenarios

| Error | Cause | Solution |
|-------|-------|----------|
| 400 - Missing user ID | No `{uid}` in path | Ensure uid is provided in URL |
| 502 - Database error | DB connection or query failure | Check CloudWatch logs, verify DB is accessible |
| Empty `source` array | User has no activity data | Normal response, user has no hits recorded |
| Timeout | Large date range or slow query | Reduce date range, check DB performance |

### Error Handling Best Practices

```javascript
async function fetchActivitySafely(userId, startDate, endDate) {
  try {
    const params = new URLSearchParams();
    if (startDate) params.append('start_date', startDate);
    if (endDate) params.append('end_date', endDate);

    const url = `https://ce7jzbocq1.execute-api.ca-central-1.amazonaws.com/dev/user-activity-summary/${userId}?${params}`;

    const response = await fetch(url);

    if (!response.ok) {
      throw new Error(`HTTP ${response.status}: ${response.statusText}`);
    }

    const data = await response.json();

    if (!data.success) {
      throw new Error(data.message);
    }

    return data.source;

  } catch (error) {
    console.error('Failed to fetch activity:', error);
    // Show user-friendly error message
    // Optionally retry or fallback to cached data
    return [];
  }
}
```

---

## Testing & Debugging

### Test Users

Based on current database data:

- **UID 125**: Has activity data (5-8794 hits/day from July-September 2025)
- **UID 1**: No activity data (returns empty array)

### Schema Inspector Endpoints

For debugging database schema issues:

**Get Table Schema:**
```bash
curl "https://ce7jzbocq1.execute-api.ca-central-1.amazonaws.com/dev/schema-inspector/describe/payment_records"
```

**Get Sample Data:**
```bash
curl "https://ce7jzbocq1.execute-api.ca-central-1.amazonaws.com/dev/schema-inspector/sample/tp_log?limit=5"
```

**Supported Tables:**
- `tp_log`
- `tp_user`
- `tp_map`
- `tp_set`
- `payment_records`
- `usage_records`
- `user_products`
- `products`

---

## Complete Test Script

Save this as `test_activity_api.sh`:

```bash
#!/bin/bash

API_BASE="https://ce7jzbocq1.execute-api.ca-central-1.amazonaws.com/dev"

echo "======================================"
echo "User Activity Summary API Tests"
echo "======================================"
echo ""

# Test 1: User with data
echo "Test 1: Get activity for user 125 (has data)"
curl -s "$API_BASE/user-activity-summary/125" | python3 -m json.tool
echo ""
echo "---"
echo ""

# Test 2: User without data
echo "Test 2: Get activity for user 1 (no data)"
curl -s "$API_BASE/user-activity-summary/1" | python3 -m json.tool
echo ""
echo "---"
echo ""

# Test 3: Date range filter
echo "Test 3: Get activity for user 125 (August 2025 only)"
curl -s "$API_BASE/user-activity-summary/125?start_date=2025-08-01&end_date=2025-08-31" | python3 -m json.tool
echo ""
echo "---"
echo ""

# Test 4: Start date only
echo "Test 4: Get activity for user 125 (from August 2025 onwards)"
curl -s "$API_BASE/user-activity-summary/125?start_date=2025-08-01" | python3 -m json.tool
echo ""
echo "---"
echo ""

# Test 5: Missing user ID (error test)
echo "Test 5: Missing user ID (should return 400 error)"
curl -s -o /dev/null -w "HTTP Status: %{http_code}\n" "$API_BASE/user-activity-summary/"
echo ""
echo "---"
echo ""

echo "======================================"
echo "Schema Inspector Tests"
echo "======================================"
echo ""

# Test 6: Get table schema
echo "Test 6: Get payment_records schema"
curl -s "$API_BASE/schema-inspector/describe/payment_records" | python3 -m json.tool
echo ""
echo "---"
echo ""

# Test 7: Get sample data
echo "Test 7: Get sample tp_log data"
curl -s "$API_BASE/schema-inspector/sample/tp_log?limit=2" | python3 -m json.tool
echo ""

echo "======================================"
echo "All tests completed!"
echo "======================================"
```

Run with:
```bash
chmod +x test_activity_api.sh
./test_activity_api.sh
```

---

## Monitoring & Logs

### CloudWatch Logs

**Log Group**: `/aws/lambda/dev-GetUserActivitySummaryFunction`

**View logs:**
```bash
aws logs tail /aws/lambda/dev-GetUserActivitySummaryFunction --follow
```

**Common log messages:**
- `Activity summary retrieved` - Successful query
- `Missing user ID` - 400 error
- `Error Message: (1054, "Unknown column...")` - SQL error (schema issue)
- `pymysql.err.OperationalError` - Database connection issue

---

## Known Limitations

1. **Payment Data**: Currently, payment records may not be populated in the database, so balance will only reflect costs (negative values)
2. **Product Pricing**: If user has no active product, defaults to $0.10/hit
3. **Multiple Products**: Only uses the most recent active product for cost calculation
4. **No Pagination**: Returns all matching records (may be slow for users with years of data)

---

## Future Enhancements

Potential improvements (not yet implemented):

- [ ] Add `sui`, `nonSui` metrics (once defined)
- [ ] Add `otherServices` array
- [ ] Implement pagination for large result sets
- [ ] Add caching layer (Redis/ElastiCache)
- [ ] Add authentication/authorization
- [ ] Support for aggregate metrics (weekly/monthly summaries)
- [ ] Export to CSV/Excel functionality

---

## Support & Troubleshooting

### Issue: Empty response for user with activity

**Check:**
1. Verify user has records in tp_log:
   ```bash
   curl "https://ce7jzbocq1.execute-api.ca-central-1.amazonaws.com/dev/schema-inspector/sample/tp_log?limit=10"
   ```
2. Check if uid matches in database

### Issue: Incorrect costs

**Check:**
1. User's active product price:
   ```bash
   curl "https://ce7jzbocq1.execute-api.ca-central-1.amazonaws.com/dev/user-products/user/{uid}"
   ```
2. Verify product pricing in products table

### Issue: Timeout errors

**Solutions:**
- Reduce date range in query
- Add database indexes on `tp_log(uid, dt)` and `payment_records(upid, datetime)`
- Increase Lambda timeout (currently 15s)

---

## API Changelog

### v1.1 (2025-10-14) - Current Version
- ✅ Fixed schema issues with payment_records (uses upid not uid)
- ✅ Fixed tp_log queries (uses dt not created_at)
- ✅ Added schema inspector for debugging
- ✅ Tested with real data
- ✅ Documentation completed

### v1.0 (2025-10-14) - Initial Release
- Initial implementation with basic functionality
- Support for date range filtering
- Product-based cost calculation

---

## Contact

For issues, questions, or feature requests, check CloudWatch logs or review the implementation at:
- Lambda: `user_activity_summary/app.py`
- SAM Template: `template-dev.yaml` (lines 1567-1598)
- This Documentation: `USER_ACTIVITY_SUMMARY_API.md`
