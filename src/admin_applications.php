<?php
ob_start(); // Start output buffering
include('db.php');
include("sidebar.php");

// Handle status update if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $application_id = $_POST['application_id'];
    $status = $_POST['status'];

    $query = "UPDATE applications SET status = ? WHERE application_id = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, 'si', $status, $application_id);
    mysqli_stmt_execute($stmt);

    if (mysqli_stmt_affected_rows($stmt) > 0) {
        header("Location: admin_applications.php?msg=Application status updated successfully");
        exit();
    } else {
        header("Location: admin_applications.php?msg=Error updating application status");
        exit();
    }
    mysqli_stmt_close($stmt);
}

$query = "SELECT a.application_id, a.cover_letter, a.applied_at, a.status, 
                 u.name AS user_name, j.job_title, c.company_name, a.resume_file 
          FROM applications a 
          JOIN users u ON a.user_id = u.id 
          JOIN jobs j ON a.job_id = j.job_id 
          JOIN companies c ON a.company_id = c.company_id";
$result = mysqli_query($con, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Job Applications</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f2f5;
        }
        .main-container {
            display: flex;
        }
        .sidebar {
            width: 250px;
            background-color: #343a40;
            color: white;
            min-height: 100vh;
            padding: 20px;
        }
        .content-container {
            flex: 1;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .table {
            background-color: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        th {
            background-color: #007bff;
            color: white;
            text-align: center;
        }
        tr {
            text-align: center;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .cover-letter {
            max-width: 250px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .btn-primary {
            background-color: #e76f51;
            border: none;
        }
        .btn-primary:hover {
            background-color: #e76f51;
        }
        .form-select {
            width: auto;
            margin: 0 auto;
        }
        .alert {
            text-align: center;
        }
    </style>
</head>
<body>

<div class="main-container">
    <div class="sidebar">
        <?php include("sidebar.php"); ?>
    </div>

    <div class="content-container">
        <h1 class="text-center mb-4">All Job Applications</h1>

        <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-info"><?php echo htmlspecialchars($_GET['msg']); ?></div>
        <?php endif; ?>

        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Application ID</th>
                    <th>User Name</th>
                    <th>Job Title</th>
                    <th>Company</th>
                    <th>Cover Letter</th>
                    <th>Applied At</th>
                    <th>Status</th>
                    <th>Resume</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['application_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['user_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['job_title']); ?></td>
                    <td><?php echo htmlspecialchars($row['company_name']); ?></td>
                    <td class="cover-letter" title="<?php echo htmlspecialchars($row['cover_letter']); ?>">
                        <?php echo htmlspecialchars($row['cover_letter']); ?>
                    </td>
                    <td><?php echo htmlspecialchars($row['applied_at']); ?></td>
                    <td>
                        <form action="" method="POST">
                            <input type="hidden" name="application_id" value="<?php echo htmlspecialchars($row['application_id']); ?>">
                            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="pending" <?php echo ($row['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                <option value="reviewed" <?php echo ($row['status'] == 'reviewed') ? 'selected' : ''; ?>>Reviewed</option>
                                <option value="accepted" <?php echo ($row['status'] == 'accepted') ? 'selected' : ''; ?>>Accepted</option>
                                <option value="rejected" <?php echo ($row['status'] == 'rejected') ? 'selected' : ''; ?>>Rejected</option>
                            </select>
                        </form>
                    </td>
                    <td>
                        <?php if (!empty($row['resume_file'])) : ?>
                            <a href="<?php echo htmlspecialchars($row['resume_file']); ?>" class="btn btn-primary btn-sm" download>
                                <i class="fas fa-file-download"></i> Download
                            </a>
                        <?php else : ?>
                            No Resume
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
mysqli_close($con);
ob_end_flush();
?>
