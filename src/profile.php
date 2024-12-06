<?php
// Start the session
session_start();

// Include your database connection file
require('db.php'); // Ensure db.php sets up a MySQLi connection

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php"); // Redirect to login if not authenticated
    exit();
}

$email = $_SESSION['email'];

// Fetch user information
$sql = "SELECT * FROM users WHERE email=?";
$stmt = $con->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Handle logout
if (isset($_POST['logout'])) {
    session_destroy(); // Destroy the session
    header("Location: login.php"); // Redirect to login after logout
    exit();
}

// Handle update logic
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $name = mysqli_real_escape_string($con, trim($_POST['name']));
    $phone_number = mysqli_real_escape_string($con, trim($_POST['phone_number']));
    $new_password = mysqli_real_escape_string($con, trim($_POST['new_password']));

    // Prepare the update SQL statement
    $update_sql = "UPDATE users SET name=?, phone_number=?" . 
                  ($new_password ? ", password=?" : "") . 
                  " WHERE email=?";
    
    $stmt = $con->prepare($update_sql);
    if ($new_password) {
        $new_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt->bind_param("sss", $name, $phone_number, $new_password, $email);
    } else {
        $stmt->bind_param("sss", $name, $phone_number, $email);
    }
    
    if ($stmt->execute()) {
        $message = "Profile updated successfully.";
        // Refresh user data after update
        $user['name'] = $name;
        $user['phone_number'] = $phone_number;
    } else {
        $error = "Error updating profile: " . $stmt->error;
    }
}

// Handle account deletion
if (isset($_POST['delete'])) {
    $delete_sql = "DELETE FROM users WHERE email=?";
    $stmt = $con->prepare($delete_sql);
    $stmt->bind_param("s", $email);
    
    if ($stmt->execute()) {
        session_destroy(); // Destroy the session
        header("Location: login.php"); // Redirect to login after deletion
        exit();
    } else {
        $error = "Error deleting account: " . $stmt->error;
    }
}

include 'navbar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Portal - Profile</title>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400&family=Montserrat:wght@700&family=Open+Sans:wght@400&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Open Sans', sans-serif;
            background-color: #F5F7FA; /* Light gray background */
            margin: 0;
            padding: 0;
        }

        .container {
            width: 90%;
            max-width: 800px;
            margin: auto;
            padding: 20px;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #333; /* Darker color for the heading */
        }

        .profile-card {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            transition: transform 0.2s; /* Add a hover effect */
        }

        .profile-card:hover {
            transform: scale(1.02); /* Slightly enlarge on hover */
        }

        .profile-card h2 {
            text-align: center;
            color: #E76F51; /* Primary color */
            margin-bottom: 15px;
            font-size: 24px;
        }

        .profile-detail {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding: 10px 15px;
            border-bottom: 1px solid #eaeaea;
        }

        .profile-detail label {
            font-weight: bold;
            color: #666; /* Slightly lighter label color */
        }

        .profile-detail span {
            color: #333; /* Darker color for details */
        }

        button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            background-color: #E76F51; /* Primary color */
            color: #fff;
            cursor: pointer;
            margin-top: 10px; /* Add some space between buttons */
            display: inline-block; /* Inline-block for button layout */
            transition: background-color 0.2s; /* Smooth transition */
        }

        button:hover {
            background-color: #d65c47; /* Darker shade on hover */
        }

        .message, .error {
            text-align: center;
            margin-bottom: 15px;
        }

        .message {
            color: green;
        }

        .error {
            color: red;
        }

        /* Popup styles */
        /* Popup styles */
.popup {
    display: none; /* Hidden by default */
    position: fixed;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    justify-content: center;
    align-items: center;
    z-index: 1000; /* Ensure it's on top of other elements */
}

.popup-content {
    background-color: white;
    padding: 30px;
    border-radius: 8px;
    width: 90%;
    max-width: 400px; /* Set a max-width for better control */
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
    animation: fadeIn 0.3s; /* Add a fade-in effect */
}

