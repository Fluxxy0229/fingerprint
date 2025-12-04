CREATE TABLE `senior_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) NOT NULL,
  `age` int(11) NOT NULL,
  `address` varchar(100) NOT NULL,
  `contact_number` varchar(20) NOT NULL,
  `birthdate` date DEFAULT NULL,
  `gender` enum('Male','Female','Prefer not to say') NOT NULL,
  `civil_status` enum('Single','Married','Separated','Widowed') NOT NULL,
  `contact_person` varchar(100) DEFAULT NULL,
  `contact_person_number` varchar(20) DEFAULT NULL,
  `contact_person_relation` enum('Spouse','Parent','Child','Sibling','Grandparent','Aunt/Uncle','Cousin') NOT NULL,
  `senior_biometric_data` boolean NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Admin','Staff') DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(1, 'BBSAISC Admin', '$2y$10$qyBawxB.oByBpTZne0vC4uZ2ZxRPSf8akU0mf5bKAYBhWIe8U46RG', 'Admin'),
(2, 'Mateo Drug Store', '$2y$10$JwZEQ0PX2CEq9vvqzMG5KOdGz29cpAmDiFPuBrxVH1WApTmxEwsRW', 'Staff');

CREATE TABLE `user_activities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `action` varchar(80) NOT NULL,
  `details` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `created_at` (`created_at`),
  CONSTRAINT `fk_user_activity_user`
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
