<?php
session_start();
require 'db.php';

// Redirect to login if admin is not logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit;
}

// Handle form submission to add a new table
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['table_number'], $_POST['capacity'], $_POST['available'], $_POST['description'])) {
    $table_number = intval($_POST['table_number']);
    $capacity = intval($_POST['capacity']);
    $available = isset($_POST['available']) ? 1 : 0; // Convert checkbox value to boolean
    $description = $_POST['description'];

    // Check if the table number already exists
    $check_stmt = $pdo->prepare("SELECT * FROM tables WHERE table_number = ?");
    $check_stmt->execute([$table_number]);
    $existing_table = $check_stmt->fetch();

    if ($existing_table) {
        echo "<script>alert('Table number $table_number already exists. Please choose a different number.');</script>";
    } else {
        // Insert the new table into the database
        $insert_stmt = $pdo->prepare("INSERT INTO tables (table_number, capacity, available, description) VALUES (?, ?, ?, ?)");
        $insert_stmt->execute([$table_number, $capacity, $available, $description]);

        if ($insert_stmt->rowCount() > 0) {
            echo "<script>alert('Table number $table_number added successfully!');</script>";
        } else {
            echo "<script>alert('Failed to add table. Please try again.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Add New Table</title>
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
                        <li class="nav-item"><a class="nav-link" href="admin_dashboard.php">Dashboard</a></li>
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
        <h2 class="text-center">Add New Table</h2>

        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title">Enter Table Details</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label for="table_number" class="form-label">Table Number</label>
                                <input type="number" class="form-control" id="table_number" name="table_number" min="1" required>
                            </div>
                            <div class="mb-3">
                                <label for="capacity" class="form-label">Capacity</label>
                                <input type="number" class="form-control" id="capacity" name="capacity" min="1" required>
                            </div>
                            <div class="mb-3">
                                <label for="available" class="form-label">Available</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="available" name="available" checked>
                                    <label class="form-check-label" for="available">
                                        Mark as Available
                                    </label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Add Table</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Footer -->
    <?php include 'footer.php'; ?>
</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</html>