# Healthcare Management System

A role-based clinic management application built with **Laravel 13** and **PHP 8.3**. It covers the core workflow of a small clinic — from booking an appointment, through the consultation and dispensing medication, to producing a paid invoice — with authorization, stock integrity, and scheduling rules enforced at the server.

Four user roles (patient, doctor, pharmacist, admin) each see a different slice of the system, backed by Laravel policies rather than UI-only checks.

---

## Modules

| Module | What it does |
| --- | --- |
| **Patients & Doctors** | Directory records, each optionally linked to a login account. |
| **Appointments** | Book, view, and update visits. Scheduling is server-validated with double-booking prevention. |
| **Medical Records** | Consultation notes tied to a patient, with file attachments. |
| **Pharmacy** | Medication catalog with stock levels, owned by the pharmacist role. |
| **Dispensing** | Hand medication to a patient against an appointment — stock decremented atomically. |
| **Invoicing** | Generate an invoice from a consultation fee plus any dispenses, download it as a PDF, and mark it paid. |
| **Reports** | Admin dashboard: headline stats, low-stock alerts, and unpaid invoices. |

---

## Roles & Access

Roles are a many-to-many relationship (`role_user`), so a user can hold more than one. Access is enforced by policies (`AppointmentPolicy`, `InvoicePolicy`, `MedicalRecordPolicy`, `MedicationPolicy`) and `role:` middleware, not by hiding buttons.

- **Patient** — sees only their own appointments and records.
- **Doctor** — sees their own patients and appointments; sets consultation fees.
- **Pharmacist** — owns the medication catalog and dispensing.
- **Admin / Reception** — sees everything, plus the reports dashboard.

---

## Tech Stack

- **Backend:** Laravel 13, PHP 8.3
- **Auth:** Laravel Breeze
- **Frontend:** Blade, Tailwind CSS, Alpine.js, Vite
- **PDF:** `barryvdh/laravel-dompdf`
- **Testing:** PHPUnit

---

## Getting Started

**Requirements:** PHP 8.3+, Composer, Node.js, and a database (MySQL/PostgreSQL, or SQLite for a quick start).

```bash
# 1. Install dependencies
composer install
npm install

# 2. Environment
cp .env.example .env
php artisan key:generate
# Configure your DB in .env (or point DB_CONNECTION=sqlite at database/database.sqlite)

# 3. Create the schema and seed demo data (patients, doctors, appointments, meds, test logins)
php artisan migrate --seed

# 4. Build assets
npm run build

# 5. Run
composer dev        # server + queue + logs + vite together
# or simply: php artisan serve
```

Then visit **http://localhost:8000**.

### Test Logins

The seeder creates one account per role. Password for all of them is `password`.

| Role | Email |
| --- | --- |
| Admin / Reception | `admin@example.com` |
| Doctor | `doctor@example.com` |
| Patient | `patient@example.com` |
| Pharmacist | `pharmacist@example.com` |

---

## Engineering Notes

A few decisions worth calling out:

- **Atomic dispensing.** Stock is never read-then-written. `Medication::dispense()` runs a conditional decrement inside a transaction — `WHERE stock_quantity >= :qty` — and treats an affected-row count of zero as "out of stock." Two pharmacists dispensing the last unit at the same time can't both succeed and drive stock negative.

- **Double-booking prevention.** `StoreAppointmentRequest` runs an `after` validation hook that rejects any slot within a 30-minute window of the doctor's existing appointments, so a clash surfaces as a normal validation error rather than a bad row.

- **Server-owned identity.** `prepareForValidation()` injects the locked `doctor_id` / `patient_id` from the authenticated user before rules run — the browser can't dictate who an appointment or dispense is for. Dispensing further verifies the chosen appointment actually belongs to the chosen patient.

- **Policy-based authorization.** Every sensitive action (viewing a record, creating an invoice, marking it paid) is gated by a policy, keeping "who can do this?" out of the controllers and views.

- **Money as integer cents.** Prices are stored as integer cents and converted from dollars at the edge, with rounding to avoid binary-float truncation.

---

## Testing

```bash
php artisan test
```
