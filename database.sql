-- CampusGigs Database Schema

-- Table 1: Users
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    university_email VARCHAR(255) UNIQUE NOT NULL,
    university_id VARCHAR(100),
    skill VARCHAR(255),
    skills TEXT,
    mobile VARCHAR(50),
    password_hash VARCHAR(255) NOT NULL,
    university VARCHAR(255) DEFAULT 'Daffodil International University',
    avatar VARCHAR(255) DEFAULT 'default.jpg',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table 2: Jobs
CREATE TABLE IF NOT EXISTS jobs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category VARCHAR(255) NOT NULL,
    budget DECIMAL(10,2) NOT NULL,
    deadline DATE NOT NULL,
    time VARCHAR(100),
    description TEXT NOT NULL,
    location VARCHAR(255) DEFAULT 'Campus',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_budget (budget)
);

-- Table 3: Job Applications
CREATE TABLE IF NOT EXISTS job_applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    job_id INT NOT NULL,
    user_id INT,
    user_name VARCHAR(255),
    user_email VARCHAR(255),
    cover_letter TEXT,
    status VARCHAR(50) DEFAULT 'pending',
    applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_job_id (job_id)
);

-- Seed data for testing
INSERT INTO users (name, university_email, university_id, skill, skills, mobile, password_hash, university) VALUES
('Abir Rahman', 'abir.rahman@diu.edu.bd', '201-15-1234', 'Coding', 'PHP, JavaScript, SQL', '01712345678', '$2y$10$w85oZsnwN2n3L1H0jK2zIu0.mZ73/1G.nFwQ93J2N326N31d3t2nC', 'Daffodil International University'),
('Fahmida Islam', 'fahmida.islam@diu.edu.bd', '201-15-5678', 'Design', 'Photoshop, Illustrator', '01812345678', '$2y$10$w85oZsnwN2n3L1H0jK2zIu0.mZ73/1G.nFwQ93J2N326N31d3t2nC', 'Daffodil International University');

INSERT INTO jobs (category, budget, deadline, time, description, location) VALUES
('assignment_research', 15.00, '2026-03-15', '23:59:00', 'Need help with writing a research paper bibliography in APA style.', 'Online/Library'),
('graphic_design', 25.00, '2026-03-12', '18:00:00', 'Design an event poster for the upcoming CSE department programming contest.', 'CSE Dept Office'),
('tech_coding', 50.00, '2026-03-20', '12:00:00', 'Develop a simple database connection script and SQL tables for a class project.', 'Campus Lab');
