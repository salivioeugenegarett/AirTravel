<?php
// booking.php: This file will redirect to index.html if accessed directly.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // If the form is not submitted, redirect the user back to index.html
    header("Location: index.html");
    exit();
}

// Create SQLite database and table using PHP
$dbname = 'airtravel.db'; // SQLite database file
$conn = new SQLite3($dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . $conn->lastErrorMsg());
}

// Create the 'bookings' table if it doesn't exist
$query = "
CREATE TABLE IF NOT EXISTS bookings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT,
    phone TEXT,
    email TEXT,
    destination TEXT,
    flight_time TEXT,
    address TEXT,
    flight_date TEXT,
    UNIQUE(destination, flight_time, flight_date)
);
";

if (!$conn->exec($query)) {
    echo "Error: " . $conn->lastErrorMsg();
    exit;
}

// Retrieve form data
$name = $_POST['name'];
$phone = $_POST['phone'];
$email = $_POST['email'];
$destination = $_POST['destination'];
$flightTime = $_POST['flightTime'];
$address = $_POST['address'];
$flightDate = $_POST['flightDate'];

// Validate form data
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Invalid email format");
}

if (!preg_match("/^[0-9]{10}$/", $phone)) {
    die("Invalid phone number format. Please enter a 10-digit phone number.");
}

// Prepare SQL to check for existing bookings for the selected flight
$stmt = $conn->prepare("SELECT COUNT(*) AS passenger_count FROM bookings WHERE destination=:destination AND flight_time=:flightTime AND flight_date=:flightDate");
$stmt->bindValue(':destination', $destination, SQLITE3_TEXT);
$stmt->bindValue(':flightTime', $flightTime, SQLITE3_TEXT);
$stmt->bindValue(':flightDate', $flightDate, SQLITE3_TEXT);
$result = $stmt->execute();
$row = $result->fetchArray(SQLITE3_ASSOC);

if ($row['passenger_count'] >= 5) {
    echo "Sorry, this flight is already full. Please choose another time.";
} else {
    // Prepare SQL to insert the new booking
    $stmt = $conn->prepare("INSERT INTO bookings (name, phone, email, destination, flight_time, address, flight_date) 
                            VALUES (:name, :phone, :email, :destination, :flightTime, :address, :flightDate)");
    $stmt->bindValue(':name', $name, SQLITE3_TEXT);
    $stmt->bindValue(':phone', $phone, SQLITE3_TEXT);
    $stmt->bindValue(':email', $email, SQLITE3_TEXT);
    $stmt->bindValue(':destination', $destination, SQLITE3_TEXT);
    $stmt->bindValue(':flightTime', $flightTime, SQLITE3_TEXT);
    $stmt->bindValue(':address', $address, SQLITE3_TEXT);
    $stmt->bindValue(':flightDate', $flightDate, SQLITE3_TEXT);

    if ($stmt->execute()) {
        echo "Booking successfully submitted!";
        // Optional: Redirect after successful booking
        // header("Location: confirmation_page.php?message=Booking Success");
        // exit;
    } else {
        echo "Error: " . $conn->lastErrorMsg();
    }
}

// Close connection
$conn->close();
?>
