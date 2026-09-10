ALTER TABLE invoices ADD COLUMN show_discount TINYINT(1) DEFAULT 0;
ALTER TABLE invoices ADD COLUMN discount_label VARCHAR(100) DEFAULT 'Goodwill Discount';
ALTER TABLE invoices ADD COLUMN discount_type ENUM('amount','percent') DEFAULT 'amount';
ALTER TABLE invoices ADD COLUMN discount_value DECIMAL(12,2) DEFAULT 0;
