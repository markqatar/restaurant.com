<?php
/**
 * Restaurant Management System Installer
 * This script helps set up the database and initial configuration
 */

// Check if already installed
if (file_exists('../config/installed.lock')) {
    die('System is already installed. If you need to reinstall, delete the config/installed.lock file.');
}

$step = $_GET['step'] ?? 1;
$error = '';
$success = '';

// Handle installation steps
if ($_POST) {
    switch ($step) {
        case 2:
            // Database configuration test
            $host = $_POST['db_host'] ?? 'localhost';
            $user = $_POST['db_user'] ?? '';
            $pass = $_POST['db_pass'] ?? '';
            $name = $_POST['db_name'] ?? '';
            
            try {
                $dsn = "mysql:host=$host;charset=utf8mb4";
                $pdo = new PDO($dsn, $user, $pass);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                // Create database if it doesn't exist
                $pdo->exec("CREATE DATABASE IF NOT EXISTS `$name`");
                $pdo->exec("USE `$name`");
                
                // Save database config
                $config_content = "<?php\n";
                $config_content .= "define('DB_HOST', '$host');\n";
                $config_content .= "define('DB_USER', '$user');\n";
                $config_content .= "define('DB_PASS', '$pass');\n";
                $config_content .= "define('DB_NAME', '$name');\n";
                $config_content .= "?>";
                
                file_put_contents('../config/database.php', $config_content);
                
                $success = 'Database connection successful!';
                $step = 3;
            } catch (Exception $e) {
                $error = 'Database connection failed: ' . $e->getMessage();
            }
            break;
            
        case 3:
            // Import database schema
            try {
                require_once '../config/database.php';
                $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
                $pdo = new PDO($dsn, DB_USER, DB_PASS);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                // Read and execute SQL file
                $sql = file_get_contents('database.sql');
                $pdo->exec($sql);
                
                $success = 'Database schema imported successfully!';
                $step = 4;
            } catch (Exception $e) {
                $error = 'Database import failed: ' . $e->getMessage();
            }
            break;
            
        case 4:
            // Create admin user
            $username = $_POST['admin_username'] ?? 'admin';
            $email = $_POST['admin_email'] ?? '';
            $password = $_POST['admin_password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            
            if ($password !== $confirm_password) {
                $error = 'Passwords do not match!';
            } elseif (strlen($password) < 6) {
                $error = 'Password must be at least 6 characters!';
            } else {
                try {
                    require_once '../config/database.php';
                    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
                    $pdo = new PDO($dsn, DB_USER, DB_PASS);
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    
                    // Update admin user
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, password = ? WHERE id = 1");
                    $stmt->execute([$username, $email, $hashed_password]);
                    
                    // Create installation lock file
                    file_put_contents('../config/installed.lock', date('Y-m-d H:i:s'));
                    
                    $success = 'Installation completed successfully!';
                    $step = 5;
                } catch (Exception $e) {
                    $error = 'Admin user creation failed: ' . $e->getMessage();
                }
            }
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Management System - Installation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }
        .install-container {
            max-width: 600px;
            margin: 0 auto;
        }
        .install-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .install-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .install-body {
            padding: 30px;
        }
        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
        }
        .step {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 10px;
            font-weight: bold;
        }
        .step.active {
            background: #667eea;
            color: white;
        }
        .step.completed {
            background: #28a745;
            color: white;
        }
    </style>
</head>
<body>
    <div class="install-container">
        <div class="install-card">
            <div class="install-header">
                <h1><i class="fas fa-utensils me-2"></i>Restaurant Management System</h1>
                <p class="mb-0">Installation Wizard</p>
            </div>
            
            <div class="install-body">
                <!-- Step Indicator -->
                <div class="step-indicator">
                    <div class="step <?php echo $step >= 1 ? 'completed' : ''; ?>">1</div>
                    <div class="step <?php echo $step == 2 ? 'active' : ($step > 2 ? 'completed' : ''); ?>">2</div>
                    <div class="step <?php echo $step == 3 ? 'active' : ($step > 3 ? 'completed' : ''); ?>">3</div>
                    <div class="step <?php echo $step == 4 ? 'active' : ($step > 4 ? 'completed' : ''); ?>">4</div>
                    <div class="step <?php echo $step == 5 ? 'active' : ''; ?>">5</div>
                </div>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success"><i class="fas fa-check me-2"></i><?php echo $success; ?></div>
                <?php endif; ?>
                
                <?php switch ($step): case 1: ?>
                    <!-- Welcome Step -->
                    <div class="text-center">
                        <h3>Welcome to Restaurant Management System</h3>
                        <p class="text-muted mb-4">This wizard will help you set up your restaurant management system.</p>
                        
                        <h5>System Requirements:</h5>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success me-2"></i>PHP 8.0 or higher</li>
                            <li><i class="fas fa-check text-success me-2"></i>MySQL 8.0 or higher</li>
                            <li><i class="fas fa-check text-success me-2"></i>Apache/Nginx web server</li>
                            <li><i class="fas fa-check text-success me-2"></i>PDO MySQL extension</li>
                        </ul>
                        
                        <a href="?step=2" class="btn btn-primary btn-lg">
                            <i class="fas fa-arrow-right me-2"></i>Start Installation
                        </a>
                    </div>
                    <?php break; case 2: ?>
                    
                    <!-- Database Configuration -->
                    <h3>Database Configuration</h3>
                    <p class="text-muted mb-4">Enter your database connection details.</p>
                    
                    <form method="POST">
                        <div class="mb-3">
                            <label for="db_host" class="form-label">Database Host</label>
                            <input type="text" class="form-control" id="db_host" name="db_host" value="localhost" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="db_name" class="form-label">Database Name</label>
                            <input type="text" class="form-control" id="db_name" name="db_name" value="restaurant_management" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="db_user" class="form-label">Database Username</label>
                            <input type="text" class="form-control" id="db_user" name="db_user" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="db_pass" class="form-label">Database Password</label>
                            <input type="password" class="form-control" id="db_pass" name="db_pass">
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-database me-2"></i>Test Connection
                        </button>
                    </form>
                    <?php break; case 3: ?>
                    
                    <!-- Database Import -->
                    <h3>Database Setup</h3>
                    <p class="text-muted mb-4">Import the database schema and default data.</p>
                    
                    <form method="POST">
                        <p>Click the button below to import the database schema:</p>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-download me-2"></i>Import Database
                        </button>
                    </form>
                    <?php break; case 4: ?>
                    
                    <!-- Admin User -->
                    <h3>Create Admin User</h3>
                    <p class="text-muted mb-4">Set up your administrator account.</p>
                    
                    <form method="POST">
                        <div class="mb-3">
                            <label for="admin_username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="admin_username" name="admin_username" value="admin" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="admin_email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="admin_email" name="admin_email" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="admin_password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="admin_password" name="admin_password" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-user-plus me-2"></i>Create Admin User
                        </button>
                    </form>
                    <?php break; case 5: ?>
                    
                    <!-- Installation Complete -->
                    <div class="text-center">
                        <div class="mb-4">
                            <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                        </div>
                        <h3>Installation Complete!</h3>
                        <p class="text-muted mb-4">Your restaurant management system has been successfully installed.</p>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <h5>Admin Panel</h5>
                                        <p>Manage your restaurant</p>
                                        <a href="../admin/" class="btn btn-primary">
                                            <i class="fas fa-cog me-2"></i>Go to Admin
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <h5>Public Website</h5>
                                        <p>Customer ordering site</p>
                                        <a href="../public/" class="btn btn-success">
                                            <i class="fas fa-globe me-2"></i>View Website
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-warning mt-4">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Security Notice:</strong> Please delete the <code>install</code> directory for security reasons.
                        </div>
                    </div>
                    <?php break; endswitch; ?>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>