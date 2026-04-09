# EMW API Contract (PHP + React)

## 1. Scope
This contract defines the first vertical slice API for Events Meets World:
- Matchmaking search by location and event type
- Client booking dashboard
- Chat read/write with DB-enforced membership rules
- Admin audit feed and vendor verification action

Status: Draft v1 (aligned to current MySQL schema and test scripts)

## 2. Conventions
- Base URL: /api
- Content type: application/json
- Time format: ISO 8601 (UTC) for API responses
- Date-only fields: YYYY-MM-DD
- IDs: integer (BIGINT in DB)
- Pagination defaults: page=1, limit=20 (max limit=100)

## 3. Auth and Roles (Application Layer)
- Role values: client, vendor, admin
- Access control is enforced in PHP middleware.
- Database enforces additional integrity for chat sender membership via triggers.

## 4. Standard Error Envelope
All non-2xx responses should use this shape:

{
  "error": {
    "code": "STRING_CODE",
    "message": "Human-readable message",
    "details": null
  }
}

Recommended codes:
- BAD_REQUEST
- UNAUTHORIZED
- FORBIDDEN
- NOT_FOUND
- CONFLICT
- VALIDATION_ERROR
- DB_CONSTRAINT
- INTERNAL_ERROR

## 5. Endpoints

### 5.1 GET /api/vendors/search
Search vendors for matchmaking cards.

Query parameters:
- county: string (optional)
- city: string (optional)
- eventType: string (optional, example: Wedding)
- verified: boolean (optional)
- maxPrice: number (optional)
- sort: string (optional: trust_desc, price_asc, rating_desc)
- page: number (optional)
- limit: number (optional)

Response 200:
{
  "data": [
    {
      "vendorID": 2,
      "vendorOrginisationName": "Majestic Marquees Kent",
      "city": "Canterbury",
      "county": "Kent",
      "isVerified": true,
      "automationScore": 85,
      "avgRating": 5.0,
      "reviewCount": 1,
      "services": [
        {
          "serviceID": 1,
          "serviceName": "Marquee (3X6M)",
          "servicePrice": 300.0,
          "isAvailable": true
        }
      ]
    }
  ],
  "meta": {
    "page": 1,
    "limit": 20,
    "total": 1
  }
}

Notes:
- Uses vendor -> address link for location filtering.
- Supports event-type filtering via service/eventTypeAllocation.

### 5.2 GET /api/dashboard/client/{clientId}/bookings
Get bookings for a client dashboard.

Path params:
- clientId: integer (required)

Query params:
- status: string (optional: pending, confirmed, cancelled, completed)
- page: number (optional)
- limit: number (optional)

Response 200:
{
  "data": [
    {
      "bookingID": 1,
      "clientID": 1,
      "vendorID": 2,
      "serviceID": 2,
      "vendorOrginisationName": "Majestic Marquees Kent",
      "serviceName": "Marquee (4X8M)",
      "eventDate": "2026-06-12",
      "status": "confirmed"
    }
  ],
  "meta": {
    "page": 1,
    "limit": 20,
    "total": 1
  }
}

### 5.3 GET /api/chat/{chatId}/messages
Get chat thread messages.

Path params:
- chatId: integer (required)

Query params:
- page: number (optional)
- limit: number (optional)

Response 200:
{
  "data": [
    {
      "messageID": 1,
      "chatID": 1,
      "senderID": 1,
      "senderType": "client",
      "messageContent": "Hi Pete, do you have the 4X8M Marquee available for June 12th?"
    },
    {
      "messageID": 2,
      "chatID": 1,
      "senderID": 1,
      "senderType": "admin",
      "messageContent": "System Sam here. Let me know if you need support with booking confirmation."
    }
  ],
  "meta": {
    "page": 1,
    "limit": 20,
    "total": 2
  }
}

### 5.4 POST /api/chat/{chatId}/messages
Create a chat message.

Path params:
- chatId: integer (required)

Request body:
{
  "senderID": 1,
  "senderType": "client",
  "messageContent": "Can you confirm setup time?"
}

Validation:
- senderType must be one of: client, vendor, admin
- messageContent required, non-empty

Response 201:
{
  "data": {
    "messageID": 3,
    "chatID": 1,
    "senderID": 1,
    "senderType": "client",
    "messageContent": "Can you confirm setup time?"
  }
}

Expected DB-integrity failure mapping:
- If sender is not valid participant for the chat, return 400 with:
{
  "error": {
    "code": "DB_CONSTRAINT",
    "message": "Client is not a participant in this chat.",
    "details": null
  }
}

### 5.5 GET /api/admin/audit-log
Get governance actions for admin panel.

Query params:
- actionType: string (optional)
- targetType: string (optional)
- page: number (optional)
- limit: number (optional)

Response 200:
{
  "data": [
    {
      "logID": 1,
      "actionDate": "2026-04-09T04:03:17Z",
      "adminID": 1,
      "adminUsername": "SystemSam",
      "actionType": "verify_vendor",
      "targetType": "vendor",
      "targetID": 1,
      "reason": "Company registration documents validated."
    }
  ],
  "meta": {
    "page": 1,
    "limit": 20,
    "total": 3
  }
}

### 5.6 POST /api/admin/vendors/{vendorId}/verify
Mark a vendor as verified and create audit log entry.

Path params:
- vendorId: integer (required)

Request body:
{
  "adminID": 1,
  "reason": "Insurance and portfolio checks completed."
}

Server-side behavior (single transaction):
1) Update vendor:
- isVerified = true
- verifiedBy = adminID
- verifiedAt = NOW()
2) Insert auditLog row:
- actionType = verify_vendor
- targetType = vendor
- targetID = vendorId
- reason = reason

Response 200:
{
  "data": {
    "vendorID": 2,
    "isVerified": true,
    "verifiedBy": 1
  }
}

## 6. Data Mapping to Current DB
Primary tables used by this contract:
- vendor, address, service, vendorServiceAllocation, eventTypeAllocation, eventType
- booking, client
- chat, message
- admin, auditLog
- review

## 7. Non-Functional Notes
- Use prepared statements in PHP for all SQL.
- Return deterministic sort order for paginated endpoints.
- Add index review after first API load tests (especially search and message listing).

## 8. Out of Scope for v1
- Payments API
- File upload/media delivery API for blog/media assets
- Real-time websocket chat transport (v1 can poll)
