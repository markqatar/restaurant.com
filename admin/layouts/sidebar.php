<?php
// Dynamic sidebar that uses database menu items
require_once get_setting('base_path', '/var/www/html') . 'admin/modules/system/models/AdminMenu.php';
require_once admin_module_path('/models/Permission.php', 'access-management');
// Get user permissions
$userPermissions = [];
if (isset($_SESSION['user_id'])) {
    $permissionModel = new Permission();
    $userPermissions = $permissionModel->getUserPermissions($_SESSION['user_id']);
}

// Get menu items from database
$menuModel = new AdminMenu();
$language = $_SESSION['language'] ?? 'en';
$menuItems = $menuModel->getMenuItems($language, $userPermissions);


function renderMenu($items, $level = 0) {
    foreach ($items as $item) {
        // Skip separators for now (can be implemented later)
        if ($item['is_separator']) {
            echo '<li class="nav-divider"></li>' . "\n";
            continue;
        }
        
        $hasChildren = !empty($item['children']);
        $isActive = isMenuActive($item);
        $activeClass = $isActive ? 'mm-active' : '';
        
        if ($hasChildren) {
            // Parent menu with children
            echo '<li class="' . $activeClass . '">' . "\n";
            echo '  <a class="has-arrow" href="javascript:;">' . "\n";
            echo '    <div class="parent-icon"><i class="' . htmlspecialchars($item['icon']) . '"></i></div>' . "\n";
            echo '    <div class="menu-title">' . htmlspecialchars($item['title']) . '</div>' . "\n";
            echo '  </a>' . "\n";
            echo '  <ul class="mm-collapse">' . "\n";
            
            // Render children
            renderMenu($item['children'], $level + 1);
            
            echo '  </ul>' . "\n";
            echo '</li>' . "\n";
        } else {
            // Single menu item
            $url = $item['url'] ? htmlspecialchars($item['permission_module']) . '/' . htmlspecialchars($item['url']) : 'javascript:;';
            $target = $item['target'] === '_blank' ? ' target="_blank"' : '';
            $cssClass = $item['css_class'] ? ' ' . htmlspecialchars($item['css_class']) : '';
            
            echo '<li class="' . $activeClass . $cssClass . '">' . "\n";
            echo '  <a href="' . get_setting('site_url', 'http://localhost') . '/admin/' .$url . '"' . $target . '>' . "\n";
            
            if ($level === 0) {
                echo '    <div class="parent-icon"><i class="' . htmlspecialchars($item['icon']) . '"></i></div>' . "\n";
                echo '    <div class="menu-title">' . htmlspecialchars($item['title']) . '</div>' . "\n";
            } else {
                echo '    <i class="' . htmlspecialchars($item['icon']) . '"></i>' . htmlspecialchars($item['title']) . "\n";
            }
            
            echo '  </a>' . "\n";
            echo '</li>' . "\n";
        }
    }
}

/**
 * Check if menu item should be active
 */
function isMenuActive($item) {
    $currentPage = basename($_SERVER['PHP_SELF']);
    
    // Direct URL match
    if ($item['url'] && basename($item['url']) === $currentPage) {
        return true;
    }
    
    // Check children for active state
    if (!empty($item['children'])) {
        foreach ($item['children'] as $child) {
            if (isMenuActive($child)) {
                return true;
            }
        }
    }
    
    return false;
}

