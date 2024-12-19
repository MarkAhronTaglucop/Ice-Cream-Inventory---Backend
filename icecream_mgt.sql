-- User Management Roles
-- Create roles with login and passwords
CREATE ROLE admin WITH LOGIN PASSWORD 'admin_password';
CREATE ROLE customer WITH LOGIN PASSWORD 'customer_password';
CREATE ROLE employee WITH LOGIN PASSWORD 'employee_password';

-- Grant privileges to admin role
GRANT CONNECT ON DATABASE "Bernie" TO admin;
GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA public TO admin;
GRANT USAGE, SELECT ON ALL SEQUENCES IN SCHEMA public TO admin;
GRANT EXECUTE ON ALL FUNCTIONS IN SCHEMA public TO admin;

ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL ON TABLES TO admin;
ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT USAGE, SELECT ON SEQUENCES TO admin;
ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT EXECUTE ON FUNCTIONS TO admin;

-- Grant privileges to customer role
GRANT CONNECT ON DATABASE "Bernie" TO customer;
GRANT SELECT ON public.roles, public.users, public.icecreams,public.prices,public.stocks TO customer;
GRANT SELECT ON public.available_icecreams, public.inventory_summary TO customer; 
GRANT EXECUTE ON FUNCTION public.SearchIceCreamByName TO customer;


-- Grant privileges to employee role
GRANT CONNECT ON DATABASE "Bernie" TO employee;
GRANT SELECT ON public.roles, public.users, public.icecreams,public.prices,public.stocks TO employee;
GRANT INSERT, UPDATE, DELETE ON public.icecreams,public.prices TO employee;
GRANT INSERT ON activity_logs TO employee;
GRANT SELECT ON all_icecreams TO employee;
GRANT USAGE, UPDATE ON SEQUENCE public.icecreams_id_seq, public.activity_logs_id_seq,public.prices_id_seq TO employee;
GRANT EXECUTE ON FUNCTION public.SearchIceCreamByName, public.add_icecreams, public.edit_icecreams TO employee;




--Triggers on all tables if changes are made
CREATE OR REPLACE TRIGGER user_changes
AFTER INSERT OR UPDATE OR DELETE ON users
FOR EACH ROW 
EXECUTE FUNCTION log_activity_changes();

CREATE OR REPLACE TRIGGER icecream_changes
AFTER INSERT OR UPDATE OR DELETE ON icecreams
FOR EACH ROW 
EXECUTE FUNCTION log_activity_changes();

CREATE OR REPLACE TRIGGER prices_changes
AFTER INSERT OR UPDATE OR DELETE ON prices
FOR EACH ROW 
EXECUTE FUNCTION log_activity_changes();

CREATE OR REPLACE TRIGGER stocks_changes
AFTER INSERT OR UPDATE OR DELETE ON stocks
FOR EACH ROW 
EXECUTE FUNCTION log_activity_changes();

--Functions for logging actions
CREATE OR REPLACE FUNCTION log_activity_changes()
RETURNS TRIGGER AS $$
BEGIN
  IF TG_OP = 'DELETE' THEN
      INSERT INTO activity_logs (user_id, table_name, action, created_at, user_type)
      VALUES (COALESCE(current_setting('myapp.user_id', true)::integer, 0), TG_TABLE_NAME, TG_OP, NOW(), CURRENT_USER);
  ELSE
      INSERT INTO activity_logs (user_id, table_name, action, created_at, user_type)
      VALUES (COALESCE(current_setting('myapp.user_id', true)::integer, 0), TG_TABLE_NAME, TG_OP, NOW(), CURRENT_USER);
  END IF;
  RETURN NEW;  
END;
$$ LANGUAGE plpgsql;


--index utilized to speed up query process
CREATE INDEX idx_icecream_name ON icecreams(name);
CREATE INDEX idx_stock_id ON stocks(id);
CREATE INDEX idx_users_id ON users(id);



--All the functions used by admin give or take haha
--CAN VIEW ALL ACTIVITY LOGS 
--CAN UPDATE THE ICECREAM STOCKS
--CAN VIEW TOTAL TYPES OF ICECREAM

--materialized view on logs
CREATE MATERIALIZED VIEW all_activity_logs AS 
SELECT 
    ul.id AS log_id,        
    ul.user_id AS users_id, 
    u.name AS username,      
    ul.table_name,          
    ul.action,               
    ul.created_at AS time, 
    ul.user_type AS role 
FROM 
    activity_logs ul
LEFT JOIN 
    users u ON ul.user_id = u.id;
	
REFRESH MATERIALIZED VIEW all_activity_logs
ALTER MATERIALIZED VIEW all_activity_logs OWNER TO admin;


