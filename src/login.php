<?php
// Start the session (if not already started)
session_start();

// Check if the user is already logged in
if (isset($_SESSION['email'])) {
    header("Location: home.php"); // Redirect to home page or dashboard
    exit();
}

// Include your database connection file
require('db.php');

// Handle login logic
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Use filter_input to get POST values safely
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

    // Prepare and execute the query to check if the user exists
    $stmt = $con->prepare("SELECT * FROM users WHERE email = ?");
    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Check user type and password verification
            if ($user['type'] == 1) { // Admin login
                if ($password === $user['password']) {
                    // Successful login for admin
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['type'] = $user['type']; // Store user type in session
                    header("Location: add_company.php"); // Redirect to admin dashboard
                    exit();
                } else {
                    $error = "Invalid email or password";
                }
            } else { // Customer login
                if (password_verify($password, $user['password'])) {
                    // Successful login for customer
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['type'] = $user['type']; // Store user type in session
                    header("Location: home.php"); // Redirect to customer home page
                    exit();
                } else {
                    $error = "Invalid email or password";
                }
            }
        } else {
            $error = "Invalid email or password";
        }

        // Close statement
        $stmt->close();
    } else {
        // Handle statement preparation failure
        echo "Failed to prepare SQL statement.";
    }
}

// Close connection only if $con is defined
if (isset($con)) {
    mysqli_close($con);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Portal - Login</title>
    <style>
        body {
            background-color: #F5F7FA;
            font-family: 'Open Sans', sans-serif;
            margin: 0;
            padding: 0;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #2c3e50;
            padding: 20px;
            color: white;
        }

        header .logo {
            font-size: 24px;
            font-family: 'Montserrat', sans-serif;
        }

        nav ul {
            list-style: none;
            display: flex;
            margin: 0;
            padding: 0;
        }

        nav ul li {
            margin: 0 15px;
        }

        nav a {
            color: white;
            text-decoration: none;
            transition: color 0.3s;
            padding: 10px 20px;
            border-radius: 5px;
            border: 2px solid transparent;
        }

        nav a:hover {
            color: #e74c3c;
            border: 2px solid #e74c3c;
        }

        #page-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: calc(100vh - 80px); /* Adjust height based on header */
        }

        .container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }

        .login-title {
            text-align: center;
            color: #34495e; /* Dark blue color */
            margin-bottom: 20px;
        }

        .error {
            color: #e74c3c;
            margin-bottom: 15px;
            text-align: center;
        }

        .input-wrapper {
            position: relative;
            margin-bottom: 20px;
        }

        .login-input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            transition: border-color 0.3s;
        }

        .login-input:focus {
            border-color: #e74c3c;
            outline: none;
        }

        .login-button {
            background-color: #2c3e50; /* Dark color */
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
            width: 100%;
        }

        .login-button:hover {
            background-color: #34495e; /* Lighter dark color */
            transform: scale(1.05);
        }

        .forgot-password {
            text-align: center;
            margin-top: 10px;
        }

        .forgot-password a {
            color: #2c3e50; /* Dark color */
            text-decoration: none;
            font-weight: bold;
        }

        .forgot-password a:hover {
            text-decoration: underline;
        }

        footer {
            background-color: #2c3e50; /* Dark background */
            color: white;
            text-align: center;
            padding: 20px;
        }

        footer ul {
            list-style: none;
            display: flex;
            justify-content: center;
            padding: 0;
        }

        footer ul li {
            margin: 0 10px;
        }

        footer a {
            color: white;
            text-decoration: none;
            transition: color 0.3s;
        }

        footer a:hover {
            color: #e74c3c; /* Hover color */
        }
    </style>
</head>
<body>
<header>
    <div class="logo">Job Portal</div>
    <nav>
        <ul>
            <li><a href="register.php">Sign Up</a></li>
            <li><a href="login.php" class="active">Sign In</a></li>
        </ul>
    </nav>
</header>

<div id="page-container">
    <div class="container">
        <form class="form" method="post" name="login">
            <h1 class="login-title">Login</h1>

            <div class="input-wrapper">
                <input type="email" class="login-input" name="email" placeholder="Email Address" required>
            </div>

            <div class="input-wrapper">
                <input type="password" class="login-input" name="password" placeholder="Password" required>
            </div>

            <input type="submit" value="Login" class="login-button">

            <div class="forgot-password">
                <a href="forgot_password.php">Forgot Password?</a>
            </div>
        </form>
    </div>
</div>

<footer>
    <ul>
        <li><a href="#">Privacy Policy</a></li>
        <li><a href="#">Terms of Service</a></li>
        <li><a href="#">Contact Us</a></li>
    </ul>
</footer>

<!-- PHP Echoes Error Message as JavaScript -->
<?php if (!empty($error)): ?>
    <script>
        alert("<?php echo $error; ?>");
    </script>
<?php endif; ?>

</body>
</html>
