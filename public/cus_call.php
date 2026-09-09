<?php
/**
 * SMARTRESTA - Customer Call (Direct Table Order Portal)
 * Allows customers at dining tables to browse food, customize items with cooking instructions,
 * and place direct orders to the Kitchen Display System (KDS) without logging in.
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../core/MenuEngine.php';

// Fetch Categories & Available Products for the Customer Call Portal
$categories = [];
$products = [];
try {
    $categories = MenuEngine::getCategories();
    $products = MenuEngine::getProducts(['status' => 'ACTIVE', 'is_available' => 1]);
} catch (Exception $e) {
    // Dynamic fallback array if DB is not populated yet
    $categories = [
        ['id' => 1, 'name' => 'Main Course', 'slug' => 'main-course'],
        ['id' => 2, 'name' => 'Beverages & Drinks', 'slug' => 'beverages'],
        ['id' => 3, 'name' => 'Desserts', 'slug' => 'desserts'],
        ['id' => 4, 'name' => 'Side Dishes', 'slug' => 'side-dishes']
    ];
    $products = [
        [
            'id' => 1, 'category_id' => 1, 'name' => 'Gourmet Wagyu Beef Burger',
            'description' => 'Juicy Wagyu beef patty with caramelized onions, cheddar, and secret house sauce.',
            'price' => 14.99, 'image_url' => 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=600&auto=format&fit=crop&q=80',
            'category_name' => 'Main Course', 'station_badge' => 'KITCHEN'
        ],
        [
            'id' => 2, 'category_id' => 1, 'name' => 'Truffle Mushroom Pasta',
            'description' => 'Fresh fettuccine tossed in rich black truffle cream sauce with wild mushrooms.',
            'price' => 16.50, 'image_url' => 'https://images.unsplash.com/photo-1621996346565-e3d5d6281270?w=600&auto=format&fit=crop&q=80',
            'category_name' => 'Main Course', 'station_badge' => 'PASTA'
        ],
        [
            'id' => 3, 'category_id' => 2, 'name' => 'Iced Vanilla Matcha Latte',
            'description' => 'Ceremonial grade matcha blended with Madagascar vanilla and oat milk.',
            'price' => 5.50, 'image_url' => 'https://images.unsplash.com/photo-1536256263959-770b48d82b0a?w=600&auto=format&fit=crop&q=80',
            'category_name' => 'Beverages & Drinks', 'station_badge' => 'BAR'
        ],
        [
            'id' => 4, 'category_id' => 3, 'name' => 'Belgian Chocolate Lava Cake',
            'description' => 'Warm molten chocolate cake served with a scoop of artisanal vanilla bean ice cream.',
            'price' => 7.99, 'image_url' => 'https://images.unsplash.com/photo-1606313564200-e75d5e30476c?w=600&auto=format&fit=crop&q=80',
            'category_name' => 'Desserts', 'station_badge' => 'DESSERT'
        ]
    ];
}

// Convert products to JSON safe array for frontend JS search & cart management
$productsJson = json_encode($products, JSON_UNESCAPED_UNICODE);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Call - Direct Table Ordering | SMARTRESTA</title>
    <meta name="description" content="Order your favorite dishes directly from your table to the kitchen with custom instructions.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --bg-dark: #0b0f19;
            --bg-card: rgba(18, 24, 38, 0.85);
            --bg-card-hover: rgba(28, 36, 56, 0.95);
            --border-color: rgba(255, 255, 255, 0.08);
            --accent-gold: #f59e0b;
            --accent-orange: #f97316;
            --accent-cyan: #06b6d4;
            --accent-green: #10b981;
            --accent-purple: #8b5cf6;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --glass-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.5);
            --radius-lg: 16px;
            --radius-md: 12px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            background-color: var(--bg-dark);
            color: var(--text-main);
            min-height: 100vh;
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(245, 158, 11, 0.08) 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, rgba(6, 182, 212, 0.08) 0%, transparent 40%);
            background-attachment: fixed;
            padding-bottom: 90px;
        }

        /* Top Header */
        .header {
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(11, 15, 25, 0.88);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 1.5rem;
        }

        .header-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: #fff;
        }

        .brand-icon {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, var(--accent-gold), var(--accent-orange));
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            color: #fff;
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
        }

        .brand-text h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 1.35rem;
            font-weight: 700;
            background: linear-gradient(90deg, #fff, var(--text-muted));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1.2;
        }

        .brand-text span {
            font-size: 0.75rem;
            color: var(--accent-gold);
            letter-spacing: 1px;
            text-transform: uppercase;
            font-weight: 600;
        }

        /* Table & Customer Setup Strip */
        .info-strip {
            background: rgba(18, 24, 38, 0.6);
            border-bottom: 1px solid var(--border-color);
            padding: 1.25rem 1.5rem;
        }

        .info-container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr 1.5fr;
            gap: 1rem;
        }

        @media (max-width: 768px) {
            .info-container {
                grid-template-columns: 1fr 1fr;
            }
            .info-container .full-width-mobile {
                grid-column: span 2;
            }
        }

        .input-group {
            position: relative;
        }

        .input-group label {
            display: block;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--accent-gold);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.35rem;
        }

        .input-group input, .input-group select {
            width: 100%;
            padding: 0.65rem 0.85rem 0.65rem 2.25rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            color: #fff;
            font-size: 0.9rem;
            outline: none;
            transition: all 0.2s ease;
        }

        .input-group input:focus {
            border-color: var(--accent-gold);
            background: rgba(255, 255, 255, 0.08);
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.15);
        }

        .input-icon {
            position: absolute;
            left: 0.75rem;
            bottom: 0.75rem;
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        /* Main Container Layout */
        .main-layout {
            max-width: 1200px;
            margin: 1.5rem auto;
            padding: 0 1.5rem;
            display: grid;
            grid-template-columns: 1fr 340px;
            gap: 1.75rem;
        }

        @media (max-width: 992px) {
            .main-layout {
                grid-template-columns: 1fr;
            }
        }

        /* Category Filter Tabs */
        .category-scroll {
            display: flex;
            gap: 0.5rem;
            overflow-x: auto;
            padding-bottom: 0.75rem;
            margin-bottom: 1.25rem;
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 255, 255, 0.2) transparent;
        }

        .category-tab {
            padding: 0.55rem 1.1rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-color);
            border-radius: 30px;
            color: var(--text-muted);
            font-size: 0.85rem;
            font-weight: 500;
            cursor: pointer;
            white-space: nowrap;
            transition: all 0.25 ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .category-tab:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.1);
        }

        .category-tab.active {
            background: linear-gradient(135deg, var(--accent-gold), var(--accent-orange));
            color: #fff;
            border-color: transparent;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
        }

        .search-box {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .search-box input {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 2.5rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            color: #fff;
            font-size: 0.95rem;
            outline: none;
        }

        .search-box input:focus {
            border-color: var(--accent-cyan);
            box-shadow: 0 0 0 3px rgba(6, 182, 212, 0.15);
        }

        .search-box i {
            position: absolute;
            left: 0.9rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
        }

        /* Products Grid */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.25rem;
        }

        .product-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .product-card:hover {
            transform: translateY(-5px);
            border-color: rgba(245, 158, 11, 0.4);
            box-shadow: var(--glass-shadow);
            background: var(--bg-card-hover);
        }

        .product-image {
            width: 100%;
            height: 160px;
            object-fit: cover;
            background: #1e293b;
        }

        .product-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(11, 15, 25, 0.85);
            backdrop-filter: blur(8px);
            color: var(--accent-cyan);
            font-size: 0.7rem;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 20px;
            border: 1px solid rgba(6, 182, 212, 0.3);
            text-transform: uppercase;
        }

        .product-body {
            padding: 1rem;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .product-title {
            font-family: 'Outfit', sans-serif;
            font-size: 1.05rem;
            font-weight: 600;
            color: #fff;
            margin-bottom: 0.35rem;
        }

        .product-desc {
            font-size: 0.8rem;
            color: var(--text-muted);
            line-height: 1.4;
            margin-bottom: 0.85rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            flex-grow: 1;
        }

        .product-instruction-input {
            width: 100%;
            padding: 0.45rem 0.65rem;
            background: rgba(0, 0, 0, 0.25);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: #e2e8f0;
            font-size: 0.78rem;
            margin-bottom: 0.85rem;
            outline: none;
        }

        .product-instruction-input:focus {
            border-color: var(--accent-gold);
        }

        .product-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.5rem;
            padding-top: 0.5rem;
            border-top: 1px solid var(--border-color);
        }

        .product-price {
            font-family: 'Outfit', sans-serif;
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--accent-gold);
        }

        .btn-add {
            padding: 0.45rem 0.85rem;
            background: linear-gradient(135deg, var(--accent-gold), var(--accent-orange));
            color: #fff;
            border: none;
            border-radius: var(--radius-md);
            font-size: 0.82rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 0.35rem;
        }

        .btn-add:hover {
            filter: brightness(1.1);
            transform: scale(1.03);
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
        }

        /* Cart Drawer Sidebar */
        .cart-sidebar {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 1.25rem;
            display: flex;
            flex-direction: column;
            height: fit-content;
            position: sticky;
            top: 85px;
            box-shadow: var(--glass-shadow);
        }

        .cart-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 0.85rem;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 1rem;
        }

        .cart-title {
            font-family: 'Outfit', sans-serif;
            font-size: 1.1rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #fff;
        }

        .cart-badge {
            background: var(--accent-orange);
            color: #fff;
            font-size: 0.75rem;
            font-weight: 700;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .cart-items {
            max-height: 340px;
            overflow-y: auto;
            margin-bottom: 1rem;
            padding-right: 0.25rem;
        }

        .cart-item {
            padding: 0.75rem 0;
            border-bottom: 1px dashed var(--border-color);
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
        }

        .cart-item-main {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .cart-item-name {
            font-size: 0.88rem;
            font-weight: 600;
            color: #f1f5f9;
        }

        .cart-item-price {
            font-size: 0.88rem;
            font-weight: 700;
            color: var(--accent-gold);
        }

        .cart-item-instruction {
            font-size: 0.75rem;
            color: var(--accent-cyan);
            font-style: italic;
        }

        .cart-item-controls {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 0.25rem;
        }

        .qty-stepper {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 6px;
            padding: 2px 6px;
        }

        .qty-btn {
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            font-size: 0.85rem;
            padding: 2px 5px;
        }

        .qty-btn:hover {
            color: #fff;
        }

        .qty-val {
            font-size: 0.85rem;
            font-weight: 700;
            color: #fff;
            min-width: 16px;
            text-align: center;
        }

        .btn-remove {
            color: #ef4444;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 0.8rem;
        }

        .cart-summary {
            border-top: 1px solid var(--border-color);
            padding-top: 0.85rem;
            margin-bottom: 1rem;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-bottom: 0.35rem;
        }

        .summary-row.total {
            font-size: 1.05rem;
            font-weight: 700;
            color: #fff;
            margin-top: 0.5rem;
            padding-top: 0.5rem;
            border-top: 1px solid var(--border-color);
        }

        .btn-submit-order {
            width: 100%;
            padding: 0.85rem;
            background: linear-gradient(135deg, var(--accent-green), #059669);
            color: #fff;
            border: none;
            border-radius: var(--radius-md);
            font-family: 'Outfit', sans-serif;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        }

        .btn-submit-order:hover {
            filter: brightness(1.1);
            transform: translateY(-2px);
        }

        .btn-submit-order:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        /* Success Modal */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(8px);
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }

        .modal-overlay.active {
            opacity: 1;
            pointer-events: auto;
        }

        .modal-card {
            background: var(--bg-dark);
            border: 1px solid var(--accent-green);
            border-radius: var(--radius-lg);
            padding: 2rem;
            max-width: 440px;
            width: 90%;
            text-align: center;
            box-shadow: 0 25px 50px -12px rgba(16, 185, 129, 0.25);
            transform: scale(0.9);
            transition: transform 0.3s ease;
        }

        .modal-overlay.active .modal-card {
            transform: scale(1);
        }

        .success-icon {
            width: 70px;
            height: 70px;
            background: rgba(16, 185, 129, 0.15);
            border: 2px solid var(--accent-green);
            color: var(--accent-green);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin: 0 auto 1.25rem;
            animation: pulse-ring 2s infinite;
        }

        @keyframes pulse-ring {
            0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4); }
            70% { box-shadow: 0 0 0 15px rgba(16, 185, 129, 0); }
            100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }

        .modal-title {
            font-family: 'Outfit', sans-serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 0.5rem;
        }

        .modal-subtitle {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-bottom: 1.25rem;
        }

        .order-meta-box {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 1rem;
            margin-bottom: 1.5rem;
            font-size: 0.85rem;
            text-align: left;
        }

        .order-meta-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.4rem;
        }

        .order-meta-row:last-child {
            margin-bottom: 0;
        }

        .btn-modal-close {
            width: 100%;
            padding: 0.75rem;
            background: linear-gradient(135deg, var(--accent-gold), var(--accent-orange));
            color: #fff;
            border: none;
            border-radius: var(--radius-md);
            font-weight: 600;
            cursor: pointer;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <header class="header">
        <div class="header-container">
            <a href="../landing.php" class="brand-logo">
                <div class="brand-icon">
                    <i class="fa-solid fa-utensils"></i>
                </div>
                <div class="brand-text">
                    <h1>SMARTRESTA</h1>
                    <span>Customer Call Order Portal</span>
                </div>
            </a>
            <a href="../landing.php" style="color: var(--text-muted); text-decoration: none; font-size: 0.85rem;">
                <i class="fa-solid fa-arrow-left"></i> Back to Main Menu
            </a>
        </div>
    </header>

    <!-- Table & Customer Information Bar -->
    <section class="info-strip">
        <div class="info-container">
            <div class="input-group">
                <label for="customerName"><i class="fa-solid fa-user input-icon"></i> Customer Name *</label>
                <input type="text" id="customerName" placeholder="Enter your name" required>
            </div>
            <div class="input-group">
                <label for="tableNumber"><label><i class="fa-solid fa-chair input-icon"></i> Table Number *</label>
                <input type="text" id="tableNumber" placeholder="e.g. Table 05" required>
            </div>
            <div class="input-group full-width-mobile">
                <label for="tableNotes"><i class="fa-solid fa-comment-dots input-icon"></i> Special Table Note (Optional)</label>
                <input type="text" id="tableNotes" placeholder="e.g. High chair needed / Celebration">
            </div>
        </div>
    </section>

    <!-- Main Content Layout -->
    <main class="main-layout">
        <!-- Products & Categories Column -->
        <section>
            <!-- Category Tabs -->
            <div class="category-scroll" id="categoryTabs">
                <button class="category-tab active" onclick="filterCategory('ALL')">
                    <i class="fa-solid fa-border-all"></i> All Items
                </button>
                <?php foreach ($categories as $cat): ?>
                    <button class="category-tab" onclick="filterCategory(<?= $cat['id'] ?>)">
                        <i class="fa-solid fa-utensils"></i> <?= htmlspecialchars($cat['name']) ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <!-- Search Bar -->
            <div class="search-box">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="searchInput" placeholder="Search dishes, drinks, desserts..." onkeyup="filterProducts()">
            </div>

            <!-- Products Grid -->
            <div class="products-grid" id="productsGrid">
                <!-- Dynamically Rendered by JS -->
            </div>
        </section>

        <!-- Floating Cart Sidebar -->
        <aside class="cart-sidebar">
            <div class="cart-header">
                <div class="cart-title">
                    <i class="fa-solid fa-cart-shopping" style="color: var(--accent-gold);"></i> Table Order
                </div>
                <div class="cart-badge" id="cartBadgeCount">0</div>
            </div>

            <div class="cart-items" id="cartItemsContainer">
                <div style="text-align: center; color: var(--text-muted); padding: 2rem 0; font-size: 0.85rem;">
                    <i class="fa-solid fa-bowl-food" style="font-size: 2rem; opacity: 0.3; margin-bottom: 0.5rem; display: block;"></i>
                    Your order is empty.<br>Tap items to add them here!
                </div>
            </div>

            <div class="cart-summary">
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span id="subtotalVal">৳0.00</span>
                </div>
                <div class="summary-row">
                    <span>VAT (5%)</span>
                    <span id="taxVal">৳0.00</span>
                </div>
                <div class="summary-row total">
                    <span>Grand Total</span>
                    <span id="totalVal" style="color: var(--accent-gold);">৳0.00</span>
                </div>
            </div>

            <button class="btn-submit-order" id="btnSubmitOrder" onclick="submitCustomerOrder()">
                <i class="fa-solid fa-paper-plane"></i> Place Order to Kitchen
            </button>
        </aside>
    </main>

    <!-- Success Modal -->
    <div class="modal-overlay" id="successModal">
        <div class="modal-card">
            <div class="success-icon">
                <i class="fa-solid fa-check"></i>
            </div>
            <h2 class="modal-title">Order Received!</h2>
            <p class="modal-subtitle">Your order has been routed directly to our Kitchen Display System (KDS).</p>
            
            <div class="order-meta-box">
                <div class="order-meta-row">
                    <span style="color: var(--text-muted);">Order Number:</span>
                    <strong id="modalOrderNum" style="color: var(--accent-gold); font-family: 'Outfit';">ORD-0000</strong>
                </div>
                <div class="order-meta-row">
                    <span style="color: var(--text-muted);">Customer:</span>
                    <span id="modalCustomerName" style="color: #fff;">-</span>
                </div>
                <div class="order-meta-row">
                    <span style="color: var(--text-muted);">Table:</span>
                    <span id="modalTableNum" style="color: #fff;">-</span>
                </div>
                <div class="order-meta-row">
                    <span style="color: var(--text-muted);">Status:</span>
                    <span style="color: var(--accent-green); font-weight: 600;"><i class="fa-solid fa-fire-burner"></i> Preparing in Kitchen</span>
                </div>
            </div>

            <button class="btn-modal-close" onclick="closeSuccessModal()">Order More Items</button>
        </div>
    </div>

    <script>
        // Load initial products from PHP
        const rawProducts = <?= $productsJson ?>;
        let cart = [];
        let activeCategory = 'ALL';

        function renderProducts(itemsToRender) {
            const grid = document.getElementById('productsGrid');
            if (!itemsToRender || itemsToRender.length === 0) {
                grid.innerHTML = `
                    <div style="grid-column: 1 / -1; text-align: center; color: var(--text-muted); padding: 3rem 0;">
                        <i class="fa-solid fa-magnifying-glass" style="font-size: 2.5rem; opacity: 0.3; margin-bottom: 0.75rem; display: block;"></i>
                        No dishes found matching your criteria.
                    </div>
                `;
                return;
            }

            grid.innerHTML = itemsToRender.map(p => {
                const img = p.image_url || 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=600&auto=format&fit=crop&q=80';
                const stationBadge = p.station_badge || 'KITCHEN';
                const price = parseFloat(p.price || 0).toFixed(2);

                return `
                    <div class="product-card">
                        <span class="product-badge">${escapeHtml(stationBadge)}</span>
                        <img src="${escapeHtml(img)}" class="product-image" alt="${escapeHtml(p.name)}" onerror="this.src='https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=600&auto=format&fit=crop&q=80'">
                        <div class="product-body">
                            <h3 class="product-title">${escapeHtml(p.name)}</h3>
                            <p class="product-desc">${escapeHtml(p.description || 'Delicious culinary creation prepared fresh.')}</p>
                            <input type="text" class="product-instruction-input" id="inst_${p.id}" placeholder="Instruction (e.g. Extra spicy, No onions)">
                            <div class="product-footer">
                                <span class="product-price">৳${price}</span>
                                <button class="btn-add" onclick="addToCart(${p.id})">
                                    <i class="fa-solid fa-plus"></i> Add
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        }

        function filterCategory(catId) {
            activeCategory = catId;
            document.querySelectorAll('.category-tab').forEach(t => t.classList.remove('active'));
            event.currentTarget.classList.add('active');
            filterProducts();
        }

        function filterProducts() {
            const query = document.getElementById('searchInput').value.toLowerCase().trim();
            let filtered = rawProducts;

            if (activeCategory !== 'ALL') {
                filtered = filtered.filter(p => parseInt(p.category_id) === parseInt(activeCategory));
            }

            if (query) {
                filtered = filtered.filter(p => 
                    p.name.toLowerCase().includes(query) || 
                    (p.description && p.description.toLowerCase().includes(query))
                );
            }

            renderProducts(filtered);
        }

        function addToCart(productId) {
            const product = rawProducts.find(p => parseInt(p.id) === parseInt(productId));
            if (!product) return;

            const instInput = document.getElementById(`inst_${productId}`);
            const instruction = instInput ? instInput.value.trim() : '';

            // Check if item with same ID & instruction already exists in cart
            const existingIndex = cart.findIndex(ci => ci.id === productId && ci.instruction === instruction);
            if (existingIndex > -1) {
                cart[existingIndex].quantity += 1;
            } else {
                cart.push({
                    id: product.id,
                    name: product.name,
                    price: parseFloat(product.price || 0),
                    quantity: 1,
                    instruction: instruction
                });
            }

            // Clear instruction input after adding
            if (instInput) instInput.value = '';

            updateCartUI();
        }

        function updateQuantity(index, delta) {
            if (cart[index]) {
                cart[index].quantity += delta;
                if (cart[index].quantity <= 0) {
                    cart.splice(index, 1);
                }
                updateCartUI();
            }
        }

        function removeFromCart(index) {
            cart.splice(index, 1);
            updateCartUI();
        }

        function updateCartUI() {
            const container = document.getElementById('cartItemsContainer');
            const badge = document.getElementById('cartBadgeCount');
            const totalQty = cart.reduce((sum, item) => sum + item.quantity, 0);
            
            badge.innerText = totalQty;

            if (cart.length === 0) {
                container.innerHTML = `
                    <div style="text-align: center; color: var(--text-muted); padding: 2rem 0; font-size: 0.85rem;">
                        <i class="fa-solid fa-bowl-food" style="font-size: 2rem; opacity: 0.3; margin-bottom: 0.5rem; display: block;"></i>
                        Your order is empty.<br>Tap items to add them here!
                    </div>
                `;
                document.getElementById('subtotalVal').innerText = '৳0.00';
                document.getElementById('taxVal').innerText = '৳0.00';
                document.getElementById('totalVal').innerText = '৳0.00';
                return;
            }

            let subtotal = 0;
            container.innerHTML = cart.map((item, idx) => {
                const itemTotal = item.price * item.quantity;
                subtotal += itemTotal;

                return `
                    <div class="cart-item">
                        <div class="cart-item-main">
                            <span class="cart-item-name">${escapeHtml(item.name)}</span>
                            <span class="cart-item-price">৳${itemTotal.toFixed(2)}</span>
                        </div>
                        ${item.instruction ? `<div class="cart-item-instruction"><i class="fa-regular fa-note-sticky"></i> ${escapeHtml(item.instruction)}</div>` : ''}
                        <div class="cart-item-controls">
                            <div class="qty-stepper">
                                <button class="qty-btn" onclick="updateQuantity(${idx}, -1)">-</button>
                                <span class="qty-val">${item.quantity}</span>
                                <button class="qty-btn" onclick="updateQuantity(${idx}, 1)">+</button>
                            </div>
                            <button class="btn-remove" onclick="removeFromCart(${idx})">
                                <i class="fa-solid fa-trash-can"></i> Remove
                            </button>
                        </div>
                    </div>
                `;
            }).join('');

            const tax = subtotal * 0.05;
            const grandTotal = subtotal + tax;

            document.getElementById('subtotalVal').innerText = `৳${subtotal.toFixed(2)}`;
            document.getElementById('taxVal').innerText = `৳${tax.toFixed(2)}`;
            document.getElementById('totalVal').innerText = `৳${grandTotal.toFixed(2)}`;
        }

        async function submitCustomerOrder() {
            const customerName = document.getElementById('customerName').value.trim();
            const tableNumber = document.getElementById('tableNumber').value.trim();
            const tableNotes = document.getElementById('tableNotes').value.trim();
            const btnSubmit = document.getElementById('btnSubmitOrder');

            if (!customerName) {
                alert('Please enter your Customer Name.');
                document.getElementById('customerName').focus();
                return;
            }

            if (!tableNumber) {
                alert('Please enter your Table Number.');
                document.getElementById('tableNumber').focus();
                return;
            }

            if (cart.length === 0) {
                alert('Your order cart is empty. Please select at least one item.');
                return;
            }

            btnSubmit.disabled = true;
            btnSubmit.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i> Sending Order...`;

            const payload = {
                customer_name: customerName,
                table_number: tableNumber,
                notes: tableNotes,
                items: cart.map(ci => ({
                    product_id: ci.id,
                    quantity: ci.quantity,
                    special_instructions: ci.instruction
                }))
            };

            try {
                const response = await fetch('../api/v1/orders/public_create.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });

                const resData = await response.json();

                if (resData.success) {
                    document.getElementById('modalOrderNum').innerText = resData.data.order_number || 'ORD-CUS-001';
                    document.getElementById('modalCustomerName').innerText = customerName;
                    document.getElementById('modalTableNum').innerText = tableNumber;

                    document.getElementById('successModal').classList.add('active');

                    // Reset Cart
                    cart = [];
                    updateCartUI();
                } else {
                    alert('Order Placement Error: ' + (resData.message || 'Unknown error occurred.'));
                }
            } catch (err) {
                alert('Network Error: Could not dispatch order to server. Please try again.');
            } finally {
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = `<i class="fa-solid fa-paper-plane"></i> Place Order to Kitchen`;
            }
        }

        function closeSuccessModal() {
            document.getElementById('successModal').classList.remove('active');
        }

        function escapeHtml(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        // Initialize Products Grid on Page Load
        document.addEventListener('DOMContentLoaded', () => {
            renderProducts(rawProducts);
        });
    </script>
</body>
</html>
