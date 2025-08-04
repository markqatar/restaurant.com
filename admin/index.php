<?php
// Process language changes first, before any output
require_once __DIR__ . '/includes/functions.php';
require_once get_setting('base_path', '/var/www/html') . 'admin/includes/process_language.php';
require_once __DIR__ . '/../includes/session.php';

// Check if user is logged in
if (!is_logged_in()) {
    redirect('/admin/login');
}

$page_title = "Dashboard - Restaurant Management";

// Get dashboard statistics
try {
    // Today's revenue
    $today_revenue_stmt = $db->prepare("
        SELECT COALESCE(SUM(total), 0) as today_revenue 
        FROM orders 
        WHERE DATE(order_date) = CURDATE() AND status NOT IN ('cancelled')
    ");
    $today_revenue_stmt->execute();
    $today_revenue = $today_revenue_stmt->fetch(PDO::FETCH_ASSOC)['today_revenue'];

    // Today's orders
    $today_orders_stmt = $db->prepare("
        SELECT COUNT(*) as today_orders 
        FROM orders 
        WHERE DATE(order_date) = CURDATE()
    ");
    $today_orders_stmt->execute();
    $today_orders = $today_orders_stmt->fetch(PDO::FETCH_ASSOC)['today_orders'];

    // Pending orders
    $pending_orders_stmt = $db->prepare("
        SELECT COUNT(*) as pending_orders 
        FROM orders 
        WHERE status IN ('pending', 'confirmed', 'preparing')
    ");
    $pending_orders_stmt->execute();
    $pending_orders = $pending_orders_stmt->fetch(PDO::FETCH_ASSOC)['pending_orders'];

    // Total customers
    $total_customers_stmt = $db->prepare("SELECT COUNT(*) as total_customers FROM customers WHERE is_active = 1");
    $total_customers_stmt->execute();
    $total_customers = $total_customers_stmt->fetch(PDO::FETCH_ASSOC)['total_customers'];

    // Recent orders
    $recent_orders_stmt = $db->prepare("
        SELECT o.*, c.first_name, c.last_name, b.name as branch_name
        FROM orders o
        LEFT JOIN customers c ON o.customer_id = c.id
        LEFT JOIN branches b ON o.branch_id = b.id
        ORDER BY o.order_date DESC
        LIMIT 10
    ");
    $recent_orders_stmt->execute();
    $recent_orders = $recent_orders_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Low stock items
    $low_stock_stmt = $db->prepare("
        SELECT name, current_stock, min_stock 
        FROM raw_materials 
        WHERE current_stock <= min_stock AND min_stock > 0
        ORDER BY (current_stock/min_stock) ASC
        LIMIT 10
    ");
    $low_stock_stmt->execute();
    $low_stock_items = $low_stock_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Expiring items
    $expiring_items_stmt = $db->prepare("
        SELECT name, expiry_date, current_stock 
        FROM raw_materials 
        WHERE expiry_date <= DATE_ADD(CURDATE(), INTERVAL 7 DAY) AND current_stock > 0
        ORDER BY expiry_date ASC
        LIMIT 10
    ");
    $expiring_items_stmt->execute();
    $expiring_items = $expiring_items_stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    $today_revenue = 0;
    $today_orders = 0;
    $pending_orders = 0;
    $total_customers = 0;
    $recent_orders = [];
    $low_stock_items = [];
    $expiring_items = [];
}
require_once get_setting('base_path', '/var/www/html') . 'admin/modules/system/controllers/TranslationManager.php';

// Inizializza il sistema di traduzioni
TranslationManager::init();

include get_setting('base_path', '/var/www/html') . 'admin/layouts/header.php'; ?>

<div class="container-fluid">
    <div class="row">        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Dashboard</h1>
                <div class="btn-toolbar mb-2 mb-md-0 ms-auto">
                    <div class="btn-group me-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="location.reload()">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Today's Revenue</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?php echo format_currency($today_revenue); ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Orders Today</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $today_orders; ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Pending Orders</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $pending_orders; ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-clock fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Total Customers</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_customers; ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-users fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="row mb-4">
                <div class="col-xl-8 col-lg-7">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Revenue Overview</h6>
                        </div>
                        <div class="card-body">
                            <canvas id="revenueChart"></canvas>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-4 col-lg-5">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Order Types</h6>
                        </div>
                        <div class="card-body">
                            <canvas id="orderTypeChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tables Row -->
            <div class="row">
                <!-- Recent Orders -->
                <div class="col-lg-6 mb-4">
                    <div class="card shadow">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">Recent Orders</h6>
                            <a href="orders.php" class="btn btn-sm btn-primary">View All</a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <thead>
                                        <tr>
                                            <th>Order #</th>
                                            <th>Customer</th>
                                            <th>Status</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recent_orders as $order): ?>
                                        <tr>
                                            <td>
                                                <a href="orders.php?view=<?php echo $order['id']; ?>">
                                                    <?php echo $order['order_number']; ?>
                                                </a>
                                            </td>
                                            <td>
                                                <?php echo $order['first_name'] . ' ' . $order['last_name'] ?: $order['customer_name']; ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?php 
                                                    echo match($order['status']) {
                                                        'pending' => 'warning',
                                                        'confirmed' => 'info',
                                                        'preparing' => 'primary',
                                                        'ready' => 'success',
                                                        'delivered' => 'success',
                                                        'cancelled' => 'danger',
                                                        default => 'secondary'
                                                    };
                                                ?>">
                                                    <?php echo ucfirst($order['status']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo format_currency($order['total']); ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Alerts -->
                <div class="col-lg-6 mb-4">
                    <!-- Low Stock Alert -->
                    <?php if (!empty($low_stock_items)): ?>
                    <div class="card shadow mb-3">
                        <div class="card-header py-3 bg-warning">
                            <h6 class="m-0 font-weight-bold text-white">
                                <i class="fas fa-exclamation-triangle me-2"></i>Low Stock Alert
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Item</th>
                                            <th>Current</th>
                                            <th>Min</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($low_stock_items as $item): ?>
                                        <tr>
                                            <td><?php echo $item['name']; ?></td>
                                            <td class="text-danger"><?php echo $item['current_stock']; ?></td>
                                            <td><?php echo $item['min_stock']; ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Expiring Items Alert -->
                    <?php if (!empty($expiring_items)): ?>
                    <div class="card shadow">
                        <div class="card-header py-3 bg-danger">
                            <h6 class="m-0 font-weight-bold text-white">
                                <i class="fas fa-calendar-times me-2"></i>Expiring Soon
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Item</th>
                                            <th>Expires</th>
                                            <th>Stock</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($expiring_items as $item): ?>
                                        <tr>
                                            <td><?php echo $item['name']; ?></td>
                                            <td class="text-danger"><?php echo format_date($item['expiry_date'], 'M d, Y'); ?></td>
                                            <td><?php echo $item['current_stock']; ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

        </main>
    </div>
</div>

<script>
// Revenue Chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
const revenueChart = new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        datasets: [{
            label: 'Revenue',
            data: [1200, 1900, 800, 1700, 2100, 2800, 2200],
            borderColor: '#667eea',
            backgroundColor: 'rgba(102, 126, 234, 0.1)',
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Order Type Chart
const orderTypeCtx = document.getElementById('orderTypeChart').getContext('2d');
const orderTypeChart = new Chart(orderTypeCtx, {
    type: 'doughnut',
    data: {
        labels: ['Delivery', 'Pickup', 'Dine-in'],
        datasets: [{
            data: [60, 25, 15],
            backgroundColor: ['#667eea', '#764ba2', '#f093fb']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});
</script>

<?php include get_setting('base_path', '/var/www/html') . 'admin/layouts/footer.php'; ?>