<?php
session_start();
require 'db.php';

// Redirect to login if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle cancel order request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cancel_order_id'])) {
    $order_id = $_POST['cancel_order_id'];

    // Update the order status to "Cancelled"
    $update_stmt = $pdo->prepare("UPDATE orders SET status = 'Cancelled' WHERE id = ? AND user_id = ?");
    $update_stmt->execute([$order_id, $user_id]);

    if ($update_stmt->rowCount() > 0) {
        echo "<script>alert('Order canceled successfully!');</script>";
    } else {
        echo "<script>alert('Failed to cancel the order. Please try again.');</script>";
    }
}

// Fetch all orders for the logged-in user
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate the total price of all orders for the user
$total_price_stmt = $pdo->prepare("SELECT SUM(price) AS total_price FROM orders WHERE user_id = ? AND status != 'Cancelled'");
$total_price_stmt->execute([$user_id]);
$total_price_result = $total_price_stmt->fetch(PDO::FETCH_ASSOC);
$total_price = $total_price_result['total_price'] ?? 0; // Default to 0 if no orders exist
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <!-- Include Navbar -->
    <?php include 'navbar.php'; ?>

    <!-- Total Price Section -->
    <div class="container mt-3">
        <div class="row">
            <div class="col-md-4">
                <div class="card bg-light">
                    <div class="card-body">
                        <h5 class="card-title">Total Spent</h5>
                        <p class="card-text display-6 text-primary">₹<?= htmlspecialchars(number_format($total_price, 2)) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mt-5">
        <h2 class="text-center">My Order History</h2>

        <?php if (!empty($orders)): ?>
            <table class="table table-bordered table-hover mt-3">
                <thead class="table-dark">
                    <tr>
                        <th>Item Name</th>
                        <th>Category</th>
                        <th>Price (₹)</th>
                        <th>Order Type</th>
                        <th>Status</th>
                        <th>Ordered On</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?= htmlspecialchars($order['item_name']) ?></td>
                            <td><?= htmlspecialchars($order['category']) ?></td>
                            <td><?= htmlspecialchars($order['price']) ?></td>
                            <td><?= htmlspecialchars($order['order_type']) ?></td>
                            <td>
                                <?php
                                // Display status with appropriate styling
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
                            <td><?= htmlspecialchars($order['created_at']) ?></td>
                            <td>
                                <!-- Cancel Order Button -->
                                <?php if ($order['status'] === 'Pending'): ?>
                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to cancel this order?');">
                                        <input type="hidden" name="cancel_order_id" value="<?= $order['id'] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">Cancel</button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-muted">No action</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-info text-center mt-5" role="alert">
                You have no orders yet.
            </div>
        <?php endif; ?>
    </div>

    <!-- Include Footer -->
    <?php include 'footer.php'; ?>
</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</html>