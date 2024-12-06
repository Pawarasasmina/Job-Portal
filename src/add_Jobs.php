<?php
// Start the session
session_start();

// Include your database connection file
require('db.php');

// Initialize success and error messages
$success = '';
$error = '';

// Fetch companies from the database for the dropdown
$companyQuery = "SELECT company_id, company_name FROM companies";
$companyResult = $con->query($companyQuery);

// Handle job addition logic
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate inputs
    $job_title = filter_input(INPUT_POST, 'job_title', FILTER_SANITIZE_STRING);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING); // Changed from job_description
    $location = filter_input(INPUT_POST, 'location', FILTER_SANITIZE_STRING);
    $salary = filter_input(INPUT_POST, 'salary', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION); // New salary field
    $company_id = filter_input(INPUT_POST, 'company', FILTER_SANITIZE_STRING);

    // Ensure all fields are filled
    if ($job_title && $description && $location && $salary && $company_id) {
        // Insert new job into the database
        $query = "INSERT INTO jobs (job_title, description, location, salary, company_id) VALUES (?, ?, ?, ?, ?)";
        $stmt = $con->prepare($query);
        $stmt->bind_param("ssssi", $job_title, $description, $location, $salary, $company_id); // Include salary in binding

        if ($stmt->execute()) {
            $success = "Job added successfully.";
        } else {
            $error = "Failed to add job. Please try again.";
        }
    } else {
        $error = "Please fill in all fields correctly.";
    }
}
?>

<?php include("sidebar.php"); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Job</title>
    <link rel="stylesheet" href="styles/addJobs.css">
</head>
<body>
    <div class="add-job-container">
        <h2>Add New Job</h2>

        <!-- Display success or error messages -->
        <?php if ($success): ?>
            <div class="success-message"><?php echo $success; ?></div>
        <?php elseif ($error): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="add_jobs.php" method="POST" onsubmit="return validateForm()">
            <div class="form-group">
                <label for="job_title">Job Title:</label>
                <input type="text" id="job_title" name="job_title" required>
            </div>
            <div class="form-group">
                <label for="description">Job Description:</label>
                <textarea id="description" name="description" required></textarea>
            </div>
            <div class="form-group">
                <label for="location">Location:</label>
                <input type="text" id="location" name="location" required>
            </div>
            <div class="form-group">
                <label for="salary">Salary:</label>
                <input type="text" id="salary" name="salary" required>
            </div>
            <div class="form-group">
                <label for="company">Company:</label>
                <select id="company" name="company" required>
                    <?php while($row = $companyResult->fetch_assoc()): ?>
                        <option value="<?php echo $row['company_id']; ?>"><?php echo $row['company_name']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <button  type="submit">Add Job</button>
            </div>
        </form>
    </div>

    <script src="scripts/validateForm.js"></script>
</body>
</html>
