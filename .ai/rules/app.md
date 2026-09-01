---
paths:
  - 'app/**'
---

# App

## Immutable dates
Dates are immutable app-wide via Date::use(CarbonImmutable::class). Use CarbonImmutable/CarbonInterval; never mutable Carbon.

## Type arrays with PHPDoc array shapes
Type array parameters and returns with PHPDoc array shapes or generics (e.g. @return array{name: string, value: string, inline: bool}), never bare array.
