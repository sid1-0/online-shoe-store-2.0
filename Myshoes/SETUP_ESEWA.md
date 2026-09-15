# eSewa Integration — Setup Guide

## 1. Run the database migration
Open phpMyAdmin (or your MySQL client) on the `shoes` database and run:
```
Admin/migration_add_esewa.sql
```
This adds `payment_method`, `payment_status`, `transaction_uuid`, and `amount`
columns to `purchase_request`.

## 2. Test it immediately (no merchant account needed)
The integration ships in **test mode** by default (`User/esewa_config.php`,
`ESEWA_MODE = 'test'`), using eSewa's official public sandbox. Nothing to sign
up for — just:

1. Add items to cart, go to checkout, choose "Pay with eSewa".
2. You'll be redirected to eSewa's test payment page.
3. Log in with a test eSewa ID:
   - eSewa ID: `9806800001` (or ...002 / ...003 / ...004 / ...005)
   - Password: `Nepal@123`
   - MPIN: `1122`
   - OTP: `123456`
4. You'll be redirected back and the order will appear in "My Orders" with
   payment status "Paid", and in the admin Orders page.

## 3. Go live
Once eSewa approves your merchant application (you'll need business
registration docs, PAN, and a bank account — this part is free, eSewa waives
integration fees, but they take a ~2-3.5% cut per transaction):

1. Open `User/esewa_config.php`
2. Change `ESEWA_MODE` to `'live'`
3. Fill in your real `ESEWA_MERCHANT_CODE` (Product Code) and `ESEWA_SECRET_KEY`
   from your eSewa merchant dashboard.
4. Update `ESEWA_SUCCESS_URL` and `ESEWA_FAILURE_URL` to your real https domain
   (eSewa requires https in live mode).

## What changed
- `User/esewa_config.php` — credentials/URLs (test vs live)
- `User/esewa_helper.php` — signature generation & verification
- `User/esewa_initiate.php` — builds the signed form, redirects to eSewa
- `User/esewa_success.php` — verifies payment, writes the order to the DB
- `User/esewa_failure.php` — handles cancelled/failed payments, keeps cart intact
- `User/place_order.php` — checkout now offers "Pay with eSewa" or the
  existing manual QR upload, side by side
- `Admin/orders.php` — now shows payment method/status/transaction ID
- `Admin/migration_add_esewa.sql` — DB migration (run this once)

## Security notes
- The success callback is verified two ways: (1) the HMAC-SHA256 signature
  eSewa signs the response with, checked against your secret key, and (2) an
  independent server-to-server status check against eSewa's API. An order is
  only written to the database if both agree the payment is `COMPLETE`.
- Your secret key never touches the browser — it's only used server-side to
  sign the outgoing request and verify the incoming one.
- The manual QR flow is untouched and still available as a fallback.
