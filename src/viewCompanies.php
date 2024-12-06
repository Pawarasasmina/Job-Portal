<?php
// Start the session
session_start();

// Include your database connection file
require('db.php');

// Handle deletion of a company
if (isset($_POST['delete_company_id'])) {
    $company_id = intval($_POST['delete_company_id']); // Get the company ID and ensure it's an integer

    // Prepare the SQL DELETE statement
    $query = "DELETE FROM companies WHERE company_id = ?";
    
    // Prepare and execute the statement
    if ($stmt = $con->prepare($query)) {
        $stmt->bind_param("i", $company_id); // Bind the company_id parameter
        $stmt->execute();

        // Check if the deletion was successful
        if ($stmt->affected_rows > 0) {
            $_SESSION['message'] = "Company deleted successfully.";
        } else {
            $_SESSION['message'] = "Error: Company could not be deleted.";
        }
        $stmt->close();
    } else {
        $_SESSION['message'] = "Error: Could not prepare the SQL statement.";
    }
}

// Handle editing of a company
if (isset($_POST['edit_company_id'])) {
    $company_id = intval($_POST['edit_company_id']);
    $name = $_POST['company_name'];
    $email = $_POST['company_email'];
    $address = $_POST['company_address'];
    $contact_number = $_POST['company_contact_number'];

    // Prepare the SQL UPDATE statement
    $query = "UPDATE companies SET company_name = ?, email = ?, address = ?, contact_number = ? WHERE company_id = ?";
    
    // Prepare and execute the statement
    if ($stmt = $con->prepare($query)) {
        $stmt->bind_param("ssssi", $name, $email, $address, $contact_number, $company_id);
        $stmt->execute();

        // Check if the update was successful
        if ($stmt->affected_rows > 0) {
            $_SESSION['message'] = "Company updated successfully.";
        } else {
            $_SESSION['message'] = "Error: Company could not be updated.";
        }
        $stmt->close();
    } else {
        $_SESSION['message'] = "Error: Could not prepare the SQL statement.";
    }
}

// Fetch all companies from the database
$query = "SELECT * FROM companies";
$result = $con->query($query);

// Include the admin navbar if needed
include("sidebar.php"); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Companies List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
    </style>
</head>
<body>
    <div style="max-width: 1200px; margin-left: 300px; padding: 20px;">
        <?php
        // Display any success or error messages
        if (isset($_SESSION['message'])) {
            echo '<div style="background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 10px; margin: 20px 0; border-radius: 5px;">' . htmlspecialchars($_SESSION['message']) . '</div>';
            unset($_SESSION['message']); // Clear the message after displaying
        }
        ?>

        <h2 style="text-align: center; margin-bottom: 20px; font-size: 2em; color: #333;">Added Companies</h2>

        <div style="display: flex; flex-wrap: wrap; gap: 20px; justify-content: center;">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div style="background-color: #ffffff; border: 1px solid #dee2e6; border-radius: 8px; padding: 15px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); width: 300px;">
                        <h3 style="margin-top: 0; font-size: 1.5em; color: #007bff;"><?php echo htmlspecialchars($row['company_name']); ?></h3>
                        <div style="margin-top: 10px;">
                            <p style="margin: 5px 0; color: #555;"><strong>Email:</strong> <?php echo htmlspecialchars($row['email']); ?></p>
                            <p style="margin: 5px 0; color: #555;"><strong>Address:</strong> <?php echo htmlspecialchars($row['address']); ?></p>
                            <p style="margin: 5px 0; color: #555;"><strong>Contact Number:</strong> <?php echo htmlspecialchars($row['contact_number']); ?></p>
                            <p style="margin: 5px 0; color: #555;"><strong>Created At:</strong> <?php echo htmlspecialchars($row['created_at']); ?></p>
                        </div>
                        <form method="POST" action="">
                            <input type="hidden" name="delete_company_id" value="<?php echo $row['company_id']; ?>">
                            <button type="submit" style="background-color: #dc3545; color: white; border: none; border-radius: 4px; padding: 8px 12px; cursor: pointer;" onclick="return confirm('Are you sure you want to delete this company?');">Delete</button>
                        </form>
                        <button style="background-color: #007bff; color: white; border: none; border-radius: 4px; padding: 8px 12px; cursor: pointer;" onclick="openEditModal(<?php echo htmlspecialchars($row['company_id']); ?>, '<?php echo htmlspecialchars($row['company_name']); ?>', '<?php echo htmlspecialchars($row['email']); ?>', '<?php echo htmlspecialchars($row['address']); ?>', '<?php echo htmlspecialchars($row['contact_number']); ?>')">Edit</button>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No companies added yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" style="display: none; position: fixed; z-index: 1; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4); padding-top: 60px;">
        <div style="background-color: #fefefe; margin: 5% auto; padding: 20px; border: 1px solid #888; width: 80%; max-width: 600px; border-radius: 8px;">
            <span style="color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer;" onclick="closeEditModal()">&times;</span>
            <h2>Edit Company</h2>
            <form id="editForm" method="POST" action="">
                <input type="hidden" name="edit_company_id" id="edit_company_id" value="">
                <label for="company_name">Company Name:</label><br>
                <input type="text" name="company_name" id="company_name" required><br>
                <label for="company_email">Email:</label><br>
                <input type="email" name="company_email" id="company_email" required><br>
                <label for="company_address">Address:</label><br>
                <input type="text" name="company_address" id="company_address" required><br>
                <label for="company_contact_number">Contact Number:</label><br>
                <input type="text" name="company_contact_number" id="company_contact_number" required><br>
                <button type="submit" style="background-color: #007bff; color: white; border: none; border-radius: 4px; padding: 8px 12px; cursor: pointer;">Save Changes</button>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(companyId, name, email, address, contactNumber) {
            document.getElementById('edit_company_id').value = companyId;
            document.getElementById('company_name').value = name;
            document.getElementById('company_email').value = email;
            document.getElementById('company_address').value = address;
            document.getElementById('company_contact_number').value = contactNumber;
            document.getElementById('editModal').style.display = 'block';
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        // Close the modal when the user clicks anywhere outside of it
        window.onclick = function(event) {
            if (event.target == document.getElementById('editModal')) {
                closeEditModal();
            }
        }
    </script>
</body>
</html>
