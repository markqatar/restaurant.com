<?php
require_once __DIR__ .'/../includes/session.php';

// Handle language change
if (isset($_GET['lang']) && in_array($_GET['lang'], ['en', 'ar', 'it'])) {
    $_SESSION['lang'] = $_GET['lang'];
    $_SESSION['language'] = $_GET['lang'];
    
    // Redirect to remove lang parameter from URL
    $redirect_url = strtok($_SERVER["REQUEST_URI"], '?');
    
    // Preserve other query parameters if they exist
    $query = $_GET;
    unset($query['lang']);
    
    if (!empty($query)) {
        $redirect_url .= '?' . http_build_query($query);
    }
    
    // Perform the redirect
    header("Location: $redirect_url");
    exit();
}

// Handle theme change
if (isset($_GET['theme']) && in_array($_GET['theme'], ['light', 'dark'])) {
    $_SESSION['theme'] = $_GET['theme'];
    
    // Redirect to remove theme parameter from URL
    $redirect_url = strtok($_SERVER["REQUEST_URI"], '?');
    
    // Preserve other query parameters if they exist
    $query = $_GET;
    unset($query['theme']);
    
    if (!empty($query)) {
        $redirect_url .= '?' . http_build_query($query);
    }
    
    // Perform the redirect
    header("Location: $redirect_url");
    exit();
}

// Get public settings
try {
    $settings_stmt = $db->prepare("SELECT setting_key, setting_value FROM settings WHERE branch_id IS NULL");
    $settings_stmt->execute();
    $settings = $settings_stmt->fetchAll(PDO::FETCH_KEY_PAIR);
} catch (Exception $e) {
    $settings = [];
}

// Get active branches
try {
    $branches_stmt = $db->prepare("SELECT * FROM branches WHERE is_active = 1 ORDER BY name");
    $branches_stmt->execute();
    $branches = $branches_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $branches = [];
}

