//TODO

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