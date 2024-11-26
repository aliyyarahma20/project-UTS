<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        /* Error message styling */
        .error-message {
            color: #dc3545;
            text-align: center;
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 5px;
            background-color: rgba(220, 53, 69, 0.1);
            display: none;
        }
    </style>
</head>
<body>
    <!-- Dekorasi Background -->
    <div class="decoration">
        <div class="circle circle-1"></div>
        <div class="circle circle-2"></div>
        <div class="wave"></div>
        <div class="dots"></div>
    </div>

    <div class="wrapper">
        <div class="logo-text">Welcome!</div>
        <div class="subtitle">Enter your login details</div>

        <div id="errorMessage" class="error-message">
            <?php if(isset($_GET['error'])): ?>
                Invalid username or password
            <?php endif; ?>
        </div>
        
        <form method="POST" action="ceklogin.php">
            <div class="input-field">
                <i class="far fa-user"></i>
                <input type="text" placeholder="Username" name="username" required>
            </div>
            
            <div class="input-field">
                <i class="fas fa-lock"></i>
                <input type="password" placeholder="Password" name="password" required>
            </div>
            
            <div class="button-group">
                <button type="submit" class="btn login-btn">Log in</button>
                <button type="button" class="btn cancel-btn" onclick="window.location.href='login.php'">Cancel</button>
            </div>
        </form>
    </div>

    <script>
        // Show error message if it exists
        <?php if(isset($_GET['error'])): ?>
            document.getElementById('errorMessage').style.display = 'block';
        <?php endif; ?>
    </script>
</body>
</html>