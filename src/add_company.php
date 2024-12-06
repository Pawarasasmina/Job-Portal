<?php
// Start the session
session_start();

// Include your database connection file
require('db.php'); // Ensure db.php sets up a MySQLi connection

// Initialize variables for success and error messages
$success = '';
$error = '';

// Handle company addition logic
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['logout'])) {
    // Sanitize and validate inputs
    $company_name = filter_input(INPUT_POST, 'company_name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
    $contact_number = filter_input(INPUT_POST, 'contact_number', FILTER_SANITIZE_STRING);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
    $logo_path = '';

    // Handle file upload for logo
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['logo']['tmp_name'];
        $fileName = $_FILES['logo']['name'];
        $fileSize = $_FILES['logo']['size'];
        $fileType = $_FILES['logo']['type'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png'];

        // Validate file extension
        if (in_array($fileExtension, $allowedExtensions)) {
            $uploadFileDir = 'uploads/';
            $logo_path = $uploadFileDir . uniqid() . '.' . $fileExtension;
            
            // Move file to upload directory
            if (!move_uploaded_file($fileTmpPath, $logo_path)) {
                $error = "There was an error moving the uploaded file.";
            }
        } else {
            $error = "Invalid file format. Only JPG, JPEG, or PNG allowed.";
        }
    }

    // Check if email already exists
    $checkQuery = "SELECT * FROM companies WHERE email = ?";
    $stmt = $con->prepare($checkQuery);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $error = "Email already exists.";
    } else {
        // Insert new company into the database
        $query = "INSERT INTO companies (company_name, email, address, contact_number, logo, description) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $con->prepare($query);
        $stmt->bind_param("ssssss", $company_name, $email, $address, $contact_number, $logo_path, $description);

        if ($stmt->execute()) {
            // Company added successfully
            $success = "Company added successfully.";
        } else {
            $error = "Failed to add company. Please try again.";
        }
    }
}
?>

<?php
// Include sidebar
include("sidebar.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Company - Admin Panel</title>

    <!-- Internal CSS -->
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f9;
            margin-top: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .add-company-container {
            background-color: #ffffff;
            margin-top: 100px;
            margin-left: 300px;
            padding: 40px;
            max-width: 500px;
            width: 100%;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            font-weight: bold;
            color: #555;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-top: 8px;
            font-size: 16px;
            background-color: #fafafa;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #007bff;
        }

        .btn-submit {
            background-color: #007bff;
            color: white;
            padding: 12px;
            width: 100%;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }

        .btn-submit:hover {
            background-color: #0056b3;
        }

        .success-message {
            color: green;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .error-message {
            color: red;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .error-message span {
            font-size: 12px;
        }

        textarea {
            resize: vertical;
        }
    </style>

    <script>
        function validateForm() {
            const companyName = document.getElementById('company_name').value;
            const email = document.getElementById('email').value;
            const address = document.getElementById('address').value;
            const contactNumber = document.getElementById('contact_number').value;
            const logo = document.getElementById('logo').value;

            let isValid = true;

            // Clear previous error messages
            document.querySelectorAll('.error-message span').forEach((elem) => {
                elem.innerText = '';
            });

            // Validate Company Name
            if (companyName.trim() === '') {
                document.getElementById('company_name-error').innerText = 'Company name is required.';
                isValid = false;
            }

            // Validate Email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                document.getElementById('email-error').innerText = 'Invalid email format.';
                isValid = false;
            }

            // Validate Address
            if (address.trim() === '') {
                document.getElementById('address-error').innerText = 'Address is required.';
                isValid = false;
            }

            // Validate Contact Number
            const phoneRegex = /^\d{10}$/; // Change the regex pattern according to your requirement
            if (!phoneRegex.test(contactNumber)) {
                document.getElementById('contact_number-error').innerText = 'Contact number must be 10 digits.';
                isValid = false;
            }

            // Validate Logo File (optional)
            if (logo && !logo.match(/\.(jpg|jpeg|png)$/i)) {
                document.getElementById('logo-error').innerText = 'Only JPG, JPEG, or PNG files are allowed.';
                isValid = false;
            }

            return isValid; // Return the overall validity
        }
    </script>
</head>
<body>
    <div class="add-company-container">
        <h2>Add New Company</h2>

        <!-- Display success or error message -->
        <?php if ($success): ?>
            <div class="success-message"><?php echo $success; ?></div>
        <?php elseif ($error): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="" method="POST" class="add-company-form" enctype="multipart/form-data" onsubmit="return validateForm()">
            <div class="form-group">
                <label for="company_name">Company Name:</label>
                <input type="text" id="company_name" name="company_name" placeholder="Enter company name" required>
                <span class="error-message"><span id="company_name-error"></span></span>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" placeholder="Enter email" required>
                <span class="error-message"><span id="email-error"></span></span>
            </div>
            <div class="form-group">
                <label for="address">Address:</label>
                <textarea id="address" name="address" placeholder="Enter address" required></textarea>
                <span class="error-message"><span id="address-error"></span></span>
            </div>
            <div class="form-group">
    <label for="contact_number">Contact Number:</label>
    <input type="tel" id="contact_number" name="contact_number" placeholder="Enter contact number" required oninput="this.value = this.value.replace(/[^0-9]/g, '')">
    <span class="error-message"><span id="contact_number-error"></span></span>
</div>

            <div class="form-group">
                <label for="logo">Company Logo (JPG, JPEG, PNG):</label>
                <input type="file" id="logo" name="logo">
                <span class="error-message"><span id="logo-error"></span></span>
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" placeholder="Enter description" required></textarea>
            </div>
            <button type="submit" class="btn-submit">Add Company</button>
            
        </form>
    </div>
</body>
</html>
