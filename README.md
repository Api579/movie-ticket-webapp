# Movie Ticket Booking platform

Full-stack web application using classic stack (HTML/CSS/Javascript/PHP/MySQL) for:
- Browsing movies
- Selecting showtimes and seats
- Booking tickets

## Stack Overview

- **Front-end:** HTML, CSS, JavaScript
- **Back-end:** PHP
- **Database:** MySQL

## Features

- Users:
    - register account: signup, login, logout
    - browse movies and showtimes
    - select seats
    - buying tickets
    - booking receipt, and email confirmation
- Admin panel:
  - manages movies
  - manages showtimes
  - manages user accounts


## Project structure

```
movie-ticket-webapp/
├── admin.php               # Admin panel
├── booking_confirmation.php
├── cart.php
├── dbconnect.php           # Database connection config
├── formvalidation.js
├── header.php
├── home.php
├── login.php
├── logout.php
├── movies.php
├── seats.js / seats.php
├── show_email_post.php
├── signup.php
├── style.css
├── tickets.js / tickets.php
├── database.sql            # Database schema
├── insert.sql              # Sample data
└── media/                  # Movie posters and banners
```

## Setup

1. Install PHP & MySQL local environment (can use: [XAMPP](https://www.apachefriends.org/) or [MAMP](https://www.mamp.info/)).
2. Create database by running `database.sql`, and populate with sample data using `insert.sql`.
3. Update credentials with `dbconnect.php` to match local MySQL setup (see notes).
4. Place project folder within the server's document root (`htdocs` for XAMPP) then start Apache and MySQL.
5. Finally visit `http://localhost/movie-ticket-webapp/home.php` in browser.

> **Note:** `dbconnect.php` contains default local development credentials: root user, empty password. Do not use these values or commit real credentials, in production deployment — use environment variables, or a config file excluded via `.gitignore` instead.
