<?php
// Include database connection
include_once "db_connection.php";

// Fetch products from the database
$sql = "SELECT * FROM products";
$result = $conn->query($sql);

// Check if products exist
if ($result->num_rows > 0) {
    $products = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $products = [];
}

// Fetch categories from the database
$sql_categories = "SELECT DISTINCT category FROM products";
$result_categories = $conn->query($sql_categories);

// Check if categories exist
if ($result_categories->num_rows > 0) {
    $categories = $result_categories->fetch_all(MYSQLI_ASSOC);
} else {
    $categories = [];
}

// Check if search query is submitted
if(isset($_GET['search'])) {
    $search = $_GET['search'];
    // Fetch products based on search query
    $sql = "SELECT * FROM products WHERE title LIKE '%$search%' OR description LIKE '%$search%' OR category LIKE '%$search%'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $products = $result->fetch_all(MYSQLI_ASSOC);
    } else {
        // No products found for the search query
        $products = [];
    }
}

// Check if category filter is applied
if(isset($_GET['category'])) {
    $selected_category = $_GET['category'];
    // Fetch products based on selected category
    $sql = "SELECT * FROM products WHERE category = '$selected_category'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $products = $result->fetch_all(MYSQLI_ASSOC);
    } else {
        // No products found for the selected category
        $products = [];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LazyMart - Discover Amazing Products</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <link rel="shortcut icon" type="image/png" href="favicon.png">
    <link rel="icon" type="image/png" href="logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* General styles */
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #0e0b16;
            margin: 0;
            padding: 0;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Navbar styles */
        .navbar {
            background-color: #0e0b16;
            color: white;
            padding: 15px 20px;
            position: relative;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .logo {
            font-size: 24px;
            padding: 15px 20px;
            align-items: left;
            font-weight: bold;
            margin-right: 10px;
            text-decoration: none;
            color: white;
        }

        .menu-toggle {
            display: none;
        }

        .menu {
            display: flex;
            align-items: center;
        }

        .menu ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
            display: flex;
        }

        .menu ul li {
            margin-right: 20px;
        }

        .menu ul li a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .menu ul li a:hover {
            color: #55d0ff;
        }

        /* Button styles */
        .buy-now-button {
            padding: 10px 20px;
            background-color: red;
            color: white;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
            display: inline-block;
        }

        .buy-now-button:hover {
            background-color: darkred;
        }

        /* Price highlight effect */
        .product-card {
            border: 1px Green;
            border-radius: 5px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            margin-bottom: 10px;
        }

        /* Product card styles */
        .product-card {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            margin-bottom: 20px;
            background-color: #d9d9d9;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        .product-card:hover {
            transform: translateY(-5px);
        }

        .product-card img {
            max-width: 100%;
            height: auto;
            margin-bottom: 15px;
            transition: transform 0.3s ease;
        }

        .product-card:hover img {
            transform: scale(1.1);
        }

        /* Share icon */
        .share-icon {
            color: black;
            font-size: 20px;
            cursor: pointer;
            margin-left: 10px;
        }

        /* Share modal */
        .share-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 999;
        }

        .share-modal.active {
            display: flex;
        }

        .share-modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            text-align: center;
            position: relative;
        }

        .close-icon {
            position: absolute;
            top: 10px;
            right: 10px;
            cursor: pointer;
            font-size: 20px;
            color: #007bff;
        }

        /* Share buttons */
        .share-buttons {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .share-buttons a {
            text-decoration: none;
            color: #007bff;
            font-size: 20px;
            margin: 0 10px;
        }

        /* Copy link */
        .copy-link-button {
            cursor: pointer;
        }

        .product-info {
            padding: 10px 0;
        }

        .product-info h3 {
            margin: 0;
        }

        .product-info p {
            margin: 5px 0;
            font-size: 14px;
        }

        /* Hero section styles */
        .hero {
            background-color: #0e0b16;
            color: white;
            padding: 60px 1px;
            text-align: center;
            padding-bottom: 10px;
        }

        .hero h1 {
            margin: 0;
            font-size: 36px;
        }

        .search-form {
            margin-top: 5px;
        }

        .search-form input[type="text"] {
            padding: 10px;
            width: 70%;
            border: none;
            border-radius: 5px;
            margin-right: 10px;
        }

        .search-form button {
            padding: 12px 20px;
            background-color: #4717f6;
            color: white;
            border: none;
            margin-top: 5px;
            border-radius: 5px;
            cursor: pointer;
        }

        /* Responsive styles */
        @media only screen and (max-width: 768px) {
            .container {
                padding: 0 10px;
            }

            .menu {
                display: none;
                position: fixed;
                top: 60px;
                left: 0;
                width: 100%;
                background-color: #0e0b16;
                padding: 10px 0;
                flex-direction: column;
                text-align: center;
            }

            .menu-toggle {
                display: block;
                position: absolute;
                top: 20px;
                right: 20px;
                font-size: 20px;
                cursor: pointer;
                color: white;
            }

            .menu ul {
                display: block;
            }

            .menu ul li {
                margin: 10px 0;
            }

            .menu.active {
                display: flex;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation bar -->
    <nav class="navbar">
        <div class="container">
            <a href="index.html" class="logo">LazyMart</a>
            <div class="menu">
                <ul>
                    <li><a href="index.html">Home</a></li>
                    <li>
                        <select onchange="location = this.value;">
                            <option value="#">Categories</option>
                            <?php foreach ($categories as $cat) : ?>
                                <option value="index.php?category=<?php echo urlencode($cat['category']); ?>"><?php echo $cat['category']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </li>
                    <li><a href="about.html">About</a></li>
                    <li><a href="admin-login.html">Admin</a></li>
                </ul>
            </div>
            <div class="menu-toggle" onclick="toggleMenu()">
                <i class="fas fa-bars"></i>
            </div>
        </div>
    </nav>

    <!-- Hero section with search bar -->
    <section class="hero">
        <div class="container">
            <h1>Discover Amazing Products</h1>
            <form class="search-form" method="GET" action="index.php">
                <input type="text" name="search" placeholder="Search products...">
                <button type="submit">Search</button>
            </form>
        </div>
    </section>

    <!-- Product display section -->
<section class="products">
    <div class="container">
        <h2>Featured Products</h2>
        <div class="product-grid">
            <!-- Loop through products and display -->
            <?php foreach ($products as $product) : ?>
                <div class="product-card">
                    <img src="<?php echo $product['image_path']; ?>" alt="Product Image">
                    <div class="product-info">
                        <h3><?php echo $product['title']; ?></h3>
                        <p><?php echo $product['description']; ?></p>
                        <p>Category: <?php echo $product['category']; ?></p>
                        <p class="price">Price: <?php echo $product['price']; ?></p>
                        <div class="button-container">
                            <a href="<?php echo $product['affiliate_link']; ?>" class="buy-now-button">Buy Now</a>
                            <i class="fas fa-share-alt share-icon" onclick="toggleShareModal('<?php echo $product['title']; ?>', '<?php echo $product['description']; ?>', '<?php echo $product['affiliate_link']; ?>', '<?php echo $product['image_path']; ?>')"></i>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>


    <!-- Footer section -->
    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 Lazymart. All rights reserved.</p>
        </div>
    </footer>

    <!-- Share modal -->
<div id="shareModal" class="share-modal">
    <div class="share-modal-content">
        <i class="fas fa-times close-icon" onclick="toggleShareModal()"></i>
        <h2>Share Product</h2>
        <div class="share-buttons">
            <a href="https://www.facebook.com/sharer.php?u=<?php echo urlencode($affiliateLink); ?>" target="_blank"><i class="fab fa-facebook"></i></a>
            <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($affiliateLink); ?>&text=<?php echo urlencode($title); ?>" target="_blank"><i class="fab fa-twitter"></i></a>
            <a href="https://api.whatsapp.com/send?text=<?php echo urlencode($title . ' ' . $affiliateLink); ?>" target="_blank"><i class="fab fa-whatsapp"></i></a>
            <a href="https://www.instagram.com/?url=<?php echo urlencode($affiliateLink); ?>" target="_blank"><i class="fab fa-instagram"></i></a>
            <a href="https://telegram.me/share/url?url=<?php echo urlencode($affiliateLink); ?>&text=<?php echo urlencode($title); ?>" target="_blank"><i class="fab fa-telegram"></i></a>
        </div>
        <!-- Copy link option -->
        <div class="copy-link-button" onclick="copyLink('<?php echo $affiliateLink; ?>')">Copy Link</div>
    </div>
</div>


    <script>
        // Function to toggle menu for small screens
        function toggleMenu() {
            var menu = document.querySelector('.menu');
            menu.classList.toggle('active');

            var menuToggle = document.querySelector('.menu-toggle');
            menuToggle.classList.toggle('active');
        }

        // Function to toggle share modal
function toggleShareModal(title, description, affiliateLink, imagePath) {
    var modal = document.getElementById('shareModal');
    modal.classList.toggle('active');

    // Set content for share modal dynamically
    var modalContent = document.querySelector('.share-modal-content');
    modalContent.innerHTML = `
        <i class="fas fa-times close-icon" onclick="toggleShareModal()"></i>
        <h2>Share Product</h2>
        <div class="share-buttons">
            <a href="https://www.facebook.com/sharer.php?u=${encodeURIComponent(affiliateLink)}" target="_blank"><i class="fab fa-facebook"></i></a>
            <a href="https://twitter.com/intent/tweet?url=${encodeURIComponent(affiliateLink)}&text=${encodeURIComponent(title)}" target="_blank"><i class="fab fa-twitter"></i></a>
            <a href="https://api.whatsapp.com/send?text=${encodeURIComponent(title + ' ' + affiliateLink)}" target="_blank"><i class="fab fa-whatsapp"></i></a>
            <a href="https://www.instagram.com/?url=${encodeURIComponent(affiliateLink)}" target="_blank"><i class="fab fa-instagram"></i></a>
            <a href="https://telegram.me/share/url?url=${encodeURIComponent(affiliateLink)}&text=${encodeURIComponent(title)}" target="_blank"><i class="fab fa-telegram"></i></a>
        </div>
        <!-- Copy link option -->
        <div class="copy-link-button" onclick="copyLink('${affiliateLink}')">Copy Link</div>
    `;
}


        // Function to copy link to clipboard
        function copyLink(link) {
            var tempInput = document.createElement('input');
            tempInput.value = link;
            document.body.appendChild(tempInput);
            tempInput.select();
            document.execCommand('copy');
            document.body.removeChild(tempInput);
            alert('Link copied to clipboard!');
        }
    </script>
</body>
</html>
