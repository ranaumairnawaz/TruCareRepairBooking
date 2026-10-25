# 🔧 TruCare — Electronics & Device Repair Booking System

<p align="center">
  <img src="https://img.shields.io/badge/PHP-7.4%2B%20%7C%208.x-777BB4?style=for-the-badge&logo=php" alt="PHP">
  <img src="https://img.shields.io/badge/TailwindCSS-Compiled-06B6D4?style=for-the-badge&logo=tailwindcss" alt="TailwindCSS">
  <img src="https://img.shields.io/badge/MySQL-PDO-4479A1?style=for-the-badge&logo=mysql" alt="MySQL">
  <img src="https://img.shields.io/badge/WhatsApp-Alerts-25D366?style=for-the-badge&logo=whatsapp" alt="WhatsApp">
</p>

---

## 📖 What is TruCare?

**TruCare** is a modern, responsive **electronics and device repair service booking platform** built with **Core PHP, Vanilla JavaScript, and Tailwind CSS**. Designed for smartphone, tablet, laptop, and gaming console repair centers, TruCare replaces chaotic walk-in clipboards and manual phone intakes with an automated 4-step appointment booking wizard and real-time administrative workflow.

---

## 🎯 Why TruCare? Problem & Solution

| The Challenge | How TruCare Solves It |
| :--- | :--- |
| **Time-Consuming Intake Phone Calls:** Repair shops spend hours every day quoting simple screen replacements, battery fixes, and diagnostic fees over the phone. | Provides an interactive **4-step AJAX booking wizard** allowing customers to select their device brand, model, issue type, and appointment slot with instant price estimation. |
| **Missed Repair Appointments:** Customers forget scheduled drop-off times, leading to technician downtime. | Automatically dispatches **instant email receipts (PHPMailer)** to the customer and real-time **WhatsApp alerts** to the repair technician team. |
| **Fragmented Repair Tracking:** Customers frequently call asking for repair progress updates. | Provides a centralized admin panel where technicians update ticket statuses (Diagnosing, Waiting for Parts, Repaired, Ready for Pickup) with transparent history logs. |

---

## 🌟 Key Features

### 📱 Customer Storefront & Booking Wizard
- **Interactive 4-Step Wizard:** Step 1: Device Category & Model &rarr; Step 2: Repair Issue & Description &rarr; Step 3: Date, Time & Preferred Branch &rarr; Step 4: Contact Details & Confirmation.
- **Zero Page Reloads (AJAX):** Fast, smooth transitions between steps with real-time field validation.
- **Service Catalog:** Public catalog showcasing repair services, estimated completion times, warranty info, and transparent pricing.
- **Responsive Mobile UI:** Polished, touch-friendly interface designed with Tailwind CSS.

### 🛡️ Administrative Control Center
- **Repair Ticket Pipeline:** View all active bookings, filter by status (Pending, In Progress, Completed, Cancelled), and update technician notes.
- **Store Configuration:** Customize store hours, working days, available time slots, and service categories without writing code.
- **WhatsApp Gateway Integration:** Connect an external HTTP WhatsApp gateway to receive instant booking notifications directly on the shop's business phone.
- **SMTP Email Configuration:** Setup SMTP settings to automatically send branded confirmation emails to customers.

### 🔒 Enterprise-Grade Security
- Secure bcrypt password hashing for admin accounts.
- 8-hour stateless JWT session authentication.
- CSRF token validation on booking submissions.
- Parameterized PDO prepared statements preventing SQL injection.
- Upload file validation using magic-byte verification.

---

## 🛠️ Technical Stack

- **Backend:** Core PHP 7.4+ / PHP 8.x (Clean, lightweight architecture without heavy framework overhead)
- **Database:** MySQL / MariaDB via PDO
- **Frontend:** Vanilla JavaScript (ES6+), Compiled Tailwind CSS, HTML5
- **Email Service:** PHPMailer via Composer
- **Messaging Alerts:** HTTP-based WhatsApp Notification Gateway

---

## 📦 Project Structure

```
trucare/
├── index.php, services.php, about.php, contact.php, booking.php
├── admin/index.php          ← Comprehensive admin dashboard
├── includes/                ← Reusable PHP partials (nav, footer, components)
├── assets/css/app.css       ← Optimized site styles (Tailwind CSS)
├── assets/js/               ← booking-wizard.js, admin.js, main.js
├── api/                     ← RESTful JSON API endpoints
├── config.php               ← Database, JWT secret, and path settings
├── install.sql              ← Complete MySQL schema
├── setup.php                ← Automated one-time installation script
└── uploads/                 ← Secure media and attachment storage
```

---

## 🚀 Quick Setup (XAMPP / Local)

### 1. Requirements
- Apache web server with PHP 7.4 or 8.x
- MySQL 5.7+ or MariaDB
- Composer

### 2. Setup Database & Config
1. Move the project folder to your web root (`htdocs/trucare`).
2. Update database credentials in `config.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'trucare_db');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   ```
3. Run the automated setup script in your browser or terminal:
   ```bash
   php setup.php
   ```
   *(Or navigate to `http://localhost/trucare/setup.php` in your browser)*

4. Install PHPMailer:
   ```bash
   composer install
   ```

5. Access the application:
   - **Customer Portal:** `http://localhost/trucare/`
   - **Admin Login:** `http://localhost/trucare/admin/` (Default: `admin` / `admin123`)

---

## 👨‍💻 Author
**Rana Umair Nawaz**  
- [GitHub Profile](https://github.com/ranaumairnawaz)  
- [LinkedIn Profile](https://linkedin.com/in/ranaumairnawaz)
