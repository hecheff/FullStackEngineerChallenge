<?php 
    // Common functions used and carried out at start of each page

    session_start();
    if(!isset($_SESSION['message'])) {
        $_SESSION['message'] = "";
    }

    // Call in default files
    include('settings.php');
    include('constants.php');

    // INITIAL RUN ONLY: Create database and tables with populated contents
    $GLOBALS['conn'] = new mysqli(SERVER_NAME, DB_USERNAME, DB_PASSWORD);
    if ($GLOBALS['conn']->connect_error) {
        die ("Database connection failed: " . $GLOBALS['conn']->connect_error);
    }
    // Check if database exists
    $tables = CheckIfTablesExist();
    if (!$tables) {
        include('_initial_run_constants.php');

        // Generate database and tables if database does not exist
        GenerateDatabase();

        $GLOBALS['conn'] = new mysqli(SERVER_NAME, DB_USERNAME, DB_PASSWORD, DB_NAME);

        // Generate tables
        GenerateTables();
    } else {
        // Log in default database set 
        $GLOBALS['conn'] = new mysqli(SERVER_NAME, DB_USERNAME, DB_PASSWORD, DB_NAME);
        if ($GLOBALS['conn']->connect_error) {
            die ("Database + table connection failed: " . $GLOBALS['conn']->connect_error);
        }
    }
    // Get user details from DB (values used throughout page)
    $GLOBALS['user_details'] = CheckUserLogin($_SESSION['username'], $_SESSION['password']);

    // Get full list of entries inside given table (get only specific entry if ID provided)
    function GetTableEntries($table_name, $id = null) {
        $query_where = "";
        if (isset($id)) {
            $query_where = " WHERE id=$id;";
        }
        $query = "SELECT * FROM $table_name".$query_where.";";
        $result = $GLOBALS['conn']->query($query);
        $rows = [];
        while($row = mysqli_fetch_array($result)) {
            $rows[] = $row;
        }
        return (!isset($id)) ? $rows : (isset($rows[0]) ? $rows[0] : false);
    }

    function GetReviewFeedbackListByStaffID($staff_id) {
        $query = "SELECT * FROM review_feedback WHERE feedback_staff_id=$staff_id;";
        $result = $GLOBALS['conn']->query($query);
        $rows = [];
        while($row = mysqli_fetch_array($result)) {
            $rows[] = $row;
        }
        return (!isset($id)) ? $rows : (isset($rows[0]) ? $rows[0] : false);
    }

    // Output list as options
    function OutputListAsOptions($list, $label_name_1, $label_name_2 = "", $divider = "") {
        foreach ($list as $entry) {
            $label_output = $entry[$label_name_1];
            $label_output .= !empty($label_name_2) ? $divider.$entry[$label_name_2] : "";
            
            echo "<option value=".$entry['id'].">".$label_output."</option>";
        }
    }

    // Get all feedback entries of review
    function GetReviewFeedbackEntriesByReviewID ($review_id) {
        $query = "SELECT * FROM review_feedback WHERE review_id=$review_id;";
        $result = $GLOBALS['conn']->query($query);
        $rows = [];
        while($row = mysqli_fetch_array($result)) {
            $rows[] = $row;
        }
        return (!isset($id)) ? $rows : (isset($rows[0]) ? $rows[0] : false);
    }

    function GetReviewFeedbackRequestsByAssignedStaffID ($feedback_staff_id, $check_for_unfinished_only) {
        $query_unfinished = "";
        if($check_for_unfinished_only) {
            $query_unfinished = " AND feedback_date_completed IS NULL";
        }

        $query = "SELECT * FROM review_feedback WHERE feedback_staff_id=$feedback_staff_id".$query_unfinished.";";
        $result = $GLOBALS['conn']->query($query);
        $rows = [];
        while($row = mysqli_fetch_array($result)) {
            $rows[] = $row;
        }
        return (!isset($id)) ? $rows : (isset($rows[0]) ? $rows[0] : false);
    }

    // Check if the table exists in database
    function CheckIfTablesExist() {
        $query = "SHOW TABLES FROM ".DB_NAME;
        $result = $GLOBALS['conn']->query($query);

        return $result;
    }

    // Create database from scratch
    function GenerateDatabase() {
        // Create table
        $query = "CREATE DATABASE ".DB_NAME;
        $GLOBALS['conn']->query($query);

        AddSessionMessage("Database created.");
    }
    function GenerateTables() {
        // Users/Staff
        $query = QUERY_CREATE_TABLE_STAFF;
        $GLOBALS['conn']->query($query);
        // Performance Reviews
        $query = QUERY_CREATE_TABLE_REVIEWS;
        $GLOBALS['conn']->query($query);
        // Review Assignment Records
        $query = QUERY_CREATE_TABLE_REVIEW_ASSIGMENTS;
        $GLOBALS['conn']->query($query);
        
        // Generate initial accounts (admin + demo entries)
        foreach (DB_POPULATE_DATA_STAFF as $staff) {
            AddStaff($staff['username'], $staff['password'], $staff['first_name'], $staff['last_name'], $staff['email'], $staff['is_admin']);
        }

        AddSessionMessage("Tables created.");
    }


    // Check user's login details and return true/false depending on match
    function CheckUserLogin($username, $password) {
        $query = "SELECT id AS staff_id, first_name, last_name, email, is_admin FROM staff WHERE username='$username' AND password='$password' LIMIT 1;";
        $result = $GLOBALS['conn']->query($query);
        if (mysqli_num_rows($result) == 1) {
            $rows = [];
            while($row = mysqli_fetch_array($result)) {
                $rows[] = $row;
            }
            return $rows[0];
        }
        return false;
    }


    // Add session message
    function AddSessionMessage($message) {
        if (!empty($_SESSION['message'])) {
            $_SESSION['message'] .= "<br>";
        }
        $_SESSION['message'] .= $message;
    }
    
    function AddStaff($username, $password, $first_name, $last_name, $email, $is_admin = 0) {
        $query = "INSERT INTO staff (username, password, first_name, last_name, email, is_admin) 
            VALUES ('$username', '".sha1($password)."', '$first_name', '$last_name', '$email', $is_admin);";
        if (!$GLOBALS['conn']->query($query)) {
            AddSessionMessage("Failed to create user: ".$GLOBALS['conn'] -> error);
        } else {
            AddSessionMessage("New staff member created successfully.");
        }
    }
    function EditStaff($id, $password, $first_name, $last_name, $email, $is_admin) {
        // Update password only when new value has been input
        $query_set_password = "";
        if(isset($password) && !empty($password)) {
            $query_set_password = "password='".sha1($password)."', ";
        }
        // If ID = 1 (master admin account), is_admin status is always true
        $is_admin = ($id == 1) ? true : $is_admin;

        $query = "UPDATE staff SET ".$query_set_password."first_name='$first_name', last_name='$last_name', email='$email', 
            is_admin=$is_admin, date_updated='".TIMESTAMP_NOW."' WHERE id=$id;";
        if (!$GLOBALS['conn']->query($query)) {
            AddSessionMessage("Failed to update staff member: ".$GLOBALS['conn'] -> error);
        } else {
            AddSessionMessage("Staff member details updated successfully.");
        }
    }
    function DeleteStaff($id) {
        $query = "DELETE FROM staff WHERE id=$id;";
        if (!$GLOBALS['conn']->query($query)) {
            AddSessionMessage("Failed to delete staff member: ".$GLOBALS['conn'] -> error);
        } else {
            AddSessionMessage("Staff member deleted successfully.");
        }
    }
    // Create new performance review
    function AddReview($staff_id, $review_contents) {
        $query = "INSERT INTO reviews (staff_id, review_contents) 
            VALUES ($staff_id, '$review_contents');";
        if (!$GLOBALS['conn']->query($query)) {
            AddSessionMessage("Failed to create performance review: ".$GLOBALS['conn'] -> error);
        } else {
            AddSessionMessage("New performance review created successfully.");
        }
    }
    // Update existing performance review
    function EditReview($id, $review_contents) {
        $query = "UPDATE reviews SET review_contents='$review_contents' WHERE id=$id;";
        if (!$GLOBALS['conn']->query($query)) {
            AddSessionMessage("Failed to update performance review: ".$GLOBALS['conn'] -> error);
        } else {
            AddSessionMessage("Performance review updated successfully.");
        }
    }
    // Delete existing performance review
    function DeleteReview($id) {
        $query = "DELETE FROM reviews WHERE id=$id;";
        if (!$GLOBALS['conn']->query($query)) {
            AddSessionMessage("Failed to delete performance review: ".$GLOBALS['conn'] -> error);
        } else {
            AddSessionMessage("Performance review deleted successfully.");
        }
    }

    // Create new review feedback entry (assigned to staff member)
    function AddReviewFeedback($review_id, $feedback_staff_id, $assigner_staff_id, $assigner_staff_comment) {
        $query = "INSERT INTO review_feedback (review_id, feedback_staff_id, assigner_staff_id, assigner_staff_comment)
            VALUES ($review_id, $feedback_staff_id, $assigner_staff_id, '$assigner_staff_comment');";
        if (!$GLOBALS['conn']->query($query)) {
            AddSessionMessage("Failed to create review feedback request: ".$GLOBALS['conn'] -> error);
        } else {
            AddSessionMessage("Performance review feedback request created successfully.");
        }
    }
    function GetReviewFeedbackByReviewIDAndStaffID($review_id, $feedback_staff_id) {
        $query = "SELECT * FROM review_feedback WHERE review_id=$review_id AND feedback_staff_id=$feedback_staff_id;";
        $result = $GLOBALS['conn']->query($query);
        if (mysqli_num_rows($result) == 1) {
            $rows = [];
            while($row = mysqli_fetch_array($result)) {
                $rows[] = $row;
            }
            return $rows[0];
        }
        return false;
    }

    // Update review feedback entry
    function UpdateReviewFeedback($id, $feedback_content) {
        $query = "UPDATE review_feedback SET feedback_content='$feedback_content', 
            feedback_date_completed='".TIMESTAMP_NOW."', feedback_date_last_updated='".TIMESTAMP_NOW."' WHERE id=$id";
        if (!$GLOBALS['conn']->query($query)) {
            AddSessionMessage("Failed to update review feedback: ".$GLOBALS['conn'] -> error);
        } else {
            AddSessionMessage("Performance review feedback updated successfully.");
        }
    }