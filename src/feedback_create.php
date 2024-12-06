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
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id =  $user['id'];
    $company_id = intval($_POST['company_id']);
    $job_id = intval($_POST['job_id']);
    $rating = intval($_POST['rating']);
    $comments = mysqli_real_escape_string($con, $_POST['comments']);

    // Insert feedback into the database
    $insertSql = "INSERT INTO feedbacks (user_id, company_id, job_id, rating, comments, created_at) 
                  VALUES ($user_id, $company_id, $job_id, $rating, '$comments', NOW())";

    if (mysqli_query($con, $insertSql)) {
        $message = "Feedback created successfully.";
    } else {
        $message = "Error creating feedback: " . mysqli_error($con);
    }
}

// Fetch all companies from the `companies` table
$companySql = "SELECT company_id, company_name FROM companies";
$companyResult = mysqli_query($con, $companySql);

// Check for query execution errors
if (!$companyResult) {
    echo "Error: " . mysqli_error($con);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Feedback</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Add your custom styles here */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            color: #343a40;
            padding: 0;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #007bff;
        }
        .form-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            max-width: 600px;
            margin: auto;
        }
        .feedback-success {
            color: green;
            text-align: center;
            margin-bottom: 15px;
            animation: fadeIn 0.5s;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        form div {
            margin-bottom: 15px;
        }
        label {
            font-weight: bold;
        }
        select, input, textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 4px;
            border: 1px solid #ced4da;
            transition: border-color 0.3s;
        }
        select:focus, input:focus, textarea:focus {
            border-color: #007bff;
            outline: none;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #0056b3;
        }
        .icon {
            margin-right: 5px;
        }
    </style>
</head>
<body>

    <div class="form-container">
        <h1><i class="fas fa-pencil-alt icon"></i>Create Feedback</h1>

        <?php if ($message): ?>
            <div class="feedback-success"><?php echo $message; ?></div>
        <?php endif; ?>

        <form method="POST" id="feedbackForm">
            <div>
                <label for="company_id"><i class="fas fa-building icon"></i>Company Name:</label>
                <select id="company_id" name="company_id" required>
                    <option value="" disabled selected>Select Company</option>
                    <?php
                    while ($row = mysqli_fetch_assoc($companyResult)) {
                        echo "<option value='" . $row['company_id'] . "'>" . htmlspecialchars($row['company_name']) . "</option>";
                    }
                    ?>
                </select>
            </div>

            <div>
                <label for="job_id"><i class="fas fa-briefcase icon"></i>Job Title:</label>
                <select id="job_id" name="job_id" required>
                    <option value="" disabled selected>Select a company first</option>
                </select>
            </div>

            <div>
                <label for="rating"><i class="fas fa-star icon"></i>Rating:</label>
                <select id="rating" name="rating" required>
                    <option value="" disabled selected>Select Rating</option>
                    <option value="1">1 - Poor</option>
        <option value="2">2 - Fair</option>
        <option value="3">3 - Good</option>
        <option value="4">4 - Very Good</option>
        <option value="5">5 - Excellent</option>
                </select>
            </div>

            <div>
                <label for="comments"><i class="fas fa-comment-dots icon"></i>Comments:</label>
                <textarea id="comments" name="comments" rows="4" placeholder="Share your experience..." required></textarea>
            </div>

            <input type="hidden" name="user_id" value="1"> <!-- Replace with dynamic user ID -->

            <button type="submit"><i class="fas fa-paper-plane icon"></i>Submit Feedback</button>
        </form>
    </div>

    <?php
    // Include the footer
    include('footer.php');
    ?>
    
    <script>
        // Handle the company selection and dynamically load jobs
        document.getElementById('company_id').addEventListener('change', function () {
            var companyId = this.value;

            // Create an AJAX request to fetch jobs based on the company
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'get_jobs.php?company_id=' + companyId, true);
            xhr.onload = function () {
                if (xhr.status === 200) {
                    // Update the job dropdown with the jobs for the selected company
                    document.getElementById('job_id').innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        });
    </script>

</body>
</html>
