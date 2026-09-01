---
paths:
  - 'routes/**'
---

# Routes

## One route file per concern
New route groups get their own file in routes/ (e.g. oauth.php, webhooks.php), registered in bootstrap/app.php's then: callback with a name prefix and middleware group.
