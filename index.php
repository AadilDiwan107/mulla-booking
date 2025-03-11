<?php
session_start();
require 'db.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Table Booking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <!-- Include Navbar -->
    <?php include 'navbar.php'; ?>

    <!-- Hero Section -->
    <div class="container mt-5">
        <h1 class="text-center display-4">Welcome to Our Hotel</h1>
        <p class="text-center lead">Book a table and enjoy a delightful dining experience.</p>
    </div>

    <!-- Features Section -->
    <div class="container mt-5">
        <h2 class="text-center mb-4">Why Choose Us?</h2>
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <div class="col">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <h5 class="card-title">Easy Booking</h5>
                        <p class="card-text">Book your table online in just a few clicks. No hassle, no waiting!</p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <h5 class="card-title">Wide Range of Tables</h5>
                        <p class="card-text">Choose from a variety of tables to suit your group size and preferences.</p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <h5 class="card-title">Delicious Food</h5>
                        <p class="card-text">Savor our chef's special dishes crafted with love and fresh ingredients.</p>
                    </div>
                </div>
            </div>
            <!-- New Card: Table Booking -->
            <div class="col">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <h5 class="card-title">Table Booking</h5>
                        <p class="card-text">Reserve your table in advance and avoid the wait. Perfect for special occasions!</p>
                        <a href="booking.php" class="btn btn-primary">Book a Table</a>
                    </div>
                </div>
            </div>
            <!-- New Card: Order for Dine-In -->
            <div class="col">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <h5 class="card-title">Order for Dine-In</h5>
                        <p class="card-text">Place your order online and enjoy a seamless dine-in experience at our hotel.</p>
                        <a href="menu.php" class="btn btn-success">View Menu</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- How It Works Section -->
    <div class="container mt-5">
        <h2 class="text-center mb-4">How It Works</h2>
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <div class="col">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <h5 class="card-title">Step 1: Select a Table</h5>
                        <p class="card-text">Browse through our available tables and choose one that suits your needs.</p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <h5 class="card-title">Step 2: Provide Details</h5>
                        <p class="card-text">Enter your details, including the date, time, and number of guests.</p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <h5 class="card-title">Step 3: Confirm Booking</h5>
                        <p class="card-text">Review your booking and confirm it. You're all set for a great dining experience!</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Call to Action Section -->
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6 text-center">
                <h3 class="mb-4">Ready to Book Your Table?</h3>
                <a href="booking.php" class="btn btn-primary btn-lg w-100">Book Now</a>
            </div>
        </div>
    </div>

    <!-- Include Footer -->
    <?php include 'footer.php'; ?>
</body>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</html>