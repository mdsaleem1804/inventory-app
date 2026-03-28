-- ============================================================
-- BAKERY SHOP INVENTORY - FRESH DATA WITH STOCK TRACKING
-- This script deletes all existing data and reloads with proper stock
-- ============================================================

SET FOREIGN_KEY_CHECKS=0;

-- ============================================================
-- TRUNCATE ALL TABLES
-- ============================================================
TRUNCATE TABLE sale_item_batches;
TRUNCATE TABLE stock_movements;
TRUNCATE TABLE sale_items;
TRUNCATE TABLE sales;
TRUNCATE TABLE purchase_items;
TRUNCATE TABLE purchases;
TRUNCATE TABLE product_batches;
TRUNCATE TABLE products;
TRUNCATE TABLE categories;
TRUNCATE TABLE customers;
TRUNCATE TABLE suppliers;

SET FOREIGN_KEY_CHECKS=1;

-- ============================================================
-- 1. CATEGORIES
-- ============================================================
INSERT INTO categories (name, created_at, updated_at) VALUES
('Bread & Loaves', NOW(), NOW()),
('Cakes & Pastries', NOW(), NOW()),
('Donuts & Beignets', NOW(), NOW()),
('Cookies & Biscuits', NOW(), NOW()),
('Beverages', NOW(), NOW()),
('Ingredients & Supplies', NOW(), NOW()),
('Breakfast Items', NOW(), NOW());

-- ============================================================
-- 2. SUPPLIERS
-- ============================================================
INSERT INTO suppliers (name, phone, email, address, created_at, updated_at) VALUES
('Premium Flour Mills', '9876543210', 'contact@flourmills.com', '123 Industrial Park, Bakery District', NOW(), NOW()),
('Fresh Dairy Supplies', '9876543211', 'sales@freshdairy.com', '456 Dairy Lane, Food Storage Area', NOW(), NOW()),
('Artisan Ingredients Co', '9876543212', 'order@artisaningredients.com', '789 Commerce Street, Supply Hub', NOW(), NOW());

-- ============================================================
-- 3. CUSTOMERS
-- ============================================================
INSERT INTO customers (name, phone, email, address, created_at, updated_at) VALUES
('John Smith', '8765432101', 'john@email.com', '101 Main Street, City Center', NOW(), NOW()),
('Sarah Johnson', '8765432102', 'sarah@email.com', '202 Park Avenue, Downtown', NOW(), NOW()),
('Mike Restaurant', '8765432103', 'orders@mikerestaurant.com', '303 Business Complex, Food Court', NOW(), NOW()),
('Emma Bakery Cafe', '8765432104', 'supply@emmacafe.com', '404 Shopping Mall, Premium District', NOW(), NOW()),
('David Catering', '8765432105', 'events@davidcatering.com', '505 Event Hall, Banquet Area', NOW(), NOW()),
('Lisa Sweet Tooth', '8765432106', 'lisa@sweetshop.com', '606 Retail Plaza, Market Street', NOW(), NOW()),
('Corporate Office', '8765432107', 'pantry@corporate.com', '707 Business Tower, Finance District', NOW(), NOW()),
('Family Store', '8765432108', 'manager@familystore.com', '808 Neighborhood, Residential Area', NOW(), NOW()),
('Hotel Reception', '8765432109', 'orders@luxhotel.com', '909 Premium Lane, Hotel District', NOW(), NOW()),
('School Cafeteria', '8765432110', 'meals@schoolcorp.com', '1010 Education Park, School Campus', NOW(), NOW());

