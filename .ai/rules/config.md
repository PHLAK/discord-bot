---
paths:
  - config/services.php
---

# Config

## Socialite custom providers read config from services.php
socialiteproviders/manager reads driver config from config("services.{provider}"), not a custom config file. Pocket ID creds (client_id/secret/redirect/base_url/use_pkce) must live under 'pocketid' in config/services.php. Do not reintroduce a separate config/oauth.php.
