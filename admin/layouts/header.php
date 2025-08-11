<?php
// Define site URL
// Get site URL from settings
require_once __DIR__ . '/../../admin/includes/functions.php';
$siteUrl = get_setting('site_url', 'http://localhost');

// Load user preferences
require_once admin_module_path('/models/UserPreferences.php', 'access-management');

if (isset($_SESSION['user_id'])) {
  $userPrefsModel = new UserPreferences();
  $userPrefs = $userPrefsModel->getUserPreferences($_SESSION['user_id']);

  // Set session variables from database
  $_SESSION['theme'] = $userPrefs['theme'];
  $_SESSION['language'] = $userPrefs['language'];
}

$currentTheme = $_SESSION['theme'] ?? 'light';
$currentLanguage = $_SESSION['language'] ?? 'en';

$currentPage = basename($_SERVER['PHP_SELF']);
$langCode = $currentLanguage;
?>
<!doctype html>
<html lang="<?php echo $langCode; ?>" data-bs-theme="<?php echo $currentTheme; ?>">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- FontAwesome CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css?family=Material+Icons+Outlined" rel="stylesheet">

  <!-- Local CSS files -->
  <link href="<?php echo $siteUrl; ?>/admin/assets/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?php echo $siteUrl; ?>/admin/assets/css/bootstrap-extended.css" rel="stylesheet">
  <link href="<?php echo $siteUrl; ?>/admin/assets/css/extra-icons.css" rel="stylesheet">
  <link href="<?php echo $siteUrl; ?>/admin/assets/sass/main.css" rel="stylesheet">
  <link href="<?php echo $siteUrl; ?>/admin/assets/sass/dark-theme.css" rel="stylesheet">
  <link href="<?php echo $siteUrl; ?>/admin/assets/sass/semi-dark.css" rel="stylesheet">
  <link href="<?php echo $siteUrl; ?>/admin/assets/sass/bordered-theme.css" rel="stylesheet">
  <link href="<?php echo $siteUrl; ?>/admin/assets/sass/responsive.css" rel="stylesheet">

  <!-- Plugins -->
  <link href="<?php echo $siteUrl; ?>/admin/assets/plugins/simplebar/css/simplebar.css" rel="stylesheet">
  <link href="<?php echo $siteUrl; ?>/admin/assets/plugins/metismenu/metisMenu.min.css" rel="stylesheet">
  <link href="<?php echo $siteUrl; ?>/admin/assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css" rel="stylesheet">

  <link href="<?php echo $siteUrl; ?>/admin/assets/plugins/datatable/css/dataTables.bootstrap5.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

  <title><?php echo TranslationManager::t('Restaurant Management System'); ?></title>
<?php
// Build frontend permission map for current user
if(isset($_SESSION['user_id'])){
  $uid = $_SESSION['user_id'];
  $permMap = load_user_permissions($uid); // uses cache
  echo "<script>window.APP_PERMISSIONS = ".json_encode($permMap).";</script>";
  echo "<script src='{$siteUrl}/admin/assets/js/permissions.js'></script>";
}
?>

  <style>
    .dropdown-menu {
      position: absolute;
      top: 100%;
      left: 0;
      z-index: 1000;
    }

    .dropdown-menu[data-bs-popper] {
      top: 100%;
      left: auto;
      right: 0;
      margin-top: 0.125rem;
    }

    .user-img {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      overflow: hidden;
      background-color: #f8f9fa;
    }

    .user-img img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
  </style>
</head>

