<?php
// Include database connection
include('db.php');

if (isset($_POST['company_id'])) {
    $company_id = $_POST['company_id'];
    // Fetch jobs for the selected company
    $jobs = mysqli_query($con, "SELECT job_id, job_title FROM jobs WHERE company_id = '$company_id'");

    // Output job options
    if (mysqli_num_rows($jobs) > 0) {
        while ($row = mysqli_fetch_assoc($jobs)) {
            echo "<option value='{$row['job_id']}'>{$row['job_title']}</option>";
        }
    } else {
        echo "<option value=''>No jobs available</option>";
    }
}

// Close the database connection
mysqli_close($con);
?>
