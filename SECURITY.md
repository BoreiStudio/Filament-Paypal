# Security Policy

## Reporting a Vulnerability

If you discover a security vulnerability in this plugin, please report it privately before disclosing it publicly.

**Do not report security issues via the public issue tracker.**

Instead, send an email to:

**dev@borei.com.ar**

We will acknowledge receipt within **48 hours** and provide an estimated timeline for a fix.

### What to include

- A clear description of the vulnerability
- Steps to reproduce it (code snippet, configuration, environment)
- The potential impact
- Any suggested fix (if applicable)

### Response timeline

- **48 hours**: Initial acknowledgment
- **7 days**: Status update with estimated fix timeline
- **30 days**: Target for releasing a patched version, depending on severity and complexity

We will coordinate the disclosure date with you once the fix is released.

## Supported Versions

| Version | Supported |
|---|---|
| 1.x | ✅ |

## Security practices

- All credentials (`client_secret`) are stored encrypted in the database.
- Webhook notifications are validated via PayPal's verify-webhook-signature endpoint before processing.
- Refund amounts are validated server-side before calling the PayPal API.
- No sensitive data is logged to any channel.
- Dependencies are kept up to date to minimize known vulnerabilities.
