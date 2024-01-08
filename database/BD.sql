-- Base de données :  `web4shop`

CREATE DATABASE IF NOT EXISTS `web4shop` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `web4shop`;

-- --------------------------------------------------------

-- Structure des tables


-- Table `admin`

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- Table `categories`

CREATE TABLE `categories` (
  `id` tinyint(4) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- Table `customers`

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `forname` varchar(50),
  `surname` varchar(50),
  `add1` varchar(50),
  `add2` varchar(50),
  `add3` varchar(50),
  `postcode` varchar(10),
  `phone` varchar(20),
  `email` varchar(150),
  `registered` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- Table `delivery_addresses`

CREATE TABLE `delivery_addresses` (
  `id` int(11) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `add1` varchar(50),
  `add2` varchar(50),
  `city` varchar(50),
  `postcode` varchar(10),
  `phone` varchar(20) NOT NULL,
  `email` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- Table `logins`

CREATE TABLE `logins` (
  `id` int(11) NOT NULL,
  `customer_id` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- Table `orderitems`

CREATE TABLE `orderitems` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- Table `orders`

CREATE TABLE `orders` (
  `id` int(20) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `registered` int(11) NOT NULL,
  `delivery_add_id` int(11) DEFAULT NULL,
  `payment_type` varchar(6) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `status` tinyint(4) NOT NULL,
  `session` varchar(100) NOT NULL,
  `total` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- Table `products`

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `cat_id` tinyint(4) NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(30) NOT NULL,
  `price` decimal(5,2) NOT NULL,
  `quantity` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- Table `reviews`

CREATE TABLE `reviews` (
  `id_product` int(2) NOT NULL,
  `name_user` varchar(50) NOT NULL,
  `stars` int(1) NOT NULL,
  `title` varchar(50) NOT NULL,
  `description` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

-- Clés primaires des tables


-- Table `admin`

ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);


-- Table `categories`

ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);


-- Index pour la table `customers`

ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);


-- Table `delivery_addresses`

ALTER TABLE `delivery_addresses`
  ADD PRIMARY KEY (`id`);


-- Table `logins`

ALTER TABLE `logins`
  ADD PRIMARY KEY (`id`);


-- Table `orderitems`

ALTER TABLE `orderitems`
  ADD PRIMARY KEY (`id`);


-- Table `orders`

ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);


-- Table `products`

ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);


-- Table `reviews`

ALTER TABLE `reviews`
  ADD KEY `review/product` (`id_product`);

-- --------------------------------------------------------

-- AUTO_INCREMENT des clés primaires des tables


-- Table `admin`

ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;


-- Table `categories`

ALTER TABLE `categories`
  MODIFY `id` tinyint(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;


-- Table `customers`

ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;


-- Table `delivery_addresses`

ALTER TABLE `delivery_addresses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;


-- Table `logins`

ALTER TABLE `logins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;


-- Table `orderitems`

ALTER TABLE `orderitems`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=247;


-- Table `orders`

ALTER TABLE `orders`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;


-- Table `products`

ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

-- --------------------------------------------------------

-- Contraintes des tables


-- Contraintes pour la table `reviews`

ALTER TABLE `reviews`
  ADD CONSTRAINT `review/product` FOREIGN KEY (`id_product`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;