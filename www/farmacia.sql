SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE DATABASE IF NOT EXISTS `farmacia` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `farmacia`;

CREATE TABLE `brands` (
  `brand_id` int(11) NOT NULL,
  `brand_name` varchar(255) NOT NULL,
  `brand_active` int(11) NOT NULL DEFAULT 0,
  `brand_status` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `brands` (`brand_id`, `brand_name`, `brand_active`, `brand_status`) VALUES
(1, 'Cipla', 1, 1),
(2, 'Mankind', 1, 1),
(3, 'Sunpharma', 1, 1),
(4, 'MicroLabs', 1, 1),
(5, 'Pfizer', 1, 1);

CREATE TABLE `categories` (
  `categories_id` int(11) NOT NULL,
  `categories_name` varchar(255) NOT NULL,
  `categories_active` int(11) NOT NULL DEFAULT 0,
  `categories_status` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `categories` (`categories_id`, `categories_name`, `categories_active`, `categories_status`) VALUES
(1, 'Pastillas', 1, 1),
(2, 'Jarabe', 1, 1),
(3, 'Inyecciones', 1, 1),
(4, 'Paliativos', 1, 1),
(5, 'Vacunas', 1, 1);

CREATE TABLE `orders` (
  `id` int(15) NOT NULL,
  `uno` varchar(50) NOT NULL,
  `orderDate` date NOT NULL,
  `clientName` text NOT NULL,
  `projectName` varchar(30) NOT NULL,
  `clientContact` int(15) NOT NULL,
  `address` varchar(30) NOT NULL,
  `subTotal` int(100) NOT NULL,
  `totalAmount` int(100) NOT NULL,
  `discount` int(100) NOT NULL,
  `grandTotalValue` int(100) NOT NULL,
  `gstn` int(100) NOT NULL,
  `paid` int(100) NOT NULL,
  `dueValue` int(100) NOT NULL,
  `paymentType` int(15) NOT NULL,
  `paymentStatus` int(15) NOT NULL,
  `paymentPlace` int(5) NOT NULL,
  `delete_status` tinyint(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `orders` (`id`, `uno`, `orderDate`, `clientName`, `projectName`, `clientContact`, `address`, `subTotal`, `totalAmount`, `discount`, `grandTotalValue`, `gstn`, `paid`, `dueValue`, `paymentType`, `paymentStatus`, `paymentPlace`, `delete_status`) VALUES
(1, 'INV-0001', '2022-02-28', 'Lucho Florez', '', 2147483647, '', 100, 10, 108, 49, 0, 49, 49, 2, 1, 0, 0),
(2, 'INV-0002', '2022-03-24', 'Bernardo Galán', '', 2147483647, '', 300, 354, 354, 354, 18, 354, 354, 1, 1, 1, 0),
(3, 'INV-0003', '2022-04-15', 'Fredy Patricio', '', 2147483647, '', 860, 1015, 10, 1005, 155, 500, 505, 2, 2, 1, 0),
(4, 'INV-0004', '2022-04-15', 'Pedro Cliente', '', 2147483647, '', 60, 71, 0, 71, 11, 50, 21, 5, 2, 1, 0),
(5, 'INV-0005', '2022-05-01', 'Juan Cliente', '', 2147483647, '', 200, 236, 0, 236, 36, 300, -64, 2, 1, 1, 0),
(6, 'INV-0006', '2022-05-01', 'Julio Cliente', '', 2147483647, '', 250, 295, 0, 295, 45, 300, -5, 2, 1, 2, 0),
(7, 'INV-0007', '2022-05-03', 'Pedro García', '', 2147483647, '', 250, 295, 0, 295, 45, 300, -5, 2, 1, 1, 0);

CREATE TABLE `order_item` (
  `id` int(15) NOT NULL,
  `productName` int(100) NOT NULL,
  `quantity` varchar(255) NOT NULL,
  `rate` varchar(255) NOT NULL,
  `total` varchar(255) NOT NULL,
  `lastid` int(50) NOT NULL,
  `added_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `order_item` (`id`, `productName`, `quantity`, `rate`, `total`, `lastid`, `added_date`) VALUES
(5, 2, '1', '100', '100.00', 1, '0000-00-00'),
(7, 1, '2', '30', '60.00', 3, '2022-04-15'),
(8, 2, '4', '150', '600.00', 3, '2022-04-15'),
(9, 3, '1', '200', '200.00', 3, '2022-04-15'),
(10, 1, '2', '30', '60.00', 4, '2022-04-15'),
(13, 2, '2', '150', '300.00', 2, '2022-04-15'),
(14, 3, '1', '200', '200.00', 5, '2022-05-01'),
(15, 5, '1', '250', '250.00', 6, '2022-05-01'),
(16, 5, '1', '250', '250.00', 7, '2022-05-03');

CREATE TABLE `product` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `barcode` varchar(50) DEFAULT NULL,
  `product_image` text NOT NULL,
  `brand_id` int(11) NOT NULL,
  `categories_id` int(11) NOT NULL,
  `quantity` varchar(255) NOT NULL,
  `purchase_price` int(100) NOT NULL DEFAULT 0,
  `rate` varchar(255) NOT NULL,
  `mrp` int(100) NOT NULL,
  `bno` varchar(50) NOT NULL,
  `expdate` date NOT NULL,
  `added_date` date NOT NULL,
  `active` int(11) NOT NULL DEFAULT 0,
  `status` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `product` (`product_id`, `product_name`, `barcode`, `product_image`, `brand_id`, `categories_id`, `quantity`, `purchase_price`, `rate`, `mrp`, `bno`, `expdate`, `added_date`, `active`, `status`) VALUES
(1, 'Acetaminofen 500', '7700000010001', 'tab.jpg', 1, 1, '50', 20, '30', 40, '307002', '2045-02-28', '2022-02-28', 1, 1),
(2, 'Fronta 23', '7700000010002', 'tab1.jpg', 2, 1, '30', 100, '150', 200, '307003', '2022-02-16', '2022-02-28', 1, 1),
(3, 'Rapazol 120', '7700000010003', 'tab3.jpg', 3, 3, '70', 140, '200', 300, '307004', '2024-03-13', '2022-02-28', 1, 1),
(4, 'Escripvo 450', '7700000010004', 'tab4.jpg', 4, 1, '500', 15, '25', 30, '307005', '2050-05-31', '2022-04-15', 1, 1),
(5, 'Vacuna 123', '7700000010005', 'vacuna pfizer.webp', 5, 5, '2500', 180, '250', 254, '171712', '2031-06-18', '2022-05-01', 1, 1);

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `users` (`user_id`, `username`, `password`, `email`) VALUES
(1, 'Administrador', '$2y$10$KustvKUJ3sNWlBNebklqGOIG6p8cEcNGIkYd8SYoGvCf1/aWZGUua', 'laformula.salud@gmail.com');

ALTER TABLE `brands` ADD PRIMARY KEY (`brand_id`);
ALTER TABLE `categories` ADD PRIMARY KEY (`categories_id`);
ALTER TABLE `orders` ADD PRIMARY KEY (`id`);
ALTER TABLE `order_item` ADD PRIMARY KEY (`id`);
ALTER TABLE `product` ADD PRIMARY KEY (`product_id`);
ALTER TABLE `users` ADD PRIMARY KEY (`user_id`);

ALTER TABLE `product` ADD UNIQUE KEY `unique_barcode` (`barcode`);

ALTER TABLE `brands` MODIFY `brand_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
ALTER TABLE `categories` MODIFY `categories_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
ALTER TABLE `orders` MODIFY `id` int(15) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
ALTER TABLE `order_item` MODIFY `id` int(15) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
ALTER TABLE `product` MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
ALTER TABLE `users` MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

COMMIT;