-- summary of all
CREATE OR REPLACE VIEW inventory_summary AS
SELECT
    (SELECT COUNT(*) FROM users WHERE role_id = 1) AS total_admin,
    (SELECT COUNT(*) FROM users WHERE role_id = 2) AS total_emloyee,
    (SELECT COUNT(*) FROM users WHERE role_id = 3) AS total_customer,
	(SELECT COUNT(*) FROM users) AS total_users,
    (SELECT COUNT(*) FROM icecreams) AS total_icecream
	
SELECT * FROM inventory_summary;


--All the functions or views used by customers give or take haha
--CAN VIEW ALL THE AVAILABLE ICECREAM
CREATE OR REPLACE VIEW available_icecreams AS
SELECT 
    i.id AS icecream_id,
    i.name AS icecream_name,
    i.description,
    i.manufactured_date,
	i.image_url,
    s.stock,
    s.status,
    p.price
FROM 
    icecreams i
JOIN 
    stocks s ON i.id = s.icecream_id
JOIN 
    prices p ON i.id = p.icecream_id
WHERE 
    s.status = 'Available';
	
--All the functions used by employeed give or take haha
--ADD AN ICECREAM VARIANT
--EDIT ICE CREAM VARIANT

CREATE OR REPLACE VIEW all_icecreams AS
SELECT 
    i.id AS icecream_id,
    i.name AS icecream_name,
    i.description,
    i.manufactured_date,
	i.image_url,
    s.stock,
    s.status,
    p.price
FROM 
    icecreams i
JOIN 
    stocks s ON i.id = s.icecream_id
JOIN 
    prices p ON i.id = p.icecream_id


select*from all_icecreams


--add functionality
select add_icecreams('booberry','my boo is cute','2024-12-03',5,'https://yawaka123')
CREATE OR REPLACE FUNCTION add_icecreams(
    p_name TEXT,
    p_description TEXT,
    p_manufactured_date DATE,
    p_price NUMERIC(8, 2),
    p_image_url VARCHAR(255)  -- Added parameter for the image URL
)
RETURNS TEXT AS $$
DECLARE
    new_icecream_id INT;
BEGIN
    -- Check if ice cream name already exists
    IF EXISTS (SELECT 1 FROM icecreams WHERE name = p_name) THEN
        RETURN 'Error: Ice cream with this name already exists.';
    END IF;

    -- Insert into the icecreams table, including the image_url
    INSERT INTO icecreams (name, description, manufactured_date, image_url, created_at, updated_at)
    VALUES (p_name, p_description, p_manufactured_date, p_image_url, NOW(), NOW())
    RETURNING id INTO new_icecream_id;

    -- Insert into the prices table
    INSERT INTO prices (icecream_id, price, created_at, updated_at)
    VALUES (new_icecream_id, p_price, NOW(), NOW());

    -- Insert into the stocks table with default stock = 1 and status = 'Available'
    INSERT INTO stocks (icecream_id, stock, status, created_at, updated_at)
    VALUES (new_icecream_id, 1, 'Available', NOW(), NOW());

    -- Confirm successful insertion
    RETURN 'Ice cream added successfully with ID: ' || new_icecream_id;
END;
$$ LANGUAGE plpgsql;
SELECT edit_icecreams(
    1,                                    -- Ice cream ID (replace with a valid ID from your database)
    'Updated Ice Cream Name',             -- Name (replace with a test name)
    'Updated Description',                -- Description (replace with a test description)
    '2024-12-10',                         -- Manufactured date (replace with a test date)
    12.99,                                -- Price (replace with a test price)
    'http://example.com/updated_image.jpg' -- Image URL (replace with a test image URL)
);


--edit funtionality for icecream
CREATE OR REPLACE FUNCTION edit_icecreams(
    p_icecream_id INT,
    p_name TEXT,
    p_description TEXT,
    p_manufactured_date DATE,
    p_price NUMERIC(8, 2),
    p_image_url VARCHAR(255) -- Updated to use VARCHAR with a length of 255
)
RETURNS TEXT AS $$
BEGIN
    -- Check if ice cream with the given ID exists
    IF NOT EXISTS (SELECT 1 FROM icecreams WHERE id = p_icecream_id) THEN
        RETURN 'Error: Ice cream with this ID does not exist.';
    END IF;

    -- Update the icecreams table
    UPDATE icecreams
    SET name = p_name,
        description = p_description,
        manufactured_date = p_manufactured_date,
        image_url = p_image_url, -- Update the image URL
        updated_at = NOW()
    WHERE id = p_icecream_id;

    -- Update the prices table
    UPDATE prices
    SET price = p_price,
        updated_at = NOW()
    WHERE icecream_id = p_icecream_id;

    -- Return success message
    RETURN 'Ice cream updated successfully with ID: ' || p_icecream_id;
