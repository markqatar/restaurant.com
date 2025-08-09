-- Migration: Add base unit to products and categories for supplier products
-- Base unit per product (global) and category per supplier product

ALTER TABLE products ADD COLUMN IF NOT EXISTS base_unit_id INT NULL AFTER name;
ALTER TABLE products ADD CONSTRAINT IF NOT EXISTS fk_products_base_unit FOREIGN KEY (base_unit_id) REFERENCES units(id);

CREATE TABLE IF NOT EXISTS supplier_product_categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(50) NOT NULL UNIQUE,
  name_en VARCHAR(100) NOT NULL,
  name_it VARCHAR(100) DEFAULT NULL,
  name_ar VARCHAR(100) DEFAULT NULL,
  is_active TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE supplier_products ADD COLUMN IF NOT EXISTS category_id INT NULL AFTER product_id;
ALTER TABLE supplier_products ADD CONSTRAINT IF NOT EXISTS fk_supplier_products_category FOREIGN KEY (category_id) REFERENCES supplier_product_categories(id);

-- Seed default categories (idempotent)
INSERT IGNORE INTO supplier_product_categories (slug, name_en, name_it, name_ar) VALUES
  ('consumables','Consumables','Consumabili','مستهلكات'),
  ('food','Food','Cibo','طعام'),
  ('raw_materials','Raw Materials','Materie Prime','مواد خام'),
  ('houseware','Houseware','Casalinghi','أدوات منزلية');
