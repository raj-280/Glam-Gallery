<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services Booked by User</title>
    <link rel="stylesheet" href="admin_style.css">
</head>
<body>
<?php
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header('Location: ../login.php');
    exit();
}
?>
<nav>
    <ul>
        <li><a href="dashboard.php">Home</a></li>
        <li><a href="manage_appointments.php">User Appointments</a></li>
        <li><a href="manage_packages.php">Packages</a></li>
        <li><a href="manage_services.php">User services</a></li>
        <li><a href="manage_users.php">Manage Users</a></li>
        <li><a href="admin_contact.php">Users Reviews</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</nav>
<div class="container">
    <h1> Services Booked by User</h1>
    <table>
        <!-- Table content goes here -->
    </table>
</div>
</body>
</html>
<?php
// Database connection
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "tgg";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch data from the database
$sql = "SELECT 
            aps.id AS service_id,
            aps.service_name,
            aps.service_price,
            aps.image,
            apt.id AS appointment_id,
            apt.customer_name,
            apt.customer_email,
            apt.customer_phone,
            apt.date,
            apt.time,
            apt.user_id AS appointment_user_id,
            COALESCE(u.name, apt.customer_name) AS user_name,
            COALESCE(u.Address, NULL) AS user_address
        FROM 
            appointment_services aps
        JOIN 
            appointments apt ON aps.appointment_id = apt.id
        LEFT JOIN 
            users u ON apt.user_id = u.id OR apt.customer_email = u.email";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Output data in a table
    echo "<table border='1'>
            <tr>
                <th>Service ID</th>
                <th>Service Name</th>
                <th>Service Price</th>
                <th>Appointment ID</th>
                <th>Customer Name</th>
                <th>Customer Email</th>
                <th>Customer Phone</th>
                <th>Date</th>
                <th>Time</th>
                <th>User ID</th>
                <th>User Name</th>
                <th>User Address</th>
            </tr>";
    // Output data of each row
    while($row = $result->fetch_assoc()) {
        echo "<tr>\n";
        echo "<td>" . $row["service_id"] . "</td>\n";
        echo "<td>" . $row["service_name"] . "</td>\n";
        echo "<td>" . $row["service_price"] . "</td>\n";
        echo "<td>" . $row["appointment_id"] . "</td>\n";
        echo "<td>" . $row["customer_name"] . "</td>\n";
        echo "<td>" . $row["customer_email"] . "</td>\n";
        echo "<td>" . $row["customer_phone"] . "</td>\n";
        echo "<td>" . $row["date"] . "</td>\n";
        echo "<td>" . $row["time"] . "</td>\n";
        // Always show appointment_user_id for User ID
        echo "<td>" . ($row["appointment_user_id"] ?? 'N/A') . "</td>\n";
        echo "<td>" . ($row["user_name"] ?? $row["customer_name"]) . "</td>\n";
        echo "<td>" . ($row["user_address"] ?? 'N/A') . "</td>\n";
        echo "</tr>\n";
    }
    echo "</table>";
} else {
    echo "0 results";
}

// Close connection
$conn->close();
?>