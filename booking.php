<?php
session_start();
require 'db.php';

// Redirect to login if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Handle booking submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $table_number = $_POST['table_number']; // Selected table number
    $name = $_POST['name'];
    $email = $_POST['email'];
    $booking_time = $_POST['booking_time'];
    $guests = $_POST['guests'];
    $special_requests = $_POST['special_requests'];
    $user_id = $_SESSION['user_id'];

    // Insert booking into the database
    $stmt = $pdo->prepare("INSERT INTO bookings (user_id, table_number, name, email, booking_time, guests, special_requests) 
                           VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $table_number, $name, $email, $booking_time, $guests, $special_requests]);

    // Mark the table as unavailable after booking
    $update_stmt = $pdo->prepare("UPDATE tables SET available = 0 WHERE table_number = ?");
    $update_stmt->execute([$table_number]);

    echo "<script>alert('Table booked successfully!');</script>";
}

// Fetch only available tables from the database
$tables_stmt = $pdo->query("SELECT * FROM tables WHERE available = 1");
$tables = $tables_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Table</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <!-- Include Navbar -->
    <?php include 'navbar.php'; ?>

    <!-- Main Content -->
    <div class="container mt-5">
        <h2 class="text-center">Book a Table</h2>

        <!-- Display All Tables in Card Format -->
        <h3 class="mt-5 text-center">Available Tables</h3>
        <div class="row row-cols-1 row-cols-md-3 g-4 mt-3">
            <?php if (!empty($tables)): ?>
                <?php foreach ($tables as $table): ?>
                    <div class="col">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">Table <?= htmlspecialchars($table['table_number']) ?></h5>
                                <p class="card-text"><strong>Capacity:</strong> <?= htmlspecialchars($table['capacity']) ?></p>
                                <!-- Booking Form for Each Table -->
                                <form method="POST" class="mt-3">
                                    <input type="hidden" name="table_number" value="<?= htmlspecialchars($table['table_number']) ?>">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Name</label>
                                        <input type="text" class="form-control" id="name" name="name" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="booking_time" class="form-label">Booking Time</label>
                                        <input type="time" class="form-control" id="booking_time" name="booking_time" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="guests" class="form-label">Number of Guests</label>
                                        <input type="number" class="form-control" id="guests" name="guests" min="1" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="special_requests" class="form-label">Special Requests</label>
                                        <textarea class="form-control" id="special_requests" name="special_requests"></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100">Book This Table</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info text-center" role="alert">
                        No tables available.
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Include Footer -->
    <?php include 'footer.php'; ?>
</body>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</html>