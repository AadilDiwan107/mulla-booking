<?php
session_start();
require 'db.php';

// Redirect to login if admin is not logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit;
}

// Fetch all orders grouped by user ID
$stmt = $pdo->query("
    SELECT user_id, GROUP_CONCAT(id) AS order_ids, 
           GROUP_CONCAT(item_name SEPARATOR ', ') AS items, 
           GROUP_CONCAT(price SEPARATOR ', ') AS prices, 
           GROUP_CONCAT(order_type SEPARATOR ', ') AS order_types, 
           GROUP_CONCAT(status SEPARATOR ', ') AS statuses, 
           MAX(created_at) AS last_order_time
    FROM orders
    GROUP BY user_id
    ORDER BY last_order_time DESC
");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Orders by User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <!-- Responsive Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <!-- Brand Name -->
            <a class="navbar-brand" href="admin_dashboard.php">Hotel Table Booking</a>

            <!-- Toggle Button for Mobile -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navbar Links -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if (isset($_SESSION['admin_id'])): ?>
                        <li class="nav-item"><a class="nav-link" href="admin_bookings.php">See Bookings</a></li>
                        <li class="nav-item"><a class="nav-link" href="admin_dashboard.php">Add Table</a></li>
                        <li class="nav-item"><a class="nav-link" href="admin_orders.php">See all orders</a></li>
                        <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                        <li class="nav-item"><a class="nav-link" href="signup.php">Sign Up</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>


    <!-- Main Content -->
    <div class="container mt-5">
        <h2 class="text-center">Orders by User</h2>

        <?php if (!empty($users)): ?>
            <div class="row row-cols-1 row-cols-md-3 g-4 mt-3">
                <?php foreach ($users as $user): ?>
                    <div class="col">
                        <div class="card h-100">
                            <div class="card-header bg-primary text-white">
                                <h5 class="card-title">User ID: <?= htmlspecialchars($user['user_id']) ?></h5>
                            </div>
                            <div class="card-body">
                                <p class="card-text"><strong>Last Order Time:</strong> <?= htmlspecialchars($user['last_order_time']) ?></p>
                                <p class="card-text"><strong>Items:</strong> <?= htmlspecialchars($user['items']) ?></p>
                                <p class="card-text"><strong>Prices:</strong> ₹<?= htmlspecialchars($user['prices']) ?></p>
                                <p class="card-text"><strong>Order Types:</strong> <?= htmlspecialchars($user['order_types']) ?></p>
                                <p class="card-text"><strong>Statuses:</strong> <?= htmlspecialchars($user['statuses']) ?></p>
                            </div>
                            <div class="card-footer text-end">
                                <a href="admin_user_details.php?user_id=<?= $user['user_id'] ?>" class="btn btn-sm btn-secondary">View Details</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center mt-5" role="alert">
                No orders found.
            </div>
        <?php endif; ?>
    </div>

    <!-- Include Footer -->
    <?php include 'footer.php'; ?>
</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</html>