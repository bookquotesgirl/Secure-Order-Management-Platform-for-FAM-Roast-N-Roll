# Risk Register (starter)

Likelihood and impact: 1 (low) to 5 (high). Score = L × I. Review at each milestone.

| ID | Risk | L | I | Score | Mitigation | Owner | Status |
| --- | --- | :-: | :-: | :-: | --- | --- | --- |
| R1 | Account takeover via weak or reused passwords | 4 | 4 | 16 | 12+ char policy, breached check, lockout, MFA | Developer | Planned |
| R2 | Refresh token theft | 3 | 4 | 12 | Rotation, reuse detection, HttpOnly cookies | Developer | Planned |
| R3 | Insider misuse of admin rights | 2 | 5 | 10 | SoD approval, auditor oversight, hash-chained logs | Developer | Planned |
| R4 | Data loss or ransomware | 2 | 5 | 10 | 3-2-1 backups, immutable copy, restore tests | Developer | Planned |
| R5 | IDOR exposing other customers' orders | 3 | 4 | 12 | Policies, branch scoping, negative tests | Developer | Planned |
| R6 | Vulnerable dependency | 3 | 3 | 9 | Composer audit, Dependabot, CI gate | Developer | Planned |
| R7 | Secrets committed to GitHub | 2 | 5 | 10 | `.gitignore`, `.env.example` only, secret scanning | Developer | Planned |
| R8 | Demo laptop failure during presentation | 3 | 3 | 9 | Recorded backup video, tagged release, restore script | Developer | Planned |
| R9 | Schedule overrun (solo, 33 days) | 4 | 3 | 12 | Tiered scope, SIEM last, weekly milestones | Developer | Active |
| R10 | Privacy breach of customer data | 2 | 4 | 8 | Encryption, minimal collection, retention rules, fake data in dev | Developer | Planned |
