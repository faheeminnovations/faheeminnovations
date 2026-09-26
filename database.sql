-- Faheem Innovations Database Schema
-- Run this in phpMyAdmin or MySQL CLI

CREATE DATABASE IF NOT EXISTS faheem_innovations CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE faheem_innovations;

-- Users & Roles
CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    permissions JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role_id INT DEFAULT 2,
    status TINYINT DEFAULT 1,
    reset_token VARCHAR(100) NULL,
    reset_expires DATETIME NULL,
    last_login DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id)
);

-- Settings
CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    setting_group VARCHAR(50) DEFAULT 'general',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Pages
CREATE TABLE pages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    slug VARCHAR(200) NOT NULL UNIQUE,
    content LONGTEXT,
    meta_title VARCHAR(200),
    meta_description TEXT,
    meta_keywords TEXT,
    og_title VARCHAR(200),
    og_description TEXT,
    og_image VARCHAR(255),
    status TINYINT DEFAULT 1,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Services
CREATE TABLE services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    slug VARCHAR(150) NOT NULL UNIQUE,
    short_description TEXT,
    full_description LONGTEXT,
    icon VARCHAR(100),
    image VARCHAR(255),
    features JSON,
    cta_text VARCHAR(100),
    cta_url VARCHAR(255),
    meta_title VARCHAR(200),
    meta_description TEXT,
    status TINYINT DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- AI Tools
CREATE TABLE ai_tools (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    slug VARCHAR(150) NOT NULL UNIQUE,
    description TEXT,
    tool_url VARCHAR(255),
    icon VARCHAR(100),
    image VARCHAR(255),
    category VARCHAR(100),
    is_free TINYINT DEFAULT 1,
    price DECIMAL(10,2) DEFAULT 0,
    features JSON,
    button_text VARCHAR(100) DEFAULT 'Try Tool',
    status TINYINT DEFAULT 1,
    sort_order INT DEFAULT 0,
    meta_title VARCHAR(200),
    meta_description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Projects
CREATE TABLE projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    slug VARCHAR(200) NOT NULL UNIQUE,
    client VARCHAR(150),
    category VARCHAR(100),
    short_description TEXT,
    full_description LONGTEXT,
    technologies JSON,
    main_image VARCHAR(255),
    project_url VARCHAR(255),
    completion_date DATE,
    is_featured TINYINT DEFAULT 0,
    status TINYINT DEFAULT 1,
    sort_order INT DEFAULT 0,
    meta_title VARCHAR(200),
    meta_description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE project_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    image VARCHAR(255) NOT NULL,
    alt_text VARCHAR(200),
    sort_order INT DEFAULT 0,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
);

-- Process Steps
CREATE TABLE processes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    step_number INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    description TEXT,
    icon VARCHAR(100),
    status TINYINT DEFAULT 1,
    sort_order INT DEFAULT 0
);

-- Testimonials
CREATE TABLE testimonials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_name VARCHAR(100) NOT NULL,
    company VARCHAR(150),
    position VARCHAR(150),
    image VARCHAR(255),
    testimonial TEXT NOT NULL,
    rating TINYINT DEFAULT 5,
    status TINYINT DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- FAQs
CREATE TABLE faq_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    sort_order INT DEFAULT 0
);

CREATE TABLE faqs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question TEXT NOT NULL,
    answer LONGTEXT NOT NULL,
    category_id INT NULL,
    status TINYINT DEFAULT 1,
    sort_order INT DEFAULT 0,
    FOREIGN KEY (category_id) REFERENCES faq_categories(id) ON DELETE SET NULL
);

