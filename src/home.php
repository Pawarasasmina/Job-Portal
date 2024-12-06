<?php include 'navbar.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Portal - Home</title>
    <style>
        /* Reset some default styles */
        body, h1, h2, h3, p {
            margin: 0;
            padding: 0;
        }

        body {
            font-family: "Sanchez", sans-serif;
            line-height: 1.6; /* Improved readability */
        }

        .container {
            width: 90%;
            max-width: 1200px; /* Centering the container */
            margin: auto;
        }

        /* Hero Section */
        .hero-section {
            background-color: #1c3d3f; /* Dark blue */
            color: #fff;
            padding: 80px 0; /* More padding for better spacing */
            text-align: center;
        }

        .hero-title {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }

        .hero-subtitle {
            font-size: 1.25rem;
            margin-bottom: 30px;
        }

        /* Search Section */
        .search-section {
            padding: 40px 0;
            text-align: center;
        }

        .search-form {
            display: flex;
            justify-content: center;
            gap: 15px;
            flex-wrap: wrap; /* Allow inputs to wrap on smaller screens */
        }

        .search-input {
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 250px; /* Fixed width for consistency */
            min-width: 200px; /* Minimum width for smaller screens */
        }

        /* Job Listings Section */
        .job-listings {
            padding: 40px 0;
        }

        .job-card {
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 20px;
            text-align: center;
            transition: box-shadow 0.3s;
            background-color: #fff; /* White background for cards */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Subtle shadow */
        }

        .job-card:hover {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }

        .job-title {
            font-size: 1.5rem;
            margin-bottom: 10px;
        }

        /* About Section */
        .about-section {
            background-color: #fafafa; /* Light gray */
            padding: 40px 0;
            text-align: center;
        }

        .section-title {
            font-size: 2rem;
            margin-bottom: 20px;
        }

        /* Button Styles */
        .btn {
            display: inline-block;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            color: #fff;
        }

        .btn-primary {
            background-color: #E76F51; /* Primary color */
        }

        .btn-secondary {
            background-color: #379D9D; /* Secondary color */
        }

    </style>
</head>
<body>

    <!-- Hero Section -->
    <header class="hero-section">
        <div class="container">
            <h1 class="hero-title">Find Your Dream Job</h1>
            <p class="hero-subtitle">Connecting you with the best opportunities.</p>
            <a href="apply.php" class="btn btn-primary">Get Started</a>
        </div>
    </header>

    <!-- Search Section -->
<section id="search" class="search-section">
    <div class="container">
        <h2 class="section-title">Search for Jobs</h2>
        <form action="job_listings.php" method="GET" class="search-form">
            <input type="text" name="keyword" placeholder="Job title or keywords" class="search-input" >
            <input type="text" name="location" placeholder="Location" class="search-input" >
            <button type="submit" class="btn btn-secondary">Search</button>
        </form>
    </div>
</section>



    <!-- Job Listings Section -->
    <section class="job-listings">
        <div class="container">
            <h2 class="section-title">Available Job Listings</h2>
            <div class="row">
                <?php
                // Fetch all jobs with their associated company names
                $query = "SELECT j.job_id, j.job_title, c.company_name, j.location, j.description 
                        FROM jobs j 
                        JOIN companies c ON j.company_id = c.company_id";
                $result = $con->query($query);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        ?>
                        <!-- Job Listing Item -->
                        <div class="col-md-4 mb-4">
                            <div class="job-card">
                                <h3 class="job-title"><?php echo htmlspecialchars($row['job_title']); ?></h3>
                                <p class="company-name"><?php echo htmlspecialchars($row['company_name']); ?></p>
                                <p class="job-location"><?php echo htmlspecialchars($row['location']); ?></p>
                                <p class="job-description"><?php echo htmlspecialchars($row['description']); ?></p>
                                <a href="job-details.php?id=<?php echo $row['job_id']; ?>" class="btn btn-primary">View Details</a>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo '<p>No jobs available at the moment.</p>';
                }
                ?>
            </div>
        </div>
    </section>



    <!-- About Section -->
    <section class="about-section">
        <div class="container">
            <h2 class="section-title">About Us</h2>
            <p>We are committed to connecting job seekers with the right opportunities. Our platform offers a variety of job listings across multiple industries.</p>
        </div>
    </section>

    <!-- Footer Section -->
    <?php include 'footer.php'; ?>

</body>
</html>
