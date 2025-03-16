# Conference Room Booking System

## Overview
The **Conference Room Booking System** is a database-driven solution designed to streamline the process of reserving conference rooms within an organization. It provides a structured and secure platform for administrators and clients to manage room bookings efficiently, minimizing scheduling conflicts and optimizing resource utilization.

## Features
- **User Authentication**: Secure login credentials for both admin and clients.
- **Real-time Room Availability**: Clients can check and book available rooms in real time.
- **Admin Controls**: Admins manage room availability, booking policies, and scheduling.
- **Booking Confirmation & Notifications**: Automated booking confirmations and reminders.
- **Payment Management**: Payment tracking and management system.
- **Reports & Analytics**: Generate reports on room utilization and booking trends.
- **User-Friendly Interface**: Intuitive design for both admins and clients.

## Technologies Used
- **Frontend**: HTML, CSS
- **Backend**: PHP
- **Database**: MySQL
- **Server**: XAMPP (Apache, MySQL, PHP)

## Installation Guide
### Prerequisites
- XAMPP or any PHP and MySQL-supported environment
- Visual Studio Code or any text editor
- Web browser (Chrome, Firefox, etc.)

### Steps
1. **Clone the repository**
   ```bash
   git clone https://github.com/your-username/conference-room-booking.git
   cd conference-room-booking
   ```
2. **Move the project to XAMPP htdocs directory**
   ```bash
   mv conference-room-booking /xampp/htdocs/
   ```
3. **Start Apache and MySQL in XAMPP**
4. **Import the database**
   - Open `phpMyAdmin` (http://localhost/phpmyadmin)
   - Create a new database `conference_booking`
   - Import the provided SQL file (`database/conference_booking.sql`)
5. **Configure database connection**
   - Navigate to `config/db.php`
   - Update database credentials:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_USER', 'root');
     define('DB_PASS', '');
     define('DB_NAME', 'conference_booking');
     ```
6. **Run the application**
   - Open a browser and go to `http://localhost/conference-room-booking`

## Usage
### Admin Features
- Login to dashboard
- Manage conference halls and rooms
- Approve or reject room booking requests
- Generate reports on room usage and payment status

### Client Features
- Register and log in to the system
- Browse and book available conference rooms
- Add services to bookings (e.g., catering, A/V support)
- Make payments for bookings
- View and manage booking history

## Database Schema
The system follows a **relational database model** with tables such as:
- `admin`
- `client`
- `conference_hall`
- `conference_room`
- `booking`
- `payment`
- `service`
