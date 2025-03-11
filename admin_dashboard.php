<?php
session_start();
require 'db.php';

// Redirect to login if admin is not logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit;
}

// Handle form submission for adding a new menu item
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_menu_item'])) {
    $name = $_POST['name'];
    $description = $_POST['description'] ?? ''; // Default to empty string if not set
    $price = $_POST['price'];
    $category = $_POST['category'];

    // Insert the new menu item into the database
    $stmt = $pdo->prepare("INSERT INTO menu (name, description, price, category) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $description, $price, $category]);

    echo "<script>alert('Menu item added successfully!');</script>";
}

// Handle form submission for updating a menu item
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_menu_item'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $description = $_POST['description'] ?? ''; // Default to empty string if not set
    $price = $_POST['price'];
    $category = $_POST['category'];

    // Update the menu item in the database
    $stmt = $pdo->prepare("UPDATE menu SET name = ?, description = ?, price = ?, category = ? WHERE id = ?");
    $stmt->execute([$name, $description, $price, $category, $id]);

    echo "<script>alert('Menu item updated successfully!');</script>";
}

// Fetch all menu items from the database
$menu_stmt = $pdo->query("SELECT * FROM menu");
$menu_items = $menu_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Management</title>
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
        <h2 class="text-center mb-4">Menu Management</h2>

        <!-- Add New Menu Item Form -->
        <h3 class="mb-3">Add New Menu Item</h3>
        <form method="POST" class="w-50 mx-auto mb-5">
            <input type="hidden" name="add_menu_item" value="1">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description"></textarea>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Price</label>
                <input type="number" step="0.01" class="form-control" id="price" name="price" required>
            </div>
            <div class="mb-3">
                <label for="category" class="form-label">Category</label>
                <select class="form-select" id="category" name="category" required>
                    <option value="Appetizers">Appetizers</option>
                    <option value="Main Course">Main Course</option>
                    <option value="Desserts">Desserts</option>
                    <option value="Beverages">Beverages</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary w-100">Add Menu Item</button>
        </form>

        <!-- Display Existing Menu Items -->
        <h3 class="text-center mb-3">Existing Menu Items</h3>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Category</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($menu_items as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['id']) ?></td>
                        <td><?= htmlspecialchars($item['name']) ?></td>
                        <td><?= htmlspecialchars($item['description'] ?? 'No description') ?></td>
                        <td><?= htmlspecialchars($item['price']) ?></td>
                        <td><?= htmlspecialchars($item['category']) ?></td>
                        <td>
                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $item['id'] ?>">Edit</button>
                        </td>
                    </tr>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="editModal<?= $item['id'] ?>" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editModalLabel">Edit Menu Item</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form method="POST">
                                    <div class="modal-body">
                                        <input type="hidden" name="id" value="<?= htmlspecialchars($item['id']) ?>">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Name</label>
                                            <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($item['name']) ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="description" class="form-label">Description</label>
                                            <textarea class="form-control" id="description" name="description"><?= htmlspecialchars($item['description'] ?? '') ?></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label for="price" class="form-label">Price</label>
                                            <input type="number" step="0.01" class="form-control" id="price" name="price" value="<?= htmlspecialchars($item['price']) ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="category" class="form-label">Category</label>
                                            <select class="form-select" id="category" name="category" required>
                                                <option value="Appetizers" <?= $item['category'] == 'Appetizers' ? 'selected' : '' ?>>Appetizers</option>
                                                <option value="Main Course" <?= $item['category'] == 'Main Course' ? 'selected' : '' ?>>Main Course</option>
                                                <option value="Desserts" <?= $item['category'] == 'Desserts' ? 'selected' : '' ?>>Desserts</option>
                                                <option value="Beverages" <?= $item['category'] == 'Beverages' ? 'selected' : '' ?>>Beverages</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" name="update_menu_item" class="btn btn-primary">Save Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Include Footer -->
    <?php include 'footer.php'; ?>

    <!-- Bootstrap JS and Popper.js (Required for Navbar Toggle and Modals) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>