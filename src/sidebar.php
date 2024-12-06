<?php
// Include your database connection file
require('db.php'); // Ensure db.php sets up a MySQLi connection

// Handle logout
if (isset($_POST['logout'])) {
    session_destroy(); // Destroy the session
    header("Location: login.php"); // Redirect to login after logout
    exit();
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Job Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400&family=Montserrat:wght@700&family=Open+Sans:wght@400&display=swap" rel="stylesheet">
    
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Open Sans', sans-serif;
            background-color: #FAFAFA; /* Light background */
        }

        .sidebar-container {
            width: 250px;
            height: 100vh;
            background-color: #2c3e50;
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 20px;
            box-shadow: 2px 0 5px rgba(0,0,0,0.5);
            transition: transform 0.3s ease;
        }

        .sidebar-logo {
            font-size: 24px;
            font-family: 'Montserrat', sans-serif;
            color: #FAFAFA; /* Light text color */
            margin-bottom: 30px; /* Spacing below logo */
        }

        .sidebar-menu {
            list-style-type: none;
            padding: 0;
            width: 100%;
        }

        .sidebar-menu li {
            margin: 15px 0;
            position: relative;
            width: 100%;
        }

        .sidebar-menu li a {
            color: #ecf0f1;
            text-decoration: none;
            font-size: 18px;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 15px;
            border-radius: 5px;
            transition: color 0.3s, background-color 0.3s, border 0.3s; /* Added border transition */
            border: 2px solid transparent; /* Initial border state */
        }

        .sidebar-menu li a:hover {
            color: #e74c3c; /* Change text color on hover */
             
            border: 2px solid #e74c3c; /* Border color on hover */
        }

        /* Logout Button */
        .logout-button {
            background-color: #e76f51;
            color: #fff;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
            width: 80%;
            text-align: center;
            margin-top: auto; /* Align at the bottom */
            margin-left: 30px;
        }

        .logout-button:hover {
            background-color: #c0392b;
        }
    </style>
</head>
<body>
    <div class="sidebar-container">
        <div class="sidebar-logo">Job Portal</div> <!-- Updated Logo -->
        <ul class="sidebar-menu">

            <li><a href="add_jobs.php">Add a Job</a></li>
            <li><a href="view_jobs_admin.php">View Jobs</a></li>
            <li><a href="admin_applications.php">View Applications</a></li>
            <li><a href="add_Company.php">Add a Company</a></li>
            <li><a href="viewCompanies.php">View Companies</a></li>
            <li>
                <form id="logoutForm" action="" method="POST" style="display: inline;">
                    <button type="submit" name="logout" class="logout-button" onclick="return confirmLogout()">Log Out</button>
                </form>
            </li>
        </ul>
    </div>

    <script>
        // Confirmation for logout
        function confirmLogout() {
            return confirm("Are you sure you want to log out?");
        }
    </script>
</body>
</html>
