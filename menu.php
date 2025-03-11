<?php
session_start();
require 'db.php';

// Redirect to login if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle order submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['item_id'])) {
    $item_id = $_POST['item_id'];

    // Fetch the selected item details from the menu
    $stmt = $pdo->prepare("SELECT name, category, price FROM menu WHERE id = ?");
    $stmt->execute([$item_id]);
    $item = $stmt->fetch();

    if ($item) {
        // Hardcode the order type as "Dine-In"
        $order_type = "Dine-In";

        // Insert the order into the database
        $insert_stmt = $pdo->prepare("INSERT INTO orders (user_id, item_name, category, price, order_type, status) 
                                      VALUES (?, ?, ?, ?, ?, ?)");
        $insert_stmt->execute([
            $user_id,
            $item['name'],
            $item['category'],
            $item['price'],
            $order_type,
            'Pending' // Default status for new orders
        ]);

        echo "<script>alert('Order placed successfully for Dine-In!');</script>";
    } else {
        echo "<script>alert('Invalid item selected. Please try again.');</script>";
    }
}

// Fetch all menu items
$menu_stmt = $pdo->query("SELECT * FROM menu");
$menu_items = $menu_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <!-- Include Navbar -->
    <?php include 'navbar.php'; ?>

    <!-- Main Content -->
    <div class="container mt-5">
        <h2 class="text-center">Our Menu</h2>

        <!-- Veg Items -->
        <h3 class="mt-5">Veg Items</h3>
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <?php foreach ($menu_items as $item): ?>
                <?php if ($item['category'] === 'Veg'): ?>
                    <div class="col">
                        <div class="card h-100">
                            <!-- Check if image exists, otherwise use default image -->
                            <img src="<?= !empty($item['image']) ? htmlspecialchars($item['image']) : 'https://cdn-icons-png.flaticon.com/512/10107/10107601.png' ?>"
                                class="card-img-top" alt="<?= htmlspecialchars($item['name']) ?>"
                                style="height: 200px; object-fit: cover;">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($item['name']) ?></h5>
                                <p class="card-text"><strong>Price:</strong> ₹<?= htmlspecialchars($item['price']) ?></p>
                                <form method="POST" class="mt-3">
                                    <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                                    <button type="submit" class="btn btn-primary w-100">Order for Dine-In</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <!-- Non-Veg Items -->
        <h3 class="mt-5">Non-Veg Items</h3>
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <?php foreach ($menu_items as $item): ?>
                <?php if ($item['category'] === 'Non-Veg'): ?>
                    <div class="col">
                        <div class="card h-100">
                            <!-- Check if image exists, otherwise use default image -->
                            <img src="<?= !empty($item['image']) ? htmlspecialchars($item['image']) : 'https://cdn-icons-png.flaticon.com/512/10107/10107601.png' ?>"
                                class="card-img-top" alt="<?= htmlspecialchars($item['name']) ?>"
                                style="height: 200px; object-fit: cover;">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($item['name']) ?></h5>
                                <p class="card-text"><strong>Price:</strong> ₹<?= htmlspecialchars($item['price']) ?></p>
                                <form method="POST" class="mt-3">
                                    <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                                    <button type="submit" class="btn btn-primary w-100">Order for Dine-In</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Include Footer -->
    <?php include 'footer.php'; ?>
</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</html>