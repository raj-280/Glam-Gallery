<?php
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header('Location: ../login.php');
    exit();
}
include 'db_connection.php';

$sql = "SELECT * FROM appointments";
$stmt = $conn->prepare($sql);
$stmt->execute();

$appointments = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $appointments[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Appointments</title>
    <link rel="stylesheet" href="admin_style.css">
</head>
<body>
    
    <nav>
    <ul>
        <li><a href="dashboard.php">Home</a></li>
        <li><a href="manage_appointments.php">User Appointments</a></li>
        <li><a href="manage_packages.php">Packages</a></li>
        <li><a href="manage_users.php">Manage Users</a></li>
        <li><a href="admin_contact.php">Users Reviews</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</nav>

    <div class="container">
        <h1>Manage Appointments</h1>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Service/Package</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($appointments as $row): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['customer_name']; ?></td>
                    <td><?php echo $row['customer_email']; ?></td>
                    <td><?php echo $row['customer_phone']; ?></td>
                    <td><?php echo $row['date']; ?></td>
                    <td><?php echo $row['time']; ?></td>
                    <td><?php echo $row['service'] ?? 'N/A'; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>