# Wallet API

## 📌 Overview
The **Wallet API** is a RESTful service built with **Laravel 11** and **Docker**. It allows users to:
- Create an account
- Top-up an account
- Charge an account
- Retrieve accounts (paginated)
- Get account details by ID

## 🚀 Technologies Used
- **Laravel 11** (PHP Framework)
- **MySQL 8.4** (Database)
- **Docker & Docker Compose** (Containerization)
- **NGINX** (Web Server)
- **PHP 8.2** (FPM)
- **Postman Collection** (API Testing)

---

## 🚀 Trying the API

You can import the [Postman collection](postman_collection.json) into Postman and start testing the API.

1. **Open Postman**
2. **Click `Import`** (top left corner)
3. **Upload `postman_collection.json`**
4. **Set the `base_url` variable in Postman:**
   - If you want to try it quickly without setting it up locally, use the **deployed version**: `http://ec2-50-19-168-107.compute-1.amazonaws.com/`
   - Otherwise, use the **local URL** obtained after setup: `http://localhost/`
   - If you have **customized the port**, set it to: `http://localhost:{HOST_WEB_PORT}`

Once the base URL is set, you can start making API requests!

---

## 🔧 Setup Instructions

### ⚙️ Requirements
- **Docker** with **Docker Compose v2** (Recommended: [Docker Desktop](https://www.docker.com/products/docker-desktop/))

### 1️⃣ Clone the Repository

```sh
git clone https://github.com/error17191/wallet
cd wallet
```

### 2️⃣ Copy Environment File & Customize Ports

```sh
cp .env.example .env
```

Edit `.env` and update **database credentials** if needed (With docker you can just keep them as they are ):

```ini
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=wallet_db
DB_USERNAME=root
DB_PASSWORD=root
```

To avoid port conflicts, you can customize the web and database ports in `.env`:

```ini
HOST_WEB_PORT=8080  # Change if port 80 is in use
HOST_DB_PORT=3307    # Change if port 3306 is in use
```

### 3️⃣ Install Dependencies & Start Docker Services

```sh
docker compose up -d --build
docker compose exec app composer install
```

### 4️⃣ Generate Key

```sh
docker compose exec app php artisan key:generate
```

### 5️⃣ Run Migrations & Set Permissions

```sh
docker compose exec app php artisan migrate

docker compose exec app chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
docker compose exec app chmod -R 775 /var/www/storage /var/www/bootstrap/cache
```

---

## ⚠️ Preventing Duplicate Transactions

The application includes a **configuration value** to prevent accidental duplicate transactions. By default, transactions with the same type and amount within a **short time window** (configurable in `.env`) are rejected.

To adjust the allowed time window for duplicate transaction detection, update the following in `.env`:

```ini
DUPLICATE_TRANSACTION_TIME_WINDOW=30  # Time in seconds
```

Ensure this key is loaded in `config/wallet.php`:

```php
return [
    'duplicate_transaction_time_window' => env('DUPLICATE_TRANSACTION_TIME_WINDOW', 30),
];
```

---

## 🛠 Running Tests

```sh
docker compose exec app php artisan test
```

---

## 📩 Database Structure
If you would like to check the database structure in SQL format, you can see it [here](mysql-schema.sql)

---

## 🔜 Next Steps

### ✅ Authentication

### ✅ CI/CD

---

