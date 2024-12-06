<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        .footer {
            opacity: 0; /* Start as invisible */
            transition: opacity 1s ease-in; /* Smooth transition */
            background-color: #2c3e50; /* Dark background */
            color: #fafafa; /* Light text color */
            padding: 2rem 1rem; /* Padding */
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }

        .footer.visible {
            opacity: 1; /* Fade in */
        }

        .footer-section {
            flex: 1;
            margin: 1rem;
        }

        .footer-section h3 {
            font-size: 1.3rem;
            margin-bottom: 1rem;
            color: #e74c3c; /* Accent color */
        }

        .footer-section ul {
            list-style: none;
            padding: 0;
        }

        .footer-section li {
            margin: 0.5rem 0;
        }

        .footer-section a {
            color: #fafafa; /* Link color */
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer-section a:hover {
            color: #e74c3c; /* Hover effect */
        }

        .footer-bottom {
            text-align: center;
            width: 100%;
            margin-top: 2rem;
            border-top: 1px solid #4ecdc4; /* Top border color */
            padding-top: 1rem;
        }

        .footer-bottom p {
            font-size: 0.9rem;
            margin: 0.5rem 0;
        }

        .bottom-links {
            list-style: none;
            padding: 0;
            display: flex;
            justify-content: center;
            gap: 1.5rem;
        }

        .bottom-links li a {
            color: #fafafa; /* Link color */
            text-decoration: none;
            transition: color 0.3s;
        }

        .social-icons {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 1rem;
        }

        .social-icons a img {
            width: 32px;
            height: 32px;
            transition: transform 0.3s;
        }

        .social-icons a:hover img {
            transform: scale(1.1); /* Zoom on hover */
        }
    </style>
</head>
<body>
    <footer id="footer" class="footer hidden">
        <div class="footer-container" style="flex: 1; display: flex; justify-content: space-between; width: 100%;">
            <div class="footer-section">
                <h3>Job Portal</h3>
                <ul>
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">Contact Us</a></li>
                    <li><a href="#">Login & Support</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Job Seekers</h3>
                <ul>
                    <li><a href="#">Search Jobs</a></li>
                    <li><a href="#">Submit Resume</a></li>
                    <li><a href="#">Career Advice</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Employers</h3>
                <ul>
                    <li><a href="#">Post a Job</a></li>
                    <li><a href="#">View Candidates</a></li>
                    <li><a href="#">Employer Dashboard</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Legal</h3>
                <ul>
                    <li><a href="#">Terms & Conditions</a></li>
                    <li><a href="#">Privacy Policy</a></li>
                    <li><a href="#">Cookie Policy</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>Copyright © All rights reserved - 2024 Designed by Your Company Name</p>
            <ul class="bottom-links">
                <li><a href="#">Terms & Conditions</a></li>
                <li><a href="#">Privacy Policy</a></li>
            </ul>
            <div class="social-icons">
                <a href="#"><img src="images/facebook-icon.png" alt="Facebook"></a>
                <a href="#"><img src="images/twitter-icon.png" alt="Twitter"></a>
                <a href="#"><img src="images/instagram-icon.png" alt="Instagram"></a>
            </div>
        </div>
    </footer>

    <script src="js/footer.js"></script> <!-- Link to your JavaScript -->
</body>
</html>
