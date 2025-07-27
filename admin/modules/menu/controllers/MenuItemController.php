<?php
require_once __DIR__ . '/../models/MenuItem.php';
require_once __DIR__ . '/../includes/functions.php';

class MenuItemController {
    private $menuItem;
    
    public function __construct($pdo) {
        $this->menuItem = new MenuItem($pdo);
    }
    
    public function index() {
        // Check permissions
        if (!has_permission($_SESSION['user_id'], 'menu_items', 'view')) {
            header('Location: /admin/dashboard.php?error=' . urlencode(translate('no_permission')));
            exit;
        }
        
        $menuItems = $this->menuItem->getAllMenuItems();
        
        return [
            'menuItems' => $menuItems
        ];
    }
    
    public function create() {
        // Check permissions
        if (!has_permission($_SESSION['user_id'], 'menu_items', 'create')) {
            header('Location: /admin/menu-items.php?error=' . urlencode(translate('no_permission')));
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $url = trim($_POST['url'] ?? '');
            $page_id = !empty($_POST['page_id']) ? intval($_POST['page_id']) : null;
            $article_id = !empty($_POST['article_id']) ? intval($_POST['article_id']) : null;
            $parent_id = !empty($_POST['parent_id']) ? intval($_POST['parent_id']) : null;
            $sort_order = intval($_POST['sort_order'] ?? 0);
            $target = $_POST['target'] ?? '_self';
            $css_class = trim($_POST['css_class'] ?? '');
            $icon = trim($_POST['icon'] ?? '');
            $status = $_POST['status'] ?? 'active';
            
            if (empty($title)) {
                return [
                    'pages' => $this->menuItem->getPages(),
                    'articles' => $this->menuItem->getArticles(),
                    'parentMenuItems' => $this->menuItem->getParentMenuItems(),
                    'error' => translate('title_required')
                ];
            }
            
            $data = [
                'title' => $title,
                'url' => $url,
                'page_id' => $page_id,
                'article_id' => $article_id,
                'parent_id' => $parent_id,
                'sort_order' => $sort_order,
                'target' => $target,
                'css_class' => $css_class,
                'icon' => $icon,
                'status' => $status
            ];
            
            if ($this->menuItem->createMenuItem($data)) {
                header('Location: /admin/menu-items.php?success=' . urlencode(translate('menu_item_created')));
                exit;
            } else {
                return [
                    'pages' => $this->menuItem->getPages(),
                    'articles' => $this->menuItem->getArticles(),
                    'parentMenuItems' => $this->menuItem->getParentMenuItems(),
                    'error' => translate('error_occurred')
                ];
            }
        }
        
        return [
            'pages' => $this->menuItem->getPages(),
            'articles' => $this->menuItem->getArticles(),
            'parentMenuItems' => $this->menuItem->getParentMenuItems()
        ];
    }
    
    public function edit($id) {
        // Check permissions
        if (!has_permission($_SESSION['user_id'], 'menu_items', 'edit')) {
            header('Location: /admin/menu-items.php?error=' . urlencode(translate('no_permission')));
            exit;
        }
        
        $menuItem = $this->menuItem->getMenuItemById($id);
        
        if (!$menuItem) {
            header('Location: /admin/menu-items.php?error=' . urlencode(translate('menu_item_not_found')));
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $url = trim($_POST['url'] ?? '');
            $page_id = !empty($_POST['page_id']) ? intval($_POST['page_id']) : null;
            $article_id = !empty($_POST['article_id']) ? intval($_POST['article_id']) : null;
            $parent_id = !empty($_POST['parent_id']) ? intval($_POST['parent_id']) : null;
            $sort_order = intval($_POST['sort_order'] ?? 0);
            $target = $_POST['target'] ?? '_self';
            $css_class = trim($_POST['css_class'] ?? '');
            $icon = trim($_POST['icon'] ?? '');
            $status = $_POST['status'] ?? 'active';
            
            if (empty($title)) {
                return [
                    'menuItem' => $menuItem,
                    'pages' => $this->menuItem->getPages(),
                    'articles' => $this->menuItem->getArticles(),
                    'parentMenuItems' => $this->menuItem->getParentMenuItems(),
                    'error' => translate('title_required')
                ];
            }
            
            $data = [
                'title' => $title,
                'url' => $url,
                'page_id' => $page_id,
                'article_id' => $article_id,
                'parent_id' => $parent_id,
                'sort_order' => $sort_order,
                'target' => $target,
                'css_class' => $css_class,
                'icon' => $icon,
                'status' => $status
            ];
            
            if ($this->menuItem->updateMenuItem($id, $data)) {
                header('Location: /admin/menu-items.php?success=' . urlencode(translate('menu_item_updated')));
                exit;
            } else {
                return [
                    'menuItem' => $menuItem,
                    'pages' => $this->menuItem->getPages(),
                    'articles' => $this->menuItem->getArticles(),
                    'parentMenuItems' => $this->menuItem->getParentMenuItems(),
                    'error' => translate('error_occurred')
                ];
            }
        }
        
        return [
            'menuItem' => $menuItem,
            'pages' => $this->menuItem->getPages(),
            'articles' => $this->menuItem->getArticles(),
            'parentMenuItems' => $this->menuItem->getParentMenuItems()
        ];
    }
    
    public function delete($id) {
        // Check permissions
        if (!has_permission($_SESSION['user_id'], 'menu_items', 'delete')) {
            header('Location: /admin/menu-items.php?error=' . urlencode(translate('no_permission')));
            exit;
        }
        
        if ($this->menuItem->deleteMenuItem($id)) {
            header('Location: /admin/menu-items.php?success=' . urlencode(translate('menu_item_deleted')));
        } else {
            header('Location: /admin/menu-items.php?error=' . urlencode(translate('error_occurred')));
        }
        exit;
    }
}
?>