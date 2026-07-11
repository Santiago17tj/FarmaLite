-- farmacia_sqlite.sql - Script limpio para SQLite
-- Generado para la migración de MySQL a SQLite

CREATE TABLE IF NOT EXISTS "brands" (
  "brand_id" INTEGER PRIMARY KEY AUTOINCREMENT,
  "brand_name" TEXT NOT NULL,
  "brand_active" INTEGER NOT NULL DEFAULT 0,
  "brand_status" INTEGER NOT NULL DEFAULT 0
);

INSERT INTO "brands" ("brand_id", "brand_name", "brand_active", "brand_status") VALUES
(1, 'Cipla', 1, 1),
(2, 'Mankind', 1, 1),
(3, 'Sunpharma', 1, 1),
(4, 'MicroLabs', 1, 1),
(5, 'Pfizer', 1, 1);

CREATE TABLE IF NOT EXISTS "categories" (
  "categories_id" INTEGER PRIMARY KEY AUTOINCREMENT,
  "categories_name" TEXT NOT NULL,
  "categories_active" INTEGER NOT NULL DEFAULT 0,
  "categories_status" INTEGER NOT NULL DEFAULT 0
);

INSERT INTO "categories" ("categories_id", "categories_name", "categories_active", "categories_status") VALUES
(1, 'Pastillas', 1, 1),
(2, 'Jarabe', 1, 1),
(3, 'Inyecciones', 1, 1),
(4, 'Paliativos', 1, 1),
(5, 'Vacunas', 1, 1);

CREATE TABLE IF NOT EXISTS "orders" (
  "id" INTEGER PRIMARY KEY AUTOINCREMENT,
  "uno" TEXT NOT NULL,
  "orderDate" TEXT NOT NULL,
  "clientName" TEXT NOT NULL,
  "projectName" TEXT NOT NULL DEFAULT '',
  "clientContact" INTEGER NOT NULL DEFAULT 0,
  "address" TEXT NOT NULL DEFAULT '',
  "subTotal" REAL NOT NULL DEFAULT 0,
  "totalAmount" REAL NOT NULL DEFAULT 0,
  "discount" REAL NOT NULL DEFAULT 0,
  "grandTotalValue" REAL NOT NULL DEFAULT 0,
  "gstn" REAL NOT NULL DEFAULT 0,
  "paid" REAL NOT NULL DEFAULT 0,
  "dueValue" REAL NOT NULL DEFAULT 0,
  "paymentType" INTEGER NOT NULL DEFAULT 0,
  "paymentStatus" INTEGER NOT NULL DEFAULT 0,
  "paymentPlace" INTEGER NOT NULL DEFAULT 0,
  "delete_status" INTEGER NOT NULL DEFAULT 0
);

INSERT INTO "orders" ("id", "uno", "orderDate", "clientName", "projectName", "clientContact", "address", "subTotal", "totalAmount", "discount", "grandTotalValue", "gstn", "paid", "dueValue", "paymentType", "paymentStatus", "paymentPlace", "delete_status") VALUES
(1, 'INV-0001', '2022-02-28', 'Lucho Florez', '', 2147483647, '', 100, 10, 108, 49, 0, 49, 49, 2, 1, 0, 0),
(2, 'INV-0002', '2022-03-24', 'Bernardo Galán', '', 2147483647, '', 300, 354, 354, 354, 18, 354, 354, 1, 1, 1, 0),
(3, 'INV-0003', '2022-04-15', 'Fredy Patricio', '', 2147483647, '', 860, 1015, 10, 1005, 155, 500, 505, 2, 2, 1, 0),
(4, 'INV-0004', '2022-04-15', 'Pedro Cliente', '', 2147483647, '', 60, 71, 0, 71, 11, 50, 21, 5, 2, 1, 0),
(5, 'INV-0005', '2022-05-01', 'Juan Cliente', '', 2147483647, '', 200, 236, 0, 236, 36, 300, -64, 2, 1, 1, 0),
(6, 'INV-0006', '2022-05-01', 'Julio Cliente', '', 2147483647, '', 250, 295, 0, 295, 45, 300, -5, 2, 1, 2, 0),
(7, 'INV-0007', '2022-05-03', 'Pedro García', '', 2147483647, '', 250, 295, 0, 295, 45, 300, -5, 2, 1, 1, 0);

