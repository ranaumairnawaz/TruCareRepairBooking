-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jul 24, 2026 at 08:23 PM
-- Server version: 11.8.8-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u344500041_trucare`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `createdAt` varchar(32) NOT NULL,
  `updatedAt` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `password_hash`, `createdAt`, `updatedAt`) VALUES
(1, 'admin', '$2y$10$c0H0r.yYAaROypYrRQ9fBuZEJYY.FkmzcIg/IxZV/dXwSdwuvAgA2', '2026-07-11T19:43:22.000Z', '2026-07-11T19:43:22.000Z');

-- --------------------------------------------------------

--
-- Table structure for table `app_locations`
--

CREATE TABLE `app_locations` (
  `id` varchar(64) NOT NULL,
  `country` varchar(128) NOT NULL,
  `cities` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `app_machines`
--

CREATE TABLE `app_machines` (
  `id` varchar(64) NOT NULL,
  `name` varchar(255) NOT NULL,
  `icon` varchar(64) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `app_services`
--

CREATE TABLE `app_services` (
  `id` varchar(64) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(64) DEFAULT NULL,
  `price` varchar(64) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `seoTitle` varchar(255) DEFAULT NULL,
  `seoDescription` text DEFAULT NULL,
  `seoKeywords` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `app_settings`
--

CREATE TABLE `app_settings` (
  `key` varchar(128) NOT NULL,
  `value` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` char(36) NOT NULL,
  `name` varchar(120) NOT NULL,
  `email` varchar(180) NOT NULL,
  `phone` varchar(40) NOT NULL,
  `machine` varchar(120) NOT NULL,
  `symptom` varchar(120) DEFAULT NULL,
  `country` varchar(100) NOT NULL,
  `city` varchar(100) NOT NULL,
  `address` varchar(300) NOT NULL,
  `date` varchar(60) NOT NULL,
  `timeSlot` varchar(60) NOT NULL,
  `message` text DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `createdAt` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `name`, `email`, `phone`, `machine`, `symptom`, `country`, `city`, `address`, `date`, `timeSlot`, `message`, `status`, `createdAt`) VALUES
('0c71eeef-989b-4b1a-bf2c-b6b4714f37b2', 'Rana Umair Nawaz', 'ranaumairnawaz@gmail.com', '03164851631', 'AC Repair', '🔴 Critical Urgent Service', 'United Arab Emirates', 'Dubai', 'Test address', 'Same-Day Emergency', 'ASAP (Emergency Dispatch)', 'Urgency Mode: 🔴 IMMEDIATE CRITICAL EMERGENCY DISPATCH\nNo further description details provided.', 'cancelled', '2026-07-11T06:36:04.434Z'),
('1b249f80-58be-403b-93eb-47fb0af90641', 'Rana Umair Nawaz', 'ranaumairnawaz@gmail.com', '923164851631', 'Freezer Repair', '🔴 Critical Urgent Service', 'United Arab Emirates', 'Dubai', 'Test Address', 'Same-Day Emergency', 'ASAP (Emergency Dispatch)', 'Urgency Mode: 🔴 IMMEDIATE CRITICAL EMERGENCY DISPATCH\nNo further description details provided.', 'pending', '2026-07-11T08:46:17.099Z'),
('80420c47-a0bb-4b42-881b-dc2860382d63', 'Test', 'ranaumairnawaz@gmail.com', '03164851631', 'Dishwasher Repair', '🔴 Critical Urgent Service', 'United Arab Emirates', 'Dubai', 'test address', 'Same-Day Emergency', 'ASAP (Emergency Dispatch)', 'Urgency Mode: 🔴 IMMEDIATE CRITICAL EMERGENCY DISPATCH\nNo further description details provided.', 'pending', '2026-07-11T08:42:42.562Z'),
('af7d25f0-667b-4abb-aab4-45cf76758d54', 'Rana Umair Nawaz', 'ranaumairnawaz@gmail.com', '03164851631', 'AC Repair', '🔴 Critical Urgent Service', 'United Arab Emirates', 'Dubai', 'House no 13, Street no 15, Main Bulebard Shadman', 'Same-Day Emergency', 'ASAP (Emergency Dispatch)', 'Urgency Mode: 🔴 IMMEDIATE CRITICAL EMERGENCY DISPATCH\nNo further description details provided.', 'cancelled', '2026-07-10T20:05:09.842Z'),
('d167b17e-d408-4cb3-9b34-67d452ca9188', 'Rana Umair Nawaz', 'ranaumairnawaz@gmail.com', '03164851631', 'AC Repair', '🔴 Critical Urgent Service', 'United Arab Emirates', 'Dubai', 'Test Address', 'Same-Day Emergency', 'ASAP (Emergency Dispatch)', 'Urgency Mode: 🔴 IMMEDIATE CRITICAL EMERGENCY DISPATCH\nNo further description details provided.', 'cancelled', '2026-07-11T08:35:46.608Z');

