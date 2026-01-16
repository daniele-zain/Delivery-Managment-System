📦 Delivery Management System (Backend)
📝 Project Overview

The Delivery Management System is a backend application developed using Laravel, designed to manage and organize delivery operations in a structured, scalable, and maintainable manner.
The system implements full CRUD (Create, Read, Update, Delete) functionality, enabling efficient handling of delivery data through well-structured RESTful APIs.

This project emphasizes clean backend architecture, proper separation of concerns, and robust database interaction using Laravel’s MVC pattern and Eloquent ORM.

🚀 Features

Complete CRUD operations for delivery management

Delivery status handling (e.g. pending, in progress, delivered)

RESTful API design following industry best practices

Server-side request validation and centralized error handling

Relational database modeling using Eloquent ORM

Clean and maintainable MVC architecture

🛠️ Tech Stack

Backend Framework: Laravel

Programming Language: PHP

Database: MySQL

ORM: Eloquent

API Architecture: RESTful APIs

Development Tools: Composer, Artisan

⚙️ Installation & Setup
Clone the repository
git clone https://github.com/daniele-zain/Delivery-Management-System.git

Install dependencies
composer install

Environment configuration

Create and configure the environment file:

cp .env.example .env


Update the database credentials in the .env file:

DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

Generate application key
php artisan key:generate

Run database migrations
php artisan migrate

Start the development server
php artisan serve