-- Enquiries
CREATE TABLE enquiries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(30),
    company VARCHAR(150),
    subject VARCHAR(200),
    message TEXT NOT NULL,
    source VARCHAR(100) DEFAULT 'contact_form',
    status ENUM('new','contacted','in_progress','converted','closed') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE enquiry_notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    enquiry_id INT NOT NULL,
    note TEXT NOT NULL,
    added_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (enquiry_id) REFERENCES enquiries(id) ON DELETE CASCADE,
    FOREIGN KEY (added_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Media
CREATE TABLE media (
    id INT AUTO_INCREMENT PRIMARY KEY,
    filename VARCHAR(255) NOT NULL,
    original_name VARCHAR(255),
    file_path VARCHAR(255) NOT NULL,
    file_type VARCHAR(50),
    file_size INT,
    alt_text VARCHAR(255),
    title VARCHAR(255),
    uploaded_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Navigation
CREATE TABLE menu_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    label VARCHAR(100) NOT NULL,
    url VARCHAR(255) NOT NULL,
    parent_id INT DEFAULT 0,
    target VARCHAR(20) DEFAULT '_self',
    status TINYINT DEFAULT 1,
    sort_order INT DEFAULT 0
);

-- Statistics
CREATE TABLE statistics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    value VARCHAR(20) NOT NULL,
    label VARCHAR(100) NOT NULL,
    sort_order INT DEFAULT 0
);

-- Social Links
CREATE TABLE social_links (
    id INT AUTO_INCREMENT PRIMARY KEY,
    platform VARCHAR(50) NOT NULL,
    url VARCHAR(255),
    icon VARCHAR(100),
    status TINYINT DEFAULT 1,
    sort_order INT DEFAULT 0
);