-- ============================================================
-- 4. PRODUCTS (52 Items)
-- ============================================================
INSERT INTO products (name, sku, barcode, category_id, price, cost_price, unit, minimum_stock, description, is_active, created_at, updated_at) VALUES
('White Bread - 400g', 'WB-400-001', '8901234567890', 1, 60.00, 35.00, 'pcs', 20, 'Fresh white bread loaf', 1, NOW(), NOW()),
('Brown Bread - 400g', 'BB-400-002', '8901234567891', 1, 70.00, 40.00, 'pcs', 20, 'Whole wheat brown bread', 1, NOW(), NOW()),
('Multigrain Bread - 400g', 'MB-400-003', '8901234567892', 1, 90.00, 50.00, 'pcs', 15, 'Nutritious multigrain loaf', 1, NOW(), NOW()),
('French Baguette - 300g', 'FB-300-004', '8901234567893', 1, 85.00, 45.00, 'pcs', 25, 'Crispy French style baguette', 1, NOW(), NOW()),
('Sourdough Bread - 500g', 'SB-500-005', '8901234567894', 1, 120.00, 65.00, 'pcs', 15, 'Tangy sourdough loaf', 1, NOW(), NOW()),
('Rye Bread - 400g', 'RB-400-006', '8901234567895', 1, 95.00, 52.00, 'pcs', 12, 'Dark rye bread', 1, NOW(), NOW()),
('Ciabatta Bread - 250g', 'CB-250-007', '8901234567896', 1, 80.00, 42.00, 'pcs', 20, 'Italian style ciabatta', 1, NOW(), NOW()),
('Focaccia Bread - 300g', 'FC-300-008', '8901234567897', 1, 100.00, 55.00, 'pcs', 15, 'Olive oil focaccia', 1, NOW(), NOW()),
('Whole Wheat Bread - 400g', 'WW-400-009', '8901234567898', 1, 75.00, 42.00, 'pcs', 20, 'Healthy whole wheat loaf', 1, NOW(), NOW()),
('Garlic Bread - 350g', 'GB-350-010', '8901234567899', 1, 95.00, 50.00, 'pcs', 18, 'Garlic flavored bread', 1, NOW(), NOW()),
('Honey Wheat Bread - 400g', 'HWB-400-011', '8901234567900', 1, 85.00, 48.00, 'pcs', 15, 'Sweet honey wheat bread', 1, NOW(), NOW()),
('Oat Bread - 400g', 'OB-400-012', '8901234567901', 1, 88.00, 49.00, 'pcs', 12, 'Fiber-rich oat bread', 1, NOW(), NOW()),
('Chocolate Cake - 500g', 'CC-500-013', '8901234567902', 2, 250.00, 120.00, 'pcs', 10, 'Rich chocolate layer cake', 1, NOW(), NOW()),
('Vanilla Cake - 500g', 'VC-500-014', '8901234567903', 2, 240.00, 110.00, 'pcs', 10, 'Classic vanilla sponge cake', 1, NOW(), NOW()),
('Strawberry Cake - 500g', 'SC-500-015', '8901234567904', 2, 280.00, 130.00, 'pcs', 8, 'Fresh strawberry layer cake', 1, NOW(), NOW()),
('Carrot Cake - 500g', 'CAC-500-016', '8901234567905', 2, 260.00, 125.00, 'pcs', 8, 'Moist carrot cake with cream', 1, NOW(), NOW()),
('Red Velvet Cake - 500g', 'RVC-500-017', '8901234567906', 2, 300.00, 140.00, 'pcs', 5, 'Premium red velvet cake', 1, NOW(), NOW()),
('Cheesecake - 500g', 'CHC-500-018', '8901234567907', 2, 320.00, 150.00, 'pcs', 5, 'Creamy New York cheesecake', 1, NOW(), NOW()),
('Croissants - Pack of 4', 'CR-4-019', '8901234567908', 2, 180.00, 85.00, 'pcs', 15, 'Butter croissants', 1, NOW(), NOW()),
('Pain au Chocolat - 3pcs', 'PC-3-020', '8901234567909', 2, 150.00, 70.00, 'pcs', 12, 'Chocolate filled pastry', 1, NOW(), NOW()),
('Danish Pastries - 6pcs', 'DP-6-021', '8901234567910', 2, 200.00, 90.00, 'pcs', 10, 'Assorted Danish pastries', 1, NOW(), NOW()),
('Eclairs - 4pcs', 'EC-4-022', '8901234567911', 2, 220.00, 100.00, 'pcs', 8, 'Chocolate eclairs', 1, NOW(), NOW()),
('Macarons - Box of 12', 'MAC-12-023', '8901234567912', 2, 300.00, 135.00, 'pcs', 6, 'French macarons assorted', 1, NOW(), NOW()),
('Tarts - 4pcs', 'TART-4-024', '8901234567913', 2, 240.00, 110.00, 'pcs', 8, 'Fruit tarts', 1, NOW(), NOW()),
('Brownies - 4pcs', 'BR-4-025', '8901234567914', 2, 160.00, 75.00, 'pcs', 12, 'Fudgy brownies', 1, NOW(), NOW()),
('Cheesecake Slices - 4pcs', 'CCS-4-026', '8901234567915', 2, 280.00, 130.00, 'pcs', 6, 'Individual cheesecake slices', 1, NOW(), NOW()),
('Tiramisu Cake - 500g', 'TC-500-027', '8901234567916', 2, 310.00, 145.00, 'pcs', 5, 'Italian tiramisu cake', 1, NOW(), NOW()),
('Black Forest Cake - 500g', 'BFC-500-028', '8901234567917', 2, 290.00, 135.00, 'pcs', 5, 'Classic Black Forest cake', 1, NOW(), NOW()),
('Glazed Donuts - 6pcs', 'GD-6-029', '8901234567918', 3, 120.00, 55.00, 'pcs', 20, 'Classic glazed donuts', 1, NOW(), NOW()),
('Chocolate Donuts - 6pcs', 'CD-6-030', '8901234567919', 3, 130.00, 60.00, 'pcs', 15, 'Chocolate frosted donuts', 1, NOW(), NOW()),
('Jelly Filled Donuts - 6pcs', 'JD-6-031', '8901234567920', 3, 140.00, 65.00, 'pcs', 12, 'Jam filled donuts', 1, NOW(), NOW()),
('Boston Cream Donuts - 6pcs', 'BCD-6-032', '8901234567921', 3, 150.00, 70.00, 'pcs', 10, 'Boston cream filled', 1, NOW(), NOW()),
('Beignets - 8pcs', 'BEI-8-033', '8901234567922', 3, 140.00, 60.00, 'pcs', 15, 'French style beignets', 1, NOW(), NOW()),
('Custard Donuts - 6pcs', 'CUSD-6-034', '8901234567923', 3, 135.00, 62.00, 'pcs', 12, 'Custard cream donuts', 1, NOW(), NOW()),
('Sprinkle Donuts - 6pcs', 'SPD-6-035', '8901234567924', 3, 125.00, 58.00, 'pcs', 15, 'Rainbow sprinkle donuts', 1, NOW(), NOW()),
('Chocolate Chip Cookies - 200g', 'CCC-200-036', '8901234567925', 4, 120.00, 55.00, 'pcs', 20, 'Classic chocolate chip cookies', 1, NOW(), NOW()),
('Oatmeal Cookies - 200g', 'OCC-200-037', '8901234567926', 4, 110.00, 50.00, 'pcs', 18, 'Chewy oatmeal cookies', 1, NOW(), NOW()),
('Shortbread Cookies - 200g', 'SBC-200-038', '8901234567927', 4, 130.00, 60.00, 'pcs', 15, 'Buttery shortbread', 1, NOW(), NOW()),
('Digestive Biscuits - 250g', 'DB-250-039', '8901234567928', 4, 100.00, 45.00, 'pcs', 25, 'Plain digestive biscuits', 1, NOW(), NOW()),
('Cream Biscuits - 250g', 'CRB-250-040', '8901234567929', 4, 115.00, 52.00, 'pcs', 20, 'Cream filled biscuits', 1, NOW(), NOW()),
('Gingerbread Cookies - 200g', 'GB-200-041', '8901234567930', 4, 125.00, 58.00, 'pcs', 12, 'Spiced gingerbread', 1, NOW(), NOW()),
('Almond Cookies - 200g', 'AC-200-042', '8901234567931', 4, 140.00, 65.00, 'pcs', 10, 'Crunchy almond cookies', 1, NOW(), NOW()),
('Fresh Orange Juice - 1L', 'OJ-1L-043', '8901234567932', 5, 80.00, 35.00, 'pcs', 30, 'Fresh squeezed orange juice', 1, NOW(), NOW()),
('Coffee - 250g', 'COFFEE-250-044', '8901234567933', 5, 200.00, 90.00, 'pcs', 15, 'Premium ground coffee', 1, NOW(), NOW()),
('Hot Chocolate Mix - 500g', 'HCM-500-045', '8901234567934', 5, 180.00, 80.00, 'pcs', 10, 'Instant hot chocolate', 1, NOW(), NOW()),
('All-Purpose Flour - 1kg', 'APF-1kg-046', '8901234567935', 6, 150.00, 70.00, 'kg', 50, 'Quality all-purpose flour', 1, NOW(), NOW()),
('Sugar - 1kg', 'SUG-1kg-047', '8901234567936', 6, 110.00, 50.00, 'kg', 40, 'Granulated white sugar', 1, NOW(), NOW()),
('Butter - 500g', 'BUT-500g-048', '8901234567937', 6, 320.00, 150.00, 'pcs', 25, 'Unsalted butter', 1, NOW(), NOW()),
('Eggs - Dozen', 'EGG-12-049', '8901234567938', 6, 180.00, 85.00, 'pcs', 40, 'Fresh farm eggs', 1, NOW(), NOW()),
('Milk - 1L', 'MILK-1L-050', '8901234567939', 6, 95.00, 42.00, 'pcs', 50, 'Fresh whole milk', 1, NOW(), NOW()),
('Baking Powder - 200g', 'BP-200g-051', '8901234567940', 6, 85.00, 38.00, 'pcs', 20, 'Double-acting baking powder', 1, NOW(), NOW()),
('Vanilla Extract - 100ml', 'VE-100ml-052', '8901234567941', 6, 250.00, 110.00, 'pcs', 10, 'Pure vanilla extract', 1, NOW(), NOW());

