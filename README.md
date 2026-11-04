# Secure Order Management Platform for FAM Roast N' Roll

Computer System Security, Project One, Addis Ababa Science and Technology University (due 2026-12-07).

A secure, database-driven ordering and management system for a two-branch family restaurant in Addis Ababa. It replaces the earlier static site's WhatsApp/Google Sheets ordering with authenticated accounts, role-based staff dashboards, audited actions, and tested backups.

> Development uses fake data only. It is not connected to the restaurant's live operations.

## Stack

PHP 8.3, Laravel, MySQL, Blade, TOTP MFA, PHPUnit, Semgrep, OWASP ZAP, Composer audit, GitHub Actions.

## Scope

- Customers: register, verify email, sign in with MFA option, order for pickup, track orders.
- Staff and managers: process orders for their own branch only.
- Admin: manage accounts, approve role changes, run backups and access reviews.
- Security auditor: read-only access to audit logs and alerts.

Out of scope for version one: online payment, delivery, mobile app.

## Security design

| Area | Approach |
| --- | --- |
| Access control | RBAC, least privilege, branch scoping, role-change approval with separation of duties |
| Authentication | 12+ char passwords, breached-password check, Argon2id/bcrypt, lockout, rate limiting, TOTP MFA with recovery codes |
| Tokens | Opaque short-lived access token + rotating refresh token, hashed in the DB, HttpOnly cookies, reuse detection |
| Audit | Structured, hash-chained, append-only audit log; JSON log lines ready for SIEM ingestion |
| Backups | Scheduled encrypted backups, 3-2-1, immutable copy, restore tests (RPO 24h, RTO 4h) |
| App security | Server-side validation, parameterized queries, output encoding, CSP and other security headers |
| Pipeline | PHPUnit, Semgrep, Composer audit in CI; ZAP scan against a local staging copy |

## Documentation

- [Threat model](docs/threat-model.md)
- [Permission matrix](docs/permission-matrix.md)
- [Risk register](docs/risk-register.md)
- [Local setup](docs/SETUP.md)
- [Security policy](SECURITY.md)

## Status

Week 1: design documents and database schema. Build order: auth and RBAC, MFA, ordering, audit logs and alerts, validation and headers, backups, testing and CI, then SIEM integration.
