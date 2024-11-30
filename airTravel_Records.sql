CREATE DATABASE IF NOT EXISTS airtravel;

USE airtravel;

CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    phone VARCHAR(20),
    email VARCHAR(100),
    destination VARCHAR(50),
    flight_time VARCHAR(20),
    address VARCHAR(255),
    flight_date DATE,
    UNIQUE(destination, flight_time, flight_date)
);