CREATE TABLE IF NOT EXISTS "order_item" (
  "id" INTEGER PRIMARY KEY AUTOINCREMENT,
  "productName" INTEGER NOT NULL,
  "quantity" TEXT NOT NULL,
  "rate" TEXT NOT NULL,
  "total" TEXT NOT NULL,
  "lastid" INTEGER NOT NULL,
  "added_date" TEXT NOT NULL DEFAULT ''
);

INSERT INTO "order_item" ("id", "productName", "quantity", "rate", "total", "lastid", "added_date") VALUES
(5, 2, '1', '100', '100.00', 1, '0000-00-00'),
(7, 1, '2', '30', '60.00', 3, '2022-04-15'),
(8, 2, '4', '150', '600.00', 3, '2022-04-15'),
(9, 3, '1', '200', '200.00', 3, '2022-04-15'),
(10, 1, '2', '30', '60.00', 4, '2022-04-15'),
(13, 2, '2', '150', '300.00', 2, '2022-04-15'),
(14, 3, '1', '200', '200.00', 5, '2022-05-01'),
(15, 5, '1', '250', '250.00', 6, '2022-05-01'),
(16, 5, '1', '250', '250.00', 7, '2022-05-03');

CREATE TABLE IF NOT EXISTS "product" (
  "product_id" INTEGER PRIMARY KEY AUTOINCREMENT,
  "product_name" TEXT NOT NULL,
  "barcode" TEXT DEFAULT NULL UNIQUE,
  "product_image" TEXT NOT NULL DEFAULT '',
  "brand_id" INTEGER NOT NULL DEFAULT 0,
  "categories_id" INTEGER NOT NULL DEFAULT 0,
  "quantity" TEXT NOT NULL DEFAULT '0',
  "purchase_price" REAL NOT NULL DEFAULT 0,
  "rate" TEXT NOT NULL DEFAULT '0',
  "mrp" REAL NOT NULL DEFAULT 0,
  "bno" TEXT NOT NULL DEFAULT '',
  "expdate" TEXT NOT NULL DEFAULT '',
  "added_date" TEXT NOT NULL DEFAULT '',
  "active" INTEGER NOT NULL DEFAULT 0,
  "status" INTEGER NOT NULL DEFAULT 0
);

INSERT INTO "product" ("product_id", "product_name", "barcode", "product_image", "brand_id", "categories_id", "quantity", "purchase_price", "rate", "mrp", "bno", "expdate", "added_date", "active", "status") VALUES
(1, 'Acetaminofen 500', '7700000010001', 'tab.jpg', 1, 1, '50', 20, '30', 40, '307002', '2045-02-28', '2022-02-28', 1, 1),
(2, 'Fronta 23', '7700000010002', 'tab1.jpg', 2, 1, '30', 100, '150', 200, '307003', '2022-02-16', '2022-02-28', 1, 1),
(3, 'Rapazol 120', '7700000010003', 'tab3.jpg', 3, 3, '70', 140, '200', 300, '307004', '2024-03-13', '2022-02-28', 1, 1),
(4, 'Escripvo 450', '7700000010004', 'tab4.jpg', 4, 1, '500', 15, '25', 30, '307005', '2050-05-31', '2022-04-15', 1, 1),
(5, 'Vacuna 123', '7700000010005', 'vacuna pfizer.webp', 5, 5, '2500', 180, '250', 254, '171712', '2031-06-18', '2022-05-01', 1, 1);

CREATE TABLE IF NOT EXISTS "users" (
  "user_id" INTEGER PRIMARY KEY AUTOINCREMENT,
  "username" TEXT NOT NULL,
  "password" TEXT NOT NULL,
  "email" TEXT NOT NULL
);

INSERT INTO "users" ("user_id", "username", "password", "email") VALUES
(1, 'Administrador', '$2y$10$KustvKUJ3sNWlBNebklqGOIG6p8cEcNGIkYd8SYoGvCf1/aWZGUua', 'laformula.salud@gmail.com');
