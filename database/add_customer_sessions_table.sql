-- Customer sessions for token-based auth
CREATE TABLE IF NOT EXISTS customer_sessions (
  customer_id INT NOT NULL,
  token_hash CHAR(64) NOT NULL,
  created_at DATETIME NOT NULL,
  last_used_at DATETIME NOT NULL,
  expires_at DATETIME NOT NULL,
  PRIMARY KEY (token_hash),
  KEY customer_id (customer_id),
  CONSTRAINT fk_customer_sessions_customer FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