<body>
  <!--start header-->
  <header class="top-header">
    <nav class="navbar navbar-expand align-items-center justify-content-between">
      <div class="btn-toggle">
        <a href="javascript:;"><i class="material-icons-outlined">menu</i></a>
      </div>

      <div class="d-flex ms-auto align-items-center gap-3">
        <ul class="navbar-nav gap-1 nav-right-links align-items-center">
          <!-- Primo UL (notifiche e avatar piccolo) -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle dropdown-toggle-nocaret position-relative" data-bs-auto-close="outside"
              data-bs-toggle="dropdown" href="javascript:;"><i class="material-icons-outlined">notifications</i>
              <span class="badge-notify">5</span>
            </a>
            <div class="dropdown-menu dropdown-notify dropdown-menu-end shadow">
              <div class="px-3 py-1 d-flex align-items-center justify-content-between border-bottom">
                <h5 class="notiy-title mb-0">Notifications</h5>
                <div class="dropdown">
                  <button class="btn btn-secondary dropdown-toggle dropdown-toggle-nocaret option" type="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="material-icons-outlined">
                      more_vert
                    </span>
                  </button>
                  <div class="dropdown-menu dropdown-option dropdown-menu-end shadow">
                    <div><a class="dropdown-item d-flex align-items-center gap-2 py-2" href="javascript:;"><i
                          class="material-icons-outlined fs-6">inventory_2</i>Archive All</a></div>
                    <div><a class="dropdown-item d-flex align-items-center gap-2 py-2" href="javascript:;"><i
                          class="material-icons-outlined fs-6">done_all</i>Mark all as read</a></div>
                    <div><a class="dropdown-item d-flex align-items-center gap-2 py-2" href="javascript:;"><i
                          class="material-icons-outlined fs-6">mic_off</i>Disable Notifications</a></div>
                    <div><a class="dropdown-item d-flex align-items-center gap-2 py-2" href="javascript:;"><i
                          class="material-icons-outlined fs-6">grade</i>What's new ?</a></div>
                    <div>
                      <hr class="dropdown-divider">
                    </div>
                    <div><a class="dropdown-item d-flex align-items-center gap-2 py-2" href="javascript:;"><i
                          class="material-icons-outlined fs-6">leaderboard</i>Reports</a></div>
                  </div>
                </div>
              </div>
              <div class="notify-list">
                <div>
                  <a class="dropdown-item border-bottom py-2" href="javascript:;">
                    <div class="d-flex align-items-center gap-3">
                      <div class="">
                        <img src="<?php echo $siteUrl; ?>/admin/assets/images/avatars/default.png" class="rounded-circle" width="45" height="45" alt="">
                      </div>
                      <div class="">
                        <h5 class="notify-title">Congratulations Jhon</h5>
                        <p class="mb-0 notify-desc">Many congtars jhon. You have won the gifts.</p>
                        <p class="mb-0 notify-time">Today</p>
                      </div>
                      <div class="notify-close position-absolute end-0 me-3">
                        <i class="material-icons-outlined fs-6">close</i>
                      </div>
                    </div>
                  </a>
                </div>
                <div>
                  <a class="dropdown-item border-bottom py-2" href="javascript:;">
                    <div class="d-flex align-items-center gap-3">
                      <div class="user-wrapper bg-primary text-primary bg-opacity-10">
                        <span>RS</span>
                      </div>
                      <div class="">
                        <h5 class="notify-title">New Account Created</h5>
                        <p class="mb-0 notify-desc">From USA an user has registered.</p>
                        <p class="mb-0 notify-time">Yesterday</p>
                      </div>
                      <div class="notify-close position-absolute end-0 me-3">
                        <i class="material-icons-outlined fs-6">close</i>
                      </div>
                    </div>
                  </a>
                </div>
                <div>
                  <a class="dropdown-item border-bottom py-2" href="javascript:;">
                    <div class="d-flex align-items-center gap-3">
                      <div class="">
                      </div>
                      <div class="">
                        <h5 class="notify-title">Payment Recived</h5>
                        <p class="mb-0 notify-desc">New payment recived successfully</p>
                        <p class="mb-0 notify-time">1d ago</p>
                      </div>
                      <div class="notify-close position-absolute end-0 me-3">
                        <i class="material-icons-outlined fs-6">close</i>
                      </div>
                    </div>
                  </a>
                </div>
                <div>
                  <a class="dropdown-item border-bottom py-2" href="javascript:;">
                    <div class="d-flex align-items-center gap-3">
                      <div class="">
                      </div>
                      <div class="">
                        <h5 class="notify-title">New Order Recived</h5>
                        <p class="mb-0 notify-desc">Recived new order from michle</p>
                        <p class="mb-0 notify-time">2:15 AM</p>
                      </div>
                      <div class="notify-close position-absolute end-0 me-3">
                        <i class="material-icons-outlined fs-6">close</i>
                      </div>
                    </div>
                  </a>
                </div>
                <div>
                  <a class="dropdown-item border-bottom py-2" href="javascript:;">
                    <div class="d-flex align-items-center gap-3">
                      <div class="">
                      </div>
                      <div class="">
                        <h5 class="notify-title">Congratulations Jhon</h5>
                        <p class="mb-0 notify-desc">Many congtars jhon. You have won the gifts.</p>
                        <p class="mb-0 notify-time">Today</p>
                      </div>
                      <div class="notify-close position-absolute end-0 me-3">
                        <i class="material-icons-outlined fs-6">close</i>
                      </div>
                    </div>
                  </a>
                </div>
                <div>
                  <a class="dropdown-item py-2" href="javascript:;">
                    <div class="d-flex align-items-center gap-3">
                      <div class="user-wrapper bg-danger text-danger bg-opacity-10">
                        <span>PK</span>
                      </div>
                      <div class="">
                        <h5 class="notify-title">New Account Created</h5>
                        <p class="mb-0 notify-desc">From USA an user has registered.</p>
                        <p class="mb-0 notify-time">Yesterday</p>
                      </div>
                      <div class="notify-close position-absolute end-0 me-3">
                        <i class="material-icons-outlined fs-6">close</i>
                      </div>
                    </div>
                  </a>
                </div>
              </div>
            </div>
          </li>
        </ul>

        <ul class="navbar-nav">
          <!-- Secondo UL (User Profile grande) -->
          <li class="nav-item dropdown dropdown-large">
            <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown">
              <div class="user-img">
                <?php
                $userAvatar = isset($userPrefs['avatar']) ? $userPrefs['avatar'] : 'default.png';
                $avatarPath = $siteUrl . '/admin/assets/images/avatars/' . $userAvatar;
                ?>
                <img src="<?php echo $avatarPath; ?>" alt="User" onerror="this.src='<?php echo $siteUrl; ?>/admin/assets/images/avatars/default.png'">
              </div>
            </a>
            <div class="dropdown-menu dropdown-menu-end">
              <div class="user-details p-3">
                <div class="d-flex mb-2">
                  <div class="user-img">
                    <img src="<?php echo $avatarPath; ?>" alt="User" onerror="this.src='<?php echo $siteUrl; ?>/admin/assets/images/avatars/default.png'">
                  </div>
                  <div class="ms-2">
                    <h6 class="mb-0">
                      <?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin'; ?>
                    </h6>
                    <p class="mb-0"><?php echo isset($_SESSION['email']) ? $_SESSION['email'] : 'admin@example.com'; ?></p>
                  </div>
                </div>
              </div>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="<?php echo $siteUrl; ?>/admin/access-management/profile">
                <div class="d-flex align-items-center">
                  <div>
                    <ion-icon name="person-outline"></ion-icon>
                  </div>
                  <div class="ms-3"><span>
                      <?php echo TranslationManager::t('Profile'); ?>
                    </span></div>
                </div>
              </a>
              <a class="dropdown-item" href="<?php echo $siteUrl; ?>/admin/settings.php">
                <div class="d-flex align-items-center">
                  <div>
                    <ion-icon name="settings-outline"></ion-icon>
                  </div>
                  <div class="ms-3"><span>
                      <?php echo TranslationManager::t('Settings'); ?>
                    </span></div>
                </div>
              </a>
              <a class="dropdown-item" href="<?php echo $siteUrl; ?>/admin/logout.php">
                <div class="d-flex align-items-center">
                  <div>
                    <ion-icon name="log-out-outline"></ion-icon>
                  </div>
                  <div class="ms-3"><span>
                      <?php echo TranslationManager::t('Logout'); ?>
                    </span></div>
                </div>
              </a>
            </div>
          </li>
        </ul>
      </div>
    </nav>
  </header>
  <!-- Dynamic Sidebar -->
  <?php include __DIR__ . '/sidebar.php'; ?>

  <script>
    // Theme toggle function
    function toggleTheme() {
      const html = document.documentElement;
      const currentTheme = html.getAttribute('data-bs-theme') || 'light';
      const newTheme = currentTheme === 'light' ? 'dark' : 'light';

      // Update HTML attribute immediately
      html.setAttribute('data-bs-theme', newTheme);

      // Update icon
      const themeIcon = document.getElementById('theme-icon');
      if (themeIcon) {
        themeIcon.textContent = newTheme === 'light' ? 'light_mode' : 'dark_mode';
      }

      // Save to database
      fetch('<?php echo get_setting('site_url', 'http://localhost.com')?>/admin/access-management/preferences', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: 'action=update_theme&theme=' + newTheme
        })
        .then(response => response.json())
        .then(data => {
          if (!data.success) {
            console.error('Failed to save theme:', data.message);
          } else {
            window.location.reload(); // Reload to apply changes
          }
        })
        .catch(error => {
          console.error('Error saving theme:', error);
        });
    }

    // Language selector
    document.addEventListener('DOMContentLoaded', function() {
      // Initialize theme icon
      const currentTheme = document.documentElement.getAttribute('data-bs-theme') || 'light';
      const themeIcon = document.getElementById('theme-icon');
      if (themeIcon) {
        themeIcon.textContent = currentTheme === 'light' ? 'light_mode' : 'dark_mode';
      }

      // Language selectors
      const languageSelectors = document.querySelectorAll('.language-selector');
      languageSelectors.forEach(selector => {
        selector.addEventListener('click', function(e) {
          e.preventDefault();
          const language = this.getAttribute('data-lang');

          // Save to database and reload page
          fetch('<?php echo get_setting('site_url', 'http://localhost.com')?>/admin/access-management/preferences', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
              },
              body: 'action=update_language&language=' + language
            })
            .then(response => response.json())
            .then(data => {
              if (data.success) {
                // Reload page to apply language changes
                window.location.reload();
              } else {
                console.error('Failed to save language:', data.message);
              }
            })
            .catch(error => {
              console.error('Error saving language:', error);
            });
        });
      });
    });
  </script>