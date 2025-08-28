# Session Hijacking Simulation and Prevention

A professional, educational PHP project demonstrating how session hijacking works and how to defend against it. Includes authentication, a safe simulation, and prevention techniques using modern best practices.

## Features
- Secure authentication (PHP + PDO + MySQL) with password hashing
- Hardened sessions: HttpOnly, Secure, SameSite, regeneration, timeouts
- Session hijacking simulation (educational, non-exploitative)
- Prevention demo: CSRF protection, fingerprint checks, output escaping
- Clean, responsive UI with explanatory content

## How Session Hijacking Happens
- Exposed session IDs (e.g., in URLs), interception over insecure HTTP
- XSS stealing cookies, malicious extensions or malware
- Predictable or reused session identifiers

This project demonstrates the risk of URL-based SID leakage by sending a fake SID to a mock attacker page. Real SIDs are never exposed.

## Prevention Techniques Used
- **Session security**: `HttpOnly`, `Secure` (on HTTPS), `SameSite=Lax` cookies
- **Regenerate ID**: `session_regenerate_id(true)` after login
- **Timeouts**: Idle (15m) and absolute (8h) session expiration
- **Binding**: Session bound to IP + User-Agent (basic anomaly detection)
- **CSRF**: Token generation/validation for POST requests
- **XSS**: All outputs escaped via `htmlspecialchars()` helper
- **SQL Injection**: Fully prepared statements; emulation disabled

## Project Structure
```
/session-hijacking-prevention  
├── /config  
│   └── db.php          # Database connection using PDO  
│   └── session.php     # Secure session handling functions  
├── /auth  
│   └── register.php    # User registration (with password hashing)  
│   └── login.php       # User login (with session regeneration)  
│   └── logout.php      # Secure logout (destroy session)  
├── /dashboard  
│   └── index.php       # User dashboard (only accessible after login)  
│   └── profile.php     # User profile page  
├── /simulation  
│   └── hijack_demo.php # Page demonstrating session hijacking simulation  
│   └── steal.php       # Fake attacker script that attempts to steal session  
├── /prevention  
│   └── secure_demo.php # Page showing prevention techniques in action  
│   └── csrf_token.php  # CSRF token generation & validation  
├── /assets  
│   └── style.css       # Professional, responsive CSS for UI  
├── /docs  
│   └── README.md       # Documentation (this file)  
├── index.php           # Landing page (intro + links to login/register)  
└── database.sql        # SQL file to create `users` table  
```

## Setup Instructions
1. Create a MySQL database, e.g., `session_security`.
2. Import `database.sql` into the database.
3. Configure environment variables for DB (recommended) or edit `config/db.php`:
   - `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`, `DB_PORT`
4. Place the project folder under your PHP web server root, e.g.,
   - Apache: `http://localhost/session-hijacking-prevention/`
5. Ensure HTTPS in production to enable `Secure` cookie flag.

## Usage
- Visit `index.php` → Sign up → Log in
- Explore Dashboard, Hijacking Demo, and Prevention Demo pages
- Use the CSRF-protected action to see token validation in action

## Notes on Security
- The hijacking demo uses fake data and never leaks real session IDs
- Session IDs are not accepted via URLs (`session.use_only_cookies=1`)
- Consider more advanced device binding and risk engines in production

## License
For educational purposes. Use responsibly.