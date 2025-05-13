<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="favicon.png">
    <title>BSU Attendance e-Tracker</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="container">
        <div class="image-container">
            <img src="emg/icon.png" alt="School Image" class="background-image">
        </div>
        <div class="form-container">
            <form action="index.php" method="post">
                <h2>Welcome to <span>BSU e-Tracker!</span></h2>
                <h3>LOGIN</h3>
                <?php if (isset($_GET['error'])) { ?>
                    <p class="error"><?php echo $_GET['error']; ?></p>
                <?php } ?>

                <input type="text" name="username" placeholder="Username" required>

                <div class="password-container">
                    <input type="password" id="password" name="password" placeholder="Password" required>
                    <img src="emg/eye-icon.png" alt="Toggle Password Visibility" class="toggle-password" onclick="togglePasswordVisibility()">
                </div>

                <div class="signin-container">
                    <button type="submit">LOGIN</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Password visibility toggle
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.querySelector('.toggle-password');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.src = 'emg/eye-slash-icon.png';
            } else {
                passwordInput.type = 'password';
                toggleIcon.src = 'emg/eye-icon.png';
            }
        }

        // Ensure animations play smoothly when page loads
        document.addEventListener('DOMContentLoaded', function() {
           
        });
    </script>
</body>
</html>