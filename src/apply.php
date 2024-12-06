<?php 
// Start the session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Include the navigation bar
include('navbar.php');

// Include your database connection file
require('db.php'); // Ensure db.php sets up a MySQLi connection

// Check if the connection is successful
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php"); // Redirect to login if not authenticated
    exit();
}

$email = $_SESSION['email'];

// Fetch user information
$sql = "SELECT * FROM users WHERE email=?";
$stmt = $con->prepare($sql);

if ($stmt === false) {
    die("Prepare failed: " . htmlspecialchars($con->error)); // Check if prepare failed
}

$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $user_id = $user['id']; // Get the user's ID from the fetched data
    $company_id = $_POST['company_id'];
    $job_id = $_POST['job_id'];
    $cover_letter = mysqli_real_escape_string($con, $_POST['cover_letter']);
    $status = 'pending'; // Default status

    // Handle file upload
    $target_dir = "uploads/"; // Directory where files will be uploaded
    $target_file = $target_dir . basename($_FILES["resume_file"]["name"]);
    $uploadOk = 1;
    $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check file size
    if ($_FILES["resume_file"]["size"] > 500000) {
        echo "<div class='alert danger'>Sorry, your file is too large.</div>";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if ($fileType != "pdf" && $fileType != "doc" && $fileType != "docx") {
        echo "<div class='alert danger'>Sorry, only PDF, DOC & DOCX files are allowed.</div>";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "<div class='alert danger'>Sorry, your file was not uploaded.</div>";
    } else {
        // If everything is ok, try to upload file
        if (move_uploaded_file($_FILES["resume_file"]["tmp_name"], $target_file)) {
            // Insert application data into the database
            $query = "INSERT INTO applications (user_id, job_id, company_id, cover_letter, resume_file, status) VALUES ('$user_id', '$job_id', '$company_id', '$cover_letter', '$target_file', '$status')";
            if (mysqli_query($con, $query)) {
                echo "<div class='alert success'>Application submitted successfully.</div>";
            } else {
                echo "<div class='alert danger'>Error: " . mysqli_error($con) . "</div>";
            }
        } else {
            echo "<div class='alert danger'>Sorry, there was an error uploading your file.</div>";
        }
    }
}

// Fetch company names
$companies = mysqli_query($con, "SELECT company_id, company_name FROM companies");

// Close the database connection
mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Application Form</title>
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            background: white;
            border-radius: 8px;
            padding: 20px;
            max-width: 600px;
            margin: 50px auto;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #343a40;
            text-align: center;
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 8px;
        }

        select, input[type="file"], textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
            border: 1px solid #ccc;
            box-sizing: border-box;
            font-size: 16px;
        }

        select:focus, input:focus, textarea:focus {
            border-color: #007bff;
            outline: none;
        }

        button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            width: 100%;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #0056b3;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .alert.success {
            background-color: #d4edda;
            color: #155724;
        }

        .alert.danger {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('company').addEventListener('change', function () {
                var company_id = this.value;
                if (company_id) {
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', 'fetch_jobs.php', true);
                    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                    xhr.onload = function () {
                        if (this.status === 200) {
                            document.getElementById('job').innerHTML = this.responseText;
                        }
                    };
                    xhr.send('company_id=' + company_id);
                } else {
                    document.getElementById('job').innerHTML = '<option value="">Select a job</option>';
                }
            });
        });
    </script>
</head>
<body>

<div class="container">
    <h2>Job Application Form</h2>
    <form action="" method="POST" enctype="multipart/form-data">
        <div>
            <label for="company">Select Company:</label>
            <select id="company" name="company_id" required>
                <option value="">Select a company</option>
                <?php while ($row = mysqli_fetch_assoc($companies)) {
                    echo "<option value='{$row['company_id']}'>{$row['company_name']}</option>";
                } ?>
            </select>
        </div>

        <div>
            <label for="job">Select Job:</label>
            <select id="job" name="job_id" required>
                <option value="">Select a job</option>
            </select>
        </div>

        <div>
            <label for="cover_letter">Cover Letter:</label>
            <textarea id="cover_letter" name="cover_letter" rows="5" required></textarea>
        </div>

        <div>
            <label for="resume">Upload Resume:</label>
            <input type="file" id="resume" name="resume_file" required>
            <small>Allowed formats: PDF, DOC, DOCX (max size: 500KB)</small>
        </div>

        <button type="submit">Submit Application</button>
    </form>
</div>
<?php
    // Include the footer
    include('footer.php');
    ?>
</body>
</html>
