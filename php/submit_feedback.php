<?php
    include('common.php');

    $id                 = isset($_POST['id'])               ? htmlspecialchars($_POST['id']) : "";
    $feedback_content   = isset($_POST['feedback_content']) ? htmlspecialchars($_POST['feedback_content']) : "";
    UpdateReviewFeedback($id, $feedback_content);
    
    header('Location: '.$_SERVER['HTTP_REFERER']);  // Redirect to previous page
    exit();