-- ============================================================
-- 5. OPENING STOCK - Product Batches
-- ============================================================
INSERT INTO product_batches (product_id, batch_number, quantity, remaining_quantity, cost_price, expiry_date, created_at, updated_at) VALUES
(1, 'BATCH-WB-001-2026', 100, 100, 35.00, '2026-04-30', NOW(), NOW()),
(2, 'BATCH-BB-001-2026', 80, 80, 40.00, '2026-04-30', NOW(), NOW()),
(3, 'BATCH-MB-001-2026', 60, 60, 50.00, '2026-04-25', NOW(), NOW()),
(4, 'BATCH-FB-001-2026', 90, 90, 45.00, '2026-04-28', NOW(), NOW()),
(5, 'BATCH-SB-001-2026', 50, 50, 65.00, '2026-04-20', NOW(), NOW()),
(6, 'BATCH-RB-001-2026', 45, 45, 52.00, '2026-05-05', NOW(), NOW()),
(7, 'BATCH-CB-001-2026', 75, 75, 42.00, '2026-04-26', NOW(), NOW()),
(8, 'BATCH-FC-001-2026', 55, 55, 55.00, '2026-04-24', NOW(), NOW()),
(9, 'BATCH-WW-001-2026', 85, 85, 42.00, '2026-04-30', NOW(), NOW()),
(10, 'BATCH-GB-001-2026', 70, 70, 50.00, '2026-04-27', NOW(), NOW()),
(11, 'BATCH-HWB-001-2026', 60, 60, 48.00, '2026-04-29', NOW(), NOW()),
(12, 'BATCH-OB-001-2026', 50, 50, 49.00, '2026-05-02', NOW(), NOW()),
(13, 'BATCH-CC-001-2026', 30, 30, 120.00, '2026-03-31', NOW(), NOW()),
(14, 'BATCH-VC-001-2026', 30, 30, 110.00, '2026-03-31', NOW(), NOW()),
(15, 'BATCH-SC-001-2026', 25, 25, 130.00, '2026-03-30', NOW(), NOW()),
(16, 'BATCH-CAC-001-2026', 25, 25, 125.00, '2026-03-31', NOW(), NOW()),
(17, 'BATCH-RVC-001-2026', 15, 15, 140.00, '2026-03-30', NOW(), NOW()),
(18, 'BATCH-CHC-001-2026', 15, 15, 150.00, '2026-03-29', NOW(), NOW()),
(19, 'BATCH-CR-001-2026', 60, 60, 85.00, '2026-03-30', NOW(), NOW()),
(20, 'BATCH-PC-001-2026', 50, 50, 70.00, '2026-03-30', NOW(), NOW()),
(21, 'BATCH-DP-001-2026', 40, 40, 90.00, '2026-03-30', NOW(), NOW()),
(22, 'BATCH-EC-001-2026', 32, 32, 100.00, '2026-03-29', NOW(), NOW()),
(23, 'BATCH-MAC-001-2026', 24, 24, 135.00, '2026-03-31', NOW(), NOW()),
(24, 'BATCH-TART-001-2026', 32, 32, 110.00, '2026-03-30', NOW(), NOW()),
(25, 'BATCH-BR-001-2026', 48, 48, 75.00, '2026-03-30', NOW(), NOW()),
(26, 'BATCH-CCS-001-2026', 24, 24, 130.00, '2026-03-29', NOW(), NOW()),
(27, 'BATCH-TC-001-2026', 15, 15, 145.00, '2026-03-29', NOW(), NOW()),
(28, 'BATCH-BFC-001-2026', 15, 15, 135.00, '2026-03-31', NOW(), NOW()),
(29, 'BATCH-GD-001-2026', 120, 120, 55.00, '2026-03-30', NOW(), NOW()),
(30, 'BATCH-CD-001-2026', 90, 90, 60.00, '2026-03-30', NOW(), NOW()),
(31, 'BATCH-JD-001-2026', 72, 72, 65.00, '2026-03-30', NOW(), NOW()),
(32, 'BATCH-BCD-001-2026', 60, 60, 70.00, '2026-03-29', NOW(), NOW()),
(33, 'BATCH-BEI-001-2026', 90, 90, 60.00, '2026-03-30', NOW(), NOW()),
(34, 'BATCH-CUSD-001-2026', 72, 72, 62.00, '2026-03-30', NOW(), NOW()),
(35, 'BATCH-SPD-001-2026', 90, 90, 58.00, '2026-03-30', NOW(), NOW()),
(36, 'BATCH-CCC-001-2026', 100, 100, 55.00, '2026-04-30', NOW(), NOW()),
(37, 'BATCH-OCC-001-2026', 90, 90, 50.00, '2026-04-30', NOW(), NOW()),
(38, 'BATCH-SBC-001-2026', 75, 75, 60.00, '2026-04-28', NOW(), NOW()),
(39, 'BATCH-DB-001-2026', 125, 125, 45.00, '2026-05-15', NOW(), NOW()),
(40, 'BATCH-CRB-001-2026', 100, 100, 52.00, '2026-05-10', NOW(), NOW()),
(41, 'BATCH-GBC-001-2026', 60, 60, 58.00, '2026-04-25', NOW(), NOW()),
(42, 'BATCH-AC-001-2026', 50, 50, 65.00, '2026-04-20', NOW(), NOW()),
(43, 'BATCH-OJ-001-2026', 150, 150, 35.00, '2026-03-29', NOW(), NOW()),
(44, 'BATCH-COFFEE-001-2026', 75, 75, 90.00, '2026-06-01', NOW(), NOW()),
(45, 'BATCH-HCM-001-2026', 50, 50, 80.00, '2026-05-20', NOW(), NOW()),
(46, 'BATCH-APF-001-2026', 300, 300, 70.00, '2026-06-01', NOW(), NOW()),
(47, 'BATCH-SUG-001-2026', 250, 250, 50.00, '2026-06-15', NOW(), NOW()),
(48, 'BATCH-BUT-001-2026', 150, 150, 150.00, '2026-05-01', NOW(), NOW()),
(49, 'BATCH-EGG-001-2026', 200, 200, 85.00, '2026-03-31', NOW(), NOW()),
(50, 'BATCH-MILK-001-2026', 250, 250, 42.00, '2026-03-30', NOW(), NOW()),
(51, 'BATCH-BP-001-2026', 100, 100, 38.00, '2026-06-01', NOW(), NOW()),
(52, 'BATCH-VE-001-2026', 50, 50, 110.00, '2026-06-01', NOW(), NOW());

