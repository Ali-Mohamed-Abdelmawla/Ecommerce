-- ecommerce
-- --------------------------------------------------------------------------------------------------------------------
-- creating tables

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    typename VARCHAR(255)
);

CREATE TABLE products (
    id CHAR(36) PRIMARY KEY,  -- UUID for product ID
    name VARCHAR(255) NOT NULL,
    inStock BOOLEAN,
    description TEXT,
    category_id INT,  -- Foreign key linking to categories table
    brand VARCHAR(255),
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

CREATE TABLE galleries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id CHAR(36),  -- Foreign key linking to products table
    image_url TEXT,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE attributes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255),  -- Attribute name (e.g., Size, Color)
    type VARCHAR(255),  -- Type of attribute (e.g., text, swatch)
    product_id CHAR(36),  -- Foreign key linking to products table
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE attribute_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    attribute_id INT,  -- Foreign key linking to attributes table
    display_value VARCHAR(255),  -- Display value (e.g., Small, Green)
    value VARCHAR(255),  -- Actual value (e.g., 'S', '#44FF03')
    FOREIGN KEY (attribute_id) REFERENCES attributes(id) ON DELETE CASCADE
);

CREATE TABLE prices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id CHAR(36),  -- Foreign key linking to products table
    amount DECIMAL(10, 2) NOT NULL,  -- Price amount
    currency_label VARCHAR(10) NOT NULL,  -- Currency label (e.g., USD)
    currency_symbol VARCHAR(5) NOT NULL,  -- Currency symbol (e.g., $)
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE orders (
    id CHAR(36) PRIMARY KEY,  -- UUID for the order ID
    status VARCHAR(50) DEFAULT 'pending',  -- Order status (e.g., pending, completed)
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,  -- Order creation timestamp
    total_amount DECIMAL(10, 2) NOT NULL,  -- Total order amount
    currency_label VARCHAR(10) NOT NULL,  -- Currency label (e.g., USD)
    currency_symbol VARCHAR(5) NOT NULL,  -- Currency symbol (e.g., $)
    product_list JSON NOT NULL  -- A JSON field to store products (ID, quantity, price)
);

-- --------------------------------------------------------------------------------------------------------------------
-- Inserting products

-- Categories

INSERT IGNORE INTO categories (name, typename) VALUES
('all', 'Category'),
('clothes', 'Category'),
('tech', 'Category');


-- --------------------------------------------------------------------------------------------------------------------
-- products

INSERT IGNORE INTO products (id, name, inStock, description, category_id, brand) VALUES
('huarache-x-stussy-le', 'Nike Air Huarache Le', true, 'Great sneakers for everyday use!', 
  (SELECT id FROM categories WHERE name = 'clothes'), 'Nike x Stussy'),
('jacket-canada-goosee', 'Jacket', true, 'Awesome winter jacket', 
  (SELECT id FROM categories WHERE name = 'clothes'), 'Canada Goose'),
('ps-5', 'PlayStation 5', true, 'A good gaming console. Plays games of PS4! Enjoy if you can buy it mwahahahaha', 
  (SELECT id FROM categories WHERE name = 'tech'), 'Sony'),
('xbox-series-s', 'Xbox Series S 512GB', false, 'Hardware-beschleunigtes Raytracing macht dein Spiel noch realistischer...', 
  (SELECT id FROM categories WHERE name = 'tech'), 'Microsoft'),
('apple-imac-2021', 'iMac 2021', true, 'The new iMac!', 
  (SELECT id FROM categories WHERE name = 'tech'), 'Apple'),
('apple-iphone-12-pro', 'iPhone 12 Pro', true, 'This is iPhone 12. Nothing else to say.', 
  (SELECT id FROM categories WHERE name = 'tech'), 'Apple'),
('apple-airpods-pro', 'AirPods Pro', false, 'Magic like you’ve never heard. Active Noise Cancellation for immersive sound...', 
  (SELECT id FROM categories WHERE name = 'tech'), 'Apple'),
('apple-airtag', 'AirTag', true, 'Lose your knack for losing things.', 
  (SELECT id FROM categories WHERE name = 'tech'), 'Apple');

-- --------------------------------------------------------------------------------------------------------------------
-- galleries

INSERT IGNORE INTO galleries (product_id, image_url) VALUES
-- Nike Air Huarache Le
('huarache-x-stussy-le', 'https://cdn.shopify.com/s/files/1/0087/6193/3920/products/DD1381200_DEOA_2_720x.jpg?v=1612816087'),
('huarache-x-stussy-le', 'https://cdn.shopify.com/s/files/1/0087/6193/3920/products/DD1381200_DEOA_1_720x.jpg?v=1612816087'),
('huarache-x-stussy-le', 'https://cdn.shopify.com/s/files/1/0087/6193/3920/products/DD1381200_DEOA_3_720x.jpg?v=1612816087'),
('huarache-x-stussy-le', 'https://cdn.shopify.com/s/files/1/0087/6193/3920/products/DD1381200_DEOA_5_720x.jpg?v=1612816087'),
('huarache-x-stussy-le', 'https://cdn.shopify.com/s/files/1/0087/6193/3920/products/DD1381200_DEOA_4_720x.jpg?v=1612816087'),