/* Input field styles */
.popup-content input {
    width: 100%;
    padding: 10px;
    margin: 10px 0; /* Add some margin for better spacing */
    border: 1px solid #E76F51; /* Use primary color for borders */
    border-radius: 5px;
    transition: border-color 0.3s; /* Smooth transition for focus */
}

.popup-content input:focus {
    border-color: #d65c47; /* Darker shade on focus */
    outline: none; /* Remove default outline */
}

/* Button styles */
.popup-content button {
    width: 100%; /* Full width for buttons */
    padding: 12px;
    border: none;
    border-radius: 5px;
    background-color: #E76F51; /* Primary color */
    color: #fff;
    cursor: pointer;
    margin-top: 15px; /* Add some space between buttons */
    transition: background-color 0.3s; /* Smooth transition */
}

.popup-content button:hover {
    background-color: #d65c47; /* Darker shade on hover */
}

/* Close button styles */
.close {
    position: absolute;
    top: 10px;
    right: 10px;
    font-size: 18px; /* Increase size for better visibility */
    cursor: pointer;
    color: #666; /* Color for close button */
}

        /* Delete confirmation styles */
        #delete-confirmation {
            display: none; /* Hidden by default */
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        /* Responsive styles */
        @media (max-width: 600px) {
            .container {
                width: 95%;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h1>User Profile</h1>
    
    <?php if (isset($message)): ?>
        <div class="message"><?= $message; ?></div>
    <?php endif; ?>
    <?php if (isset($error)): ?>
        <div class="error"><?= $error; ?></div>
    <?php endif; ?>

    <div class="profile-card">
        <h2>Profile Information</h2>
        <div class="profile-detail">
            <label for="name">Name:</label>
            <span><?= htmlspecialchars($user['name']); ?></span>
        </div>
        <div class="profile-detail">
            <label for="email">Email:</label>
            <span><?= htmlspecialchars($user['email']); ?></span>
        </div>
        <div class="profile-detail">
            <label for="phone_number">Phone Number:</label>
            <span><?= htmlspecialchars($user['phone_number']); ?></span>
        </div>
        <div class="profile-detail">
            <label for="created_at">Account Created:</label>
            <span><?= htmlspecialchars($user['created_at']); ?></span>
        </div>
        <button id="edit-button" aria-label="Edit Profile">Edit</button>
        <button type="button" onclick="document.getElementById('delete-confirmation').style.display='block'" aria-label="Delete Account">Delete Account</button>
    </div>

    <div id="delete-confirmation" class="popup">
        <div class="popup-content">
            <h3>Are you sure you want to delete your account?</h3>
            <form method="POST">
                <button type="submit" name="delete" style="background-color: red;">Yes, Delete</button>
                <button type="button" onclick="document.getElementById('delete-confirmation').style.display='none'">Cancel</button>
            </form>
        </div>
    </div>

    <div id="popup" class="popup">
    <div class="popup-content">
        <span class="close" onclick="document.getElementById('popup').style.display='none'">&times;</span>
        <h2 style="text-align: center; margin-bottom: 20px; color: #E76F51;">Edit Profile</h2>
        <form method="POST">
            <div class="form-group">
                <label for="name-edit">Name</label>
                <input type="text" id="name-edit" name="name" value="<?= htmlspecialchars($user['name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="phone_number-edit">Phone Number</label>
                <input type="text" id="phone_number-edit" name="phone_number" value="<?= htmlspecialchars($user['phone_number']); ?>" required>
            </div>
            <div class="form-group">
                <label for="new_password">New Password (Leave empty to keep current)</label>
                <input type="password" id="new_password" name="new_password">
            </div>
            <button type="submit" name="update" aria-label="Update Profile">Update</button>
        </form>
    </div>
</div>

</div>
<!-- Footer Section -->
<?php include 'footer.php'; ?>

<script>
    document.getElementById('edit-button').onclick = function() {
        document.getElementById('popup').style.display = 'flex'; // Show the edit profile popup
    }
    window.onclick = function(event) {
        if (event.target.className === 'popup') {
            event.target.style.display = 'none'; // Close the popup if user clicks outside
        }
    }
</script>
</body>
</html>
