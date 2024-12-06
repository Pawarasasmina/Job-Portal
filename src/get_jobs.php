<?php
// Include the database connection
include('db.php');

if (isset($_GET['company_id'])) {
    $company_id = intval($_GET['company_id']);

    // Query to get jobs related to the selected company
    $jobSql = "SELECT job_id, job_title FROM jobs WHERE company_id = $company_id";
    $jobResult = mysqli_query($con, $jobSql);

    // Check if there are jobs for the selected company
    if (mysqli_num_rows($jobResult) > 0) {
        echo "<option value='' disabled selected>Select Job</option>";
        while ($row = mysqli_fetch_assoc($jobResult)) {
            echo "<option value='" . $row['job_id'] . "'>" . htmlspecialchars($row['job_title']) . "</option>";
        }
    } else {
        echo "<option value='' disabled>No jobs available</option>";
    }
} else {
    echo "<option value='' disabled>Select a company first</option>";
}
?>


