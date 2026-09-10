-- =============================================
-- SKM Engineering Laravel System
-- Database: skm_laravel
-- Run this in phpMyAdmin
-- =============================================

CREATE DATABASE IF NOT EXISTS skm_laravel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE skm_laravel;

CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(200),
    role ENUM('admin','staff') DEFAULT 'admin',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

CREATE TABLE company (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    address VARCHAR(500),
    phone VARCHAR(50),
    email VARCHAR(100),
    paynow VARCHAR(100),
    bank_name VARCHAR(100),
    bank_account VARCHAR(100),
    cheque_name VARCHAR(200),
    conclusion_text TEXT,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

CREATE TABLE clients (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    designation VARCHAR(200),
    company VARCHAR(200),
    address VARCHAR(500),
    postal_code VARCHAR(20),
    phone VARCHAR(50),
    email VARCHAR(100),
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

CREATE TABLE invoices (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    invoice_no VARCHAR(50) UNIQUE NOT NULL,
    client_id BIGINT UNSIGNED NOT NULL,
    project_title VARCHAR(300),
    invoice_date DATE NOT NULL,
    show_qty TINYINT(1) DEFAULT 0,
    show_deposit TINYINT(1) DEFAULT 1,
    deposit_percent DECIMAL(5,2) DEFAULT 50.00,
    deposit_label VARCHAR(20) DEFAULT '1st',
    status ENUM('draft','sent','paid','cancelled') DEFAULT 'draft',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (client_id) REFERENCES clients(id)
);

CREATE TABLE invoice_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    invoice_id BIGINT UNSIGNED NOT NULL,
    item_order INT DEFAULT 1,
    description TEXT NOT NULL,
    qty DECIMAL(10,2) DEFAULT NULL,
    unit VARCHAR(50) DEFAULT NULL,
    unit_price DECIMAL(12,2) DEFAULT NULL,
    amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE
);

CREATE TABLE quotations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    quotation_no VARCHAR(50) UNIQUE NOT NULL,
    client_id BIGINT UNSIGNED NOT NULL,
    project_title VARCHAR(300),
    quotation_date DATE NOT NULL,
    valid_days INT DEFAULT 15,
    intro_text TEXT,
    show_qty TINYINT(1) DEFAULT 0,
    payment_terms TEXT,
    price_basis TEXT,
    conclusion TEXT,
    status ENUM('draft','sent','accepted','rejected') DEFAULT 'draft',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (client_id) REFERENCES clients(id)
);

CREATE TABLE quotation_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    quotation_id BIGINT UNSIGNED NOT NULL,
    item_order INT DEFAULT 1,
    description TEXT NOT NULL,
    qty DECIMAL(10,2) DEFAULT NULL,
    unit VARCHAR(50) DEFAULT NULL,
    unit_price DECIMAL(12,2) DEFAULT NULL,
    amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (quotation_id) REFERENCES quotations(id) ON DELETE CASCADE
);

-- Default admin user (password: admin123)
INSERT INTO users (username, password, full_name, role, created_at, updated_at) VALUES (
    'admin',
    '$2y$10$tKRNGYGvcWqxG.ugL4j3b.ewFa3HdzGt0RTAJpUHR7NbfmLo88KdG',
    'Administrator',
    'admin',
    NOW(), NOW()
);

-- Company info
INSERT INTO company (name, address, phone, email, paynow, bank_name, bank_account, cheque_name, conclusion_text, created_at, updated_at) VALUES (
    'SKM ENGINEERING PTE LTD',
    '35 Sturdee Road 01-01 Singapore 207847',
    '8267-1782',
    'skmengsg@gmail.com',
    '202234884W',
    'OCBC BANK',
    '5950-6533-5001',
    'SKM Engineering Pte Ltd',
    'Thank you for your time and for considering SKM Engineering Pte. Ltd. for your project. We truly appreciate the trust you place in us.',
    NOW(), NOW()
);

-- Sample client
INSERT INTO clients (name, designation, company, address, postal_code, email, created_at, updated_at) VALUES (
    'Adeline Song', 'HR Manager', 'Lincotrade & Associates Pte Ltd',
    '370A Sembawang Avenue 03-107', '751370', 'adeline@lincotrade.com',
    NOW(), NOW()
);
