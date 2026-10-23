---
paths:
  - 'database/migrations/**'
---

# Migrations

## OAuth token columns must be TEXT, not string(255)
Pocket ID (socialiteproviders/pocketid) returns JWT access tokens well beyond 255 chars; storing them in varchar(255) fails on MySQL strict mode with "Data too long" (1406). pocketid_token and pocketid_refresh_token on users are TEXT via migration 2026_09_12_175809. Keep any future OAuth token columns TEXT. The feature tests run on MySQL strict mode and use >255-char tokens in PocketIDControllerTest, so regressions are caught there.
