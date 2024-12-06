-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 02, 2024 at 06:06 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--

-- Database: `online_job_portal`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `type` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 for client, 1 for admin',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


--
-- Dumping data for table `users`
--

INSERT INTO `users` (`name`, `email`, `password`, `phone_number`, `type`) VALUES
('John Doe', 'john.doe@example.com', 'hashedpassword1', '1234567890', 0),
('Jane Smith', 'jane.smith@example.com', 'hashedpassword2', '0987654321', 1);

-- --------------------------------------------------------

CREATE TABLE `companies` (
  `company_id` int(11) NOT NULL AUTO_INCREMENT,
  `company_name` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL UNIQUE,
  `address` text NOT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Table structure for table `companies`
--


--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`company_name`, `email`, `address`, `contact_number`) VALUES
('Tech Corp', 'contact@techcorp.com', '1234 Tech Street, Tech City', '1234567890'),
('Innovative Solutions', 'info@innovativesol.com', '5678 Innovation Lane, Tech Valley', '0987654321');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `job_id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `job_title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `location` varchar(255) NOT NULL,
  `salary` decimal(10,2) DEFAULT NULL,
  `posted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`job_id`),
  FOREIGN KEY (`company_id`) REFERENCES `companies`(`company_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



--
--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`company_id`, `job_title`, `description`, `location`, `salary`) VALUES
(1, 'Software Engineer', 'Develop and maintain software applications.', 'Tech City', 75000.00),
(2, 'Data Analyst', 'Analyze data trends and generate reports.', 'Tech Valley', 65000.00);

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `application_id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Unique ID for each application',
  `user_id` INT(11) NOT NULL COMMENT 'ID of the user applying for the job, references users table',
  `job_id` INT(11) NOT NULL COMMENT 'ID of the job being applied to, references jobs table',
  `cover_letter` TEXT DEFAULT NULL COMMENT 'Optional cover letter submitted with the application',
  `applied_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Timestamp when the application was submitted',
  `status` ENUM('pending', 'reviewed', 'accepted', 'rejected') DEFAULT 'pending' COMMENT 'Status of the job application',
  PRIMARY KEY (`application_id`),
  CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_job_id` FOREIGN KEY (`job_id`) REFERENCES `jobs`(`job_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_job_id` (`job_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE applications
ADD COLUMN company_id INT(11) NOT NULL COMMENT 'ID of the company associated with the job application',
ADD FOREIGN KEY (company_id) REFERENCES companies(company_id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE applications
ADD COLUMN resume_file VARCHAR(255) DEFAULT NULL COMMENT 'Path or filename of the uploaded resume file';


--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`user_id`, `job_id`, `company_id`, `cover_letter`, `resume_file`, `status`) VALUES
(1, 1, 1, 'I am excited to apply for the Software Engineer position...', 'resume1.pdf', 'pending'),
(2, 2, 2, 'I would like to be considered for the Data Analyst role...', 'resume2.pdf', 'pending');


-- --------------------------------------------------------

--
-- Table structure for table `feedbacks`
--
CREATE TABLE `feedbacks` (
  `feedback_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `rating` int(1) NOT NULL CHECK (`rating` >= 1 AND `rating` <= 5),
  `comments` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`feedback_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`company_id`) REFERENCES `companies`(`company_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE feedbacks
ADD COLUMN job_id INT(11) NULL AFTER company_id,
ADD FOREIGN KEY (job_id) REFERENCES jobs(job_id) ON DELETE CASCADE;

--
-- Dumping data for table `feedbacks`
--

INSERT INTO `feedbacks` (`user_id`, `company_id`,`job_id`, `rating`, `comments`) VALUES
(1, 1,2, 5, 'Excellent company to work with!'),
(2, 2,1, 4, 'Great experience, but there is room for improvement.');

-- --------------------------------------------------------

ALTER TABLE `companies`
ADD COLUMN `logo` VARCHAR(255) DEFAULT NULL AFTER `contact_number`,
ADD COLUMN `description` TEXT DEFAULT NULL AFTER `logo`;


COMMIT;
