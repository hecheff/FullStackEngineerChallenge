<?php
    // Adding/editing/deleting tasks only allowed by admins

    include('common.php');

    // Run credentials check. ONLY allow running if current user is admin
    if ($GLOBALS['user_details']['is_admin']) {
        $type   = htmlspecialchars($_GET['type']);
        $action = htmlspecialchars($_GET['action']);
    
        if (in_array($type, ADMIN_ALLOWED_TYPES)) {
            if ($type == "staff") {
                $id         = isset($_POST['id'])           ? htmlspecialchars($_POST['id']) : "";
                $username   = isset($_POST['username'])     ? htmlspecialchars($_POST['username']) : "";
                $password   = isset($_POST['password'])     ? htmlspecialchars($_POST['password']) : "";
                $first_name = isset($_POST['first_name'])   ? addslashes(htmlspecialchars($_POST['first_name'])) : "";
                $last_name  = isset($_POST['last_name'])    ? addslashes(htmlspecialchars($_POST['last_name'])) : "";
                $email      = isset($_POST['email'])        ? htmlspecialchars($_POST['email']) : "";
                $is_admin   = isset($_POST['is_admin']) && !empty($_POST['is_admin']) ? "true" : "false";
                
                if (in_array($action, ADMIN_ALLOWED_ACTIONS)) {
                    if ($action == "add") {
                        AddStaff($username, $password, $first_name, $last_name, $email, $is_admin);
                    } elseif ($action == "edit") {
                        EditStaff($id, $password, $first_name, $last_name, $email, $is_admin);
                    } elseif ($action == "delete") {
                        DeleteStaff($id);
                    } else {
                        AddSessionMessage("An unknown error has occurred. Please notify an administrator.");
                    }
                } else {
                    AddSessionMessage("Access error. Action setting issue detected.");
                }
            } elseif ($type == "review") {
                if (in_array($action, ADMIN_ALLOWED_ACTIONS)) {
                    $id                 = isset($_POST['id'])       ? htmlspecialchars($_POST['id']) : "";
                    $staff_id           = isset($_POST['staff_id']) ? htmlspecialchars($_POST['staff_id']) : "";
                    $review_contents    = isset($_POST['review_contents']) ? addslashes(htmlspecialchars($_POST['review_contents'])) : "";

                    if ($action == "add") {
                        AddReview($staff_id, $review_contents);
                    } elseif ($action == "edit") {
                        EditReview($id, $review_contents);
                    } elseif ($action == "delete") {
                        DeleteReview($id);
                    } else {
                        AddSessionMessage("An unknown error has occurred. Please notify an administrator.");
                    }
                } else {
                    AddSessionMessage("Access error. Action setting issue detected.");
                }
            } elseif ($type == "review_feedback") {
                if (in_array($action, ADMIN_ALLOWED_ACTIONS)) {
                    $id                     = isset($_POST['id'])                           ? htmlspecialchars($_POST['id']) : "";
                    $review_id              = isset($_POST['review_id'])                    ? htmlspecialchars($_POST['review_id']) : "";
                    $feedback_staff_id      = isset($_POST['feedback_staff_id'])            ? htmlspecialchars($_POST['feedback_staff_id']) : "";
                    $feedback_content       = isset($_POST['feedback_content'])             ? htmlspecialchars($_POST['feedback_content']) : "";
                    $assigner_staff_id      = isset($GLOBALS['user_details']['staff_id'])   ? $GLOBALS['user_details']['staff_id'] : ""; 
                    $assigner_staff_comment = isset($_POST['assigner_staff_comment'])       ? htmlspecialchars($_POST['assigner_staff_comment']) : "";
                    
                    if (!empty($assigner_staff_id)) {
                        if ($action == "add") {
                            if (!GetReviewFeedbackByReviewIDAndStaffID($review_id, $feedback_staff_id)) {
                                AddReviewFeedback($review_id, $feedback_staff_id, $assigner_staff_id, $assigner_staff_comment);
                            } else {
                                AddSessionMessage("A feedback request for this review has already been assigned to this staff member.");
                            }
                        } elseif ($action == "edit") {
                            AddSessionMessage("Performance review feedback submissions not supported via this method. Please notify an administrator.");
                        } elseif ($action == "delete") {
                            AddSessionMessage("Performance review feedback cannot be deleted. If an entry needs to be deleted, please notify an administrator.");
                        } else {
                            AddSessionMessage("An unknown error has occurred. Please notify an administrator.");
                        }
                    } else {
                        AddSessionMessage("Access permission error.");
                    }
                } else {
                    AddSessionMessage("Access error. Action setting issue detected.");
                }
            }
        } else {
            AddSessionMessage("Access error. Type setting issue detected.");
        }
    } else {
        AddSessionMessage("Access forbidden.");
    }
    header('Location: '.$_SERVER['HTTP_REFERER']);  // Redirect to previous page
    exit();
