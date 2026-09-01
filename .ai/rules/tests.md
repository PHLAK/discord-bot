---
paths:
  - 'tests/**'
---

# Tests

## PHPUnit attributes with it_ test names
Tests use PHPUnit attributes (#[Test], #[CoversClass] or #[CoversNothing], #[DataProvider]) and snake_case method names starting with it_.

## Test fixtures live in tests/_data
Fixture files (JSON payloads, sample uploads) live in tests/_data, mirroring the subject's structure.
