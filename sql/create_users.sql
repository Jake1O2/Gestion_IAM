-- SQL script to create a basic `users` table for the IAM school management site
-- Run in MySQL (phpMyAdmin or CLI) against the `gestion_system` database

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(150) NOT NULL,
  `email` VARCHAR(200) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` VARCHAR(50) NOT NULL DEFAULT 'student',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Example insert (change password as needed):
-- INSERT INTO `users` (`name`,`email`,`password`,`role`) VALUES ('Admin','admin@iam.test', '<hashed_password>', 'admin');
