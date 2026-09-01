---
paths:
  - 'app/Models/**'
---

# Models

## OAuth-only users: nullable password + pocketid fields fillable
Login is OAuth-only (no email/password form). The users.password column is nullable so PocketID updateOrCreate inserts work on MySQL strict mode, and pocketid_id/token/refresh_token are in User::$fillable. Keep these fields in fillable when touching User.
