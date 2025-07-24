<?php
require_once __DIR__ . '/../models/DeliveryArea.php';
require_once __DIR__ . '/../includes/functions.php';

class DeliveryAreaController {
    private $deliveryArea;
    
    public function __construct($pdo) {
        $this->deliveryArea = new DeliveryArea($pdo);
    }
    
    public function index() {
        // Check permissions
        if (!hasPermission('view_delivery_areas')) {
            header('Location: /admin/dashboard.php?error=' . urlencode(translate('no_permission')));
            exit;
        }
        
        $deliveryAreas = $this->deliveryArea->getAllDeliveryAreas();
        $branches = $this->deliveryArea->getAllBranches();
        
        return [
            'deliveryAreas' => $deliveryAreas,
            'branches' => $branches
        ];
    }
    
    public function create() {
        // Check permissions
        if (!hasPermission('create_delivery_areas')) {
            header('Location: /admin/delivery-areas.php?error=' . urlencode(translate('no_permission')));
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $area_name = trim($_POST['area_name'] ?? '');
            $shop_id = intval($_POST['shop_id'] ?? 0);
            
            if (empty($area_name)) {
                return ['error' => translate('area_name_required')];
            }
            
            if ($shop_id <= 0) {
                return ['error' => translate('branch_required')];
            }
            
            if ($this->deliveryArea->createDeliveryArea($area_name, $shop_id)) {
                header('Location: /admin/delivery-areas.php?success=' . urlencode(translate('delivery_area_created')));
                exit;
            } else {
                return ['error' => translate('error_creating_delivery_area')];
            }
        }
        
        $branches = $this->deliveryArea->getAllBranches();
        return ['branches' => $branches];
    }
    
    public function edit($id) {
        // Check permissions
        if (!hasPermission('edit_delivery_areas')) {
            header('Location: /admin/delivery-areas.php?error=' . urlencode(translate('no_permission')));
            exit;
        }
        
        $deliveryArea = $this->deliveryArea->getDeliveryAreaById($id);
        
        if (!$deliveryArea) {
            header('Location: /admin/delivery-areas.php?error=' . urlencode(translate('delivery_area_not_found')));
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $area_name = trim($_POST['area_name'] ?? '');
            $shop_id = intval($_POST['shop_id'] ?? 0);
            
            if (empty($area_name)) {
                return [
                    'deliveryArea' => $deliveryArea,
                    'branches' => $this->deliveryArea->getAllBranches(),
                    'error' => translate('area_name_required')
                ];
            }
            
            if ($shop_id <= 0) {
                return [
                    'deliveryArea' => $deliveryArea,
                    'branches' => $this->deliveryArea->getAllBranches(),
                    'error' => translate('branch_required')
                ];
            }
            
            if ($this->deliveryArea->updateDeliveryArea($id, $area_name, $shop_id)) {
                header('Location: /admin/delivery-areas.php?success=' . urlencode(translate('delivery_area_updated')));
                exit;
            } else {
                return [
                    'deliveryArea' => $deliveryArea,
                    'branches' => $this->deliveryArea->getAllBranches(),
                    'error' => translate('error_updating_delivery_area')
                ];
            }
        }
        
        $branches = $this->deliveryArea->getAllBranches();
        return [
            'deliveryArea' => $deliveryArea,
            'branches' => $branches
        ];
    }
    
    public function delete($id) {
        // Check permissions
        if (!hasPermission('delete_delivery_areas')) {
            header('Location: /admin/delivery-areas.php?error=' . urlencode(translate('no_permission')));
            exit;
        }
        
        if ($this->deliveryArea->deleteDeliveryArea($id)) {
            header('Location: /admin/delivery-areas.php?success=' . urlencode(translate('delivery_area_deleted')));
        } else {
            header('Location: /admin/delivery-areas.php?error=' . urlencode(translate('error_deleting_delivery_area')));
        }
        exit;
    }
}
?>