<?php
// Start the session
session_start();

// Include your database connection file
require('db.php');

// Initialize search query
$search_query = "";
if (isset($_GET['search'])) {
    $search_query = $_GET['search'];
}

// Fetch jobs with their associated company names, filtered by search query
$query = "SELECT j.job_id, j.job_title, j.description, j.location, j.salary, c.company_name 
          FROM jobs j 
          JOIN companies c ON j.company_id = c.company_id 
          WHERE j.job_title LIKE ? OR c.company_name LIKE ? OR j.location LIKE ?";
$stmt = $con->prepare($query);
$search_term = "%" . $search_query . "%";
$stmt->bind_param("sss", $search_term, $search_term, $search_term);
$stmt->execute();
$result = $stmt->get_result();
?>

<?php include("sidebar.php"); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Jobs</title>
    <link rel="stylesheet" href="styles/job_view.css">
</head>
<body>
    <div class="job-container">
        <h2>Available Jobs</h2>

        <!-- Search Form -->
        <form class="search-form" action="" method="GET">
            <input type="text" name="search" placeholder="Search by job title, company, or location" value="<?php echo htmlspecialchars($search_query); ?>">
            <button type="submit" class="search-button">Search</button>
        </form>

        <div class="job-grid">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="job-card">
                        <h3><?php echo htmlspecialchars($row['job_title']); ?></h3>
                        <p><strong>Company:</strong> <?php echo htmlspecialchars($row['company_name']); ?></p>
                        <p><strong>Description:</strong> <?php echo htmlspecialchars($row['description']); ?></p>
                        <p><strong>Location:</strong> <?php echo htmlspecialchars($row['location']); ?></p>
                        <p><strong>Salary:</strong> $<?php echo htmlspecialchars($row['salary']); ?></p>
                        <div class="button-group">
                            <a href="edit_job.php?id=<?php echo $row['job_id']; ?>" class="button edit-button">Edit Job</a>
                            <a href="delete_job.php?id=<?php echo $row['job_id']; ?>" class="button delete-button">Delete Job</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No jobs available at the moment.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