-- ============================================================
-- 6. PURCHASES
-- ============================================================
INSERT INTO purchases (supplier_id, invoice_number, total_amount, created_at, updated_at) VALUES
(1, 'PUR-2026-03-24-001', 8500.00, '2026-03-24 08:30:00', '2026-03-24 08:30:00'),
(2, 'PUR-2026-03-25-001', 6800.00, '2026-03-25 09:15:00', '2026-03-25 09:15:00'),
(3, 'PUR-2026-03-26-001', 7200.00, '2026-03-26 07:45:00', '2026-03-26 07:45:00'),
(1, 'PUR-2026-03-27-001', 9300.00, '2026-03-27 08:00:00', '2026-03-27 08:00:00'),
(2, 'PUR-2026-03-28-001', 6400.00, '2026-03-28 10:30:00', '2026-03-28 10:30:00');

INSERT INTO purchase_items (purchase_id, product_id, quantity, cost_price, total, created_at, updated_at) VALUES
(1, 1, 50, 35.00, 1750.00, '2026-03-24 08:30:00', '2026-03-24 08:30:00'),
(1, 2, 40, 40.00, 1600.00, '2026-03-24 08:30:00', '2026-03-24 08:30:00'),
(1, 3, 30, 50.00, 1500.00, '2026-03-24 08:30:00', '2026-03-24 08:30:00'),
(1, 46, 100, 70.00, 3650.00, '2026-03-24 08:30:00', '2026-03-24 08:30:00'),
(2, 48, 40, 150.00, 6000.00, '2026-03-25 09:15:00', '2026-03-25 09:15:00'),
(2, 49, 50, 85.00, 4250.00, '2026-03-25 09:15:00', '2026-03-25 09:15:00'),
(2, 50, 100, 42.00, 4200.00, '2026-03-25 09:15:00', '2026-03-25 09:15:00'),
(3, 13, 15, 120.00, 1800.00, '2026-03-26 07:45:00', '2026-03-26 07:45:00'),
(3, 14, 15, 110.00, 1650.00, '2026-03-26 07:45:00', '2026-03-26 07:45:00'),
(3, 15, 12, 130.00, 1560.00, '2026-03-26 07:45:00', '2026-03-26 07:45:00'),
(3, 29, 60, 55.00, 3300.00, '2026-03-26 07:45:00', '2026-03-26 07:45:00'),
(3, 30, 40, 60.00, 2400.00, '2026-03-26 07:45:00', '2026-03-26 07:45:00'),
(4, 4, 60, 45.00, 2700.00, '2026-03-27 08:00:00', '2026-03-27 08:00:00'),
(4, 5, 30, 65.00, 1950.00, '2026-03-27 08:00:00', '2026-03-27 08:00:00'),
(4, 6, 25, 52.00, 1300.00, '2026-03-27 08:00:00', '2026-03-27 08:00:00'),
(4, 47, 100, 50.00, 5000.00, '2026-03-27 08:00:00', '2026-03-27 08:00:00'),
(4, 51, 50, 38.00, 1900.00, '2026-03-27 08:00:00', '2026-03-27 08:00:00'),
(5, 36, 60, 55.00, 3300.00, '2026-03-28 10:30:00', '2026-03-28 10:30:00'),
(5, 37, 50, 50.00, 2500.00, '2026-03-28 10:30:00', '2026-03-28 10:30:00'),
(5, 39, 80, 45.00, 3600.00, '2026-03-28 10:30:00', '2026-03-28 10:30:00');

