<?php
    include('common.php');
    $_SESSION['username']   = '';
    $_SESSION['password']   = '';
    header('location: /');
    exit();