# 🏍️ MotoRelease
### Web-Based OR/CR and License Plate Tracking, Releasing, and Automated Customer Notification System

> **Independent Project Brand Notice:**  
> **MotoRelease** is an independent, dealer-level document and license plate releasing management system designed for motorcycle dealerships (pilot implementation prepared for a participating Yamaha dealership in San Jose de Buenavista, Antique, Philippines).  
> *Note:* This application does not claim official endorsement by Yamaha Philippines nor direct integration with the Land Transportation Office (LTO) government databases. All availability tracking reflects verified physical arrival and release status at the dealership level.

---

## 📋 Table of Contents
1. [System Overview & Problem Statement](#system-overview--problem-statement)
2. [Key Architecture & Core Features](#key-architecture--core-features)
3. [Security, Privacy & RA 10173 Compliance](#security-privacy--ra-10173-compliance)
4. [User Roles & Permissions Matrix](#user-roles--permissions-matrix)
5. [Physical Verification Quality Gates](#physical-verification-quality-gates)
6. [Philippine SMS Integration (Semaphore & Mock)](#philippine-sms-integration)
7. [Customer Tracking Portal & QR Code Access](#customer-tracking-portal--qr-code-access)
8. [Release Management & Double-Claim Prevention](#release-management--double-claim-prevention)
9. [Operational Reports & Audit Trails](#operational-reports--audit-trails)
10. [Pre-Seeded Demonstration Accounts & Scenarios](#pre-seeded-demonstration-accounts--scenarios)
11. [Installation & Setup Guide](#installation--setup-guide)
12. [Automated Test Suite & Verification](#automated-test-suite--verification)

---

## 1. System Overview & Problem Statement

In traditional motorcycle dealership operations in the Philippines, document releases (Official Receipts / Certificates of Registration) and metal/temporary license plates are announced via physical paper lists pinned on dealership notice boards or handled via repetitive customer phone inquiries.

**Observed Bottlenecks:**
- Customers travel long distances to the dealership only to find their documents are not ready.
- Dealership staff repeatedly answer the same status questions, causing service delays.
- Paper lists get lost, damaged, or expose personal customer details to the public.
- Lack of audit logging leads to discrepancies in who claimed which document.
- OR and CR arrival dates differ, creating confusion over whether a vehicle can be legally operated.

**The MotoRelease Solution:**
MotoRelease digitizes customer records, motorcycle profiles, OR/CR document packages, and physical license plates into independent lifecycles. When physical items arrive and pass strict physical verification quality gates, automated SMS messages alert customers with collection instructions, while customers can privately track their status via a mobile-responsive QR-code-enabled web portal.

---

## 2. Key Architecture & Core Features

```
+-------------------------------------------------------------------------------+
|                                  MotoRelease                                  |
|            Laravel 11 LTS | Tailwind CSS | Alpine.js | MySQL | PHP 8.2+       |
+-------------------------------------------------------------------------------+
        |                                                              |
        v                                                              v
+-----------------------------+                               +-----------------------------+
|    Dealership Staff & Admin |                               |   Customer Tracking Portal  |
|    - Dynamic Dashboard      |                               |   - QR Code / Tracking Ref  |
|    - Customer Registration  |                               |   - Last-4 Phone PIN Gate   |
|    - Motorcycle Management  |                               |   - Masked Privacy View     |
|    - Physical Gate Verify   |                               |   - Claim Requirements      |
|    - Releasing & E-Receipt  |                               |   - Branch Directions       |
|    - Reports & Audit Logs   |                               +-----------------------------+
+-----------------------------+                                              ^
        |                                                                    |
        +--------------------> [ Philippine SMS Engine ] --------------------+
                               - Semaphore SMS API Gateway
                               - Queued Background Retries
                               - Idempotency De-duplication
```

- **Backend:** Laravel 11 LTS with Eloquent ORM, Form Requests, Queues, Database Transactions.
- **Frontend:** Blade templates styled with Tailwind CSS, Alpine.js reactive components, and Lucide Icons.
- **Database:** MariaDB / MySQL 8.x with relational foreign key cascades, soft-deletes where applicable, and indexed tracking codes.
- **Pure PHP QR Generation:** Integrated `chillerlan/php-qrcode` generating inline SVG Data URIs (no external third-party API dependencies).
- **Timezone:** Configured for `Asia/Manila` (PHT, UTC+8).

---

## 3. Security, Privacy & RA 10173 Compliance

MotoRelease strictly complies with the **Philippine Data Privacy Act of 2012 (Republic Act No. 10173)**:

1. **Masked Public Presentation:**
   - Names are displayed as `J*** D**`.
   - Contact numbers appear as `+63 917 ••• ••34`.
   - Engine and Chassis numbers are masked on public surfaces (`G3M•••••56`).
2. **Customer Portal Authentication Gate:**
   - Tracking is not open to random guessing. Customers must provide their **Tracking Reference Number** (or scan their personalized QR code) and verify the **Last 4 Digits of their Registered Mobile Number**.
   - Failed verification attempts are rate-limited to prevent brute-force enumeration.
3. **Audit Trail Accountability:**
   - Every status transition, receipt update, verification gate approval, hold action, and document release is immutably logged in the `audit_logs` table with the user ID, timestamp, IP address, and payload diffs.
4. **No Raw Government ID Photocopy Storage:**
   - Dealership staff inspect physical primary IDs upon release and record only the ID type and masked ID number (e.g., `UMID ••••-1234`). No photocopies or unencrypted scans are saved on the server.

---

## 4. User Roles & Permissions Matrix

The system implements Role-Based Access Control (`CheckRole` middleware) with 4 distinct roles:

| Feature / Action | Admin | Dealership Manager | Dealership Staff | Customer |
| :--- | :---: | :---: | :---: | :---: |
| **System Dashboard & Live Analytics** | ✅ | ✅ | ✅ (Operational metrics) | ❌ |
| **Customer Registration & Editing** | ✅ | ✅ | ✅ | ❌ |
| **Motorcycle Registration & Print Slip** | ✅ | ✅ | ✅ | ❌ |
| **OR/CR & Plate Receipt Logging** | ✅ | ✅ | ✅ | ❌ |
| **Quality Gate Physical Verification** | ✅ | ✅ | ✅ | ❌ |
| **Document / Plate Releasing & E-Receipt** | ✅ | ✅ | ✅ | ❌ |
| **Operational Reports & CSV Export** | ✅ | ✅ | ❌ | ❌ |
| **Staff User Account Management** | ✅ | ❌ | ❌ | ❌ |
| **Branch & Notification Settings** | ✅ | ❌ | ❌ | ❌ |
| **Audit Logs Inspection** | ✅ | ❌ | ❌ | ❌ |
| **Private Online Status Portal** | ❌ | ❌ | ❌ | ✅ (PIN Verified) |

---

## 5. Physical Verification Quality Gates

To prevent premature notification or customer frustration, MotoRelease enforces **Physical Verification Quality Gates**:

### A. Independent OR/CR Tracking
- An OR/CR record tracks the **Official Receipt (OR)** and **Certificate of Registration (CR)** separately.
- Statuses: `awaiting_documents` ➡️ `partially_received` ➡️ `ready_for_pickup` ➡️ `claimed` (or `on_hold`).
- **Strict Quality Rule:** Staff cannot mark an OR/CR package as `ready_for_pickup` unless **both** `is_or_received = true` AND `is_cr_received = true`. Any attempt to force readiness without both documents is rejected with a validation exception.

### B. Independent Physical License Plate Tracking
- Metal/temporary license plates often arrive weeks or months after the OR/CR documents.
- Plates have their own lifecycle: `pending_arrival` ➡️ `ready_for_pickup` ➡️ `claimed` (or `on_hold`).
- Claiming an OR/CR document **does not** mark the license plate as claimed, and vice versa.

---

## 6. Philippine SMS Integration

MotoRelease includes a production-ready SMS notification architecture:

- **Supported Drivers:**
  - `semaphore`: Live SMS delivery via [Semaphore API](https://semaphore.co) (Philippines).
  - `mock`: Default local development driver that logs outbound SMS to `notifications` table and `storage/logs/laravel.log`.
- **Philippine Number Normalization:** Automatically standardizes local inputs (`09171234567`, `9171234567`, `+639171234567`) to E.164 format `+639171234567`.
- **Idempotency Protection:** Prevents duplicate SMS messages by hashing the recipient, motorcycle ID, and template code. Repeated status updates within a 24-hour window will not spam the customer.
- **Asynchronous Queue Workers:** SMS dispatch is dispatched to Laravel's queue (`SendSmsNotificationJob`) with exponential backoff and failed job logging.
- **Customizable SMS Templates:** System administrators can customize SMS templates with dynamic placeholders:
  - `{customer_name}`, `{model}`, `{tracking_reference}`, `{claim_type}`, `{branch_name}`, `{branch_address}`, `{branch_contact}`.

---

## 7. Customer Tracking Portal & QR Code Access

- **Public Route:** `/track`
- **Scan-to-Track:** Every registered motorcycle receives a printable tracking stub with an SVG QR code containing a direct access URL:
  `http://localhost:8000/track?ref=MR-SJA-2026-000001`
- **PIN Verification:** Customer enters the last 4 digits of their phone number to unlock:
  - Document availability status badges (`Ready for Pickup`, `Awaiting Documents`, `Claimed`, etc.).
  - Estimated turnaround and verified arrival dates.
  - Dealership pickup guidelines (Owner requirements vs. Authorized Representative requirements with SPA).
  - Operating hours and contact information for their specific branch.

---

## 8. Release Management & Double-Claim Prevention

1. **Releasing Workflow (`/releases/create`):**
   - Selects customer and motorcycle.
   - Selects eligible items (`OR/CR Document`, `License Plate`, or `Both`).
   - Validates claimant type:
     - **Registered Owner:** Requires verification of 2 valid Philippine government-issued IDs.
     - **Authorized Representative:** Requires verification of Signed Authorization Letter / Special Power of Attorney (SPA), Representative's Valid ID, and Photocopy of Owner's ID.
   - Records claimant full name, relationship, contact number, and ID details.
   - Captures **Electronic Canvas Signature** or physical release signature.
2. **Double-Claim Prevention:**
   - Database transactions lock records (`lockForUpdate`).
   - If an item is already marked `claimed`, the system terminates the transaction and aborts with a 422 error.
3. **Printable Release Acknowledgment Receipt (`/releases/{id}/print-receipt`):**
   - Generates a print-ready release receipt containing full transaction details, release code (`REL-2026-000001`), item serials, releasing staff signature line, and claimant acknowledgment text.

---

## 9. Operational Reports & Audit Trails

- **Dynamic Metrics (`/reports`):**
  - Average cycle time from vehicle registration to physical document arrival.
  - Average cycle time from document verification to customer pickup.
  - **Unclaimed Items Aging Report:** Flags items ready for pickup over 15, 30, and 60 days.
  - SMS notification delivery breakdown (Delivered, Pending, Failed).
  - Branch-by-branch comparative volume.
- **CSV Data Export (`/reports/export-csv`):**
  - Streamed CSV export for `releases` (complete transaction history) and `inventory` (current document and plate availability).
- **System Audit Logs (`/settings/audit-logs`):**
  - Searchable log of administrative actions, role updates, settings changes, and manual SMS resends.

---

## 10. Pre-Seeded Demonstration Accounts & Scenarios

The database seeder (`php artisan db:seed`) provides realistic pre-configured accounts and lifecycle test scenarios:

### Staff & Administrative User Accounts
| Role | Email | Password | Branch Assigned |
| :--- | :--- | :--- | :--- |
| **System Administrator** | `admin@motorelease.ph` | `Password123!` | All Branches |
| **Dealership Manager** | `manager@motorelease.ph` | `Password123!` | San Jose Main Dealership |
| **Dealership Staff** | `staff@motorelease.ph` | `Password123!` | San Jose Main Dealership |
| **Deactivated Staff** | `inactive@motorelease.ph` | `Password123!` | San Jose Main Dealership |

### Demonstration Tracking Scenarios (Customer Portal Testing)
| Customer Name | Motorcycle Model | Tracking Ref | Phone PIN (Last 4) | Lifecycle Status |
| :--- | :--- | :--- | :---: | :--- |
| **Juan Dela Cruz** | Yamaha NMAX 155 | `MR-SJA-2026-000001` | `1234` | OR/CR & Plate **Both Ready for Pickup** |
| **Maria Santos** | Yamaha Aerox 155 | `MR-SJA-2026-000002` | `2345` | OR/CR **Partially Received** (CR missing) |
| **Roberto Reyes** | Yamaha Sniper 155 | `MR-SJA-2026-000003` | `3456` | OR/CR **Claimed**, Plate **Pending Arrival** |
| **Elena Garcia** | Yamaha Mio Gravis | `MR-SJA-2026-000004` | `4567` | Both OR/CR and Plate **Claimed** |
| **Arnel Dalisay** | Yamaha XSR155 | `MR-SJA-2026-000005` | `5678` | OR/CR **On Hold** (LTO Name Discrepancy) |
| **Rhea Villamor** | Yamaha Fazzio 125 | `MR-SJA-2026-000006` | `6789` | Brand New Registration (Awaiting Arrival) |

---

## 11. Installation & Setup Guide

### System Requirements
- PHP `>= 8.2` (with `pdo_mysql`, `mbstring`, `openssl`, `curl`, `gd` extensions enabled)
- Composer `>= 2.0`
- MySQL `>= 8.0` or MariaDB `>= 10.4` (e.g. XAMPP)
- Node.js `>= 18.0` and NPM

### Step-by-Step Local Setup

1. **Clone the Repository & Enter Directory:**
   ```bash
   cd "C:\Users\jhonk\OneDrive\Documents\YAMAHA ORCR REALEASING SYSTEM"
   ```

2. **Install PHP Dependencies:**
   ```bash
   composer install
   ```

3. **Install NPM Dependencies & Build Assets:**
   ```bash
   npm.cmd install
   npm.cmd run build
   ```

4. **Configure Environment File:**
   ```bash
   copy .env.example .env
   php artisan key:generate
   ```

   Verify your database settings in `.env`:
   ```env
   APP_NAME="MotoRelease"
   APP_ENV=local
   APP_DEBUG=true
   APP_TIMEZONE="Asia/Manila"
   APP_URL=http://localhost:8000

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=motorelease
   DB_USERNAME=root
   DB_PASSWORD=

   # SMS Notification Driver ('mock' for testing, 'semaphore' for production)
   SMS_DRIVER=mock
   SEMAPHORE_API_KEY=
   SEMAPHORE_SENDER_NAME=MotoRelease
   ```

5. **Run Migrations & Seed Sample Data:**
   ```bash
   php artisan migrate:fresh --seed
   ```

6. **Start Local Development Server:**
   ```bash
   php artisan serve
   ```
   Open your browser to:
   - **Customer Tracking Portal:** `http://localhost:8000/track`
   - **Dealership Staff Login:** `http://localhost:8000/login`

7. **(Optional) Run Background Queue Worker:**
   ```bash
   php artisan queue:work --tries=3
   ```

---

## 12. Automated Test Suite & Verification

MotoRelease includes a 29-test automated test suite covering all security, verification quality gates, customer portal access, releasing logic, and reporting features.

To execute the test suite:
```bash
php artisan test
```

### Test Coverage Highlights:
- **`AuthenticationTest`**: Login, lockout, logout, active vs. deactivated staff checks.
- **`AuthorizationTest`**: RBAC permissions for Admin, Manager, and Staff roles.
- **`CustomerAndMotorcycleTest`**: Registration, validation, duplicate prevention, and multi-vehicle ownership.
- **`CustomerPortalTest`**: Reference search, phone PIN verification, privacy masking, unauthorized access rejection.
- **`OrcrVerificationTest`**: Physical verification quality gate (rejection when only one document is present, approval when both are present, SMS idempotency).
- **`LicensePlateVerificationTest`**: Independent plate workflow, SMS dispatch, failed notification retry.
- **`ReleaseManagementTest`**: Release transactions, representative SPA validation, double-claim prevention lock.
- **`ReportingAndAuditTest`**: Dynamic KPIs, unclaimed aging calculations, streaming CSV export, immutable audit logging.

```
   PASS  Tests\Unit\ExampleTest
  ✓ that true is true

   PASS  Tests\Feature\AuthenticationTest
  ✓ login screen can be rendered
  ✓ admin successfully logs in
  ✓ deactivated staff cannot log in
  ✓ authenticated user can log out

   PASS  Tests\Feature\AuthorizationTest
  ✓ unauthorized staff cannot access admin settings
  ✓ staff cannot access reports
  ✓ manager can access reports but not user settings
  ✓ admin has full access

   PASS  Tests\Feature\CustomerAndMotorcycleTest
  ✓ staff registers a customer and motorcycle
  ✓ customer can own more than one motorcycle

   PASS  Tests\Feature\CustomerPortalTest
  ✓ customer authenticates with valid tracking ref and pin
  ✓ invalid pin is rejected
  ✓ invalid tracking reference is rejected
  ✓ unauthenticated user cannot access status directly

   PASS  Tests\Feature\ExampleTest
  ✓ the application redirects to customer tracking

   PASS  Tests\Feature\LicensePlateVerificationTest
  ✓ plate availability remains independent from orcr
  ✓ plate is received and verified ready with sms notification
  ✓ failed sms notification can be retried safely

   PASS  Tests\Feature\OrcrVerificationTest
  ✓ orcr remains pending when only one document is available
  ✓ physical verification gate rejects readiness if cr is missing
  ✓ staff confirms readiness when both documents are physically verified
  ✓ repeated saves do not create duplicate sms notifications

   PASS  Tests\Feature\ReleaseManagementTest
  ✓ staff releases orcr independently
  ✓ duplicate claim attempts are strictly rejected
  ✓ representative release requires authorization letter verification

   PASS  Tests\Feature\ReportingAndAuditTest
  ✓ reports page calculates and displays dynamic metrics
  ✓ csv export streams proper csv file
  ✓ audit logs record events accurately

  Tests:    29 passed (92 assertions)
```

---

## 📄 License
This project is developed as an educational and operational tracking system for motorcycle dealerships. All rights reserved.