-- ============================================================
-- 7. SALES
-- ============================================================
INSERT INTO sales (customer_id, invoice_number, total_amount, created_at, updated_at) VALUES
(1, 'SAL-2026-03-24-001', 1095.00, '2026-03-24 10:15:00', '2026-03-24 10:15:00'),
(3, 'SAL-2026-03-24-002', 2380.00, '2026-03-24 14:30:00', '2026-03-24 14:30:00'),
(2, 'SAL-2026-03-25-001', 1680.00, '2026-03-25 09:45:00', '2026-03-25 09:45:00'),
(4, 'SAL-2026-03-25-002', 3220.00, '2026-03-25 11:20:00', '2026-03-25 11:20:00'),
(5, 'SAL-2026-03-26-001', 4200.00, '2026-03-26 10:00:00', '2026-03-26 10:00:00'),
(6, 'SAL-2026-03-26-002', 1850.00, '2026-03-26 15:30:00', '2026-03-26 15:30:00'),
(7, 'SAL-2026-03-27-001', 2540.00, '2026-03-27 12:15:00', '2026-03-27 12:15:00'),
(8, 'SAL-2026-03-27-002', 2485.00, '2026-03-27 17:45:00', '2026-03-27 17:45:00'),
(9, 'SAL-2026-03-28-001', 3125.00, '2026-03-28 09:30:00', '2026-03-28 09:30:00'),
(10, 'SAL-2026-03-28-002', 2565.00, '2026-03-28 14:00:00', '2026-03-28 14:00:00');

