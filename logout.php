<?php
session_start();


$name = $_SESSION['name'] ?? 'User';


session_unset();
session_destroy();

header("refresh:3;url=loginpage.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logging Out</title>
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
            flex-direction: column;
        }
        .logout-message {
            position: relative;
            margin-top: 50px;
            color: #4A4949;
            font-weight: 500;
            font-size: 1.2rem;
            text-align: center;
            width: 100%;
        }
        .user-name {
            color: #FF0000;
            font-weight: 700;
        }
        #wifi-loader {
            margin-bottom: 20px;
        }
        .text {
            white-space: nowrap;
        }
    </style>
</head>
<body>
    <div id="wifi-loader">
        <svg viewBox="0 0 86 86" class="circle-outer">
            <circle r="40" cy="43" cx="43" class="back"></circle>
            <circle r="40" cy="43" cx="43" class="front"></circle>
        </svg>
        <svg viewBox="0 0 60 60" class="circle-middle">
            <circle r="27" cy="30" cx="30" class="back"></circle>
            <circle r="27" cy="30" cx="30" class="front"></circle>
        </svg>
        <div data-text="Logging out..." class="text"></div>
    </div>
    <div class="logout-message">
        Goodbye, <span class="user-name"><?php echo htmlspecialchars($name); ?></span>...
    </div>
    
    <script>
        document.body.style.opacity = 0;
        window.addEventListener('DOMContentLoaded', () => {
            document.body.style.transition = 'opacity 0.5s ease';
            document.body.style.opacity = 1;
        });
    </script>
</body>
</html>