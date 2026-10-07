-- Repair Shop — MySQL schema
-- Run once: mysql -u root -p < install.sql

CREATE DATABASE IF NOT EXISTS repair_shop
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE repair_shop;

CREATE TABLE IF NOT EXISTS kv_settings (
  `key` VARCHAR(64) NOT NULL PRIMARY KEY,
  `value` LONGTEXT NOT NULL,
  updatedAt VARCHAR(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS bookings (
  id CHAR(36) NOT NULL PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(180) NOT NULL,
  phone VARCHAR(40) NOT NULL,
  machine VARCHAR(120) NOT NULL,
  symptom VARCHAR(120) DEFAULT NULL,
  country VARCHAR(100) NOT NULL,
  city VARCHAR(100) NOT NULL,
  address VARCHAR(300) NOT NULL,
  `date` VARCHAR(60) NOT NULL,
  timeSlot VARCHAR(60) NOT NULL,
  message TEXT,
  status VARCHAR(20) NOT NULL DEFAULT 'pending',
  createdAt VARCHAR(32) NOT NULL,
  INDEX idx_bookings_created (createdAt DESC),
  INDEX idx_bookings_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS admin_users (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(64) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  createdAt VARCHAR(32) NOT NULL,
  updatedAt VARCHAR(32) NOT NULL,
  UNIQUE KEY uk_admin_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
