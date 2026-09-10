USE skm_laravel;

-- Company: signatory + clauses (skip if already added)
ALTER TABLE company 
ADD COLUMN IF NOT EXISTS signatory_name VARCHAR(100) DEFAULT 'Mr. Shahadat',
ADD COLUMN IF NOT EXISTS signatory_title VARCHAR(100) DEFAULT 'Operations Manager',
ADD COLUMN IF NOT EXISTS important_clauses TEXT;

-- Remove old single-badge columns if exist
ALTER TABLE company DROP COLUMN IF EXISTS badge_left;
ALTER TABLE company DROP COLUMN IF EXISTS badge_right;

-- Quotation: discount type (amount or percent)
ALTER TABLE quotations 
ADD COLUMN IF NOT EXISTS show_discount TINYINT(1) DEFAULT 0,
ADD COLUMN IF NOT EXISTS discount_label VARCHAR(100) DEFAULT 'Goodwill Discount',
ADD COLUMN IF NOT EXISTS discount_type ENUM('amount','percent') DEFAULT 'amount',
ADD COLUMN IF NOT EXISTS discount_value DECIMAL(12,2) DEFAULT 0,
ADD COLUMN IF NOT EXISTS important_clauses TEXT;

ALTER TABLE quotations DROP COLUMN IF EXISTS discount_amount;

-- New badges table (unlimited badges, left or right)
CREATE TABLE IF NOT EXISTS badges (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    position ENUM('left','right') NOT NULL DEFAULT 'left',
    image_path VARCHAR(255) NOT NULL,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

UPDATE company SET important_clauses = '1. Deposit & Cancellation Clause
Upon acceptance of this quotation, a deposit is required for commencement of works, including permit applications (e.g. HDB where applicable), material procurement, and project preparation for HDB, landed, and commercial projects.
In the event of cancellation, postponement, or withdrawal after confirmation, the Company reserves the right to deduct from the deposit all costs incurred, including but not limited to permit fees, materials purchased, work completed, manpower commitments, and administrative expenses.
If incurred costs exceed the deposit, the Client shall be liable for the balance. Any remaining deposit, after deduction of incurred costs, may be refunded at the Company\'s discretion. Supporting documents will be provided upon reasonable request.

2. Variation Works
Any additional works not indicated in the drawings or scope shall be treated as variation works and charged accordingly.

3. Late Payment Clause
The Company further reserves the right to suspend work or services, upon written notice, in the event of continued non-payment, without prejudice to any other rights or remedies available under this Agreement or at law.'
WHERE id = 1 AND (important_clauses IS NULL OR important_clauses = '');
