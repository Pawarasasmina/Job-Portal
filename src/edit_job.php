<?php
// Start the session
session_start();

// Include your database connection file
require('db.php');

// Check if job ID is provided
if (isset($_GET['id'])) {
    $job_id = $_GET['id'];

    // Fetch the job details to pre-fill the form
    $query = "SELECT job_title, description, location, salary, company_id FROM jobs WHERE job_id = ?";
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

    // Update the job details when form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $job_title = $_POST['job_title'];
        $description = $_POST['description'];
        $location = $_POST['location'];
        $salary = $_POST['salary'];
        $company_id = $_POST['company_id'];

        // Prepare the update query
        $update_query = "UPDATE jobs SET job_title = ?, description = ?, location = ?, salary = ?, company_id = ? WHERE job_id = ?";
        $stmt = $con->prepare($update_query);
        $stmt->bind_param("ssssii", $job_title, $description, $location, $salary, $company_id, $job_id);

        if ($stmt->execute()) {
            header("Location: view_jobs_admin.php"); // Redirect back to the job list after updating
            exit;
        } else {
            echo "Error updating job: " . $con->error;
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
    <title>Edit Job</title>
    <link rel="stylesheet" href="styles/editJobs.css">
</head>
<body>
    <div class="job-container2">
        <h2>Edit Job</h2>

        <form action="" method="POST">
            <div class="form-group2">
                <label for="job_title">Job Title:</label>
                <input type="text" name="job_title" id="job_title" value="<?php echo htmlspecialchars($job['job_title']); ?>" required>
            </div>

            <div class="form-group2">
                <label for="description">Description:</label>
                <textarea name="description" id="description" required><?php echo htmlspecialchars($job['description']); ?></textarea>
            </div>

            <div class="form-group2">
                <label for="location">Location:</label>
                <input type="text" name="location" id="location" value="<?php echo htmlspecialchars($job['location']); ?>" required>
            </div>

            <div class="form-group2">
                <label for="salary">Salary:</label>
                <input type="number" name="salary" id="salary" value="<?php echo htmlspecialchars($job['salary']); ?>" required>
            </div>

            <div class="form-group2">
                <label for="company_id">Company:</label>
                <select name="company_id" id="company_id" required>
                    <?php
                    // Fetch all companies for the dropdown
                    $company_query = "SELECT company_id, company_name FROM companies";
                    $company_result = $con->query($company_query);
                    while ($company = $company_result->fetch_assoc()) {
                        $selected = ($company['company_id'] == $job['company_id']) ? 'selected' : '';
                        echo "<option value='" . $company['company_id'] . "' $selected>" . htmlspecialchars($company['company_name']) . "</option>";
                    }
                    ?>
                </select>
            </div>

            <button type="submit" class="button2 edit-button2">Update Job</button>
            <a href="view_jobs_admin.php" class="button2 cancel-button2">Cancel</a>
        </form>
    </div>
</body>
</html>
