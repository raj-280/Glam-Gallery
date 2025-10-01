<?php
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header('Location: ../login.php');
    exit();
}
include 'db_connection.php';

// Fetch all contact submissions
$sql = "SELECT * FROM contact_submissions ORDER BY submission_time DESC";
$stmt = $conn->query($sql);
$submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Contact Submissions</title>
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
        <h1>Contact Submissions</h1>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Message</th>
                    <th>Submission Time</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (
                    $submissions as $submission): ?>
                <tr>
                    <td><?php echo $submission['id']; ?></td>
                    <td><?php echo $submission['name']; ?></td>
                    <td><?php echo $submission['email']; ?></td>
                    <td><?php echo $submission['message']; ?></td>
                    <td><?php echo $submission['submission_time']; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>