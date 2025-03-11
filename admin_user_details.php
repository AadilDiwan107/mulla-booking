<?php
session_start();
require 'db.php';

// Redirect to login if admin is not logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit;
}

// Get the user ID from the query string
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

if ($user_id <= 0) {
    header('Location: admin_orders.php');
    exit;
}

// Handle status update submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_id'], $_POST['new_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['new_status'];

    // Update the status of the order in the database
    $update_stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $update_stmt->execute([$new_status, $order_id]);

    if ($update_stmt->rowCount() > 0) {
        echo "<script>alert('Order status updated successfully!');</script>";
    } else {
        echo "<script>alert('Failed to update order status. Please try again.');</script>";
    }
}

// Fetch all orders for the selected user
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate the total bill for the user
$total_bill = 0;
foreach ($orders as $order) {
    $total_bill += $order['price'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - User <?= $user_id ?> Details</title>
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
        <h2 class="text-center">User <?= htmlspecialchars($user_id) ?> Details</h2>

        <?php if (!empty($orders)): ?>
            <table class="table table-bordered table-hover mt-3">
                <thead class="table-dark">
                    <tr>
                        <th>Item Name</th>
                        <th>Price (₹)</th>
                        <th>Order Type</th>
                        <th>Status</th>
                        <th>Update Status</th>
                        <th>Ordered On</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?= htmlspecialchars($order['item_name']) ?></td>
                            <td><?= htmlspecialchars($order['price']) ?></td>
                            <td><?= htmlspecialchars($order['order_type']) ?></td>
                            <td>
                                <?php
                                // Display current status with appropriate styling
                                $status = $order['status'];
                                switch ($status) {
                                    case 'Pending':
                                        echo '<span class="badge bg-warning text-dark">Pending</span>';
                                        break;
                                    case 'Completed':
                                        echo '<span class="badge bg-success">Completed</span>';
                                        break;
                                    case 'Cancelled':
                                        echo '<span class="badge bg-danger">Cancelled</span>';
                                        break;
                                    default:
                                        echo '<span class="badge bg-secondary">Unknown</span>';
                                }
                                ?>
                            </td>
                            <td>
                                <!-- Form to update the status -->
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                    <select name="new_status" class="form-select form-select-sm d-inline" style="width: auto;">
                                        <option value="Pending" <?= $order['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                        <option value="Completed" <?= $order['status'] === 'Completed' ? 'selected' : '' ?>>Completed</option>
                                        <option value="Cancelled" <?= $order['status'] === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-primary ms-2">Update</button>
                                </form>
                            </td>
                            <td><?= htmlspecialchars($order['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Generate Bill Section -->
            <div class="card mt-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title">Generate Bill</h5>
                </div>
                <div class="card-body">
                    <p class="card-text"><strong>Total Items:</strong> <?= count($orders) ?></p>
                    <p class="card-text"><strong>Items Ordered:</strong></p>
                    <ul>
                        <?php foreach ($orders as $order): ?>
                            <li><?= htmlspecialchars($order['item_name']) ?> - ₹<?= htmlspecialchars($order['price']) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <p class="card-text"><strong>Total Amount:</strong> ₹<?= number_format($total_bill, 2) ?></p>
                    <button class="btn btn-success" onclick="printBill()">Generate Bill</button>
                </div>
            </div>

        <?php else: ?>
            <div class="alert alert-info text-center mt-5" role="alert">
                No orders found for this user.
            </div>
        <?php endif; ?>
    </div>

    <!-- Include Footer -->
    <?php include 'footer.php'; ?>
</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<!-- JavaScript to Print the Bill -->
<script>
    function printBill() {
        const billContent = document.querySelector('.card-body').innerHTML;
        const originalContent = document.body.innerHTML;

        document.body.innerHTML = `
        <div class="container mt-5">
            <h2 class="text-center">Bill</h2>
            <div class="card mt-4">
                <div class="card-body">
                    ${billContent}
                </div>
            </div>
        </div>
    `;

        window.print();
        document.body.innerHTML = originalContent;
        location.reload(); // Reload the page to restore the original state
    }
</script>

</html>