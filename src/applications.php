<?php
// Include database connection
include('db.php');
// Include navigation bar
include('navBar.php');

// Start the session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in and get the email
if (!isset($_SESSION['email'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

$user_email = $_SESSION['email'];

// Fetch user ID based on the email
$query = "SELECT id FROM users WHERE email = ?";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, 's', $user_email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if ($user) {
    $user_id = $user['id']; // Get the user ID from the fetched result
} else {
    header("Location: login.php"); // Redirect if user not found
    exit();
}

// Handle deletion if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the application ID from the POST request
    $application_id = $_POST['application_id'];

    // Prepare and execute the delete query
    $query = "DELETE FROM applications WHERE application_id = ? AND user_id = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, 'ii', $application_id, $user_id);
    mysqli_stmt_execute($stmt);

    // Check if the application was deleted
    if (mysqli_stmt_affected_rows($stmt) > 0) {
        // Redirect back with success message
        header("Location: applications.php?msg=Application deleted successfully");
        exit();
    } else {
        // Redirect back with error message
        header("Location: applications.php?msg=Error deleting application");
        exit();
    }

    // Close the prepared statement
    mysqli_stmt_close($stmt);
}

// Fetch applications for the logged-in user
$query = "SELECT a.application_id, a.cover_letter, a.applied_at, a.status, 
                 u.name AS user_name, j.job_title, c.company_name, a.resume_file 
          FROM applications a 
          JOIN users u ON a.user_id = u.id 
          JOIN jobs j ON a.job_id = j.job_id 
          JOIN companies c ON a.company_id = c.company_id
          WHERE a.user_id = ?"; // Filter by user_id
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Applications</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        .container {
            margin-top: 20px;
        }
        .card {
            border: 1px solid #dee2e6;
            border-radius: 10px;
            margin-bottom: 20px;
            transition: box-shadow 0.3s;
        }
        .card:hover {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        .status-accepted {
            color: green;
            font-weight: bold;
        }
        .status-rejected {
            color: red;
            font-weight: bold;
        }
        .status-pending {
            color: orange;
            font-weight: bold;
        }
        .cover-letter {
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .apply-button {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1 class="text-center mb-4">Job Applications</h1>

    <!-- Display success/error message -->
    <?php if (isset($_GET['msg'])) : ?>
        <div class="alert alert-info" role="alert">
            <?php echo htmlspecialchars($_GET['msg']); ?>
        </div>
    <?php endif; ?>

    <!-- Attractive button to navigate to the application form -->
    <div class="text-center apply-button">
        <a href="apply.php" class="btn btn-success btn-lg">
            <i class="fas fa-plus-circle"></i> Apply for a Job
        </a>
    </div>

    <div class="row">
        <?php while ($row = mysqli_fetch_assoc($result)) : ?>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($row['job_title']); ?></h5>
                        <h6 class="card-subtitle mb-2 text-muted"><?php echo htmlspecialchars($row['company_name']); ?></h6>
                        <p class="card-text"><strong>User:</strong> <?php echo htmlspecialchars($row['user_name']); ?></p>
                        <p class="card-text"><strong>Cover Letter:</strong> <span class="cover-letter" title="<?php echo htmlspecialchars($row['cover_letter']); ?>"><?php echo htmlspecialchars($row['cover_letter']); ?></span></p>
                        <p class="card-text"><strong>Applied At:</strong> <?php echo htmlspecialchars($row['applied_at']); ?></p>
                        <p class="card-text">
                            <strong>Status:</strong> 
                            <?php 
                                $status = htmlspecialchars($row['status']);
                                if (strcasecmp($status, 'accepted') === 0) {
                                    echo "<span class='status-accepted'>$status</span>";
                                } elseif (strcasecmp($status, 'rejected') === 0) {
                                    echo "<span class='status-rejected'>$status</span>";
                                } else {
                                    echo "<span class='status-pending'>$status</span>";
                                }
                            ?>
                        </p>
                        <p class="card-text">
                            <strong>Resume:</strong>
                            <?php if (!empty($row['resume_file'])) : ?>
                                <a href="<?php echo htmlspecialchars($row['resume_file']); ?>" class="btn btn-primary btn-sm" download>
                                    <i class="fas fa-file-download"></i> Download
                                </a>
                            <?php else : ?>
                                No Resume
                            <?php endif; ?>
                        </p>
                        <div class="d-flex justify-content-between">
                            <form action="" method="POST" onsubmit="return confirm('Are you sure you want to delete this application?');">
                                <input type="hidden" name="application_id" value="<?php echo htmlspecialchars($row['application_id']); ?>">
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                            <?php if (strcasecmp($row['status'], 'accepted') !== 0) : ?>
                                <a href="edit_application.php?application_id=<?php echo htmlspecialchars($row['application_id']); ?>" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<?php
// Include the footer
include('footer.php');
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Close connection
mysqli_close($con);
?>
