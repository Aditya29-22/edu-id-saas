# EDU-ID SaaS Platform

An enterprise-grade, Multi-Tenant SaaS platform designed for Schools to manage Students, Staff, ID Cards, QR-Based Attendance, and Subscription billing processing.

## 🚀 Tech Stack
- **Backend:** Laravel 10 (PHP 8.1+)
- **Database:** MySQL 8.x
- **Frontend architecture:** Blade Templating, Vanilla CSS (White, Gold, and Black theme), JavaScript
- **Cloud/Assets:** Filesystems prepared for AWS S3 Storage and CDN delivery (represented dynamically via `AssetService.php`).
- **Payments:** Razorpay Gateway flow integration simulation
- **Image Optimization:** Intervention Image (Implementation structure built within `AssetService` for local vs CDN swaps).

---

## 🛠️ Setup Steps

1. **Clone & Environment Setup**
   Ensure PHP 8.1+, Composer, and MySQL are installed.
   ```bash
   git clone <repository_url>
   cd edu-id-saas
   copy .env.example .env
   ```

2. **Install Dependencies**
   ```bash
   composer install
   npm install
   npm run build
   ```

3. **Database Configuration**
   Open the `.env` file and set the MySQL credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=edu_id_saas
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. **Run Migrations & Seeders**
   ```bash
   php artisan key:generate
   php artisan storage:link
   php artisan migrate --seed
   ```

5. **Start the Application**
   ```bash
   php artisan serve
   ```
   **Access the dashboard here:** `http://localhost:8000`
   **Default Admin:** `admin@eduid.com` / `Admin@123`

---

## 🔒 Security & Middleware (RBAC)
- **Role-Based Access Control (`RBACMiddleware`)**: Segregates access across Super Admin, School Admin, Teachers, and Security Guards.
- **Subscription Lock (`SubscriptionCheckMiddleware`)**: Validates if the `school_id` has an `ACTIVE` subscription. Prevents database actions for expired schools.
- **Multi-Tenant Protection**: Uses `BelongsToTenant` scope to automatically restrict queries securely by `school_id` so tenants never leak data to each other.

---

## 📡 API Endpoints (Web Routes mapped to UI Flow)

Because this system embraces an integrated MVC Blade approach, the REST endpoints correspond to route behaviors secured by stateful sessions:

| Method | Endpoint | Description | Guard |
|--------|----------|-------------|-------|
| `POST` | `/login` | Authenticate users and establish session | `Guest` |
| `POST` | `/logout` | Invalidate active session | `Auth` |
| `POST` | `/schools` | Create a new tenant school | `RBAC: manage_schools` |
| `POST` | `/students` | Register a student, upload photo via S3/Local Service | `RBAC: manage_students` |
| `POST` | `/users` | Assign a user a systemic Role mapped to a School | `RBAC: manage_school_users` |
| `GET`  | `/qr-codes/generate/{id}` | Formats and outputs the encoded SVG string of a token | `RBAC: generate_id_cards` |
| `POST` | `/scanner/process` | Processes QR logic. Determines ENTRY or EXIT and lateness | `RBAC: scan_qr` |
| `POST` | `/subscriptions` | Activates SaaS subscription, creating payment logs | `RBAC: manage_subscription` |

---

## ⚖️ Core Assumptions
1. **Attendance QR Flow:** If a student has no record for the current date, scanning acts as **Entry**. A secondary scan acts as **Exit**. Third scan rejects.
2. **CDN Simulation:** Because AWS credentials aren't enforced upon reviewer's machine in local setup, the `AssetService` operates using the disk `public`. It behaves structurally identical to S3 configurations natively integrated within Laravel (`AWS_URL` mappings).
3. **Payments Handling:** Real credit-card processing requires PCI-DSS scope. A simulated payment flow replicates the logic (Razorpay simulated keys).
4. **Roles:** Security guards interact strictly with the scanner layer to prevent privacy access overrides.

---

### Package Contents
- Source Code
- `database_schema.md` (Detailed structural breakdown of database maps)
- This README document
