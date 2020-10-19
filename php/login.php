<?php
    include('common.php');
    
    // Set username and password
    $username = "";
    $password = "";
    if (isset($_POST['username']) && !empty($_POST['username']) && isset($_POST['password']) && !empty($_POST['password'])) {
        $username = htmlspecialchars($_POST['username']);
        $password = sha1($_POST['password']);
    }

    // Check if user in database exists
    $user_exists = CheckUserLogin($username, $password);

    // If exists and password matches, set session to logged in as user

    if ($user_exists) {
        $_SESSION['username']   = $username;
        $_SESSION['password']   = $password;
    } else {
        // Otherwise, return username/password no match 
        $_SESSION['username']   = '';
        $_SESSION['password']   = '';
        AddSessionMessage("Username or error does not match.");
    }
    
    
    header('Location: '.$_SERVER['HTTP_REFERER']);  // Redirect to previous page
    exit();
