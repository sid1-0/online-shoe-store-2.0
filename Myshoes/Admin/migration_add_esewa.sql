-- Run this once against your `shoes` database before using the eSewa integration.
-- Safe to re-run: it checks for column existence first via IF NOT EXISTS where supported.

ALTER TABLE purchase_request
    ADD COLUMN payment_method VARCHAR(20) NOT NULL DEFAULT 'Manual QR' AFTER phone,
    ADD COLUMN payment_status VARCHAR(20) NOT NULL DEFAULT 'Unverified' AFTER payment_method,
    ADD COLUMN transaction_uuid VARCHAR(64) NULL AFTER payment_status,
    ADD COLUMN amount DECIMAL(10,2) NULL AFTER transaction_uuid;

-- payment_method: 'Manual QR' or 'eSewa'
-- payment_status: 'Unverified' (admin must check screenshot), 'Paid' (confirmed by eSewa), 'Failed'
-- transaction_uuid: eSewa's transaction reference, NULL for manual QR orders
-- amount: the amount charged for that line item, used for eSewa reconciliation
