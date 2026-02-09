# ClinicFlow - SaaS Clinic Management System

ClinicFlow is a multi-tenant SaaS application built with **Laravel 10**, tailored for clinics and doctors in Pakistan. It simplifies appointment booking, patient management, and automated reminders (WhatsApp/SMS).

## 🚀 Features

- **Multi-Tenancy**: Single database, clinic-scoped data security.
- **Roles**: Clinic Admin & Receptionist.
- **Appointment Booking**: Smart scheduling with double-booking prevention.
- **Automated Reminders**: Queue-based WhatsApp (24h) and SMS (2h) reminders.
- **Reporting**: Financial estimates and appointment stats.
- **Patient History**: Track patient visits and details.

## 🛠 Tech Stack

- **Backend**: Laravel 10+ (PHP 8.2+)
- **Database**: MySQL
- **Frontend**: Blade Templates + Tailwind CSS (Alpine.js for interactivity)
- **Auth**: Laravel Breeze (Custom Implementation)
- **Queue**: Database Driver

## 📦 Installation Requirement

Ensure you have PHP, Composer, and Node.js installed.

1. **Clone the repository**
   ```bash
   git clone https://github.com/yourusername/clinic-flow.git
   cd ClinicFlow
   ```

2. **Install Dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment Setup**
   Copy `.env.example` to `.env` and configure your database:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   
   Update `.env` with your DB credentials:
   ```
   DB_DATABASE=clinicflow
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. **Database & Seeding**
   Run migrations and seed the demo clinic:
   ```bash
   php artisan migrate:fresh --seed
   ```

5. **Run the Application**
   Start the development server:
   ```bash
   npm run build
   php artisan serve
   ```

6. **Run Queue Worker (For Reminders)**
   Open a new terminal tab to process scheduled SMS/WhatsApp jobs:
   ```bash
   php artisan queue:work
   ```
   *Note: To simulate the scheduler locally, run `php artisan schedule:work`.*

## 🔑 Demo Login

**Clinic Admin:**
- Email: `admin@shifa.com`
- Password: `password`

**Receptionist:**
- Email: `reception@shifa.com`
- Password: `password`

## 🧩 Project Structure

- `app/Models/Clinic.php`: The central tenant model.
- `app/Http/Middleware/EnsureUserBelongsToClinic.php`: Enforces tenant security.
- `app/Services/WhatsAppService.php`: Abstracted messaging logic.
- `app/Console/Commands`: Custom Artisan commands for reminders.

## ⚠️ Notes for Production

- Configure a real SMS/WhatsApp API in `app/Services`.
- Set up a Cron job for the scheduler: `* * * * * php /path-to-project/artisan schedule:run >> /dev/null 2>&1`.
- Update `QUEUE_CONNECTION` to `redis` for better performance.

---
**Built for Pakistan's Healthcare Sector 🇵🇰**