-- SEO Meta
CREATE TABLE seo_meta (
    id INT AUTO_INCREMENT PRIMARY KEY,
    page_identifier VARCHAR(100) NOT NULL UNIQUE,
    meta_title VARCHAR(200),
    meta_description TEXT,
    meta_keywords TEXT,
    og_title VARCHAR(200),
    og_description TEXT,
    og_image VARCHAR(255),
    robots VARCHAR(50) DEFAULT 'index,follow',
    canonical_url VARCHAR(255),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- =====================
-- DEFAULT DATA
-- =====================

INSERT INTO roles (name, permissions) VALUES
('super_admin', '["all"]'),
('admin', '["content","enquiries","media","settings"]'),
('editor', '["content","media"]');

INSERT INTO users (name, email, password, role_id) VALUES
('Super Admin', 'admin@faheeminnovations.online', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);
-- Default password: password (change immediately)

INSERT INTO settings (setting_key, setting_value, setting_group) VALUES
('site_name', 'Faheem Innovations', 'general'),
('site_tagline', 'Innovation starts with a vision.', 'general'),
('site_email', 'hello@faheeminnovations.online', 'general'),
('site_phone', '0304-1277320', 'general'),
('site_whatsapp', '923041277320', 'general'),
('site_address', 'Razzaqia Town Near Punjab Hotel, Faheem Innovations, Depalpur, Pakistan', 'general'),
('site_logo', 'images/main-logo.png', 'branding'),
('site_favicon', 'images/main-logo.png', 'branding'),
('primary_color', '#0B3C33', 'branding'),
('secondary_color', '#1a6b5a', 'branding'),
('google_analytics', '', 'analytics'),
('google_tag_manager', '', 'analytics'),
('google_maps_url', '', 'contact'),
('footer_description', 'Faheem Innovations builds modern websites, custom software, AI solutions and digital products designed around real business needs.', 'general');

INSERT INTO social_links (platform, url, icon, sort_order) VALUES
('Facebook', '', 'fab fa-facebook-f', 1),
('Instagram', '', 'fab fa-instagram', 2),
('LinkedIn', '', 'fab fa-linkedin-in', 3),
('YouTube', '', 'fab fa-youtube', 4),
('X', '', 'fab fa-x-twitter', 5),
('GitHub', '', 'fab fa-github', 6),
('WhatsApp', '', 'fab fa-whatsapp', 7);

INSERT INTO statistics (value, label, sort_order) VALUES
('50+', 'Projects & Solutions', 1),
('30+', 'Businesses & Clients', 2),
('10+', 'Digital Products', 3),
('5+', 'Years of Experience', 4);

INSERT INTO services (name, slug, short_description, full_description, icon, features, cta_text, cta_url, sort_order) VALUES
('Web Development', 'web-development', 'Professional, responsive and high-performance websites designed to establish a strong online presence.', 'Build a professional online presence with modern, responsive and high-performance websites.', 'fas fa-globe', '["Business websites","Corporate websites","Landing pages","Web portals","Custom websites","Responsive design","CMS integration","SEO-friendly development"]', 'Get Started', '/contact', 1),
('Software Development', 'software-development', 'Custom business software built around your workflows, operations and specific business requirements.', 'Build custom software around your business processes and operational requirements.', 'fas fa-code', '["Business management systems","CRM systems","ERP-style solutions","Inventory systems","Billing systems","Management dashboards","Reporting systems","Custom business platforms"]', 'Get Started', '/contact', 2),
('AI Solutions', 'ai-solutions', 'Practical AI-powered solutions that help businesses process information and improve productivity.', 'Use practical AI technology to create smarter digital products and improve everyday workflows.', 'fas fa-brain', '["AI-powered web applications","AI content tools","Document processing","Image processing","Speech processing","AI integrations","Custom AI solutions"]', 'Get Started', '/contact', 3),
('Digital Solutions', 'digital-solutions', 'End-to-end digital solutions that connect your business needs with modern technology.', 'Connect your business requirements with modern digital technology.', 'fas fa-laptop-code', '["Digital platforms","Web portals","Customer-facing systems","Internal business systems","Digital transformation solutions","Custom integrations"]', 'Get Started', '/contact', 4),
('Business Software', 'business-software', 'Purpose-built software for managing customers, operations, sales, records and day-to-day business activities.', 'Simplify business operations with software built around your processes.', 'fas fa-briefcase', '["Customer management","Product management","Sales management","Expense management","Invoice management","Reports","User management","Business dashboards"]', 'Get Started', '/contact', 5),
('Custom Applications', 'custom-applications', 'Web and application solutions designed specifically around your organization\'s requirements.', 'Need something specific? We build custom applications based on your unique requirements.', 'fas fa-puzzle-piece', '["Custom web applications","Management systems","Internal applications","Client portals","Data management systems","Custom dashboards"]', 'Get Started', '/contact', 6);

INSERT INTO ai_tools (name, slug, description, icon, category, is_free, features, button_text, sort_order) VALUES
('AI Image to Text', 'ai-image-to-text', 'Extract readable text from images, screenshots, scanned documents and photos.', 'fas fa-image', 'Image Tools', 1, '["Extract text from images","Support for screenshots","Scanned document processing","Photo text extraction"]', 'Try Tool', 1),
('AI Document Summariser', 'ai-document-summariser', 'Summarize documents and extract important information without reading every page manually.', 'fas fa-file-alt', 'Document Tools', 1, '["Document summarization","Key information extraction","Multiple format support","Fast processing"]', 'Try Tool', 2),
('AI Text to Image', 'ai-text-to-image', 'Turn written ideas and descriptions into visual images.', 'fas fa-paint-brush', 'Image Tools', 1, '["Text to image generation","Creative visuals","Multiple styles","High quality output"]', 'Try Tool', 3),
('AI Text to Speech', 'ai-text-to-speech', 'Convert written content into natural-sounding speech.', 'fas fa-volume-up', 'Audio Tools', 1, '["Natural voice synthesis","Multiple languages","Downloadable audio","Fast conversion"]', 'Try Tool', 4),
('AI Speech to Text', 'ai-speech-to-text', 'Convert spoken audio into written text quickly and conveniently.', 'fas fa-microphone', 'Audio Tools', 1, '["Audio transcription","Multiple formats","High accuracy","Fast processing"]', 'Try Tool', 5),
('AI Video HD', 'ai-video-hd', 'Create or process high-quality video content using modern AI-powered technology.', 'fas fa-video', 'Video Tools', 1, '["HD video processing","AI enhancement","Quality upscaling","Modern technology"]', 'Explore Tool', 6);

INSERT INTO processes (step_number, title, description, icon, sort_order) VALUES
(1, 'Discover', 'We understand your business, requirements, challenges and goals.', 'fas fa-search', 1),
(2, 'Plan', 'We define the project scope, features, structure and development approach.', 'fas fa-clipboard-list', 2),
(3, 'Design', 'We create a clean and user-friendly experience based on your business needs.', 'fas fa-pencil-ruler', 3),
(4, 'Develop', 'Our development team turns the approved concept into a working digital product.', 'fas fa-code', 4),
(5, 'Test', 'We test functionality, responsiveness, usability and overall performance.', 'fas fa-check-circle', 5),
(6, 'Launch', 'The completed solution is deployed and prepared for real users.', 'fas fa-rocket', 6),
(7, 'Support', 'We can continue improving and maintaining the product as your requirements evolve.', 'fas fa-headset', 7);

INSERT INTO faqs (question, answer, status, sort_order) VALUES
('What services does Faheem Innovations provide?', 'Faheem Innovations provides web development, software development, AI solutions, digital solutions, business software and custom application development.', 1, 1),
('Can you build custom software for my business?', 'Yes. We can develop software around your business processes, requirements and operational needs.', 1, 2),
('Do you develop AI-powered applications?', 'Yes. We develop practical AI-powered tools and applications for different business and digital use cases.', 1, 3),
('Do you build mobile-responsive websites?', 'Yes. Websites are designed to work across desktop, tablet and mobile devices.', 1, 4),
('Can you redesign an existing website?', 'Yes. Existing websites can be redesigned with a modern user experience, improved structure and responsive design.', 1, 5),
('How do I start a project?', 'Contact us through the website and share your requirements. Our team can then discuss the project scope and next steps.', 1, 6),
('Do you provide custom solutions?', 'Yes. Custom applications and software can be developed according to specific business requirements.', 1, 7),
('Do you provide support after development?', 'Support and further improvements can be discussed according to the project requirements.', 1, 8);

INSERT INTO menu_items (label, url, sort_order) VALUES
('Home', '/', 1),
('About', '/about', 2),
('Services', '/services', 3),
('AI Tools', '/ai-tools', 4),
('Projects', '/projects', 5),
('Process', '/process', 6),
('FAQs', '/faqs', 7),
('Our Clients', '/our-clients', 8),
('Contact', '/contact', 9);

INSERT INTO pages (title, slug, meta_title, meta_description, status) VALUES
('Home', 'home', 'Faheem Innovations - Web Development, Software & AI Solutions', 'Faheem Innovations creates modern websites, custom software, AI-powered tools and digital solutions designed to help businesses grow online.', 1),
('About', 'about', 'About Faheem Innovations - Technology Built Around Your Business', 'Learn about Faheem Innovations, a technology company focused on web development, software development, AI solutions and custom digital products.', 1),
('Services', 'services', 'Our Services - Web Development, Software, AI Solutions | Faheem Innovations', 'From websites to custom business software and AI-powered solutions, we build technology around your goals.', 1),
('AI Tools', 'ai-tools', 'AI Tools - Practical AI for Everyday Work | Faheem Innovations', 'Explore simple and useful AI-powered tools designed to help users handle common digital tasks faster.', 1),
('Projects', 'projects', 'Our Projects | Faheem Innovations', 'A selection of websites, software systems, AI tools and digital solutions developed by Faheem Innovations.', 1),
('Process', 'process', 'How We Work | Faheem Innovations', 'A structured process helps us transform your requirements into a reliable digital product.', 1),
('FAQ', 'faq', 'Frequently Asked Questions | Faheem Innovations', 'Find answers to common questions about Faheem Innovations services and development process.', 1),
('Contact', 'contact', 'Contact Faheem Innovations - Start Your Project', 'Have a project idea? Contact Faheem Innovations to discuss your web development, software or AI solution requirements.', 1);
