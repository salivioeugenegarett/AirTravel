<?php
// booking.php: This file will redirect to index.html if accessed directly.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // If the form is not submitted, redirect the user back to index.html
    header("Location: index.html");
    exit();
}

// If form is submitted, process booking...
$servername = "127.0.0.1";
$username = "root"; // Your MySQL username
$password = ""; // Your MySQL password
$dbname = "airtravel";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
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
$stmt = $conn->prepare("SELECT COUNT(*) AS passenger_count FROM bookings WHERE destination=? AND flight_time=? AND flight_date=?");
$stmt->bind_param("sss", $destination, $flightTime, $flightDate);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row['passenger_count'] >= 5) {
    echo "Sorry, this flight is already full. Please choose another time.";
} else {
    // Prepare SQL to insert the new booking
    $stmt = $conn->prepare("INSERT INTO bookings (name, phone, email, destination, flight_time, address, flight_date) 
                            VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $name, $phone, $email, $destination, $flightTime, $address, $flightDate);

    if ($stmt->execute()) {
        echo "Booking successfully submitted!";
        // Optional: Redirect after successful booking
        // header("Location: confirmation_page.php?message=Booking Success");
        // exit;
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Close connection
$stmt->close();
$conn->close();
?>