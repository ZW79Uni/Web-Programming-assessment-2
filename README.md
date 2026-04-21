Events Meets World (EMW) Website Design and Implementation

## MySQL Integration Test Trackability

Run this command from the repository root:

```powershell
cmd /c '"C:\Program Files\MySQL\MySQL Server 8.0\bin\mysql.exe" -u root < .\MySQL\test.SQL'
```

The script executes three integration checks and prints a pass/fail table:

- DB-INT-01: Verify Booking Ownership (`booking -> client -> vendor` join integrity)
- DB-INT-02: Rating Aggregation (`AVG(rating) GROUP BY vendorID` for UI vendor cards)
- DB-INT-03: Trigger Security (invalid chat sender membership insert is rejected)

Expected status after loading `schema.SQL` and `sample_data.SQL`: all three tests report `PASSED`.

## Clean Build and Verify

Run a clean rebuild:

```powershell
$mysql='C:\Program Files\MySQL\MySQL Server 8.0\bin\mysql.exe'
& $mysql -u root -e "DROP DATABASE IF EXISTS h6zp02h_EMW_Database; CREATE DATABASE h6zp02h_EMW_Database CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
cmd /c '"C:\Program Files\MySQL\MySQL Server 8.0\bin\mysql.exe" -u root < .\MySQL\schema.SQL'
cmd /c '"C:\Program Files\MySQL\MySQL Server 8.0\bin\mysql.exe" -u root < .\MySQL\sample_data.SQL'
cmd /c '"C:\Program Files\MySQL\MySQL Server 8.0\bin\mysql.exe" -u root < .\MySQL\test.SQL'
```

Optional smoke check for location filtering:

```sql
SELECT v.vendorID, v.vendorOrginisationName, a.city, a.county
FROM vendor v
JOIN address a ON a.addressID = v.addressID
ORDER BY v.vendorID;
```

## Team Kickoff Checklist (PHP and React)

- Confirm API route ownership and naming: `/api/vendors/search`, `/api/chat/:chatId/messages`, `/api/dashboard/client/:clientId/bookings`, `/api/admin/audit-log`.
- Align contract fields with DB columns before coding serializers.
- Decide authentication split (client, vendor, admin) and session strategy.
- Add pagination/sorting requirements now for vendor lists, chat history, and audit log.
- Define error contract for DB trigger failures so React can show clear messages.
- Agree seed reset workflow for demo branches: schema load, sample load, test run.

## Story-Driven Meeting Demo

Run this command to execute all five vertical-slice proof queries:

```powershell
cmd /c '"C:\Program Files\MySQL\MySQL Server 8.0\bin\mysql.exe" -u root < .\MySQL\story_driven_queries.SQL'
```

Script location: `MySQL/story_driven_queries.SQL`

This script demonstrates:

- Matchmaking query (verified Kent vendors for wedding services)
- Confirmed booking dashboard join flow
- Trust and reputation aggregation
- Secure chat membership enforcement
- Governance audit feed for admin actions