INSERT INTO sale_items (sale_id, product_id, quantity, price, total, created_at, updated_at) VALUES
(1, 1, 10, 60.00, 600.00, '2026-03-24 10:15:00', '2026-03-24 10:15:00'),
(1, 29, 6, 120.00, 720.00, '2026-03-24 10:15:00', '2026-03-24 10:15:00'),
(1, 36, 3, 120.00, 360.00, '2026-03-24 10:15:00', '2026-03-24 10:15:00'),
(2, 2, 15, 70.00, 1050.00, '2026-03-24 14:30:00', '2026-03-24 14:30:00'),
(2, 13, 5, 250.00, 1250.00, '2026-03-24 14:30:00', '2026-03-24 14:30:00'),
(2, 43, 2, 80.00, 160.00, '2026-03-24 14:30:00', '2026-03-24 14:30:00'),
(3, 4, 8, 85.00, 680.00, '2026-03-25 09:45:00', '2026-03-25 09:45:00'),
(3, 19, 4, 180.00, 720.00, '2026-03-25 09:45:00', '2026-03-25 09:45:00'),
(3, 36, 4, 120.00, 480.00, '2026-03-25 09:45:00', '2026-03-25 09:45:00'),
(4, 13, 8, 250.00, 2000.00, '2026-03-25 11:20:00', '2026-03-25 11:20:00'),
(4, 14, 5, 240.00, 1200.00, '2026-03-25 11:20:00', '2026-03-25 11:20:00'),
(4, 43, 1, 80.00, 80.00, '2026-03-25 11:20:00', '2026-03-25 11:20:00'),
(5, 1, 20, 60.00, 1200.00, '2026-03-26 10:00:00', '2026-03-26 10:00:00'),
(5, 2, 20, 70.00, 1400.00, '2026-03-26 10:00:00', '2026-03-26 10:00:00'),
(5, 3, 15, 90.00, 1350.00, '2026-03-26 10:00:00', '2026-03-26 10:00:00'),
(5, 29, 10, 120.00, 1200.00, '2026-03-26 10:00:00', '2026-03-26 10:00:00'),
(6, 23, 4, 300.00, 1200.00, '2026-03-26 15:30:00', '2026-03-26 15:30:00'),
(6, 22, 3, 220.00, 660.00, '2026-03-26 15:30:00', '2026-03-26 15:30:00'),
(7, 4, 12, 85.00, 1020.00, '2026-03-27 12:15:00', '2026-03-27 12:15:00'),
(7, 10, 10, 95.00, 950.00, '2026-03-27 12:15:00', '2026-03-27 12:15:00'),
(7, 39, 15, 100.00, 1500.00, '2026-03-27 12:15:00', '2026-03-27 12:15:00'),
(8, 5, 10, 120.00, 1200.00, '2026-03-27 17:45:00', '2026-03-27 17:45:00'),
(8, 13, 3, 250.00, 750.00, '2026-03-27 17:45:00', '2026-03-27 17:45:00'),
(8, 31, 12, 140.00, 1680.00, '2026-03-27 17:45:00', '2026-03-27 17:45:00'),
(9, 14, 6, 240.00, 1440.00, '2026-03-28 09:30:00', '2026-03-28 09:30:00'),
(9, 15, 5, 280.00, 1400.00, '2026-03-28 09:30:00', '2026-03-28 09:30:00'),
(9, 44, 3, 200.00, 600.00, '2026-03-28 09:30:00', '2026-03-28 09:30:00'),
(10, 29, 15, 120.00, 1800.00, '2026-03-28 14:00:00', '2026-03-28 14:00:00'),
(10, 40, 12, 115.00, 1380.00, '2026-03-28 14:00:00', '2026-03-28 14:00:00'),
(10, 45, 2, 180.00, 360.00, '2026-03-28 14:00:00', '2026-03-28 14:00:00');

