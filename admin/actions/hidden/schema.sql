-- ==========================================
-- 1. GEOGRAPHY MODULE (Scalability Layer)
-- ==========================================

CREATE TABLE `countries` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `sortname` VARCHAR(3) NOT NULL, -- e.g., 'NG', 'GH', 'KE'
    `name` VARCHAR(150) NOT NULL,
    `phonecode` INT NOT NULL -- e.g., 234
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `states` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(150) NOT NULL,
    `country_id` INT NOT NULL,
    FOREIGN KEY (`country_id`) REFERENCES `countries`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `cities` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(150) NOT NULL,
    `state_id` INT NOT NULL,
    FOREIGN KEY (`state_id`) REFERENCES `states`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==========================================
-- 2. AUTHENTICATION MODULE
-- ==========================================

CREATE TABLE `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `full_name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `role` ENUM('admin', 'company_owner', 'user') DEFAULT 'user',
    `avatar_url` VARCHAR(255) NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==========================================
-- 3. COMPANY CORE MODULE
-- ==========================================

CREATE TABLE `companies` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL, -- Owner of the listing
    `name` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE, -- For URL: techmedia.ng/company/paystack
    `tagline` VARCHAR(255) NULL, 
    `description` TEXT NULL,
    `logo_url` VARCHAR(255) NULL,
    `cover_image_url` VARCHAR(255) NULL,
    
    -- Contact & Location
    `email` VARCHAR(100) NULL,
    `phone` VARCHAR(50) NULL,
    `website_url` VARCHAR(255) NULL,
    `address` TEXT NULL,
    `city_id` INT NULL,
    `state_id` INT NULL, -- Added for faster filtering
    `country_id` INT NULL, -- Critical for multi-country scaling
    
    -- Business Metrics
    `team_size` VARCHAR(50) NULL, -- e.g., "50-100"
    `year_founded` YEAR NULL,
    `starting_cost` DECIMAL(15, 2) NULL, 
    `currency` VARCHAR(3) DEFAULT 'NGN',
    
    -- Status & Tiers
    `subscription_tier` ENUM('free', 'silver', 'gold') DEFAULT 'free',
    `is_verified` TINYINT(1) DEFAULT 0, -- The "Trust Badge"
    `is_featured` TINYINT(1) DEFAULT 0, -- Homepage placement
    
    -- Social & Stats (JSON for flexibility)
    `social_links` JSON NULL, 
    `profile_stats` JSON NULL, -- e.g. {"response_rate": 95}
    
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`),
    FOREIGN KEY (`city_id`) REFERENCES `cities`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==========================================
-- 4. CATEGORIES & PORTFOLIO
-- ==========================================

CREATE TABLE `categories` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL, -- e.g., "Fintech"
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `icon_class` VARCHAR(50) NULL -- e.g., "fas fa-wallet"
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Junction Table: Companies <-> Categories (Many-to-Many)
CREATE TABLE `company_categories` (
    `company_id` INT NOT NULL,
    `category_id` INT NOT NULL,
    PRIMARY KEY (`company_id`, `category_id`),
    FOREIGN KEY (`company_id`) REFERENCES `companies`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Portfolio Projects
CREATE TABLE `company_projects` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `company_id` INT NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT NULL,
    `image_url` VARCHAR(255) NULL,
    `completion_date` DATE NULL,
    `project_url` VARCHAR(255) NULL,
    FOREIGN KEY (`company_id`) REFERENCES `companies`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==========================================
-- 5. EVENTS MODULE
-- ==========================================

CREATE TABLE `events` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `organizer_company_id` INT NULL, -- Optional link to a listed company
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `description` TEXT NOT NULL,
    `event_type` ENUM('conference', 'hackathon', 'meetup', 'workshop', 'webinar') NOT NULL,
    
    `start_datetime` DATETIME NOT NULL,
    `end_datetime` DATETIME NOT NULL,
    
    `location_name` VARCHAR(255) NULL,
    `city_id` INT NULL,
    `is_virtual` TINYINT(1) DEFAULT 0,
    
    `price` DECIMAL(15, 2) DEFAULT 0.00,
    `ticket_url` VARCHAR(255) NULL,
    
    `is_featured` TINYINT(1) DEFAULT 0,
    `banner_image_url` VARCHAR(255) NULL,
    
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`organizer_company_id`) REFERENCES `companies`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==========================================
-- 6. NEWS & ARTICLES MODULE
-- ==========================================

CREATE TABLE `articles` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `author_id` INT NOT NULL, -- Admin or Editor
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `content` LONGTEXT NOT NULL,
    `featured_image_url` VARCHAR(255) NULL,
    `status` ENUM('draft', 'published') DEFAULT 'draft',
    `views` INT DEFAULT 0,
    `published_at` DATETIME NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`author_id`) REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tags for Articles
CREATE TABLE `tags` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `article_tags` (
    `article_id` INT NOT NULL,
    `tag_id` INT NOT NULL,
    PRIMARY KEY (`article_id`, `tag_id`),
    FOREIGN KEY (`article_id`) REFERENCES `articles`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`tag_id`) REFERENCES `tags`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Junction Table: Articles <-> Companies (Links news to company profiles)
CREATE TABLE `article_companies` (
    `article_id` INT NOT NULL,
    `company_id` INT NOT NULL,
    PRIMARY KEY (`article_id`, `company_id`),
    FOREIGN KEY (`article_id`) REFERENCES `articles`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`company_id`) REFERENCES `companies`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==========================================
-- 7. REVIEWS & ANALYTICS
-- ==========================================

CREATE TABLE `reviews` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `company_id` INT NOT NULL,
    `user_id` INT NOT NULL,
    `rating` INT NOT NULL CHECK (`rating` BETWEEN 1 AND 5),
    `comment` TEXT NULL,
    `is_approved` TINYINT(1) DEFAULT 0, -- Moderation queue
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`company_id`) REFERENCES `companies`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Daily Analytics (Tracks profile visits for the Company Dashboard)
CREATE TABLE `company_analytics` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `company_id` INT NOT NULL,
    `visit_date` DATE NOT NULL,
    `visit_count` INT DEFAULT 1,
    UNIQUE KEY `unique_daily_visit` (`company_id`, `visit_date`),
    FOREIGN KEY (`company_id`) REFERENCES `companies`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;