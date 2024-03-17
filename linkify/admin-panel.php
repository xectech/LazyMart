<?php
session_start();
// Check if user is logged in
if (!isset($_SESSION["admin"])) {
    header("Location: admin-login.html");
    exit();
}

// Include database connection
include_once "db_connection.php";

// Function to delete product
function deleteProduct($conn, $product_id) {
    // Prepare statement for deleting product from database
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);

    // Execute statement
    if ($stmt->execute()) {
        // Product deleted successfully
        return true;
    } else {
        return false;
    }
}

// Check if product ID is provided in the URL for deletion
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    // Delete product if confirmation is received
    if (isset($_GET['confirm']) && $_GET['confirm'] == 'yes') {
        if (deleteProduct($conn, $delete_id)) {
            header("Location: admin-panel.php");
            exit();
        } else {
            echo "Error: Unable to delete product.";
        }
    } else {
        // Display confirmation dialog
        echo "<script>
                var confirmDelete = confirm('Are you sure you want to delete this product?');
                if (confirmDelete) {
                    window.location.href = 'admin-panel.php?delete_id=$delete_id&confirm=yes';
                } else {
                    window.location.href = 'admin-panel.php';
                }
              </script>";
    }
}

// Fetch products from the database
$sql = "SELECT * FROM products";
$result = $conn->query($sql);

// Check if products exist
if ($result->num_rows > 0) {
    $products = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $products = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="favicon.png">
    <link rel="icon" type="image/png" href="logo.png">
    <title>Admin Panel</title>
    <style>
        /* Agency-style CSS */
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 50px auto;
            background-color: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #333;
        }

        a {
            display: block;
            margin-bottom: 10px;
            text-align: center;
            text-decoration: none;
            color: #007bff;
        }

        a:hover {
            text-decoration: underline;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #ddd;
        }

        /* Image styling */
        .product-image {
            max-width: 100px;
            max-height: 100px;
        }

        /* Confirmation dialog */
        .confirmation-dialog {
            text-align: center;
            margin-top: 20px;
        }

        .confirm-button,
        .cancel-button {
            padding: 10px 20px;
            margin: 0 10px;
            border-radius: 5px;
            cursor: pointer;
        }

        .confirm-button {
            background-color: #007bff;
            color: white;
            border: none;
        }

        .cancel-button {
            background-color: #ccc;
            color: #333;
            border: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Welcome, Admin</h2>
        <!-- Navigation link to add product page -->
        <a href="add-product.php">Add Product</a>
        <!-- Navigation link to view products -->
        <a href="index.php">View Products</a>
        <table>
            <tr>
                <th>Title</th>
                <th>Description</th>
                <th>Category</th>
                <th>Price</th>
                <th>Image</th>
                <th>Action</th>
            </tr>
            <?php foreach ($products as $product) : ?>
                <tr>
                    <td><?php echo $product['title']; ?></td>
                    <td><?php echo $product['description']; ?></td>
                    <td><?php echo $product['category']; ?></td>
                    <td><?php echo $product['price']; ?></td>
                    <!-- Check if image path is not empty before displaying image -->
                    <td><?php if (!empty($product['image_path'])) : ?><img src="<?php echo $product['image_path']; ?>" alt="Product Image" class="product-image"><?php endif; ?></td>
                    <!-- Add button for product deletion with confirmation -->
                    <td>
                        <button onclick="confirmDelete(<?php echo $product['id']; ?>)">Delete</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        <!-- Hidden form for confirmation dialog -->
        <form id="confirmationForm" method="GET" style="display: none;">
            <input type="hidden" name="delete_id" id="delete_id">
            <input type="hidden" name="confirm" id="confirm">
        </form>
    </div>

    <script>
        // Function to show confirmation dialog
        function confirmDelete(productId) {
            var confirmDelete = confirm('Are you sure you want to delete this product?');
            if (confirmDelete) {
                // Set delete_id and confirm in hidden form fields
                document.getElementById('delete_id').value = productId;
                document.getElementById('confirm').value = 'yes';
                // Submit form to trigger deletion
                document.getElementById('confirmationForm').submit();
            }
        }
    </script>
</body>
</html>
