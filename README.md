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