-- ============================================================
-- 8. UPDATE BATCH QUANTITIES AFTER SALES
-- ============================================================
UPDATE product_batches SET remaining_quantity = remaining_quantity - 10 WHERE product_id = 1;
UPDATE product_batches SET remaining_quantity = remaining_quantity - 15 WHERE product_id = 2;
UPDATE product_batches SET remaining_quantity = remaining_quantity - 15 WHERE product_id = 3;
UPDATE product_batches SET remaining_quantity = remaining_quantity - 20 WHERE product_id = 4;
UPDATE product_batches SET remaining_quantity = remaining_quantity - 10 WHERE product_id = 5;
UPDATE product_batches SET remaining_quantity = remaining_quantity - 0 WHERE product_id = 6;
UPDATE product_batches SET remaining_quantity = remaining_quantity - 0 WHERE product_id = 7;
UPDATE product_batches SET remaining_quantity = remaining_quantity - 0 WHERE product_id = 8;
UPDATE product_batches SET remaining_quantity = remaining_quantity - 0 WHERE product_id = 9;
UPDATE product_batches SET remaining_quantity = remaining_quantity - 10 WHERE product_id = 10;
UPDATE product_batches SET remaining_quantity = remaining_quantity - 0 WHERE product_id = 11;
UPDATE product_batches SET remaining_quantity = remaining_quantity - 0 WHERE product_id = 12;
UPDATE product_batches SET remaining_quantity = remaining_quantity - 13 WHERE product_id = 13;
UPDATE product_batches SET remaining_quantity = remaining_quantity - 11 WHERE product_id = 14;
UPDATE product_batches SET remaining_quantity = remaining_quantity - 5 WHERE product_id = 15;
UPDATE product_batches SET remaining_quantity = remaining_quantity - 0 WHERE product_id = 16;
UPDATE product_batches SET remaining_quantity = remaining_quantity - 0 WHERE product_id = 17;
UPDATE product_batches SET remaining_quantity = remaining_quantity - 0 WHERE product_id = 18;
UPDATE product_batches SET remaining_quantity = remaining_quantity - 4 WHERE product_id = 19;
UPDATE product_batches SET remaining_quantity = remaining_quantity - 0 WHERE product_id = 20;
UPDATE product_batches SET remaining_quantity = remaining_quantity - 0 WHERE product_id = 21;
UPDATE product_batches SET remaining_quantity = remaining_quantity - 3 WHERE product_id = 22;
UPDATE product_batches SET remaining_quantity = remaining_quantity - 4 WHERE product_id = 23;
UPDATE product_batches SET remaining_quantity = remaining_quantity - 0 WHERE product_id = 24;
UPDATE product_batches SET remaining_quantity = remaining_quantity - 0 WHERE product_id = 25;
UPDATE product_batches SET remaining_quantity = remaining_quantity - 0 WHERE product_id = 26;
UPDATE product_batches SET remaining_quantity = remaining_quantity - 0 WHERE product_id = 27;
UPDATE product_batches SET remaining_quantity = remaining_quantity - 0 WHERE product_id = 28;
UPDATE product_batches SET remaining_quantity = remaining_quantity - 31 WHERE product_id = 29;
UPDATE product_batches SET remaining_quantity = remaining_quantity - 0 WHERE product_id = 30;
UPDATE product_batches SET remaining_quantity = remaining_quantity - 12 WHERE product_id = 31;
UPDATE product_batches SET remaining_quantity = remaining_quantity - 0 WHERE product_id = 32;
UPDATE product_batches SET remaining_quantity = remaining_quantity - 0 WHERE product_id = 33;
UPDATE product_batches SET remaining_quantity = remaining_quantity - 0 WHERE product_id = 34;
UPDATE product_batches SET remaining_quantity = remaining_quantity - 0 WHERE product_id = 35;
UPDATE product_batches SET remaining_quantity = remaining_quantity - 7 WHERE product_id = 36;
UPDATE product_batches SET remaining_quantity = remaining_quantity - 0 WHERE product_id = 37;
UPDATE product_batches SET remaining_quantity = remaining_quantity - 0 WHERE product_id = 38;
UPDATE product_batches SET remaining_quantity = remaining_quantity - 15 WHERE product_id = 39;
UPDATE product_batches SET remaining_quantity = remaining_quantity - 12 WHERE product_id = 40;
UPDATE product_batches SET remaining_quantity = remaining_quantity - 0 WHERE product_id = 41;
UPDATE product_batches SET remaining_quantity = remaining_quantity - 0 WHERE product_id = 42;
UPDATE product_batches SET remaining_quantity = remaining_quantity - 3 WHERE product_id = 43;
UPDATE product_batches SET remaining_quantity = remaining_quantity - 3 WHERE product_id = 44;
UPDATE product_batches SET remaining_quantity = remaining_quantity - 2 WHERE product_id = 45;
UPDATE product_batches SET remaining_quantity = remaining_quantity - 0 WHERE product_id = 46;
UPDATE product_batches SET remaining_quantity = remaining_quantity - 0 WHERE product_id = 47;
UPDATE product_batches SET remaining_quantity = remaining_quantity - 0 WHERE product_id = 48;
UPDATE product_batches SET remaining_quantity = remaining_quantity - 0 WHERE product_id = 49;
UPDATE product_batches SET remaining_quantity = remaining_quantity - 0 WHERE product_id = 50;
UPDATE product_batches SET remaining_quantity = remaining_quantity - 0 WHERE product_id = 51;
UPDATE product_batches SET remaining_quantity = remaining_quantity - 0 WHERE product_id = 52;

