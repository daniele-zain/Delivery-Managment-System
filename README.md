# Delivery Management System (Backend)

## Project Overview
The Delivery Management System is a backend application developed using Laravel, designed to manage and organize delivery operations in a structured, scalable, and maintainable manner.  
The system implements full CRUD (Create, Read, Update, Delete) functionality, enabling efficient handling of delivery data through well-structured RESTful APIs.

This project emphasizes clean backend architecture, proper separation of concerns, and robust database interaction using Laravel’s MVC pattern and Eloquent ORM.

---

## Features
- Complete CRUD operations for delivery management
- Delivery status handling (pending, in progress, delivered)
- RESTful API design following industry best practices
- Server-side request validation and centralized error handling
- Relational database modeling using Eloquent ORM
- Clean and maintainable MVC architecture

---

## Tech Stack
- Backend Framework: Laravel
- Programming Language: PHP
- Database: MySQL
- ORM: Eloquent
- API Architecture: RESTful APIs
- Development Tools: Composer, Artisan

---

## Installation & Setup

### Clone the repository
```bash
git clone https://github.com/daniele-zain/Delivery-Management-System.git
```
### Install dependencies
```bash
composer install
```
### Environment configuration
```bash
cp .env.example .env
```
Update the database credentials in the .env file:

DB_DATABASE=""

DB_USERNAME=""

DB_PASSWORD="

### Run database migrations
```bash
php artisan migrate
```

### Start the development server
```bash
php artisan serve
```

