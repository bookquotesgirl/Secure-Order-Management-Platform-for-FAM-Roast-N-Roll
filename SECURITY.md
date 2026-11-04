# Security Policy

This is a university project using fake data only. No real customer information should ever be committed to this repository or entered into a development instance.

## Reporting a vulnerability

Open a private report through GitHub Security Advisories on this repository, or contact the project owner directly. Please do not open a public issue for a security problem. Expect an acknowledgement within 3 days.

## Secrets

Never commit `.env`, API keys, database passwords or backup files. Use `.env.example` for placeholders. If a secret is committed by mistake, rotate it immediately and treat it as compromised.

## Supported version

Only the `main` branch is supported.
