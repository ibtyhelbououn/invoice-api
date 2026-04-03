# Invoice API

A RESTful API built with Laravel and MySQL for managing invoices with PDF export and dashboard analytics.

## Tech Stack

- PHP 8.2
- Laravel 12
- MySQL
- DomPDF (barryvdh/laravel-dompdf)

## Features

- Create invoices with multiple line items
- Automatic total calculation with tax and discount
- Mark invoices as paid or unpaid
- Download invoices as PDF
- Dashboard with revenue stats and monthly breakdown

## Installation

1. Clone the repository
   git clone https://github.com/ibtyhelbououn/invoice-api.git

2. Install dependencies
   composer install

3. Copy the environment file
   cp .env.example .env

4. Generate app key
   php artisan key:generate

5. Configure your database in .env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=invoice_api
   DB_USERNAME=root
   DB_PASSWORD=

6. Run migrations
   php artisan migrate

7. Start the server
   php artisan serve

## API Endpoints

### Create Invoice
POST /api/invoices

Request body:
{
    "client_name": "John Doe",
    "client_email": "john@example.com",
    "client_phone": "+1234567890",
    "due_date": "2026-05-01",
    "tax": 19,
    "discount": 10,
    "items": [
        {
            "description": "Web Development",
            "quantity": 10,
            "unit_price": 50
        }
    ]
}

### List All Invoices
GET /api/invoices

### Get Single Invoice
GET /api/invoices/{id}

### Update Invoice Status
PUT /api/invoices/{id}/status

Request body:
{
    "status": "paid"
}

### Download Invoice PDF
GET /api/invoices/{id}/pdf

Returns a downloadable PDF file with full invoice details.

### Delete Invoice
DELETE /api/invoices/{id}

### Dashboard Stats
GET /api/dashboard

Response:
{
    "total_invoices": 10,
    "paid_count": 7,
    "unpaid_count": 3,
    "total_revenue": 15420.50,
    "recent_invoices": [...],
    "revenue_by_month": {
        "2026-03": 8200.00,
        "2026-04": 7220.50
    }
}

## Total Calculation

Total is calculated dynamically from line items:
1. Subtotal = sum of (quantity x unit_price) for all items
2. After discount = subtotal - (subtotal x discount%)
3. Total = after discount + (after discount x tax%)

## Error Handling

Invalid or missing invoices return a 404 response:
{
    "error": "Invoice not found"
}