// Get featured products
try {
    $featured_stmt = $db->prepare("
        SELECT p.*, c.name as category_name 
        FROM final_products p 
        JOIN product_categories c ON p.category_id = c.id 
        WHERE p.is_active = 1 AND p.show_on_website = 1 
        ORDER BY p.sort_order, p.name 
        LIMIT 8
    ");
    $featured_stmt->execute();
    $featured_products = $featured_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $featured_products = [];
}

$page_title = $settings['site_title'] ?? 'Restaurant Management System';
$current_lang = $_SESSION['language'] ?? 'en';
$current_theme = $_SESSION['theme'] ?? 'light';
?>
<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>" dir="<?php echo ($current_lang === 'ar') ? 'rtl' : 'ltr'; ?>" data-bs-theme="<?php echo $current_theme; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    
    <!-- Bootstrap CSS -->
    <?php if ($current_lang === 'ar'): ?>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <?php else: ?>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <?php endif; ?>
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
        }
        
        .hero-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 100px 0;
        }
        
        .product-card {
            transition: transform 0.3s ease;
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
        }
        
        .btn-primary {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            border: none;
        }
        
        .btn-primary:hover {
            background: linear-gradient(45deg, var(--secondary-color), var(--primary-color));
        }
        
        .footer {
            background-color: #2c3e50;
            color: white;
            padding: 50px 0 20px;
        }
        
        .feature-icon {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-utensils me-2 text-primary"></i>
                <?php echo $settings['site_name'] ?? 'Restaurant'; ?>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="menu.php">Menu</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.php">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">Contact</a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <!-- Theme Selector -->
                    <li class="nav-item">
                        <button class="nav-link btn btn-link border-0" id="theme-toggle" title="Toggle Theme">
                            <i class="fas fa-moon" id="theme-icon"></i>
                        </button>
                    </li>
                    
                    <!-- Language Selector -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-globe me-1"></i>
                            <?php echo strtoupper($current_lang); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item lang-selector" href="#" data-lang="en">English</a></li>
                            <li><a class="dropdown-item lang-selector" href="#" data-lang="ar">العربية</a></li>
                            <li><a class="dropdown-item lang-selector" href="#" data-lang="it">Italiano</a></li>
                        </ul>
                    </li>
                    
                    <!-- Cart -->
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="cart.php">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="cart-count">
                                0
                            </span>
                        </a>
                    </li>
                    
                    <!-- User Account -->
                    <?php if (isset($_SESSION['customer_id'])): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-1"></i>
                            <?php echo $_SESSION['customer_name']; ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="dashboard.php">Dashboard</a></li>
                            <li><a class="dropdown-item" href="orders.php">My Orders</a></li>
                            <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                        </ul>
                    </li>
                    <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="admin/login">
                            <i class="fas fa-sign-in-alt me-1"></i>Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="auth/register.php">Register</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section text-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold mb-4">
                        Benvenuti al nostro Ristorante
                    </h1>
                    <p class="lead mb-5">
                        Scoprite la cucina più raffinata con ingredienti freschi e un servizio eccezionale. 
                        Ordinate online per la consegna o il ritiro!
                    </p>
                    <div class="d-flex gap-3 justify-content-center flex-wrap">
                        <a href="menu.php" class="btn btn-primary btn-lg px-4">
                            <i class="fas fa-utensils me-2"></i>Visualizza Menu
                        </a>
                        <a href="#order-online" class="btn btn-outline-light btn-lg px-4">
                            <i class="fas fa-shopping-cart me-2"></i>Ordina Online
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5">
        <div class="container">
            <div class="row text-center">
                <div class="col-lg-4 mb-4">
                    <div class="feature-icon">
                        <i class="fas fa-truck"></i>
                    </div>
                    <h4>Consegna Veloce</h4>
                    <p class="text-muted">Consegna rapida e affidabile direttamente a casa vostra</p>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="feature-icon">
                        <i class="fas fa-leaf"></i>
                    </div>
                    <h4>Ingredienti Freschi</h4>
                    <p class="text-muted">Utilizziamo solo ingredienti freschi e di alta qualità</p>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="feature-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h4>Ordina Facilmente</h4>
                    <p class="text-muted">Sistema di ordinazione online semplice e intuitivo</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    <?php if (!empty($featured_products)): ?>
    <section class="py-5 bg-light" id="featured-products">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2 class="display-5 fw-bold">I Nostri Piatti Speciali</h2>
                    <p class="lead text-muted">Scoprite le nostre specialità più apprezzate</p>
                </div>
            </div>
            <div class="row">
                <?php foreach ($featured_products as $product): ?>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card product-card h-100">
                        <?php if ($product['image']): ?>
                        <img src="../uploads/products/<?php echo $product['image']; ?>" class="card-img-top" alt="<?php echo $product['name']; ?>" style="height: 200px; object-fit: cover;">
                        <?php else: ?>
                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                            <i class="fas fa-utensils fa-3x text-muted"></i>
                        </div>
                        <?php endif; ?>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?php echo $product['name']; ?></h5>
                            <p class="card-text text-muted small"><?php echo $product['description'] ?? ''; ?></p>
                            <div class="mt-auto d-flex justify-content-between align-items-center">
                                <h6 class="text-primary mb-0"><?php echo format_currency($product['price']); ?></h6>
                                <button class="btn btn-primary btn-sm" onclick="addToCart(<?php echo $product['id']; ?>)">
                                    <i class="fas fa-plus"></i> Aggiungi
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-4">
                <a href="menu.php" class="btn btn-outline-primary btn-lg">
                    Visualizza Menu Completo <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Branches Section -->
    <?php if (!empty($branches)): ?>
    <section class="py-5" id="branches">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2 class="display-5 fw-bold">Le Nostre Sedi</h2>
                    <p class="lead text-muted">Trovateci nelle seguenti località</p>
                </div>
            </div>
            <div class="row">
                <?php foreach ($branches as $branch): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                <?php echo $branch['name']; ?>
                            </h5>
                            <p class="card-text">
                                <strong>Indirizzo:</strong><br>
                                <?php echo $branch['address']; ?><br>
                                <?php echo $branch['city']; ?>
                            </p>
                            <?php if ($branch['phone']): ?>
                            <p class="card-text">
                                <strong>Telefono:</strong><br>
                                <a href="tel:<?php echo $branch['phone']; ?>"><?php echo $branch['phone']; ?></a>
                            </p>
                            <?php endif; ?>
                            <div class="mb-3">
                                <strong>Servizi:</strong><br>
                                <?php if ($branch['dine_in']): ?>
                                    <span class="badge bg-success me-1">Pranzo in sede</span>
                                <?php endif; ?>
                                <?php if ($branch['pickup']): ?>
                                    <span class="badge bg-info me-1">Ritiro</span>
                                <?php endif; ?>
                                <?php if ($branch['delivery']): ?>
                                    <span class="badge bg-primary me-1">Consegna</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-utensils me-2"></i>
                        <?php echo $settings['site_name'] ?? 'Restaurant'; ?>
                    </h5>
                    <p class="text-light">
                        <?php echo $settings['site_description'] ?? 'Il miglior ristorante della città con cucina autentica e servizio eccellente.'; ?>
                    </p>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="fw-bold mb-3">Menu</h6>
                    <ul class="list-unstyled">
                        <li><a href="index.php" class="text-light text-decoration-none">Home</a></li>
                        <li><a href="menu.php" class="text-light text-decoration-none">Menu</a></li>
                        <li><a href="about.php" class="text-light text-decoration-none">Chi Siamo</a></li>
                        <li><a href="contact.php" class="text-light text-decoration-none">Contatti</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <h6 class="fw-bold mb-3">Contatti</h6>
                    <ul class="list-unstyled text-light">
                        <li><i class="fas fa-envelope me-2"></i> info@restaurant.com</li>
                        <li><i class="fas fa-phone me-2"></i> +39 123 456 7890</li>
                        <li><i class="fas fa-map-marker-alt me-2"></i> Via Roma 123, Milano</li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-12 mb-4">
                    <h6 class="fw-bold mb-3">Seguici</h6>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-light fs-4"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="text-light fs-4"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-light fs-4"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-light fs-4"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
            </div>
            <hr class="my-4">
            <div class="text-center">
                <p class="mb-0">&copy; <?php echo date('Y'); ?> <?php echo $settings['site_name'] ?? 'Restaurant'; ?>. Tutti i diritti riservati.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Language and Theme Handler -->
    <script src="../admin/admin/assets/js/language-theme.js"></script>
    
    <!-- Custom JS -->
    <script>
        // Shopping cart functionality
        let cart = JSON.parse(localStorage.getItem('restaurant_cart')) || [];
        
        function addToCart(productId) {
            // Add product to cart
            const existingItem = cart.find(item => item.id === productId);
            
            if (existingItem) {
                existingItem.quantity += 1;
            } else {
                cart.push({
                    id: productId,
                    quantity: 1
                });
            }
            
            localStorage.setItem('restaurant_cart', JSON.stringify(cart));
            updateCartCount();
            
            // Show success message
            alert('Prodotto aggiunto al carrello!');
        }
        
        function updateCartCount() {
            const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
            document.getElementById('cart-count').textContent = totalItems;
        }
        
        // Handle language and theme changes
        document.addEventListener('DOMContentLoaded', function() {
            updateCartCount();
            initializeTheme();
            
            // Language selector
            const langLinks = document.querySelectorAll('.lang-selector');
            langLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const lang = this.dataset.lang;
                    const url = new URL(window.location);
                    url.searchParams.set('lang', lang);
                    window.location.href = url.toString();
                });
            });
            
            // Theme toggle
            const themeToggle = document.getElementById('theme-toggle');
            const themeIcon = document.getElementById('theme-icon');
            
            themeToggle.addEventListener('click', function() {
                const currentTheme = document.documentElement.getAttribute('data-bs-theme');
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                
                // Update theme immediately
                document.documentElement.setAttribute('data-bs-theme', newTheme);
                updateThemeIcon(newTheme);
                
                // Save theme preference
                const url = new URL(window.location);
                url.searchParams.set('theme', newTheme);
                
                // Use fetch to save the theme without reloading the page
                fetch(url.toString())
                    .then(() => {
                        // Remove theme parameter from URL
                        url.searchParams.delete('theme');
                        window.history.replaceState({}, '', url.toString());
                    })
                    .catch(console.error);
            });
        });
        
        function initializeTheme() {
            const currentTheme = document.documentElement.getAttribute('data-bs-theme') || 'light';
            updateThemeIcon(currentTheme);
        }
        
        function updateThemeIcon(theme) {
            const themeIcon = document.getElementById('theme-icon');
            if (theme === 'dark') {
                themeIcon.className = 'fas fa-sun';
            } else {
                themeIcon.className = 'fas fa-moon';
            }
        }
        
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        });
    </script>
</body>
</html>