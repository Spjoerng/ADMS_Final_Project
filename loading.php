<?php
session_start();
include "connection.php";

if (isset($_SESSION['id'])) {
    $redirect = 'home.php'; // Default redirect
    
    // Handle redirect parameter
    if (isset($_GET['redirect'])) {
        $redirect = $_GET['redirect'];
    }
    
    // Handle form submissions
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['original_action'])) {
        $redirect = $_POST['original_action'];
        $_SESSION['form_data'] = $_POST;
    }
    
    header("refresh:3;url=" . $redirect);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="favicon.png">
    <title>Loading</title>
    <link rel="stylesheet" href="loading.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f5f5f5;
            margin: 0;
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body>
    <div id="wifi-loader">
        <svg viewBox="0 0 86 86" class="circle-outer">
            <circle r="40" cy="43" cx="43" class="back"></circle>
            <circle r="40" cy="43" cx="43" class="front"></circle>
            <circle r="40" cy="43" cx="43" class="new"></circle>
        </svg>
        <svg viewBox="0 0 60 60" class="circle-middle">
            <circle r="27" cy="30" cx="30" class="back"></circle>
            <circle r="27" cy="30" cx="30" class="front"></circle>
        </svg>
        <div data-text="Loading..." class="text"></div>
    </div>
</body>
</html>
<?php
} else {
    header("Location: loginpage.php");
    exit();
}
?>