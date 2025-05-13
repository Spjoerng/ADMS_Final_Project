<?php
session_start();
include "connection.php";

if (isset($_SESSION['id']) && isset($_SESSION['username'])) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <link rel="icon" type="image/png" href="favicon.png">
        <title>HOME</title>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;900&display=swap"
            rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="home.css">
    </head>

    <body class="d-flex flex-column min-vh-100 overflow-hidden">
        <!-- Sidebar -->
        <div class="offcanvas offcanvas-start" tabindex="-1" id="sidebar" aria-labelledby="sidebarLabel">
            <div class="offcanvas-header bg-red">
                <h5 class="offcanvas-title text-white" id="sidebarLabel">Menu</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                    aria-label="Close"></button>
            </div>
            <div class="offcanvas-body bg-white">
                <a href="logout.php" class="btn btn-outline-danger w-100 mb-3">Log Out</a>
            </div>
        </div>

        <!-- Main Content Container -->
        <div class="container-fluid flex-grow-1 overflow-auto bg-f5f5f5">
            <div class="row min-vh-100">
                <!-- Header with Menu and Options -->
                <div
                    class="col-12 d-flex justify-content-between align-items-center position-sticky top-0 bg-f5f5f5 z-index-1 py-2">
                    <div class="d-flex align-items-center">
                        <button class="btn menu-icon ms-5" data-bs-toggle="offcanvas" data-bs-target="#sidebar">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="28" viewBox="0 0 47 41"
                                fill="#6358DC">
                                <path
                                    d="M0 0V5.77194H46.1755V0H0ZM0 17.1427V22.9146H46.1755V17.1427H0ZM0 34.4585V40.2304H46.1755V34.4585H0Z" />
                            </svg>
                        </button>
                    </div>

                    <!-- Options in top right -->
                    <div class="options d-flex" style="margin-right: 6rem; gap: 6rem;">
                        <a href="loading.php?redirect=manageclass.php" class="option-btn btn btn-lg"
                            style="padding: 1rem 2rem; font-size: 1.2rem;">Classes</a>
                        <a href="loading.php?redirect=manageattend.php" class="option-btn btn btn-lg"
                            style="padding: 1rem 2rem; font-size: 1.2rem;">Attendance</a>
                    </div>
                </div>

                <!-- Main Content Area -->
                <div class="col-12 col-lg-6">
                    <div class="main-content p-4 p-md-5 my-3 my-md-5 mx-lg-5 bg-white">
                        <h1 class="greeting mb-3 color-red animate-greeting">Greetings!</h1>
                        <h2 class="user-name mb-4 animate-username"><?php echo htmlspecialchars($_SESSION['name']); ?></h2>
                        <p class="college-name animate-college">College of Informatics and Computing Sciences</p>
                    </div>
                </div>

                <!-- Illustration - Hidden on mobile, shown on desktop -->
                <div class="col-lg-6 d-none d-lg-flex align-items-center justify-content-center bg-f5f5f5">
                    <img src="emg/illus.png" alt="Attendance Illustration" class="illustration img-fluid">
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

        <script>
            // Ensure animations play when the page loads
            document.addEventListener('DOMContentLoaded', function() {
                
            });
            
            // Original logout button handler
            document.getElementById('logout-btn')?.addEventListener('click', function (e) {
                e.preventDefault();
                document.body.innerHTML = `
                    <div style="display:flex; justify-content:center; align-items:center; height:100vh; background:#f5f5f5;">
                        <div id="wifi-loader">
                            <!-- Your loading SVG elements -->
                            <div data-text="Logging out..." class="text"></div>
                        </div>
                    </div>
                `;
                // Then proceed with logout
                window.location.href = 'logout.php';
            });
        </script>
    </body>

    </html>
    <?php
} else {
    header("Location: index.php");
    exit();
}
?>