# 📦 Delivery Management System (Backend)

## 📝 Project Overview
The **Delivery Management System** is a backend web application built using **Laravel**.  
It provides full **CRUD (Create, Read, Update, Delete)** functionality to manage delivery operations efficiently.

This project focuses on backend development best practices, RESTful API design, and database management using Laravel and Eloquent ORM.

---

## 🚀 Features
- Full CRUD operations for deliveries
- Manage delivery status (e.g. pending, in-progress, delivered)
- RESTful API endpoints
- Request validation and error handling
- Database relationships using Eloquent ORM
- Clean MVC architecture

---

## 🛠️ Tech Stack
- **Backend:** Laravel
- **Language:** PHP
- **Database:** MySQL
- **ORM:** Eloquent
- **API Style:** RESTful APIs
- **Tools:** Composer, Artisan

## ⚙️ Installation & Setup

### Clone the repository
in Bash
git clone https://github.com/daniele-zain/Delivery-Management-System.git

### Install dependencies
composer install

### Configure database

Update your .env file with database credentials:
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

### Run migrations
php artisan migrate

### Start the server
php artisan serve
