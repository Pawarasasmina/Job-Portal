<?php
// Start the session
session_start();

// Include your database connection file
require('db.php');

// Check if job ID is provided
if (isset($_GET['id'])) {
    $job_id = $_GET['id'];

    // Fetch the job details to display as a preview
    $query = "SELECT j.job_title, j.description, j.location, j.salary, c.company_name 
              FROM jobs j 
              JOIN companies c ON j.company_id = c.company_id 
              WHERE j.job_id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $job_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Check if the job exists
    if ($result->num_rows > 0) {
        $job = $result->fetch_assoc();
    } else {
        echo "Job not found.";
        exit;
    }

    // Prepare and execute delete query when form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $delete_query = "DELETE FROM jobs WHERE job_id = ?";
        $stmt = $con->prepare($delete_query);
        $stmt->bind_param("i", $job_id);

        if ($stmt->execute()) {
            header("Location: view_jobs_admin.php"); // Redirect back to the job list after deletion
            exit;
        } else {
            echo "Error deleting job: " . $con->error;
        }
    }
} else {
    echo "No job ID provided.";
    exit;
}
?>

<?php include("sidebar.php"); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Deletion</title>
    <link rel="stylesheet" href="styles/job_view.css">
</head>
<body>
    <div class="job-container1">
        <h2>Confirm Deletion</h2>
        <p>Are you sure you want to delete the following job?</p>

        <!-- Job card preview -->
        <div class="job-card1">
            <h3><?php echo htmlspecialchars($job['job_title']); ?></h3>
            <p><strong>Company:</strong> <?php echo htmlspecialchars($job['company_name']); ?></p>
            <p><strong>Description:</strong> <?php echo htmlspecialchars($job['description']); ?></p>
            <p><strong>Location:</strong> <?php echo htmlspecialchars($job['location']); ?></p>
            <p><strong>Salary:</strong> $<?php echo htmlspecialchars($job['salary']); ?></p>
        </div>

        <!-- Confirmation form -->
        <form action="" method="POST">
            <button type="submit" class="button delete-button">Yes, Delete Job</button>
            <a href="view_jobs_admin.php" class="button cancel-button">Cancel</a>
        </form>
    </div>
</body>
</html>
