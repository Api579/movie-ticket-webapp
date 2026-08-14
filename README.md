# Movie Ticket Booking Web App

A full-stack web application for browsing movies, selecting showtimes and seats, and booking tickets. Built for NTU Module IE4727 (Web Application Design).

## Overview

- **Front-end:** HTML, CSS, JavaScript
- **Back-end:** PHP
- **Database:** MySQL

## Features

- User signup / login / logout
- Browse movies and showtimes
- Seat selection
- Shopping cart and booking confirmation
- Admin panel for managing movies and showtimes
- Email confirmation on booking

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

1. Install a local PHP + MySQL environment (e.g. [XAMPP](https://www.apachefriends.org/) or [MAMP](https://www.mamp.info/)).
2. Create the database by running `database.sql`, then populate sample data with `insert.sql`.
3. Update the credentials in `dbconnect.php` to match your local MySQL setup (see note below).
4. Place the project folder in your server's document root (e.g. `htdocs` for XAMPP) and start Apache + MySQL.
5. Visit `http://localhost/movie-ticket-webapp/home.php` in your browser.

> **Note:** `dbconnect.php` currently contains default local development credentials (root user, empty password). Do not use these values, or commit real credentials, in a production deployment — use environment variables or a config file excluded via `.gitignore` instead.

## Team

Built as a team project for IE4727 — Web Application Design, NTU (Aug–Dec 2025).
