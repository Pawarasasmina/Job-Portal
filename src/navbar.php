<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require('db.php'); // Ensure db.php sets up a MySQLi connection

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400&family=Montserrat:wght@700&family=Open+Sans:wght@400&display=swap" rel="stylesheet">
    <title>Online Job Portal</title>
    <style>
        body {
            margin: 0;
            font-family: 'Open Sans', sans-serif;
            background-color: #FAFAFA; /* Light background */
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #2c3e50; /* Darker theme color */
            padding: 15px 20px;
            color: white;
        }

        .logo {
            font-size: 24px;
            font-family: 'Montserrat', sans-serif;
        }

        nav {
            display: flex;
            align-items: center;
        }

        nav ul {
            list-style: none;
            display: flex;
            margin: 0;
            padding: 0;
        }

        nav ul li {
            margin: 0 10px;
        }

        nav a {
    color: white;
    text-decoration: none;
    transition: color 0.3s, border 0.3s; /* Added border transition */
    padding: 10px 20px;
    border-radius: 5px;
    border: 2px solid transparent; /* Initial border state */
}

nav a:hover {
    color: #e74c3c; /* Text color on hover */
    border: 2px solid #e74c3c; /* Border color on hover */
}

        .dropdown {
            position: relative;
            display: inline-block;
            margin-left: 20px; /* Adjust margin */
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #2c3e50; /* Dark theme */
            min-width: 50px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            z-index: 1;
        }

        .dropdown-content a {
            color: #FAFAFA; /* Light text */
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }

        .dropdown-content a:hover {
            background-color: #e74c3c; /* Light teal */
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        .search-bar {
            display: flex;
            align-items: center;
            margin-left: 20px;
        }

        .search-bar input {
            padding: 8px;
            border: none;
            border-radius: 5px;
            width: 250px;
        }

        .search-bar button {
            padding: 8px 10px;
            border: none;
            border-radius: 5px;
            background-color: #e74c3c; /* Red theme */
            color: white;
            margin-left: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .search-bar button:hover {
            background-color: #c0392b; /* Darker red */
        }

        .profile-pic {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            margin-left: 10px;
            margin-right: 60px;
            border: 2px solid #FAFAFA; /* Light text */
            cursor: pointer; /* Add cursor pointer for profile pic */
        }

        @media (max-width: 768px) {
            nav ul {
                flex-direction: column;
                gap: 10px;
                display: none;
            }

            nav.active ul {
                display: flex; /* Show on toggle */
            }

            .hamburger {
                display: block;
                cursor: pointer;
            }

            .hamburger div {
                width: 30px;
                height: 3px;
                background-color: #FAFAFA;
                margin: 5px;
                transition: 0.3s;
            }
        }

        .hamburger {
            display: none;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">Job Portal</div>
        <div class="hamburger" onclick="toggleNav()">
            <div></div>
            <div></div>
            <div></div>
        </div>
        <nav>
            <ul>
                <li><a href="home.php">Home</a></li>
                <li><a href="job_listings.php">Job Listings</a></li>
                <li><a href="applications.php">Applications</a></li>
                <li><a href="manageFeedbacks.php"> Feedbacks</a></li>
                <li><a href="about_us.php">About Us</a></li>
            </ul>
           
            <div class="dropdown">
                <img src="images\profile-icon.png" alt="Profile" class="profile-pic" onclick="toggleProfileDropdown()"> <!-- Replace with dynamic image -->
                <div class="dropdown-content">
                    <a href="profile.php">View Profile</a>
                    <form method="POST" style="margin: 0;" onsubmit="return confirmLogout();">
                        <button type="submit" name="logout" style="background: none; border: none; color: #FAFAFA; padding: 12px 16px; width: 100%; text-align: left; cursor: pointer;">Logout</button>
                    </form>
                </div>
            </div>
        </nav>
    </header>

    <script>
        function confirmLogout() {
            return confirm("Are you sure you want to log out?");
        }

        function toggleNav() {
            const nav = document.querySelector('nav');
            nav.classList.toggle('active');
        }

        function toggleProfileDropdown() {
            const dropdownContent = document.querySelector('.dropdown-content');
            dropdownContent.classList.toggle('show');
        }

        // Close the dropdown if the user clicks outside of it
        window.onclick = function(event) {
            if (!event.target.matches('.profile-pic')) {
                const dropdowns = document.getElementsByClassName("dropdown-content");
                for (let i = 0; i < dropdowns.length; i++) {
                    const openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('show')) {
                        openDropdown.classList.remove('show');
                    }
                }
            }
        }
    </script>
</body>
</html>
