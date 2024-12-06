<?php
// Start the session
session_start();

// Include your database connection file
require('db.php');

// Initialize search variables
$keyword = "";
$location = "";

// Check if search parameters are set
if (isset($_GET['keyword'])) {
    $keyword = $_GET['keyword'];
}
if (isset($_GET['location'])) {
    $location = $_GET['location'];
}

// Fetch jobs with their associated company names, filtered by search parameters
$query = "SELECT j.job_id, j.job_title, j.description, j.location, j.salary, c.company_name 
          FROM jobs j 
          JOIN companies c ON j.company_id = c.company_id 
          WHERE j.job_title LIKE ? AND j.location LIKE ?";
$stmt = $con->prepare($query);
$keyword_term = "%" . $keyword . "%";
$location_term = "%" . $location . "%";
$stmt->bind_param("ss", $keyword_term, $location_term);
$stmt->execute();
$result = $stmt->get_result();
?>

<?php include("navbar.php"); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Jobs</title>
    <link rel="stylesheet" href="styles/clientJobView.css">
</head>
<body>
    <div class="job-container">
        <h2>Available Jobs</h2>

            <div class ="middle_section">

                    <!-- Search Form -->
                    <form class="search-form" action="" method="GET">
                        <input type="text" name="keyword" placeholder="Search by job title" value="<?php echo htmlspecialchars($keyword); ?>">
                        <input type="text" name="location" placeholder="Location" value="<?php echo htmlspecialchars($location); ?>">
                      
                    </form>
                    <button type="submit" class="search-button">Search</button>
                    <h4>*If You want to apply for these jobs, You have to navigate to the Application Tab</h4>

            </div> 

        <div class="job-grid">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="job-card">
                        <h3><?php echo htmlspecialchars($row['job_title']); ?></h3>
                        <div class ="line"></div>
                        <p><strong>Company:</strong> <?php echo htmlspecialchars($row['company_name']); ?></p>
                        <p><strong>Description:</strong> <?php echo htmlspecialchars($row['description']); ?></p>
                        <p><strong>Location:</strong> <?php echo htmlspecialchars($row['location']); ?></p>
                        <p><strong>Salary:</strong> $<?php echo htmlspecialchars($row['salary']); ?></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No jobs available at the moment.</p>
            <?php endif; ?>
        </div>
    </div>
<?php include("footer.php"); ?>
</body>
</html>
