# Mamikos Backend - Laravel

Production-ready Laravel backend for the Mamikos Technical Test.

---

## 🛠️ Tech Stack & Versions

| Technology | Version | Description |
| :--- | :--- | :--- |
| **PHP** | `^8.2` | Programming Language |
| **Laravel** | `^12.0` | Backend PHP Framework |
| **Laravel Sanctum** | `^4.0` | API Token Authentication |
| **L5-Swagger / swagger-php** | `^11.1` | OpenAPI / Swagger API Documentation |
| **Database** | MySQL / SQLite | Relational Database |
| **Composer** | Latest | Dependency Management |

---

## 📋 Features & Requirements Implemented
1. **User Roles & Credits**:
   - `REGULAR_USER`: Gets 20 initial credits.
   - `PREMIUM_USER`: Gets 40 initial credits.
   - `OWNER`: Gets 0 credits. Can add, update, delete, and view their own kosts.
2. **Kost Management (Owner)**:
   - Create, update, delete kosts.
   - View owner kost list.
3. **Kost Search & Filter (Public/User)**:
   - Search by name, location, price range (`minPrice`, `maxPrice`).
   - Sort results by price (`sort=asc` or `sort=desc`).
   - View kost detail.
4. **Room Availability Inquiry**:
   - Users can ask about room availability (`-5 credits` per inquiry).
   - Validates sufficient credits.
5. **Scheduled Task**:
   - Monthly credit recharge on the 1st of every month (Laravel scheduler / command).
6. **Architecture & Clean Code**:
   - Standard Laravel MVC / Service Layer pattern (`Controllers`, `FormRequests`, `Resources`, `Services`, `Models`).
   - Global exception handling with standard `BaseResponse`.
   - Strict Git commit convention (`feat:`, `fix:`, `refactor:`, `test:`, `chore:`).

---

## ⚙️ Prerequisites
- **PHP >= 8.2** with extensions (`OpenSSL`, `PDO`, `Mbstring`, `Tokenizer`, `XML`, `Ctype`, `JSON`)
- **Composer** installed globally
- **MySQL** or **SQLite**

---

## 🚀 Step-by-Step Installation & Running Guide

### 1. Clone the Repository
```bash
git clone https://github.com/your-username/mamikos-be-laravel.git
cd mamikos-be-laravel
```

### 2. Install Dependencies
```bash
composer install
```

### 3. Environment Configuration
Copy `.env.example` to `.env` and generate the application key:
```bash
cp .env.example .env
php artisan key:generate
```

Configure your database connection in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mamikos_laravel_db
DB_USERNAME=root
DB_PASSWORD=your_password
```
*(Alternatively, use SQLite: set `DB_CONNECTION=sqlite` and create `database/database.sqlite`).*

### 4. Run Database Migrations
```bash
php artisan migrate
```

### 5. Run the Application
Start the local development server:
```bash
php artisan serve
```

The application will start at `http://127.0.0.1:8000`.

---

## 📖 API Documentation & Swagger UI
Interactive API documentation is available via Swagger UI once the application is running:
- **Swagger UI**: [http://127.0.0.1:8000/api/documentation](http://127.0.0.1:8000/api/documentation)
- **OpenAPI JSON**: `http://127.0.0.1:8000/storage/api-docs/api-docs.json`

*(To regenerate Swagger docs after annotation changes: `php artisan l5-swagger:generate`)*

---

## 🧪 API Endpoints Reference

### 1. Auth API (`/api/auth`)
- **Register**: `POST /api/auth/register`
  ```json
  {
    "username": "budi_owner",
    "email": "budi@owner.com",
    "password": "password123",
    "role": "OWNER"
  }
  ```
- **Login**: `POST /api/auth/login`
  ```json
  {
    "usernameOrEmail": "budi_owner",
    "password": "password123"
  }
  ```
- **Get Profile**: `GET /api/auth/me` (Requires Bearer Token)
- **Update Profile**: `PUT /api/auth/me` (Requires Bearer Token)
- **Change Password**: `PUT /api/auth/password` (Requires Bearer Token)

### 2. Kost API (`/api/kosts`)
- **Create Kost (Owner)**: `POST /api/kosts` (Requires Bearer Token)
  ```json
  {
    "name": "Kost Melati Indah",
    "location": "Jakarta Selatan",
    "price": 1500000.0,
    "description": "Kost nyaman dekat stasiun",
    "roomCount": 10
  }
  ```
- **Search Kost (Public)**: `GET /api/kosts/search?location=Jakarta&sort=asc`
- **Kost Detail (Public)**: `GET /api/kosts/{id}`
- **Owner Kosts**: `GET /api/kosts/owner/my-kosts` (Requires Owner Token)
- **Update Kost**: `PUT /api/kosts/{id}` (Owner)
- **Delete Kost**: `DELETE /api/kosts/{id}` (Owner)

### 3. Inquiry API (`/api/inquiries`)
- **Ask Room Availability (-5 credits)**: `POST /api/inquiries` (Requires Regular/Premium User Token)
  ```json
  {
    "kostId": 1,
    "message": "Apakah kamar masih tersedia untuk bulan depan?"
  }
  ```
- **User Inquiries**: `GET /api/inquiries/my-inquiries` (Requires Bearer Token)

