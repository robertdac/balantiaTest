# Balantia Test Project

## Requirements

- **Node.js** v18.20.8 (with npm v10.8.2)  
- **PHP** ^7.3 or ^8.0  
- **MySQL** 8.0.42  
- **Composer v2**

## Setup Instructions

### 1. Clone the Repository

```bash
git clone git@github.com:robertdac/balantiaTest.git -b develop
cd balantiaTest
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Set Up Environment

- Copy the `.env.example` file to `.env` and configure as needed.
- Make sure to change the queue connection to use the database:

```env
QUEUE_CONNECTION=database
```

- Configure Redis or database connection settings accordingly.

### 4. Enable LOCAL INFILE in MySQL

Make sure your MySQL server has `LOCAL INFILE` enabled. You can verify this by running:

```sql
SHOW VARIABLES LIKE 'local_infile';
```

It should return:

```
 local_infile  ON 

```

If it's off, you will need to enable it in your MySQL configuration.

### 5. Run Migrations

```bash
php artisan migrate
```

### 6. Install Node Modules

```bash
npm install
```

### 7. Build Frontend Assets

```bash
npm run dev
```

### 8. Start the Laravel Development Server

```bash
php artisan serve
```

### 9. Start the Queue Worker

```bash
php artisan queue:work --timeout=600
```

## Test Route

To test the CSV upload functionality, visit:

```
http://127.0.0.1:8000/upload/csv/create
```

## Generate Sample CSV

To generate a test CSV file, run the following command:

```bash
php artisan generate:csv
```
