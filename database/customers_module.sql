-- Customers module schema
CREATE TABLE IF NOT EXISTS customers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(190) UNIQUE,
  password_hash VARCHAR(255),
  first_name VARCHAR(100),
  last_name VARCHAR(100),
  phone VARCHAR(30) UNIQUE,
  date_of_birth DATE NULL,
  status ENUM('pending_profile','active','disabled') DEFAULT 'pending_profile',
  primary_auth_method ENUM('email','google','apple','facebook') DEFAULT 'email',
  meta JSON NULL,
  last_login_at DATETIME NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS customers_auth_providers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  customer_id INT NOT NULL,
  provider ENUM('google','apple','facebook') NOT NULL,
  provider_user_id VARCHAR(191) NOT NULL,
  email_at_provider VARCHAR(190),
  access_token_hash VARCHAR(255),
  refresh_token_hash VARCHAR(255),
  token_expires_at DATETIME NULL,
  last_used_at DATETIME NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_provider_user (provider, provider_user_id),
  KEY idx_customer (customer_id),
  CONSTRAINT fk_cap_customer FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS customer_addresses (
  id INT AUTO_INCREMENT PRIMARY KEY,
  customer_id INT NOT NULL,
  label VARCHAR(50),
  line1 VARCHAR(150) NOT NULL,
  line2 VARCHAR(150),
  bell_name VARCHAR(100),
  phone_alt VARCHAR(30),
  city_id INT,
  state_id INT,
  postal_code VARCHAR(20),
  delivery_area_id INT,
  is_default TINYINT(1) DEFAULT 0,
  extra JSON NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY idx_customer (customer_id),
  KEY idx_delivery_area (delivery_area_id),
  CONSTRAINT fk_ca_customer FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS address_field_rules (
  id INT AUTO_INCREMENT PRIMARY KEY,
  state_id INT NULL,
  delivery_area_id INT NULL,
  field_key VARCHAR(50) NOT NULL,
  requirement ENUM('required','optional','hidden') NOT NULL DEFAULT 'optional',
  label VARCHAR(100),
  sort_order INT DEFAULT 0,
  active TINYINT(1) DEFAULT 1,
  UNIQUE KEY uq_rule (state_id, delivery_area_id, field_key),
  KEY idx_state (state_id),
  KEY idx_delivery_area (delivery_area_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
