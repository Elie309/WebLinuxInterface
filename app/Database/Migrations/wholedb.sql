-- Users table
CREATE TABLE `users` (
  `user_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_fullname` varchar(100) UNIQUE NOT NULL,
  `user_email` varchar(255) UNIQUE NOT NULL,
  `user_password` varchar(255) NOT NULL,
  `user_role` enum('admin','user') DEFAULT 'user',
  `user_is_active` tinyint(1) DEFAULT 1,
  `user_created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `user_updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_deleted_at` TIMESTAMP DEFAULT NULL,
  `user_last_login` datetime DEFAULT NULL,
  PRIMARY KEY (`user_id`)
)

-- Servers table
CREATE TABLE `servers` (
  `server_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `server_name` varchar(100) NOT NULL,
  `server_hostname` varchar(255) NOT NULL,
  `server_ip_address` varchar(45) NOT NULL,
  `server_port` int(5) DEFAULT 22,
  `server_username` varchar(100) NOT NULL,
  `server_auth_type` enum('password','key') DEFAULT 'password',
  `server_password` varchar(255) DEFAULT NULL,
  `server_key_file` varchar(255) DEFAULT NULL,
  `server_description` text DEFAULT NULL,
  `server_status` enum('active','inactive','maintenance') DEFAULT 'active',
  `server_created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `server_updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`server_id`)
) 

-- Commands table
CREATE TABLE `commands` (
  `command_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `command_name` varchar(100) NOT NULL,
  `command_script` text NOT NULL,
  `command_description` text DEFAULT NULL,
  `command_category` varchar(50) DEFAULT NULL,
  `command_is_dangerous` tinyint(1) DEFAULT 0,
  `command_created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `command_updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`command_id`)
) 

-- Command logs table
CREATE TABLE `command_logs` (
  `log_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `log_user_id` int(11) UNSIGNED NOT NULL,
  `log_server_id` int(11) UNSIGNED NOT NULL,
  `log_command_id` int(11) UNSIGNED DEFAULT NULL,
  `custom_command` text DEFAULT NULL,
  `command_log_output` text DEFAULT NULL,
  `command_log_status` enum('success','error','running') DEFAULT 'running',
  `command_log_executed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `command_log_execution_time` float DEFAULT NULL,
  PRIMARY KEY (`log_id`),
  FOREIGN KEY (`log_user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  FOREIGN KEY (`log_server_id`) REFERENCES `servers` (`server_id`) ON DELETE CASCADE,
  FOREIGN KEY (`log_command_id`) REFERENCES `commands` (`command_id`) ON DELETE SET NULL
)

-- User-Server access permissions
CREATE TABLE `user_server_permissions` (
  `user_id` int(11) UNSIGNED NOT NULL,
  `server_id` int(11) UNSIGNED NOT NULL,
  `permissions` enum('read','execute','full') DEFAULT 'read',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`, `server_id`),
  CONSTRAINT `user_server_permissions_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_server_permissions_server_id_fk` FOREIGN KEY (`server_id`) REFERENCES `servers` (`id`) ON DELETE CASCADE
) 