-- Canada Goose Jacket
('jacket-canada-goosee', 'https://images.canadagoose.com/image/upload/w_480,c_scale,f_auto,q_auto:best/v1576016105/product-image/2409L_61.jpg'),
('jacket-canada-goosee', 'https://images.canadagoose.com/image/upload/w_480,c_scale,f_auto,q_auto:best/v1576016107/product-image/2409L_61_a.jpg'),
('jacket-canada-goosee', 'https://images.canadagoose.com/image/upload/w_480,c_scale,f_auto,q_auto:best/v1576016108/product-image/2409L_61_b.jpg'),
('jacket-canada-goosee', 'https://images.canadagoose.com/image/upload/w_480,c_scale,f_auto,q_auto:best/v1576016109/product-image/2409L_61_c.jpg'),
('jacket-canada-goosee', 'https://images.canadagoose.com/image/upload/w_480,c_scale,f_auto,q_auto:best/v1634058169/product-image/2409L_61_o.png'),
('jacket-canada-goosee', 'https://images.canadagoose.com/image/upload/w_480,c_scale,f_auto,q_auto:best/v1634058159/product-image/2409L_61_p.png'),

-- PlayStation 5
('ps-5', 'https://images-na.ssl-images-amazon.com/images/I/510VSJ9mWDL._SL1262_.jpg'),
('ps-5', 'https://images-na.ssl-images-amazon.com/images/I/610%2B69ZsKCL._SL1500_.jpg'),
('ps-5', 'https://images-na.ssl-images-amazon.com/images/I/51iPoFwQT3L._SL1230_.jpg'),
('ps-5', 'https://images-na.ssl-images-amazon.com/images/I/61qbqFcvoNL._SL1500_.jpg'),
('ps-5', 'https://images-na.ssl-images-amazon.com/images/I/51HCjA3rqYL._SL1230_.jpg'),

-- Xbox Series S
('xbox-series-s', 'https://images-na.ssl-images-amazon.com/images/I/71vPCX0bS-L._SL1500_.jpg'),
('xbox-series-s', 'https://images-na.ssl-images-amazon.com/images/I/71q7JTbRTpL._SL1500_.jpg'),
('xbox-series-s', 'https://images-na.ssl-images-amazon.com/images/I/71iQ4HGHtsL._SL1500_.jpg'),
('xbox-series-s', 'https://images-na.ssl-images-amazon.com/images/I/61IYrCrBzxL._SL1500_.jpg'),
('xbox-series-s', 'https://images-na.ssl-images-amazon.com/images/I/61RnXmpAmIL._SL1500_.jpg'),

