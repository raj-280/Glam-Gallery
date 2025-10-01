<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Debugging: Print POST data
echo "<pre>";
print_r($_POST);
echo "</pre>";

// 1. Database connection details
$host = "localhost";
$username = "root";
$password = "";
$database = "tgg";

// 2. Establish database connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 3. Validate and sanitize input data
$required_fields = ["name", "email", "number", "service", "date", "time"];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        die("Error: Missing required field '$field'.");
    }
}

// Get user_id from session if logged in
session_start();
$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : null;

$customer_name = htmlspecialchars($_POST["name"]);
$customer_email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
$customer_phone = htmlspecialchars($_POST["number"]);
$service = htmlspecialchars($_POST["service"]);
$date = htmlspecialchars($_POST["date"]);
$time = htmlspecialchars($_POST["time"]);

// Validate email
if (!filter_var($customer_email, FILTER_VALIDATE_EMAIL)) {
    die("Error: Invalid email address.");
}

// Validate date format (YYYY-MM-DD)
if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $date)) {
    die("Error: Invalid date format. Expected YYYY-MM-DD.");
}

// Validate time format (HH:MM)
if (!preg_match("/^\d{2}:\d{2}$/", $time)) {
    die("Error: Invalid time format. Expected HH:MM.");
}

// 4. Prepare and execute the SQL query
try {
    // Use the correct table name and columns
    if ($user_id !== null) {
        $sql = "INSERT INTO appointments (user_id, customer_name, customer_phone, customer_email, date, service, time)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("issssss", $user_id, $customer_name, $customer_phone, $customer_email, $date, $service, $time);
    } else {
        $sql = "INSERT INTO appointments (customer_name, customer_phone, customer_email, date, service, time)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("ssssss", $customer_name, $customer_phone, $customer_email, $date, $service, $time);
    }

    if ($stmt->execute()) {
        $appointment_id = $stmt->insert_id;
        // Insert booked package services into appointment_services
        if (isset($_POST['package_id'])) {
            $package_id = intval($_POST['package_id']);
            // Define package services (should match your pack.php)
            $package_services = [
                1 => [
                    ['name' => 'Simple Haircut', 'price' => 500, 'image' => 'service-icon-1.png'],
                    ['name' => 'Gel polish', 'price' => 499, 'image' => 'service-icon-2.png'],
                ],
                2 => [
                    ['name' => 'Facial', 'price' => 699, 'image' => 'service-icon-1.png'],
                    ['name' => 'Body Massage', 'price' => 500, 'image' => 'service-icon-2.png'],
                ],
                3 => [
                    ['name' => 'Facial', 'price' => 699, 'image' => 'service-icon-1.png'],
                    ['name' => 'Manicure & Pedicure', 'price' => 500, 'image' => 'service-icon-2.png'],
                    ['name' => 'Hot Stone Massage', 'price' => 300, 'image' => 'service-icon-3.png'],
                ],
                4 => [
                    ['name' => 'Haircut + Hair Style', 'price' => 999, 'image' => 'service-icon-1.png'],
                    ['name' => 'Acrylic Nail Extension', 'price' => 900, 'image' => 'service-icon-2.png'],
                ],
                5 => [
                    ['name' => 'Airbrush makeup', 'price' => 1199, 'image' => 'service-icon-1.png'],
                    ['name' => 'Temperory Nail Extension', 'price' => 1000, 'image' => 'service-icon-2.png'],
                ],
                6 => [
                    ['name' => 'Fancy Hair Styling', 'price' => 1499, 'image' => 'service-icon-1.png'],
                    ['name' => 'HD Makeup', 'price' => 1000, 'image' => 'service-icon-2.png'],
                ],
                7 => [
                    ['name' => 'Simple Haircut', 'price' => 999, 'image' => 'service-icon-1.png'],
                    ['name' => 'Fancy Hair Styling', 'price' => 2000, 'image' => 'service-icon-2.png'],
                ],
                8 => [
                    ['name' => 'Bridal Look', 'price' => 9999, 'image' => 'service-icon-1.png'],
                    ['name' => 'Bridal Mehndi', 'price' => 5000, 'image' => 'service-icon-2.png'],
                ],
                9 => [
                    ['name' => 'Facial', 'price' => 2999, 'image' => 'service-icon-1.png'],
                    ['name' => 'Bridal Look', 'price' => 9999, 'image' => 'service-icon-2.png'],
                    ['name' => 'Bridal Mehndi', 'price' => 3000, 'image' => 'service-icon-3.png'],
                    ['name' => 'Temperory Nail Extension', 'price' => 1000, 'image' => 'service-icon-1.png'],
                ],
            ];
            if (isset($package_services[$package_id])) {
                foreach ($package_services[$package_id] as $service) {
                    $sql_service = "INSERT INTO appointment_services (appointment_id, service_name, service_price, image) VALUES (?, ?, ?, ?)";
                    $stmt_service = $conn->prepare($sql_service);
                    $stmt_service->bind_param("isds", $appointment_id, $service['name'], $service['price'], $service['image']);
                    $stmt_service->execute();
                    $stmt_service->close();
                }
            }
        }
        // Redirect to thank you page
        header("Location: thank_you.php");
        exit();
    } else {
        throw new Exception("Error booking appointment: " . $stmt->error);
    }
} catch (Exception $e) {
    // Log the error and display a user-friendly message
    error_log("Appointment booking error: " . $e->getMessage());
    echo "<p style='color: red;'>An error occurred while booking your appointment. Please try again later.</p>";
    echo "<p>Error details: " . $e->getMessage() . "</p>"; // Display detailed error for debugging
} finally {
    // Close the statement and connection
    if (isset($stmt)) {
        $stmt->close();
    }
    $conn->close();
}
?>