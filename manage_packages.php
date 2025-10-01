<?php
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header('Location: ../login.php');
    exit();
}
include 'db_connection.php';

$sql = "SELECT * FROM packages";
$stmt = $conn->prepare($sql);
$stmt->execute();

$packages = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $packages[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Packages</title>
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
        <h1>Manage Packages</h1>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Description</th>
                    <th>Created At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (
                    $packages as $row): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['price']; ?></td>
                    <td><?php echo $row['description']; ?></td>
                    <td><?php echo $row['created_at']; ?></td>
                    <td>
                        <a href="delete_package.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this package?');">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>