# Security Policy

## Supported versions

The `2.x` branch targets Flarum 2 and is covered by the automated checks described
in this repository. Install the latest release compatible with your forum. Reports
affecting the separate Flarum 1.x branch are also welcome; include the exact version
so applicability and any necessary backport can be assessed.

## Reporting a vulnerability

Use [GitHub's private vulnerability reporting](https://github.com/huseyinfiliz/traderfeedback/security/advisories/new)
or email **mysuperuser01@gmail.com** with the subject `Trader Feedback security report`.
Include the extension and Flarum versions, relevant settings and integrations,
the reader's permissions, and minimal reproduction steps using synthetic data.

Do not include passwords, access tokens, database credentials, or other users'
personal data. Please coordinate disclosure privately before publishing details.

## Access boundaries

Approved feedback and trader statistics are readable by guests only when the
forum's policies allow them to view the forum. Pending feedback requires feedback
moderation permission. Supported profile-privacy integrations may further restrict
feedback records according to the recipient's privacy settings.

Public reputation badges and ordinary profile metadata are not made confidential
solely by hiding the feedback profile tab. Discussion visibility is evaluated
separately from feedback visibility.
