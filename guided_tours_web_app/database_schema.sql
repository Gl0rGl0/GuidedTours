-- Database Schema for Guided Tours Web App
-- Target DBMS: MySQL / MariaDB

-- Settings Table (for one-time and global configurations)
CREATE TABLE settings (
    setting_key VARCHAR(50) PRIMARY KEY,
    setting_value TEXT,
    description VARCHAR(255) NULL
);

-- Initial settings (can be inserted/updated via Configurator interface)
-- INSERT INTO settings (setting_key, setting_value, description) VALUES
-- ('territorial_scope', NULL, 'Geographical area covered by the organization'),
-- ('max_registration_size', '5', 'Max people per single user registration');

-- Users Table (Configurators, Volunteers, Fruitori)
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('configurator', 'volunteer', 'fruitore') NOT NULL,
    first_login BOOLEAN DEFAULT TRUE, -- To enforce password change
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
-- Note: Volunteer username should match their nickname from the requirements.

-- Places Table
CREATE TABLE places (
    place_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) UNIQUE NOT NULL COMMENT 'Unique identifier like "Parco Grotta Cascata del Varone"',
    description TEXT NULL COMMENT 'Additional information',
    location VARCHAR(255) NOT NULL COMMENT 'Address or coordinates',
    -- Assuming territorial_scope is checked application-side before insert
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Visit Types Table
CREATE TABLE visit_types (
    visit_type_id INT AUTO_INCREMENT PRIMARY KEY,
    place_id INT NOT NULL,
    title VARCHAR(255) NOT NULL COMMENT 'e.g., "Alla scoperta del Moretto"',
    description TEXT NULL,
    meeting_point VARCHAR(255) NOT NULL,
    period_start DATE NOT NULL COMMENT 'Date from which this type is programmable',
    period_end DATE NOT NULL COMMENT 'Date until which this type is programmable',
    -- Programmable weekdays stored application-side or as separate table if complex logic needed
    -- For simplicity here, assume check happens in PHP logic based on visit date
    start_time TIME NOT NULL,
    duration_minutes INT NOT NULL,
    requires_ticket BOOLEAN DEFAULT FALSE,
    min_participants INT NOT NULL,
    max_participants INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (place_id) REFERENCES places(place_id) ON DELETE CASCADE -- If place is deleted, its visit types are deleted
);
-- TODO: Add constraint/check to ensure non-overlapping times for same place/day in application logic

-- Volunteers <-> Visit Types Association (Many-to-Many)
CREATE TABLE volunteers_visit_types (
    user_id INT NOT NULL COMMENT 'Refers to a user with role=volunteer',
    visit_type_id INT NOT NULL,
    PRIMARY KEY (user_id, visit_type_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE, -- If volunteer user deleted, remove associations
    FOREIGN KEY (visit_type_id) REFERENCES visit_types(visit_type_id) ON DELETE CASCADE -- If visit type deleted, remove associations
);
-- Constraint: Ensure user_id refers to a user with role 'volunteer' (can be enforced by application logic or trigger)

-- Volunteer Availability Table
CREATE TABLE volunteer_availability (
    availability_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL COMMENT 'Refers to a user with role=volunteer',
    available_date DATE NOT NULL,
    month_year CHAR(7) NOT NULL COMMENT 'Format YYYY-MM, for indexing/querying',
    declared_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_availability (user_id, available_date), -- Volunteer can only declare once per date
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);
-- Index for faster lookup by month
CREATE INDEX idx_availability_month ON volunteer_availability(month_year);

-- Precluded Dates Table (Dates blocked by Configurator)
CREATE TABLE precluded_dates (
    precluded_date DATE PRIMARY KEY,
    reason VARCHAR(255) NULL,
    set_by_user_id INT NULL, -- Optional: track which configurator set it
    set_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (set_by_user_id) REFERENCES users(user_id) ON DELETE SET NULL -- Keep date even if configurator deleted
);

-- Visits Table (Specific scheduled instances)
CREATE TABLE visits (
    visit_id INT AUTO_INCREMENT PRIMARY KEY,
    visit_type_id INT NOT NULL,
    visit_date DATE NOT NULL,
    assigned_volunteer_id INT NULL, -- Assigned during planning (refers to user with role=volunteer)
    status ENUM('proposed', 'complete', 'confirmed', 'cancelled', 'effected') NOT NULL DEFAULT 'proposed',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- When the plan was generated
    status_updated_at TIMESTAMP NULL, -- When status last changed
    FOREIGN KEY (visit_type_id) REFERENCES visit_types(visit_type_id) ON DELETE CASCADE, -- If type deleted, cancel/delete instance? Or handle in logic.
    FOREIGN KEY (assigned_volunteer_id) REFERENCES users(user_id) ON DELETE SET NULL -- Keep visit record if volunteer deleted, but unassign
);
-- Index for faster lookup by date and status
CREATE INDEX idx_visit_date_status ON visits(visit_date, status);

-- Registrations Table (User bookings)
CREATE TABLE registrations (
    registration_id INT AUTO_INCREMENT PRIMARY KEY,
    visit_id INT NOT NULL,
    user_id INT NOT NULL COMMENT 'Refers to user with role=fruitore',
    num_participants INT NOT NULL,
    booking_code VARCHAR(20) UNIQUE NOT NULL, -- Unique code generated upon registration
    registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    cancelled_at TIMESTAMP NULL, -- Timestamp if cancelled
    FOREIGN KEY (visit_id) REFERENCES visits(visit_id) ON DELETE CASCADE, -- If visit cancelled/deleted, remove registrations
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE -- If user deleted, remove their registrations
);
-- Index for faster lookup by visit or user
CREATE INDEX idx_registration_visit ON registrations(visit_id);
CREATE INDEX idx_registration_user ON registrations(user_id);

-- Historical Archive (Could be a separate table or handled by keeping 'effected'/'cancelled' visits in the main table)
-- For simplicity, we keep them in the main 'visits' table with status 'effected' or 'cancelled'.
-- If performance becomes an issue, move old visits to an archive table.
