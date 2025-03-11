<?php
session_start();
require 'db.php';

// Redirect to login if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle cancellation of a booking
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cancel_booking'])) {
    $booking_id = $_POST['booking_id'];

    // Start a transaction to ensure atomicity
    try {
        $pdo->beginTransaction();

        // Fetch the table_number associated with the booking
        $stmt = $pdo->prepare("SELECT table_number FROM bookings WHERE id = ? AND user_id = ?");
        $stmt->execute([$booking_id, $user_id]);
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($booking) {
            $table_number = $booking['table_number'];

            // Delete the booking from the database
            $delete_stmt = $pdo->prepare("DELETE FROM bookings WHERE id = ? AND user_id = ?");
            $delete_stmt->execute([$booking_id, $user_id]);

            // Increment the available column for the corresponding table
            $update_stmt = $pdo->prepare("UPDATE tables SET available = available + 1 WHERE table_number = ?");
            $update_stmt->execute([$table_number]);

            // Commit the transaction
            $pdo->commit();

            echo "<script>alert('Booking cancelled successfully!');</script>";
        } else {
            echo "<script>alert('Booking not found or already cancelled.');</script>";
        }
    } catch (Exception $e) {
        // Rollback the transaction in case of an error
        $pdo->rollBack();
        echo "<script>alert('An error occurred while cancelling the booking. Please try again.');</script>";
    }

    // Redirect to refresh the page and reflect the changes
    header('Location: bookings_page.php');
    exit;
}

// Fetch all bookings for the logged-in user
$stmt = $pdo->prepare("SELECT * FROM bookings WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <!-- Include Navbar -->
    <?php include 'navbar.php'; ?>

    <!-- Main Content -->
    <div class="container mt-5">
        <h2 class="text-center">My Bookings</h2>

        <?php if (!empty($bookings)): ?>
            <div class="row row-cols-1 row-cols-md-2 g-4 mt-3">
                <?php foreach ($bookings as $booking): ?>
                    <div class="col">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">Table <?= htmlspecialchars($booking['table_number']) ?></h5>
                                <p class="card-text"><strong>Name:</strong> <?= htmlspecialchars($booking['name']) ?></p>
                                <p class="card-text"><strong>Email:</strong> <?= htmlspecialchars($booking['email']) ?></p>
                                <p class="card-text"><strong>Booking Time:</strong> <?= htmlspecialchars($booking['booking_time']) ?></p>
                                <p class="card-text"><strong>Guests:</strong> <?= htmlspecialchars($booking['guests']) ?></p>
                                <p class="card-text"><strong>Special Requests:</strong> <?= htmlspecialchars($booking['special_requests'] ?? 'None') ?></p>
                                <p class="card-text"><small class="text-muted">Booked on: <?= htmlspecialchars($booking['created_at']) ?></small></p>
                                <!-- Cancel Booking Button -->
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="booking_id" value="<?= htmlspecialchars($booking['id']) ?>">
                                    <button type="submit" name="cancel_booking" class="btn btn-danger btn-sm">Cancel Booking</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center mt-5" role="alert">
                You have no bookings yet.
            </div>
        <?php endif; ?>
    </div>

    <!-- Include Footer -->
    <?php include 'footer.php'; ?>
</body>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</html>