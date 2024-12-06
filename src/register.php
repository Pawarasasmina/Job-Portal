<?php
// Start the session
session_start();

// Include your database connection file
require('db.php'); // Ensure db.php sets up a MySQLi connection

// Handle registration logic
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate inputs
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $phone_number = filter_input(INPUT_POST, 'phone_number', FILTER_SANITIZE_STRING);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Initialize error array
    $errors = [];

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    // Check if password and confirm password match
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    // If no errors, proceed to check email existence
    if (empty($errors)) {
        $checkQuery = "SELECT * FROM users WHERE email = ?";
        $stmt = $con->prepare($checkQuery);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $errors[] = "Email already exists.";
        } else {
            // Insert new user into the database
            $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Use password_hash for better security
            $query = "INSERT INTO users (name, email, phone_number, password, type) VALUES (?, ?, ?, ?, 0)";
            $stmt = $con->prepare($query);
            $stmt->bind_param("ssss", $name, $email, $phone_number, $hashed_password);

            if ($stmt->execute()) {
    // Registration successful
    $_SESSION['name'] = $name; // Store name in session
   

    // Auto redirect after 3 seconds
    header("Location: login.php");
exit();

} else {
    $errors[] = "Registration failed. Please try again.";
}
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400&family=Montserrat:wght@700&family=Open+Sans:wght@400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles/regstyle.css">
    <title>Registration Page</title>
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

        .hero {
            text-align: center;
            padding: 50px 0;
            color: #34495e;
        }

        .registration-form {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            margin: 50px auto;
            transition: transform 0.3s;
        }

        .registration-form:hover {
            transform: translateY(-5px);
        }

        .error-messages {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 15px;
        }

        .registration-form form {
            display: flex;
            flex-direction: column;
        }

        .registration-form label {
            margin-bottom: 5px;
            font-weight: bold;
            color: #2c3e50;
        }

        .registration-form input {
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            transition: border-color 0.3s;
        }

        .registration-form input:focus {
            border-color: #e74c3c;
            outline: none;
        }

        .registration-form button {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
        }

        .registration-form button:hover {
            background-color: #c0392b;
            transform: scale(1.05);
        }

        footer {
            background-color: #2c3e50;
            color: white;
            text-align: center;
            padding: 20px;
        }

        footer ul {
            list-style: none;
            padding: 0;
        }

        footer ul li {
            display: inline;
            margin: 0 10px;
        }

        footer a {
            color: white;
            text-decoration: none;
            transition: color 0.3s;
        }

        footer a:hover {
            color: #e74c3c;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">Job Portal</div>
        <nav>
            <ul>
                <li><a href="register.php">Sign Up</a></li>
                <li><a href="login.php">Sign In</a></li>
            </ul>
        </nav>
    </header>
    
    <main>
        <section class="hero">
            <h1>Join Our Professional Community Today!</h1>
        </section>
        
        <section class="registration-form">
            <?php if (!empty($errors)): ?>
                <div class="error-messages">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            <form method="POST" action="">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" placeholder="" required>
                
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="" required>
                
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="" required>
                
                <label for="confirm-password">Confirm Password</label>
                <input type="password" id="confirm-password" name="confirm_password" placeholder="" required>
                
                <label for="phone">Phone</label>
                <input type="tel" id="phone" name="phone_number" placeholder="" required>
                
                <button type="submit">Register Now</button>
            </form>
        </section>
    </main>
    
    <?php include 'footer.php'; ?>
</body>
</html>
