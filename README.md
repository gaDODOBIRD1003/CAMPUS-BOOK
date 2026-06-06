# 📚 Campus Book

A web-based room reservation system for APC students and faculty.

## Features
- User registration and login
- Browse available rooms
- Book rooms with conflict detection
- Track reservation status (pending, approved, rejected)
- Admin panel for approving/rejecting reservations
- Room management (add, update, delete)
- Dark mode

## Tech Stack
- Frontend: HTML + Tailwind CSS (CDN)
- Backend: PHP 8.2 (vanilla)
- Database: MySQL
- Server: XAMPP

## Setup
1. Clone the repo
2. Place the `campus_book` folder inside `htdocs`
3. Import the database — create `campus_book_db` in phpMyAdmin and set up the tables
4. Start Apache and MySQL in XAMPP
5. Open `http://localhost/campus_book`

## Default Admin Account
- Email: `admin@apc.edu.ph`
- Password: `123user`

## Folder Structure
- `database/` — PHP database connection and query files
- `pages/` — all system pages
- `includes/` — shared header and footer
- `index.php` — entry point
- `logout.php` — session handler
