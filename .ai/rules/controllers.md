---
paths:
  - 'app/Http/Controllers/**'
---

# Controllers

## Controllers dispatch events; logic lives in queued listeners
Keep controllers thin: dispatch an event or make the direct framework call, and put business logic in queued listeners (ShouldQueue). There is no action, service, or repository layer — use Eloquent and facades directly.

## Group controllers by domain subfolder
Controllers live in a domain subfolder of app/Http/Controllers (e.g. Auth, OAuth, Webhooks) and are single-purpose: invokable, or a small abstract contract like ProviderController::redirect()/callback(). No resource controllers.
