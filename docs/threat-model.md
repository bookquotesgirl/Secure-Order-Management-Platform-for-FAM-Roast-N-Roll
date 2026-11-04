# Threat Model (STRIDE)

System: Secure Order Management Platform for FAM Roast N' Roll. Laravel monolith + MySQL, accessed by customers, branch staff, managers, admins and a security auditor, over HTTPS.

## Assets (data classification)

| Asset | Class | Why it matters |
| --- | --- | --- |
| Password hashes, MFA secrets, token hashes | Restricted | Account takeover |
| Customer name, email, phone, order history | Confidential | Privacy, Ethiopian data-protection obligations |
| Audit logs | Confidential | Forensics; must be tamper-evident |
| Orders and menu | Internal | Business operation |
| Public menu and branch info | Public | None |

## Trust boundaries

1. Internet to web server (TLS termination).
2. Web server to application (PHP).
3. Application to database and log storage.
4. Application to backup storage (offsite).

## Threats and mitigations

| # | STRIDE | Threat | Mitigation | Evidence |
| --- | --- | --- | --- | --- |
| T1 | Spoofing | Credential stuffing or brute force on login | Rate limit, lockout after 5 failures, breached-password check, MFA | Login tests, lockout test |
| T2 | Spoofing | Fake account creation by bots | CAPTCHA, email verification | Registration test |
| T3 | Spoofing | Stolen refresh token replayed | Hashed storage, rotation, reuse detection revokes the family, HttpOnly cookies | Token tests |
| T4 | Tampering | SQL injection in menu search or order input | Eloquent/parameterized queries, server-side validation | SAST + DAST results |
| T5 | Tampering | Customer alters price or quantity in the request | Server recomputes totals from DB prices; snapshot stored | Order test |
| T6 | Tampering | Admin edits audit rows to hide actions | Hash chain, append-only DB triggers, auditor-only access, `audit:verify` | Tamper demo |
| T7 | Repudiation | Staff denies changing an order status | Audit log with user, IP, timestamp, action | Audit records |
| T8 | Info disclosure | One customer reads another's order (IDOR) | Policies on every route, non-sequential order numbers | Negative tests |
| T9 | Info disclosure | Stack traces or DB errors shown to users | Generic error pages, `APP_DEBUG=false`, internal logs | Error test |
| T10 | Info disclosure | XSS steals a session | Output encoding (Blade), CSP, HttpOnly cookies | ZAP scan |
| T11 | Info disclosure | Secrets or real data committed to GitHub | `.env` ignored, secret scan in CI, fake data only | CI report |
| T12 | Denial of service | Request floods on login or order endpoints | Rate limiting, input size limits | Repeated-request test |
| T13 | Denial of service | Ransomware or disk loss destroys data | 3-2-1 backups, immutable copy, restore test (RPO 24h, RTO 4h) | Restore log |
| T14 | Elevation | Staff escalates to admin without approval | Approval workflow, SoD CHECK constraint, access review | Negative tests |
| T15 | Elevation | CSRF triggers privileged actions | CSRF tokens, SameSite cookies | Security tests |
| T16 | Elevation | Vulnerable dependency exploited | `composer audit`, Dependabot, CI | CI report |

## Out of scope (first version)

Online payments, delivery tracking, mobile app, real customer data, integration with live restaurant operations.
