# Repair Shop Platform

A full-stack repair-service booking site with a public storefront, a smart booking wizard, and an admin panel — built with **plain HTML + PHP + vanilla JavaScript** (no React/Vue) and **MySQL**.

## Stack

| Layer | Technology |
|-------|------------|
| Frontend | PHP templates, Tailwind CSS (compiled), vanilla JS |
| Backend | Core PHP 7.4+ |
| Database | MySQL via PDO |
| Email | PHPMailer (Composer) |
| WhatsApp | External HTTP gateway (admin alerts only) |

## Quick start (XAMPP)

1. Copy the project to `htdocs/repair-shop-php`
2. Start **Apache** and **MySQL** in XAMPP
3. Edit `config.php` — set MySQL credentials and `BASE_PATH` (default: `/repair-shop-php`)
4. Run setup once:

```bash
php setup.php
```

Or visit `http://localhost/repair-shop-php/setup.php` in the browser.

5. Install PHPMailer:

```bash
composer install
```

6. Open `http://localhost/repair-shop-php/`

**Default admin login:** `admin` / `admin123` — change the password in MySQL after first login.

## Project structure

```
repair-shop-php/
├── index.php, services.php, about.php, contact.php, booking.php
├── admin/index.php          ← Admin panel
├── includes/                ← PHP partials (nav, footer, sections)
├── assets/css/app.css       ← Site styles (Tailwind compiled)
├── assets/js/               ← booking-wizard.js, admin.js, main.js
├── api/                     ← JSON API (booking, settings, auth, upload)
├── config.php               ← MySQL, JWT secret, BASE_PATH
├── install.sql              ← MySQL schema
├── setup.php                ← One-time database bootstrap
└── uploads/                 ← Admin-uploaded images
```

## Pages

| URL | Description |
|-----|-------------|
| `/` | Home (hero, services, about, contact) |
| `/services.php` | Services catalog |
| `/about.php` | About + services |
| `/contact.php` | Contact + map + service areas |
| `/booking.php` | 4-step booking wizard (AJAX, no reload between steps) |
| `/admin/` | Admin panel (double-click logo or navigate directly) |

## Notifications

| Event | Customer | Admin |
|-------|----------|-------|
| New booking | Email (SMTP) | WhatsApp |
| Status change | — | — |

Configure SMTP and WhatsApp in **Admin → WhatsApp Gateway & SMTP Config**.

## Security

- Bcrypt admin passwords (stored in `admin_users` table), JWT sessions (8h)
- CSRF protection on booking submission
- PDO prepared statements (MySQL)
- Input sanitization, rate limiting, upload magic-byte validation
- Security headers via `api/helpers.php`

## API endpoints

| Method | Path | Auth |
|--------|------|------|
| GET | `/api/settings` | Public |
| POST | `/api/booking` | Public + CSRF |
| POST | `/api/admin/login` | Public |
| POST | `/api/admin/verify` | Public (alias of login) |
| POST | `/api/admin/logout` | Public |
| GET | `/api/bookings` | Admin |
| POST | `/api/settings/public` | Admin |
| POST | `/api/settings/secrets` | Admin |
| POST | `/api/upload` | Admin |