-- ============================================================
-- 9. STOCK MOVEMENTS
-- ============================================================
INSERT INTO stock_movements (product_id, type, quantity, reference, notes, created_at) VALUES
(1, 'IN', 50, 'PUR-2026-03-24-001', 'Purchase', '2026-03-24 08:30:00'),
(2, 'IN', 40, 'PUR-2026-03-24-001', 'Purchase', '2026-03-24 08:30:00'),
(3, 'IN', 30, 'PUR-2026-03-24-001', 'Purchase', '2026-03-24 08:30:00'),
(46, 'IN', 100, 'PUR-2026-03-24-001', 'Purchase', '2026-03-24 08:30:00'),
(48, 'IN', 40, 'PUR-2026-03-25-001', 'Purchase', '2026-03-25 09:15:00'),
(49, 'IN', 50, 'PUR-2026-03-25-001', 'Purchase', '2026-03-25 09:15:00'),
(50, 'IN', 100, 'PUR-2026-03-25-001', 'Purchase', '2026-03-25 09:15:00'),
(13, 'IN', 15, 'PUR-2026-03-26-001', 'Purchase', '2026-03-26 07:45:00'),
(14, 'IN', 15, 'PUR-2026-03-26-001', 'Purchase', '2026-03-26 07:45:00'),
(15, 'IN', 12, 'PUR-2026-03-26-001', 'Purchase', '2026-03-26 07:45:00'),
(29, 'IN', 60, 'PUR-2026-03-26-001', 'Purchase', '2026-03-26 07:45:00'),
(30, 'IN', 40, 'PUR-2026-03-26-001', 'Purchase', '2026-03-26 07:45:00'),
(4, 'IN', 60, 'PUR-2026-03-27-001', 'Purchase', '2026-03-27 08:00:00'),
(5, 'IN', 30, 'PUR-2026-03-27-001', 'Purchase', '2026-03-27 08:00:00'),
(6, 'IN', 25, 'PUR-2026-03-27-001', 'Purchase', '2026-03-27 08:00:00'),
(47, 'IN', 100, 'PUR-2026-03-27-001', 'Purchase', '2026-03-27 08:00:00'),
(51, 'IN', 50, 'PUR-2026-03-27-001', 'Purchase', '2026-03-27 08:00:00'),
(36, 'IN', 60, 'PUR-2026-03-28-001', 'Purchase', '2026-03-28 10:30:00'),
(37, 'IN', 50, 'PUR-2026-03-28-001', 'Purchase', '2026-03-28 10:30:00'),
(39, 'IN', 80, 'PUR-2026-03-28-001', 'Purchase', '2026-03-28 10:30:00'),
(1, 'OUT', 30, 'SAL-2026-03-24-001', 'Sales', '2026-03-24 10:15:00'),
(2, 'OUT', 35, 'SAL-2026-03-24-002', 'Sales', '2026-03-24 14:30:00'),
(3, 'OUT', 15, 'SAL-2026-03-26-001', 'Sales', '2026-03-26 10:00:00'),
(4, 'OUT', 20, 'SAL-2026-03-25-001', 'Sales', '2026-03-25 09:45:00'),
(5, 'OUT', 10, 'SAL-2026-03-27-002', 'Sales', '2026-03-27 17:45:00'),
(10, 'OUT', 10, 'SAL-2026-03-27-001', 'Sales', '2026-03-27 12:15:00'),
(13, 'OUT', 16, 'SAL-2026-03-24-002', 'Sales', '2026-03-24 14:30:00'),
(14, 'OUT', 11, 'SAL-2026-03-25-002', 'Sales', '2026-03-25 11:20:00'),
(15, 'OUT', 5, 'SAL-2026-03-28-001', 'Sales', '2026-03-28 09:30:00'),
(19, 'OUT', 4, 'SAL-2026-03-25-001', 'Sales', '2026-03-25 09:45:00'),
(22, 'OUT', 3, 'SAL-2026-03-26-002', 'Sales', '2026-03-26 15:30:00'),
(23, 'OUT', 4, 'SAL-2026-03-26-002', 'Sales', '2026-03-26 15:30:00'),
(29, 'OUT', 31, 'SAL-2026-03-26-001', 'Sales', '2026-03-26 10:00:00'),
(31, 'OUT', 12, 'SAL-2026-03-27-002', 'Sales', '2026-03-27 17:45:00'),
(36, 'OUT', 7, 'SAL-2026-03-25-001', 'Sales', '2026-03-25 09:45:00'),
(39, 'OUT', 15, 'SAL-2026-03-27-001', 'Sales', '2026-03-27 12:15:00'),
(40, 'OUT', 12, 'SAL-2026-03-28-002', 'Sales', '2026-03-28 14:00:00'),
(43, 'OUT', 3, 'SAL-2026-03-24-002', 'Sales', '2026-03-24 14:30:00'),
(44, 'OUT', 3, 'SAL-2026-03-28-001', 'Sales', '2026-03-28 09:30:00'),
(45, 'OUT', 2, 'SAL-2026-03-28-002', 'Sales', '2026-03-28 14:00:00');

-- ============================================================
-- DATA LOADED SUCCESSFULLY
-- ============================================================