?>
<aside class="sidebar-wrapper">
    <div class="sidebar-header">
      <div class="logo-icon">
        <?php 
        $logoPath = get_setting('logo_path', 'default.png');
        $logoUrl = !empty($logoPath) ? '/public/assets/images/logo/' . $logoPath : '/public/assets/images/logo/default.png';
        ?>
        <img src="<?php echo $logoUrl; ?>" class="logo-img" alt="Logo">
      </div>
      <div class="logo-name flex-grow-1">
        <h5 class="mb-0">Metoxi</h5>
      </div>
      <div class="sidebar-close">
        <span class="material-icons-outlined">close</span>
      </div>
    </div>
    <div class="sidebar-nav" data-simplebar="init"><div class="simplebar-wrapper" style="margin: 0px;"><div class="simplebar-height-auto-observer-wrapper"><div class="simplebar-height-auto-observer"></div></div><div class="simplebar-mask"><div class="simplebar-offset" style="right: 0px; bottom: 0px;"><div class="simplebar-content-wrapper" style="height: 100%; overflow: hidden scroll;"><div class="simplebar-content mm-active" style="padding: 0px;">
        <!--navigation-->
        <ul class="metismenu mm-show" id="sidenav">
            <?php renderMenu($menuItems); ?>
         </ul>
        <!--end navigation-->

    </div></div></div></div><div class="simplebar-placeholder" style="width: auto; height: 1187px;"></div></div><div class="simplebar-track simplebar-horizontal" style="visibility: hidden;"><div class="simplebar-scrollbar" style="width: 0px; display: none;"></div></div><div class="simplebar-track simplebar-vertical" style="visibility: visible;"><div class="simplebar-scrollbar" style="height: 512px; transform: translate3d(0px, 0px, 0px); display: block;"></div></div></div>
    <div class="sidebar-bottom gap-4">
        <div class="dark-mode">
          <a href="javascript:;" class="footer-icon dark-mode-icon" onclick="toggleTheme()">
            <i class="material-icons-outlined" id="theme-icon">light_mode</i>  
          </a>
        </div>
        <?php
        $currentLang = $_SESSION['language'] ?? 'en';
        $languages = get_active_admin_languages();
        ?>

        <div class="dropdown dropup-center dropup dropdown-language">
          <a class="dropdown-toggle dropdown-toggle-nocaret footer-icon" href="javascript:;" data-bs-toggle="dropdown">
            <?php
            // Trova la lingua corrente dal DB (default English)
            $currentFlag = 'en.svg';
            foreach ($languages as $lang) {
                if ($lang['code'] === $currentLang) {
                    $currentFlag = $lang['code'] . '.svg';
                    break;
                }
            }
            ?>
            <img src="<?php echo get_setting('site_url'); ?>/admin/assets/images/country/<?php echo $currentFlag; ?>" width="22" alt="">
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            <?php foreach ($languages as $lang): ?>
              <li>
                <a class="dropdown-item d-flex align-items-center py-2 language-selector <?php echo $currentLang === $lang['code'] ? 'active' : ''; ?>"
                  href="javascript:;" data-lang="<?php echo $lang['code']; ?>">
                  <img src="<?php echo get_setting('site_url'); ?>/admin/assets/images/country/<?php echo $lang['code']; ?>.svg" width="20" alt="">
                  <span class="ms-2"><?php echo htmlspecialchars($lang['name']); ?></span>
                  <?php if ($currentLang === $lang['code']): ?>
                    <i class="fas fa-check text-success ms-auto"></i>
                  <?php endif; ?>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
        <div class="dropdown dropup-center dropup dropdown-help">
          <a class="footer-icon  dropdown-toggle dropdown-toggle-nocaret option" href="javascript:;" data-bs-toggle="dropdown" aria-expanded="false">
            <span class="material-icons-outlined">
              info
            </span>
          </a>
          <div class="dropdown-menu dropdown-option dropdown-menu-end shadow">
            <div><a class="dropdown-item d-flex align-items-center gap-2 py-2" href="javascript:;"><i class="material-icons-outlined fs-6">inventory_2</i>Archive All</a></div>
            <div><a class="dropdown-item d-flex align-items-center gap-2 py-2" href="javascript:;"><i class="material-icons-outlined fs-6">done_all</i>Mark all as read</a></div>
            <div><a class="dropdown-item d-flex align-items-center gap-2 py-2" href="javascript:;"><i class="material-icons-outlined fs-6">mic_off</i>Disable Notifications</a></div>
            <div><a class="dropdown-item d-flex align-items-center gap-2 py-2" href="javascript:;"><i class="material-icons-outlined fs-6">grade</i>What's new ?</a></div>
            <div>
              <hr class="dropdown-divider">
            </div>
            <div><a class="dropdown-item d-flex align-items-center gap-2 py-2" href="javascript:;"><i class="material-icons-outlined fs-6">leaderboard</i>Reports</a></div>
          </div>
        </div>
    </div>
</aside>
<!--start main wrapper-->
<main class="main-wrapper">
    <div class="main-content">
