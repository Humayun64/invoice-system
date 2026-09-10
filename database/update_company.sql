USE skm_laravel;
ALTER TABLE company 
ADD COLUMN badge_left VARCHAR(255) DEFAULT NULL AFTER conclusion_text,
ADD COLUMN badge_right VARCHAR(255) DEFAULT NULL AFTER badge_left,
ADD COLUMN signatory_name VARCHAR(100) DEFAULT 'Mr. Shahadat' AFTER badge_right,
ADD COLUMN signatory_title VARCHAR(100) DEFAULT 'Operations Manager' AFTER signatory_name,
ADD COLUMN important_clauses TEXT AFTER signatory_title;

UPDATE company SET important_clauses = '1. Deposit & Cancellation Clause
Upon acceptance of this quotation, a deposit is required for commencement of works, including permit applications (e.g. HDB where applicable), material procurement, and project preparation for HDB, landed, and commercial projects.
In the event of cancellation, postponement, or withdrawal after confirmation, the Company reserves the right to deduct from the deposit all costs incurred, including but not limited to permit fees, materials purchased, work completed, manpower commitments, and administrative expenses.
If incurred costs exceed the deposit, the Client shall be liable for the balance. Any remaining deposit, after deduction of incurred costs, may be refunded at the Company’s discretion. Supporting documents will be provided upon reasonable request.

2. Variation Works
Any additional works not indicated in the drawings or scope shall be treated as variation works and charged accordingly.

3. Late Payment Clause
The Company further reserves the right to suspend work or services, upon written notice, in the event of continued non-payment, without prejudice to any other rights or remedies available under this Agreement or at law.' WHERE id = 1;
