CREATE TABLE IF NOT EXISTS "settings" (
  "key" TEXT PRIMARY KEY,
  "value" TEXT NOT NULL,
  "updated_at" DATETIME DEFAULT CURRENT_TIMESTAMP
);

INSERT OR IGNORE INTO "settings" ("key", "value") VALUES
('business_name', 'FarmaLite'),
('nit', '901234567-8'),
('address', 'Dirección no configurada'),
('phone', '0000000000'),
('email', 'correo@farmalite.local'),
('logo', 'logo.png'),
('currency', 'COP'),
('printer_width', '80'),
('backup_days', '7'),
('low_stock_threshold', '5'),
('schema_version', '1'),
('installation_id', 'FL-0001');

CREATE TABLE IF NOT EXISTS "system_log" (
  "id" INTEGER PRIMARY KEY AUTOINCREMENT,
  "date" DATETIME DEFAULT CURRENT_TIMESTAMP,
  "user" TEXT NOT NULL,
  "module" TEXT NOT NULL,
  "action" TEXT NOT NULL,
  "details" TEXT
);
