-- CampusGigs Database Schema
CREATE DATABASE IF NOT EXISTS campusgigs;
USE campusgigs;

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
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
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
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE
);
