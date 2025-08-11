<?php
class InventoryTransfersController {
    public function __construct() {}
    public function index(){ header('Location: '.get_setting('site_url').'/admin/warehouse/transfers'); exit; }
    public function create(){ header('Location: '.get_setting('site_url').'/admin/warehouse/transfers/create'); exit; }
    public function store(){ header('Location: '.get_setting('site_url').'/admin/warehouse/transfers'); exit; }
}
