# I-Tracking System

<p align="center">
<img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
</p>

## Prerequisites

Before you begin, ensure you have the following installed:

-   PHP >= 8.1
-   Composer
-   Node.js & NPM
-   MySQL or any compatible database
-   Git

## Installation Guide

### 1. Clone the Repository

```bash
git clone https://github.com/leynnnnnn0/itracking-filament.git
cd itracking-filament
```

### 2. Install Dependencies

```bash
composer install
npm install
```

### 3. Environment Setup

```bash
# Create a copy of the .env file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Configure Database

1. Open `.env` file and update the database configuration:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=itracking_filament
DB_USERNAME=your_username or root
DB_PASSWORD=your_password or leave as empty
```

2. Create a new database in MySQL with the name you specified in DB_DATABASE

### 5. Run Migrations and Seeders

```bash
# Run migrations
php artisan migrate

# Run seeders
php artisan db:seed

# Download Dependencies
php artisan composer update
npm install
```

### 6. Start the Development Server

```bash
# Compile assets
npm run dev

# Start Laravel server
php artisan serve
```

The application will be available at `http://localhost:8000`

## Additional Information

-   Make sure your database server is running before running migrations and seeders
-   If you encounter any issues with the seeder, you can refresh the database and run all migrations and seeders again using:

```bash
php artisan migrate:fresh --seed
```

## Troubleshooting

If you encounter any issues:

1. Make sure all dependencies are installed correctly
2. Verify database credentials in `.env` file
3. Clear application cache:

```bash
php artisan config:clear
php artisan cache:clear
```

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
