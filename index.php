<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.html");
    exit();
}

$dbname = 'airtravel.db';
$conn = new SQLite3($dbname);

if (!$conn) {
    die("Connection failed: " . $conn->lastErrorMsg());
}

$query = "
CREATE TABLE IF NOT EXISTS bookings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT,
    phone TEXT,
    email TEXT,
    destination TEXT,
    flight_time TEXT,
    address TEXT,
    flight_date TEXT
);
";

if (!$conn->exec($query)) {
    echo "Error: " . $conn->lastErrorMsg();
    exit;
}

$name = $_POST['name'];
$phone = $_POST['phone'];
$email = $_POST['email'];
$destination = $_POST['destination'];
$flightTime = $_POST['flightTime'];
$address = $_POST['address'];
$flightDate = $_POST['flightDate'];

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Invalid email format");
}

if (!preg_match("/^[0-9]{11}$/", $phone)) {
    die("Invalid phone number format. Please enter a 11-digit phone number.");
}

$stmt = $conn->prepare("
    SELECT COUNT(*) AS passenger_count 
    FROM bookings 
    WHERE destination = :destination AND flight_time = :flightTime AND flight_date = :flightDate
");
$stmt->bindValue(':destination', $destination, SQLITE3_TEXT);
$stmt->bindValue(':flightTime', $flightTime, SQLITE3_TEXT);
$stmt->bindValue(':flightDate', $flightDate, SQLITE3_TEXT);
$result = $stmt->execute();
$row = $result->fetchArray(SQLITE3_ASSOC);

if ($row['passenger_count'] >= 5) {
    echo "Sorry, this flight is already full. Please choose another time.";
} else {
    $stmt = $conn->prepare("
        INSERT INTO bookings (name, phone, email, destination, flight_time, address, flight_date) 
        VALUES (:name, :phone, :email, :destination, :flightTime, :address, :flightDate)
    ");
    $stmt->bindValue(':name', $name, SQLITE3_TEXT);
    $stmt->bindValue(':phone', $phone, SQLITE3_TEXT);
    $stmt->bindValue(':email', $email, SQLITE3_TEXT);
    $stmt->bindValue(':destination', $destination, SQLITE3_TEXT);
    $stmt->bindValue(':flightTime', $flightTime, SQLITE3_TEXT);
    $stmt->bindValue(':address', $address, SQLITE3_TEXT);
    $stmt->bindValue(':flightDate', $flightDate, SQLITE3_TEXT);

    if ($stmt->execute()) {
        echo "Booking successfully submitted!";
    } else {
        echo "Error: " . $conn->lastErrorMsg();
    }
}

$conn->close();
?>
