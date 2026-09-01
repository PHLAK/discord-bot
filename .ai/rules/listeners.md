---
paths:
  - 'app/Listeners/**'
---

# Listeners

## Controllers dispatch events; logic lives in queued listeners
Keep controllers thin: dispatch an event or make the direct framework call, and put business logic in queued listeners (ShouldQueue). There is no action, service, or repository layer — use Eloquent and facades directly.