END;
$$ LANGUAGE plpgsql;
select*from all_icecreams





--funtion that only admins can add  stocks
CREATE OR REPLACE FUNCTION update_icecream_stock(
    p_icecream_id INT, 
    p_new_stock INT
)
RETURNS TEXT AS $$
BEGIN
    UPDATE stocks
    SET stock = p_new_stock,
        status = CASE WHEN p_new_stock > 0 THEN 'Available' ELSE 'Out of Stock' END,
        updated_at = NOW()
    WHERE icecream_id = p_icecream_id;

    RETURN 'Stock updated successfully.';
END;
$$ LANGUAGE plpgsql;

SELECT update_icecream_stock(2, 50);



--function to delete all the icecream
CREATE OR REPLACE FUNCTION delete_icecream(p_icecream_id INT)
RETURNS TEXT AS $$
BEGIN
    -- Delete from prices table
    DELETE FROM prices
    WHERE icecream_id = p_icecream_id;

    -- Delete from stocks table
    DELETE FROM stocks
    WHERE icecream_id = p_icecream_id;

    -- Delete from icecreams table
    DELETE FROM icecreams
    WHERE id = p_icecream_id;

    -- Return success message
    RETURN 'Ice cream and its related data deleted successfully.';
END;
$$ LANGUAGE plpgsql;
 
SELECT delete_icecream(15); 
select*from all_icecreams


--search query
CREATE OR REPLACE FUNCTION SearchIceCreamByName(NameSearch VARCHAR)
    RETURNS TABLE (
        ID BIGINT,          
        NAME VARCHAR,
        DESCRIPTION TEXT,
        MANUFACTURED_DATE DATE,
		IMAGE_URL VARCHAR,
        PRICE NUMERIC(8, 2),
        STOCK INT,
        STATUS VARCHAR
    ) AS $$
BEGIN
    RETURN QUERY
    SELECT 
        i.ID,                               
        i.NAME,
        i.DESCRIPTION,
        i.MANUFACTURED_DATE, 
		i.image_url,
        p.PRICE,
        s.STOCK,
        s.STATUS
    FROM 
        ICECREAMS i
    JOIN 
        PRICES p ON p.ICECREAM_ID = i.ID
    JOIN 
        STOCKS s ON s.ICECREAM_ID = i.ID
    WHERE 
        i.NAME ILIKE '%' || NameSearch || '%'; 
END;
$$ LANGUAGE plpgsql;


select SearchIceCreamByName('straw')
























	
-- Create and Insert Queries for Roles
-- Insert roles into the roles table
INSERT INTO roles 
VALUES 
    (1, 'admin', NOW(), NOW()),
    (2, 'Employee', NOW(), NOW()),
    (3, 'Customer', NOW(), NOW());

-- Insert sample data into the icecream table
INSERT INTO icecreams (name, description, manufactured_date, image_url) 
VALUES 
('Chocolate Fudge', 'A rich and creamy chocolate ice cream with fudge chunks.', '2024-12-01', 'https://i.imgur.com/someimage.jpg'),
('Vanilla Dream', 'Classic vanilla ice cream made with real vanilla beans.', '2024-12-05', 'https://i.imgur.com/anotherimage.jpg'),
('Strawberry Swirl', 'A sweet and tangy strawberry ice cream with a strawberry swirl.', '2024-11-25', 'https://i.imgur.com/yetanotherimage.jpg'),
('Mint Chocolate Chip', 'Refreshing mint ice cream with crunchy chocolate chips.', '2024-11-15', 'https://i.imgur.com/moreimages.jpg'),
('Coffee Crunch', 'A bold coffee ice cream with crispy caramelized crunch.', '2024-10-30', 'https://i.imgur.com/moreimages.jpg');

INSERT INTO prices (id, icecream_id, price, created_at, updated_at) VALUES
(1, 1, 2.99, NOW(), NOW()),
(2, 2, 3.49, NOW(), NOW()),
(3, 3, 3.19, NOW(), NOW()),
(4, 4, 2.89, NOW(), NOW());

INSERT INTO stocks (id, icecream_id, stock, status, created_at, updated_at) VALUES
(1, 1, 50, 'Available', NOW(), NOW()),
(2, 2, 0, 'Out of Stock', NOW(), NOW()),
(3, 3, 30, 'Available', NOW(), NOW()),
(4, 4, 10, 'Available', NOW(), NOW());

-- Query the table to verify the data
SELECT * FROM icecreams;
