<?php 
    // Constants used throughout project

    /**
     * ASSUMPTIONS:
     * - For testing purposes, all credentials will be made publicly visible on the repository
     *      - Encryption (e.g. AWS's KMS) needs to be applied during actual rollout. 
     *      - For convenience, DB credentials are kept the same for this assignment (needs to be different for better security)
     * - Assuming database credentials already exist and/or is created/managed outside of this assignment scope
     * - 
     */
    
    define('TITLE', 'Performance Review System DEMO');

    define('CSS_VERSION',       '0.001-20201017'); 
    define('TIMESTAMP_NOW',     date("Y-m-d H:i:s"));

    // Database Credentials
    define('SERVER_NAME',       'localhost');
    define('DB_NAME',           'db_performance_reviews');
    define('DB_USERNAME',       'db_performance_reviews');
    define('DB_PASSWORD',       'password');

    // Database Queries (Default Creation)
    define('QUERY_CREATE_TABLE_STAFF', "CREATE TABLE staff (
        id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
        username VARCHAR(32) UNIQUE NOT NULL, 
        password VARCHAR(255) NOT NULL, 
        first_name VARCHAR(60) NOT NULL, 
        last_name VARCHAR(60) NOT NULL, 
        email VARCHAR(50), 
        is_admin BOOLEAN, 
        date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
        date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );");
    define('QUERY_CREATE_TABLE_REVIEWS', "CREATE TABLE reviews (
        id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
        staff_id INT(10) UNSIGNED NOT NULL, 
        review_group_id INT(10) UNSIGNED DEFAULT NULL, 
        review_contents VARCHAR(65536), 
        date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
        date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );");
    define('QUERY_CREATE_TABLE_REVIEW_ASSIGMENTS', "CREATE TABLE review_feedback (
        id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
        review_id INT(10) UNSIGNED NOT NULL, 
        feedback_staff_id INT(10) UNSIGNED NOT NULL, 
        feedback_content VARCHAR(65536), 
        feedback_date_completed TIMESTAMP NULL, 
        feedback_date_last_updated TIMESTAMP NULL, 
        assigner_staff_id INT(10) UNSIGNED NOT NULL, 
        assigner_staff_comment VARCHAR(65536), 
        date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
        date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );");
    // Create default admin account (ID is always 1)
    define('QUERY_INSERT_ADMIN_ACCOUNT', "INSERT INTO staff 
         (id, username, password, first_name, last_name, email, is_admin) 
         VALUES (1, 'admin', '".sha1('password')."', 'Admin', 'Account', 'admin@someaddress.com', true);
    ");

    define('ADMIN_ALLOWED_TYPES', ['review', 'review_feedback', 'staff']);
    define('ADMIN_ALLOWED_ACTIONS', ['add', 'edit', 'delete']);

    define('USER_ALLOWED_TYPES', ['review_feedback', 'staff']);

    define('REVIEW_CHARACTER_PREVIEW_LIMIT', 50);