-- iMac 2021
('apple-imac-2021', 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/imac-24-blue-selection-hero-202104?wid=904&hei=840&fmt=jpeg&qlt=80&.v=1617492405000'),

-- iPhone 12 Pro
('apple-iphone-12-pro', 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-12-pro-family-hero?wid=940&hei=1112&fmt=jpeg&qlt=80&.v=1604021663000'),

-- AirPods Pro
('apple-airpods-pro', 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/MWP22?wid=572&hei=572&fmt=jpeg&qlt=95&.v=1591634795000'),

-- AirTag
('apple-airtag', 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/airtag-double-select-202104?wid=445&hei=370&fmt=jpeg&qlt=95&.v=1617761672000');

-- --------------------------------------------------------------------------------------------------------------------
-- attributes

INSERT IGNORE INTO attributes (name, type, product_id) VALUES
('Size', 'text', 'huarache-x-stussy-le'),
('Capacity', 'text', 'ps-5'),
('Color', 'swatch', 'ps-5'),
('Capacity', 'text', 'apple-imac-2021'),
('With USB 3 ports', 'text', 'apple-imac-2021'),
('Touch ID in keyboard', 'text', 'apple-imac-2021'),
('Capacity', 'text', 'apple-iphone-12-pro'),
('Color', 'swatch', 'apple-iphone-12-pro'),
('Size', 'text', 'jacket-canada-goosee'),
('Capacity', 'text', 'xbox-series-s'),
('Color', 'swatch', 'xbox-series-s');


-- --------------------------------------------------------------------------------------------------------------------
-- attributeItems

INSERT IGNORE INTO attribute_items (attribute_id, display_value, value) VALUES
-- Nike Air Huarache Le (Size attribute)
((SELECT id FROM attributes WHERE name = 'Size' AND product_id = 'huarache-x-stussy-le'), '40', '40'),
((SELECT id FROM attributes WHERE name = 'Size' AND product_id = 'huarache-x-stussy-le'), '41', '41'),
((SELECT id FROM attributes WHERE name = 'Size' AND product_id = 'huarache-x-stussy-le'), '42', '42'),
((SELECT id FROM attributes WHERE name = 'Size' AND product_id = 'huarache-x-stussy-le'), '43', '43'),

-- Jacket Canada Goose (Size attribute)
((SELECT id FROM attributes WHERE name = 'Size' AND product_id = 'jacket-canada-goosee'), 'Small', 'S'),
((SELECT id FROM attributes WHERE name = 'Size' AND product_id = 'jacket-canada-goosee'), 'Medium', 'M'),
((SELECT id FROM attributes WHERE name = 'Size' AND product_id = 'jacket-canada-goosee'), 'Large', 'L'),
((SELECT id FROM attributes WHERE name = 'Size' AND product_id = 'jacket-canada-goosee'), 'Extra Large', 'XL'),

-- PlayStation 5 (Color and Capacity attributes)
((SELECT id FROM attributes WHERE name = 'Color' AND product_id = 'ps-5'), 'Green', '#44FF03'),
((SELECT id FROM attributes WHERE name = 'Color' AND product_id = 'ps-5'), 'Cyan', '#03FFF7'),
((SELECT id FROM attributes WHERE name = 'Color' AND product_id = 'ps-5'), 'Blue', '#030BFF'),
((SELECT id FROM attributes WHERE name = 'Color' AND product_id = 'ps-5'), 'Black', '#000000'),
((SELECT id FROM attributes WHERE name = 'Color' AND product_id = 'ps-5'), 'White', '#FFFFFF'),
((SELECT id FROM attributes WHERE name = 'Capacity' AND product_id = 'ps-5'), '512G', '512G'),
((SELECT id FROM attributes WHERE name = 'Capacity' AND product_id = 'ps-5'), '1T', '1T'),

-- Xbox Series S (Color and Capacity attributes)
((SELECT id FROM attributes WHERE name = 'Color' AND product_id = 'xbox-series-s'), 'Green', '#44FF03'),
((SELECT id FROM attributes WHERE name = 'Color' AND product_id = 'xbox-series-s'), 'Cyan', '#03FFF7'),
((SELECT id FROM attributes WHERE name = 'Color' AND product_id = 'xbox-series-s'), 'Blue', '#030BFF'),
((SELECT id FROM attributes WHERE name = 'Color' AND product_id = 'xbox-series-s'), 'Black', '#000000'),
((SELECT id FROM attributes WHERE name = 'Color' AND product_id = 'xbox-series-s'), 'White', '#FFFFFF'),
((SELECT id FROM attributes WHERE name = 'Capacity' AND product_id = 'xbox-series-s'), '512G', '512G'),
((SELECT id FROM attributes WHERE name = 'Capacity' AND product_id = 'xbox-series-s'), '1T', '1T'),

-- iMac 2021 (Capacity, With USB 3 Ports, and Touch ID in keyboard attributes)
((SELECT id FROM attributes WHERE name = 'Capacity' AND product_id = 'apple-imac-2021'), '256GB', '256GB'),
((SELECT id FROM attributes WHERE name = 'Capacity' AND product_id = 'apple-imac-2021'), '512GB', '512GB'),
((SELECT id FROM attributes WHERE name = 'With USB 3 ports' AND product_id = 'apple-imac-2021'), 'Yes', 'Yes'),
((SELECT id FROM attributes WHERE name = 'With USB 3 ports' AND product_id = 'apple-imac-2021'), 'No', 'No'),
((SELECT id FROM attributes WHERE name = 'Touch ID in keyboard' AND product_id = 'apple-imac-2021'), 'Yes', 'Yes'),
((SELECT id FROM attributes WHERE name = 'Touch ID in keyboard' AND product_id = 'apple-imac-2021'), 'No', 'No'),

-- iPhone 12 Pro (Capacity and Color attributes)
((SELECT id FROM attributes WHERE name = 'Capacity' AND product_id = 'apple-iphone-12-pro'), '512G', '512G'),
((SELECT id FROM attributes WHERE name = 'Capacity' AND product_id = 'apple-iphone-12-pro'), '1T', '1T'),
((SELECT id FROM attributes WHERE name = 'Color' AND product_id = 'apple-iphone-12-pro'), 'Green', '#44FF03'),
((SELECT id FROM attributes WHERE name = 'Color' AND product_id = 'apple-iphone-12-pro'), 'Cyan', '#03FFF7'),
((SELECT id FROM attributes WHERE name = 'Color' AND product_id = 'apple-iphone-12-pro'), 'Blue', '#030BFF'),
((SELECT id FROM attributes WHERE name = 'Color' AND product_id = 'apple-iphone-12-pro'), 'Black', '#000000'),
((SELECT id FROM attributes WHERE name = 'Color' AND product_id = 'apple-iphone-12-pro'), 'White', '#FFFFFF');


-- --------------------------------------------------------------------------------------------------------------------
-- prices

INSERT IGNORE INTO prices (product_id, amount, currency_label, currency_symbol) VALUES
('huarache-x-stussy-le', 144.69, 'USD', '$'),
('jacket-canada-goosee', 518.47, 'USD', '$'),
('ps-5', 844.02, 'USD', '$'),
('xbox-series-s', 333.99, 'USD', '$'),
('apple-imac-2021', 1688.03, 'USD', '$'),
('apple-iphone-12-pro', 1000.76, 'USD', '$'),
('apple-airpods-pro', 300.23, 'USD', '$'),
('apple-airtag', 120.57, 'USD', '$');
