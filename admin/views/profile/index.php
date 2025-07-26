<?php
$pageTitle = $data['pageTitle'];
include get_setting('base_path', '/var/www/html') . 'admin/layouts/header.php';

?>

<div class="page-content-wrapper">
    <!-- start page content-->
    <div class="page-content">
        <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
            <div class="breadcrumb-title pe-3">User</div>
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="index.php"><i class="bx bx-home-alt"></i></a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Profile</li>
                    </ol>
                </nav>
            </div>
        </div>

        <?php if (!empty($data['errors'])): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($data['errors'] as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (!empty($data['success'])): ?>
            <div class="alert alert-success">
                <ul class="mb-0">
                    <?php foreach ($data['success'] as $message): ?>
                        <li><?php echo htmlspecialchars($message); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-12 col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex flex-column align-items-center text-center py-3">
                            <div class="position-relative mb-3">
                                <img src="<?php echo $siteUrl; ?>/admin/assets/images/avatars/<?php echo htmlspecialchars($data['preferences']['avatar'] ?? 'default.png'); ?>" 
                                     alt="User Avatar" class="rounded-circle p-1 bg-primary" width="110" height="110"
                                     onerror="this.src='<?php echo $siteUrl; ?>/admin/assets/images/avatars/default.png'">
                            </div>
                            <div class="mt-3">
                                <h4><?php echo htmlspecialchars($data['user']['username'] ?? ''); ?></h4>
                                <p class="text-secondary mb-1"><?php echo htmlspecialchars($data['user']['role_name'] ?? 'User'); ?></p>
                            </div>
                        </div>
                        
                        <form action="profile.php" method="post" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="avatar" class="form-label">Update Profile Picture</label>
                                <input class="form-control" type="file" id="avatar" name="avatar" accept="image/*">
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" name="update_avatar" class="btn btn-primary">
                                    Upload Avatar
                                </button>
                                <button type="submit" name="delete_avatar" class="btn btn-outline-danger">
                                    Remove Avatar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-12 col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <ul class="nav nav-tabs nav-primary mb-4" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" data-bs-toggle="tab" href="#username" role="tab" aria-selected="true">
                                    <div class="d-flex align-items-center">
                                        <div class="tab-icon"><i class="material-icons-outlined me-1">person</i></div>
                                        <div class="tab-title">Username</div>
                                    </div>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" data-bs-toggle="tab" href="#password" role="tab" aria-selected="false">
                                    <div class="d-flex align-items-center">
                                        <div class="tab-icon"><i class="material-icons-outlined me-1">lock</i></div>
                                        <div class="tab-title">Password</div>
                                    </div>
                                </a>
                            </li>
                        </ul>
                        
                        <div class="tab-content py-3">
                            <!-- Username Tab -->
                            <div class="tab-pane fade show active" id="username" role="tabpanel">
                                <form action="profile.php" method="post">
                                    <div class="mb-3">
                                        <label for="username" class="form-label">Username</label>
                                        <input type="text" class="form-control" id="username" name="username" 
                                               value="<?php echo htmlspecialchars($data['user']['username'] ?? ''); ?>" required>
                                    </div>
                                    <div class="text-end">
                                        <button type="submit" name="update_username" class="btn btn-primary">
                                            Update Username
                                        </button>
                                    </div>
                                </form>
                            </div>
                            
                            <!-- Password Tab -->
                            <div class="tab-pane fade" id="password" role="tabpanel">
                                <form action="profile.php" method="post">
                                    <div class="mb-3">
                                        <label for="current_password" class="form-label">Current Password</label>
                                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="new_password" class="form-label">New Password</label>
                                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                    </div>
                                    <div class="text-end">
                                        <button type="submit" name="update_password" class="btn btn-primary">
                                            Update Password
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end page content-->
</div>
<?php include get_setting('base_path', '/var/www/html') . 'admin/layouts/footer.php'; ?>