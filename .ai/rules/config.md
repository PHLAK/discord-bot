---
paths:
  - config/services.php
  - config/cache.php
---

# Config

## Socialite custom providers read config from services.php
socialiteproviders/manager reads driver config from config("services.{provider}"), not a custom config file. Pocket ID creds (client_id/secret/redirect/base_url/use_pkce) must live under 'pocketid' in config/services.php. Do not reintroduce a separate config/oauth.php.

## Keep serializable_classes allowlisted for Pulse
cache.serializable_classes must include Illuminate\Support\Collection and stdClass. With it set to false, Pulse's Livewire cards (which cache Collections of stdClass aggregate rows via RemembersQueries for 5s) unserialize to __PHP_Incomplete_Class and /pulse intermittently returns 500s. Never set the option back to plain false.
