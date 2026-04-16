# Database Schema Design

The `edu_id_saas` database is highly normalized and implements cascading foreign keys to guarantee data integrity across boundaries.

## 1. Tables Overview

### `schools`
Handles the core multi-tenant layer.
- `id` (PK)
- `name` (VARCHAR 200)
- `code` (VARCHAR 20) Unique tenant code
- `email`, `phone`, `city`, `state`
- `subscription_status` (ENUM: active, expired, none)
- `late_threshold` (TIME)

### `users`
System and tenant operators (RBAC mapped).
- `id` (PK)
- `school_id` (FK -> schools.id) NULLABLE for Super Admins
- `name`, `email`, `password`
- `role` (ENUM: super_admin, school_admin, teacher, student, security_guard)
- `is_active` (BOOLEAN)

### `students`
Student core identities linked contextually to a school.
- `id` (PK)
- `school_id` (FK -> schools.id) CASCADE DELETE
- `user_id` (FK -> users.id) NULL ON DELETE
- `first_name`, `last_name`, `roll_number`, `class_name`, `section`
- `gender`, `date_of_birth`, `blood_group`
- `guardian_name`, `guardian_phone`
- `photo_original_url`, `photo_compressed_url`, `photo_thumbnail_url`
- `qr_token` (VARCHAR UNIQUE) Used to stop QR data duplication/spoofing

### `attendance`
Maintains immutable scanning transaction logs.
- `id` (PK)
- `school_id` (FK -> schools.id) CASCADE DELETE
- `student_id` (FK -> students.id) CASCADE DELETE
- `date` (DATE)
- `entry_time` (DATETIME)
- `exit_time` (DATETIME)
- `entry_scanned_by` (FK -> users.id) System tracking of Guard
- `is_late` (BOOLEAN)
- `status` (ENUM: entered, exited, absent)

### `plans`
SaaS plan pricing brackets.
- `id` (PK)
- `name`, `slug`
- `price_monthly`, `price_yearly`
- `features` (JSON)
- `max_students`

### `subscriptions`
M2M Link representing the active lifecycle of a School's billing.
- `id` (PK)
- `school_id` (FK -> schools.id)
- `plan_id` (FK -> plans.id)
- `status` (ENUM: active, expired, pending, cancelled)
- `start_date`, `end_date`

### `payments`
Ledger of successful and failed transaction flows.
- `id` (PK)
- `school_id` (FK -> schools.id)
- `subscription_id` (FK -> subscriptions.id)
- `razorpay_order_id`, `razorpay_payment_id`
- `amount` (DECIMAL 10,2)
- `status` (ENUM: captured, failed, pending)

---

## 2. Multi-Tenant Protection Measures

- **Unique Constraints:** The `students` table has a composite unique key: `UNIQUE(school_id, student_id)` to ensure ID numbers do not conflict across different tenant schools.
- **Cascading Logic:** All core tables (`students`, `users`, `attendance`, `subscriptions`, `payments`) are attached natively to a `school_id` foreign key. Using `ON DELETE CASCADE`, if a school terminates the service and its record disappears, ALL nested references are wiped off disk simultaneously.
- **Indexed Lookups**: Heavy tracking tables such as `attendance` utilized combined indexing `INDEX(school_id, date)` to aggressively speed up daily dashboard metric reads representing thousands of scanner interactions.