-- --------------------------------------------------------

--
-- Table structure for table `kv_settings`
--

CREATE TABLE `kv_settings` (
  `key` varchar(64) NOT NULL,
  `value` longtext NOT NULL,
  `updatedAt` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kv_settings`
--

INSERT INTO `kv_settings` (`key`, `value`, `updatedAt`) VALUES
('public', '{\"platformName\":\"TruCare Appliances Repair\",\"favicon\":\"\\/uploads\\/166c915e138bc89fd478623fa3221ead.png\",\"logo\":\"⚙️ TruCare Repair\",\"primaryColor\":\"#eb6924\",\"secondaryColor\":\"#143176\",\"textColor\":\"#1e293b\",\"backgroundColor\":\"#f8fafc\",\"headingFont\":\"Merriweather\",\"bodyFont\":\"Inter\",\"footerDescription\":\"Prompt, transparent, and certified appliance repair specialists. Serving households and businesses with absolute craftsmanship.\",\"hero\":{\"title\":\"Fast & Reliable Appliance Repair in Dubai\",\"subtitle\":\"Same-day AC, washing machine, dishwasher, cooker & freezer repair across Dubai — plus trusted plumbing and electrical services. Certified technicians, transparent pricing, and fast dispatch when you need it most.\",\"ctaText\":\"Book Now\",\"image\":\"\\/uploads\\/f9a143d8639386253caa76aa0a29e430.png\"},\"services\":[{\"id\":\"1783712946848\",\"name\":\"AC Repair\",\"description\":\"Reliable AC Repair Services | Trucare Appliances Repair Is your air conditioner blowing warm air or making strange noises? Trust Trucare Appliances Repair for fast, expert AC repair and maintenance. Our certified technicians handle all major brands to restore your home’s cooling fast. Book your expert service online today!\",\"icon\":\"Gauge\",\"price\":\"\"},{\"id\":\"1783712985947\",\"name\":\"Washing Machine repair\",\"description\":\"Need fast washing machine repair in Dubai? Trust Trucare Appliances Repair. We fix all major washer brands, leaks, & spin issues with same-day home service. Book now!\",\"icon\":\"Sparkles\",\"price\":\"\"},{\"id\":\"1783713014796\",\"name\":\"Dishwasher Repair\",\"description\":\"Expert dishwasher repair in Dubai. We fix all major brands, leaks, drainage issues & error codes with same-day home service. Book Trucare Appliances Repair now!\",\"icon\":\"Tv2\",\"price\":\"\"},{\"id\":\"1783713054377\",\"name\":\"Gas Coocker Repair\",\"description\":\"Safe & reliable gas cooker repair in Dubai. We fix gas hobs, burner ignition issues, & ovens with same-day home service. Book Trucare Appliances Repair now!\",\"icon\":\"Flame\",\"price\":\"\"},{\"id\":\"1783713114043\",\"name\":\"Freezer Repair\",\"description\":\"Fast deep freezer repair in Dubai. We fix all major brands, cooling failures, ice build-up & leaks with same-day home service. Book Trucare Appliances Repair now!\",\"icon\":\"IceCream\",\"price\":\"\"},{\"id\":\"1783713183685\",\"name\":\"Plumber Services\",\"description\":\"Need an expert plumber in Dubai? Trucare offers reliable, same-day plumbing services. We fix leaks, unblock drains, & repair water heaters. Book online now!\",\"icon\":\"Wrench\",\"price\":\"\"},{\"id\":\"1783713250763\",\"name\":\"Electrical Work\",\"description\":\"Need a certified electrician in Dubai? Trucare offers reliable, same-day electrical repairs. We fix tripping breakers, short circuits, & install lighting. Book now!\",\"icon\":\"Plug\",\"price\":\"\"}],\"machines\":[{\"id\":\"1783712418232\",\"name\":\"Air Conditioner\",\"icon\":\"Gauge\"},{\"id\":\"1783712442218\",\"name\":\"Washing Machine\",\"icon\":\"Sparkles\"},{\"id\":\"1783712465810\",\"name\":\"Dishwasher\",\"icon\":\"Microwave\"},{\"id\":\"1783712531803\",\"name\":\"Freezer\",\"icon\":\"IceCream\"},{\"id\":\"1783712866636\",\"name\":\"Gas Coocker\",\"icon\":\"Flame\"}],\"serviceLocations\":[{\"id\":\"1783663135283\",\"country\":\"United Arab Emirates\",\"cities\":[\"Dubai\"]}],\"about\":{\"title\":\"Precision Engineering Excellence\",\"text\":\"TruCare Appliances Repair has been serving homes and businesses across Dubai with fast, reliable appliance, AC, plumbing, and electrical repair services. Our certified technicians arrive fully equipped to diagnose and fix the problem in a single visit, using genuine parts and transparent, upfront pricing. Every job is backed by our workmanship guarantee — so you can book with confidence, any time you need us.\",\"features\":[\"Same-Day Emergency On-Site Service Available\",\"Fully Certified Heavy Machinery Specialist Technicians\",\"Upfront Diagnostic Audits & Fixed Price Quotes\",\"90-Day Parts & Labor Service Guarantee\"],\"image\":\"\\/uploads\\/2dfbbf0297db0f9223dad800d7ab38b2.png\"},\"contact\":{\"title\":\"Connect with a Specialist\",\"subtitle\":\"Have a critical machine down or need preventative shop maintenance? Get in touch directly or use our automated scheduler.\",\"phone\":\"+971 52 584 2465\",\"email\":\"service@precisionmech.com\",\"address\":\"Trade Center First, Al Satwa - Dubai - United Arab Emirates\",\"hours\":\"Mon - Sat: 8:00 AM - 6:00 PM, Sun: Closed\",\"mapEmbedUrl\":\"https:\\/\\/www.google.com\\/maps\\/embed?pb=!1m18!1m12!1m3!1d28875.902902433278!2d55.2530074461002!3d25.22048804747991!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3e5f42f52c7f9adb%3A0xfd52c7771df58061!2sAl%20Satwa%20-%20Dubai%20-%20United%20Arab%20Emirates!5e0!3m2!1sen!2s!4v1783802531079!5m2!1sen!2s\"},\"seo\":{\"home\":{\"title\":\"AC, Appliance, Plumbing & Electrical Repair Dubai - TruCare\",\"description\":\"Same-day AC, washing machine, dishwasher, cooker & freezer repair in Dubai. Trusted plumbing & electrical services. Book online or on WhatsApp today.\",\"ogImage\":\"\\/repair-shop-php\\/uploads\\/6ee37ff280e350cd60ac2dba2d75493a.png\"},\"about\":{\"title\":\"About TruCare | Trusted Home Repair Experts in Dubai\",\"description\":\"Meet the certified technicians behind TruCare — serving Dubai homes and businesses with reliable AC, appliance, plumbing & electrical repairs.\",\"ogImage\":\"\\/repair-shop-php\\/uploads\\/b9f08eaa77af138f0466b6ce54b4d886.png\"},\"contact\":{\"title\":\"Contact TruCare | AC & Appliance Repair Dubai\",\"description\":\"Get in touch with TruCare for fast AC, appliance, plumbing or electrical repair in Dubai. Call, WhatsApp, or check the areas we serve.\",\"ogImage\":\"\\/repair-shop-php\\/uploads\\/8b42085ceff43581909fb216e9e7189e.png\"},\"booking\":{\"title\":\"Book a Repair Online | AC, Appliance & Plumbing – TruCare\",\"description\":\"Schedule AC, washing machine, dishwasher, cooker or freezer repair — plus plumbing & electrical — in Dubai in minutes. Fast confirmation, easy booking.\",\"ogImage\":\"\\/repair-shop-php\\/uploads\\/fd41e693aab901a521dc525ff478cb94.png\"}}}', '2026-07-11T20:56:50.000Z'),
('secrets', '{\"twilioSid\":\"\",\"twilioToken\":\"\",\"twilioFrom\":\"whatsapp:+14155238886\",\"smtpHost\":\"\",\"smtpPort\":\"587\",\"smtpUser\":\"\",\"smtpPass\":\"ZedXSol@786\",\"emailTemplate\":\"<!DOCTYPE html>\\n<html>\\n<head>\\n    <meta charset=\\\"UTF-8\\\">\\n    <meta name=\\\"viewport\\\" content=\\\"width=device-width, initial-scale=1.0\\\">\\n    <title>Booking Confirmation</title>\\n    <style>\\n        body {\\n            font-family: \'Segoe UI\', Tahoma, Geneva, Verdana, sans-serif;\\n            background-color: #f4f6f9;\\n            margin: 0;\\n            padding: 0;\\n            -webkit-font-smoothing: antialiased;\\n        }\\n        .email-container {\\n            max-width: 600px;\\n            margin: 20px auto;\\n            background-color: #ffffff;\\n            border-radius: 8px;\\n            overflow: hidden;\\n            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);\\n            border: 1px solid #e1e5eb;\\n        }\\n        .header {\\n            background-color: #0056b3; /* Trucare Blue */\\n            padding: 30px 20px;\\n            text-align: center;\\n            border-bottom: 5px solid #ff7300; /* Trucare Orange */\\n        }\\n        .header h1 {\\n            color: #ffffff;\\n            margin: 0;\\n            font-size: 24px;\\n            font-weight: 600;\\n            letter-spacing: 0.5px;\\n        }\\n        .content {\\n            padding: 30px 40px;\\n            color: #333333;\\n            line-height: 1.6;\\n        }\\n        .content h2 {\\n            color: #0056b3;\\n            font-size: 20px;\\n            margin-top: 0;\\n        }\\n        .details-box {\\n            background-color: #f8fafc;\\n            border-left: 4px solid #ff7300;\\n            padding: 20px;\\n            margin: 25px 0;\\n            border-radius: 0 6px 6px 0;\\n        }\\n        .details-table {\\n            width: 100%;\\n            border-collapse: collapse;\\n        }\\n        .details-table td {\\n            padding: 8px 0;\\n            vertical-align: top;\\n            font-size: 15px;\\n        }\\n        .details-table td.label {\\n            width: 35%;\\n            font-weight: bold;\\n            color: #555555;\\n        }\\n        .details-table td.value {\\n            color: #222222;\\n        }\\n        .status-badge {\\n            background-color: #e1f5fe;\\n            color: #0288d1;\\n            padding: 4px 12px;\\n            border-radius: 50px;\\n            font-size: 13px;\\n            font-weight: bold;\\n            display: inline-block;\\n        }\\n        .footer {\\n            background-color: #f1f3f5;\\n            padding: 20px;\\n            text-align: center;\\n            font-size: 13px;\\n            color: #666666;\\n            border-top: 1px solid #e1e5eb;\\n        }\\n        .footer p {\\n            margin: 5px 0;\\n        }\\n    </style>\\n</head>\\n<body>\\n\\n    <div class=\\\"email-container\\\">\\n        <!-- Header Banner -->\\n        <div class=\\\"header\\\">\\n            <h1>TRUCARE APPLIANCES REPAIR</h1>\\n        </div>\\n\\n        <!-- Main Content -->\\n        <div class=\\\"content\\\">\\n            <h2>Booking Confirmation</h2>\\n            <p>Hi {{name}},</p>\\n            <p>Thank you for choosing <strong>{{platformName}}</strong>! Your service booking has been successfully received and is officially on our radar.</p>\\n            \\n            <!-- Appointment Details Box -->\\n            <div class=\\\"details-box\\\">\\n                <table class=\\\"details-table\\\">\\n                    <tr>\\n                        <td class=\\\"label\\\">Machine / Appliance:</td>\\n                        <td class=\\\"value\\\">{{machine}}</td>\\n                    </tr>\\n                    <tr>\\n                        <td class=\\\"label\\\">Issue / Symptom:</td>\\n                        <td class=\\\"value\\\">{{symptom}}</td>\\n                    </tr>\\n                    <tr>\\n                        <td class=\\\"label\\\">Service Location:</td>\\n                        <td class=\\\"value\\\">{{location}}</td>\\n                    </tr>\\n                    <tr>\\n                        <td class=\\\"label\\\">Date & Time Slot:</td>\\n                        <td class=\\\"value\\\" style=\\\"font-weight: 600; color: #0056b3;\\\">{{datetime}}</td>\\n                    </tr>\\n                    <tr>\\n                        <td class=\\\"label\\\">Booking Status:</td>\\n                        <td class=\\\"value\\\"><span class=\\\"status-badge\\\">Scheduled</span></td>\\n                    </tr>\\n                </table>\\n            </div>\\n\\n            <p><strong>What happens next?</strong><br>\\n            Our dedicated technician will review your request and contact you shortly at <strong>{{phone}}</strong> to confirm their exact arrival window.</p>\\n            \\n            <p style=\\\"margin-top: 30px;\\\">Best regards,<br>\\n            <strong>The {{platformName}} Team</strong></p>\\n        </div>\\n\\n        <!-- Footer -->\\n        <div class=\\\"footer\\\">\\n            <p>Expert Service • Reliable & Trusted • Expert Repair</p>\\n            <p style=\\\"font-size: 11px; color: #999999; margin-top: 10px;\\\">\\n                This is an automated confirmation email regarding your repair request. Please do not reply directly to this message.\\n            </p>\\n        </div>\\n    </div>\\n\\n</body>\\n</html>\",\"waApiKey\":\"wa_api_2c1cc6568c22a1f93affc44330dfd533\",\"waGatewayUrl\":\"http://13.60.229.151:3000/api/messages/send\",\"waReceiverNumber\":\"923164851631\"}', '2026-07-11T08:45:52.485Z');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_admin_username` (`username`);

--
-- Indexes for table `app_locations`
--
ALTER TABLE `app_locations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `app_machines`
--
ALTER TABLE `app_machines`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `app_services`
--
ALTER TABLE `app_services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `app_settings`
--
ALTER TABLE `app_settings`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_bookings_created` (`createdAt`),
  ADD KEY `idx_bookings_status` (`status`);

--
-- Indexes for table `kv_settings`
--
ALTER TABLE `kv_settings`
  ADD PRIMARY KEY (`key`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
