-- Migration: Create page_translations table for multi-language page content
CREATE TABLE IF NOT EXISTS `page_translations` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `page_id` INT NOT NULL,
  `language_code` VARCHAR(10) NOT NULL,
  `title` VARCHAR(200) NOT NULL,
  `slug` VARCHAR(200) DEFAULT NULL,
  `content` TEXT DEFAULT NULL,
  `meta_title` VARCHAR(200) DEFAULT NULL,
  `meta_description` TEXT DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_page_language` (`page_id`,`language_code`),
  KEY `idx_language_code` (`language_code`),
  CONSTRAINT `fk_page_translations_page` FOREIGN KEY (`page_id`) REFERENCES `pages`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
