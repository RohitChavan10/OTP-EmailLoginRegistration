# OTP & Email Login/Registration (Laravel 10)

This is a Laravel application that supports **user registration and login with OTP (via phone number)** and **email verification**.

## 🚀 Features
- Register with **Phone Number + OTP**
- Login with **Phone Number + OTP**
- Optional **Email Verification**
- Auto-generated **username**
- Secure password hashing
- Dashboard with authenticated user details

---

## 🛠️ Requirements
- PHP >= 8.1
- Composer
- MySQL
- Node.js & NPM (for frontend assets, optional)

---

## 📥 Installation

### 1. Clone Repository
```bash
git clone https://github.com/RohitChavan10/OTP-EmailLoginRegistration.git
cd OTP-EmailLoginRegistration

2. Install Dependencies
composer install
npm install && npm run dev

3. Setup Environment

Copy .env.example to .env:

4. Generate Key
php artisan key:generate

5. Run Migrations
php artisan migrate

6. Serve App
php artisan serve


Now visit:
👉 http://127.0.0.1:8000
